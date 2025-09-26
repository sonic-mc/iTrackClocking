<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\AttendanceLog;
use App\Traits\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as SysLog;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;


class AuditLogController extends Controller
{
    use AuditLogger;

    /**
     * List and filter audit logs.
     * Returns the audits list view.
     */
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('action'), fn($q) => $q->where('action', 'like', '%' . $request->action . '%'))
            ->when($request->filled('description'), fn($q) => $q->where('description', 'like', '%' . $request->description . '%'))
            ->when($request->filled('from'), fn($q) => $q->where('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->where('created_at', '<=', $request->to))
            ->orderBy('created_at', 'desc')
            ->paginate(50);
    
        $users = User::orderBy('name')->get();
    
        return view('admin.audits', compact('logs', 'users'));
    }
    

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
    
        $logs = AuditLog::with('user')
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('action'), fn($q) => $q->where('action', 'like', '%' . $request->action . '%'))
            ->when($request->filled('description'), fn($q) => $q->where('description', 'like', '%' . $request->description . '%'))
            ->when($request->filled('from'), fn($q) => $q->where('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->where('created_at', '<=', $request->to))
            ->orderBy('created_at', 'desc')
            ->get();
    
        $fileName = 'audit_logs_' . now()->format('Ymd_His');
    
        if ($format === 'csv') {
            $csv = "ID,User,Action,Description,Created At,Updated At\n";
    
            foreach ($logs as $log) {
                $csv .= '"' . $log->id . '",';
                $csv .= '"' . ($log->user->name ?? 'System') . '",';
                $csv .= '"' . $log->action . '",';
                $csv .= '"' . ($log->description ?? '-') . '",';
                $csv .= '"' . optional($log->created_at)->toDateTimeString() . '",';
                $csv .= '"' . optional($log->updated_at)->toDateTimeString() . "\"\n";
            }
    
            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$fileName}.csv\"",
            ]);
        }
    
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('audit_logs.pdf', compact('logs'))
                      ->setPaper('a4', 'landscape');
    
            return $pdf->download("{$fileName}.pdf");
        }
    
        return back()->with('error', 'Unsupported export format.');
    }

    /**
     * Store a new audit log row using your schema.
     * Accepts optional model and changes payload.
     */


    /**
     * Show one audit log (optional). Returns the same view for consistency.
     */
    public function show(AuditLog $auditLog)
    {
        // Reuse the audits view, passing a single-item paginator-like collection
        $logs = collect([$auditLog]);
        return view('admin.audits', compact('logs'));
    }

  

    /**
     * Dashboard summary counts (unchanged logic).
     */
    public function dashboardSummary()
    {
        $now = Carbon::now();

        $totalEvents   = AuditLog::count();
        $todayEvents   = AuditLog::whereDate('created_at', $now->toDateString())->count();
        $uniqueUsers   = AuditLog::whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $criticalEvents= AuditLog::where('action', 'like', '%critical%')->count();
        $exportCount   = AuditLog::where('action', 'like', '%export%')->count();

        // Keep your existing view if you have one for the dashboard
        return view('admin.audit.index', compact(
            'totalEvents',
            'todayEvents',
            'uniqueUsers',
            'criticalEvents',
            'exportCount'
        ));
    }

    /**
     * Example report method kept as-is (unrelated to audits listing).
     */
    public function generateAttendanceReport(Request $request)
    {
        $start = $request->input('start_date') ?? Carbon::now()->startOfMonth();
        $end   = $request->input('end_date') ?? Carbon::now();

        $logs = AttendanceLog::with('employee')
            ->whereBetween('clock_in_time', [$start, $end])
            ->orderBy('clock_in_time')
            ->get();

        $report = $logs->map(function ($log) {
            $clockIn  = Carbon::parse($log->clock_in_time);
            $clockOut = $log->clock_out_time ? Carbon::parse($log->clock_out_time) : null;

            return [
                'employee'  => $log->employee->name ?? 'Unknown',
                'date'      => $clockIn->toDateString(),
                'clock_in'  => $clockIn->format('H:i'),
                'clock_out' => $clockOut ? $clockOut->format('H:i') : '—',
                'late'      => $clockIn->hour > 8 ? 'Yes' : 'No',
                'duration'  => $clockOut ? $clockIn->diffInHours($clockOut) . ' hrs' : '—',
                'device'    => $log->device_info ?? '—',
                'geofence'  => $log->geofence_status ? '✅' : '❌',
            ];
        });

        return response()->json(['report' => $report]);
    }

    /**
     * Legacy filtered method maintained; returns the audits view.
     */
    public function filtered(Request $request)
    {
        $query = AuditLog::with('user');
    
        // 🔍 Free-text search (action + description only, since those exist in schema)
        if ($search = $request->input('search')) {
            $query->where(function ($w) use ($search) {
                $w->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
    
        // 📅 Date range filters (based on created_at)
        switch ($request->input('date_range')) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'quarter':
                $query->whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()]);
                break;
        }
    
        // 🎯 Filter by action type
        if ($request->filled('action_type') && $request->input('action_type') !== 'all') {
            $query->where('action', 'like', '%' . $request->input('action_type') . '%');
        }
    
        // 👤 Optional filter by user_id
        if ($request->filled('user_id') && $request->input('user_id') !== 'all') {
            $query->where('user_id', $request->input('user_id'));
        }
    
        $logs = $query->latest('id')->paginate(50)->withQueryString();
    
        return view('admin.audits', compact('logs'));
    }

    /**
     * CSV download of audits using current filters if desired (simple version downloads all).
     */
    public function download(Request $request)
    {
        $fileName = 'audit_logs_' . now()->format('Ymd_His') . '.csv';
    
        // Build query with user relation
        $query = AuditLog::query()->with('user')->orderBy('id');
    
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
    
            // UTF-8 BOM for Excel compatibility
            echo "\xEF\xBB\xBF";
    
            // Header row (aligned with new schema)
            fputcsv($out, [
                'ID',
                'User ID',
                'User Name',
                'Action',
                'Description',
                'Timestamp',
            ]);
    
            foreach ($query->cursor() as $log) {
                fputcsv($out, [
                    $log->id,
                    $log->user_id,
                    optional($log->user)->name,
                    $log->action,
                    $log->description,
                    optional($log->timestamp)->toDateTimeString(),
                ]);
            }
    
            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
    
}


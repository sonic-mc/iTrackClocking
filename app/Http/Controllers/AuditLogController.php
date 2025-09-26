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
            ->orderBy('timestamp', 'desc')
            ->paginate(50);
    
        $users = User::orderBy('name')->get();

        $modules = [
            'attendance',
            'audit-logs',
            'biometrics',
            'branches',
            'departments',
            'employees',
            'employeeshifts',
            'geofences',
            'leaverequests',
            'overtimeslogs',
            'notifications',
            'shidts',
            'payments',
        ];

        return view('admin.audits', compact('logs', 'users', 'modules'));

    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $logs = AuditLog::with('user')
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('module'), fn($q) => $q->where('module', $request->module))
            ->when($request->filled('from'), fn($q) => $q->where('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->where('created_at', '<=', $request->to))
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'csv') {
            $csv = "User,Action,Module,IP,Created At\n";

            foreach ($logs as $log) {
                $csv .= '"' . ($log->user->name ?? 'System') . '",';
                $csv .= '"' . $log->action . '",';
                $csv .= '"' . $log->model_type . '",';
                $csv .= '"' . ($log->ip_address ?? '-') . '",';
                $csv .= '"' . $log->created_at->toDateTimeString() . "\"\n";
            }
            

            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="audit_logs.csv"',
            ]);
        }

        // PDF export placeholder
        return back()->with('error', 'PDF export not implemented yet.');
    }

    /**
     * Store a new audit log row using your schema.
     * Accepts optional model and changes payload.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'action'      => 'required|string|max:255',
            'model_type'  => 'nullable|string|max:255',
            'model_id'    => 'nullable|integer',
            'ip_address'  => 'nullable|ip',
            'url'         => 'nullable|string|max:2048',
            'method'      => 'nullable|string|max:10',
            'user_agent'  => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            // changes can be posted as array or JSON string
            'changes'     => 'nullable',
        ]);

        // Normalize changes to JSON string for storage (robust to model casts)
        $changes = $validated['changes'] ?? null;
        if (is_string($changes)) {
            // Try decode; if it decodes to array/object keep JSON string, else wrap string
            $decoded = json_decode($changes, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $changesJson = json_encode($decoded, JSON_UNESCAPED_UNICODE);
            } else {
                $changesJson = json_encode(['message' => $changes], JSON_UNESCAPED_UNICODE);
            }
        } elseif (is_array($changes) || is_object($changes)) {
            $changesJson = json_encode($changes, JSON_UNESCAPED_UNICODE);
        } else {
            $changesJson = null;
        }

        try {
            AuditLog::create([
                'user_id'    => Auth::id(),
                'action'     => $validated['action'],
                'model_type' => $validated['model_type'] ?? null,
                'model_id'   => $validated['model_id'] ?? null,
                'changes'    => $changesJson,
                'ip_address' => $validated['ip_address'] ?? $request->ip(),
                'url'        => $validated['url'] ?? $request->fullUrl(),
                'method'     => $validated['method'] ?? $request->method(),
                'user_agent' => $validated['user_agent'] ?? $request->header('User-Agent'),
                'location'   => $validated['location'] ?? (session('clocking_terminal') ?? session('branch_name') ?? null),
            ]);
        } catch (\Throwable $e) {
            SysLog::error('Failed to create audit log', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to record audit log.'], 500);
        }

        return response()->json(['message' => 'Audit log recorded.'], 201);
    }

    /**
     * Show one audit log (optional). Returns the same view for consistency.
     */
    public function show(AuditLog $auditLog)
    {
        // Reuse the audits view, passing a single-item paginator-like collection
        $logs = collect([$auditLog]);
        return view('admin.audits', compact('logs'));
    }

    public function create() {}
    public function edit(AuditLog $auditLog) {}
    public function update(Request $request, AuditLog $auditLog) {}

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

        if ($search = $request->input('search')) {
            $query->where(function ($w) use ($search) {
                $w->where('action', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%");
            });
        }

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
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'quarter':
                $query->whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()]);
                break;
        }

        if ($request->filled('action_type') && $request->input('action_type') !== 'all') {
            $query->where('action', 'like', '%' . $request->input('action_type') . '%');
        }

        if ($request->filled('user_filter') && $request->input('user_filter') !== 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->input('user_filter'));
            });
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

        // If you want to reuse filters from index, copy the same query builder here.
        $query = AuditLog::query()->with('user')->orderBy('id');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            echo "\xEF\xBB\xBF";

            // Header row (aligned with your schema)
            fputcsv($out, [
                'ID',
                'Timestamp',
                'User ID',
                'User Name',
                'Action',
                'Model Type',
                'Model ID',
                'IP Address',
                'Location',
                'Method',
                'URL',
                'User Agent',
                'Changes (JSON)',
            ]);

            foreach ($query->cursor() as $log) {
                // Normalize changes to JSON string for CSV
                $changes = $log->changes;
                if (is_array($changes) || is_object($changes)) {
                    $changes = json_encode($changes, JSON_UNESCAPED_UNICODE);
                } elseif ($changes === null) {
                    $changes = '';
                } else {
                    $changes = (string) $changes;
                }

                fputcsv($out, [
                    $log->id,
                    optional($log->created_at)->toDateTimeString(),
                    $log->user_id,
                    optional($log->user)->name,
                    $log->action,
                    $log->model_type,
                    $log->model_id,
                    $log->ip_address,
                    $log->location,
                    $log->method,
                    $log->url,
                    $log->user_agent,
                    $changes,
                ]);
            }

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
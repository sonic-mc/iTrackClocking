<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Traits\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;


class AuditLogController extends Controller
{
    use AuditLogger;

    /**
     * Display filtered audit logs.
     */
    public function index(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('action'), fn($q) => $q->where('action', 'like', '%' . $request->action . '%'))
            ->when($request->filled('description'), fn($q) => $q->where('description', 'like', '%' . $request->description . '%'))
            ->when($request->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->orderByDesc('created_at')
            ->paginate(50);

        $users = User::orderBy('name')->get();

        return view('admin.audits', compact('logs', 'users'));
    }

    /**
     * Export logs as CSV or PDF.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $logs = AuditLog::with('user')
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('action'), fn($q) => $q->where('action', 'like', '%' . $request->action . '%'))
            ->when($request->filled('description'), fn($q) => $q->where('description', 'like', '%' . $request->description . '%'))
            ->when($request->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->orderByDesc('created_at')
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
            $pdf = Pdf::loadView('admin.pdf', compact('logs'))
                      ->setPaper('a4', 'landscape');

            return $pdf->download("{$fileName}.pdf");
        }

        return back()->with('error', 'Unsupported export format.');
    }

    /**
     * Show a single audit log.
     */
    public function show(AuditLog $auditLog)
    {
        return view('admin.audit.show', compact('auditLog'));
    }

    /**
     * Admin dashboard summary.
     */
    public function dashboardSummary()
    {
        $now = Carbon::now();

        return view('admin.audits', [
            'totalEvents'    => AuditLog::count(),
            'todayEvents'    => AuditLog::whereDate('created_at', $now->toDateString())->count(),
            'uniqueUsers'    => AuditLog::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'criticalEvents' => AuditLog::where('action', 'like', '%critical%')->count(),
            'exportCount'    => AuditLog::where('action', 'like', '%export%')->count(),
        ]);
    }

    /**
     * Legacy filtered view.
     */
    public function filtered(Request $request)
    {
        $query = AuditLog::with('user');

        if ($search = $request->input('search')) {
            $query->where(function ($w) use ($search) {
                $w->where('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
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
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'quarter':
                $query->whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()]);
                break;
        }

        if ($request->filled('action_type') && $request->input('action_type') !== 'all') {
            $query->where('action', 'like', '%' . $request->input('action_type') . '%');
        }

        if ($request->filled('user_id') && $request->input('user_id') !== 'all') {
            $query->where('user_id', $request->input('user_id'));
        }

        $logs = $query->latest('id')->paginate(50)->withQueryString();

        return view('admin.audits', compact('logs'));
    }

    /**
     * Streamed CSV download for large datasets.
     */
    public function download(Request $request)
    {
        $fileName = 'audit_logs_' . now()->format('Ymd_His') . '.csv';

        $query = AuditLog::with('user')->orderBy('id');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            echo "\xEF\xBB\xBF"; // BOM for Excel
            fputcsv($out, ['ID', 'User ID', 'User Name', 'Action', 'Description', 'Created At', 'Updated At']);

            foreach ($query->cursor() as $log) {
                fputcsv($out, [
                    $log->id,
                    $log->user_id,
                    optional($log->user)->name,
                    $log->action,
                    $log->description,
                    optional($log->created_at)->toDateTimeString(),
                    optional($log->updated_at)->toDateTimeString(),
                ]);
            }

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}

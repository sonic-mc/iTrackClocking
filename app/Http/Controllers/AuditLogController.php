<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Traits\AuditLogger;
use App\Models\AttendanceLog;



class AuditLogController extends Controller
{

    use AuditLogger;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
    $logs = AuditLog::with('user')->latest()->get();

    return view('admin.audit', compact('logs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show(AuditLog $auditLog)
{
    return view('admin.audit', compact('auditLog'));
}
    /**
     * Display the specified resource.
     */
    public function store(Request $request)
{
    $request->validate([
        'action' => 'required|string|max:255',
        'ip_address' => 'nullable|ip',
    ]);

    AuditLog::create([
        'user_id' => Auth::id(),
        'action' => $request->action,
        'ip_address' => $request->ip_address ?? $request->ip(),
    ]);

    return response()->json(['message' => 'Audit log recorded.'], 201);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AuditLog $auditLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AuditLog $auditLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function dashboardSummary()
    {
        $now = Carbon::now();
    
        $totalEvents = AuditLog::count();
    
        $todayEvents = AuditLog::whereDate('created_at', $now->toDateString())->count();
    
        $uniqueUsers = AuditLog::whereNotNull('user_id')->distinct('user_id')->count('user_id');
    
        $criticalEvents = AuditLog::where('action', 'like', '%critical%')->count();
    
        $exportCount = AuditLog::where('action', 'like', '%export%')->count();
    
        return view('admin.audit.index', compact(
            'totalEvents',
            'todayEvents',
            'uniqueUsers',
            'criticalEvents',
            'exportCount'
        ));
    }

    public function generateAttendanceReport(Request $request)
    {
        $start = $request->input('start_date') ?? Carbon::now()->startOfMonth();
        $end = $request->input('end_date') ?? Carbon::now();

        $logs = AttendanceLog::with('employee')
            ->whereBetween('clock_in_time', [$start, $end])
            ->orderBy('clock_in_time')
            ->get();

        $report = $logs->map(function ($log) {
            $clockIn = Carbon::parse($log->clock_in_time);
            $clockOut = $log->clock_out_time ? Carbon::parse($log->clock_out_time) : null;

            return [
                'employee' => $log->employee->name ?? 'Unknown',
                'date' => $clockIn->toDateString(),
                'clock_in' => $clockIn->format('H:i'),
                'clock_out' => $clockOut ? $clockOut->format('H:i') : '—',
                'late' => $clockIn->hour > 8 ? 'Yes' : 'No',
                'duration' => $clockOut ? $clockIn->diffInHours($clockOut) . ' hrs' : '—',
                'device' => $log->device_info ?? '—',
                'geofence' => $log->geofence_status ? '✅' : '❌',
            ];
        });

        return response()->json(['report' => $report]);
    }

    public function filtered(Request $request)
{
    $query = AuditLog::with('user');

    // 🔍 Search
    if ($search = $request->input('search')) {
        $query->where('action', 'like', "%{$search}%");
    }

    // 📆 Date Range
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
        // Add custom range support if needed
    }

    // 🧠 Action Type
    if ($request->input('action_type') && $request->input('action_type') !== 'all') {
        $query->where('action', 'like', '%' . $request->input('action_type') . '%');
    }

    // 👤 User Role Filter
    if ($request->input('user_filter') && $request->input('user_filter') !== 'all') {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('role', $request->input('user_filter'));
        });
    }

    $logs = $query->latest()->get();

    return view('admin.audit', compact('logs'));
}
}

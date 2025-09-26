<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

trait AuditLogger
{
    /**
     * Log a generic audit event.
     */
    public static function log(
        string $action,
        ?string $message = null,
        ?string $modelType = null,
        ?int $modelId = null,
        
        ?array $changes = null
    ): void {
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'changes'     => $changes ? json_encode($changes) : null,
            'ip_address'  => Request::ip(),
            'url'         => Request::fullUrl(),
            'method'      => Request::method(),
            'user_agent'  => Request::header('User-Agent'),
            'location'    => session('clocking_terminal') ?? session('branch_name') ?? null,
            
        ]);
    }

    /**
     * Log a clocking-specific event (shortcut).
     */
    public static function clocking(
        string $action,
        string $message,
        int $employeeId,
        int $attendanceId,
        string $severity = 'info',
        ?array $meta = null
    ): void {
        self::log(
            $action,
            $message,
            'AttendanceLog',
            $attendanceId,
            $severity,
            array_merge([
                'employee_id' => $employeeId,
                'timestamp'   => now()->toDateTimeString(),
                'location'    => [
                    'lat' => Request::input('location_lat'),
                    'lng' => Request::input('location_lng'),
                ],
                'device'      => Request::input('device_info'),
                'geofence'    => $meta['geofence'] ?? 'unknown',
            ], $meta ?? [])
        );
    }
}

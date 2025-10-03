<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeofenceViolation extends Model
{
    use HasFactory;

    // Optional: explicitly define table name
    protected $table = 'geofence_violations';

    // Mass assignable fields
    protected $fillable = [
        'employee_id',
        'attendance_log_id',
        'latitude',
        'longitude',
        'device_info',
        'violation_time',
        'violation_type',
    ];

    // Cast violation_time to datetime
    protected $casts = [
        'violation_time' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * The employee who triggered this violation
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * The attendance log associated with this violation
     */
    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLog::class);
    }
}

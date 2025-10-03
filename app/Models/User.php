<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\OvertimeLog;
use Carbon\Carbon;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


     // 🔑 Role helper methods
     public function isEmployee(): bool
     {
         return $this->role === 'employee';
     }
 
     public function isManager(): bool
     {
         return $this->role === 'manager';
     }
 
     public function isAdmin(): bool
     {
         return $this->role === 'admin';
     }


     public function attendances()
{
    return $this->hasMany(AttendanceLog::class, 'employee_id');
}


public function isClockedIn(): bool
    {
        $employee = $this->employee;
        if (!$employee) {
            return false;
        }

        // Consider user "clocked in" if there is a log today with clock_in_time set and clock_out_time not set
        return $employee->attendanceLogs()
            ->whereDate('created_at', Carbon::today())
            ->whereNotNull('clock_in_time')
            ->whereNull('clock_out_time')
            ->exists();
    }

public function employee()
{
    return $this->hasOne(Employee::class);
}

public function shifts()
{
    // User → Employee → EmployeeShift
    return $this->hasManyThrough(EmployeeShift::class, Employee::class, 'user_id', 'employee_id');
}

public function overtimes()
{
    // User → Employee → OvertimeLog
    return $this->hasManyThrough(OvertimeLog::class, Employee::class, 'user_id', 'employee_id');
}

}

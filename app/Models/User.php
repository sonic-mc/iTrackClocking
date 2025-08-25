<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\AttendanceLog;

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
    // Check latest attendance record
    $lastAttendance = $this->attendances()->latest()->first();

    return $lastAttendance && is_null($lastAttendance->clock_out_time);
}
}

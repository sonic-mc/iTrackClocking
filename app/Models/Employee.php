<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'branch_id',
        'department_id',
        'position',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    


 // Each employee belongs to a Branch
 public function branch()
 {
     return $this->belongsTo(Branch::class);
 }

 // Each employee belongs to a Department
 public function department()
 {
     return $this->belongsTo(Department::class);
 }

public function shifts()
{
    return $this->hasMany(EmployeeShift::class);
}

public function overtimes()
{
    return $this->hasMany(OvertimeLog::class, 'employee_id');
}

public function attendanceLogs()
{
    return $this->hasMany(\App\Models\AttendanceLog::class, 'employee_id');
}

}

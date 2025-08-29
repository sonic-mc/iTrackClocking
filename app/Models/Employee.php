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
    return $this->belongsTo(User::class);
}

public function shifts()
{
    return $this->hasMany(EmployeeShift::class);
}

public function overtimes()
{
    return $this->hasMany(OvertimeLog::class, 'employee_id');
}

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeShift extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'name', 'date', 'start_time', 'end_time'];

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function employee()
{
    return $this->belongsTo(Employee::class);
}

public function shift()
{
    return $this->belongsTo(Shift::class);
}

}

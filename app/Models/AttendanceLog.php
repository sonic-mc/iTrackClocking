<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'user_id',
        'clock_in_time',
        'clock_out_time',
        'location', // optional
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id');
}

}

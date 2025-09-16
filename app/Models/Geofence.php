<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geofence extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
        'name',
        'latitude',
        'longitude',
        'radius',
    ];
    

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function activeEmployees()
    {
        return $this->hasMany(Employee::class)->where('status', 'active');
    }

    


}

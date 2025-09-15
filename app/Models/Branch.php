<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
  
    protected $fillable = ['name', 'address', 'geofence_coordinates'];

    protected $casts = [
        'geofence_coordinates' => 'array',
    ];

    public function geofence()
{
    return $this->hasOne(Geofence::class);
}

}

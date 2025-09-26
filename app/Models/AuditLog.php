<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

   
    public $timestamps = false;

    

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'module',
        'timestamp',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public static function log(string $action, ?string $description = null, ?string $module = null ): void
{
    self::create([
        'user_id'    => auth()->id(),
        'action'     => $action,
        'description'=> $description,
        'module'     => $module,
        'timestamp'  => now(),
    ]);
}
}

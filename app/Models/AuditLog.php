<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Laravel already manages created_at & updated_at automatically
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, ?string $description = null): void
    {
        self::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'description' => $description,
        ]);
    }
}

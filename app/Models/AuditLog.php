<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * Laravel automatically manages created_at & updated_at.
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'action',
        'description',
    ];

    protected $table = 'audit_logs';


    /**
     * Attribute casting.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Log belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Static helper to create a log entry.
     *
     * @param string      $action
     * @param string|null $description
     * @param int|null    $userId
     * @return void
     */
    public static function log(string $action, ?string $description = null): void
    {
        $userId = auth()->check() && \App\Models\User::where('id', auth()->id())->exists()
            ? auth()->id()
            : null;
    
        self::create([
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
        ]);
    }
    
}

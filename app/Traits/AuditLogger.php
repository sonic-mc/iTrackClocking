<?php

namespace App\Traits;

use App\Models\AuditLog;

trait AuditLogger
{
    /**
     * Store a new audit log entry.
     *
     * @param string $action
     * @param string|null $description
     */
    public function logAudit(string $action, ?string $description = null): void
    {
        AuditLog::create([
            'user_id'     => auth()->check() ? auth()->id() : null,
            'action'      => $action,
            'description' => $description,
        ]);
    }
}

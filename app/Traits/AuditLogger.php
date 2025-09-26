<?php

namespace App\Traits;

use App\Models\AuditLog;

trait AuditLogger
{
    public function logAudit(string $action, ?string $description = null): void
    {
        AuditLog::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'description' => $description,
        ]);
    }
}

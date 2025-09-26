<?php

namespace App\Traits;

use App\Models\AuditLog;

trait AuditLogger
{
    public function logAudit(string $action, ?string $description = null, ?string $module = null ): void
    {
        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'description'=> $description,
            'module'     => $module,
            'timestamp'  => now(),
        ]);
    }
}

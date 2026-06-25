<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public function log(
        string $action,
        string $description = '',
        ?string $model = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $organizationId = null,
        ?int $userId = null,
    ): AuditLog {
        return AuditLog::create([
            'organization_id' => $organizationId ?? Auth::user()?->organization_id,
            'user_id'         => $userId ?? Auth::id(),
            'action'          => $action,
            'model'           => $model,
            'model_id'        => $modelId,
            'old_values'      => $oldValues,
            'new_values'      => $newValues,
            'description'     => $description,
            'ip_address'      => Request::ip(),
            'user_agent'      => Request::userAgent(),
        ]);
    }
}

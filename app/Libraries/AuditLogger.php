<?php

namespace App\Libraries;

use App\Models\AuditLogModel;

class AuditLogger
{
    public static function log(string $action, string $resourceType, ?int $resourceId = null, ?array $oldData = null, ?array $newData = null): void
    {
        $model = new AuditLogModel();

        $model->insert([
            'user_id' => session()->get('user_id'),
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'ip_address' => service('request')->getIPAddress(),
            'user_agent' => substr((string) service('request')->getUserAgent(), 0, 255),
            'old_data' => $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
            'new_data' => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

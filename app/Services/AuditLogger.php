<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogger
{
    public static function created(string $table, int $recordId, array $newValue): AuditLog
    {
        return self::log('CREATE', $table, $recordId, null, $newValue);
    }

    public static function updated(string $table, int $recordId, array $oldValue, array $newValue): AuditLog
    {
        return self::log('EDIT', $table, $recordId, $oldValue, $newValue);
    }

    public static function deleted(string $table, int $recordId, array $oldValue): AuditLog
    {
        return self::log('DELETE', $table, $recordId, $oldValue, null);
    }

    private static function log(string $action, string $table, int $recordId, ?array $oldValue, ?array $newValue): AuditLog
    {
        return AuditLog::create([
            'user_id'    => auth()->id(),
            'table_name' => $table,
            'record_id'  => $recordId,
            'action'     => $action,
            'old_value'  => $oldValue,
            'new_value'  => $newValue,
        ]);
    }
}

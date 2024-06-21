<?php

namespace App\Classes;

use App\Models\AuditLog as AuditLogModel;
use Illuminate\Database\Eloquent\Model;

class AuditLog
{
    public static function log(string $action, Model $model, ?string $description = null): void
    {
        $auditLog = new AuditLogModel();
        $auditLog->action = $action;
        $auditLog->model()->associate($model);
        $auditLog->description = $description;
        $auditLog->user_id = auth()->id();
        $auditLog->save();
    }

    public static function updated(Model $model): void
    {
        $changes = $model->getChanges();
        $changes = collect($changes)->map(function ($newValue, $key) use ($model) {
            $oldValue = $model->getOriginal($key);

            return ['old' => $oldValue, 'new' => $newValue];
        })->toArray();
        unset($changes['updated_at']);

        $user_id = auth()->id();
        $ip_address = request()->ip();
        $user_agent = request()->userAgent();

        $auditLog = new AuditLogModel();
        $auditLog->action = 'updated';
        $auditLog->model()->associate($model);
        $auditLog->changes = $changes;
        $auditLog->user_id = $user_id;
        $auditLog->ip_address = $ip_address;
        $auditLog->user_agent = $user_agent;
        $auditLog->save();
    }
}

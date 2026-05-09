<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait RecordsActivityLogs
{
    public static function bootRecordsActivityLogs(): void
    {
        static::created(function (Model $model): void {
            $model->writeAuditLog('created', [], $model->auditVisibleAttributes($model->getAttributes()));
        });

        static::updated(function (Model $model): void {
            $changes = collect($model->getChanges())
                ->except(['updated_at'])
                ->all();

            if ($changes === []) {
                return;
            }

            $oldValues = [];
            foreach (array_keys($changes) as $key) {
                $oldValues[$key] = $model->getOriginal($key);
            }

            $model->writeAuditLog(
                'updated',
                $model->auditVisibleAttributes($oldValues),
                $model->auditVisibleAttributes($changes)
            );
        });

        static::deleted(function (Model $model): void {
            $model->writeAuditLog('deleted', $model->auditVisibleAttributes($model->getOriginal()), []);
        });
    }

    public function writeAuditLog(string $event, array $oldValues = [], array $newValues = [], array $metadata = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'label' => $this->auditLabel(),
            'ip_address' => request()?->ip(),
            'user_agent' => Str::limit((string) request()?->userAgent(), 500, ''),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
        ]);
    }

    public function auditLabel(): string
    {
        foreach (['name', 'code', 'email', 'transaction_no', 'opname_no', 'symbol'] as $attribute) {
            if (filled($this->getAttribute($attribute))) {
                return (string) $this->getAttribute($attribute);
            }
        }

        return class_basename(static::class).' #'.$this->getKey();
    }

    public function auditVisibleAttributes(array $attributes): array
    {
        return collect($attributes)
            ->except(['password', 'remember_token'])
            ->map(fn ($value) => is_scalar($value) || is_null($value) ? $value : json_encode($value))
            ->all();
    }
}

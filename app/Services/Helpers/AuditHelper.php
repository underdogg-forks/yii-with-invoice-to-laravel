<?php

namespace App\Services\Helpers;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditHelper
{
    /**
     * Log model change.
     */
    public function logChange(Model $model, string $action): void
    {
        $oldValues = $this->getOldValues($model, $action);
        $newValues = $this->getNewValues($model, $action);

        Audit::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get old values for audit.
     */
    protected function getOldValues(Model $model, string $action): ?array
    {
        if ($action === 'created') {
            return null;
        }

        if ($action === 'deleted') {
            return $model->getOriginal();
        }

        if ($action === 'updated') {
            return $model->getOriginal();
        }

        return null;
    }

    /**
     * Get new values for audit.
     */
    protected function getNewValues(Model $model, string $action): ?array
    {
        if ($action === 'deleted') {
            return null;
        }

        return $model->getAttributes();
    }

    /**
     * Get audit trail for model.
     */
    public function getAuditTrail(Model $model): Collection
    {
        return Audit::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Restore model to previous version.
     */
    public function restoreVersion(Model $model, int $auditId): Model
    {
        $audit = Audit::findOrFail($auditId);

        if ($audit->model_type !== get_class($model) || $audit->model_id !== $model->id) {
            throw new \Exception('Audit record does not match model');
        }

        if (!$audit->old_values) {
            throw new \Exception('No old values to restore');
        }

        // Restore old values
        $model->fill($audit->old_values);
        $model->save();

        // Log restoration
        $this->logChange($model, 'restored');

        return $model;
    }

    /**
     * Compare two versions.
     */
    public function compareVersions(int $auditIdA, int $auditIdB): array
    {
        $auditA = Audit::findOrFail($auditIdA);
        $auditB = Audit::findOrFail($auditIdB);

        if ($auditA->model_type !== $auditB->model_type || $auditA->model_id !== $auditB->model_id) {
            throw new \Exception('Audit records do not match the same model');
        }

        $valuesA = $auditA->new_values ?? $auditA->old_values ?? [];
        $valuesB = $auditB->new_values ?? $auditB->old_values ?? [];

        $diff = [];

        // Find differences
        foreach ($valuesA as $key => $valueA) {
            $valueB = $valuesB[$key] ?? null;
            
            if ($valueA !== $valueB) {
                $diff[$key] = [
                    'version_a' => $valueA,
                    'version_b' => $valueB,
                ];
            }
        }

        // Check for keys in B that don't exist in A
        foreach ($valuesB as $key => $valueB) {
            if (!isset($valuesA[$key])) {
                $diff[$key] = [
                    'version_a' => null,
                    'version_b' => $valueB,
                ];
            }
        }

        return $diff;
    }

    /**
     * Get changes made in specific audit.
     */
    public function getChanges(Audit $audit): array
    {
        if (!$audit->old_values || !$audit->new_values) {
            return [];
        }

        $changes = [];

        foreach ($audit->new_values as $key => $newValue) {
            $oldValue = $audit->old_values[$key] ?? null;
            
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'ip_address',
        'user_agent',
        'performed_at',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'performed_at' => 'datetime',
    ];

    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_RESTORE = 'restore';

    const ACTIONS = [
        self::ACTION_CREATE => 'Create',
        self::ACTION_UPDATE => 'Update',
        self::ACTION_DELETE => 'Delete',
        self::ACTION_RESTORE => 'Restore',
    ];

    // Disable timestamps as we use performed_at
    public $timestamps = false;

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by table name
     */
    public function scopeByTable(Builder $query, string $tableName): Builder
    {
        return $query->where('table_name', $tableName);
    }

    /**
     * Scope for filtering by record ID
     */
    public function scopeByRecord(Builder $query, int $recordId): Builder
    {
        return $query->where('record_id', $recordId);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('performed_at', [$startDate, $endDate]);
    }

    /**
     * Scope for create actions
     */
    public function scopeCreates(Builder $query): Builder
    {
        return $query->where('action', self::ACTION_CREATE);
    }

    /**
     * Scope for update actions
     */
    public function scopeUpdates(Builder $query): Builder
    {
        return $query->where('action', self::ACTION_UPDATE);
    }

    /**
     * Scope for delete actions
     */
    public function scopeDeletes(Builder $query): Builder
    {
        return $query->where('action', self::ACTION_DELETE);
    }

    /**
     * Scope for restore actions
     */
    public function scopeRestores(Builder $query): Builder
    {
        return $query->where('action', self::ACTION_RESTORE);
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('performed_at', '>=', now()->subDays($days));
    }

    /**
     * Check if action is create
     */
    public function isCreate(): bool
    {
        return $this->action === self::ACTION_CREATE;
    }

    /**
     * Check if action is update
     */
    public function isUpdate(): bool
    {
        return $this->action === self::ACTION_UPDATE;
    }

    /**
     * Check if action is delete
     */
    public function isDelete(): bool
    {
        return $this->action === self::ACTION_DELETE;
    }

    /**
     * Check if action is restore
     */
    public function isRestore(): bool
    {
        return $this->action === self::ACTION_RESTORE;
    }

    /**
     * Get formatted action name
     */
    public function getActionNameAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? 'Unknown';
    }

    /**
     * Get changes summary
     */
    public function getChangesSummaryAttribute(): array
    {
        if ($this->isCreate()) {
            return ['summary' => 'Record created', 'changes' => $this->new_values];
        }

        if ($this->isDelete()) {
            return ['summary' => 'Record deleted', 'changes' => $this->old_values];
        }

        if ($this->isUpdate()) {
            $changes = [];
            $oldValues = $this->old_values ?? [];
            $newValues = $this->new_values ?? [];
            
            foreach ($newValues as $field => $newValue) {
                $oldValue = $oldValues[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
            
            return ['summary' => 'Record updated', 'changes' => $changes];
        }

        return ['summary' => 'Unknown action', 'changes' => []];
    }

    /**
     * Get human-readable description
     */
    public function getDescriptionAttribute(): string
    {
        $userName = $this->user ? $this->user->name : 'System';
        $actionName = $this->getActionNameAttribute();
        $tableName = str_replace('_', ' ', $this->table_name);
        
        return "{$userName} {$actionName} {$tableName} record (ID: {$this->record_id})";
    }

    /**
     * Get affected fields for update actions
     */
    public function getAffectedFieldsAttribute(): array
    {
        if (!$this->isUpdate()) {
            return [];
        }

        $oldValues = $this->old_values ?? [];
        $newValues = $this->new_values ?? [];
        
        $fields = [];
        foreach ($newValues as $field => $newValue) {
            $oldValue = $oldValues[$field] ?? null;
            if ($oldValue !== $newValue) {
                $fields[] = $field;
            }
        }
        
        return $fields;
    }

    /**
     * Create an audit log entry
     */
    public static function createLog(array $data): self
    {
        $data['performed_at'] = $data['performed_at'] ?? now();
        $data['ip_address'] = $data['ip_address'] ?? request()->ip();
        $data['user_agent'] = $data['user_agent'] ?? request()->userAgent();
        
        return static::create($data);
    }

    /**
     * Log a create action
     */
    public static function logCreate(string $tableName, int $recordId, array $newValues, int $userId = null): self
    {
        return static::createLog([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action' => self::ACTION_CREATE,
            'new_values' => $newValues,
            'user_id' => $userId,
        ]);
    }

    /**
     * Log an update action
     */
    public static function logUpdate(string $tableName, int $recordId, array $oldValues, array $newValues, int $userId = null): self
    {
        return static::createLog([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action' => self::ACTION_UPDATE,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId,
        ]);
    }

    /**
     * Log a delete action
     */
    public static function logDelete(string $tableName, int $recordId, array $oldValues, int $userId = null): self
    {
        return static::createLog([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action' => self::ACTION_DELETE,
            'old_values' => $oldValues,
            'user_id' => $userId,
        ]);
    }

    /**
     * Log a restore action
     */
    public static function logRestore(string $tableName, int $recordId, array $newValues, int $userId = null): self
    {
        return static::createLog([
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action' => self::ACTION_RESTORE,
            'new_values' => $newValues,
            'user_id' => $userId,
        ]);
    }

    /**
     * Get audit log for a specific record
     */
    public static function getRecordHistory(string $tableName, int $recordId): Builder
    {
        return static::query()
            ->where('table_name', $tableName)
            ->where('record_id', $recordId)
            ->with('user')
            ->orderBy('performed_at', 'desc');
    }

    /**
     * Get audit statistics
     */
    public static function getAuditStats($startDate = null, $endDate = null): array
    {
        $query = static::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('performed_at', [$startDate, $endDate]);
        }
        
        return [
            'total_actions' => $query->count(),
            'creates' => $query->where('action', self::ACTION_CREATE)->count(),
            'updates' => $query->where('action', self::ACTION_UPDATE)->count(),
            'deletes' => $query->where('action', self::ACTION_DELETE)->count(),
            'restores' => $query->where('action', self::ACTION_RESTORE)->count(),
        ];
    }

    /**
     * Get most active users
     */
    public static function getMostActiveUsers(int $limit = 10, $startDate = null, $endDate = null): array
    {
        $query = static::query()
            ->selectRaw('user_id, COUNT(*) as action_count')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('action_count', 'desc')
            ->limit($limit);
        
        if ($startDate && $endDate) {
            $query->whereBetween('performed_at', [$startDate, $endDate]);
        }
        
        return $query->with('user')->get()->toArray();
    }

    /**
     * Get most modified tables
     */
    public static function getMostModifiedTables(int $limit = 10, $startDate = null, $endDate = null): array
    {
        $query = static::query()
            ->selectRaw('table_name, COUNT(*) as action_count')
            ->groupBy('table_name')
            ->orderBy('action_count', 'desc')
            ->limit($limit);
        
        if ($startDate && $endDate) {
            $query->whereBetween('performed_at', [$startDate, $endDate]);
        }
        
        return $query->get()->toArray();
    }

    /**
     * Clean up old audit logs
     */
    public static function cleanupOldLogs(int $daysToKeep = 90): int
    {
        return static::where('performed_at', '<', now()->subDays($daysToKeep))->delete();
    }
}

<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasAuditColumns
{
    /**
     * Indicates if the model is currently force deleting.
     *
     * @var bool
     */
    protected $forceDeleting = false;

    /**
     * Boot the HasAuditColumns trait for a model.
     */
    public static function bootHasAuditColumns(): void
    {
        // Add global scope to exclude soft deleted records
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->whereNull($builder->getModel()->getQualifiedDeletedAtColumn());
        });

        // Set created_at and created_by when creating (NOT updated_at/updated_by)
        static::creating(function ($model) {
            $now = time(); // epoch timestamp

            $model->created_at = $now;

            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });

        // Set updated_at and updated_by when updating (NOT on create)
        static::updating(function ($model) {
            $model->updated_at = time(); // epoch timestamp

            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Initialize the soft deleting trait for an instance.
     */
    public function initializeHasAuditColumns(): void
    {
        if (! isset($this->casts['deleted_at'])) {
            $this->casts['deleted_at'] = 'integer';
        }
    }

    /**
     * Perform the actual delete query on this model instance.
     */
    protected function performDeleteOnModel(): void
    {
        if ($this->forceDeleting) {
            $this->newModelQuery()->where($this->getKeyName(), $this->getKey())->forceDelete();
            $this->exists = false;

            return;
        }

        $this->runSoftDelete();
    }

    /**
     * Perform the actual soft delete on this model.
     * Only sets deleted_at and deleted_by (NOT updated_at/updated_by)
     */
    protected function runSoftDelete(): void
    {
        $query = $this->newModelQuery()->where($this->getKeyName(), $this->getKey());

        $time = time(); // epoch timestamp

        $columns = [
            'deleted_at' => $time,
        ];

        if (Auth::check()) {
            $columns['deleted_by'] = Auth::id();
        }

        $this->{$this->getDeletedAtColumn()} = $time;

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));
    }

    /**
     * Restore a soft-deleted model instance.
     */
    public function restore(): bool
    {
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = null;
        $this->deleted_by = null;

        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Determine if the model instance has been soft-deleted.
     */
    public function trashed(): bool
    {
        return ! is_null($this->{$this->getDeletedAtColumn()});
    }

    /**
     * Get a new query builder that includes soft deletes.
     */
    public static function withTrashed(): Builder
    {
        return (new static)->newQueryWithoutScope('notDeleted');
    }

    /**
     * Get a new query builder that only includes soft deletes.
     */
    public static function onlyTrashed(): Builder
    {
        return static::withTrashed()->whereNotNull((new static)->getQualifiedDeletedAtColumn());
    }

    /**
     * Get the name of the "deleted at" column.
     */
    public function getDeletedAtColumn(): string
    {
        return defined(static::class.'::DELETED_AT') ? static::DELETED_AT : 'deleted_at';
    }

    /**
     * Get the fully qualified "deleted at" column.
     */
    public function getQualifiedDeletedAtColumn(): string
    {
        return $this->qualifyColumn($this->getDeletedAtColumn());
    }

    /**
     * Determine if the model is currently force deleting.
     */
    public function isForceDeleting(): bool
    {
        return $this->forceDeleting;
    }

    /**
     * Force a hard delete on a soft deleted model.
     */
    public function forceDelete(): ?bool
    {
        $this->forceDeleting = true;

        return tap($this->delete(), function ($deleted) {
            $this->forceDeleting = false;

            if ($deleted) {
                $this->fireModelEvent('forceDeleted', false);
            }
        });
    }

    /**
     * Register a "softDeleted" model event callback with the dispatcher.
     */
    public static function softDeleted(callable $callback): void
    {
        static::registerModelEvent('trashed', $callback);
    }

    /**
     * Register a "restoring" model event callback with the dispatcher.
     */
    public static function restoring(callable $callback): void
    {
        static::registerModelEvent('restoring', $callback);
    }

    /**
     * Register a "restored" model event callback with the dispatcher.
     */
    public static function restored(callable $callback): void
    {
        static::registerModelEvent('restored', $callback);
    }

    /**
     * Register a "forceDeleted" model event callback with the dispatcher.
     */
    public static function forceDeleted(callable $callback): void
    {
        static::registerModelEvent('forceDeleted', $callback);
    }

    /**
     * Get the user who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted this record.
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}

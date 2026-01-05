<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

trait HasAuditColumns
{
    use SoftDeletes;

    /**
     * Boot the HasAuditColumns trait for a model.
     */
    public static function bootHasAuditColumns(): void
    {
        // Set created_at, updated_at, created_by and updated_by when creating
        static::creating(function ($model) {
            $now = time(); // epoch timestamp

            $model->created_at = $now;
            $model->updated_at = $now;

            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        // Set updated_at and updated_by when updating
        static::updating(function ($model) {
            $model->updated_at = time(); // epoch timestamp

            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Set deleted_at and deleted_by when soft deleting
        static::deleting(function ($model) {
            if (!$model->isForceDeleting()) {
                $model->deleted_at = time(); // epoch timestamp

                if (Auth::check()) {
                    $model->deleted_by = Auth::id();
                }

                // Save manually because we're in deleting event
                $model->save();
            }
        });
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

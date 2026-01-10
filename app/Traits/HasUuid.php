<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Cursor;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait.
     */
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Paginate using cursor but encode/decode with UUID instead of ID.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $cursorName
     * @param  Cursor|string|null  $cursor
     */
    public function scopeUuidCursorPaginate($query, $perPage = null, $columns = ['*'], $cursorName = 'cursor', $cursor = null): array
    {
        $cursor = $cursor ?? request()->input($cursorName);

        // Get the table name
        $table = $query->getModel()->getTable();

        // Get total count before pagination
        $total = $query->toBase()->getCountForPagination();

        // Decode UUID cursor to ID cursor
        if ($cursor) {
            $cursor = $this->decodeUuidCursor($cursor);
        }

        // Ensure 'id' is always in the columns for cursor pagination
        if ($columns !== ['*'] && !in_array('id', $columns) && !in_array("{$table}.id", $columns)) {
            $columns[] = "{$table}.id";
        }

        // Perform cursor pagination with ID (use table-qualified column name)
        $paginator = $query->orderBy("{$table}.id")->cursorPaginate($perPage, $columns, $cursorName, $cursor);

        // Transform cursors to use UUID and return as array
        $response = $this->transformCursorsToUuid($paginator, $table);

        // Add total count to response
        $response['total'] = $total;

        return $response;
    }

    /**
     * Decode UUID-based cursor to ID-based cursor.
     */
    protected function decodeUuidCursor(string $cursor): ?Cursor
    {
        try {
            $decoded = json_decode(base64_decode($cursor), true);

            if (!$decoded) {
                return null;
            }

            // Find the UUID key and get the ID
            $newParams = [];
            $pointsToNext = true;

            foreach ($decoded as $key => $value) {
                if ($key === '_pointsToNextItems') {
                    $pointsToNext = $value;

                    continue;
                }

                // This is a table.column => uuid pair
                // We need to look up the ID from UUID
                if (str_contains($key, '.')) {
                    [$table, $column] = explode('.', $key);

                    if ($column === 'uuid') {
                        // Look up ID from UUID
                        $id = DB::table($table)->where('uuid', $value)->value('id');

                        if ($id) {
                            $newParams["{$table}.id"] = $id;
                        }
                    } else {
                        $newParams[$key] = $value;
                    }
                } else {
                    $newParams[$key] = $value;
                }
            }

            return new Cursor($newParams, $pointsToNext);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Transform paginator cursors from ID to UUID.
     */
    protected function transformCursorsToUuid(CursorPaginator $paginator, string $table): array
    {
        $items = $paginator->items();
        $response = $paginator->toArray();

        // Remove ID-based cursors
        $response['next_cursor'] = null;
        $response['prev_cursor'] = null;

        if (empty($items)) {
            return $response;
        }

        // Transform next_cursor
        if ($paginator->hasMorePages()) {
            $lastItem = end($items);
            $response['next_cursor'] = $this->encodeUuidCursor($table, $lastItem->uuid, true);
        }

        // Transform prev_cursor
        if ($paginator->previousCursor()) {
            $firstItem = reset($items);
            $response['prev_cursor'] = $this->encodeUuidCursor($table, $firstItem->uuid, false);
        }

        return $response;
    }

    /**
     * Encode UUID-based cursor.
     */
    protected function encodeUuidCursor(string $table, string $uuid, bool $pointsToNext): string
    {
        return base64_encode(json_encode([
            "{$table}.uuid" => $uuid,
            '_pointsToNextItems' => $pointsToNext,
        ]));
    }
}

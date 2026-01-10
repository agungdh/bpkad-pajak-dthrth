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

            $params = $decoded;
            $pointsToNext = $params['_pointsToNextItems'] ?? true;
            unset($params['_pointsToNextItems']);

            // Generic table lookup
            // We assume the model class using this trait knows its table
            // But here we are in a static context or scope? No, scope is not static but trait methods are ...
            // Wait, this is called from scope which has $query.
            // But decodeUuidCursor doesn't have query.
            // We need to find table name from key.

            $newParams = [];
            foreach ($params as $key => $value) {
                if (str_ends_with($key, '.uuid')) {
                    $table = substr($key, 0, -5); // remove .uuid
                    $id = DB::table($table)->where('uuid', $value)->value('id');

                    if ($id) {
                        $newParams["{$table}.id"] = $id;
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
        $response = $paginator->toArray();

        // Remove ID-based cursors
        $response['next_cursor'] = null;
        $response['prev_cursor'] = null;

        if ($paginator->hasMorePages() && $paginator->nextCursor()) {
            $cursor = $paginator->nextCursor();
            $response['next_cursor'] = $this->encodeGenericCursor($cursor, $table, $paginator->items(), true);
        }

        if ($paginator->previousCursor()) {
            $cursor = $paginator->previousCursor();
            $response['prev_cursor'] = $this->encodeGenericCursor($cursor, $table, $paginator->items(), false);
        }

        return $response;
    }

    /**
     * Encode generic cursor, swapping ID for UUID.
     */
    protected function encodeGenericCursor(Cursor $cursor, string $table, array $items, bool $isNext): string
    {
        $cursorArray = $cursor->toArray();
        if (isset($cursorArray['_pointsToNextItems'])) {
            unset($cursorArray['_pointsToNextItems']);
        }
        $params = $cursorArray;

        // We need the UUID of the reference item.
        // For Next cursor, it's the last item.
        // For Prev cursor, it's the first item.
        $item = $isNext ? end($items) : reset($items);

        // Safety check
        if ($item && isset($item->uuid) && isset($params["{$table}.id"])) {
            unset($params["{$table}.id"]);
            $params["{$table}.uuid"] = $item->uuid;
        }

        return base64_encode(json_encode(array_merge(
            $params,
            ['_pointsToNextItems' => $cursor->pointsToNextItems()]
        )));
    }
}

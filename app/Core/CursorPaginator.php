<?php
namespace App\Core;

/**
 * Universal Keyset / Cursor-Based Pagination Engine
 * Eliminates MySQL OFFSET performance lag ($O(N)$ -> $O(1)$) and prevents duplicate/skipped rows.
 */
class CursorPaginator {

    /**
     * Encode cursor payload into an opaque URL-safe string
     */
    public static function encodeCursor(array $payload): string {
        $json = json_encode($payload);
        return rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
    }

    /**
     * Decode opaque cursor token into structured payload
     */
    public static function decodeCursor(?string $cursor): ?array {
        if (empty($cursor)) {
            return null;
        }

        $normalized = strtr($cursor, '-_', '+/');
        $padding = strlen($normalized) % 4;
        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $json = @base64_decode($normalized, true);
        if ($json === false) {
            return null;
        }

        $data = @json_decode($json, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Paginate query using Keyset Cursor
     * 
     * @param string $baseSql Base SQL query WITHOUT ORDER BY or LIMIT
     * @param array $params Query parameters
     * @param int $limit Number of items to fetch
     * @param string|null $cursor Raw cursor string passed in request
     * @param string $orderColumn Column used for cursor seek (e.g. 'c.id' or 'id')
     * @param string $direction 'DESC' or 'ASC'
     * @param string|null $tableAlias Optional table prefix alias for the column
     * @return array ['items' => array, 'next_cursor' => string|null, 'has_more' => bool, 'limit' => int]
     */
    public static function paginate(
        string $baseSql,
        array $params = [],
        int $limit = 20,
        ?string $cursor = null,
        string $orderColumn = 'id',
        string $direction = 'DESC',
        ?string $tableAlias = null
    ): array {
        $limit = max(1, min($limit, 100)); // Cap between 1 and 100
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $operator = ($direction === 'DESC') ? '<' : '>';

        $decoded = self::decodeCursor($cursor);
        $cursorVal = $decoded['v'] ?? null;

        $targetCol = $tableAlias ? "{$tableAlias}.{$orderColumn}" : $orderColumn;

        $whereClause = "";
        $execParams = $params;

        if ($cursorVal !== null) {
            $hasWhere = (bool)preg_match('/\bWHERE\b/i', $baseSql);
            $connector = $hasWhere ? " AND " : " WHERE ";
            $whereClause = "{$connector} {$targetCol} {$operator} ? ";
            $execParams[] = $cursorVal;
        }

        // Fetch limit + 1 to determine if there is a next page
        $fetchLimit = $limit + 1;
        $sql = "{$baseSql} {$whereClause} ORDER BY {$targetCol} {$direction} LIMIT {$fetchLimit}";

        $rows = Database::query($sql, $execParams);

        $hasMore = count($rows) > $limit;
        if ($hasMore) {
            array_pop($rows); // Remove the extra record
        }

        $nextCursor = null;
        if ($hasMore && !empty($rows)) {
            $lastItem = end($rows);
            // Extract seek key from row
            $seekColKey = str_contains($orderColumn, '.') ? substr(strrchr($orderColumn, '.'), 1) : $orderColumn;
            $seekVal = $lastItem[$seekColKey] ?? null;
            if ($seekVal !== null) {
                $nextCursor = self::encodeCursor(['v' => $seekVal, 't' => time()]);
            }
        }

        return [
            'items' => $rows,
            'next_cursor' => $nextCursor,
            'has_more' => $hasMore,
            'limit' => $limit,
            'count' => count($rows)
        ];
    }
}

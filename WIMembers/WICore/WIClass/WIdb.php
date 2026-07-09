<?php
declare(strict_types=1);

/**
 * Database Class
 * Created by Warner Infinity
 * Author Jules Warner
 */
final class WIdb extends PDO
{
    private static ?self $_instance = null;

    public function __construct(
        string $dbType,
        string $dbHost,
        string $dbName,
        string $dbUser,
        string $dbPass
    ) {
        try {
            parent::__construct(
                $dbType . ':host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8mb4',
                $dbUser,
                $dbPass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            $this->exec('SET NAMES utf8mb4');
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed.', 0, $e);
        }
    }

    public static function getInstance(): self
    {
        if (self::$_instance === null) {
            self::$_instance = new self(
                (string) DB_TYPE,
                (string) DB_HOST,
                (string) DB_NAME,
                (string) DB_USER,
                (string) DB_PASS
            );
        }

        return self::$_instance;
    }

    private function bindParams(PDOStatement $stmt, string $sql, array $params = []): void
    {
        foreach ($params as $key => $value) {
            $placeholder = ':' . ltrim((string) $key, ':');

            if (strpos($sql, $placeholder) === false) {
                continue;
            }

            if (is_int($value)) {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_INT);
            } elseif (is_bool($value)) {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_BOOL);
            } elseif ($value === null) {
                $stmt->bindValue($placeholder, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue($placeholder, (string) $value, PDO::PARAM_STR);
            }
        }
    }

    public function select(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $stmt = $this->prepare($sql);
        $this->bindParams($stmt, $sql, $params);
        $stmt->execute();

        $result = $stmt->fetchAll($fetchMode);
        $stmt->closeCursor();

        return is_array($result) ? $result : [];
    }


    /**
     * Return the first row for a query or an empty array.
     *
     * @param array<string,mixed> $params
     * @return array<string,mixed>
     */
    public function row(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $rows = $this->select($sql, $params, $fetchMode);

        return isset($rows[0]) && is_array($rows[0]) ? $rows[0] : [];
    }

    public function tableExists(string $table): bool
    {
        $table = preg_replace('/[^A-Za-z0-9_]/', '', $table) ?: '';

        if ($table === '') {
            return false;
        }

        $rows = $this->select(
            'SELECT COUNT(*) AS total
               FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = :table
              LIMIT 1',
            ['table' => $table]
        );

        return (int) ($rows[0]['total'] ?? 0) > 0;
    }

    public function columnExists(string $table, string $column): bool
    {
        $table = preg_replace('/[^A-Za-z0-9_]/', '', $table) ?: '';
        $column = preg_replace('/[^A-Za-z0-9_]/', '', $column) ?: '';

        if ($table === '' || $column === '') {
            return false;
        }

        $rows = $this->select(
            'SELECT COUNT(*) AS total
               FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = :table
                AND COLUMN_NAME = :column
              LIMIT 1',
            [
                'table' => $table,
                'column' => $column,
            ]
        );

        return (int) ($rows[0]['total'] ?? 0) > 0;
    }

    public function selectID(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $stmt = $this->prepare($sql);

        foreach ($params as $key => $value) {
            $placeholder = ':' . ltrim((string) $key, ':');

            if (strpos($sql, $placeholder) === false) {
                continue;
            }

            $stmt->bindValue($placeholder, (int) $value, PDO::PARAM_INT);
        }

        $stmt->execute();
        $result = $stmt->fetchAll($fetchMode);
        $stmt->closeCursor();

        return is_array($result) ? $result : [];
    }

    /**
     * Legacy compatibility:
     * old signature sometimes included a $while string before fetch mode.
     */
    public function Selected(
        string $sql,
        array $params = [],
        string $while = '',
        int $fetchMode = PDO::FETCH_ASSOC
    ): array {
        $result = $this->select($sql, $params, $fetchMode);

        if ($while !== '') {
            foreach ($result as $_row) {
                echo $while;
            }
        }

        return $result;
    }

    /**
     * Backward compatible:
     * Legacy: selectColumn($sql, $array, $column)
     * New:    selectColumn($sql, $column, $array)
     */
    public function selectColumn(
        string $sql,
        mixed $arg2 = null,
        mixed $arg3 = null,
        int $fetchMode = PDO::FETCH_ASSOC
    ): mixed {
        $params = [];
        $column = null;

        if (is_array($arg2)) {
            $params = $arg2;
            $column = is_string($arg3) ? $arg3 : null;
        } else {
            $column = is_string($arg2) ? $arg2 : null;
            $params = is_array($arg3) ? $arg3 : [];
        }

        $stmt = $this->prepare($sql);
        $this->bindParams($stmt, $sql, $params);
        $stmt->execute();

        $result = $stmt->fetch($fetchMode);
        $stmt->closeCursor();

        if ($column !== null && is_array($result) && array_key_exists($column, $result)) {
            return $result[$column];
        }

        return null;
    }

    public function blindFreeColumn(
        string $sql,
        string $column,
        int $fetchMode = PDO::FETCH_ASSOC
    ): mixed {
        $stmt = $this->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch($fetchMode);
        $stmt->closeCursor();

        if (is_array($result) && array_key_exists($column, $result)) {
            return $result[$column];
        }

        return null;
    }

    public function bindfree(string $query, int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $stmt = $this->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll($fetchMode);
        $stmt->closeCursor();

        return is_array($result) ? $result : [];
    }

    public function insert(string $table, array $data): bool
    {
        if ($data === []) {
            return false;
        }

        ksort($data);

        $fieldNames = '`' . implode('`, `', array_keys($data)) . '`';
        $fieldValues = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$fieldNames}) VALUES ({$fieldValues})";
        $stmt = $this->prepare($sql);

        foreach ($data as $key => $value) {
            $placeholder = ':' . $key;

            if (is_int($value)) {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_INT);
            } elseif (is_bool($value)) {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_BOOL);
            } elseif ($value === null) {
                $stmt->bindValue($placeholder, null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue($placeholder, (string) $value, PDO::PARAM_STR);
            }
        }

        return $stmt->execute();
    }

    public function Arrayinsert(string $table, array $data): bool
    {
        return $this->insert($table, $data);
    }

    public function update(
        string $table,
        array $data,
        string $where,
        array $whereBindArray = []
    ): bool {
        if ($data === []) {
            return false;
        }

        ksort($data);

        $fieldDetails = [];
        foreach ($data as $key => $value) {
            $fieldDetails[] = "`{$key}` = :{$key}";
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $fieldDetails) . " WHERE {$where}";
        $stmt = $this->prepare($sql);

        $this->bindParams($stmt, $sql, $data);
        $this->bindParams($stmt, $sql, $whereBindArray);

        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    public function delete(string $table, string $where, array $bind = [], int $limit = 1): bool
    {
        $sql = "DELETE FROM {$table} WHERE {$where} LIMIT {$limit}";
        $stmt = $this->prepare($sql);

        $this->bindParams($stmt, $sql, $bind);

        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    public function exists(string $sql, array $params = []): bool
    {
        $stmt = $this->prepare($sql);
        $this->bindParams($stmt, $sql, $params);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return is_array($row) && $row !== [];
    }

    public function first(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): ?array
    {
        $stmt = $this->prepare($sql);
        $this->bindParams($stmt, $sql, $params);
        $stmt->execute();

        $row = $stmt->fetch($fetchMode);
        $stmt->closeCursor();

        return is_array($row) ? $row : null;
    }

    public function scalar(string $sql, array $params = []): mixed
    {
        $stmt = $this->prepare($sql);
        $this->bindParams($stmt, $sql, $params);
        $stmt->execute();

        $value = $stmt->fetchColumn();
        $stmt->closeCursor();

        return $value;
    }

    public function lastInsertIdSafe(): int
    {
        return (int) $this->lastInsertId();
    }
}
<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIdb
| File: WIdb.php
| Location: /WIAdmin/WICore/WIClass/WIdb.php
| Type: Database Layer
| Layer: Shared Core
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Canonical database layer for shared WI systems.
|
| Notes:
| - Singleton access via WIdb::getInstance()
| - PDO is the canonical query layer
| - PDO is the only connection/query implementation
| - Prepared statements everywhere
|--------------------------------------------------------------------------
*/

class WIdb extends PDO
{
    private static ?self $instance = null;

    public function __construct(
        string $dbType,
        string $dbHost,
        string $dbName,
        string $dbUser,
        string $dbPass
    ) {
        $dsn = sprintf(
            '%s:host=%s;dbname=%s;charset=utf8mb4',
            $dbType,
            $dbHost,
            $dbName
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        if (defined('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY')) {
            $options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
        }

        parent::__construct($dsn, $dbUser, $dbPass, $options);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(
                (string) DB_TYPE,
                (string) DB_HOST,
                (string) DB_NAME,
                (string) DB_USER,
                (string) DB_PASS
            );
        }

        return self::$instance;
    }

    public function getPDO(): self
    {
        return $this;
    }

    public function begin(): bool
    {
        return $this->beginTransaction();
    }

    public function commitTransaction(): bool
    {
        return $this->commit();
    }

    public function rollbackTransaction(): bool
    {
        return $this->rollBack();
    }

    public function select(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $stmt = $this->prepareAndBind($sql, $params);
        $stmt->execute();

        $result = $stmt->fetchAll($fetchMode);
        $stmt->closeCursor();

        return is_array($result) ? $result : [];
    }

    public function selectID(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $normalized = [];
        foreach ($params as $key => $value) {
            $normalized[$key] = is_numeric($value) ? (int) $value : 0;
        }

        return $this->select($sql, $normalized, $fetchMode);
    }

    public function selectwithOptions(string $sql, array $params = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        return $this->select($sql, $params, $fetchMode);
    }

    public function Selected(string $sql, array $params = [], string $while = '', int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $result = $this->select($sql, $params, $fetchMode);

        if ($while !== '') {
            foreach ($result as $row) {
                echo $while;
            }
        }

        return $result;
    }

    public function selectColumn(string $sql, mixed $arg2 = null, mixed $arg3 = null, int $fetchMode = PDO::FETCH_ASSOC): mixed
    {
        $params = [];
        $column = null;

        if (is_array($arg2)) {
            $params = $arg2;
            $column = is_string($arg3) ? $arg3 : null;
        } else {
            $column = is_string($arg2) ? $arg2 : null;
            $params = is_array($arg3) ? $arg3 : [];
        }

        $stmt = $this->prepareAndBind($sql, $params);
        $stmt->execute();

        $result = $stmt->fetch($fetchMode);
        $stmt->closeCursor();

        if ($column !== null && is_array($result) && array_key_exists($column, $result)) {
            return $result[$column];
        }

        return null;
    }

    public function blindFreeColumn(string $sql, string $column, int $fetchMode = PDO::FETCH_ASSOC): mixed
    {
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
        if (!$this->isSafeIdentifier($table) || $data === []) {
            return false;
        }

        $columns = [];
        $placeholders = [];
        $params = [];

        foreach ($data as $column => $value) {
            if (!$this->isSafeIdentifier((string) $column)) {
                continue;
            }

            $columns[] = '`' . $column . '`';
            $placeholders[] = ':' . $column;
            $params[$column] = $value;
        }

        if ($columns === []) {
            return false;
        }

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->prepareAndBind($sql, $params);
        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    public function Arrayinsert(string $table, array $data): bool
    {
        return $this->insert($table, $data);
    }

    public function update(string $table, array $data, string $where, array $whereBindArray = []): bool
    {
        if (!$this->isSafeIdentifier($table) || $data === []) {
            return false;
        }

        $assignments = [];
        $params = [];

        foreach ($data as $column => $value) {
            if (!$this->isSafeIdentifier((string) $column)) {
                continue;
            }

            $assignments[] = sprintf('`%s` = :%s', $column, $column);
            $params[$column] = $value;
        }

        if ($assignments === []) {
            return false;
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', $assignments),
            $where
        );

        $stmt = $this->prepareAndBind($sql, array_merge($params, $whereBindArray));
        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    public function delete(string $table, string $where, array $bind = [], int $limit = 1): bool
    {
        if (!$this->isSafeIdentifier($table)) {
            return false;
        }

        $limit = max(1, $limit);

        $sql = sprintf(
            'DELETE FROM `%s` WHERE %s LIMIT %d',
            $table,
            $where,
            $limit
        );

        $stmt = $this->prepareAndBind($sql, $bind);
        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    public function Fulldelete(string $table, string $where, array $bind = []): bool
    {
        if (!$this->isSafeIdentifier($table)) {
            return false;
        }

        $sql = sprintf(
            'DELETE FROM `%s` WHERE %s',
            $table,
            $where
        );

        $stmt = $this->prepareAndBind($sql, $bind);
        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    public function exists(string $table, string $where, array $params = []): bool
    {
        if (!$this->isSafeIdentifier($table)) {
            return false;
        }

        $sql = sprintf('SELECT 1 FROM `%s` WHERE %s LIMIT 1', $table, $where);
        $stmt = $this->prepareAndBind($sql, $params);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return is_array($row);
    }

    public function tableExists(string $table): bool
    {
        if (!$this->isSafeIdentifier($table)) {
            return false;
        }

        $result = $this->select(
            'SELECT COUNT(*) AS count_value
             FROM INFORMATION_SCHEMA.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name',
            ['table_name' => $table]
        );

        return (int) ($result[0]['count_value'] ?? 0) > 0;
    }

    public function columnExists(string $table, string $column): bool
    {
        if (!$this->isSafeIdentifier($table) || !$this->isSafeIdentifier($column)) {
            return false;
        }

        $result = $this->select(
            'SELECT COUNT(*) AS count_value
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = :column_name',
            [
                'table_name' => $table,
                'column_name' => $column,
            ]
        );

        return (int) ($result[0]['count_value'] ?? 0) > 0;
    }

    private function prepareAndBind(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->prepare($sql);

        foreach ($params as $key => $value) {
            $placeholder = ':' . ltrim((string) $key, ':');

            if (strpos($sql, $placeholder) === false) {
                continue;
            }

            $type = PDO::PARAM_STR;

            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } elseif ($value === null) {
                $type = PDO::PARAM_NULL;
            } elseif (is_float($value)) {
                $value = (string) $value;
            }

            $stmt->bindValue($placeholder, $value, $type);
        }

        return $stmt;
    }

    private function isSafeIdentifier(string $value): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_]+$/', $value);
    }
}
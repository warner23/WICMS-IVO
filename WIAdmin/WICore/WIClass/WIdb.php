<?php
declare(strict_types=1);


/**
 * Database Class
 * Created by Warner Infinity
 * Author Jules Warner
 */
class WIdb extends PDO
{
    private static ?self $_instance = null;

    public $mysqli;

    public function __construct($DB_TYPE, $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS)
    {

                $this->mysqli = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME
        );

        if ($this->mysqli->connect_error) {
            die("Database connection failed: " . $this->mysqli->connect_error);
        }

        
        try {

            parent::__construct(
                $DB_TYPE . ':host=' . $DB_HOST . ';dbname=' . $DB_NAME . ';charset=utf8mb4',
                $DB_USER,
                $DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

        } catch (PDOException $e) {

            throw new RuntimeException(
                'Database connection failed: ' . $e->getMessage()
            );

        }
    }

    /**
     * Create instance if it doesn't exist
     */
    public static function getInstance(): self
    {
        if (self::$_instance === null) {
            self::$_instance = new self(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
        }

        return self::$_instance;
    }

    /**
     * Internal safe binder
     */
    private function bindParams(PDOStatement $stmt, string $sql, array $params = [], int $defaultType = PDO::PARAM_STR): void
    {
        foreach ($params as $key => $value) {
            $placeholder = ':' . ltrim((string)$key, ':');

            if (strpos($sql, $placeholder) === false) {
                continue;
            }

            $type = $defaultType;

            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } elseif ($value === null) {
                $type = PDO::PARAM_NULL;
            }

            $stmt->bindValue($placeholder, $value, $type);
        }
    }

    public function select(string $sql, array $array = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);

        $this->bindParams($stmt, $sql, $array, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll($fetchMode);
        $stmt->closeCursor();

        return is_array($result) ? $result : [];
    }

    public function selectID(string $sql, array $array = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);

        foreach ($array as $key => $value) {
            $placeholder = ':' . ltrim((string)$key, ':');

            if (strpos($sql, $placeholder) === false) {
                continue;
            }

            $stmt->bindValue($placeholder, (int)$value, PDO::PARAM_INT);
        }

        $stmt->execute();
        $result = $stmt->fetchAll($fetchMode);
        $stmt->closeCursor();

        return is_array($result) ? $result : [];
    }

    public function selectwithOptions(string $sql, array $array = [], int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);

        $this->bindParams($stmt, $sql, $array, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll($fetchMode);
        $stmt->closeCursor();

        return is_array($result) ? $result : [];
    }

    public function Selected(string $sql, array $array = [], string $while = '', int $fetchMode = PDO::FETCH_ASSOC): array
    {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);

        $this->bindParams($stmt, $sql, $array, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll($fetchMode);
        $stmt->closeCursor();

        foreach ($result as $row) {
            echo $while;
        }

        return is_array($result) ? $result : [];
    }

    /**
     * Backward compatible:
     * Legacy: selectColumn($sql, $array, $column)
     * New:    selectColumn($sql, $column, $array)
     */
    public function selectColumn(string $sql, $arg2 = null, $arg3 = null, int $fetchMode = PDO::FETCH_ASSOC)
    {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);

        $array = [];
        $column = null;

        if (is_array($arg2)) {
            $array = $arg2;
            $column = is_string($arg3) ? $arg3 : null;
        } else {
            $column = is_string($arg2) ? $arg2 : null;
            $array = is_array($arg3) ? $arg3 : [];
        }

        $this->bindParams($stmt, $sql, $array, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch($fetchMode);
        $stmt->closeCursor();

        if ($column !== null && is_array($result) && array_key_exists($column, $result)) {
            return $result[$column];
        }

        return null;
    }

    public function blindFreeColumn(string $sql, string $column, int $fetchMode = PDO::FETCH_ASSOC)
    {
        $db = self::getInstance();
        $stmt = $db->prepare($sql);
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
        $db = self::getInstance();
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll($fetchMode);
        $stmt->closeCursor();

        return is_array($result) ? $result : [];
    }

    public function insert(string $table, array $data): bool
    {
        if (empty($data)) {
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
                $stmt->bindValue($placeholder, (string)$value, PDO::PARAM_STR);
            }
        }

        return $stmt->execute();
    }

    public function Arrayinsert(string $table, array $data): bool
    {
        return $this->insert($table, $data);
    }

    public function update(string $table, array $data, string $where, array $whereBindArray = []): bool
    {
        if (empty($data)) {
            return false;
        }

        $db = self::getInstance();
        ksort($data);

        $fieldDetails = [];
        foreach ($data as $key => $value) {
            $fieldDetails[] = "`{$key}` = :{$key}";
        }

        $sql = "UPDATE {$table} SET " . implode(', ', $fieldDetails) . " WHERE {$where}";
        $stmt = $db->prepare($sql);

        $this->bindParams($stmt, $sql, $data, PDO::PARAM_STR);
        $this->bindParams($stmt, $sql, $whereBindArray, PDO::PARAM_STR);

        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    public function delete(string $table, string $where, array $bind = [], int $limit = 1): bool
    {
        $db = self::getInstance();
        $sql = "DELETE FROM {$table} WHERE {$where} LIMIT {$limit}";
        $stmt = $db->prepare($sql);

        $this->bindParams($stmt, $sql, $bind, PDO::PARAM_STR);

        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    public function Fulldelete(string $table, string $where, array $bind = []): bool
    {
        $db = self::getInstance();
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $db->prepare($sql);

        $this->bindParams($stmt, $sql, $bind, PDO::PARAM_STR);

        $ok = $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }
}
?>
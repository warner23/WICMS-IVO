<?php
declare(strict_types=1);

/**
 * WICMS Utility Library
 * Core helper layer
 */

final class WILib
{
    private static ?self $instance = null;

    private WIdb $db;

    private function __construct()
    {
        $this->db = WIdb::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function db(): WIdb
    {
        return $this->db;
    }

    /**
     * Fetch a single column value from a query
     */
    public function selectColumn(string $sql, array $params, string $column): mixed
    {
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {

            $placeholder = ':' . ltrim((string)$key, ':');

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

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        if ($result && array_key_exists($column, $result)) {
            return $result[$column];
        }

        return null;
    }

    /**
     * Basic query helper
     */
    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {

            $placeholder = ':' . ltrim((string)$key, ':');

            $stmt->bindValue($placeholder, $value);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        return $result ?: [];
    }

}
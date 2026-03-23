<?php

declare(strict_types=1);

class WIKitchenCompliInstall
{
    protected mixed $db;

    public function __construct(mixed $db = null)
    {
        $this->db = $db ?? (class_exists('WIdb') && method_exists('WIdb', 'getInstance') ? WIdb::getInstance() : null);
    }

    public function install(): array
    {
        $sqlFile = dirname(__DIR__, 2) . '/WI_Compliance.sql';
        if (!is_file($sqlFile)) {
            return ['status' => 'error', 'message' => 'SQL file not found.'];
        }

        $sql = (string) file_get_contents($sqlFile);
        $executed = 0;

        if ($this->db && method_exists($this->db, 'prepare')) {
            $statements = array_filter(array_map('trim', explode(";\n", $sql)));
            foreach ($statements as $statement) {
                if ($statement === '') {
                    continue;
                }
                try {
                    $stmt = $this->db->prepare($statement);
                    if (method_exists($stmt, 'execute')) {
                        $stmt->execute();
                        $executed++;
                    }
                } catch (Throwable $e) {
                    // Keep going so partial environments still install as much as possible.
                }
            }
        }

        return [
            'status' => 'success',
            'message' => 'Kitchen Compliance install completed.',
            'statements_executed' => $executed,
        ];
    }
}

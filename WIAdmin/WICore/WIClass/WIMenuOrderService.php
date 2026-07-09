<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Menu Order Service
|--------------------------------------------------------------------------
| Core WICMS-only service for saving sortable menu order.
*/

final class WIMenuOrderService
{
    private WIdb $db;

    /** @var array<string, string> */
    private array $allowedTables = [
        'wi_menu'       => 'Site menu',
        'wi_admin_menu' => 'Admin menu',
        'wi_sidebar'    => 'Sidebar menu',
    ];

    public function __construct(?WIdb $db = null)
    {
        $this->db = $db ?: WIdb::getInstance();
    }

    /**
     * @param array<int|string, mixed> $order
     * @return array<string, mixed>
     */
    public function save(string $table, array $order): array
    {
        $table = trim($table);

        if (!isset($this->allowedTables[$table])) {
            return $this->error('Unsupported menu table.');
        }

        $ids = $this->normaliseIds($order);

        if ($ids === []) {
            return $this->error('No valid menu IDs were submitted.');
        }

        $this->db->begin();

        try {
            foreach ($ids as $sort => $id) {
                $this->db->update(
                    $table,
                    ['sort' => $sort],
                    '`id` = :id',
                    ['id' => $id]
                );
            }

            $this->db->commitTransaction();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollbackTransaction();
            }

            return $this->error('Menu order could not be saved.');
        }

        return [
            'status'  => 'success',
            'message' => $this->allowedTables[$table] . ' order saved.',
            'data'    => [
                'table' => $table,
                'count' => count($ids),
            ],
        ];
    }

    /**
     * @param array<int|string, mixed> $order
     * @return array<int, int>
     */
    private function normaliseIds(array $order): array
    {
        $ids = [];

        foreach ($order as $id) {
            if (is_array($id)) {
                continue;
            }

            $id = (int) $id;

            if ($id > 0 && !in_array($id, $ids, true)) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * @return array<string, string>
     */
    private function error(string $message): array
    {
        return [
            'status'  => 'error',
            'message' => $message,
        ];
    }
}

<?php
#[\AllowDynamicProperties]

class WIPage
{
    protected $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function getPages(): array
    {
        return $this->WIdb->bindfree(
            "SELECT `id`, `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`
             FROM `wi_page`
             ORDER BY `id` ASC"
        );
    }

    public function getPageById(int $id): ?array
    {
        $result = $this->WIdb->select(
            "SELECT `id`, `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`
             FROM `wi_page`
             WHERE `id` = :id
             LIMIT 1",
            ['id' => $id]
        );

        return count($result) > 0 ? $result[0] : null;
    }

    public function pageNameExists(string $name, int $excludeId = 0): bool
    {
        $sql = "SELECT `id`
                FROM `wi_page`
                WHERE LOWER(`name`) = :name";

        $params = ['name' => strtolower(trim($name))];

        if ($excludeId > 0) {
            $sql .= " AND `id` != :id";
            $params['id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $result = $this->WIdb->select($sql, $params);

        return count($result) > 0;
    }

    public function savePage(array $data): array
    {
        $id            = isset($data['id']) ? (int)$data['id'] : 0;
        $name          = trim((string)($data['name'] ?? ''));
        $panel         = isset($data['panel']) ? (string)$data['panel'] : '0';
        $topHead       = isset($data['top_head']) ? (string)$data['top_head'] : '0';
        $header        = isset($data['header']) ? (string)$data['header'] : '0';
        $leftSidebar   = isset($data['left_sidebar']) ? (string)$data['left_sidebar'] : '0';
        $rightSidebar  = isset($data['right_sidebar']) ? (string)$data['right_sidebar'] : '0';
        $contents      = trim((string)($data['contents'] ?? ''));
        $footer        = isset($data['footer']) ? (string)$data['footer'] : '0';

        $errors = [];

        if ($name === '') {
            $errors[] = ['id' => 'page-name', 'msg' => 'Page name is required.'];
        }

        if ($contents === '') {
            $errors[] = ['id' => 'page-contents', 'msg' => 'Contents module is required.'];
        }

        if ($this->pageNameExists($name, $id)) {
            $errors[] = ['id' => 'page-name', 'msg' => 'Page name already exists.'];
        }

        foreach (['panel' => $panel, 'top_head' => $topHead, 'header' => $header, 'left_sidebar' => $leftSidebar, 'right_sidebar' => $rightSidebar, 'footer' => $footer] as $field => $value) {
            if (!in_array($value, ['0', '1'], true)) {
                $errors[] = ['id' => 'page-' . str_replace('_', '-', $field), 'msg' => 'Invalid value supplied.'];
            }
        }

        if (!empty($errors)) {
            return [
                'status' => 'error',
                'errors' => $errors
            ];
        }

        $payload = [
            'name'          => $name,
            'panel'         => $panel,
            'top_head'      => $topHead,
            'header'        => $header,
            'left_sidebar'  => $leftSidebar,
            'right_sidebar' => $rightSidebar,
            'contents'      => $contents,
            'footer'        => $footer
        ];

        if ($id > 0) {
            $this->WIdb->update(
                'wi_page',
                $payload,
                '`id` = :id',
                ['id' => $id]
            );

            return [
                'status' => 'success',
                'msg'    => 'Page updated successfully.'
            ];
        }

        $this->WIdb->insert('wi_page', $payload);

        return [
            'status' => 'success',
            'msg'    => 'Page created successfully.'
        ];
    }

    public function deletePage(int $id): array
    {
        if ($id <= 0) {
            return [
                'status' => 'error',
                'msg'    => 'Invalid page id.'
            ];
        }

        $this->WIdb->delete(
            'wi_page',
            '`id` = :id',
            ['id' => $id]
        );

        return [
            'status' => 'success',
            'msg'    => 'Page deleted successfully.'
        ];
    }
}
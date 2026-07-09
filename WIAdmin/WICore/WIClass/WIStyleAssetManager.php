<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Style Asset Manager
|--------------------------------------------------------------------------
| Core WICMS service for Theme, CSS, JS and Meta records.
| Keeps admin UI logic out of WIAjax and keeps public loading data-driven.
*/
final class WIStyleAssetManager
{
    private WIdb $db;
    private string $csrfForm = 'wicms_style_assets';

    /** @var array<string,array<string,string>> */
    private array $types = [
        'css' => [
            'table' => 'wi_css',
            'id' => 'id',
            'path' => 'href',
            'page' => 'page',
            'label' => 'CSS',
        ],
        'js' => [
            'table' => 'wi_scripts',
            'id' => 'id',
            'path' => 'src',
            'page' => 'page',
            'label' => 'JS',
        ],
        'meta' => [
            'table' => 'wi_meta',
            'id' => 'meta_id',
            'name' => 'name',
            'content' => 'content',
            'author' => 'author',
            'page' => 'page',
            'label' => 'Meta',
        ],
        'theme' => [
            'table' => 'wi_theme',
            'id' => 'id',
            'label' => 'Theme',
        ],
    ];

    public function __construct(?WIdb $db = null)
    {
        $this->db = $db ?? WIdb::getInstance();
    }

    /**
     * @return array<string,mixed>
     */
    public function bootstrap(): array
    {
        return [
            'status' => 'success',
            'message' => 'Style assets ready.',
            'data' => [
                'pages' => $this->pages(),
                'csrf_token' => $this->freshToken(),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $request
     * @return array<string,mixed>
     */
    public function list(array $request): array
    {
        $type = $this->normaliseType((string) ($request['type'] ?? 'css'));
        $pageFilter = $this->normalisePageFilter((string) ($request['page_filter'] ?? 'all'));
        $search = trim((string) ($request['search'] ?? ''));
        $page = max(1, (int) ($request['page_no'] ?? $request['page'] ?? 1));
        $perPage = (int) ($request['per_page'] ?? 10);
        $perPage = in_array($perPage, [5, 10, 20, 50], true) ? $perPage : 10;

        if ($type === 'theme') {
            return $this->listThemes($search, $page, $perPage);
        }

        $map = $this->types[$type];
        $table = $map['table'];
        $idColumn = $map['id'];
        $where = [];
        $params = [];

        if ($pageFilter !== 'all') {
            $where[] = '`page` = :page_filter';
            $params['page_filter'] = $pageFilter;
        }

        if ($search !== '') {
            if ($type === 'meta') {
                $where[] = '(`name` LIKE :search OR `content` LIKE :search OR `author` LIKE :search OR `page` LIKE :search)';
            } else {
                $pathColumn = $map['path'];
                $where[] = '(`' . $pathColumn . '` LIKE :search OR `page` LIKE :search)';
            }
            $params['search'] = '%' . $search . '%';
        }

        $whereSql = $where === [] ? '1 = 1' : implode(' AND ', $where);
        $totalRows = $this->db->select('SELECT COUNT(*) AS count_value FROM `' . $table . '` WHERE ' . $whereSql, $params);
        $total = (int) ($totalRows[0]['count_value'] ?? 0);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT * FROM `' . $table . '` WHERE ' . $whereSql . ' ORDER BY `page` ASC, `' . $idColumn . '` ASC LIMIT ' . $perPage . ' OFFSET ' . $offset;
        $rows = $this->db->select($sql, $params);

        return [
            'status' => 'success',
            'message' => $this->types[$type]['label'] . ' records loaded.',
            'data' => [
                'type' => $type,
                'records' => array_map(fn(array $row): array => $this->normaliseRecord($type, $row), $rows),
                'pages' => $this->pages(),
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $totalPages,
                ],
                'csrf_token' => $this->freshToken(),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $request
     * @return array<string,mixed>
     */
    public function get(array $request): array
    {
        $type = $this->normaliseType((string) ($request['type'] ?? 'css'));
        $id = max(0, (int) ($request['id'] ?? 0));

        if ($id <= 0) {
            return $this->error('Record id is required.');
        }

        $map = $this->types[$type];
        $rows = $this->db->select(
            'SELECT * FROM `' . $map['table'] . '` WHERE `' . $map['id'] . '` = :id LIMIT 1',
            ['id' => $id]
        );

        if ($rows === []) {
            return $this->error('Record not found.');
        }

        return [
            'status' => 'success',
            'message' => 'Record loaded.',
            'data' => [
                'record' => $this->normaliseRecord($type, $rows[0]),
                'csrf_token' => $this->freshToken(),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $request
     * @return array<string,mixed>
     */
    public function save(array $request): array
    {
        $type = $this->normaliseType((string) ($request['type'] ?? 'css'));

        if ($type === 'theme') {
            return $this->saveTheme($request);
        }

        $id = max(0, (int) ($request['id'] ?? 0));
        $page = $this->normalisePage((string) ($request['asset_page'] ?? $request['page_name'] ?? $request['page'] ?? ''));

        if ($page === '') {
            return $this->error('Page is required.');
        }

        if ($type === 'css') {
            $href = $this->normaliseAssetPath((string) ($request['href'] ?? $request['path'] ?? ''));
            $rel = $this->normaliseRel((string) ($request['rel'] ?? 'stylesheet'));

            if ($href === '') {
                return $this->error('CSS href is required.');
            }

            $payload = ['href' => $href, 'rel' => $rel, 'page' => $page];
            $table = 'wi_css';
            $idColumn = 'id';
        } elseif ($type === 'js') {
            $src = $this->normaliseAssetPath((string) ($request['src'] ?? $request['path'] ?? ''));

            if ($src === '') {
                return $this->error('JS src is required.');
            }

            $payload = ['src' => $src, 'page' => $page];
            $table = 'wi_scripts';
            $idColumn = 'id';
        } else {
            $name = $this->normaliseMetaName((string) ($request['name'] ?? ''));
            $content = trim((string) ($request['content'] ?? ''));
            $author = trim((string) ($request['author'] ?? 'WICMS'));

            if ($name === '') {
                return $this->error('Meta name is required.');
            }

            if (mb_strlen($content) > 2000) {
                return $this->error('Meta content is too long.');
            }

            $payload = ['page' => $page, 'name' => $name, 'content' => $content, 'author' => $author];
            $table = 'wi_meta';
            $idColumn = 'meta_id';
        }

        if ($id > 0) {
            $this->db->update($table, $payload, '`' . $idColumn . '` = :id', ['id' => $id]);
            $message = $this->types[$type]['label'] . ' record updated.';
        } else {
            $this->db->insert($table, $payload);
            $id = (int) $this->db->lastInsertId();
            $message = $this->types[$type]['label'] . ' record created.';
        }

        return [
            'status' => 'success',
            'message' => $message,
            'data' => [
                'id' => $id,
                'csrf_token' => $this->freshToken(),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $request
     * @return array<string,mixed>
     */
    public function delete(array $request): array
    {
        $type = $this->normaliseType((string) ($request['type'] ?? 'css'));
        $id = max(0, (int) ($request['id'] ?? 0));

        if ($id <= 0) {
            return $this->error('Record id is required.');
        }

        if ($type === 'theme') {
            return $this->deleteTheme($id);
        }

        $map = $this->types[$type];
        $this->db->delete($map['table'], '`' . $map['id'] . '` = :id', ['id' => $id]);

        return [
            'status' => 'success',
            'message' => $map['label'] . ' record deleted.',
            'data' => [
                'csrf_token' => $this->freshToken(),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $request
     * @return array<string,mixed>
     */
    public function activateTheme(array $request): array
    {
        $id = max(0, (int) ($request['id'] ?? 0));

        if ($id <= 0) {
            return $this->error('Theme id is required.');
        }

        $rows = $this->db->select('SELECT `id` FROM `wi_theme` WHERE `id` = :id LIMIT 1', ['id' => $id]);
        if ($rows === []) {
            return $this->error('Theme not found.');
        }

        $this->db->update('wi_theme', ['in_use' => 0], '1 = 1', []);
        $this->db->update('wi_theme', ['in_use' => 1], '`id` = :id', ['id' => $id]);

        return [
            'status' => 'success',
            'message' => 'Theme activated.',
            'data' => ['csrf_token' => $this->freshToken()],
        ];
    }

    /**
     * @return array<int,array<string,string>>
     */
    public function pages(): array
    {
        $pages = ['global' => 'Global / all pages'];

        if ($this->db->tableExists('wi_page')) {
            $rows = $this->db->select('SELECT `name` FROM `wi_page` ORDER BY `name` ASC');
            foreach ($rows as $row) {
                $name = $this->normalisePage((string) ($row['name'] ?? ''));
                if ($name !== '') {
                    $pages[$name] = $name;
                }
            }
        }

        foreach (['wi_css' => 'page', 'wi_scripts' => 'page', 'wi_meta' => 'page'] as $table => $column) {
            if (!$this->db->tableExists($table)) {
                continue;
            }
            $rows = $this->db->select('SELECT DISTINCT `' . $column . '` AS page_name FROM `' . $table . '` ORDER BY `' . $column . '` ASC');
            foreach ($rows as $row) {
                $name = $this->normalisePage((string) ($row['page_name'] ?? ''));
                if ($name !== '') {
                    $pages[$name] = $name === 'global' ? 'Global / all pages' : $name;
                }
            }
        }

        return array_map(
            static fn(string $key, string $label): array => ['value' => $key, 'label' => $label],
            array_keys($pages),
            array_values($pages)
        );
    }

    /**
     * @return array<string,mixed>
     */
    private function listThemes(string $search, int $page, int $perPage): array
    {
        $where = '1 = 1';
        $params = [];

        if ($search !== '') {
            $where = '(`theme` LIKE :search OR `destination` LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $totalRows = $this->db->select('SELECT COUNT(*) AS count_value FROM `wi_theme` WHERE ' . $where, $params);
        $total = (int) ($totalRows[0]['count_value'] ?? 0);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $totalPages);
        $offset = ($page - 1) * $perPage;

        $rows = $this->db->select(
            'SELECT * FROM `wi_theme` WHERE ' . $where . ' ORDER BY `in_use` DESC, `theme` ASC LIMIT ' . $perPage . ' OFFSET ' . $offset,
            $params
        );

        return [
            'status' => 'success',
            'message' => 'Theme records loaded.',
            'data' => [
                'type' => 'theme',
                'records' => array_map(fn(array $row): array => $this->normaliseRecord('theme', $row), $rows),
                'pages' => $this->pages(),
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $totalPages,
                ],
                'csrf_token' => $this->freshToken(),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $request
     * @return array<string,mixed>
     */
    private function saveTheme(array $request): array
    {
        $id = max(0, (int) ($request['id'] ?? 0));
        $theme = $this->normaliseThemeName((string) ($request['theme'] ?? ''));
        $destination = trim((string) ($request['destination'] ?? ''));
        $inUse = $this->truthy($request['in_use'] ?? 0) ? 1 : 0;

        if ($theme === '') {
            return $this->error('Theme name is required.');
        }

        if ($destination === '') {
            $destination = 'WITheme/' . $theme . '/';
        }

        $destination = $this->normaliseDestination($destination);

        $existing = $this->db->select(
            'SELECT `id` FROM `wi_theme` WHERE LOWER(`theme`) = :theme AND `id` != :id LIMIT 1',
            ['theme' => strtolower($theme), 'id' => $id]
        );

        if ($existing !== []) {
            return $this->error('A theme with that name already exists.');
        }

        $payload = ['theme' => $theme, 'destination' => $destination, 'in_use' => $inUse];

        if ($inUse === 1) {
            $this->db->update('wi_theme', ['in_use' => 0], '1 = 1', []);
        }

        if ($id > 0) {
            $this->db->update('wi_theme', $payload, '`id` = :id', ['id' => $id]);
            $message = 'Theme updated.';
        } else {
            $this->db->insert('wi_theme', $payload);
            $id = (int) $this->db->lastInsertId();
            $message = 'Theme created.';
        }

        $this->ensureThemeFolder($destination);

        return [
            'status' => 'success',
            'message' => $message,
            'data' => ['id' => $id, 'csrf_token' => $this->freshToken()],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function deleteTheme(int $id): array
    {
        $rows = $this->db->select('SELECT * FROM `wi_theme` WHERE `id` = :id LIMIT 1', ['id' => $id]);
        if ($rows === []) {
            return $this->error('Theme not found.');
        }

        if ((int) ($rows[0]['in_use'] ?? 0) === 1) {
            return $this->error('Active theme cannot be deleted. Activate another theme first.');
        }

        $this->db->delete('wi_theme', '`id` = :id', ['id' => $id]);

        return [
            'status' => 'success',
            'message' => 'Theme record deleted. Theme files were not deleted automatically.',
            'data' => ['csrf_token' => $this->freshToken()],
        ];
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function normaliseRecord(string $type, array $row): array
    {
        if ($type === 'theme') {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'type' => 'theme',
                'theme' => (string) ($row['theme'] ?? ''),
                'destination' => (string) ($row['destination'] ?? ''),
                'in_use' => (int) ($row['in_use'] ?? 0),
            ];
        }

        if ($type === 'meta') {
            return [
                'id' => (int) ($row['meta_id'] ?? 0),
                'type' => 'meta',
                'page' => (string) ($row['page'] ?? ''),
                'name' => (string) ($row['name'] ?? ''),
                'content' => (string) ($row['content'] ?? ''),
                'author' => (string) ($row['author'] ?? ''),
            ];
        }

        if ($type === 'css') {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'type' => 'css',
                'page' => (string) ($row['page'] ?? ''),
                'href' => (string) ($row['href'] ?? ''),
                'rel' => (string) ($row['rel'] ?? 'stylesheet'),
            ];
        }

        return [
            'id' => (int) ($row['id'] ?? 0),
            'type' => 'js',
            'page' => (string) ($row['page'] ?? ''),
            'src' => (string) ($row['src'] ?? ''),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function error(string $message): array
    {
        return [
            'status' => 'error',
            'message' => $message,
            'data' => ['csrf_token' => $this->freshToken()],
        ];
    }

    private function normaliseType(string $type): string
    {
        $type = strtolower(trim($type));
        return array_key_exists($type, $this->types) ? $type : 'css';
    }

    private function normalisePageFilter(string $page): string
    {
        $page = strtolower(trim($page));
        if ($page === '' || $page === 'all') {
            return 'all';
        }
        return $this->normalisePage($page);
    }

    private function normalisePage(string $page): string
    {
        $page = trim($page);
        if ($page === '*' || strtolower($page) === 'all') {
            return 'global';
        }
        return preg_replace('/[^A-Za-z0-9_\-]/', '', $page) ?? '';
    }

    private function normaliseAssetPath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $path = preg_replace('#/+#', '/', $path) ?? '';
        $path = ltrim($path, '/');

        if ($path === '' || str_contains($path, '..') || preg_match('#^[a-z]+:#i', $path)) {
            return '';
        }

        return mb_substr($path, 0, 255);
    }

    private function normaliseRel(string $rel): string
    {
        $rel = strtolower(trim($rel));
        return in_array($rel, ['stylesheet', 'preload', 'prefetch'], true) ? $rel : 'stylesheet';
    }

    private function normaliseMetaName(string $name): string
    {
        $name = trim($name);
        return preg_match('/^[A-Za-z0-9_:\-\.]+$/', $name) ? mb_substr($name, 0, 255) : '';
    }

    private function normaliseThemeName(string $name): string
    {
        $name = preg_replace('/[^A-Za-z0-9_\-]/', '', trim($name)) ?? '';
        return mb_substr($name, 0, 80);
    }

    private function normaliseDestination(string $destination): string
    {
        $destination = str_replace('\\', '/', trim($destination));
        $destination = preg_replace('#/+#', '/', $destination) ?? '';
        $destination = ltrim($destination, '/');

        if ($destination === '' || str_contains($destination, '..')) {
            return 'WITheme/WICMS/';
        }

        if (!str_ends_with($destination, '/')) {
            $destination .= '/';
        }

        return mb_substr($destination, 0, 255);
    }

    private function ensureThemeFolder(string $destination): void
    {
        $path = dirname(__DIR__, 3) . '/' . $destination;

        if (!is_dir($path)) {
            @mkdir($path, 0775, true);
        }
    }

    private function truthy(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }
        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'on'], true);
    }

    private function freshToken(): string
    {
        return class_exists('WIToken') ? WIToken::getToken($this->csrfForm) : '';
    }
}

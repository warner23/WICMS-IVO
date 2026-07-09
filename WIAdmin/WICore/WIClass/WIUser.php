<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Class: WIUser
| File: WIUser.php
| Location: /WIAdmin/WICore/WIClass/WIUser.php
| Type: Core Admin User Management Service
| Layer: Service
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Production-ready core WICMS user management service.
|
| Notes:
| - WICMS core only; no Compliance ownership logic here.
| - Uses WIdb/PDO only.
| - Schema-aware for older `id`/`registered` installs and current
|   `user_id`/`register_date` installs.
| - Keeps legacy public methods used by older admin screens.
|--------------------------------------------------------------------------
*/

class WIUser
{
    private WIdb $WIdb;

    public function __construct(?WIdb $WIdb = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
    }

    /**
     * Return paged users for the modern manager.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function searchUsers(array $filters = []): array
    {
        if (!$this->WIdb->tableExists('wi_members')) {
            return [
                'items' => [],
                'total' => 0,
                'page' => 1,
                'per_page' => 10,
                'pages' => 1,
                'message' => 'Users table is not available.',
            ];
        }

        $idColumn = $this->memberIdColumn();
        $registeredColumn = $this->memberRegisteredColumn();
        $hasDetails = $this->WIdb->tableExists('wi_user_details');
        $hasRoles = $this->WIdb->tableExists('wi_user_roles');
        $hasBanned = $this->WIdb->columnExists('wi_members', 'banned');
        $hasConfirmed = $this->WIdb->columnExists('wi_members', 'confirmed');
        $hasLastLogin = $this->WIdb->columnExists('wi_members', 'last_login');

        $search = trim((string)($filters['search'] ?? ''));
        $roleId = max(0, (int)($filters['role_id'] ?? 0));
        $status = trim((string)($filters['status'] ?? 'all'));
        $page = max(1, (int)($filters['page'] ?? 1));
        $perPage = (int)($filters['per_page'] ?? 10);
        $allowedPerPage = [5, 10, 20, 50];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $where = ['1=1'];
        $params = [];

        if ($search !== '') {
            $where[] = '(m.`username` LIKE :search OR m.`email` LIKE :search'
                . ($hasDetails ? ' OR d.`first_name` LIKE :search OR d.`last_name` LIKE :search OR d.`phone` LIKE :search' : '')
                . ')';
            $params['search'] = '%' . $search . '%';
        }

        if ($roleId > 0 && $this->WIdb->columnExists('wi_members', 'user_role')) {
            $where[] = 'm.`user_role` = :role_id';
            $params['role_id'] = $roleId;
        }

        if ($status === 'banned' && $hasBanned) {
            $where[] = "m.`banned` = 'Y'";
        } elseif ($status === 'active' && $hasBanned) {
            $where[] = "m.`banned` = 'N'";
        } elseif ($status === 'confirmed' && $hasConfirmed) {
            $where[] = "m.`confirmed` = 'Y'";
        } elseif ($status === 'unconfirmed' && $hasConfirmed) {
            $where[] = "m.`confirmed` = 'N'";
        }

        $joinDetails = $hasDetails ? "LEFT JOIN `wi_user_details` d ON d.`user_id` = m.`{$idColumn}`" : '';
        $joinRoles = $hasRoles ? 'LEFT JOIN `wi_user_roles` r ON r.`role_id` = m.`user_role`' : '';
        $whereSql = implode(' AND ', $where);

        $totalRow = $this->WIdb->select(
            "SELECT COUNT(*) AS total_count
             FROM `wi_members` m
             {$joinDetails}
             {$joinRoles}
             WHERE {$whereSql}",
            $params
        );

        $total = (int)($totalRow[0]['total_count'] ?? 0);
        $pages = max(1, (int)ceil($total / $perPage));
        $page = min($page, $pages);
        $offset = ($page - 1) * $perPage;

        $selectDetails = $hasDetails
            ? "d.`first_name`, d.`last_name`, d.`phone`, d.`address`, d.`avatar`,"
            : "'' AS first_name, '' AS last_name, '' AS phone, '' AS address, '' AS avatar,";

        $selectRole = $hasRoles ? "r.`role` AS role_name" : "'' AS role_name";
        $selectBanned = $hasBanned ? "m.`banned`" : "'N' AS banned";
        $selectConfirmed = $hasConfirmed ? "m.`confirmed`" : "'Y' AS confirmed";
        $selectLastLogin = $hasLastLogin ? "m.`last_login`" : "NULL AS last_login";

        $items = $this->WIdb->select(
            "SELECT
                m.`{$idColumn}` AS user_id,
                m.`username`,
                m.`email`,
                m.`user_role` AS role_id,
                m.`{$registeredColumn}` AS registered_at,
                {$selectBanned},
                {$selectConfirmed},
                {$selectLastLogin},
                {$selectDetails}
                {$selectRole}
             FROM `wi_members` m
             {$joinDetails}
             {$joinRoles}
             WHERE {$whereSql}
             ORDER BY m.`{$idColumn}` DESC
             LIMIT :limit_value OFFSET :offset_value",
            array_merge($params, [
                'limit_value' => $perPage,
                'offset_value' => $offset,
            ])
        );

        return [
            'items' => array_map([$this, 'shapeUserRow'], $items),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => $pages,
        ];
    }

    /**
     * Legacy compatibility: return the first page of users.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getUsers(): array
    {
        $result = $this->searchUsers(['page' => 1, 'per_page' => 50]);
        return $result['items'] ?? [];
    }

    /**
     * Return one user by id.
     *
     * @return array<string, mixed>|null
     */
    public function getUserById(int $id): ?array
    {
        if ($id <= 0 || !$this->WIdb->tableExists('wi_members')) {
            return null;
        }

        $idColumn = $this->memberIdColumn();
        $registeredColumn = $this->memberRegisteredColumn();
        $hasDetails = $this->WIdb->tableExists('wi_user_details');
        $hasRoles = $this->WIdb->tableExists('wi_user_roles');
        $hasBanned = $this->WIdb->columnExists('wi_members', 'banned');
        $hasConfirmed = $this->WIdb->columnExists('wi_members', 'confirmed');
        $hasLastLogin = $this->WIdb->columnExists('wi_members', 'last_login');

        $selectDetails = $hasDetails
            ? "d.`first_name`, d.`last_name`, d.`phone`, d.`address`, d.`country`, d.`region`, d.`city`, d.`website`, d.`avatar`,"
            : "'' AS first_name, '' AS last_name, '' AS phone, '' AS address, '' AS country, '' AS region, '' AS city, '' AS website, '' AS avatar,";

        $selectRole = $hasRoles ? "r.`role` AS role_name" : "'' AS role_name";
        $selectBanned = $hasBanned ? "m.`banned`" : "'N' AS banned";
        $selectConfirmed = $hasConfirmed ? "m.`confirmed`" : "'Y' AS confirmed";
        $selectLastLogin = $hasLastLogin ? "m.`last_login`" : "NULL AS last_login";
        $joinDetails = $hasDetails ? "LEFT JOIN `wi_user_details` d ON d.`user_id` = m.`{$idColumn}`" : '';
        $joinRoles = $hasRoles ? 'LEFT JOIN `wi_user_roles` r ON r.`role_id` = m.`user_role`' : '';

        $rows = $this->WIdb->select(
            "SELECT
                m.`{$idColumn}` AS user_id,
                m.`username`,
                m.`email`,
                m.`password`,
                m.`user_role` AS role_id,
                m.`{$registeredColumn}` AS registered_at,
                {$selectBanned},
                {$selectConfirmed},
                {$selectLastLogin},
                {$selectDetails}
                {$selectRole}
             FROM `wi_members` m
             {$joinDetails}
             {$joinRoles}
             WHERE m.`{$idColumn}` = :user_id
             LIMIT 1",
            ['user_id' => $id]
        );

        if ($rows === []) {
            return null;
        }

        return $this->shapeUserRow($rows[0]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRoles(): array
    {
        if (!$this->WIdb->tableExists('wi_user_roles')) {
            return [];
        }

        return $this->WIdb->bindfree(
            "SELECT `role_id`, `role`
             FROM `wi_user_roles`
             ORDER BY `role` ASC"
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function saveUser(array $payload, int $currentAdminId = 0): array
    {
        if (!$this->WIdb->tableExists('wi_members')) {
            return $this->error('Users table is not available.');
        }

        $idColumn = $this->memberIdColumn();
        $userId = max(0, (int)($payload['user_id'] ?? 0));
        $email = strtolower(trim((string)($payload['email'] ?? '')));
        $username = trim((string)($payload['username'] ?? ''));
        $firstName = trim((string)($payload['first_name'] ?? ''));
        $lastName = trim((string)($payload['last_name'] ?? ''));
        $phone = trim((string)($payload['phone'] ?? ''));
        $address = trim((string)($payload['address'] ?? ''));
        $roleId = max(1, (int)($payload['role_id'] ?? 1));
        $confirmed = ((string)($payload['confirmed'] ?? 'Y')) === 'N' ? 'N' : 'Y';
        $banned = ((string)($payload['banned'] ?? 'N')) === 'Y' ? 'Y' : 'N';
        $password = (string)($payload['password'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('Enter a valid email address.');
        }

        if ($username === '') {
            $username = $email;
        }

        if (mb_strlen($username) > 250 || mb_strlen($email) > 254) {
            return $this->error('Username or email is too long.');
        }

        if (!$this->roleExists($roleId)) {
            return $this->error('Selected role does not exist.');
        }

        if ($userId === 0 && trim($password) === '') {
            return $this->error('Password is required for new users.');
        }

        if (trim($password) !== '' && strlen($password) < 8) {
            return $this->error('Password must be at least 8 characters.');
        }

        $duplicate = $this->findDuplicateMember($email, $username, $userId);
        if ($duplicate !== null) {
            return $this->error('A user with that email or username already exists.');
        }

        if ($userId > 0) {
            $existing = $this->getUserById($userId);
            if ($existing === null) {
                return $this->error('User not found.');
            }

            if ($userId === $currentAdminId && !$this->isAdminRole($roleId)) {
                return $this->error('You cannot remove your own admin access.');
            }

            if ($this->isAdminRole((int)$existing['role_id']) && !$this->isAdminRole($roleId) && $this->countAdminUsers($userId) <= 0) {
                return $this->error('You cannot remove the last admin user.');
            }

            $memberData = [
                'email' => $email,
                'username' => $username,
                'user_role' => $roleId,
            ];

            if ($this->WIdb->columnExists('wi_members', 'confirmed')) {
                $memberData['confirmed'] = $confirmed;
            }

            if ($this->WIdb->columnExists('wi_members', 'banned')) {
                $memberData['banned'] = $banned;
            }

            if (trim($password) !== '') {
                $memberData['password'] = $this->hashPassword($password);
            }

            $this->WIdb->update('wi_members', $memberData, "`{$idColumn}` = :user_id", ['user_id' => $userId]);
            $this->upsertUserDetails($userId, $firstName, $lastName, $phone, $address);

            $this->log('Updated WICMS user', ['user_id' => $userId], 'info');

            return $this->success('User updated successfully.', ['user_id' => $userId]);
        }

        $memberData = [
            'email' => $email,
            'username' => $username,
            'password' => $this->hashPassword($password),
            'confirmation_key' => '',
            'confirmed' => $confirmed,
            'password_reset_key' => '',
            'password_reset_confirmed' => 'N',
            'password_reset_timestamp' => date('Y-m-d H:i:s'),
            'register_date' => date('Y-m-d'),
            'user_role' => $roleId,
            'last_login' => date('Y-m-d H:i:s'),
            'ip_addr' => $_SERVER['REMOTE_ADDR'] ?? '',
            'banned' => $banned,
        ];

        $memberData = $this->filterMemberColumns($memberData);

        $this->WIdb->insert('wi_members', $memberData);
        $newUserId = (int)$this->WIdb->lastInsertId();
        $this->upsertUserDetails($newUserId, $firstName, $lastName, $phone, $address);

        $this->log('Created WICMS user', ['user_id' => $newUserId], 'info');

        return $this->success('User created successfully.', ['user_id' => $newUserId]);
    }

    /**
     * Legacy compatibility method.
     *
     * @return array<string, mixed>
     */
    public function updateUserRole(int $userId, int $roleId): array
    {
        return $this->changeRole($userId, $roleId, 0);
    }

    /**
     * @return array<string, mixed>
     */
    public function changeRole(int $userId, int $roleId, int $currentAdminId = 0): array
    {
        if ($userId <= 0 || !$this->roleExists($roleId)) {
            return $this->error('Invalid user or role.');
        }

        $user = $this->getUserById($userId);
        if ($user === null) {
            return $this->error('User not found.');
        }

        if ($userId === $currentAdminId && !$this->isAdminRole($roleId)) {
            return $this->error('You cannot remove your own admin access.');
        }

        if ($this->isAdminRole((int)$user['role_id']) && !$this->isAdminRole($roleId) && $this->countAdminUsers($userId) <= 0) {
            return $this->error('You cannot remove the last admin user.');
        }

        $idColumn = $this->memberIdColumn();
        $this->WIdb->update('wi_members', ['user_role' => $roleId], "`{$idColumn}` = :user_id", ['user_id' => $userId]);

        return $this->success('Role updated.', ['user_id' => $userId, 'role_id' => $roleId]);
    }

    /**
     * @return array<string, mixed>
     */
    public function setStatus(int $userId, string $field, string $value, int $currentAdminId = 0): array
    {
        $allowed = ['banned', 'confirmed'];
        if ($userId <= 0 || !in_array($field, $allowed, true)) {
            return $this->error('Invalid status request.');
        }

        if (!$this->WIdb->columnExists('wi_members', $field)) {
            return $this->error('Status field is not available on this install.');
        }

        $value = $value === 'Y' ? 'Y' : 'N';

        if ($field === 'banned' && $value === 'Y' && $userId === $currentAdminId) {
            return $this->error('You cannot ban your own account.');
        }

        $idColumn = $this->memberIdColumn();
        $this->WIdb->update('wi_members', [$field => $value], "`{$idColumn}` = :user_id", ['user_id' => $userId]);

        return $this->success('User status updated.', [
            'user_id' => $userId,
            'field' => $field,
            'value' => $value,
        ]);
    }

    /**
     * Legacy compatibility method.
     *
     * @return array<string, mixed>
     */
    public function deleteUser(int $id): array
    {
        return $this->deleteUserById($id, 0);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteUserById(int $userId, int $currentAdminId = 0): array
    {
        if ($userId <= 0) {
            return $this->error('Invalid user id.');
        }

        if ($userId === $currentAdminId) {
            return $this->error('You cannot delete your own account.');
        }

        $user = $this->getUserById($userId);
        if ($user === null) {
            return $this->error('User not found.');
        }

        if ($this->isAdminRole((int)$user['role_id']) && $this->countAdminUsers($userId) <= 0) {
            return $this->error('You cannot delete the last admin user.');
        }

        $idColumn = $this->memberIdColumn();

        $this->WIdb->begin();
        try {
            if ($this->WIdb->tableExists('wi_user_details')) {
                $this->WIdb->delete('wi_user_details', '`user_id` = :user_id', ['user_id' => $userId]);
            }

            if ($this->WIdb->tableExists('wi_social_logins')) {
                $this->WIdb->Fulldelete('wi_social_logins', '`user_id` = :user_id', ['user_id' => $userId]);
            }

            if ($this->WIdb->tableExists('wi_comments')) {
                $this->WIdb->Fulldelete('wi_comments', '`posted_by` = :user_id', ['user_id' => $userId]);
            }

            $this->WIdb->delete('wi_members', "`{$idColumn}` = :user_id", ['user_id' => $userId]);
            $this->WIdb->commitTransaction();
        } catch (Throwable $e) {
            if ($this->WIdb->inTransaction()) {
                $this->WIdb->rollbackTransaction();
            }
            return $this->error('Unable to delete user safely.');
        }

        $this->log('Deleted WICMS user', ['user_id' => $userId], 'warning');

        return $this->success('User deleted.', ['user_id' => $userId]);
    }

    public function memberIdColumn(): string
    {
        return $this->WIdb->columnExists('wi_members', 'user_id') ? 'user_id' : 'id';
    }

    public function memberRegisteredColumn(): string
    {
        if ($this->WIdb->columnExists('wi_members', 'register_date')) {
            return 'register_date';
        }

        return $this->WIdb->columnExists('wi_members', 'registered') ? 'registered' : $this->memberIdColumn();
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function shapeUserRow(array $row): array
    {
        $first = trim((string)($row['first_name'] ?? ''));
        $last = trim((string)($row['last_name'] ?? ''));
        $fullName = trim($first . ' ' . $last);

        return [
            'user_id' => (int)($row['user_id'] ?? 0),
            'id' => (int)($row['user_id'] ?? 0),
            'username' => (string)($row['username'] ?? ''),
            'email' => (string)($row['email'] ?? ''),
            'first_name' => $first,
            'last_name' => $last,
            'name' => $fullName !== '' ? $fullName : (string)($row['username'] ?? ''),
            'phone' => (string)($row['phone'] ?? ''),
            'address' => (string)($row['address'] ?? ''),
            'country' => (string)($row['country'] ?? ''),
            'region' => (string)($row['region'] ?? ''),
            'city' => (string)($row['city'] ?? ''),
            'website' => (string)($row['website'] ?? ''),
            'avatar' => (string)($row['avatar'] ?? ''),
            'role_id' => (int)($row['role_id'] ?? 0),
            'role' => (string)($row['role_name'] ?? ''),
            'role_name' => (string)($row['role_name'] ?? ''),
            'registered' => (string)($row['registered_at'] ?? ''),
            'registered_at' => (string)($row['registered_at'] ?? ''),
            'last_login' => (string)($row['last_login'] ?? ''),
            'banned' => (string)($row['banned'] ?? 'N'),
            'confirmed' => (string)($row['confirmed'] ?? 'Y'),
            'status_label' => ((string)($row['banned'] ?? 'N') === 'Y') ? 'Banned' : (((string)($row['confirmed'] ?? 'Y') === 'N') ? 'Unconfirmed' : 'Active'),
        ];
    }

    private function roleExists(int $roleId): bool
    {
        if ($roleId <= 0 || !$this->WIdb->tableExists('wi_user_roles')) {
            return $roleId > 0;
        }

        return $this->WIdb->exists('wi_user_roles', '`role_id` = :role_id', ['role_id' => $roleId]);
    }

    private function isAdminRole(int $roleId): bool
    {
        return $roleId > 70;
    }

    private function countAdminUsers(int $excludeUserId = 0): int
    {
        if (!$this->WIdb->tableExists('wi_members')) {
            return 0;
        }

        $idColumn = $this->memberIdColumn();
        $params = [];
        $where = '`user_role` > 70';

        if ($this->WIdb->columnExists('wi_members', 'banned')) {
            $where .= " AND `banned` = 'N'";
        }

        if ($excludeUserId > 0) {
            $where .= " AND `{$idColumn}` <> :exclude_user_id";
            $params['exclude_user_id'] = $excludeUserId;
        }

        $row = $this->WIdb->select("SELECT COUNT(*) AS admin_count FROM `wi_members` WHERE {$where}", $params);
        return (int)($row[0]['admin_count'] ?? 0);
    }

    private function findDuplicateMember(string $email, string $username, int $ignoreUserId): ?array
    {
        $idColumn = $this->memberIdColumn();
        $params = [
            'email' => $email,
            'username' => $username,
        ];
        $where = '(`email` = :email OR `username` = :username)';

        if ($ignoreUserId > 0) {
            $where .= " AND `{$idColumn}` <> :ignore_user_id";
            $params['ignore_user_id'] = $ignoreUserId;
        }

        $rows = $this->WIdb->select("SELECT `{$idColumn}` AS user_id FROM `wi_members` WHERE {$where} LIMIT 1", $params);
        return $rows[0] ?? null;
    }

    private function upsertUserDetails(int $userId, string $firstName, string $lastName, string $phone, string $address): void
    {
        if ($userId <= 0 || !$this->WIdb->tableExists('wi_user_details')) {
            return;
        }

        $data = [
            'first_name' => mb_substr($firstName, 0, 35),
            'last_name' => mb_substr($lastName, 0, 35),
            'phone' => mb_substr($phone, 0, 30),
            'address' => mb_substr($address, 0, 100),
        ];

        if ($this->WIdb->exists('wi_user_details', '`user_id` = :user_id', ['user_id' => $userId])) {
            $this->WIdb->update('wi_user_details', $data, '`user_id` = :user_id', ['user_id' => $userId]);
            return;
        }

        $data['user_id'] = $userId;
        $this->WIdb->insert('wi_user_details', $data);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function filterMemberColumns(array $data): array
    {
        $filtered = [];
        foreach ($data as $column => $value) {
            if ($this->WIdb->columnExists('wi_members', (string)$column)) {
                $filtered[$column] = $value;
            }
        }
        return $filtered;
    }

    private function hashPassword(string $password): string
    {
        if (class_exists('WIRegister')) {
            try {
                $register = new WIRegister();
                if (method_exists($register, 'hashPassword')) {
                    return $register->hashPassword($password);
                }
            } catch (Throwable $e) {
            }
        }

        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function log(string $message, array $data = [], string $level = 'info'): void
    {
        if (!class_exists('WILogger')) {
            return;
        }

        try {
            if ($level === 'warning' && method_exists('WILogger', 'warning')) {
                WILogger::warning($message, $data, 'users');
                return;
            }

            if (method_exists('WILogger', 'info')) {
                WILogger::info($message, $data, 'users');
            }
        } catch (Throwable $e) {
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function success(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function error(string $message): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
        ];
    }
}

<?php
declare(strict_types=1);

/**
 * User Class
 * Created by Warner Infinity
 * Author Jules Warner
 */
#[\AllowDynamicProperties]
class WIUser
{
    private ?int $userId = null;
    private WIdb $WIdb;

    public function __construct($userId = null)
    {
        $this->userId = ($userId !== null) ? (int)$userId : null;
        $this->WIdb = WIdb::getInstance();
    }

    public function getAll()
    {
        if ($this->userId === null) {
            return null;
        }

        $query = "SELECT `wi_members`.`email`, `wi_members`.`username`, `wi_members`.`last_login`, `wi_user_details`.*
                  FROM `wi_members`, `wi_user_details`
                  WHERE `wi_members`.`user_id` = :id
                  AND `wi_members`.`user_id` = `wi_user_details`.`user_id`";

        $result = $this->WIdb->select($query, ['id' => $this->userId]);

        return (count($result) > 0) ? $result[0] : null;
    }

    public static function getAdmin()
    {
        $role = new WIRole();
        $adminRoleId = $role->getId('admin');
        $WIdb = WIdb::getInstance();

        $result = $WIdb->select(
            'SELECT * FROM `wi_members` WHERE `user_role` = :role_id LIMIT 1',
            ['role_id' => $adminRoleId]
        );

        return (count($result) > 0) ? $result[0] : null;
    }

    public function add($postData): array
    {
        $result = [];
        $reg = new WIRegister();
        $errors = $reg->validateUser($postData, false);

        if (count($errors) > 0) {
            return [
                "status" => "error",
                "errors" => $errors
            ];
        }

        $data = $postData['userData'] ?? [];

        $this->WIdb->insert('wi_members', [
            'email'            => trim((string)($data['email'] ?? '')),
            'username'         => trim((string)($data['username'] ?? '')),
            'password'         => $reg->hashPassword((string)($data['password'] ?? '')),
            'confirmed'        => 'Y',
            'confirmation_key' => '',
            'register_date'    => date('Y-m-d H:i:s')
        ]);

        $id = (int)$this->WIdb->lastInsertId();

        $this->WIdb->insert('wi_user_details', [
            'user_id'    => $id,
            'first_name' => (string)($data['first_name'] ?? ''),
            'last_name'  => (string)($data['last_name'] ?? ''),
            'phone'      => (string)($data['phone'] ?? ''),
            'address'    => (string)($data['address'] ?? '')
        ]);

        return [
            "status" => "success",
            "msg"    => WILang::get("user_added_successfully")
        ];
    }

    public function updateUser($data): void
    {
        $errors = $this->_validateUserUpdate($data);

        if (count($errors) > 0) {
            echo json_encode([
                "status" => "error",
                "errors" => $errors
            ]);
            return;
        }

        $userData = $data['userData'] ?? [];
        $currInfo = $this->getInfo();

        if (!$currInfo) {
            echo json_encode([
                "status" => "error",
                "errors" => [["id" => "user", "msg" => WILang::get('user_dont_exist')]]
            ]);
            return;
        }

        $userInfo = [];

        if (($currInfo['email'] ?? '') !== ($userData['email'] ?? '')) {
            $userInfo['email'] = trim((string)$userData['email']);
        }

        if (($currInfo['username'] ?? '') !== ($userData['username'] ?? '')) {
            $userInfo['username'] = trim((string)$userData['username']);
        }

        if (!empty($userData['password'])) {
            $reg = new WIRegister();
            $userInfo['password'] = $reg->hashPassword((string)$userData['password']);
        }

        if (count($userInfo) > 0) {
            $this->updateInfo($userInfo);
        }

        $this->updateDetails([
            'first_name' => (string)($userData['first_name'] ?? ''),
            'last_name'  => (string)($userData['last_name'] ?? ''),
            'phone'      => (string)($userData['phone'] ?? ''),
            'address'    => (string)($userData['address'] ?? '')
        ]);

        echo json_encode([
            "status" => "success",
            "msg"    => WILang::get("user_updated_successfully")
        ]);
    }

    public function id($newId = null)
    {
        if ($newId !== null) {
            $this->userId = (int)$newId;
        }

        return $this->userId;
    }

    public function isAdmin(): bool
    {
        if ($this->userId === null) {
            return false;
        }

        $roleId = (int)$this->getRoleId();
        return $roleId > 4;
    }

    public function isStaff(): bool
    {
        if ($this->userId === null) {
            return false;
        }

        $role = (string)$this->getRole();

        return in_array($role, [
            "Administrator",
            "Developer",
            "Head Administrator",
            "Owner",
            "Manager",
            "FOH",
            "Kitchen",
            "Bar",
            "Cashier"
        ], true);
    }

    public function updatePassword($oldPass, $newPass): void
    {
        $info = $this->getInfo();

        if (!$info || empty($info['password'])) {
            echo WILang::get('wrong_old_password');
            return;
        }

        $reg = new WIRegister();

        if ($reg->verifyPassword((string)$oldPass, (string)$info['password'])) {
            $this->updateInfo([
                "password" => $reg->hashPassword((string)$newPass)
            ]);
            return;
        }

        echo WILang::get('wrong_old_password');
    }

    public function changeRole()
    {
        $role = $_POST['role'] ?? null;

        if ($role === null) {
            return null;
        }

        $result = $this->WIdb->select(
            "SELECT * FROM `wi_user_roles` WHERE `role_id` = :r LIMIT 1",
            ["r" => $role]
        );

        if (count($result) === 0) {
            return null;
        }

        $this->updateInfo(["user_role" => $role]);

        return $result[0]['role'] ?? null;
    }

    public function getRole()
    {
        if ($this->userId === null) {
            return null;
        }

        $result = $this->WIdb->select(
            "SELECT `wi_user_roles`.`role` AS role
             FROM `wi_user_roles`, `wi_members`
             WHERE `wi_members`.`user_role` = `wi_user_roles`.`role_id`
             AND `wi_members`.`user_id` = :id
             LIMIT 1",
            ["id" => $this->userId]
        );

        return $result[0]['role'] ?? null;
    }

    public function getRoleId()
    {
        if ($this->userId === null) {
            return null;
        }

        $result = $this->WIdb->select(
            "SELECT `wi_user_roles`.`role_id` AS role
             FROM `wi_user_roles`, `wi_members`
             WHERE `wi_members`.`user_role` = `wi_user_roles`.`role_id`
             AND `wi_members`.`user_id` = :id
             LIMIT 1",
            ["id" => $this->userId]
        );

        return $result[0]['role'] ?? null;
    }

    public function getInfo()
    {
        if ($this->userId === null) {
            return null;
        }

        $result = $this->WIdb->select(
            "SELECT * FROM `wi_members` WHERE `user_id` = :id LIMIT 1",
            ["id" => $this->userId]
        );

        return (count($result) > 0) ? $result[0] : null;
    }

    public function updateInfo($updateData): void
    {
        if ($this->userId === null) {
            return;
        }

        $this->WIdb->update(
            "wi_members",
            $updateData,
            "`user_id` = :id",
            ["id" => $this->userId]
        );
    }

    public function getDetails()
    {
        if ($this->userId === null) {
            return null;
        }

        $result = $this->WIdb->select(
            "SELECT * FROM `wi_user_details` WHERE `user_id` = :id LIMIT 1",
            ["id" => $this->userId]
        );

        if (count($result) === 0) {
            return [
                "first_name" => "",
                "last_name"  => "",
                "phone"      => "",
                "address"    => "",
                "empty"      => true
            ];
        }

        return $result[0];
    }

    public function updateDetails($details): void
    {
        if ($this->userId === null) {
            return;
        }

        $currDetails = $this->getDetails();

        if (isset($currDetails['empty'])) {
            $details["user_id"] = $this->userId;
            $this->WIdb->insert("wi_user_details", $details);
            return;
        }

        $this->WIdb->update(
            "wi_user_details",
            $details,
            "`user_id` = :id",
            ["id" => $this->userId]
        );
    }

    public function deleteUser(): void
    {
        if ($this->userId === null) {
            return;
        }

        $this->WIdb->delete("wi_members", "user_id = :id", ["id" => $this->userId]);
        $this->WIdb->delete("wi_user_details", "user_id = :id", ["id" => $this->userId]);
        $this->WIdb->delete("wi_comments", "posted_by = :id", ["id" => $this->userId]);
        $this->WIdb->delete("wi_social_logins", "user_id = :id", ["id" => $this->userId]);
    }

    private function _validateUserUpdate($data): array
    {
        $id        = $data['fieldId'] ?? [];
        $user      = $data['userData'] ?? [];
        $errors    = [];
        $validator = new WIValidator();
        $userInfo  = $this->getInfo();

        if ($userInfo === null) {
            $errors[] = [
                "id"  => $id['email'] ?? 'email',
                "msg" => WILang::get('user_dont_exist')
            ];
            return $errors;
        }

        if ($validator->isEmpty($user['email'] ?? '')) {
            $errors[] = ["id" => $id['email'] ?? 'email', "msg" => WILang::get('email_required')];
        }

        if ($validator->isEmpty($user['username'] ?? '')) {
            $errors[] = ["id" => $id['username'] ?? 'username', "msg" => WILang::get('username_required')];
        }

        if (!empty($user['password']) && (($user['password'] ?? '') !== ($user['confirm_password'] ?? ''))) {
            $errors[] = ["id" => $id['confirm_password'] ?? 'confirm_password', "msg" => WILang::get('passwords_dont_match')];
        }

        if (!$validator->emailValid($user['email'] ?? '')) {
            $errors[] = ["id" => $id['email'] ?? 'email', "msg" => WILang::get('email_wrong_format')];
        }

        if (($user['email'] ?? '') !== ($userInfo['email'] ?? '') && $validator->emailExist($user['email'] ?? '')) {
            $errors[] = ["id" => $id['email'] ?? 'email', "msg" => WILang::get('email_taken')];
        }

        if (($user['username'] ?? '') !== ($userInfo['username'] ?? '') && $validator->usernameExist($user['username'] ?? '')) {
            $errors[] = ["id" => $id['username'] ?? 'username', "msg" => WILang::get('username_taken')];
        }

        return $errors;
    }
}
?>
<?php
declare(strict_types=1);

/**
 * Contact Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

#[\AllowDynamicProperties]
class WIContact
{
    private $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    private function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate contact form
     */
    public function validate(array $data): array
    {
        $errors = [];

        $name = trim((string)($data['name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $message = trim((string)($data['message'] ?? ''));

        if ($name === '' || strlen($name) < 2) {
            $errors[] = "Invalid name";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email";
        }

        if ($message === '' || strlen($message) < 10) {
            $errors[] = "Message too short";
        }

        return $errors;
    }

    /**
     * Save contact message
     */
    public function save(array $data): bool
    {
        $name = trim((string)$data['name']);
        $email = trim((string)$data['email']);
        $message = trim((string)$data['message']);

        return $this->WIdb->insert(
            "wi_contact",
            [
                "name" => $name,
                "email" => $email,
                "message" => $message,
                "created" => date("Y-m-d H:i:s")
            ]
        );
    }

    /**
     * Send contact email
     */
    public function sendEmail(array $data): bool
    {
        $name = trim((string)$data['name']);
        $email = trim((string)$data['email']);
        $message = trim((string)$data['message']);

        $to = CONTACT_EMAIL;
        $subject = "Website Contact Form";

        $body =
        "Name: {$name}\n" .
        "Email: {$email}\n\n" .
        "Message:\n{$message}";

        $headers = "From: {$email}";

        return mail($to, $subject, $body, $headers);
    }

    /**
     * Process contact form
     */
    public function process(array $data): array
    {
        if (!WICsrf::checkPost()) {
            return [
                "status" => "error",
                "message" => "Invalid request"
            ];
        }

        $errors = $this->validate($data);

        if (!empty($errors)) {
            return [
                "status" => "error",
                "message" => implode(", ", $errors)
            ];
        }

        $this->save($data);

        $this->sendEmail($data);

        return [
            "status" => "success",
            "message" => "Message sent successfully"
        ];
    }

    /**
     * Get contact messages (admin)
     */
    public function getMessages(): array
    {
        $result = $this->WIdb->bindfree(
            "SELECT * FROM `wi_contact` ORDER BY `created` DESC"
        );

        return is_array($result) ? $result : [];
    }

    /**
     * Delete message
     */
    public function delete(int $id): bool
    {
        return $this->WIdb->delete(
            "wi_contact",
            "`id` = :id",
            [
                "id" => $id
            ]
        );
    }
}
?>
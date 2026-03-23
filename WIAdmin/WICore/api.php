<?php
declare(strict_types=1);

require_once __DIR__ . '/WI.php';

/*
|--------------------------------------------------------------------------
| CORS / Headers
|--------------------------------------------------------------------------
|
| Tighten this further later to your real frontend/app domains.
|
*/

$allowedOrigins = [
    'http://localhost',
    'http://127.0.0.1',
];

$origin = WIRequest::server('HTTP_ORIGIN', '');

if (is_string($origin) && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
}

header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Content-Type: application/json; charset=UTF-8');

if (WIRequest::method() === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/*
|--------------------------------------------------------------------------
| API Router
|--------------------------------------------------------------------------
*/

try {
    $method = WIRequest::method();
    $action = WIRequest::string('action', '', 'get');
    $db = WIdb::getInstance();

    if ($action === '') {
        WIResponse::error('No API action provided.', [], 400);
    }

    switch ($action) {
        case 'register':
            if ($method !== 'POST') {
                WIResponse::error('Method not allowed.', [], 405);
            }

            $payload = WIRequest::json();

            $username = trim((string) ($payload['username'] ?? ''));
            $email = trim((string) ($payload['email'] ?? ''));
            $password = (string) ($payload['password'] ?? '');

            if ($username === '' || $email === '' || $password === '') {
                WIResponse::validationError([
                    ['field' => 'username', 'msg' => 'Username is required'],
                    ['field' => 'email', 'msg' => 'Email is required'],
                    ['field' => 'password', 'msg' => 'Password is required'],
                ]);
            }

            $validator = new WIValidator();

            $errors = [];

            if (!$validator->emailValid($email)) {
                $errors[] = ['field' => 'email', 'msg' => 'Invalid email format'];
            }

            if ($validator->usernameExist($username)) {
                $errors[] = ['field' => 'username', 'msg' => 'Username already exists'];
            }

            if ($validator->emailExist($email)) {
                $errors[] = ['field' => 'email', 'msg' => 'Email already exists'];
            }

            if ($errors !== []) {
                WIResponse::validationError($errors);
            }

            $register = new WIRegister();

            $db->insert('wi_members', [
                'username' => $username,
                'email' => $email,
                'password' => $register->hashPassword($password),
                'confirmed' => 'Y',
                'register_date' => date('Y-m-d'),
                'ip_addr' => WIRequest::ip(),
            ]);

            $userId = (int) $db->lastInsertId();

            $db->insert('wi_user_details', [
                'user_id' => $userId,
            ]);

            WILogger::info('API user registered', [
                'user_id' => $userId,
                'username' => $username,
            ], 'api');

            WIResponse::success('Registration successful.', [
                'user_id' => $userId,
            ], 201);
            break;

        case 'login':
            if ($method !== 'POST') {
                WIResponse::error('Method not allowed.', [], 405);
            }

            $payload = WIRequest::json();

            $username = trim((string) ($payload['username'] ?? ''));
            $password = (string) ($payload['password'] ?? '');

            if ($username === '' || $password === '') {
                WIResponse::validationError([
                    ['field' => 'username', 'msg' => 'Username is required'],
                    ['field' => 'password', 'msg' => 'Password is required'],
                ]);
            }

            $rows = $db->select(
                'SELECT * FROM `wi_members` WHERE `username` = :username LIMIT 1',
                ['username' => $username]
            );

            if (count($rows) !== 1) {
                WILogger::warning('API login failed: unknown username', [
                    'username' => $username,
                    'ip' => WIRequest::ip(),
                ], 'api');

                WIResponse::unauthorized('Invalid username or password.');
            }

            $user = $rows[0];
            $storedPassword = (string) ($user['password'] ?? '');

            if ($storedPassword === '' || !password_verify($password, $storedPassword)) {
                WILogger::warning('API login failed: bad password', [
                    'username' => $username,
                    'ip' => WIRequest::ip(),
                ], 'api');

                WIResponse::unauthorized('Invalid username or password.');
            }

            if (($user['banned'] ?? 'N') === 'Y') {
                WILogger::security('Blocked banned user API login', [
                    'user_id' => $user['user_id'] ?? null,
                    'username' => $username,
                ], 'api');

                WIResponse::forbidden('User account is banned.');
            }

            $token = WIToken::generate(32);

            WILogger::info('API login successful', [
                'user_id' => $user['user_id'] ?? null,
                'username' => $username,
            ], 'api');

            WIResponse::success('Login successful.', [
                'token' => $token,
                'user' => [
                    'user_id' => (int) ($user['user_id'] ?? 0),
                    'username' => (string) ($user['username'] ?? ''),
                    'email' => (string) ($user['email'] ?? ''),
                ],
            ]);
            break;

        case 'me':
            WIResponse::success('API is working.', [
                'method' => $method,
                'time' => date('c'),
            ]);
            break;

        default:
            WIResponse::notFound('Unknown API action.');
    }
} catch (Throwable $e) {
    WILogger::error('API exception', [
        'message' => $e->getMessage(),
        'action' => WIRequest::string('action', '', 'get'),
        'ip' => WIRequest::ip(),
    ], 'api');

    if (WIConfig::getBool('APP_DEBUG', false)) {
        WIResponse::serverError('API error.', [
            'exception' => $e->getMessage(),
        ]);
    }

    WIResponse::serverError('An unexpected API error occurred.');
}
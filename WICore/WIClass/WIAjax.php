<?php
declare(strict_types=1);

require_once __DIR__ . '/WI.php';

header('Content-Type: application/json; charset=UTF-8');

/*
|--------------------------------------------------------------------------
| Security checks
|--------------------------------------------------------------------------
*/

if (
    empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
    exit;
}

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$url = parse_url($referer);

if (!isset($url['host']) || $url['host'] !== ($_SERVER['SERVER_NAME'] ?? '')) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid origin'
    ]);
    exit;
}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function postValue(string $key, $default = null)
{
    return $_POST[$key] ?? $default;
}

function postString(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;

    if (is_array($value)) {
        return $default;
    }

    return trim((string) $value);
}

function postInt(string $key, int $default = 0): int
{
    $value = $_POST[$key] ?? $default;
    return is_numeric($value) ? (int) $value : $default;
}

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function jsonError(string $message, int $statusCode = 400): void
{
    jsonResponse([
        'status' => 'error',
        'message' => $message
    ], $statusCode);
}

function jsonSuccess(array $data = []): void
{
    jsonResponse(array_merge([
        'status' => 'success'
    ], $data));
}

function onlyAdmin(): void
{
    $login = new WILogin();

    if (!$login->isLoggedIn()) {
        jsonError('Unauthorized', 401);
    }

    $loggedUser = new WIUser((int) WISession::get('user_id', 0));

    if (!$loggedUser->isAdmin()) {
        jsonError('Forbidden', 403);
    }
}

/*
|--------------------------------------------------------------------------
| Action router
|--------------------------------------------------------------------------
*/

$action = postString('action', '');

switch ($action) {
    case 'checkLogin':
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $logged = $login->userLogin((string) $username, (string) $password);

    if ($logged === true) {
        $redirectPage = 'index.php';

        if (function_exists('get_redirect_page')) {
            $redirectPage = (string) get_redirect_page();
        } elseif (WISession::get('user_id') !== null) {
            $user = new WIUser((int) WISession::get('user_id'));

            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                $redirectPage = 'WIAdmin/dashboard.php';
            }
        }

        echo json_encode([
            'status' => 'success',
            'page'   => $redirectPage
        ]);
    }
    break;

    case 'registerUser':
        $register->register((array) postValue('User', []));
        exit;

    case 'resetPassword':
        $register->resetPassword(
            postString('newPass'),
            postString('key')
        );
        jsonSuccess([
            'message' => 'Password reset complete'
        ]);
        break;

    case 'forgotPassword':
        $result = $register->forgotPassword(postString('email'));

        if ($result !== true) {
            jsonError((string) $result);
        }

        jsonSuccess([
            'message' => 'Password reset request sent'
        ]);
        break;

    case 'postComment':
        $WIComment = new WIComment();

        jsonResponse([
            'status' => 'success',
            'html' => $WIComment->insertComment(
                WISession::get('user_id'),
                postString('comment')
            )
        ]);
        break;

    case 'updatePassword':
        $user = new WIUser((int) WISession::get('user_id', 0));
        $user->updatePassword(postString('oldpass'), postString('newpass'));
        jsonSuccess();
        break;

    case 'updateDetails':
        $user = new WIUser((int) WISession::get('user_id', 0));
        $user->updateDetails((array) postValue('details', []));
        jsonSuccess();
        break;

    case 'changeRole':
        onlyAdmin();
        $user = new WIUser(postInt('userId'));
        jsonSuccess([
            'role' => ucfirst((string) $user->changeRole())
        ]);
        break;

    case 'deleteUser':
        onlyAdmin();
        $user = new WIUser(postInt('userId'));
        $user->deleteUser();
        jsonSuccess();
        break;

    case 'getUserDetails':
        onlyAdmin();
        $user = new WIUser(postInt('userId'));
        jsonResponse($user->getAll());
        break;

    case 'addRole':
        onlyAdmin();
        $role = new WIRole();
        jsonResponse($role->add(postString('role')));
        break;

    case 'deleteRole':
        onlyAdmin();
        $role = new WIRole();
        $role->delete(postInt('roleId'));
        jsonSuccess();
        break;

    case 'addUser':
        onlyAdmin();
        $user = new WIUser(null);
        jsonResponse($user->add($_POST));
        break;

    case 'updateUser':
        onlyAdmin();
        $user = new WIUser(postInt('userId'));
        $user->updateUser($_POST);
        jsonSuccess();
        break;

    case 'banUser':
        onlyAdmin();
        $user = new WIUser(postInt('userId'));
        $user->updateInfo(['banned' => 'Y']);
        jsonSuccess();
        break;

    case 'unbanUser':
        onlyAdmin();
        $user = new WIUser(postInt('userId'));
        $user->updateInfo(['banned' => 'N']);
        jsonSuccess();
        break;

    case 'getUser':
        onlyAdmin();
        $user = new WIUser(postInt('userId'));
        jsonResponse($user->getAll());
        break;

    /*
    |--------------------------------------------------------------------------
    | Calendar / bookings
    |--------------------------------------------------------------------------
    */

    case 'dayPicker':
        $ma = new WIMartialArts();
        $ma->dayPicker(postInt('id'));
        exit;

    case 'getDayCalendar':
        $calendar = new WICalendar();
        $calendar->getDayCalendar();
        exit;

    case 'getWeekCalendar':
        $calendar = new WICalendar();
        $calendar->getWeekCalendar();
        exit;

    case 'getCalendar':
        $calendar = new WICalendar();
        $calendar->getCalendar(postInt('year'), postInt('month'));
        exit;

    case 'addEvent':
        $calendar = new WICalendar();
        $calendar->addEvent(postString('date'));
        exit;

    case 'addEventBtn':
        $calendar = new WICalendar();
        $calendar->addEventBtn((array) postValue('appointment', []));
        exit;

    case 'getEvents':
        $calendar = new WICalendar();
        $calendar->getEvents(postString('date'));
        exit;

    case 'getEventTypes':
        $calendar = new WICalendar();
        $calendar->getEventTypes();
        exit;

    case 'showPaymentExecute':
        $calendar = new WICalendar();
        $calendar->showPaymentExecute(
            postValue('response'),
            postValue('ord')
        );
        exit;

    case 'showPaymentGet':
        $calendar = new WICalendar();
        $calendar->showPaymentGet(postValue('response'));
        exit;

    case 'createOrder':
        $calendar = new WICalendar();
        $calendar->createOrder(
            postValue('item_amt'),
            postValue('item_qty'),
            postValue('item_title'),
            postValue('total_amt'),
            postValue('duration'),
            postValue('type'),
            postValue('place'),
            postValue('name'),
            postValue('selectedtime'),
            postValue('contact_no'),
            postValue('notes'),
            postValue('eventDate')
        );
        exit;

    case 'appointmentFinish':
        $calendar = new WICalendar();
        $calendar->appointmentfinish(
            postString('email'),
            postValue('docReceipt')
        );
        exit;

    case 'timeslots':
        $calendar = new WICalendar();
        $calendar->timeSlots(postString('date'), postString('type'));
        exit;

    case 'training':
        $calendar = new WICalendar();
        $calendar->trainingType(postString('date'), postValue('class'));
        exit;

    case 'details':
        $calendar = new WICalendar();
        $calendar->completeDetails(postString('date'), postString('type'));
        exit;

    case 'paypalpayment':
        $calendar = new WICalendar();
        $calendar->paypalPayment(
            postString('date'),
            postString('type'),
            postValue('duration'),
            postValue('startTime'),
            postValue('placement'),
            postValue('contact_no'),
            postValue('name'),
            postValue('notes')
        );
        exit;

    /*
    |--------------------------------------------------------------------------
    | Contact / misc
    |--------------------------------------------------------------------------
    */

    case 'send':
        $contact = new WIContact();
        $contact->Contact((array) postValue('info', []));
        exit;

    case 'nextSlider':
        $pagin = new WIPagination();
        $pagin->SlideNextPagination(
            postValue('ele'),
            postValue('pagin'),
            postValue('clas'),
            postValue('item_per_page'),
            postValue('current_page'),
            postValue('total_records'),
            postValue('total_pages')
        );
        exit;

    default:
        jsonError('Unknown action', 404);
}
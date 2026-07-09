<?php
declare(strict_types=1);

/** WIMembers AJAX controller with stale-CSRF repair. */
final class WIAjax
{
    private array $input;
    private int $userId;

    public function __construct()
    {
        $this->input = $_POST + $_GET;
        $this->userId = (int) WISession::get('user_id', 0);
    }

    public function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $login = new WILogin();
            if (!$login->isLoggedIn()) {
                $this->respond(['success' => false, 'message' => 'Not logged in.', 'csrf_token' => WICsrf::getToken()], 401);
            }

            $action = (string)($this->input['action'] ?? 'member_profile_payload');
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !WICsrf::validateToken((string)($this->input['csrf_token'] ?? ''))) {
                $this->respond(['success' => false, 'message' => 'Security token refreshed. Please try again.', 'csrf_token' => WICsrf::generateToken()], 403);
            }

            $result = match ($action) {
                'member_profile_payload' => (new WIProfile($this->userId))->dashboardPayload(),
                'member_profile_save' => (new WIProfile($this->userId))->updateProfile($this->input),
                'member_profile_photo_upload' => $this->profilePhotoUpload(),
                'member_profile_photo_select' => $this->profilePhotoSelect(),
                'member_profile_photo_remove' => $this->profilePhotoRemove(),
                'member_profile_photo_library' => $this->profilePhotoLibrary(),
                'member_training_list' => ['success' => true, 'training' => (new WITraining())->assignedToUser($this->userId)],
                'member_training_start' => (new WITraining())->startAssignment($this->userId, (int)($this->input['assignment_id'] ?? 0)),
                'member_training_complete' => (new WITraining())->completeAssignment($this->userId, (int)($this->input['assignment_id'] ?? 0), $this->input),
                'member_forms_list' => ['success' => true, 'forms' => (new WIForms())->availableForUser($this->userId), 'documents' => (new WIForms())->documentAcknowledgementsForUser($this->userId)],
                'member_form_submit' => (new WIForms())->submit($this->userId, $this->input),
                'member_document_acknowledge' => (new WIForms())->acknowledgeDocument($this->userId, (int)($this->input['document_id'] ?? 0), trim((string)($this->input['notes'] ?? ''))),
                'member_profile_tasks' => $this->profileTasks(),
                'member_profile_task_complete' => $this->completeProfileTask(),
                'member_profile_task_attach_evidence' => $this->attachProfileTaskEvidence(),
                'member_account_save' => (new WIAccount($this->userId))->updateAccount($this->input),
                'member_password_save' => (new WIAccount($this->userId))->updatePassword($this->input),
                'member_settings_save' => (new WISettings())->saveForUser($this->userId, $this->input),
                'member_delete_request' => $this->deleteRequest(),
                default => ['success' => false, 'message' => 'Unknown action.'],
            };

            if (is_array($result) && !isset($result['csrf_token'])) {
                $result['csrf_token'] = WICsrf::getToken();
            }
            $this->respond($result);
        } catch (Throwable $e) {
            $this->respond(['success' => false, 'message' => $e->getMessage(), 'csrf_token' => WICsrf::getToken()], 500);
        }
    }


    private function profilePhotoUpload(): array
    {
        $file = $_FILES['profile_photo'] ?? $_FILES['file'] ?? $_FILES['media'] ?? null;
        if (!is_array($file)) {
            return ['success' => false, 'message' => 'Choose an image to upload.'];
        }

        $media = $this->wimedia();
        $result = $media->upload($file, $this->profilePhotoMediaContext('Uploaded profile picture'));

        if (($result['success'] ?? false) !== true) {
            return $result;
        }

        $mediaId = (int)($result['media_id'] ?? ($result['media']['id'] ?? 0));
        if ($mediaId <= 0) {
            return ['success' => false, 'message' => 'WIMedia upload completed but no media ID was returned.'];
        }

        return $this->applyProfilePhotoMedia($mediaId, 'Profile picture uploaded.');
    }

    private function profilePhotoSelect(): array
    {
        $mediaId = (int)($this->input['media_id'] ?? 0);
        if ($mediaId <= 0) {
            return ['success' => false, 'message' => 'Choose a WIMedia image first.'];
        }

        return $this->applyProfilePhotoMedia($mediaId, 'Profile picture selected from WIMedia.');
    }

    private function profilePhotoRemove(): array
    {
        $result = (new WIUser($this->userId))->removeAvatar();
        $result['csrf_token'] = WICsrf::getToken();
        return $result;
    }

    private function profilePhotoLibrary(): array
    {
        $search = trim((string)($this->input['search'] ?? ''));
        $items = $this->wimedia()->getMediaList([
            'media_type' => 'image',
            'status' => 1,
            'search' => $search !== '' ? $search : null,
            'limit' => 24,
        ]);

        $context = $this->profilePhotoMediaContext('Profile picture library');
        foreach ($items as &$item) {
            $mediaId = (int)($item['id'] ?? 0);
            $safeUrl = $mediaId > 0 ? $this->wimedia()->viewUrl($mediaId, $context) : (string)($item['file_url'] ?? '');
            $item['safe_view_url'] = $this->normaliseProfilePhotoMediaUrl($safeUrl !== '' ? $safeUrl : (string)($item['file_path'] ?? ''));
            $item['file_url'] = $this->normaliseProfilePhotoMediaUrl((string)($item['file_url'] ?? ''));
            $item['file_path_url'] = $this->normaliseProfilePhotoMediaUrl((string)($item['file_path'] ?? ''));
        }
        unset($item);

        return [
            'success' => true,
            'message' => 'WIMedia image library loaded.',
            'items' => $items,
            'count' => count($items),
        ];
    }

    private function applyProfilePhotoMedia(int $mediaId, string $message): array
    {
        $media = $this->wimedia();
        $item = $media->getMediaById($mediaId);
        if ($item === false || $item === []) {
            return ['success' => false, 'message' => 'WIMedia image could not be found.'];
        }

        if ((string)($item['media_type'] ?? '') !== 'image' && !str_starts_with((string)($item['mime_type'] ?? ''), 'image/')) {
            return ['success' => false, 'message' => 'Profile pictures must be image files.'];
        }

        $media->linkMedia($mediaId, $this->profilePhotoMediaContext('Active profile picture'));
        $result = (new WIUser($this->userId))->setAvatarFromMedia($mediaId);
        $result['message'] = $message;
        $result['csrf_token'] = WICsrf::getToken();
        return $result;
    }

    private function profilePhotoMediaContext(string $title): array
    {
        return [
            'system_code' => 'wicms',
            'entity_type' => 'member_profile',
            'entity_id' => $this->userId,
            'link_type' => 'profile_picture',
            'folder' => 'Members/ProfilePictures',
            'title' => $title,
            'alt_text' => 'Member profile picture',
            'caption' => 'Member profile picture',
            'visibility' => 'private',
            'access_scope' => 'user',
            'is_private' => 1,
            'is_sensitive' => 0,
            'user_id' => $this->userId,
            'uploaded_by_user_id' => $this->userId,
            'created_by_user_id' => $this->userId,
        ];
    }

    private function normaliseProfilePhotoMediaUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '' || str_starts_with($url, 'data:image/')) {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (is_string($path) && $path !== '' && str_contains($path, '/WIAdmin/WIMedia/')) {
            $relative = substr($path, strpos($path, '/WIAdmin/WIMedia/') + 1);
            return $this->memberRootUrl($relative);
        }

        if (str_starts_with($url, '/WIAdmin/WIMedia/')) {
            return $this->memberRootUrl(ltrim($url, '/'));
        }

        if (str_starts_with($url, 'WIAdmin/WIMedia/')) {
            return $this->memberRootUrl($url);
        }

        if (preg_match('/^(https?:)?\/\//i', $url)) {
            return $url;
        }

        return $this->memberRootUrl($url);
    }

    private function memberRootUrl(string $path): string
    {
        $script = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
        $path = ltrim(trim($path), '/');
        return str_contains($script, '/WIMembers/') ? '../' . $path : $path;
    }

    private function wimedia(): WIMedia
    {
        $root = dirname(__DIR__, 3);
        $path = $root . '/WIAdmin/WICore/WIClass/WIMedia.php';
        if (is_file($path) && !class_exists('WIMedia', false)) {
            require_once $path;
        }
        if (!class_exists('WIMedia', false)) {
            throw new RuntimeException('WIMedia is not available.');
        }
        return new WIMedia(WIdb::getInstance());
    }

    private function profileTasks(): array
    {
        return $this->profileTaskBridge()->dashboard([
            'status' => (string)($this->input['status'] ?? 'all'),
            'priority' => (string)($this->input['priority'] ?? 'all'),
            'org_business_id' => (int)($this->input['org_business_id'] ?? 0),
            'org_site_id' => (int)($this->input['org_site_id'] ?? 0),
        ]);
    }

    private function completeProfileTask(): array
    {
        return $this->profileTaskBridge()->completeTask((int)($this->input['task_id'] ?? 0));
    }

    private function attachProfileTaskEvidence(): array
    {
        return $this->profileTaskBridge()->attachEvidence((int)($this->input['task_id'] ?? 0), (int)($this->input['media_id'] ?? 0), trim((string)($this->input['evidence_type'] ?? 'photo')) ?: 'photo', trim((string)($this->input['caption'] ?? '')));
    }

    private function profileTaskBridge(): WIProfileTaskEngineBridge
    {
        $path = __DIR__ . '/WIProfileTaskEngineBridge.php';
        if (is_file($path) && !class_exists('WIProfileTaskEngineBridge', false)) { require_once $path; }
        if (!class_exists('WIProfileTaskEngineBridge', false)) { throw new RuntimeException('WIProfileTaskEngineBridge is not installed.'); }
        return new WIProfileTaskEngineBridge($this->userId);
    }

    private function deleteRequest(): array
    {
        $confirm = trim((string)($this->input['confirm_text'] ?? ''));
        if (mb_strtolower($confirm) !== 'delete') { return ['success' => false, 'message' => 'Type DELETE to request profile deletion.']; }
        $db = WIdb::getInstance();
        if ($db->tableExists('wi_member_delete_requests')) {
            $db->insert('wi_member_delete_requests', ['user_id'=>$this->userId, 'request_status'=>'requested', 'reason'=>trim((string)($this->input['reason'] ?? '')), 'requested_at'=>date('Y-m-d H:i:s')]);
        }
        (new WIUser($this->userId))->disableAccount();
        (new WILogin())->logout();
        return ['success' => true, 'message' => 'Profile deletion request recorded and account disabled.'];
    }

    private function respond(array $payload, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($payload, JSON_THROW_ON_ERROR);
        exit;
    }
}

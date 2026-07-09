<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WIMembers / WIProfile
| Project: WI Ecosystem
| File: WIBusiness.php
| Location: /WIMembers/WICore/WIClass/WIBusiness.php
| Type: Class
| Layer: Business Context Bridge
| Purpose Area: Business/site context for member workspace
| Version: 1.1.0
| Created: 2026-05-24
| Last Updated: 2026-06-08
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Resolves business and site access from WIHR first, with compliance mirror
| tables used only as fallback. This prevents WIMembers from depending on empty
| legacy member-site assignment tables.
*/

final class WIBusiness
{
    private WIdb $WIdb;
    private WIHr $hr;

    public function __construct(?WIdb $WIdb = null, ?WIHr $hr = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
        $this->hr = $hr ?? new WIHr($this->WIdb);
    }

    public function forUser(?int $userId): array
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return [];
        }

        $fromHr = $this->businessesFromHr($userId);
        if ($fromHr !== []) {
            return $fromHr;
        }

        return $this->businessesFromComplianceMirror($userId);
    }

    public function sitesForUser(?int $userId): array
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return [];
        }

        $fromHr = $this->hr->siteAssignmentsForUser($userId);
        if ($fromHr !== []) {
            return array_map(static function (array $site): array {
                $site['can_complete_checklists'] = 1;
                $site['can_review_checklists'] = 0;
                return $site;
            }, $fromHr);
        }

        return $this->sitesFromComplianceMirror($userId);
    }

    public function roleFlags(?int $userId): array
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return [
                'has_business_context' => false,
                'has_compliance_assignment' => false,
                'can_open_compliance_workspace' => false,
                'is_worker' => false,
                'is_manager' => false,
                'site_count' => 0,
                'role_count' => 0,
                'compliance_site_count' => 0,
            ];
        }

        $sites = $this->sitesForUser($userId);
        $roles = $this->hr->roleAssignmentsForUser($userId);
        $canReview = false;
        $canComplete = $sites !== [];

        foreach ($sites as $site) {
            $canReview = $canReview || ((int) ($site['can_review_checklists'] ?? 0) === 1);
            $canComplete = $canComplete || ((int) ($site['can_complete_checklists'] ?? 0) === 1);
        }

        foreach ($roles as $role) {
            $roleName = mb_strtolower((string) ($role['role_name'] ?? ''));
            if (str_contains($roleName, 'manager') || str_contains($roleName, 'owner') || str_contains($roleName, 'admin')) {
                $canReview = true;
            }
        }

        $complianceSiteCount = $this->complianceSiteCount($userId);
        $hasBusinessContext = $sites !== [] || $complianceSiteCount > 0;
        $hasComplianceAssignment = $complianceSiteCount > 0 || $canComplete || $canReview;

        return [
            'has_business_context' => $hasBusinessContext,
            'has_compliance_assignment' => $hasComplianceAssignment,
            'can_open_compliance_workspace' => $hasBusinessContext && $hasComplianceAssignment,
            'is_worker' => $canComplete,
            'is_manager' => $canReview,
            'site_count' => count($sites),
            'role_count' => count($roles),
            'compliance_site_count' => $complianceSiteCount,
        ];
    }

    public function contextForUser(?int $userId): array
    {
        $userId = (int) $userId;
        $businesses = $this->forUser($userId);
        $sites = $this->sitesForUser($userId);
        $primarySite = [];

        foreach ($sites as $site) {
            if ((int) ($site['is_primary'] ?? 0) === 1) {
                $primarySite = $site;
                break;
            }
        }

        if ($primarySite === [] && $sites !== []) {
            $primarySite = $sites[0];
        }

        return [
            'businesses' => $businesses,
            'sites' => $sites,
            'primary_site' => $primarySite,
            'flags' => $this->roleFlags($userId),
        ];
    }

    private function businessesFromHr(int $userId): array
    {
        if (!$this->hasTables(['wi_hr_employees', 'wi_org_businesses'])) {
            return [];
        }

        return $this->WIdb->select(
            'SELECT DISTINCT b.id,
                    b.business_ref,
                    b.display_name,
                    b.status
               FROM `wi_hr_employees` e
         INNER JOIN `wi_org_businesses` b ON b.id = e.org_business_id
              WHERE e.user_id = :user_id
                AND e.data_status <> "erased"
           ORDER BY b.display_name ASC',
            ['user_id' => $userId]
        );
    }

    private function businessesFromComplianceMirror(int $userId): array
    {
        if (!$this->hasTables(['wi_compliance_user_sites', 'wi_org_sites', 'wi_org_businesses'])) {
            return [];
        }

        return $this->WIdb->select(
            'SELECT DISTINCT b.id,
                    b.business_ref,
                    b.display_name,
                    b.status
               FROM `wi_compliance_user_sites` a
         INNER JOIN `wi_org_sites` s ON s.id = a.org_site_id
         INNER JOIN `wi_org_businesses` b ON b.id = s.org_business_id
              WHERE a.user_id = :user_id
                AND a.is_active = 1
           ORDER BY b.display_name ASC',
            ['user_id' => $userId]
        );
    }

    private function sitesFromComplianceMirror(int $userId): array
    {
        if (!$this->hasTables(['wi_compliance_user_sites', 'wi_org_sites'])) {
            return [];
        }

        return $this->WIdb->select(
            'SELECT s.id AS org_site_id,
                    s.org_business_id,
                    s.site_name,
                    s.site_type,
                    s.city_name,
                    s.region_name,
                    s.country_code,
                    s.status AS site_status,
                    a.role_id,
                    a.is_primary,
                    a.is_active AS can_complete_checklists,
                    0 AS can_review_checklists
               FROM `wi_compliance_user_sites` a
         INNER JOIN `wi_org_sites` s ON s.id = a.org_site_id
              WHERE a.user_id = :user_id
                AND a.is_active = 1
           ORDER BY a.is_primary DESC, s.site_name ASC',
            ['user_id' => $userId]
        );
    }

    private function complianceSiteCount(int $userId): int
    {
        if ($userId <= 0 || !$this->WIdb->tableExists('wi_compliance_user_sites')) {
            return 0;
        }

        try {
            return (int) ($this->WIdb->selectColumn(
                'SELECT COUNT(*) AS total FROM `wi_compliance_user_sites` WHERE `user_id` = :user_id AND `is_active` = 1',
                ['user_id' => $userId],
                'total'
            ) ?? 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    private function hasTables(array $tables): bool
    {
        foreach ($tables as $table) {
            if (!$this->WIdb->tableExists((string) $table)) {
                return false;
            }
        }

        return true;
    }
}

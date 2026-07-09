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
| File: WIHr.php
| Location: /WICore/WIClass/WIHr.php
| Type: Class
| Layer: HR Bridge
| Purpose Area: Worker/member employee context for profile, training and forms
| Version: 1.1.0
| Created: 2026-05-24
| Last Updated: 2026-06-08
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Resolves the logged-in member's HR employee context using WIHR tables. This
| keeps WIMembers as the workspace shell while WIHR remains the source of truth
| for employee, role, site and department assignments.
*/

final class WIHr
{
    private WIdb $WIdb;

    public function __construct(?WIdb $WIdb = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
    }

    public function employeeForUser(int $userId): array
    {
        if ($userId <= 0 || !$this->WIdb->tableExists('wi_hr_employees')) {
            return [];
        }

        return $this->WIdb->row(
            'SELECT e.*, b.display_name AS business_name
               FROM `wi_hr_employees` e
          LEFT JOIN `wi_org_businesses` b ON b.id = e.org_business_id
              WHERE e.user_id = :user_id
                AND e.data_status <> "erased"
              LIMIT 1',
            ['user_id' => $userId]
        );
    }

    public function employeeIdForUser(int $userId): int
    {
        $employee = $this->employeeForUser($userId);
        return (int) ($employee['id'] ?? 0);
    }

    public function onboardingStatus(int $userId): array
    {
        $employee = $this->employeeForUser($userId);
        $employeeId = (int) ($employee['id'] ?? 0);

        return [
            'is_employee' => $employeeId > 0,
            'employee_id' => $employeeId,
            'employee_number' => (string) ($employee['employee_number'] ?? ''),
            'display_name' => (string) ($employee['public_display_name'] ?? ''),
            'org_business_id' => (int) ($employee['org_business_id'] ?? 0),
            'business_name' => (string) ($employee['business_name'] ?? ''),
            'status' => (string) ($employee['data_status'] ?? 'not_started'),
            'right_to_work_status' => (string) ($employee['right_to_work_status'] ?? 'not_recorded'),
            'sites' => $employeeId > 0 ? $this->siteAssignmentsForEmployee($employeeId) : [],
            'roles' => $employeeId > 0 ? $this->roleAssignmentsForEmployee($employeeId) : [],
            'departments' => $employeeId > 0 ? $this->departmentAssignmentsForEmployee($employeeId) : [],
        ];
    }

    public function siteAssignmentsForUser(int $userId): array
    {
        $employeeId = $this->employeeIdForUser($userId);
        return $employeeId > 0 ? $this->siteAssignmentsForEmployee($employeeId) : [];
    }

    public function roleAssignmentsForUser(int $userId): array
    {
        $employeeId = $this->employeeIdForUser($userId);
        return $employeeId > 0 ? $this->roleAssignmentsForEmployee($employeeId) : [];
    }

    public function departmentAssignmentsForUser(int $userId): array
    {
        $employeeId = $this->employeeIdForUser($userId);
        return $employeeId > 0 ? $this->departmentAssignmentsForEmployee($employeeId) : [];
    }

    public function siteAssignmentsForEmployee(int $employeeId): array
    {
        if ($employeeId <= 0 || !$this->hasTables(['wi_hr_employee_site_assignments', 'wi_org_sites', 'wi_org_businesses'])) {
            return [];
        }

        return $this->WIdb->select(
            'SELECT a.id AS assignment_id,
                    a.employee_id,
                    a.org_site_id,
                    a.assignment_type,
                    a.is_primary,
                    a.status AS assignment_status,
                    a.effective_from,
                    a.effective_to,
                    s.org_business_id,
                    s.site_name,
                    s.site_type,
                    s.city_name,
                    s.region_name,
                    s.country_code,
                    s.status AS site_status,
                    b.display_name AS business_name
               FROM `wi_hr_employee_site_assignments` a
         INNER JOIN `wi_org_sites` s ON s.id = a.org_site_id
          LEFT JOIN `wi_org_businesses` b ON b.id = s.org_business_id
              WHERE a.employee_id = :employee_id
                AND a.status = "active"
           ORDER BY a.is_primary DESC, s.site_name ASC',
            ['employee_id' => $employeeId]
        );
    }

    public function roleAssignmentsForEmployee(int $employeeId): array
    {
        if ($employeeId <= 0 || !$this->WIdb->tableExists('wi_hr_employee_role_assignments')) {
            return [];
        }

        $roleJoin = $this->WIdb->tableExists('wi_user_roles')
            ? 'LEFT JOIN `wi_user_roles` r ON r.role_id = a.role_id'
            : '';

        $roleLabel = $this->WIdb->tableExists('wi_user_roles') && $this->WIdb->columnExists('wi_user_roles', 'role')
            ? 'r.role AS role_name'
            : 'CONCAT("Role #", a.role_id) AS role_name';

        return $this->WIdb->select(
            'SELECT a.id AS assignment_id,
                    a.employee_id,
                    a.role_id,
                    a.org_site_id,
                    a.org_department_id,
                    a.assignment_type,
                    a.is_primary,
                    a.status AS assignment_status,
                    a.effective_from,
                    a.effective_to,
                    ' . $roleLabel . '
               FROM `wi_hr_employee_role_assignments` a
               ' . $roleJoin . '
              WHERE a.employee_id = :employee_id
                AND a.status = "active"
           ORDER BY a.is_primary DESC, a.role_id ASC',
            ['employee_id' => $employeeId]
        );
    }

    public function departmentAssignmentsForEmployee(int $employeeId): array
    {
        if ($employeeId <= 0 || !$this->hasTables(['wi_hr_employee_department_assignments', 'wi_org_departments'])) {
            return [];
        }

        return $this->WIdb->select(
            'SELECT a.id AS assignment_id,
                    a.employee_id,
                    a.org_department_id,
                    a.org_site_id,
                    a.assignment_type,
                    a.is_primary,
                    a.status AS assignment_status,
                    d.department_key,
                    d.department_name,
                    d.department_type
               FROM `wi_hr_employee_department_assignments` a
         INNER JOIN `wi_org_departments` d ON d.id = a.org_department_id
              WHERE a.employee_id = :employee_id
                AND a.status = "active"
           ORDER BY a.is_primary DESC, d.sort_order ASC, d.department_name ASC',
            ['employee_id' => $employeeId]
        );
    }

    public function primarySiteForUser(int $userId): array
    {
        $sites = $this->siteAssignmentsForUser($userId);

        foreach ($sites as $site) {
            if ((int) ($site['is_primary'] ?? 0) === 1) {
                return $site;
            }
        }

        return $sites[0] ?? [];
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

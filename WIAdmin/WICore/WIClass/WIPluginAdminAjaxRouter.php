<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Ecosystem
| Project: Package / Manifest Skeleton System
| File: WIPluginAdminAjaxRouter.php
| Location: /WIAdmin/WICore/WIClass/WIPluginAdminAjaxRouter.php
| Type: PHP Admin AJAX Router
| Layer: Controller / Router Only
| Purpose Area: Generic Plugin Admin AJAX Dispatch
| Version: 1.0.0
| Created: 2026-06-03
| Last Updated: 2026-06-03
| Status: Production Ready - Batch 1 Route + Package Registry Skeleton
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Routes plugin admin actions such as wispecs_*, wirestaurant_* and
| witaskengine_* to their existing thin AdminController classes. This keeps
| WIAjax.php as a dispatcher and avoids hardcoding every plugin action into
| the main admin route file.
|--------------------------------------------------------------------------
*/

final class WIPluginAdminAjaxRouter
{
    private WIdb $WIdb;

    /** @var array<string,string> */
    private array $prefixToController = [
        'wialerts' => 'WIAlertsAdminController',
        'alerts' => 'WIAlertsAdminController',
        'wibarops' => 'WIBarOpsAdminController',
        'barops' => 'WIBarOpsAdminController',
        'wibookings' => 'WIBookingsAdminController',
        'bookings' => 'WIBookingsAdminController',
        'wideliverydispatch' => 'WIDeliveryDispatchAdminController',
        'deliverydispatch' => 'WIDeliveryDispatchAdminController',
        'wievents' => 'WIEventsAdminController',
        'events' => 'WIEventsAdminController',
        'wiguestcrm' => 'WIGuestCRMAdminController',
        'guestcrm' => 'WIGuestCRMAdminController',
        'wimaintenance' => 'WIMaintenanceAdminController',
        'maintenance' => 'WIMaintenanceAdminController',
        'winotifications' => 'WINotificationsAdminController',
        'notifications' => 'WINotificationsAdminController',
        'wiorderingsuppliers' => 'WIOrderingSuppliersAdminController',
        'orderingsuppliers' => 'WIOrderingSuppliersAdminController',
        'wipayroll' => 'WIPayrollAdminController',
        'payroll' => 'WIPayrollAdminController',
        'wiprofitengine' => 'WIProfitEngineAdminController',
        'profitengine' => 'WIProfitEngineAdminController',
        'wireportsanalytics' => 'WIReportsAnalyticsAdminController',
        'reportsanalytics' => 'WIReportsAnalyticsAdminController',
        'wirestaurant' => 'WIRestaurantAdminController',
        'restaurant' => 'WIRestaurantAdminController',
        'wirooms' => 'WIRoomsAdminController',
        'rooms' => 'WIRoomsAdminController',
        'wischeduler' => 'WISchedulerAdminController',
        'scheduler' => 'WISchedulerAdminController',
        'wispecs' => 'WISpecsAdminController',
        'specs' => 'WISpecsAdminController',
        'wistock' => 'WIStockAdminController',
        'stock' => 'WIStockAdminController',
        'witaskengine' => 'WITaskEngineAdminController',
        'taskengine' => 'WITaskEngineAdminController',
    ];

    public function __construct(?WIdb $WIdb = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
    }

    public function supports(string $action): bool
    {
        return $this->controllerClassForAction($action) !== null;
    }

    /** @param array<string,mixed> $request @return array<string,mixed> */
    public function dispatch(string $action, array $request): array
    {
        $controllerClass = $this->controllerClassForAction($action);
        if ($controllerClass === null) {
            return $this->error('Unsupported plugin admin action.', ['action' => $action]);
        }

        if (!class_exists($controllerClass)) {
            return $this->error('Plugin admin controller is not available.', [
                'action' => $action,
                'controller' => $controllerClass,
            ]);
        }

        try {
            $controller = new $controllerClass($this->WIdb);
        } catch (Throwable $e) {
            return $this->error('Plugin admin controller failed to initialise.', [
                'action' => $action,
                'controller' => $controllerClass,
                'error' => $e->getMessage(),
            ]);
        }

        if (!method_exists($controller, 'dispatch')) {
            return $this->error('Plugin admin controller does not expose dispatch().', [
                'action' => $action,
                'controller' => $controllerClass,
            ]);
        }

        $request['action'] = $action;

        try {
            $response = $controller->dispatch($request);
        } catch (Throwable $e) {
            return $this->error('Plugin admin action failed.', [
                'action' => $action,
                'controller' => $controllerClass,
                'error' => $e->getMessage(),
            ]);
        }

        return is_array($response) ? $response : $this->error('Plugin admin controller returned an invalid response.', [
            'action' => $action,
            'controller' => $controllerClass,
        ]);
    }

    private function controllerClassForAction(string $action): ?string
    {
        $action = strtolower(trim($action));
        if ($action === '' || !preg_match('/^[a-z0-9_.\-]+$/', $action)) {
            return null;
        }

        $normalised = str_replace(['.', '-'], '_', $action);
        $prefix = strtok($normalised, '_');
        if (!is_string($prefix) || $prefix === '') {
            return null;
        }

        return $this->prefixToController[$prefix] ?? null;
    }

    /** @param array<string,mixed> $data @return array<string,mixed> */
    private function error(string $message, array $data = []): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'data' => $data,
            'errors' => [$message],
        ];
    }
}

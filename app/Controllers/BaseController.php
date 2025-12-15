<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Shared regex rule to limit free-text inputs to letters, numbers, spaces, and line breaks.
     */
    protected const ALPHANUMERIC_SPACE_RULE = 'regex_match[/^[A-Za-z0-9 ]+$/]';
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    protected $helpers = [];

    protected $session;

    protected RoleModel $roleModel;
    protected NotificationModel $notificationModel;

    /**
     * Cached current user's permissions for the lifetime of the request.
     */
    private array $cachedPermissions = [];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = session();
        helper(['url']);
        $this->roleModel = new RoleModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Check if current user's role has been changed to have no permissions.
     * If so, log them out automatically.
     */
    protected function checkRolePermissions(): ?RedirectResponse
    {
        if (!$this->session->get('isLoggedIn')) {
            return null;
        }

        // System Administrator always has access
        if ($this->session->get('role') === 'System Administrator') {
            return null;
        }

        $roleId = (int) ($this->session->get('role_id') ?? 0);
        
        if ($roleId > 0) {
            $role = $this->roleModel->find($roleId);
            
            if ($role) {
                // Check if role has permissions
                $permissions = [];
                if (!empty($role['permissions'])) {
                    $decoded = json_decode($role['permissions'], true);
                    if (is_array($decoded)) {
                        $permissions = $decoded;
                    }
                }
                
                // If role has no permissions, log the user out
                if (empty($permissions)) {
                    $this->session->destroy();
                    return redirect()->to(site_url('login'))->with('error', 'You don\'t have permission to access this system.');
                }
            }
        }

        return null;
    }

    /**
     * Ensure the current user has the required permission.
     *
     * @param null|string|array $requiredPermission
     */
    protected function authorize(null|string|array $requiredPermission = null): ?RedirectResponse
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please login first.');
        }

        // Check if user's role has been changed to have no permissions
        $roleCheck = $this->checkRolePermissions();
        if ($roleCheck !== null) {
            return $roleCheck;
        }

        if ($this->session->get('role') === 'System Administrator') {
            return null;
        }

        if ($this->canAccess($requiredPermission)) {
            return null;
        }

        return redirect()->back()->with('error', 'You do not have permission to perform this action.');
    }

    /**
     * Determine if the current user can access the provided permission.
     *
     * @param null|string|array $permission
     */
    protected function canAccess(null|string|array $permission): bool
    {
        if (!$this->session->get('isLoggedIn')) {
            return false;
        }

        if ($this->session->get('role') === 'System Administrator') {
            return true;
        }

        if ($permission === null) {
            return true;
        }

        $permissions = $this->getCurrentPermissions();

        if ($this->hasPermission('system.full_access', $permissions)) {
            return true;
        }

        if (is_array($permission)) {
            foreach ($permission as $perm) {
                if ($this->hasPermission($perm, $permissions)) {
                    return true;
                }
            }
            return false;
        }

        return $this->hasPermission($permission, $permissions);
    }

    /**
     * Retrieve current user's permissions, loading from the database if needed.
     */
    protected function getCurrentPermissions(): array
    {
        if (!empty($this->cachedPermissions)) {
            return $this->cachedPermissions;
        }

        $permissions = $this->session->get('permissions');

        if (!is_array($permissions)) {
            $roleId = (int) ($this->session->get('role_id') ?? 0);
            if ($roleId > 0) {
                $role = $this->roleModel->find($roleId);
                if ($role && !empty($role['permissions'])) {
                    $decoded = json_decode($role['permissions'], true);
                    if (is_array($decoded)) {
                        $permissions = $decoded;
                    }
                }
            }
            $permissions = is_array($permissions) ? $permissions : [];
            $this->session->set('permissions', $permissions);
        }

        $this->cachedPermissions = $permissions;

        return $permissions;
    }

    /**
     * Helper to check if a permission exists in the provided set.
     */
    protected function hasPermission(string $permission, ?array $permissions = null): bool
    {
        if ($permission === '') {
            return false;
        }

        $permissions = $permissions ?? $this->getCurrentPermissions();

        return in_array($permission, $permissions, true);
    }

    /**
     * Forget cached permissions for the current request/session.
     */
    protected function refreshPermissions(array $permissions): void
    {
        $this->cachedPermissions = $permissions;
        $this->session->set('permissions', $permissions);
    }

    /**
     * Create notification for action (helper method for all controllers)
     * 
     * @param string $action Action type (create, update, delete, approve, reject, etc.)
     * @param string $module Module name (users, purchase_requests, etc.)
     * @param int|null $referenceId Reference ID for the action
     * @param array $userIds Array of user IDs to notify
     * @param string|null $customMessage Custom message (optional)
     * @param string|null $customTitle Custom title (optional)
     */
    protected function notifyAction(string $action, string $module, ?int $referenceId = null, array $userIds = [], ?string $customMessage = null, ?string $customTitle = null): void
    {
        if (empty($userIds)) {
            return;
        }

        // Default messages for common actions
        $actionMessages = [
            'create' => 'A new {module} has been created.',
            'update' => 'A {module} has been updated.',
            'delete' => 'A {module} has been deleted.',
            'approve' => 'A {module} has been approved.',
            'reject' => 'A {module} has been rejected.',
            'cancel' => 'A {module} has been cancelled.',
            'complete' => 'A {module} has been completed.',
            'payment' => 'A payment has been recorded for {module}.',
            'status_change' => 'Status of {module} has been changed.',
        ];

        $moduleLabels = [
            'users' => 'user',
            'purchase_requests' => 'purchase request',
            'purchase_orders' => 'purchase order',
            'deliveries' => 'delivery',
            'inventory' => 'inventory item',
            'branches' => 'branch',
            'franchises' => 'franchise',
            'accounts_payable' => 'invoice',
            'branch_transfers' => 'branch transfer',
            'supplier_contracts' => 'supplier contract',
            'roles' => 'role',
        ];

        $moduleLabel = $moduleLabels[$module] ?? $module;
        $defaultMessage = $actionMessages[$action] ?? 'An action has been performed on {module}.';
        $message = $customMessage ?? str_replace('{module}', $moduleLabel, $defaultMessage);
        $title = $customTitle ?? ucfirst(str_replace('_', ' ', $module)) . ' ' . ucfirst($action);

        // Get current user ID
        $currentUserId = (int) ($this->session->get('user_id') ?? 0);
        
        // If no specific users provided, notify all relevant users based on module
        if (empty($userIds)) {
            $userModel = new \App\Models\UserModel();
            
            switch ($module) {
                case 'purchase_requests':
                case 'purchase_orders':
                    // Notify central office admins and logistics coordinators
                    $users = $userModel->whereIn('role', ['Central Office Admin', 'Logistics Coordinator'])->findAll();
                    $userIds = array_column($users, 'id');
                    break;
                case 'deliveries':
                    // Notify branch managers and inventory staff
                    $users = $userModel->whereIn('role', ['Branch Manager', 'Inventory Staff'])->findAll();
                    $userIds = array_column($users, 'id');
                    break;
                case 'users':
                case 'roles':
                case 'branches':
                    // Notify all admins
                    $users = $userModel->whereIn('role', ['System Administrator', 'Central Office Admin'])->findAll();
                    $userIds = array_column($users, 'id');
                    break;
                default:
                    // Notify all users (can be refined per module)
                    $users = $userModel->findAll();
                    $userIds = array_column($users, 'id');
            }
        }

        // Remove current user from notifications (don't notify yourself)
        $userIds = array_filter($userIds, fn($id) => $id !== $currentUserId);

        // Create notifications for each user
        foreach ($userIds as $userId) {
            $this->notificationModel->createNotification([
                'user_id' => $userId,
                'type' => 'in_app',
                'title' => $title,
                'message' => $message,
                'reference_type' => $module,
                'reference_id' => $referenceId ?? 0,
            ]);
        }
    }
}

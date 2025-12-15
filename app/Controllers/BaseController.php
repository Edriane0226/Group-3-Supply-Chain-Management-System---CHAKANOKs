<?php

namespace App\Controllers;

use App\Models\RoleModel;
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

        // Always reload from database to ensure we have the latest permissions
        $roleId = (int) ($this->session->get('role_id') ?? 0);
        $permissions = [];
        
        if ($roleId > 0) {
            $role = $this->roleModel->find($roleId);
            if ($role && !empty($role['permissions'])) {
                $decoded = json_decode($role['permissions'], true);
                if (is_array($decoded)) {
                    $permissions = $decoded;
                }
            }
        }
        
        // Update session with latest permissions
        $this->session->set('permissions', $permissions);
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
}

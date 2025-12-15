<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\BranchModel;
use App\Models\ActivityLogModel;
use App\Models\SystemSettingModel;
use App\Models\ContactMessageModel;
class SystemAdmin extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;
    protected BranchModel $branchModel;
    protected ActivityLogModel $activityLogModel;
    protected SystemSettingModel $settingModel;
    protected ContactMessageModel $contactModel;
    protected $session;
    private array $permissionGroups;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->branchModel = new BranchModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->settingModel = new SystemSettingModel();
        $this->contactModel = new ContactMessageModel();
        $this->session = session();
        $this->permissionGroups = $this->buildPermissionGroups();
        helper(['form', 'url', 'text']);
    }


    /**
     * Log activity helper
     */
    private function logActivity(string $action, string $module, string $description = null, array $oldData = null, array $newData = null): void
    {
        $this->activityLogModel->logActivity($action, $module, $description, $oldData, $newData);
    }

    /**
     * Dashboard - System Admin overview
     */
    public function index()
    {
        if ($redirect = $this->authorize('system.full_access')) {
            return $redirect;
        }

        $userId = (int)($this->session->get('user_id') ?? 0);
        $notifications = $userId > 0 ? $this->notificationModel->getUserNotifications($userId, null) : [];
        $unreadCount = $userId > 0 ? $this->notificationModel->getUnreadCount($userId) : 0;
        
        $data = [
            'role'  => $this->session->get('role'),
            'title' => 'System Administration',
            'stats' => [
                'total_users'    => $this->userModel->countAll(),
                'active_users'   => $this->userModel->countAll(), // Users table may not have status column
                'total_branches' => $this->branchModel->countAll(),
                'total_roles'    => $this->roleModel->countAll(),
            ],
            'unreadMessages'   => $this->contactModel->getUnreadCount(),
            'activityStats'    => $this->activityLogModel->getStatistics(),
            'recentActivities' => $this->activityLogModel->getRecentActivities(10),
            'systemHealth'     => $this->getSystemHealth(),
            'notifications'    => array_slice($notifications, 0, 10),
            'unreadCount'      => $unreadCount,
        ];

        return view('reusables/sidenav', $data) . view('admin/dashboard', $data);
    }

    /**
     * User Management - List all users
     */
    public function users()
    {
        if ($redirect = $this->authorize('users.view')) {
            return $redirect;
        }

        $search = $this->request->getGet('search');
        $roleFilter = $this->request->getGet('role');

        $builder = $this->userModel->select('users.*, roles.role_name, branches.branch_name')
                                   ->join('roles', 'roles.id = users.role_id', 'left')
                                   ->join('branches', 'branches.id = users.branch_id', 'left');

        if ($search) {
            $builder->groupStart()
                    ->like('users.first_Name', $search)
                    ->orLike('users.last_Name', $search)
                    ->orLike('users.email', $search)
                    ->groupEnd();
        }

        if ($roleFilter) {
            $builder->where('users.role_id', $roleFilter);
        }

        $users = $builder->orderBy('users.id', 'DESC')->findAll();

        $data = [
            'role'       => $this->session->get('role'),
            'title'      => 'User Management',
            'users'      => $users,
            'roles'      => $this->roleModel->findAll(),
            'search'     => $search,
            'roleFilter' => $roleFilter,
        ];

        return view('reusables/sidenav', $data) . view('admin/users', $data);
    }

    /**
     * Create User Form
     */
    public function createUser()
    {
        if ($redirect = $this->authorize('users.create')) {
            return $redirect;
        }

        $data = [
            'role'     => $this->session->get('role'),
            'title'    => 'Create New User',
            'roles'    => $this->roleModel->findAll(),
            'branches' => $this->branchModel->findAll(),
        ];

        return view('reusables/sidenav', $data) . view('admin/user_form', $data);
    }

    /**
     * Store new user
     */
    public function storeUser()
    {
        if ($redirect = $this->authorize('users.create')) {
            return $redirect;
        }

        $rules = [
            'first_Name' => 'required|' . self::ALPHANUMERIC_SPACE_RULE . '|min_length[2]|max_length[100]',
            'middle_Name' => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[100]',
            'last_Name'  => 'required|' . self::ALPHANUMERIC_SPACE_RULE . '|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[6]',
            'role_id'    => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'first_Name'  => $this->request->getPost('first_Name'),
            'last_Name'   => $this->request->getPost('last_Name'),
            'middle_Name' => $this->request->getPost('middle_Name'),
            'email'       => $this->request->getPost('email'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id'     => $this->request->getPost('role_id'),
            'branch_id'   => $this->request->getPost('branch_id') ?: null,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $userId = $this->userModel->insert($userData);

        if ($userId) {
            $this->logActivity('create', 'users', 'Created new user: ' . $userData['email'], null, $userData);
            return redirect()->to(site_url('admin/users'))->with('success', 'User created successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create user.');
    }

    /**
     * Edit User Form
     */
    public function editUser(int $id)
    {
        if ($redirect = $this->authorize('users.update')) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User not found.');
        }

        $data = [
            'role'     => $this->session->get('role'),
            'title'    => 'Edit User',
            'user'     => $user,
            'roles'    => $this->roleModel->findAll(),
            'branches' => $this->branchModel->findAll(),
        ];

        return view('reusables/sidenav', $data) . view('admin/user_form', $data);
    }

    /**
     * Update user
     */
    public function updateUser(int $id)
    {
        if ($redirect = $this->authorize('users.update')) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $rules = [
            'first_Name' => 'required|' . self::ALPHANUMERIC_SPACE_RULE . '|min_length[2]|max_length[100]',
            'middle_Name' => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[100]',
            'last_Name'  => 'required|' . self::ALPHANUMERIC_SPACE_RULE . '|min_length[2]|max_length[100]',
            'email'      => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role_id'    => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $oldData = $user;

        $userData = [
            'first_Name'  => $this->request->getPost('first_Name'),
            'last_Name'   => $this->request->getPost('last_Name'),
            'middle_Name' => $this->request->getPost('middle_Name'),
            'email'       => $this->request->getPost('email'),
            'role_id'     => $this->request->getPost('role_id'),
            'branch_id'   => $this->request->getPost('branch_id') ?: null,
        ];

        // Update password if provided
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $userData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($id, $userData)) {
            $this->logActivity('update', 'users', 'Updated user: ' . $userData['email'], $oldData, $userData);
            return redirect()->to(site_url('admin/users'))->with('success', 'User updated successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update user.');
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id)
    {
        if ($redirect = $this->authorize('users.delete')) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Prevent self-deletion
        if ($id == $this->session->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($this->userModel->delete($id)) {
            $this->logActivity('delete', 'users', 'Deleted user: ' . $user['email'], $user, null);
            return redirect()->to(site_url('admin/users'))->with('success', 'User deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete user.');
    }

    /**
     * Reset user password
     */
    public function resetPassword(int $id)
    {
        if ($redirect = $this->authorize('users.reset_password')) {
            return $redirect;
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $newPassword = 'password123'; // Default reset password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($this->userModel->update($id, ['password' => $hashedPassword])) {
            $this->logActivity('password_reset', 'users', 'Reset password for user: ' . $user['email']);
            return redirect()->back()->with('success', "Password reset to default: {$newPassword}");
        }

        return redirect()->back()->with('error', 'Failed to reset password.');
    }

    /**
     * Role Management
     */
    public function roles()
    {
        if ($redirect = $this->authorize('roles.view')) {
            return $redirect;
        }

        $roles = $this->roleModel->findAll();

        foreach ($roles as &$role) {
            $role['permissions'] = $this->decodePermissions($role['permissions'] ?? null);
            $role['user_count'] = $this->userModel->where('role_id', $role['id'])->countAllResults();
        }
        unset($role);

        $data = [
            'role'  => $this->session->get('role'),
            'title' => 'Role Management',
            'roles' => $roles,
            'permissionGroups' => $this->permissionGroups,
        ];

        return view('reusables/sidenav', $data) . view('admin/roles', $data);
    }

    /**
     * Create Role
     */
    public function createRole()
    {
        if ($redirect = $this->authorize('roles.create')) {
            return $redirect;
        }

        $rules = [
            'role_name' => 'required|' . self::ALPHANUMERIC_SPACE_RULE . '|min_length[3]|max_length[100]|is_unique[roles.role_name]',
            'description' => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $permissions = $this->sanitizePermissions($this->request->getPost('permissions') ?? []);

        $roleData = [
            'role_name'   => $this->request->getPost('role_name'),
            'description' => $this->request->getPost('description'),
            'permissions' => json_encode($permissions),
        ];

        if ($this->roleModel->insert($roleData)) {
            $this->logActivity('create', 'roles', 'Created new role: ' . $roleData['role_name']);
            return redirect()->to(site_url('admin/roles'))->with('success', 'Role created successfully.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create role.');
    }

    /**
     * Update Role
     */
    public function updateRole(int $id)
    {
        if ($redirect = $this->authorize('roles.update')) {
            return $redirect;
        }

        $role = $this->roleModel->find($id);

        if (!$role) {
            return redirect()->back()->with('error', 'Role not found.');
        }

        $rules = [
            'role_name' => "required|" . self::ALPHANUMERIC_SPACE_RULE . "|min_length[3]|max_length[100]|is_unique[roles.role_name,id,{$id}]",
            'description' => 'permit_empty|' . self::ALPHANUMERIC_SPACE_RULE . '|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $permissions = $this->sanitizePermissions($this->request->getPost('permissions') ?? []);

        $roleData = [
            'role_name'   => $this->request->getPost('role_name'),
            'description' => $this->request->getPost('description'),
            'permissions' => json_encode($permissions),
        ];

        if ($this->roleModel->update($id, $roleData)) {
            $this->logActivity('update', 'roles', 'Updated role: ' . $roleData['role_name']);
            if ((int) ($this->session->get('role_id') ?? 0) === $id) {
                $this->refreshPermissions($permissions);
            }
            return redirect()->to(site_url('admin/roles'))->with('success', 'Role updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update role.');
    }

    /**
     * Delete Role
     */
    public function deleteRole(int $id)
    {
        if ($redirect = $this->authorize('roles.delete')) {
            return $redirect;
        }

        $role = $this->roleModel->find($id);

        if (!$role) {
            return redirect()->back()->with('error', 'Role not found.');
        }

        // Check if role has users
        $userCount = $this->userModel->where('role_id', $id)->countAllResults();
        if ($userCount > 0) {
            return redirect()->back()->with('error', "Cannot delete role. {$userCount} user(s) are assigned to this role.");
        }

        if ($this->roleModel->delete($id)) {
            $this->logActivity('delete', 'roles', 'Deleted role: ' . $role['role_name']);
            return redirect()->to(site_url('admin/roles'))->with('success', 'Role deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete role.');
    }

    /**
     * Branch Management
     */
    public function branches()
    {
        if ($redirect = $this->authorize('branches.view')) {
            return $redirect;
        }

        $branches = $this->branchModel->findAll();

        $data = [
            'role'     => $this->session->get('role'),
            'title'    => 'Branch Management',
            'branches' => $branches,
        ];

        return view('reusables/sidenav', $data) . view('admin/branches', $data);
    }

    /**
     * Activity Logs
     */
    public function activityLogs()
    {
        if ($redirect = $this->authorize('activity_logs.view')) {
            return $redirect;
        }

        $filters = [
            'user_id'   => $this->request->getGet('user_id'),
            'action'    => $this->request->getGet('action'),
            'module'    => $this->request->getGet('module'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
            'search'    => $this->request->getGet('search'),
        ];

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $logs = $this->activityLogModel->getLogs($filters, $perPage, $offset);
        $totalLogs = $this->activityLogModel->getLogsCount($filters);

        $data = [
            'role'      => $this->session->get('role'),
            'title'     => 'Activity Logs',
            'logs'      => $logs,
            'filters'   => $filters,
            'actions'   => $this->activityLogModel->getUniqueActions(),
            'modules'   => $this->activityLogModel->getUniqueModules(),
            'users'     => $this->userModel->select('id, first_Name, last_Name')->findAll(),
            'totalLogs' => $totalLogs,
            'page'      => $page,
            'perPage'   => $perPage,
            'totalPages' => ceil($totalLogs / $perPage),
        ];

        return view('reusables/sidenav', $data) . view('admin/activity_logs', $data);
    }

    /**
     * Clear old activity logs
     */
    public function clearLogs()
    {
        if ($redirect = $this->authorize('activity_logs.clear')) {
            return $redirect;
        }

        $daysToKeep = (int) ($this->request->getPost('days') ?? 90);
        $deletedCount = $this->activityLogModel->clearOldLogs($daysToKeep);

        $this->logActivity('clear_logs', 'system', "Cleared {$deletedCount} activity logs older than {$daysToKeep} days");

        return redirect()->back()->with('success', "Deleted {$deletedCount} old log entries.");
    }

    /**
     * System Settings
     */
    public function settings()
    {
        if ($redirect = $this->authorize('settings.view')) {
            return $redirect;
        }

        $data = [
            'role'     => $this->session->get('role'),
            'title'    => 'System Settings',
            'settings' => $this->settingModel->getGroupedSettings(),
            'groups'   => $this->settingModel->getGroups(),
        ];

        return view('reusables/sidenav', $data) . view('admin/settings', $data);
    }

    /**
     * Update Settings
     */
    public function updateSettings()
    {
        if ($redirect = $this->authorize('settings.update')) {
            return $redirect;
        }

        $settings = $this->request->getPost();
        unset($settings['csrf_test_name']); // Remove CSRF token

        if ($this->settingModel->updateSettings($settings)) {
            $this->logActivity('update', 'settings', 'Updated system settings');
            return redirect()->back()->with('success', 'Settings updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update settings.');
    }

    /**
     * Backup & Maintenance
     */
    public function backup()
    {
        if ($redirect = $this->authorize('backups.view')) {
            return $redirect;
        }

        $backupDir = WRITEPATH . 'backups';
        $backups = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.sql');
            foreach ($files as $file) {
                $backups[] = [
                    'filename' => basename($file),
                    'size'     => filesize($file),
                    'date'     => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
            // Sort by date descending
            usort($backups, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
        }

        $data = [
            'role'         => $this->session->get('role'),
            'title'        => 'Backup & Maintenance',
            'backups'      => $backups,
            'systemHealth' => $this->getSystemHealth(),
            'phpInfo'      => [
                'version'       => PHP_VERSION,
                'memory_limit'  => ini_get('memory_limit'),
                'max_execution' => ini_get('max_execution_time'),
                'upload_max'    => ini_get('upload_max_filesize'),
            ],
        ];

        return view('reusables/sidenav', $data) . view('admin/backup', $data);
    }

    /**
     * Create database backup
     */
    public function createBackup()
    {
        if ($redirect = $this->authorize('backups.create')) {
            return $redirect;
        }

        $backupDir = WRITEPATH . 'backups';

        // Create backup directory if not exists
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        // Get database configuration
        $db = \Config\Database::connect();
        $dbName = $db->getDatabase();

        // Simple backup using PHP (works without shell access)
        try {
            $tables = $db->listTables();
            $sql = "-- Database Backup\n";
            $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- Database: {$dbName}\n\n";

            foreach ($tables as $table) {
                // Get create table statement
                $query = $db->query("SHOW CREATE TABLE `{$table}`");
                $row = $query->getRowArray();
                $sql .= "\n\n-- Table: {$table}\n";
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $row['Create Table'] . ";\n";

                // Get table data
                $data = $db->table($table)->get()->getResultArray();
                if (!empty($data)) {
                    $sql .= "\n-- Data for {$table}\n";
                    foreach ($data as $row) {
                        $values = array_map(function($val) use ($db) {
                            return $val === null ? 'NULL' : "'" . $db->escapeString($val) . "'";
                        }, $row);
                        $sql .= "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
                    }
                }
            }

            file_put_contents($filepath, $sql);

            $this->logActivity('backup', 'system', 'Created database backup: ' . $filename);

            return redirect()->back()->with('success', 'Backup created successfully: ' . $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup(string $filename)
    {
        if ($redirect = $this->authorize('backups.download')) {
            return $redirect;
        }

        $filepath = WRITEPATH . 'backups/' . basename($filename);

        if (!file_exists($filepath)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        $this->logActivity('download', 'system', 'Downloaded backup: ' . $filename);

        return $this->response->download($filepath, null);
    }

    /**
     * Delete backup file
     */
    public function deleteBackup(string $filename)
    {
        if ($redirect = $this->authorize('backups.delete')) {
            return $redirect;
        }

        $filepath = WRITEPATH . 'backups/' . basename($filename);

        if (!file_exists($filepath)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        if (unlink($filepath)) {
            $this->logActivity('delete', 'system', 'Deleted backup: ' . $filename);
            return redirect()->back()->with('success', 'Backup deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete backup.');
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        if ($redirect = $this->authorize('backups.clear_cache')) {
            return $redirect;
        }

        // Clear CodeIgniter cache
        $cache = \Config\Services::cache();
        $cache->clean();

        // Clear writable/cache directory
        $cacheDir = WRITEPATH . 'cache';
        $this->deleteDirectory($cacheDir, false);

        $this->logActivity('clear_cache', 'system', 'Cleared system cache');

        return redirect()->back()->with('success', 'Cache cleared successfully.');
    }

    /**
     * Get system health information
     */
    private function getSystemHealth(): array
    {
        $db = \Config\Database::connect();

        return [
            'database_connected' => $db->connect() ? true : false,
            'writable_cache'     => is_writable(WRITEPATH . 'cache'),
            'writable_logs'      => is_writable(WRITEPATH . 'logs'),
            'writable_uploads'   => is_writable(FCPATH . 'uploads'),
            'php_version'        => PHP_VERSION,
            'ci_version'         => \CodeIgniter\CodeIgniter::CI_VERSION,
            'server_time'        => date('Y-m-d H:i:s'),
            'disk_free'          => disk_free_space('/'),
            'disk_total'         => disk_total_space('/'),
        ];
    }

    /**
     * Helper: Delete directory contents
     */
    private function deleteDirectory(string $dir, bool $deleteDir = true): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..', '.htaccess', 'index.html']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        return $deleteDir ? rmdir($dir) : true;
    }

    /**
     * Build the list of available permission groups.
     */
    private function buildPermissionGroups(): array
    {
        return [
            'system' => [
                'label' => 'System Access',
                'permissions' => [
                    'full_access' => 'Full system administration access',
                ],
            ],
            'dashboard' => [
                'label' => 'Dashboard',
                'permissions' => [
                    'view' => 'View system dashboard',
                ],
            ],
            'users' => [
                'label' => 'User Management',
                'permissions' => [
                    'view' => 'View user list',
                    'create' => 'Create users',
                    'update' => 'Update users',
                    'delete' => 'Delete users',
                    'reset_password' => 'Reset user passwords',
                ],
            ],
            'roles' => [
                'label' => 'Role Management',
                'permissions' => [
                    'view' => 'View roles',
                    'create' => 'Create roles',
                    'update' => 'Update roles',
                    'delete' => 'Delete roles',
                ],
            ],
            'branch_manager' => [
                'label' => 'Branch Manager Portal',
                'permissions' => [
                    'dashboard' => 'Access branch manager dashboard',
                    'reports' => 'View branch reports',
                    'manage_staff' => 'Manage branch staff',
                ],
            ],
            'branches' => [
                'label' => 'Branch Management',
                'permissions' => [
                    'view' => 'View branches',
                    'create' => 'Create branches',
                    'update' => 'Update branches',
                    'delete' => 'Delete branches',
                ],
            ],
            'branch_transfers' => [
                'label' => 'Branch Transfers',
                'permissions' => [
                    'view' => 'View transfer requests',
                    'create' => 'Create transfer requests',
                    'approve' => 'Approve incoming transfers',
                    'reject' => 'Reject transfer requests',
                    'complete' => 'Mark transfers as completed',
                ],
            ],
            'deliveries' => [
                'label' => 'Deliveries',
                'permissions' => [
                    'view' => 'View delivery schedules',
                    'receive' => 'Acknowledge received deliveries',
                    'cancel' => 'Cancel pending deliveries',
                ],
            ],
            'inventory' => [
                'label' => 'Inventory Management',
                'permissions' => [
                    'view' => 'View inventory balances',
                    'staff_portal' => 'Access inventory staff portal',
                    'stock_in' => 'Record stock in',
                    'stock_out' => 'Record stock out',
                    'adjust' => 'Adjust stock levels',
                    'export' => 'Export inventory reports',
                    'reports' => 'View inventory reports',
                    'confirm_delivery' => 'Confirm delivery receipts',
                ],
            ],
            'accounts_payable' => [
                'label' => 'Accounts Payable',
                'permissions' => [
                    'view' => 'View accounts payable records',
                    'manage' => 'Record and manage payments',
                    'print' => 'Print payment receipts',
                ],
            ],
            'purchase_requests' => [
                'label' => 'Purchase Requests',
                'permissions' => [
                    'view' => 'View purchase requests',
                    'create' => 'Create purchase requests',
                    'update' => 'Update purchase requests',
                    'delete' => 'Delete purchase requests',
                    'approve' => 'Approve purchase requests',
                    'reject' => 'Reject purchase requests',
                    'cancel' => 'Cancel purchase requests',
                ],
            ],
            'logistics' => [
                'label' => 'Logistics Coordination',
                'permissions' => [
                    'dashboard' => 'Access logistics dashboard',
                    'schedule' => 'Schedule deliveries',
                    'optimize_route' => 'Optimize delivery routes',
                    'update_status' => 'Update delivery statuses',
                    'calendar' => 'View logistics calendar',
                    'notifications' => 'View logistics notifications',
                ],
            ],
            'franchise' => [
                'label' => 'Franchise Management',
                'permissions' => [
                    'dashboard' => 'Access franchise dashboard',
                    'applications' => 'Manage franchise applications',
                    'franchises' => 'View franchise directory',
                    'manage_franchises' => 'Activate or suspend franchises',
                    'payments' => 'Manage franchise payments',
                    'allocations' => 'Manage franchise supply allocations',
                    'reports' => 'View franchise reports',
                    'send_reminders' => 'Send franchise payment reminders',
                ],
            ],
            'supplier_contracts' => [
                'label' => 'Supplier Contracts',
                'permissions' => [
                    'view' => 'View supplier contracts',
                    'create' => 'Create supplier contracts',
                    'update' => 'Update supplier contracts',
                    'delete' => 'Delete supplier contracts',
                ],
            ],
            'settings' => [
                'label' => 'System Settings',
                'permissions' => [
                    'view' => 'View settings',
                    'update' => 'Update settings',
                ],
            ],
            'activity_logs' => [
                'label' => 'Activity Logs',
                'permissions' => [
                    'view' => 'View activity logs',
                    'clear' => 'Clear activity logs',
                ],
            ],
            'backups' => [
                'label' => 'Backups & Maintenance',
                'permissions' => [
                    'view' => 'Access backup page',
                    'create' => 'Create database backups',
                    'download' => 'Download backups',
                    'delete' => 'Delete backups',
                    'clear_cache' => 'Clear system cache',
                ],
            ],
            'contact' => [
                'label' => 'Contact Messages',
                'permissions' => [
                    'view' => 'View contact messages',
                    'update_status' => 'Update message status',
                    'delete' => 'Delete contact messages',
                    'reply' => 'Reply to contact messages',
                ],
            ],
        ];
    }

    /**
     * Return the flat list of valid permission keys.
     */
    private function getAllPermissionKeys(): array
    {
        static $keys = null;

        if ($keys !== null) {
            return $keys;
        }

        $keys = [];

        foreach ($this->permissionGroups as $groupKey => $group) {
            foreach (array_keys($group['permissions']) as $permissionKey) {
                $keys[] = $groupKey . '.' . $permissionKey;
            }
        }

        return $keys;
    }

    /**
     * Sanitize incoming permissions data to allow only defined keys.
     */
    private function sanitizePermissions($permissions): array
    {
        if (!is_array($permissions)) {
            $permissions = $permissions ? [$permissions] : [];
        }

        $permissions = array_map('strval', $permissions);
        $validPermissions = $this->getAllPermissionKeys();

        return array_values(array_unique(array_intersect($permissions, $validPermissions)));
    }

    /**
     * Decode a JSON permissions payload.
     */
    private function decodePermissions(?string $permissions): array
    {
        if (empty($permissions)) {
            return [];
        }

        $decoded = json_decode($permissions, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Contact Messages - List all messages
     */
    public function contactMessages()
    {
        if ($redirect = $this->authorize('contact.view')) {
            return $redirect;
        }

        $status = $this->request->getGet('status') ?? 'all';
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $messages = $this->contactModel->getMessages($status === 'all' ? null : $status, $perPage, $offset);
        $totalMessages = $status === 'all' 
            ? $this->contactModel->countAll() 
            : $this->contactModel->where('status', $status)->countAllResults();
        $unreadCount = $this->contactModel->getUnreadCount();

        $data = [
            'role'         => $this->session->get('role'),
            'title'        => 'Contact Messages',
            'messages'      => $messages,
            'status'        => $status,
            'unreadCount'  => $unreadCount,
            'totalMessages' => $totalMessages,
            'currentPage'  => $page,
            'perPage'      => $perPage,
            'totalPages'   => ceil($totalMessages / $perPage),
        ];

        return view('reusables/sidenav', $data) . view('admin/contact_messages', $data);
    }

    /**
     * View single contact message
     */
    public function viewContactMessage($id)
    {
        if ($redirect = $this->authorize('contact.view')) {
            return $redirect;
        }

        $message = $this->contactModel->getMessage($id);

        if (!$message) {
            return redirect()->to(site_url('admin/contact-messages'))
                ->with('error', 'Message not found.');
        }

        // Mark as read if unread
        if ($message['status'] === 'unread') {
            $this->contactModel->markAsRead($id, $this->session->get('user_id'));
            $this->logActivity('read_message', 'contact', "Read contact message from {$message['name']}");
        }

        $data = [
            'role'    => $this->session->get('role'),
            'title'   => 'View Message',
            'message' => $message,
        ];

        return view('reusables/sidenav', $data) . view('admin/view_contact_message', $data);
    }

    /**
     * Update message status
     */
    public function updateMessageStatus($id)
    {
        if ($redirect = $this->authorize('contact.update_status')) {
            return $redirect;
        }

        $status = $this->request->getPost('status');
        $validStatuses = ['unread', 'read', 'replied', 'archived'];

        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $message = $this->contactModel->find($id);
        if (!$message) {
            return redirect()->back()->with('error', 'Message not found.');
        }

        $this->contactModel->updateStatus($id, $status);
        
        if ($status === 'read' && $message['status'] === 'unread') {
            $this->contactModel->markAsRead($id, $this->session->get('user_id'));
        }

        $this->logActivity('update_message_status', 'contact', "Updated message status to {$status}");

        return redirect()->back()->with('success', 'Message status updated.');
    }

    /**
     * Delete contact message
     */
    public function deleteContactMessage($id)
    {
        if ($redirect = $this->authorize('contact.delete')) {
            return $redirect;
        }

        $message = $this->contactModel->find($id);
        if (!$message) {
            return redirect()->back()->with('error', 'Message not found.');
        }

        $this->contactModel->delete($id);
        $this->logActivity('delete_message', 'contact', "Deleted contact message from {$message['name']}");

        return redirect()->to(site_url('admin/contact-messages'))
            ->with('success', 'Message deleted successfully.');
    }
}


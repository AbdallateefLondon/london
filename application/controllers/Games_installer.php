<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Educational Games Web Installer
 * Automated installation system for the Educational Games module
 */
class Games_installer extends CI_Controller
{
    private $required_tables = [
        'game_categories',
        'games', 
        'game_levels',
        'game_scores',
        'game_leaderboards',
        'game_permissions',
        'game_favorites'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->dbforge();
        $this->load->library('session');
    }

    /**
     * Main installer page
     */
    public function index()
    {
        // Check if already installed
        if ($this->isAlreadyInstalled()) {
            $this->showStatus('Educational Games System is already installed!', 'success');
            return;
        }

        $data['title'] = 'Educational Games System - Web Installer';
        $data['step'] = 1;
        
        $this->load->view('games_installer/index', $data);
    }

    /**
     * System requirements check
     */
    public function check_requirements()
    {
        $requirements = [
            'PHP Version >= 7.0' => version_compare(PHP_VERSION, '7.0.0', '>='),
            'CodeIgniter Framework' => defined('BASEPATH'),
            'MySQL Database' => $this->db->conn_id !== false,
            'GD Extension (for image processing)' => extension_loaded('gd'),
            'JSON Extension' => extension_loaded('json'),
            'Uploads Directory Writable' => is_writable('./uploads/'),
            'Games Upload Directory' => $this->checkGamesDirectory()
        ];

        $all_passed = true;
        foreach ($requirements as $requirement => $status) {
            if (!$status) {
                $all_passed = false;
                break;
            }
        }

        $data = [
            'title' => 'System Requirements Check',
            'requirements' => $requirements,
            'all_passed' => $all_passed,
            'step' => 2
        ];

        $this->load->view('games_installer/requirements', $data);
    }

    /**
     * Database installation step
     */
    public function install_database()
    {
        try {
            $this->createDatabaseTables();
            $this->insertInitialData();
            $this->createMenuEntries();
            $this->setupPermissions();
            
            $data = [
                'title' => 'Database Installation Complete',
                'step' => 3,
                'success' => true,
                'message' => 'All database tables and initial data have been created successfully!'
            ];

        } catch (Exception $e) {
            $data = [
                'title' => 'Database Installation Failed',
                'step' => 3,
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }

        $this->load->view('games_installer/database_result', $data);
    }

    /**
     * Final configuration and completion
     */
    public function complete_installation()
    {
        try {
            // Mark installation as complete
            $this->markInstallationComplete();
            
            // Set default game categories
            $this->setDefaultCategories();
            
            $data = [
                'title' => 'Installation Complete!',
                'step' => 4,
                'success' => true,
                'admin_url' => base_url('admin/games'),
                'student_url' => base_url('user/student_games'),
                'leaderboard_url' => base_url('leaderboard')
            ];

        } catch (Exception $e) {
            $data = [
                'title' => 'Installation Error',
                'step' => 4,
                'success' => false,
                'message' => 'Error completing installation: ' . $e->getMessage()
            ];
        }

        $this->load->view('games_installer/complete', $data);
    }

    /**
     * Uninstall the games system
     */
    public function uninstall()
    {
        if ($this->input->post('confirm_uninstall') === 'yes') {
            try {
                $this->removeMenuEntries();
                $this->removePermissions();
                $this->dropDatabaseTables();
                
                $this->showStatus('Educational Games System has been uninstalled successfully.', 'success');
            } catch (Exception $e) {
                $this->showStatus('Error during uninstallation: ' . $e->getMessage(), 'danger');
            }
        } else {
            $this->load->view('games_installer/uninstall_confirm');
        }
    }

    /**
     * Check if games system is already installed
     */
    private function isAlreadyInstalled()
    {
        foreach ($this->required_tables as $table) {
            if (!$this->db->table_exists($table)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Create games upload directory
     */
    private function checkGamesDirectory()
    {
        $games_dir = './uploads/games/';
        
        if (!is_dir($games_dir)) {
            if (mkdir($games_dir, 0755, true)) {
                // Create index.html for security
                file_put_contents($games_dir . 'index.html', '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
                return true;
            }
            return false;
        }
        
        return is_writable($games_dir);
    }

    /**
     * Create all required database tables
     */
    private function createDatabaseTables()
    {
        // Read and execute the SQL file
        $sql_file = APPPATH . '../database_games_setup.sql';
        
        if (!file_exists($sql_file)) {
            throw new Exception('SQL installation file not found: ' . $sql_file);
        }

        $sql_content = file_get_contents($sql_file);
        $sql_statements = explode(';', $sql_content);

        foreach ($sql_statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $this->db->query($statement);
                } catch (Exception $e) {
                    // Log the error but continue (some statements might fail due to existing data)
                    log_message('error', 'Games Installer SQL Error: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Insert initial game categories and sample data
     */
    private function insertInitialData()
    {
        // Game categories are already inserted via SQL file
        // Check if we need to add any additional initial data
        
        $category_count = $this->db->count_all('game_categories');
        if ($category_count == 0) {
            // Fallback - insert categories manually if SQL didn't work
            $categories = [
                ['name' => 'Math Quiz', 'description' => 'Mathematical problems and arithmetic challenges', 'icon' => 'fa-calculator'],
                ['name' => 'Word Completion', 'description' => 'Complete missing letters and words', 'icon' => 'fa-font'],
                ['name' => 'Memory Match', 'description' => 'Memory card matching games', 'icon' => 'fa-th-large'],
                ['name' => 'List Sorting', 'description' => 'Match items from List A to List B', 'icon' => 'fa-sort'],
                ['name' => 'True False Challenge', 'description' => 'Quick true or false questions', 'icon' => 'fa-check-circle'],
                ['name' => 'Picture Puzzle', 'description' => 'Visual puzzles and problem solving', 'icon' => 'fa-puzzle-piece'],
                ['name' => 'Vocabulary Builder', 'description' => 'Word definitions and vocabulary', 'icon' => 'fa-book']
            ];

            foreach ($categories as $category) {
                $this->db->insert('game_categories', $category);
            }
        }
    }

    /**
     * Create sidebar menu entries
     */
    private function createMenuEntries()
    {
        // Check if sidebar_menus table exists (may vary by Smart School version)
        if ($this->db->table_exists('sidebar_menus')) {
            // Insert main menu item
            $menu_data = [
                'menu_label' => 'Educational Games',
                'link_type' => 'internal',
                'access_permissions' => 'games,can_view',
                'icon' => 'fa-gamepad',
                'sidebar_order' => 999,
                'activate_menu' => 'Educational_Games',
                'short_code' => 'games',
                'lang_key' => 'educational_games',
                'is_active' => 'yes'
            ];

            $this->db->insert('sidebar_menus', $menu_data);
            $menu_id = $this->db->insert_id();

            // Insert submenu items
            $submenus = [
                [
                    'sidebar_menu_id' => $menu_id,
                    'menu_label' => 'Manage Games',
                    'link_type' => 'internal',
                    'url' => 'admin/games',
                    'access_permissions' => 'games,can_view',
                    'activate_controller' => 'games',
                    'activate_methods' => 'index,create,edit,view',
                    'sidebar_order' => 1,
                    'lang_key' => 'manage_games',
                    'is_active' => 'yes'
                ],
                [
                    'sidebar_menu_id' => $menu_id,
                    'menu_label' => 'Leaderboards',
                    'link_type' => 'internal',
                    'url' => 'leaderboard',
                    'access_permissions' => 'games,can_view',
                    'activate_controller' => 'leaderboard',
                    'activate_methods' => 'index,game,class_leaderboard',
                    'sidebar_order' => 2,
                    'lang_key' => 'leaderboards',
                    'is_active' => 'yes'
                ]
            ];

            foreach ($submenus as $submenu) {
                $this->db->insert('sidebar_sub_menus', $submenu);
            }
        }
    }

    /**
     * Setup permissions for games module
     */
    private function setupPermissions()
    {
        // Check if permission system tables exist
        if ($this->db->table_exists('permission_group') && $this->db->table_exists('permissions')) {
            // Create permission group
            $permission_group_data = [
                'name' => 'Games',
                'short_code' => 'games',
                'is_active' => 'yes',
                'system' => 'yes',
                'sort_order' => 999
            ];

            $this->db->insert('permission_group', $permission_group_data);
            $group_id = $this->db->insert_id();

            // Create individual permissions
            $permissions = [
                [
                    'permission_group_id' => $group_id,
                    'name' => 'Games',
                    'short_code' => 'games',
                    'enable_view' => 1,
                    'enable_add' => 1,
                    'enable_edit' => 1,
                    'enable_delete' => 1
                ],
                [
                    'permission_group_id' => $group_id,
                    'name' => 'Game Categories',
                    'short_code' => 'game_categories',
                    'enable_view' => 1,
                    'enable_add' => 1,
                    'enable_edit' => 1,
                    'enable_delete' => 1
                ]
            ];

            foreach ($permissions as $permission) {
                $this->db->insert('permissions', $permission);
                $permission_id = $this->db->insert_id();

                // Assign to Super Admin role (role_id = 1)
                $role_permission_data = [
                    'role_id' => 1,
                    'permission_id' => $permission_id
                ];
                $this->db->insert('role_permission', $role_permission_data);
            }
        }
    }

    /**
     * Mark installation as complete
     */
    private function markInstallationComplete()
    {
        // Create a marker file or database entry to indicate completion
        $marker_file = APPPATH . '../games_installed.txt';
        file_put_contents($marker_file, 'Educational Games System installed on: ' . date('Y-m-d H:i:s'));
    }

    /**
     * Set default game categories if needed
     */
    private function setDefaultCategories()
    {
        // Ensure all 7 game categories are active
        $this->db->where('is_active !=', 'yes');
        $this->db->update('game_categories', ['is_active' => 'yes']);
    }

    /**
     * Remove menu entries during uninstall
     */
    private function removeMenuEntries()
    {
        if ($this->db->table_exists('sidebar_menus')) {
            $this->db->where('short_code', 'games');
            $menu = $this->db->get('sidebar_menus')->row();
            
            if ($menu) {
                // Remove submenus first
                $this->db->where('sidebar_menu_id', $menu->id);
                $this->db->delete('sidebar_sub_menus');
                
                // Remove main menu
                $this->db->where('id', $menu->id);
                $this->db->delete('sidebar_menus');
            }
        }
    }

    /**
     * Remove permissions during uninstall
     */
    private function removePermissions()
    {
        if ($this->db->table_exists('permission_group')) {
            $this->db->where('short_code', 'games');
            $group = $this->db->get('permission_group')->row();
            
            if ($group) {
                // Remove role permissions
                $this->db->select('id');
                $this->db->where('permission_group_id', $group->id);
                $permissions = $this->db->get('permissions')->result();
                
                foreach ($permissions as $permission) {
                    $this->db->where('permission_id', $permission->id);
                    $this->db->delete('role_permission');
                }
                
                // Remove permissions
                $this->db->where('permission_group_id', $group->id);
                $this->db->delete('permissions');
                
                // Remove permission group
                $this->db->where('id', $group->id);
                $this->db->delete('permission_group');
            }
        }
    }

    /**
     * Drop all game-related tables
     */
    private function dropDatabaseTables()
    {
        foreach (array_reverse($this->required_tables) as $table) {
            if ($this->db->table_exists($table)) {
                $this->dbforge->drop_table($table, true);
            }
        }
        
        // Remove marker file
        $marker_file = APPPATH . '../games_installed.txt';
        if (file_exists($marker_file)) {
            unlink($marker_file);
        }
    }

    /**
     * Show status message
     */
    private function showStatus($message, $type = 'info')
    {
        $data = [
            'title' => 'Educational Games Installer',
            'message' => $message,
            'type' => $type
        ];
        
        $this->load->view('games_installer/status', $data);
    }
}
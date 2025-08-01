<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Games Controller (Admin/Teacher)
 * Handles educational games management for teachers and administrators
 * Following Smart School CodeIgniter V3 patterns
 */
class Games extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models
        $this->load->model('Games_model');
        $this->load->model('Gamecategories_model');
        $this->load->model('Gamescores_model');
        $this->load->model('Gameleaderboard_model');
        
        // Load additional Smart School models for relationships
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->model('subject_model');
        $this->load->model('staff_model');
        
        // Load libraries
        $this->load->library('form_validation');
        $this->load->library('upload');
        
        // Get school settings
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    /**
     * Games dashboard - List all games
     */
    public function index()
    {
        if (!$this->rbac->hasPrivilege('games', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Educational Games');
        $this->session->set_userdata('sub_menu', 'admin/games');
        
        $data['title'] = 'Educational Games';
        
        // Get current staff ID to filter games
        $staff_id = $this->customlib->getStaffID();
        $user_type = $this->customlib->getStaffRole();
        
        // Super Admin can see all games, Teachers see their own games
        if ($user_type && json_decode($user_type)->id == 1) {
            // Super Admin - get all games
            $data['games'] = $this->Games_model->getAllGames();
        } else {
            // Teacher - get only their games
            $data['games'] = $this->Games_model->getGamesByTeacher($staff_id);
        }
        
        // Get game categories for filter
        $data['categories'] = $this->Gamecategories_model->getAllCategories();
        
        // Get classes for filter
        $data['classlist'] = $this->class_model->get();
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/games/index', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Create new game form
     */
    public function create()
    {
        if (!$this->rbac->hasPrivilege('games', 'can_add')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Educational Games');
        $this->session->set_userdata('sub_menu', 'admin/games/create');
        
        $data['title'] = 'Create New Game';
        
        // Get form data
        $data['categories'] = $this->Gamecategories_model->getAllCategories();
        $data['classlist'] = $this->class_model->get();
        $data['subjectlist'] = $this->subject_model->get();
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/games/create', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Store new game
     */
    public function store()
    {
        if (!$this->rbac->hasPrivilege('games', 'can_add')) {
            access_denied();
        }

        // Form validation rules
        $this->form_validation->set_rules('title', 'Game Title', 'required|trim|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'required|trim|xss_clean');
        $this->form_validation->set_rules('game_category_id', 'Game Category', 'required|numeric');
        $this->form_validation->set_rules('class_id', 'Class', 'required|numeric');
        $this->form_validation->set_rules('section_id', 'Section', 'required|numeric');
        $this->form_validation->set_rules('subject_id', 'Subject', 'required|numeric');
        $this->form_validation->set_rules('difficulty_level', 'Difficulty Level', 'required|in_list[Easy,Medium,Hard,Expert]');
        $this->form_validation->set_rules('max_attempts', 'Max Attempts', 'required|integer');
        $this->form_validation->set_rules('time_limit', 'Time Limit', 'required|numeric');
        $this->form_validation->set_rules('passing_score', 'Passing Score', 'required|numeric|greater_than[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('total_questions', 'Total Questions', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('game_data', 'Game Questions', 'required|callback_validate_game_data');

        if ($this->form_validation->run() == false) {
            // Return to create form with errors
            $this->create();
        } else {
            // Prepare game data
            $game_data = array(
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'game_category_id' => $this->input->post('game_category_id'),
                'class_id' => $this->input->post('class_id'),
                'section_id' => $this->input->post('section_id'),
                'subject_id' => $this->input->post('subject_id'),
                'created_by_staff_id' => $this->customlib->getStaffID(),
                'difficulty_level' => $this->input->post('difficulty_level'),
                'max_attempts' => $this->input->post('max_attempts'),
                'time_limit' => $this->input->post('time_limit'),
                'passing_score' => $this->input->post('passing_score'),
                'total_questions' => $this->input->post('total_questions'),
                'game_data' => $this->input->post('game_data'),
                'show_answers' => $this->input->post('show_answers') ? 1 : 0,
                'randomize_questions' => $this->input->post('randomize_questions') ? 1 : 0
            );

            // Handle image upload if provided
            if (!empty($_FILES['game_image']['name'])) {
                $upload_result = $this->handleImageUpload();
                if ($upload_result['status']) {
                    $game_data['game_image'] = $upload_result['filename'];
                } else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger">' . $upload_result['error'] . '</div>');
                    $this->create();
                    return;
                }
            }

            // Create the game
            $game_id = $this->Games_model->createGame($game_data);
            
            if ($game_id) {
                $this->session->set_flashdata('msg', '<div class="alert alert-success">Game created successfully!</div>');
                redirect('admin/games');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger">Failed to create game. Please try again.</div>');
                $this->create();
            }
        }
    }

    /**
     * Edit game form
     */
    public function edit($game_id)
    {
        if (!$this->rbac->hasPrivilege('games', 'can_edit')) {
            access_denied();
        }

        // Get game details
        $game = $this->Games_model->getGameById($game_id);
        if (!$game) {
            show_404();
        }

        // Check if user can edit this game (teachers can only edit their own games)
        $staff_id = $this->customlib->getStaffID();
        $user_type = $this->customlib->getStaffRole();
        
        if ($user_type && json_decode($user_type)->id != 1 && $game['created_by_staff_id'] != $staff_id) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Educational Games');
        $this->session->set_userdata('sub_menu', 'admin/games/edit');
        
        $data['title'] = 'Edit Game';
        $data['game'] = $game;
        $data['categories'] = $this->Gamecategories_model->getAllCategories();
        $data['classlist'] = $this->class_model->get();
        $data['subjectlist'] = $this->subject_model->get();
        $data['sections'] = $this->section_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/games/edit', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Update game
     */
    public function update($game_id)
    {
        if (!$this->rbac->hasPrivilege('games', 'can_edit')) {
            access_denied();
        }

        // Get game details
        $game = $this->Games_model->getGameById($game_id);
        if (!$game) {
            show_404();
        }

        // Check if user can edit this game
        $staff_id = $this->customlib->getStaffID();
        $user_type = $this->customlib->getStaffRole();
        
        if ($user_type && json_decode($user_type)->id != 1 && $game['created_by_staff_id'] != $staff_id) {
            access_denied();
        }

        // Form validation
        $this->form_validation->set_rules('title', 'Game Title', 'required|trim|xss_clean');
        $this->form_validation->set_rules('description', 'Description', 'required|trim|xss_clean');
        $this->form_validation->set_rules('game_category_id', 'Game Category', 'required|numeric');
        $this->form_validation->set_rules('class_id', 'Class', 'required|numeric');
        $this->form_validation->set_rules('section_id', 'Section', 'required|numeric');
        $this->form_validation->set_rules('subject_id', 'Subject', 'required|numeric');
        $this->form_validation->set_rules('difficulty_level', 'Difficulty Level', 'required|in_list[Easy,Medium,Hard,Expert]');
        $this->form_validation->set_rules('max_attempts', 'Max Attempts', 'required|integer');
        $this->form_validation->set_rules('time_limit', 'Time Limit', 'required|numeric');
        $this->form_validation->set_rules('passing_score', 'Passing Score', 'required|numeric|greater_than[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('total_questions', 'Total Questions', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('game_data', 'Game Questions', 'required|callback_validate_game_data');

        if ($this->form_validation->run() == false) {
            $this->edit($game_id);
        } else {
            // Prepare update data
            $update_data = array(
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'game_category_id' => $this->input->post('game_category_id'),
                'class_id' => $this->input->post('class_id'),
                'section_id' => $this->input->post('section_id'),
                'subject_id' => $this->input->post('subject_id'),
                'difficulty_level' => $this->input->post('difficulty_level'),
                'max_attempts' => $this->input->post('max_attempts'),
                'time_limit' => $this->input->post('time_limit'),
                'passing_score' => $this->input->post('passing_score'),
                'total_questions' => $this->input->post('total_questions'),
                'game_data' => $this->input->post('game_data'),
                'show_answers' => $this->input->post('show_answers') ? 1 : 0,
                'randomize_questions' => $this->input->post('randomize_questions') ? 1 : 0
            );

            // Handle image upload if provided
            if (!empty($_FILES['game_image']['name'])) {
                $upload_result = $this->handleImageUpload();
                if ($upload_result['status']) {
                    $update_data['game_image'] = $upload_result['filename'];
                } else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger">' . $upload_result['error'] . '</div>');
                    $this->edit($game_id);
                    return;
                }
            }

            // Update the game
            $result = $this->Games_model->updateGame($game_id, $update_data);
            
            if ($result) {
                $this->session->set_flashdata('msg', '<div class="alert alert-success">Game updated successfully!</div>');
                redirect('admin/games');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger">Failed to update game. Please try again.</div>');
                $this->edit($game_id);
            }
        }
    }

    /**
     * Delete/deactivate game
     */
    public function delete($game_id)
    {
        if (!$this->rbac->hasPrivilege('games', 'can_delete')) {
            access_denied();
        }

        // Get game details
        $game = $this->Games_model->getGameById($game_id);
        if (!$game) {
            show_404();
        }

        // Check if user can delete this game
        $staff_id = $this->customlib->getStaffID();
        $user_type = $this->customlib->getStaffRole();
        
        if ($user_type && json_decode($user_type)->id != 1 && $game['created_by_staff_id'] != $staff_id) {
            access_denied();
        }

        // Soft delete the game
        $result = $this->Games_model->deleteGame($game_id);
        
        if ($result) {
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Game deleted successfully!</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Failed to delete game. Please try again.</div>');
        }
        
        redirect('admin/games');
    }

    /**
     * View game details and statistics
     */
    public function view($game_id)
    {
        if (!$this->rbac->hasPrivilege('games', 'can_view')) {
            access_denied();
        }

        // Get game details
        $game = $this->Games_model->getGameById($game_id);
        if (!$game) {
            show_404();
        }

        $this->session->set_userdata('top_menu', 'Educational Games');
        $this->session->set_userdata('sub_menu', 'admin/games/view');
        
        $data['title'] = 'Game Details: ' . $game['title'];
        $data['game'] = $game;
        
        // Get game statistics
        $data['stats'] = $this->Games_model->getGameStats($game_id);
        
        // Get detailed analytics
        $data['analytics'] = $this->Gamescores_model->getGameAnalytics($game_id);
        
        // Get top 10 leaderboard
        $data['leaderboard'] = $this->Gameleaderboard_model->getGameLeaderboard($game_id, 10);
        
        // Get recent activities
        $data['recent_activities'] = $this->Gameleaderboard_model->getRecentActivities(10);
        
        // Get performance stats
        $data['performance_stats'] = $this->Gameleaderboard_model->getGamePerformanceStats($game_id);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/games/view', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Preview game (for testing)
     */
    public function preview($game_id)
    {
        if (!$this->rbac->hasPrivilege('games', 'can_view')) {
            access_denied();
        }

        // Get game details
        $game = $this->Games_model->getGameById($game_id);
        if (!$game) {
            show_404();
        }

        $data['title'] = 'Preview Game: ' . $game['title'];
        $data['game'] = $game;
        $data['preview_mode'] = true;

        $this->load->view('admin/games/preview', $data);
    }

    /**
     * Get sections for a specific class (AJAX)
     */
    public function getSections()
    {
        $class_id = $this->input->post('class_id');
        $sections = $this->section_model->getSectionByClassId($class_id);
        
        $html = '<option value="">Select Section</option>';
        foreach ($sections as $section) {
            $html .= '<option value="' . $section['id'] . '">' . $section['section'] . '</option>';
        }
        
        echo $html;
    }

    /**
     * Validate game data (JSON format)
     */
    public function validate_game_data($game_data)
    {
        $decoded = json_decode($game_data, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->form_validation->set_message('validate_game_data', 'Game data must be valid JSON format.');
            return false;
        }
        
        if (!isset($decoded['questions']) || !is_array($decoded['questions'])) {
            $this->form_validation->set_message('validate_game_data', 'Game data must contain questions array.');
            return false;
        }
        
        if (empty($decoded['questions'])) {
            $this->form_validation->set_message('validate_game_data', 'Game must have at least one question.');
            return false;
        }
        
        return true;
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload()
    {
        $config['upload_path'] = './uploads/games/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = true;
        
        // Create directory if it doesn't exist
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }
        
        $this->upload->initialize($config);
        
        if ($this->upload->do_upload('game_image')) {
            $upload_data = $this->upload->data();
            return array('status' => true, 'filename' => $upload_data['file_name']);
        } else {
            return array('status' => false, 'error' => $this->upload->display_errors());
        }
    }
}
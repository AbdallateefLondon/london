<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Leaderboard Controller
 * Handles leaderboard displays for educational games
 * Accessible by both students and teachers
 * Following Smart School CodeIgniter V3 patterns
 */
class Leaderboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Determine user type and load appropriate parent
        $this->load->library('session');
        $user_data = $this->session->userdata();
        
        if (isset($user_data['student'])) {
            // Student user
            $this->load->library('customlib');
            $this->load->model('setting_model');
        } elseif (isset($user_data['admin'])) {
            // Admin/Teacher user
            $this->load->library('rbac');
            $this->load->library('customlib');
            $this->load->model('setting_model');
        } else {
            // Not logged in, redirect to login
            redirect('site/login');
        }
        
        // Load required models
        $this->load->model('Games_model');
        $this->load->model('Gamecategories_model');
        $this->load->model('Gamescores_model');
        $this->load->model('Gameleaderboard_model');
        $this->load->model('student_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->model('subject_model');
        
        // Get school settings
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    /**
     * Main leaderboard page
     */
    public function index()
    {
        $user_type = $this->getUserType();
        
        $this->session->set_userdata('top_menu', 'Educational Games');
        $this->session->set_userdata('sub_menu', 'leaderboard');
        
        $data['title'] = 'Game Leaderboards';
        $data['user_type'] = $user_type;
        
        // Get filter options
        $data['categories'] = $this->Gamecategories_model->getAllCategories();
        $data['classes'] = $this->class_model->get();
        
        // Get top performers across all games
        $data['top_performers'] = $this->Gameleaderboard_model->getTopPerformersAllGames(10);
        
        // Get recent activities
        $data['recent_activities'] = $this->Gameleaderboard_model->getRecentActivities(10);
        
        // Get game statistics
        $data['game_stats'] = $this->getOverallGameStats();
        
        // Load appropriate view based on user type
        if ($user_type == 'student') {
            $this->load->view('layout/student/header', $data);
            $this->load->view('user/leaderboard/index', $data);
            $this->load->view('layout/student/footer', $data);
        } else {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/leaderboard/index', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    /**
     * Game-specific leaderboard
     */
    public function game($game_id)
    {
        $user_type = $this->getUserType();
        
        // Validate game exists
        $game = $this->Games_model->getGameById($game_id);
        if (!$game || $game['is_active'] != 'yes') {
            show_404();
        }
        
        // Check permissions for students
        if ($user_type == 'student') {
            $student_session_id = $this->customlib->getStudentSessionUserID();
            if (!$this->canStudentViewGame($game_id, $student_session_id)) {
                show_error('Access denied. You do not have permission to view this leaderboard.');
            }
        }
        
        $data['title'] = 'Leaderboard: ' . $game['title'];
        $data['game'] = $game;
        $data['user_type'] = $user_type;
        
        // Get game leaderboard
        $data['leaderboard'] = $this->Gameleaderboard_model->getGameLeaderboard($game_id, 50);
        
        // Get game statistics
        $data['game_stats'] = $this->Games_model->getGameStats($game_id);
        
        // Get detailed analytics
        $data['analytics'] = $this->Gamescores_model->getGameAnalytics($game_id);
        
        // Get performance distribution
        $data['performance_distribution'] = $this->getPerformanceDistribution($game_id);
        
        // If student, get their position and stats
        if ($user_type == 'student') {
            $student_data = $this->customlib->getLoggedInUserData();
            $data['student_position'] = $this->getStudentPosition($game_id, $student_data['student_id']);
            $data['student_stats'] = $this->getStudentGameStats($game_id, $student_data['student_id']);
        }
        
        // Load appropriate view
        if ($user_type == 'student') {
            $this->load->view('layout/student/header', $data);
            $this->load->view('user/leaderboard/game', $data);
            $this->load->view('layout/student/footer', $data);
        } else {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/leaderboard/game', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    /**
     * Class-wise leaderboard
     */
    public function class_leaderboard($class_id = null, $section_id = null)
    {
        $user_type = $this->getUserType();
        
        // For students, use their own class
        if ($user_type == 'student') {
            $student_class = $this->customlib->getStudentCurrentClsSection();
            $class_id = $student_class->class_id;
            $section_id = $student_class->section_id;
        }
        
        if (!$class_id) {
            redirect('leaderboard');
        }
        
        // Get class information
        $class_info = $this->class_model->get($class_id);
        $section_info = null;
        
        if ($section_id) {
            $this->db->where('id', $section_id);
            $section_info = $this->db->get('sections')->row();
        }
        
        $data['title'] = 'Class Leaderboard: ' . $class_info[0]['class'];
        if ($section_info) {
            $data['title'] .= ' - ' . $section_info->section;
        }
        
        $data['class_info'] = $class_info[0];
        $data['section_info'] = $section_info;
        $data['user_type'] = $user_type;
        
        // Get class-specific leaderboard
        $filters = array('class_id' => $class_id);
        if ($section_id) {
            $filters['section_id'] = $section_id;
        }
        
        $data['class_leaderboard'] = $this->Gameleaderboard_model->getClassLeaderboard($filters, 30);
        
        // Get class performance stats
        $data['class_stats'] = $this->getClassPerformanceStats($class_id, $section_id);
        
        // Get top games for this class
        $data['popular_games'] = $this->getPopularGamesForClass($class_id, $section_id);
        
        // Load appropriate view
        if ($user_type == 'student') {
            $this->load->view('layout/student/header', $data);
            $this->load->view('user/leaderboard/class', $data);
            $this->load->view('layout/student/footer', $data);
        } else {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/leaderboard/class', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    /**
     * Subject-wise leaderboard
     */
    public function subject($subject_id)
    {
        $user_type = $this->getUserType();
        
        // Get subject information
        $this->db->where('id', $subject_id);
        $subject = $this->db->get('subjects')->row();
        
        if (!$subject) {
            show_404();
        }
        
        $data['title'] = 'Subject Leaderboard: ' . $subject->name;
        $data['subject'] = $subject;
        $data['user_type'] = $user_type;
        
        // Get subject-specific leaderboard
        $data['subject_leaderboard'] = $this->Gameleaderboard_model->getSubjectLeaderboard($subject_id, 30);
        
        // Get subject performance stats
        $data['subject_stats'] = $this->getSubjectPerformanceStats($subject_id);
        
        // Get games for this subject
        $data['subject_games'] = $this->getGamesForSubject($subject_id);
        
        // Load appropriate view
        if ($user_type == 'student') {
            $this->load->view('layout/student/header', $data);
            $this->load->view('user/leaderboard/subject', $data);
            $this->load->view('layout/student/footer', $data);
        } else {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/leaderboard/subject', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    /**
     * Get leaderboard data via AJAX
     */
    public function ajax_leaderboard()
    {
        $game_id = $this->input->post('game_id');
        $limit = $this->input->post('limit') ? $this->input->post('limit') : 10;
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        
        if ($game_id) {
            $leaderboard = $this->Gameleaderboard_model->getGameLeaderboard($game_id, $limit);
        } elseif ($class_id) {
            $filters = array('class_id' => $class_id);
            if ($section_id) {
                $filters['section_id'] = $section_id;
            }
            $leaderboard = $this->Gameleaderboard_model->getClassLeaderboard($filters, $limit);
        } else {
            $leaderboard = $this->Gameleaderboard_model->getTopPerformersAllGames($limit);
        }
        
        $response = array(
            'status' => 'success',
            'data' => $leaderboard
        );
        
        echo json_encode($response);
    }

    /**
     * Determine user type (student or admin/teacher)
     */
    private function getUserType()
    {
        $user_data = $this->session->userdata();
        
        if (isset($user_data['student'])) {
            return 'student';
        } elseif (isset($user_data['admin'])) {
            return 'admin';
        }
        
        return 'guest';
    }

    /**
     * Check if student can view specific game leaderboard
     */
    private function canStudentViewGame($game_id, $student_session_id)
    {
        $game = $this->Games_model->getGameById($game_id);
        $student_info = $this->customlib->getStudentCurrentClsSection($student_session_id);
        
        return ($game['class_id'] == $student_info->class_id && 
                $game['section_id'] == $student_info->section_id);
    }

    /**
     * Get overall game statistics
     */
    private function getOverallGameStats()
    {
        $stats = array();
        
        // Total active games
        $this->db->where('is_active', 'yes');
        $stats['total_games'] = $this->db->count_all_results('games');
        
        // Total plays
        $this->db->where('is_completed', 1);
        $stats['total_plays'] = $this->db->count_all_results('game_scores');
        
        // Active players (unique students who played)
        $this->db->select('COUNT(DISTINCT student_id) as active_players');
        $this->db->where('is_completed', 1);
        $result = $this->db->get('game_scores')->row();
        $stats['active_players'] = $result->active_players;
        
        // Average score across all games
        $this->db->select('AVG(score) as avg_score');
        $this->db->where('is_completed', 1);
        $result = $this->db->get('game_scores')->row();
        $stats['average_score'] = round($result->avg_score, 2);
        
        return $stats;
    }

    /**
     * Get performance distribution for a specific game
     */
    private function getPerformanceDistribution($game_id)
    {
        $distribution = array(
            'excellent' => 0,  // 90-100%
            'good' => 0,       // 75-89%
            'average' => 0,    // 60-74%
            'below_average' => 0  // <60%
        );
        
        $this->db->select('
            COUNT(CASE WHEN score >= 90 THEN 1 END) as excellent,
            COUNT(CASE WHEN score >= 75 AND score < 90 THEN 1 END) as good,
            COUNT(CASE WHEN score >= 60 AND score < 75 THEN 1 END) as average,
            COUNT(CASE WHEN score < 60 THEN 1 END) as below_average
        ');
        $this->db->where('game_id', $game_id);
        $this->db->where('is_completed', 1);
        $result = $this->db->get('game_scores')->row();
        
        if ($result) {
            $distribution['excellent'] = $result->excellent;
            $distribution['good'] = $result->good;
            $distribution['average'] = $result->average;
            $distribution['below_average'] = $result->below_average;
        }
        
        return $distribution;
    }

    /**
     * Get student's position in game leaderboard
     */
    private function getStudentPosition($game_id, $student_id)
    {
        $this->db->select('gl.*, 
                          (@rank := @rank + 1) as position', false);
        $this->db->from('game_leaderboards gl');
        $this->db->join('(SELECT @rank := 0) r', null, '', false);
        $this->db->where('gl.game_id', $game_id);
        $this->db->order_by('gl.best_score', 'DESC');
        $this->db->order_by('gl.total_attempts', 'ASC');
        
        $results = $this->db->get()->result();
        
        foreach ($results as $index => $result) {
            if ($result->student_id == $student_id) {
                return $index + 1;
            }
        }
        
        return null;
    }

    /**
     * Get student's game-specific statistics
     */
    private function getStudentGameStats($game_id, $student_id)
    {
        $this->db->select('
            COUNT(*) as attempts,
            MAX(score) as best_score,
            AVG(score) as average_score,
            MIN(time_taken) as best_time,
            AVG(time_taken) as average_time
        ');
        $this->db->where('game_id', $game_id);
        $this->db->where('student_id', $student_id);
        $this->db->where('is_completed', 1);
        
        return $this->db->get('game_scores')->row_array();
    }

    /**
     * Get class performance statistics
     */
    private function getClassPerformanceStats($class_id, $section_id = null)
    {
        $this->db->select('
            COUNT(DISTINCT gl.student_id) as active_students,
            AVG(gl.best_score) as class_average,
            COUNT(gl.id) as total_games_played,
            COUNT(CASE WHEN gl.medal_type = "gold" THEN 1 END) as gold_medals,
            COUNT(CASE WHEN gl.medal_type = "silver" THEN 1 END) as silver_medals,
            COUNT(CASE WHEN gl.medal_type = "bronze" THEN 1 END) as bronze_medals
        ');
        $this->db->from('game_leaderboards gl');
        $this->db->join('student_session ss', 'ss.student_id = gl.student_id');
        $this->db->where('ss.class_id', $class_id);
        
        if ($section_id) {
            $this->db->where('ss.section_id', $section_id);
        }
        
        return $this->db->get()->row_array();
    }

    /**
     * Get popular games for a specific class
     */
    private function getPopularGamesForClass($class_id, $section_id = null)
    {
        $this->db->select('g.*, gc.name as category_name, COUNT(gs.id) as play_count');
        $this->db->from('games g');
        $this->db->join('game_categories gc', 'gc.id = g.game_category_id');
        $this->db->join('game_scores gs', 'gs.game_id = g.id', 'left');
        $this->db->where('g.class_id', $class_id);
        
        if ($section_id) {
            $this->db->where('g.section_id', $section_id);
        }
        
        $this->db->where('g.is_active', 'yes');
        $this->db->group_by('g.id');
        $this->db->order_by('play_count', 'DESC');
        $this->db->limit(5);
        
        return $this->db->get()->result_array();
    }

    /**
     * Get subject performance statistics
     */
    private function getSubjectPerformanceStats($subject_id)
    {
        $this->db->select('
            COUNT(DISTINCT g.id) as total_games,
            COUNT(gs.id) as total_plays,
            AVG(gs.score) as average_score,
            COUNT(DISTINCT gs.student_id) as students_participated
        ');
        $this->db->from('games g');
        $this->db->join('game_scores gs', 'gs.game_id = g.id', 'left');
        $this->db->where('g.subject_id', $subject_id);
        $this->db->where('g.is_active', 'yes');
        $this->db->where('gs.is_completed', 1);
        
        return $this->db->get()->row_array();
    }

    /**
     * Get games for a specific subject
     */
    private function getGamesForSubject($subject_id)
    {
        $this->db->select('g.*, gc.name as category_name, c.class, s.section');
        $this->db->from('games g');
        $this->db->join('game_categories gc', 'gc.id = g.game_category_id');
        $this->db->join('classes c', 'c.id = g.class_id');
        $this->db->join('sections s', 's.id = g.section_id');
        $this->db->where('g.subject_id', $subject_id);
        $this->db->where('g.is_active', 'yes');
        $this->db->order_by('g.created_at', 'DESC');
        
        return $this->db->get()->result_array();
    }
}
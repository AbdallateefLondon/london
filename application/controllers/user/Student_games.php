<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Student Games Controller
 * Handles game playing interface for students
 * Following Smart School CodeIgniter V3 patterns
 */
class Student_games extends Student_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models
        $this->load->model('Games_model');
        $this->load->model('Gamecategories_model');
        $this->load->model('Gamescores_model');
        $this->load->model('Gameleaderboard_model');
        
        // Load additional models for student context
        $this->load->model('student_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->model('subject_model');
        
        // Load libraries
        $this->load->library('form_validation');
        
        // Get school settings
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    /**
     * Games dashboard for students - List available games
     */
    public function index()
    {
        $this->session->set_userdata('top_menu', 'Educational Games');
        $this->session->set_userdata('sub_menu', 'user/student_games');
        
        $data['title'] = 'Educational Games';
        
        // Get current student info
        $student_session_id = $this->customlib->getStudentSessionUserID();
        $student_data = $this->customlib->getLoggedInUserData();
        
        // Get available games for student
        $data['games'] = $this->Games_model->getGamesForStudent($student_session_id);
        
        // Get game categories for filter
        $data['categories'] = $this->Gamecategories_model->getAllCategories();
        
        // Get student's recent activities
        $data['recent_activities'] = $this->Gameleaderboard_model->getStudentRecentActivities($student_data['student_id'], 5);
        
        // Get student's favorite games
        $data['favorite_games'] = $this->getFavoriteGames($student_session_id);
        
        // Get achievement stats
        $data['achievement_stats'] = $this->getAchievementStats($student_data['student_id']);
        
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/student_games/index', $data);
        $this->load->view('layout/student/footer', $data);
    }

    /**
     * Show game details and start game interface
     */
    public function play($game_id)
    {
        // Validate game exists and is accessible
        $game = $this->Games_model->getGameById($game_id);
        if (!$game || $game['is_active'] != 'yes') {
            show_404();
        }
        
        // Get current student info
        $student_session_id = $this->customlib->getStudentSessionUserID();
        $student_data = $this->customlib->getLoggedInUserData();
        $student_id = $student_data['student_id'];
        
        // Check if student can play this game (class/section match)
        if (!$this->canStudentAccessGame($game_id, $student_session_id)) {
            $this->session->set_flashdata('error_msg', 'You do not have access to this game.');
            redirect('user/student_games');
        }
        
        // Check attempt limits
        if (!$this->Games_model->canStudentPlayGame($game_id, $student_id)) {
            $this->session->set_flashdata('error_msg', 'You have reached the maximum number of attempts for this game.');
            redirect('user/student_games');
        }
        
        $data['title'] = 'Play Game: ' . $game['title'];
        $data['game'] = $game;
        $data['student_data'] = $student_data;
        
        // Get student's previous attempts
        $data['previous_attempts'] = $this->Gamescores_model->getStudentGameScores($game_id, $student_id);
        
        // Get game leaderboard for motivation
        $data['leaderboard'] = $this->Gameleaderboard_model->getGameLeaderboard($game_id, 5);
        
        $this->load->view('user/student_games/play', $data);
    }

    /**
     * Submit game results and calculate score
     */
    public function submit_game()
    {
        // Validate POST request
        if (!$this->input->post()) {
            show_404();
        }
        
        $this->form_validation->set_rules('game_id', 'Game ID', 'required|numeric');
        $this->form_validation->set_rules('answers', 'Answers', 'required');
        $this->form_validation->set_rules('time_taken', 'Time Taken', 'required|numeric');
        $this->form_validation->set_rules('start_time', 'Start Time', 'required');
        
        if ($this->form_validation->run() == false) {
            $response = array('status' => 'error', 'message' => 'Invalid submission data.');
            echo json_encode($response);
            return;
        }
        
        $game_id = $this->input->post('game_id');
        $answers = json_decode($this->input->post('answers'), true);
        $time_taken = $this->input->post('time_taken');
        $start_time = $this->input->post('start_time');
        
        // Get game details
        $game = $this->Games_model->getGameById($game_id);
        if (!$game) {
            $response = array('status' => 'error', 'message' => 'Game not found.');
            echo json_encode($response);
            return;
        }
        
        // Get student info
        $student_data = $this->customlib->getLoggedInUserData();
        $student_id = $student_data['student_id'];
        $student_session_id = $this->customlib->getStudentSessionUserID();
        
        // Calculate score
        $score_result = $this->calculateGameScore($game, $answers);
        
        // Save game score
        $score_data = array(
            'game_id' => $game_id,
            'student_id' => $student_id,
            'student_session_id' => $student_session_id,
            'score' => $score_result['score'],
            'total_questions' => $score_result['total_questions'],
            'correct_answers' => $score_result['correct_answers'],
            'time_taken' => $time_taken,
            'answers_data' => json_encode($answers),
            'is_completed' => 1,
            'completed_at' => date('Y-m-d H:i:s')
        );
        
        $score_id = $this->Gamescores_model->saveGameScore($score_data);
        
        if ($score_id) {
            // Update leaderboard
            $this->updateLeaderboard($game_id, $student_id, $student_session_id, $score_result['score']);
            
            // Check for achievements/medals
            $achievement = $this->checkAchievements($game_id, $student_id, $score_result['score'], $game['passing_score']);
            
            $response = array(
                'status' => 'success',
                'message' => 'Game completed successfully!',
                'score' => $score_result['score'],
                'total_questions' => $score_result['total_questions'],
                'correct_answers' => $score_result['correct_answers'],
                'passing_score' => $game['passing_score'],
                'passed' => $score_result['score'] >= $game['passing_score'],
                'achievement' => $achievement,
                'time_taken' => $time_taken
            );
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to save game results.');
        }
        
        echo json_encode($response);
    }

    /**
     * Add/remove game from favorites
     */
    public function toggle_favorite()
    {
        $game_id = $this->input->post('game_id');
        $student_session_id = $this->customlib->getStudentSessionUserID();
        
        if (!$game_id || !$student_session_id) {
            $response = array('status' => 'error', 'message' => 'Invalid request.');
            echo json_encode($response);
            return;
        }
        
        // Check if already favorite
        $this->db->where('game_id', $game_id);
        $this->db->where('student_session_id', $student_session_id);
        $existing = $this->db->get('game_favorites')->row();
        
        if ($existing) {
            // Remove from favorites
            $this->db->where('id', $existing->id);
            $this->db->delete('game_favorites');
            $message = 'Removed from favorites';
            $is_favorite = false;
        } else {
            // Add to favorites
            $favorite_data = array(
                'game_id' => $game_id,
                'student_session_id' => $student_session_id,
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->db->insert('game_favorites', $favorite_data);
            $message = 'Added to favorites';
            $is_favorite = true;
        }
        
        $response = array(
            'status' => 'success',
            'message' => $message,
            'is_favorite' => $is_favorite
        );
        echo json_encode($response);
    }

    /**
     * Get student's game history
     */
    public function history()
    {
        $this->session->set_userdata('top_menu', 'Educational Games');
        $this->session->set_userdata('sub_menu', 'user/student_games/history');
        
        $data['title'] = 'Game History';
        
        $student_data = $this->customlib->getLoggedInUserData();
        $student_id = $student_data['student_id'];
        
        // Get all game scores for student
        $data['game_history'] = $this->Gamescores_model->getStudentAllGameScores($student_id);
        
        // Get performance statistics
        $data['performance_stats'] = $this->getStudentPerformanceStats($student_id);
        
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/student_games/history', $data);
        $this->load->view('layout/student/footer', $data);
    }

    /**
     * Calculate game score based on answers
     */
    private function calculateGameScore($game, $student_answers)
    {
        $game_data = json_decode($game['game_data'], true);
        $questions = $game_data['questions'];
        
        $total_questions = count($questions);
        $correct_answers = 0;
        
        foreach ($questions as $index => $question) {
            $question_id = $question['id'] ?? $index;
            $correct_answer = $question['correct_answer'];
            $student_answer = $student_answers[$question_id] ?? null;
            
            if ($student_answer == $correct_answer) {
                $correct_answers++;
            }
        }
        
        // Calculate percentage score
        $score = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100, 2) : 0;
        
        return array(
            'score' => $score,
            'total_questions' => $total_questions,
            'correct_answers' => $correct_answers
        );
    }

    /**
     * Update student leaderboard entry
     */
    private function updateLeaderboard($game_id, $student_id, $student_session_id, $score)
    {
        $leaderboard_data = array(
            'game_id' => $game_id,
            'student_id' => $student_id,
            'student_session_id' => $student_session_id,
            'best_score' => $score,
            'last_played' => date('Y-m-d H:i:s')
        );
        
        $this->Gameleaderboard_model->updateStudentLeaderboard($leaderboard_data);
    }

    /**
     * Check if student can access game based on class/section
     */
    private function canStudentAccessGame($game_id, $student_session_id)
    {
        $game = $this->Games_model->getGameById($game_id);
        $student_info = $this->customlib->getStudentCurrentClsSection($student_session_id);
        
        return ($game['class_id'] == $student_info->class_id && 
                $game['section_id'] == $student_info->section_id);
    }

    /**
     * Check for achievements and medals
     */
    private function checkAchievements($game_id, $student_id, $score, $passing_score)
    {
        $achievement = array('medal' => null, 'message' => '');
        
        if ($score >= 95) {
            $achievement['medal'] = 'gold';
            $achievement['message'] = 'Excellent! You earned a Gold Medal!';
        } elseif ($score >= 85) {
            $achievement['medal'] = 'silver';
            $achievement['message'] = 'Great job! You earned a Silver Medal!';
        } elseif ($score >= $passing_score) {
            $achievement['medal'] = 'bronze';
            $achievement['message'] = 'Well done! You earned a Bronze Medal!';
        }
        
        // Update medal in leaderboard if earned
        if ($achievement['medal']) {
            $this->Gameleaderboard_model->updateStudentMedal($game_id, $student_id, $achievement['medal']);
        }
        
        return $achievement;
    }

    /**
     * Get student's favorite games
     */
    private function getFavoriteGames($student_session_id)
    {
        $this->db->select('g.*, gc.name as category_name, s.name as subject_name');
        $this->db->from('game_favorites gf');
        $this->db->join('games g', 'g.id = gf.game_id');
        $this->db->join('game_categories gc', 'gc.id = g.game_category_id');
        $this->db->join('subjects s', 's.id = g.subject_id');
        $this->db->where('gf.student_session_id', $student_session_id);
        $this->db->where('g.is_active', 'yes');
        $this->db->order_by('gf.created_at', 'DESC');
        $this->db->limit(5);
        
        return $this->db->get()->result_array();
    }

    /**
     * Get student achievement statistics
     */
    private function getAchievementStats($student_id)
    {
        $stats = array(
            'total_games_played' => 0,
            'average_score' => 0,
            'gold_medals' => 0,
            'silver_medals' => 0,
            'bronze_medals' => 0,
            'total_time_played' => 0
        );
        
        // Total games played
        $this->db->where('student_id', $student_id);
        $this->db->where('is_completed', 1);
        $stats['total_games_played'] = $this->db->count_all_results('game_scores');
        
        // Average score
        $this->db->select('AVG(score) as avg_score, SUM(time_taken) as total_time');
        $this->db->where('student_id', $student_id);
        $this->db->where('is_completed', 1);
        $result = $this->db->get('game_scores')->row();
        
        if ($result) {
            $stats['average_score'] = round($result->avg_score, 2);
            $stats['total_time_played'] = $result->total_time;
        }
        
        // Medal counts
        $this->db->select('medal_type, COUNT(*) as count');
        $this->db->where('student_id', $student_id);
        $this->db->where('medal_type IS NOT NULL');
        $this->db->group_by('medal_type');
        $medals = $this->db->get('game_leaderboards')->result();
        
        foreach ($medals as $medal) {
            $stats[$medal->medal_type . '_medals'] = $medal->count;
        }
        
        return $stats;
    }

    /**
     * Get student performance statistics
     */
    private function getStudentPerformanceStats($student_id)
    {
        $this->db->select('
            COUNT(*) as total_attempts,
            AVG(score) as average_score,
            MAX(score) as best_score,
            MIN(score) as lowest_score,
            SUM(time_taken) as total_time,
            COUNT(CASE WHEN score >= 60 THEN 1 END) as passed_games
        ');
        $this->db->where('student_id', $student_id);
        $this->db->where('is_completed', 1);
        $result = $this->db->get('game_scores')->row();
        
        return $result ? (array)$result : array();
    }
}
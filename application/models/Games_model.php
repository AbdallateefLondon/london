<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Games Model
 * Handles all database operations for educational games
 * Follows Smart School CodeIgniter patterns
 */
class Games_model extends MY_Model
{
    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get all games with filters
     * @param array $filters - class_id, section_id, subject_id, category_id, created_by
     * @return array
     */
    public function getAllGames($filters = array())
    {
        $this->db->select('games.*, 
                          game_categories.name as category_name,
                          game_categories.icon as category_icon,
                          classes.class,
                          sections.section,
                          subjects.name as subject_name,
                          staff.name as teacher_name,
                          staff.surname as teacher_surname,
                          COUNT(gs.id) as total_plays,
                          AVG(gs.score) as average_score');
        
        $this->db->from('games');
        $this->db->join('game_categories', 'game_categories.id = games.game_category_id');
        $this->db->join('classes', 'classes.id = games.class_id');
        $this->db->join('sections', 'sections.id = games.section_id');
        $this->db->join('subjects', 'subjects.id = games.subject_id');
        $this->db->join('staff', 'staff.id = games.created_by_staff_id');
        $this->db->join('game_scores gs', 'gs.game_id = games.id', 'left');
        
        // Apply filters
        if (!empty($filters['class_id'])) {
            $this->db->where('games.class_id', $filters['class_id']);
        }
        if (!empty($filters['section_id'])) {
            $this->db->where('games.section_id', $filters['section_id']);
        }
        if (!empty($filters['subject_id'])) {
            $this->db->where('games.subject_id', $filters['subject_id']);
        }
        if (!empty($filters['category_id'])) {
            $this->db->where('games.game_category_id', $filters['category_id']);
        }
        if (!empty($filters['created_by'])) {
            $this->db->where('games.created_by_staff_id', $filters['created_by']);
        }
        
        $this->db->where('games.is_active', 'yes');
        $this->db->group_by('games.id');
        $this->db->order_by('games.created_at', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get single game by ID with all details
     * @param int $game_id
     * @return array|null
     */
    public function getGameById($game_id)
    {
        $this->db->select('games.*, 
                          game_categories.name as category_name,
                          game_categories.icon as category_icon,
                          classes.class,
                          sections.section,
                          subjects.name as subject_name,
                          subjects.code as subject_code,
                          staff.name as teacher_name,
                          staff.surname as teacher_surname,
                          staff.image as teacher_image');
        
        $this->db->from('games');
        $this->db->join('game_categories', 'game_categories.id = games.game_category_id');
        $this->db->join('classes', 'classes.id = games.class_id');
        $this->db->join('sections', 'sections.id = games.section_id');
        $this->db->join('subjects', 'subjects.id = games.subject_id');
        $this->db->join('staff', 'staff.id = games.created_by_staff_id');
        
        $this->db->where('games.id', $game_id);
        $this->db->limit(1);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get games available for a student based on their class/section
     * @param int $student_session_id
     * @return array
     */
    public function getGamesForStudent($student_session_id)
    {
        // First get student's class and section
        $student_info = $this->getStudentClassSection($student_session_id);
        
        if (!$student_info) {
            return array();
        }
        
        $this->db->select('games.*, 
                          game_categories.name as category_name,
                          game_categories.icon as category_icon,
                          subjects.name as subject_name,
                          staff.name as teacher_name,
                          staff.surname as teacher_surname,
                          gl.best_score,
                          gl.total_attempts,
                          gl.medal_type,
                          gf.id as is_favorite');
        
        $this->db->from('games');
        $this->db->join('game_categories', 'game_categories.id = games.game_category_id');
        $this->db->join('subjects', 'subjects.id = games.subject_id');
        $this->db->join('staff', 'staff.id = games.created_by_staff_id');
        $this->db->join('game_leaderboards gl', 'gl.game_id = games.id AND gl.student_session_id = ' . $student_session_id, 'left');
        $this->db->join('game_favorites gf', 'gf.game_id = games.id AND gf.student_session_id = ' . $student_session_id, 'left');
        
        $this->db->where('games.class_id', $student_info['class_id']);
        $this->db->where('games.section_id', $student_info['section_id']);
        $this->db->where('games.is_active', 'yes');
        $this->db->order_by('games.created_at', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get student's class and section info
     * @param int $student_session_id
     * @return array|null
     */
    private function getStudentClassSection($student_session_id)
    {
        $this->db->select('class_id, section_id, student_id');
        $this->db->from('student_session');
        $this->db->where('id', $student_session_id);
        $this->db->where('session_id', $this->current_session);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Create new game
     * @param array $data
     * @return int|bool - insert ID or false
     */
    public function createGame($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        
        // Prepare data
        $game_data = array(
            'title' => $data['title'],
            'description' => $data['description'],
            'game_category_id' => $data['game_category_id'],
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'created_by_staff_id' => $data['created_by_staff_id'],
            'difficulty_level' => $data['difficulty_level'],
            'max_attempts' => $data['max_attempts'],
            'time_limit' => $data['time_limit'],
            'passing_score' => $data['passing_score'],
            'total_questions' => $data['total_questions'],
            'game_data' => $data['game_data'], // JSON string
            'game_image' => isset($data['game_image']) ? $data['game_image'] : null,
            'show_answers' => isset($data['show_answers']) ? $data['show_answers'] : 1,
            'randomize_questions' => isset($data['randomize_questions']) ? $data['randomize_questions'] : 1,
            'is_active' => 'yes',
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->insert('games', $game_data);
        $insert_id = $this->db->insert_id();
        
        // Log the action
        $message = "Game created: " . $data['title'];
        $this->log($message, $insert_id, "Insert");
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    /**
     * Update existing game
     * @param int $game_id
     * @param array $data
     * @return bool
     */
    public function updateGame($game_id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        
        // Prepare update data
        $update_data = array(
            'title' => $data['title'],
            'description' => $data['description'],
            'game_category_id' => $data['game_category_id'],
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'difficulty_level' => $data['difficulty_level'],
            'max_attempts' => $data['max_attempts'],
            'time_limit' => $data['time_limit'],
            'passing_score' => $data['passing_score'],
            'total_questions' => $data['total_questions'],
            'game_data' => $data['game_data'],
            'show_answers' => isset($data['show_answers']) ? $data['show_answers'] : 1,
            'randomize_questions' => isset($data['randomize_questions']) ? $data['randomize_questions'] : 1,
            'updated_at' => date('Y-m-d H:i:s')
        );
        
        // Add image only if provided
        if (isset($data['game_image']) && !empty($data['game_image'])) {
            $update_data['game_image'] = $data['game_image'];
        }
        
        $this->db->where('id', $game_id);
        $this->db->update('games', $update_data);
        
        // Log the action
        $message = "Game updated: " . $data['title'];
        $this->log($message, $game_id, "Update");
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Delete game (soft delete - set inactive)
     * @param int $game_id
     * @return bool
     */
    public function deleteGame($game_id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        
        // Get game title for logging
        $game = $this->getGameById($game_id);
        
        // Soft delete - set as inactive
        $this->db->where('id', $game_id);
        $this->db->update('games', array('is_active' => 'no', 'updated_at' => date('Y-m-d H:i:s')));
        
        // Log the action
        $message = "Game deleted: " . (isset($game['title']) ? $game['title'] : 'Unknown Game');
        $this->log($message, $game_id, "Delete");
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get game statistics
     * @param int $game_id
     * @return array
     */
    public function getGameStats($game_id)
    {
        // Total plays
        $this->db->select('COUNT(*) as total_plays');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $this->db->where('is_completed', 1);
        $total_plays = $this->db->get()->row()->total_plays;

        // Average score
        $this->db->select('AVG(score) as avg_score');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $this->db->where('is_completed', 1);
        $avg_score = $this->db->get()->row()->avg_score;

        // Unique students played
        $this->db->select('COUNT(DISTINCT student_id) as unique_students');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $unique_students = $this->db->get()->row()->unique_students;

        return array(
            'total_plays' => $total_plays,
            'average_score' => round($avg_score, 2),
            'unique_students' => $unique_students
        );
    }

    /**
     * Check if student can play game (attempt limits)
     * @param int $game_id
     * @param int $student_id
     * @return bool
     */
    public function canStudentPlayGame($game_id, $student_id)
    {
        // Get game max attempts
        $game = $this->getGameById($game_id);
        if (!$game) {
            return false;
        }

        // If unlimited attempts (-1), always allow
        if ($game['max_attempts'] == -1) {
            return true;
        }

        // Count student's attempts
        $this->db->select('COUNT(*) as attempts');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $this->db->where('student_id', $student_id);
        $attempts = $this->db->get()->row()->attempts;

        return $attempts < $game['max_attempts'];
    }

    /**
     * Get games by teacher (staff member)
     * @param int $staff_id
     * @return array
     */
    public function getGamesByTeacher($staff_id)
    {
        return $this->getAllGames(array('created_by' => $staff_id));
    }

    /**
     * Search games
     * @param string $search_term
     * @param array $filters
     * @return array
     */
    public function searchGames($search_term, $filters = array())
    {
        $this->db->select('games.*, 
                          game_categories.name as category_name,
                          classes.class,
                          sections.section,
                          subjects.name as subject_name');
        
        $this->db->from('games');
        $this->db->join('game_categories', 'game_categories.id = games.game_category_id');
        $this->db->join('classes', 'classes.id = games.class_id');
        $this->db->join('sections', 'sections.id = games.section_id');
        $this->db->join('subjects', 'subjects.id = games.subject_id');
        
        // Search in title and description
        $this->db->group_start();
        $this->db->like('games.title', $search_term);
        $this->db->or_like('games.description', $search_term);
        $this->db->group_end();
        
        // Apply additional filters
        if (!empty($filters['category_id'])) {
            $this->db->where('games.game_category_id', $filters['category_id']);
        }
        
        $this->db->where('games.is_active', 'yes');
        $this->db->order_by('games.title', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
}
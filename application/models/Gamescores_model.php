<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Game Scores Model
 * Handles student game attempts, scores, and progress tracking
 * Follows Smart School CodeIgniter patterns
 */
class Gamescores_model extends MY_Model
{
    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Start a new game session for student
     * @param array $data
     * @return int|bool - insert ID or false
     */
    public function startGameSession($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        
        // Get next attempt number for this student and game
        $attempt_number = $this->getNextAttemptNumber($data['game_id'], $data['student_id']);
        
        $score_data = array(
            'game_id' => $data['game_id'],
            'student_id' => $data['student_id'],
            'student_session_id' => $data['student_session_id'],
            'attempt_number' => $attempt_number,
            'total_questions' => $data['total_questions'],
            'is_completed' => 0,
            'started_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->insert('game_scores', $score_data);
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    /**
     * Complete game session with results
     * @param int $score_id
     * @param array $data
     * @return bool
     */
    public function completeGameSession($score_id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        
        // Calculate level and experience points
        $level_data = $this->calculateLevelAndExperience($data['score']);
        
        $update_data = array(
            'score' => $data['score'],
            'correct_answers' => $data['correct_answers'],
            'wrong_answers' => $data['wrong_answers'],
            'time_taken' => isset($data['time_taken']) ? $data['time_taken'] : null,
            'is_completed' => 1,
            'is_passed' => $data['score'] >= $data['passing_score'] ? 1 : 0,
            'game_level_achieved' => $level_data['level'],
            'experience_points' => $level_data['experience'],
            'answers_data' => isset($data['answers_data']) ? $data['answers_data'] : null,
            'completed_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->where('id', $score_id);
        $this->db->update('game_scores', $update_data);
        
        // Get the score record to update leaderboard
        $score_record = $this->getScoreById($score_id);
        if ($score_record) {
            $this->updateLeaderboard($score_record);
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get score record by ID
     * @param int $score_id
     * @return array|null
     */
    public function getScoreById($score_id)
    {
        $this->db->select('gs.*, g.title as game_title, s.firstname, s.lastname');
        $this->db->from('game_scores gs');
        $this->db->join('games g', 'g.id = gs.game_id');
        $this->db->join('students s', 's.id = gs.student_id');
        $this->db->where('gs.id', $score_id);
        $this->db->limit(1);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get student's scores for a specific game
     * @param int $game_id
     * @param int $student_id
     * @return array
     */
    public function getStudentGameScores($game_id, $student_id)
    {
        $this->db->select('*');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $this->db->where('student_id', $student_id);
        $this->db->order_by('attempt_number', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get student's best score for a game
     * @param int $game_id
     * @param int $student_id
     * @return array|null
     */
    public function getStudentBestScore($game_id, $student_id)
    {
        $this->db->select('*');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $this->db->where('student_id', $student_id);
        $this->db->where('is_completed', 1);
        $this->db->order_by('score', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get next attempt number for student and game
     * @param int $game_id
     * @param int $student_id
     * @return int
     */
    private function getNextAttemptNumber($game_id, $student_id)
    {
        $this->db->select('MAX(attempt_number) as max_attempt');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $this->db->where('student_id', $student_id);
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result->max_attempt ? $result->max_attempt + 1 : 1;
    }

    /**
     * Calculate level and experience points based on score
     * @param int $score
     * @return array
     */
    private function calculateLevelAndExperience($score)
    {
        // Simple calculation - can be made more complex
        $level = 1;
        $experience = $score;
        
        if ($score >= 90) {
            $level = 5;
            $experience = $score * 2;
        } elseif ($score >= 80) {
            $level = 4;
            $experience = $score * 1.8;
        } elseif ($score >= 70) {
            $level = 3;
            $experience = $score * 1.5;
        } elseif ($score >= 60) {
            $level = 2;
            $experience = $score * 1.2;
        }
        
        return array('level' => $level, 'experience' => round($experience));
    }

    /**
     * Update leaderboard after game completion
     * @param array $score_record
     * @return bool
     */
    private function updateLeaderboard($score_record)
    {
        // Check if student already has a leaderboard entry for this game
        $this->db->select('*');
        $this->db->from('game_leaderboards');
        $this->db->where('game_id', $score_record['game_id']);
        $this->db->where('student_id', $score_record['student_id']);
        $existing = $this->db->get()->row_array();
        
        if ($existing) {
            // Update existing record if this score is better
            if ($score_record['score'] > $existing['best_score']) {
                $update_data = array(
                    'best_score' => $score_record['score'],
                    'best_time' => $score_record['time_taken'],
                    'current_level' => $score_record['game_level_achieved'],
                    'total_experience' => $existing['total_experience'] + $score_record['experience_points'],
                    'last_played' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                
                $this->db->where('id', $existing['id']);
                $this->db->update('game_leaderboards', $update_data);
            }
            
            // Update total attempts
            $this->db->where('id', $existing['id']);
            $this->db->set('total_attempts', 'total_attempts + 1', FALSE);
            $this->db->update('game_leaderboards');
            
        } else {
            // Create new leaderboard entry
            $leaderboard_data = array(
                'game_id' => $score_record['game_id'],
                'student_id' => $score_record['student_id'],
                'student_session_id' => $score_record['student_session_id'],
                'best_score' => $score_record['score'],
                'best_time' => $score_record['time_taken'],
                'total_attempts' => 1,
                'current_level' => $score_record['game_level_achieved'],
                'total_experience' => $score_record['experience_points'],
                'last_played' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            );
            
            $this->db->insert('game_leaderboards', $leaderboard_data);
        }
        
        // Update medal types and rankings
        $this->updateGameRankings($score_record['game_id']);
        
        return true;
    }

    /**
     * Update rankings and medal types for a game
     * @param int $game_id
     * @return bool
     */
    private function updateGameRankings($game_id)
    {
        // Get top performers
        $this->db->select('id, best_score');
        $this->db->from('game_leaderboards');
        $this->db->where('game_id', $game_id);
        $this->db->order_by('best_score', 'DESC');
        $this->db->order_by('best_time', 'ASC');
        
        $query = $this->db->get();
        $leaderboard = $query->result_array();
        
        foreach ($leaderboard as $index => $entry) {
            $rank = $index + 1;
            $medal = 'Participant';
            
            if ($rank == 1) {
                $medal = 'Gold';
            } elseif ($rank == 2) {
                $medal = 'Silver';
            } elseif ($rank == 3) {
                $medal = 'Bronze';
            }
            
            $this->db->where('id', $entry['id']);
            $this->db->update('game_leaderboards', array(
                'rank_position' => $rank,
                'medal_type' => $medal
            ));
        }
        
        return true;
    }

    /**
     * Get game statistics for teacher
     * @param int $game_id
     * @return array
     */
    public function getGameAnalytics($game_id)
    {
        // Total attempts
        $this->db->select('COUNT(*) as total_attempts');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $total_attempts = $this->db->get()->row()->total_attempts;

        // Completed attempts
        $this->db->select('COUNT(*) as completed_attempts');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $this->db->where('is_completed', 1);
        $completed_attempts = $this->db->get()->row()->completed_attempts;

        // Average score
        $this->db->select('AVG(score) as avg_score, MIN(score) as min_score, MAX(score) as max_score');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $this->db->where('is_completed', 1);
        $score_stats = $this->db->get()->row();

        // Pass rate
        $this->db->select('COUNT(*) as passed_attempts');
        $this->db->from('game_scores');
        $this->db->where('game_id', $game_id);
        $this->db->where('is_passed', 1);
        $passed_attempts = $this->db->get()->row()->passed_attempts;

        $pass_rate = $completed_attempts > 0 ? round(($passed_attempts / $completed_attempts) * 100, 2) : 0;

        return array(
            'total_attempts' => $total_attempts,
            'completed_attempts' => $completed_attempts,
            'average_score' => round($score_stats->avg_score, 2),
            'minimum_score' => $score_stats->min_score,
            'maximum_score' => $score_stats->max_score,
            'pass_rate' => $pass_rate,
            'completion_rate' => $total_attempts > 0 ? round(($completed_attempts / $total_attempts) * 100, 2) : 0
        );
    }

    /**
     * Get student progress across all games
     * @param int $student_id
     * @return array
     */
    public function getStudentProgress($student_id)
    {
        $this->db->select('g.title as game_title, gc.name as category_name, gs.score, gs.is_passed, gs.created_at');
        $this->db->from('game_scores gs');
        $this->db->join('games g', 'g.id = gs.game_id');
        $this->db->join('game_categories gc', 'gc.id = g.game_category_id');
        $this->db->where('gs.student_id', $student_id);
        $this->db->where('gs.is_completed', 1);
        $this->db->order_by('gs.created_at', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
}
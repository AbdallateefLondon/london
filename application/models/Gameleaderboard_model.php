<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Game Leaderboard Model
 * Handles leaderboards, rankings, and student achievements
 * Follows Smart School CodeIgniter patterns
 */
class Gameleaderboard_model extends MY_Model
{
    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get top 10 leaderboard for a specific game
     * @param int $game_id
     * @param int $limit (default 10)
     * @return array
     */
    public function getGameLeaderboard($game_id, $limit = 10)
    {
        $this->db->select('gl.*, 
                          s.firstname, 
                          s.lastname, 
                          s.image as student_image,
                          s.admission_no,
                          c.class,
                          sec.section,
                          g.title as game_title');
        
        $this->db->from('game_leaderboards gl');
        $this->db->join('students s', 's.id = gl.student_id');
        $this->db->join('student_session ss', 'ss.id = gl.student_session_id');
        $this->db->join('classes c', 'c.id = ss.class_id');
        $this->db->join('sections sec', 'sec.id = ss.section_id');
        $this->db->join('games g', 'g.id = gl.game_id');
        
        $this->db->where('gl.game_id', $game_id);
        $this->db->where('ss.session_id', $this->current_session);
        $this->db->where('s.is_active', 'yes');
        
        $this->db->order_by('gl.best_score', 'DESC');
        $this->db->order_by('gl.best_time', 'ASC'); // Faster time as tiebreaker
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get student's position in game leaderboard
     * @param int $game_id
     * @param int $student_id
     * @return array|null
     */
    public function getStudentRank($game_id, $student_id)
    {
        $this->db->select('gl.*, 
                          s.firstname, 
                          s.lastname,
                          (@rank := @rank + 1) as current_rank');
        
        $this->db->from('game_leaderboards gl');
        $this->db->join('students s', 's.id = gl.student_id');
        $this->db->join('student_session ss', 'ss.id = gl.student_session_id');
        $this->db->join('(SELECT @rank := 0) r', null, '', false);
        
        $this->db->where('gl.game_id', $game_id);
        $this->db->where('ss.session_id', $this->current_session);
        $this->db->where('s.is_active', 'yes');
        
        $this->db->order_by('gl.best_score', 'DESC');
        $this->db->order_by('gl.best_time', 'ASC');
        
        // Get all results and find the student's position
        $query = $this->db->get();
        $results = $query->result_array();
        
        foreach ($results as $result) {
            if ($result['student_id'] == $student_id) {
                return $result;
            }
        }
        
        return null;
    }

    /**
     * Get overall leaderboard across all games for a class
     * @param int $class_id
     * @param int $section_id
     * @param int $limit
     * @return array
     */
    public function getClassLeaderboard($class_id, $section_id = null, $limit = 20)
    {
        $this->db->select('s.id as student_id,
                          s.firstname, 
                          s.lastname, 
                          s.image as student_image,
                          s.admission_no,
                          SUM(gl.total_experience) as total_experience,
                          COUNT(gl.id) as games_played,
                          AVG(gl.best_score) as average_score,
                          MAX(gl.current_level) as highest_level,
                          COUNT(CASE WHEN gl.medal_type = "Gold" THEN 1 END) as gold_medals,
                          COUNT(CASE WHEN gl.medal_type = "Silver" THEN 1 END) as silver_medals,
                          COUNT(CASE WHEN gl.medal_type = "Bronze" THEN 1 END) as bronze_medals');
        
        $this->db->from('game_leaderboards gl');
        $this->db->join('students s', 's.id = gl.student_id');
        $this->db->join('student_session ss', 'ss.id = gl.student_session_id');
        
        $this->db->where('ss.class_id', $class_id);
        if ($section_id) {
            $this->db->where('ss.section_id', $section_id);
        }
        $this->db->where('ss.session_id', $this->current_session);
        $this->db->where('s.is_active', 'yes');
        
        $this->db->group_by('s.id');
        $this->db->order_by('total_experience', 'DESC');
        $this->db->order_by('average_score', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get school-wide leaderboard (top performers)
     * @param int $limit
     * @return array
     */
    public function getSchoolLeaderboard($limit = 50)
    {
        $this->db->select('s.id as student_id,
                          s.firstname, 
                          s.lastname, 
                          s.image as student_image,
                          s.admission_no,
                          c.class,
                          sec.section,
                          SUM(gl.total_experience) as total_experience,
                          COUNT(gl.id) as games_played,
                          AVG(gl.best_score) as average_score,
                          COUNT(CASE WHEN gl.medal_type = "Gold" THEN 1 END) as gold_medals,
                          COUNT(CASE WHEN gl.medal_type = "Silver" THEN 1 END) as silver_medals,
                          COUNT(CASE WHEN gl.medal_type = "Bronze" THEN 1 END) as bronze_medals');
        
        $this->db->from('game_leaderboards gl');
        $this->db->join('students s', 's.id = gl.student_id');
        $this->db->join('student_session ss', 'ss.id = gl.student_session_id');
        $this->db->join('classes c', 'c.id = ss.class_id');
        $this->db->join('sections sec', 'sec.id = ss.section_id');
        
        $this->db->where('ss.session_id', $this->current_session);
        $this->db->where('s.is_active', 'yes');
        
        $this->db->group_by('s.id');
        $this->db->order_by('total_experience', 'DESC');
        $this->db->order_by('games_played', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get student's achievements and badges
     * @param int $student_id
     * @return array
     */
    public function getStudentAchievements($student_id)
    {
        // Get student's game performances
        $this->db->select('gl.*, 
                          g.title as game_title,
                          gc.name as category_name,
                          gc.icon as category_icon,
                          c.class,
                          sec.section');
        
        $this->db->from('game_leaderboards gl');
        $this->db->join('games g', 'g.id = gl.game_id');
        $this->db->join('game_categories gc', 'gc.id = g.game_category_id');
        $this->db->join('student_session ss', 'ss.id = gl.student_session_id');
        $this->db->join('classes c', 'c.id = ss.class_id');
        $this->db->join('sections sec', 'sec.id = ss.section_id');
        
        $this->db->where('gl.student_id', $student_id);
        $this->db->where('ss.session_id', $this->current_session);
        $this->db->order_by('gl.best_score', 'DESC');
        
        $achievements = $this->db->get()->result_array();

        // Calculate summary statistics
        $total_experience = array_sum(array_column($achievements, 'total_experience'));
        $total_games_played = count($achievements);
        $gold_medals = count(array_filter($achievements, function($a) { return $a['medal_type'] == 'Gold'; }));
        $silver_medals = count(array_filter($achievements, function($a) { return $a['medal_type'] == 'Silver'; }));
        $bronze_medals = count(array_filter($achievements, function($a) { return $a['medal_type'] == 'Bronze'; }));
        $average_score = $total_games_played > 0 ? array_sum(array_column($achievements, 'best_score')) / $total_games_played : 0;

        return array(
            'achievements' => $achievements,
            'summary' => array(
                'total_experience' => $total_experience,
                'total_games_played' => $total_games_played,
                'gold_medals' => $gold_medals,
                'silver_medals' => $silver_medals,
                'bronze_medals' => $bronze_medals,
                'average_score' => round($average_score, 2),
                'highest_level' => $total_games_played > 0 ? max(array_column($achievements, 'current_level')) : 0
            )
        );
    }

    /**
     * Get leaderboard for a specific category
     * @param int $category_id
     * @param int $limit
     * @return array
     */
    public function getCategoryLeaderboard($category_id, $limit = 10)
    {
        $this->db->select('s.id as student_id,
                          s.firstname, 
                          s.lastname, 
                          s.image as student_image,
                          c.class,
                          sec.section,
                          SUM(gl.total_experience) as category_experience,
                          COUNT(gl.id) as category_games_played,
                          AVG(gl.best_score) as category_average_score,
                          COUNT(CASE WHEN gl.medal_type = "Gold" THEN 1 END) as gold_medals');
        
        $this->db->from('game_leaderboards gl');
        $this->db->join('games g', 'g.id = gl.game_id');
        $this->db->join('students s', 's.id = gl.student_id');
        $this->db->join('student_session ss', 'ss.id = gl.student_session_id');
        $this->db->join('classes c', 'c.id = ss.class_id');
        $this->db->join('sections sec', 'sec.id = ss.section_id');
        
        $this->db->where('g.game_category_id', $category_id);
        $this->db->where('ss.session_id', $this->current_session);
        $this->db->where('s.is_active', 'yes');
        
        $this->db->group_by('s.id');
        $this->db->order_by('category_experience', 'DESC');
        $this->db->order_by('category_average_score', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get recent activities (who played what recently)
     * @param int $limit
     * @param int $class_id (optional filter)
     * @return array
     */
    public function getRecentActivities($limit = 20, $class_id = null)
    {
        $this->db->select('gl.last_played,
                          gl.best_score,
                          gl.medal_type,
                          s.firstname,
                          s.lastname,
                          g.title as game_title,
                          gc.name as category_name,
                          gc.icon as category_icon,
                          c.class,
                          sec.section');
        
        $this->db->from('game_leaderboards gl');
        $this->db->join('students s', 's.id = gl.student_id');
        $this->db->join('games g', 'g.id = gl.game_id');
        $this->db->join('game_categories gc', 'gc.id = g.game_category_id');
        $this->db->join('student_session ss', 'ss.id = gl.student_session_id');
        $this->db->join('classes c', 'c.id = ss.class_id');
        $this->db->join('sections sec', 'sec.id = ss.section_id');
        
        if ($class_id) {
            $this->db->where('ss.class_id', $class_id);
        }
        $this->db->where('ss.session_id', $this->current_session);
        $this->db->where('s.is_active', 'yes');
        
        $this->db->order_by('gl.last_played', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get game performance comparison between students
     * @param int $game_id
     * @return array
     */
    public function getGamePerformanceStats($game_id)
    {
        // Score distribution
        $this->db->select('
            COUNT(CASE WHEN best_score >= 90 THEN 1 END) as excellent,
            COUNT(CASE WHEN best_score >= 80 AND best_score < 90 THEN 1 END) as good,
            COUNT(CASE WHEN best_score >= 70 AND best_score < 80 THEN 1 END) as average,
            COUNT(CASE WHEN best_score >= 60 AND best_score < 70 THEN 1 END) as below_average,
            COUNT(CASE WHEN best_score < 60 THEN 1 END) as poor,
            AVG(best_score) as overall_average,
            COUNT(*) as total_students
        ');
        
        $this->db->from('game_leaderboards');
        $this->db->where('game_id', $game_id);
        
        $stats = $this->db->get()->row_array();

        // Medal distribution
        $this->db->select('medal_type, COUNT(*) as count');
        $this->db->from('game_leaderboards');
        $this->db->where('game_id', $game_id);
        $this->db->group_by('medal_type');
        
        $medal_stats = $this->db->get()->result_array();
        
        return array(
            'score_distribution' => $stats,
            'medal_distribution' => $medal_stats
        );
    }

    /**
     * Update all rankings for a game (call after score changes)
     * @param int $game_id
     * @return bool
     */
    public function updateGameRankings($game_id)
    {
        // Reset all rankings first
        $this->db->where('game_id', $game_id);
        $this->db->update('game_leaderboards', array('rank_position' => null));
        
        // Get sorted leaderboard
        $this->db->select('id');
        $this->db->from('game_leaderboards');
        $this->db->where('game_id', $game_id);
        $this->db->order_by('best_score', 'DESC');
        $this->db->order_by('best_time', 'ASC');
        
        $leaderboard = $this->db->get()->result_array();
        
        // Update rankings
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
     * Get top performers this week
     * @param int $limit
     * @return array
     */
    public function getWeeklyTopPerformers($limit = 10)
    {
        $week_start = date('Y-m-d', strtotime('monday this week'));
        
        $this->db->select('s.firstname, s.lastname, s.image as student_image,
                          c.class, sec.section,
                          COUNT(gl.id) as games_played_this_week,
                          SUM(gl.total_experience) as weekly_experience');
        
        $this->db->from('game_leaderboards gl');
        $this->db->join('students s', 's.id = gl.student_id');
        $this->db->join('student_session ss', 'ss.id = gl.student_session_id');
        $this->db->join('classes c', 'c.id = ss.class_id');
        $this->db->join('sections sec', 'sec.id = ss.section_id');
        
        $this->db->where('gl.last_played >=', $week_start);
        $this->db->where('ss.session_id', $this->current_session);
        $this->db->where('s.is_active', 'yes');
        
        $this->db->group_by('s.id');
        $this->db->order_by('games_played_this_week', 'DESC');
        $this->db->order_by('weekly_experience', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get student's favorite games (most played)
     * @param int $student_id
     * @param int $limit
     * @return array
     */
    public function getStudentFavoriteGames($student_id, $limit = 5)
    {
        $this->db->select('gl.*, g.title as game_title, gc.name as category_name, gc.icon as category_icon');
        $this->db->from('game_leaderboards gl');
        $this->db->join('games g', 'g.id = gl.game_id');
        $this->db->join('game_categories gc', 'gc.id = g.game_category_id');
        
        $this->db->where('gl.student_id', $student_id);
        $this->db->order_by('gl.total_attempts', 'DESC');
        $this->db->order_by('gl.best_score', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }
}
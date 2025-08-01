<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Game Categories Model
 * Handles the 7 types of educational games
 * Follows Smart School CodeIgniter patterns
 */
class Gamecategories_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all active game categories
     * @return array
     */
    public function getAllCategories()
    {
        $this->db->select('*');
        $this->db->from('game_categories');
        $this->db->where('is_active', 'yes');
        $this->db->order_by('name', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get category by ID
     * @param int $category_id
     * @return array|null
     */
    public function getCategoryById($category_id)
    {
        $this->db->select('*');
        $this->db->from('game_categories');
        $this->db->where('id', $category_id);
        $this->db->where('is_active', 'yes');
        $this->db->limit(1);
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get category with game count
     * @return array
     */
    public function getCategoriesWithGameCount()
    {
        $this->db->select('gc.*, COUNT(g.id) as game_count');
        $this->db->from('game_categories gc');
        $this->db->join('games g', 'g.game_category_id = gc.id AND g.is_active = \'yes\'', 'left');
        $this->db->where('gc.is_active', 'yes');
        $this->db->group_by('gc.id');
        $this->db->order_by('gc.name', 'ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Add new category (for super admin)
     * @param array $data
     * @return int|bool
     */
    public function addCategory($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        
        $category_data = array(
            'name' => $data['name'],
            'description' => $data['description'],
            'icon' => isset($data['icon']) ? $data['icon'] : 'fa-gamepad',
            'is_active' => 'yes',
            'created_at' => date('Y-m-d H:i:s')
        );
        
        $this->db->insert('game_categories', $category_data);
        $insert_id = $this->db->insert_id();
        
        // Log the action
        $message = "Game category created: " . $data['name'];
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
     * Update category
     * @param int $category_id
     * @param array $data
     * @return bool
     */
    public function updateCategory($category_id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        
        $update_data = array(
            'name' => $data['name'],
            'description' => $data['description'],
            'icon' => isset($data['icon']) ? $data['icon'] : 'fa-gamepad'
        );
        
        $this->db->where('id', $category_id);
        $this->db->update('game_categories', $update_data);
        
        // Log the action
        $message = "Game category updated: " . $data['name'];
        $this->log($message, $category_id, "Update");
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get category statistics
     * @param int $category_id
     * @return array
     */
    public function getCategoryStats($category_id)
    {
        // Total games in this category
        $this->db->select('COUNT(*) as total_games');
        $this->db->from('games');
        $this->db->where('game_category_id', $category_id);
        $this->db->where('is_active', 'yes');
        $total_games = $this->db->get()->row()->total_games;

        // Total plays in this category
        $this->db->select('COUNT(*) as total_plays');
        $this->db->from('game_scores gs');
        $this->db->join('games g', 'g.id = gs.game_id');
        $this->db->where('g.game_category_id', $category_id);
        $this->db->where('gs.is_completed', 1);
        $this->db->where('g.is_active', 'yes');
        $total_plays = $this->db->get()->row()->total_plays;

        return array(
            'total_games' => $total_games,
            'total_plays' => $total_plays
        );
    }

    /**
     * Get popular categories (most played)
     * @param int $limit
     * @return array
     */
    public function getPopularCategories($limit = 5)
    {
        $this->db->select('gc.*, COUNT(gs.id) as play_count');
        $this->db->from('game_categories gc');
        $this->db->join('games g', 'g.game_category_id = gc.id AND g.is_active = \'yes\'');
        $this->db->join('game_scores gs', 'gs.game_id = g.id AND gs.is_completed = 1', 'left');
        $this->db->where('gc.is_active', 'yes');
        $this->db->group_by('gc.id');
        $this->db->order_by('play_count', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result_array();
    }
}
<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Game Types Controller
 * Handles the 7 specific educational game types
 * Math Quiz, Word Completion, Memory Match, List Sorting, True/False, Picture Puzzle, Vocabulary Builder
 */
class Game_types extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models and libraries
        $this->load->model('Games_model');
        $this->load->model('Gamecategories_model');
        $this->load->library('session');
    }

    /**
     * Get game type specific interface
     */
    public function get_game_interface()
    {
        $game_type = $this->input->post('game_type');
        $total_questions = $this->input->post('total_questions', TRUE) ?: 5;
        
        $interface_html = '';
        
        switch ($game_type) {
            case 'Math Quiz':
                $interface_html = $this->generateMathQuizInterface($total_questions);
                break;
            case 'Word Completion':
                $interface_html = $this->generateWordCompletionInterface($total_questions);
                break;
            case 'Memory Match':
                $interface_html = $this->generateMemoryMatchInterface($total_questions);
                break;
            case 'List Sorting':
                $interface_html = $this->generateListSortingInterface($total_questions);
                break;
            case 'True False Challenge':
                $interface_html = $this->generateTrueFalseInterface($total_questions);
                break;
            case 'Picture Puzzle':
                $interface_html = $this->generatePicturePuzzleInterface($total_questions);
                break;
            case 'Vocabulary Builder':
                $interface_html = $this->generateVocabularyInterface($total_questions);
                break;
            default:
                $interface_html = '<div class="alert alert-warning">Unknown game type selected.</div>';
        }
        
        echo json_encode(array(
            'status' => 'success',
            'html' => $interface_html,
            'game_type' => $game_type
        ));
    }

    /**
     * Generate Math Quiz Interface
     */
    private function generateMathQuizInterface($total_questions)
    {
        $html = '<div class="game-type-container math-quiz">';
        $html .= '<h4><i class="fa fa-calculator"></i> Math Quiz Generator</h4>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Math Operation Type</label>';
        $html .= '<select class="form-control" id="math-operation">';
        $html .= '<option value="addition">Addition (+)</option>';
        $html .= '<option value="subtraction">Subtraction (-)</option>';
        $html .= '<option value="multiplication">Multiplication (×)</option>';
        $html .= '<option value="division">Division (÷)</option>';
        $html .= '<option value="mixed">Mixed Operations</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Difficulty Range</label>';
        $html .= '<select class="form-control" id="math-difficulty">';
        $html .= '<option value="1-10">Numbers 1-10 (Easy)</option>';
        $html .= '<option value="1-50">Numbers 1-50 (Medium)</option>';
        $html .= '<option value="1-100">Numbers 1-100 (Hard)</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<button type="button" class="btn btn-primary" onclick="generateMathQuestions(' . $total_questions . ')">Generate Math Questions</button>';
        $html .= '<div id="math-questions-container" style="margin-top: 20px;"></div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Generate Word Completion Interface
     */
    private function generateWordCompletionInterface($total_questions)
    {
        $html = '<div class="game-type-container word-completion">';
        $html .= '<h4><i class="fa fa-font"></i> Word Completion Game</h4>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Word Category</label>';
        $html .= '<select class="form-control" id="word-category">';
        $html .= '<option value="animals">Animals</option>';
        $html .= '<option value="colors">Colors</option>';
        $html .= '<option value="food">Food Items</option>';
        $html .= '<option value="countries">Countries</option>';
        $html .= '<option value="science">Science Terms</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Completion Type</label>';
        $html .= '<select class="form-control" id="completion-type">';
        $html .= '<option value="missing-letters">Missing Letters</option>';
        $html .= '<option value="scrambled">Unscramble Words</option>';
        $html .= '<option value="fill-blanks">Fill in the Blanks</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<button type="button" class="btn btn-primary" onclick="generateWordQuestions(' . $total_questions . ')">Generate Word Questions</button>';
        $html .= '<div id="word-questions-container" style="margin-top: 20px;"></div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Generate Memory Match Interface
     */
    private function generateMemoryMatchInterface($total_questions)
    {
        $html = '<div class="game-type-container memory-match">';
        $html .= '<h4><i class="fa fa-th-large"></i> Memory Match Game</h4>';
        $html .= '<div class="alert alert-info">';
        $html .= '<i class="fa fa-info-circle"></i> Memory Match games will show pairs of cards that students need to match.';
        $html .= '</div>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Match Type</label>';
        $html .= '<select class="form-control" id="memory-type">';
        $html .= '<option value="numbers">Numbers & Words</option>';
        $html .= '<option value="images">Images & Names</option>';
        $html .= '<option value="equations">Math Equations</option>';
        $html .= '<option value="definitions">Terms & Definitions</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Grid Size</label>';
        $html .= '<select class="form-control" id="grid-size">';
        $html .= '<option value="2x3">2×3 (6 cards)</option>';
        $html .= '<option value="3x4">3×4 (12 cards)</option>';
        $html .= '<option value="4x4">4×4 (16 cards)</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<button type="button" class="btn btn-primary" onclick="generateMemoryQuestions(' . $total_questions . ')">Generate Memory Game</button>';
        $html .= '<div id="memory-questions-container" style="margin-top: 20px;"></div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Generate List Sorting Interface  
     */
    private function generateListSortingInterface($total_questions)
    {
        $html = '<div class="game-type-container list-sorting">';
        $html .= '<h4><i class="fa fa-sort"></i> List Sorting Game</h4>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Sorting Category</label>';
        $html .= '<select class="form-control" id="sorting-category">';
        $html .= '<option value="alphabetical">Alphabetical Order</option>';
        $html .= '<option value="numerical">Numerical Order</option>';
        $html .= '<option value="chronological">Chronological Order</option>';
        $html .= '<option value="size">Size Order</option>';
        $html .= '<option value="matching">Category Matching</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>List Length</label>';
        $html .= '<select class="form-control" id="list-length">';
        $html .= '<option value="4">4 items</option>';
        $html .= '<option value="6">6 items</option>';
        $html .= '<option value="8">8 items</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<button type="button" class="btn btn-primary" onclick="generateSortingQuestions(' . $total_questions . ')">Generate Sorting Questions</button>';
        $html .= '<div id="sorting-questions-container" style="margin-top: 20px;"></div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Generate True/False Interface
     */
    private function generateTrueFalseInterface($total_questions)
    {
        $html = '<div class="game-type-container true-false">';
        $html .= '<h4><i class="fa fa-check-circle"></i> True/False Challenge</h4>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Subject Area</label>';
        $html .= '<select class="form-control" id="tf-subject">';
        $html .= '<option value="general">General Knowledge</option>';
        $html .= '<option value="science">Science Facts</option>';
        $html .= '<option value="history">History</option>';
        $html .= '<option value="geography">Geography</option>';
        $html .= '<option value="math">Math Concepts</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Statement Type</label>';
        $html .= '<select class="form-control" id="statement-type">';
        $html .= '<option value="facts">Factual Statements</option>';
        $html .= '<option value="calculations">Simple Calculations</option>';
        $html .= '<option value="definitions">Definitions</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<button type="button" class="btn btn-primary" onclick="generateTrueFalseQuestions(' . $total_questions . ')">Generate True/False Questions</button>';
        $html .= '<div id="tf-questions-container" style="margin-top: 20px;"></div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Generate Picture Puzzle Interface
     */
    private function generatePicturePuzzleInterface($total_questions)
    {
        $html = '<div class="game-type-container picture-puzzle">';
        $html .= '<h4><i class="fa fa-puzzle-piece"></i> Picture Puzzle Game</h4>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Puzzle Type</label>';
        $html .= '<select class="form-control" id="puzzle-type">';
        $html .= '<option value="identify">Identify the Object</option>';
        $html .= '<option value="missing-piece">Find Missing Piece</option>';
        $html .= '<option value="sequence">Picture Sequence</option>';
        $html .= '<option value="spot-difference">Spot the Difference</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Image Category</label>';
        $html .= '<select class="form-control" id="image-category">';
        $html .= '<option value="animals">Animals</option>';
        $html .= '<option value="objects">Common Objects</option>';
        $html .= '<option value="shapes">Shapes & Patterns</option>';
        $html .= '<option value="nature">Nature</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '<label>Upload Images (Optional)</label>';
        $html .= '<input type="file" class="form-control" id="puzzle-images" multiple accept="image/*">';
        $html .= '<small class="text-muted">You can upload custom images for the puzzle, or use default images.</small>';
        $html .= '</div>';
        $html .= '<button type="button" class="btn btn-primary" onclick="generatePuzzleQuestions(' . $total_questions . ')">Generate Picture Puzzles</button>';
        $html .= '<div id="puzzle-questions-container" style="margin-top: 20px;"></div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Generate Vocabulary Builder Interface
     */
    private function generateVocabularyInterface($total_questions)
    {
        $html = '<div class="game-type-container vocabulary">';
        $html .= '<h4><i class="fa fa-book"></i> Vocabulary Builder</h4>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Vocabulary Level</label>';
        $html .= '<select class="form-control" id="vocab-level">';
        $html .= '<option value="beginner">Beginner (Grade 1-3)</option>';
        $html .= '<option value="intermediate">Intermediate (Grade 4-6)</option>';
        $html .= '<option value="advanced">Advanced (Grade 7+)</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="form-group">';
        $html .= '<label>Question Type</label>';
        $html .= '<select class="form-control" id="vocab-type">';
        $html .= '<option value="definitions">Match Word to Definition</option>';
        $html .= '<option value="synonyms">Find Synonyms</option>';
        $html .= '<option value="antonyms">Find Antonyms</option>';
        $html .= '<option value="context">Use in Context</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<div class="form-group">';
        $html .= '<label>Word Category</label>';
        $html .= '<select class="form-control" id="word-list">';
        $html .= '<option value="academic">Academic Vocabulary</option>';
        $html .= '<option value="everyday">Everyday Words</option>';
        $html .= '<option value="subject-specific">Subject Specific</option>';
        $html .= '<option value="custom">Custom Word List</option>';
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div id="custom-words-section" style="display: none; margin-bottom: 15px;">';
        $html .= '<label>Custom Words (one per line)</label>';
        $html .= '<textarea class="form-control" id="custom-words" rows="5" placeholder="Enter words, one per line..."></textarea>';
        $html .= '</div>';
        $html .= '<button type="button" class="btn btn-primary" onclick="generateVocabQuestions(' . $total_questions . ')">Generate Vocabulary Questions</button>';
        $html .= '<div id="vocab-questions-container" style="margin-top: 20px;"></div>';
        $html .= '</div>';
        
        return $html;
    }
}
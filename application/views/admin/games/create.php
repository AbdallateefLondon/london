<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_game'); ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-gamepad"></i> <?php echo $this->lang->line('create_game'); ?></h3>
                    </div>

                    <form id="game-form" action="<?php echo base_url('admin/games/store'); ?>" method="post" enctype="multipart/form-data">
                        <?php echo $this->customlib->getCSRF(); ?>
                        
                        <div class="box-body">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php echo $this->session->flashdata('msg'); ?>
                            <?php } ?>

                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Basic Information -->
                                    <div class="form-group">
                                        <label for="title">Game Title <span class="req">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title" 
                                               value="<?php echo set_value('title'); ?>" placeholder="Enter game title">
                                        <span class="text-danger"><?php echo form_error('title'); ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description <span class="req">*</span></label>
                                        <textarea class="form-control" name="description" id="description" rows="3" 
                                                  placeholder="Enter game description"><?php echo set_value('description'); ?></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>

                                    <!-- Game Configuration -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="game_category_id">Game Category <span class="req">*</span></label>
                                                <select class="form-control" name="game_category_id" id="game_category_id">
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($categories as $category) { ?>
                                                        <option value="<?php echo $category['id']; ?>" 
                                                                <?php echo set_select('game_category_id', $category['id']); ?>>
                                                            <?php echo $category['name']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('game_category_id'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="difficulty_level">Difficulty Level <span class="req">*</span></label>
                                                <select class="form-control" name="difficulty_level" id="difficulty_level">
                                                    <option value="Easy" <?php echo set_select('difficulty_level', 'Easy'); ?>>Easy</option>
                                                    <option value="Medium" <?php echo set_select('difficulty_level', 'Medium'); ?>>Medium</option>
                                                    <option value="Hard" <?php echo set_select('difficulty_level', 'Hard'); ?>>Hard</option>
                                                    <option value="Expert" <?php echo set_select('difficulty_level', 'Expert'); ?>>Expert</option>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('difficulty_level'); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Class and Subject Selection -->
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="class_id">Class <span class="req">*</span></label>
                                                <select class="form-control" name="class_id" id="class_id">
                                                    <option value="">Select Class</option>
                                                    <?php foreach ($classlist as $class) { ?>
                                                        <option value="<?php echo $class['id']; ?>" 
                                                                <?php echo set_select('class_id', $class['id']); ?>>
                                                            <?php echo $class['class']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="section_id">Section <span class="req">*</span></label>
                                                <select class="form-control" name="section_id" id="section_id">
                                                    <option value="">First Select Class</option>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="subject_id">Subject <span class="req">*</span></label>
                                                <select class="form-control" name="subject_id" id="subject_id">
                                                    <option value="">Select Subject</option>
                                                    <?php foreach ($subjectlist as $subject) { ?>
                                                        <option value="<?php echo $subject['id']; ?>" 
                                                                <?php echo set_select('subject_id', $subject['id']); ?>>
                                                            <?php echo $subject['name']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <span class="text-danger"><?php echo form_error('subject_id'); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Game Settings -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="total_questions">Total Questions <span class="req">*</span></label>
                                                <input type="number" class="form-control" name="total_questions" id="total_questions" 
                                                       value="<?php echo set_value('total_questions', '10'); ?>" min="1" max="100">
                                                <span class="text-danger"><?php echo form_error('total_questions'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="time_limit">Time Limit (minutes) <span class="req">*</span></label>
                                                <input type="number" class="form-control" name="time_limit" id="time_limit" 
                                                       value="<?php echo set_value('time_limit', '5'); ?>" min="1" max="120">
                                                <span class="text-danger"><?php echo form_error('time_limit'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="passing_score">Passing Score (%) <span class="req">*</span></label>
                                                <input type="number" class="form-control" name="passing_score" id="passing_score" 
                                                       value="<?php echo set_value('passing_score', '60'); ?>" min="1" max="100">
                                                <span class="text-danger"><?php echo form_error('passing_score'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="max_attempts">Max Attempts</label>
                                                <input type="number" class="form-control" name="max_attempts" id="max_attempts" 
                                                       value="<?php echo set_value('max_attempts', '3'); ?>" min="-1">
                                                <small class="text-muted">-1 for unlimited attempts</small>
                                                <span class="text-danger"><?php echo form_error('max_attempts'); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Game Options -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="show_answers" value="1" 
                                                           <?php echo set_checkbox('show_answers', '1', true); ?>>
                                                    Show correct answers after game
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="randomize_questions" value="1" 
                                                           <?php echo set_checkbox('randomize_questions', '1', true); ?>>
                                                    Randomize question order
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Game Image -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="game_image">Game Image</label>
                                        <input type="file" class="form-control" name="game_image" id="game_image" accept="image/*">
                                        <small class="text-muted">Upload an image for the game (max 2MB)</small>
                                        <span class="text-danger"><?php echo form_error('game_image'); ?></span>
                                    </div>
                                    
                                    <div id="image-preview" style="display: none;">
                                        <img id="preview-img" src="" class="img-responsive" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Game Questions Section -->
                            <hr>
                            <h4><i class="fa fa-question-circle"></i> Game Questions</h4>
                            
                            <div id="questions-container">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> Questions will be generated based on the selected game category. 
                                    Click "Add Questions" after selecting a category.
                                </div>
                            </div>

                            <div class="text-center" style="margin: 20px 0;">
                                <button type="button" class="btn btn-info" id="add-questions-btn" disabled>
                                    <i class="fa fa-plus"></i> Add Questions
                                </button>
                            </div>

                            <input type="hidden" name="game_data" id="game_data" value="">
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                <i class="fa fa-check"></i> Create Game
                            </button>
                            <a href="<?php echo base_url('admin/games'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Load sections when class is selected
    $('#class_id').on('change', function() {
        var class_id = $(this).val();
        if (class_id) {
            $.ajax({
                url: '<?php echo base_url("admin/games/getSections"); ?>',
                type: 'POST',
                data: {
                    class_id: class_id,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                success: function(response) {
                    $('#section_id').html(response);
                }
            });
        } else {
            $('#section_id').html('<option value="">First Select Class</option>');
        }
    });

    // Enable questions button when category is selected
    $('#game_category_id').on('change', function() {
        if ($(this).val()) {
            $('#add-questions-btn').prop('disabled', false);
        } else {
            $('#add-questions-btn').prop('disabled', true);
            $('#questions-container').html('<div class="alert alert-info"><i class="fa fa-info-circle"></i> Questions will be generated based on the selected game category. Click "Add Questions" after selecting a category.</div>');
            $('#game_data').val('');
            $('#submit-btn').prop('disabled', true);
        }
    });

    // Image preview
    $('#game_image').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').hide();
        }
    });

    // Add questions functionality
    $('#add-questions-btn').on('click', function() {
        var category_id = $('#game_category_id').val();
        var total_questions = $('#total_questions').val();
        
        if (!category_id || !total_questions) {
            alert('Please select a category and specify total questions');
            return;
        }
        
        generateQuestionForm(category_id, total_questions);
    });

    // Update total questions
    $('#total_questions').on('change', function() {
        if ($('#game_category_id').val()) {
            $('#add-questions-btn').trigger('click');
        }
    });

    function generateQuestionForm(category_id, total_questions) {
        var questionsHtml = '<div class="panel panel-default">';
        questionsHtml += '<div class="panel-heading"><strong>Game Questions (' + total_questions + ' questions)</strong></div>';
        questionsHtml += '<div class="panel-body">';
        
        for (var i = 1; i <= total_questions; i++) {
            questionsHtml += '<div class="question-item" data-question="' + i + '">';
            questionsHtml += '<h5>Question ' + i + '</h5>';
            questionsHtml += '<div class="row">';
            questionsHtml += '<div class="col-md-8">';
            questionsHtml += '<div class="form-group">';
            questionsHtml += '<label>Question Text <span class="req">*</span></label>';
            questionsHtml += '<input type="text" class="form-control question-text" placeholder="Enter question text" required>';
            questionsHtml += '</div>';
            questionsHtml += '</div>';
            questionsHtml += '<div class="col-md-4">';
            questionsHtml += '<div class="form-group">';
            questionsHtml += '<label>Question Type</label>';
            questionsHtml += '<select class="form-control question-type">';
            questionsHtml += '<option value="multiple_choice">Multiple Choice</option>';
            questionsHtml += '<option value="true_false">True/False</option>';
            questionsHtml += '</select>';
            questionsHtml += '</div>';
            questionsHtml += '</div>';
            questionsHtml += '</div>';
            
            // Answer options container
            questionsHtml += '<div class="answer-options-container">';
            questionsHtml += generateAnswerOptions('multiple_choice');
            questionsHtml += '</div>';
            
            questionsHtml += '<hr>';
            questionsHtml += '</div>';
        }
        
        questionsHtml += '</div>';
        questionsHtml += '</panel>';
        
        $('#questions-container').html(questionsHtml);
        
        // Enable submit button
        $('#submit-btn').prop('disabled', false);
        
        // Bind question type change event
        $('.question-type').on('change', function() {
            var container = $(this).closest('.question-item').find('.answer-options-container');
            container.html(generateAnswerOptions($(this).val()));
        });
    }

    function generateAnswerOptions(type) {
        var optionsHtml = '';
        
        if (type === 'multiple_choice') {
            optionsHtml += '<div class="row">';
            for (var i = 0; i < 4; i++) {
                var letter = String.fromCharCode(65 + i);
                optionsHtml += '<div class="col-md-6">';
                optionsHtml += '<div class="form-group">';
                optionsHtml += '<label>Option ' + letter + '</label>';
                optionsHtml += '<div class="input-group">';
                optionsHtml += '<span class="input-group-addon">';
                optionsHtml += '<input type="radio" name="correct_answer_' + Math.random() + '" value="' + i + '" ' + (i === 0 ? 'checked' : '') + '>';
                optionsHtml += '</span>';
                optionsHtml += '<input type="text" class="form-control option-text" placeholder="Enter option ' + letter + '" required>';
                optionsHtml += '</div>';
                optionsHtml += '</div>';
                optionsHtml += '</div>';
            }
            optionsHtml += '</div>';
        } else if (type === 'true_false') {
            optionsHtml += '<div class="form-group">';
            optionsHtml += '<label>Correct Answer</label>';
            optionsHtml += '<div>';
            optionsHtml += '<label class="radio-inline">';
            optionsHtml += '<input type="radio" name="tf_answer_' + Math.random() + '" value="true" checked> True';
            optionsHtml += '</label>';
            optionsHtml += '<label class="radio-inline">';
            optionsHtml += '<input type="radio" name="tf_answer_' + Math.random() + '" value="false"> False';
            optionsHtml += '</label>';
            optionsHtml += '</div>';
            optionsHtml += '</div>';
        }
        
        return optionsHtml;
    }

    // Form submission
    $('#game-form').on('submit', function(e) {
        e.preventDefault();
        
        var gameData = {
            questions: []
        };
        
        $('.question-item').each(function(index) {
            var questionText = $(this).find('.question-text').val();
            var questionType = $(this).find('.question-type').val();
            
            if (!questionText.trim()) {
                alert('Please fill in all question texts');
                return false;
            }
            
            var question = {
                id: index + 1,
                question: questionText,
                type: questionType
            };
            
            if (questionType === 'multiple_choice') {
                var options = [];
                var correctAnswer = 0;
                
                $(this).find('.option-text').each(function(optIndex) {
                    options.push($(this).val());
                });
                
                var checkedRadio = $(this).find('input[type="radio"]:checked');
                if (checkedRadio.length) {
                    correctAnswer = parseInt(checkedRadio.val());
                }
                
                question.options = options;
                question.correct_answer = correctAnswer;
            } else if (questionType === 'true_false') {
                var tfAnswer = $(this).find('input[name*="tf_answer"]:checked').val();
                question.correct_answer = tfAnswer;
            }
            
            gameData.questions.push(question);
        });
        
        $('#game_data').val(JSON.stringify(gameData));
        
        // Submit form
        this.submit();
    });
});
</script>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $game['title']; ?> - Educational Games</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/iCheck/flat/blue.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/morris/morris.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/datepicker/datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/daterangepicker/daterangepicker-bs3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    
    <style>
        .game-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .question-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
        }
        
        .question-number {
            background: #3c8dbc;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            display: inline-block;
            margin-right: 10px;
        }
        
        .answer-option {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .answer-option:hover {
            background-color: #f5f5f5;
        }
        
        .answer-option.selected {
            background-color: #3c8dbc;
            color: white;
            border-color: #3c8dbc;
        }
        
        .timer {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #d9534f;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            z-index: 1000;
        }
        
        .progress-bar-container {
            margin-bottom: 20px;
        }
        
        .game-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        
        .navigation-buttons {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn-nav {
            margin: 0 10px;
        }
    </style>
</head>

<body class="hold-transition skin-blue layout-top-nav">
    <div class="wrapper">
        <header class="main-header">
            <nav class="navbar navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <a href="<?php echo base_url('user/student_games'); ?>" class="navbar-brand">
                            <i class="fa fa-gamepad"></i> Educational Games
                        </a>
                    </div>
                    
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li>
                                <a href="<?php echo base_url('user/student_games'); ?>">
                                    <i class="fa fa-arrow-left"></i> Back to Games
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <div class="content-wrapper">
            <div class="container">
                <!-- Timer -->
                <div class="timer" id="timer">
                    <i class="fa fa-clock-o"></i> <span id="time-remaining"><?php echo $game['time_limit']; ?>:00</span>
                </div>

                <div class="game-container">
                    <!-- Game Header -->
                    <div class="game-header">
                        <h2><?php echo $game['title']; ?></h2>
                        <p><?php echo $game['description']; ?></p>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Questions:</strong> <?php echo $game['total_questions']; ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Time:</strong> <?php echo $game['time_limit']; ?> minutes
                            </div>
                            <div class="col-md-3">
                                <strong>Passing Score:</strong> <?php echo $game['passing_score']; ?>%
                            </div>
                            <div class="col-md-3">
                                <strong>Difficulty:</strong> 
                                <span class="label label-<?php echo $game['difficulty_level'] == 'Easy' ? 'success' : ($game['difficulty_level'] == 'Medium' ? 'warning' : 'danger'); ?>">
                                    <?php echo $game['difficulty_level']; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress-bar-container">
                        <div class="progress">
                            <div class="progress-bar progress-bar-info" role="progressbar" 
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" id="progress-bar">
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                        <p class="text-center">Question <span id="current-question">1</span> of <?php echo $game['total_questions']; ?></p>
                    </div>

                    <!-- Game Form -->
                    <form id="game-form">
                        <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
                        <input type="hidden" name="start_time" value="<?php echo date('Y-m-d H:i:s'); ?>">
                        
                        <!-- Questions will be loaded here via JavaScript -->
                        <div id="questions-container"></div>
                        
                        <!-- Navigation -->
                        <div class="navigation-buttons">
                            <button type="button" class="btn btn-default btn-nav" id="prev-btn" style="display: none;">
                                <i class="fa fa-arrow-left"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-nav" id="next-btn">
                                Next <i class="fa fa-arrow-right"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-nav" id="submit-btn" style="display: none;">
                                <i class="fa fa-check"></i> Submit Game
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div class="modal fade" id="result-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Game Results</h4>
                </div>
                <div class="modal-body" id="result-content">
                    <!-- Results will be loaded here -->
                </div>
                <div class="modal-footer">
                    <a href="<?php echo base_url('user/student_games'); ?>" class="btn btn-primary">Back to Games</a>
                    <a href="<?php echo base_url('leaderboard/game/' . $game['id']); ?>" class="btn btn-info">View Leaderboard</a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>backend/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/plugins/fastclick/fastclick.js"></script>
    <script src="<?php echo base_url(); ?>backend/dist/js/app.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/dist/js/demo.js"></script>

    <script>
        var gameData = <?php echo $game['game_data']; ?>;
        var timeLimit = <?php echo $game['time_limit']; ?> * 60; // Convert to seconds
        var currentQuestion = 0;
        var answers = {};
        var startTime = Date.now();
        var timerInterval;

        $(document).ready(function() {
            loadQuestion(currentQuestion);
            startTimer();
            
            // Navigation buttons
            $('#next-btn').on('click', function() {
                if (currentQuestion < gameData.questions.length - 1) {
                    currentQuestion++;
                    loadQuestion(currentQuestion);
                    updateProgress();
                }
            });
            
            $('#prev-btn').on('click', function() {
                if (currentQuestion > 0) {
                    currentQuestion--;
                    loadQuestion(currentQuestion);
                    updateProgress();
                }
            });
            
            $('#submit-btn').on('click', function() {
                submitGame();
            });
            
            // Answer selection
            $(document).on('click', '.answer-option', function() {
                var questionId = $(this).data('question-id');
                var answerValue = $(this).data('answer-value');
                
                // Remove previous selection
                $('.answer-option[data-question-id="' + questionId + '"]').removeClass('selected');
                
                // Select current option
                $(this).addClass('selected');
                
                // Save answer
                answers[questionId] = answerValue;
            });
        });

        function loadQuestion(index) {
            var question = gameData.questions[index];
            var questionHtml = '<div class="question-card">';
            questionHtml += '<h4><span class="question-number">' + (index + 1) + '</span>' + question.question + '</h4>';
            
            if (question.image) {
                questionHtml += '<img src="' + question.image + '" class="img-responsive" style="max-height: 200px; margin: 10px 0;">';
            }
            
            // Generate answer options based on question type
            if (question.type === 'multiple_choice') {
                question.options.forEach(function(option, optionIndex) {
                    var isSelected = answers[question.id] == optionIndex ? 'selected' : '';
                    questionHtml += '<div class="answer-option ' + isSelected + '" data-question-id="' + question.id + '" data-answer-value="' + optionIndex + '">';
                    questionHtml += '<strong>' + String.fromCharCode(65 + optionIndex) + '.</strong> ' + option;
                    questionHtml += '</div>';
                });
            } else if (question.type === 'true_false') {
                var trueSelected = answers[question.id] == 'true' ? 'selected' : '';
                var falseSelected = answers[question.id] == 'false' ? 'selected' : '';
                
                questionHtml += '<div class="answer-option ' + trueSelected + '" data-question-id="' + question.id + '" data-answer-value="true">';
                questionHtml += '<strong>True</strong>';
                questionHtml += '</div>';
                
                questionHtml += '<div class="answer-option ' + falseSelected + '" data-question-id="' + question.id + '" data-answer-value="false">';
                questionHtml += '<strong>False</strong>';
                questionHtml += '</div>';
            }
            
            questionHtml += '</div>';
            
            $('#questions-container').html(questionHtml);
            $('#current-question').text(index + 1);
            
            // Update navigation buttons
            if (index === 0) {
                $('#prev-btn').hide();
            } else {
                $('#prev-btn').show();
            }
            
            if (index === gameData.questions.length - 1) {
                $('#next-btn').hide();
                $('#submit-btn').show();
            } else {
                $('#next-btn').show();
                $('#submit-btn').hide();
            }
            
            updateProgress();
        }

        function updateProgress() {
            var progress = ((currentQuestion + 1) / gameData.questions.length) * 100;
            $('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
        }

        function startTimer() {
            timerInterval = setInterval(function() {
                timeLimit--;
                
                var minutes = Math.floor(timeLimit / 60);
                var seconds = timeLimit % 60;
                
                $('#time-remaining').text(
                    (minutes < 10 ? '0' : '') + minutes + ':' + 
                    (seconds < 10 ? '0' : '') + seconds
                );
                
                if (timeLimit <= 0) {
                    clearInterval(timerInterval);
                    submitGame();
                }
            }, 1000);
        }

        function submitGame() {
            clearInterval(timerInterval);
            
            var timeTaken = Math.round((Date.now() - startTime) / 1000);
            
            $.ajax({
                url: '<?php echo base_url("user/student_games/submit_game"); ?>',
                type: 'POST',
                data: {
                    game_id: <?php echo $game['id']; ?>,
                    answers: JSON.stringify(answers),
                    time_taken: timeTaken,
                    start_time: '<?php echo date('Y-m-d H:i:s'); ?>',
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    showResults(response);
                },
                error: function() {
                    alert('An error occurred while submitting the game. Please try again.');
                }
            });
        }

        function showResults(result) {
            var resultHtml = '<div class="text-center">';
            
            if (result.passed) {
                resultHtml += '<div class="alert alert-success">';
                resultHtml += '<h3><i class="fa fa-check-circle"></i> Congratulations!</h3>';
                resultHtml += '<p>You passed the game!</p>';
            } else {
                resultHtml += '<div class="alert alert-warning">';
                resultHtml += '<h3><i class="fa fa-exclamation-triangle"></i> Good Try!</h3>';
                resultHtml += '<p>Keep practicing to improve your score!</p>';
            }
            resultHtml += '</div>';
            
            resultHtml += '<div class="row">';
            resultHtml += '<div class="col-md-6"><h4>Your Score: <span class="text-primary">' + result.score + '%</span></h4></div>';
            resultHtml += '<div class="col-md-6"><h4>Passing Score: <span class="text-info">' + result.passing_score + '%</span></h4></div>';
            resultHtml += '</div>';
            
            resultHtml += '<div class="row">';
            resultHtml += '<div class="col-md-6"><p>Correct Answers: <strong>' + result.correct_answers + '/' + result.total_questions + '</strong></p></div>';
            resultHtml += '<div class="col-md-6"><p>Time Taken: <strong>' + Math.floor(result.time_taken / 60) + ':' + (result.time_taken % 60).toString().padStart(2, '0') + '</strong></p></div>';
            resultHtml += '</div>';
            
            if (result.achievement && result.achievement.medal) {
                resultHtml += '<div class="alert alert-info">';
                resultHtml += '<h4><i class="fa fa-trophy"></i> ' + result.achievement.message + '</h4>';
                resultHtml += '</div>';
            }
            
            resultHtml += '</div>';
            
            $('#result-content').html(resultHtml);
            $('#result-modal').modal('show');
        }
    </script>
</body>
</html>
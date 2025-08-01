<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Preview: <?php echo $game['title']; ?> - Educational Games</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/AdminLTE.min.css">
    
    <style>
        body {
            background-color: #f4f4f4;
        }
        
        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 20px;
        }
        
        .game-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .question-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .question-number {
            background: #3c8dbc;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            text-align: center;
            line-height: 35px;
            display: inline-block;
            margin-right: 15px;
            font-weight: bold;
        }
        
        .answer-option {
            margin: 12px 0;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafafa;
        }
        
        .answer-option:hover {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }
        
        .answer-option.selected {
            background-color: #3c8dbc;
            color: white;
            border-color: #3c8dbc;
        }
        
        .answer-option.correct {
            background-color: #5cb85c;
            color: white;
            border-color: #4cae4c;
        }
        
        .answer-option.incorrect {
            background-color: #d9534f;
            color: white;
            border-color: #d43f3a;
        }
        
        .timer {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #d9534f;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .progress-container {
            margin-bottom: 30px;
        }
        
        .navigation-buttons {
            text-align: center;
            margin-top: 40px;
        }
        
        .btn-nav {
            margin: 0 15px;
            padding: 12px 30px;
            font-size: 16px;
        }
        
        .preview-banner {
            background: #f39c12;
            color: white;
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="preview-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><i class="fa fa-gamepad"></i> Game Preview Mode</h1>
                    <p class="lead">Testing interface for: <strong><?php echo $game['title']; ?></strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="preview-banner">
            <i class="fa fa-info-circle"></i> <strong>PREVIEW MODE:</strong> This is a test view for teachers. Scores will not be saved.
        </div>

        <!-- Timer -->
        <div class="timer" id="timer">
            <i class="fa fa-clock-o"></i> <span id="time-remaining"><?php echo $game['time_limit']; ?>:00</span>
        </div>

        <div class="game-container">
            <!-- Game Header -->
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-info-circle"></i> Game Information</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h3><?php echo $game['title']; ?></h3>
                            <p class="lead"><?php echo $game['description']; ?></p>
                            <div class="row">
                                <div class="col-sm-3">
                                    <strong>Category:</strong><br>
                                    <i class="<?php echo $game['category_icon']; ?>"></i> <?php echo $game['category_name']; ?>
                                </div>
                                <div class="col-sm-3">
                                    <strong>Questions:</strong><br>
                                    <?php echo $game['total_questions']; ?> questions
                                </div>
                                <div class="col-sm-3">
                                    <strong>Time Limit:</strong><br>
                                    <?php echo $game['time_limit']; ?> minutes
                                </div>
                                <div class="col-sm-3">
                                    <strong>Passing Score:</strong><br>
                                    <?php echo $game['passing_score']; ?>%
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <?php if (!empty($game['game_image'])) { ?>
                                <img src="<?php echo base_url('uploads/games/' . $game['game_image']); ?>" 
                                     class="img-responsive img-thumbnail">
                            <?php } else { ?>
                                <div class="text-center text-muted">
                                    <i class="fa fa-gamepad" style="font-size: 80px;"></i>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="progress">
                            <div class="progress-bar progress-bar-info" role="progressbar" 
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" id="progress-bar">
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                        <p class="text-center">Question <span id="current-question">1</span> of <?php echo $game['total_questions']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Questions Container -->
            <div id="questions-container">
                <!-- Questions will be loaded here via JavaScript -->
            </div>
            
            <!-- Navigation Buttons -->
            <div class="navigation-buttons">
                <button type="button" class="btn btn-default btn-nav" id="prev-btn" style="display: none;">
                    <i class="fa fa-arrow-left"></i> Previous
                </button>
                <button type="button" class="btn btn-primary btn-nav" id="next-btn">
                    Next <i class="fa fa-arrow-right"></i>
                </button>
                <button type="button" class="btn btn-success btn-nav" id="finish-btn" style="display: none;">
                    <i class="fa fa-check"></i> Finish Preview
                </button>
            </div>
        </div>
    </div>

    <!-- Results Modal -->
    <div class="modal fade" id="results-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-trophy"></i> Preview Results</h4>
                </div>
                <div class="modal-body" id="results-content">
                    <!-- Results will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close Preview</button>
                    <a href="<?php echo base_url('admin/games/view/' . $game['id']); ?>" class="btn btn-primary">
                        <i class="fa fa-eye"></i> View Game Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>backend/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/bootstrap/js/bootstrap.min.js"></script>

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
            
            $('#finish-btn').on('click', function() {
                finishPreview();
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
                
                // Show feedback in preview mode
                showAnswerFeedback(questionId);
            });
        });

        function loadQuestion(index) {
            var question = gameData.questions[index];
            var questionHtml = '<div class="question-card">';
            questionHtml += '<h4><span class="question-number">' + (index + 1) + '</span>' + question.question + '</h4>';
            
            if (question.image) {
                questionHtml += '<img src="' + question.image + '" class="img-responsive center-block" style="max-height: 300px; margin: 20px 0;">';
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
                questionHtml += '<i class="fa fa-check-circle"></i> <strong>True</strong>';
                questionHtml += '</div>';
                
                questionHtml += '<div class="answer-option ' + falseSelected + '" data-question-id="' + question.id + '" data-answer-value="false">';
                questionHtml += '<i class="fa fa-times-circle"></i> <strong>False</strong>';
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
                $('#finish-btn').show();
            } else {
                $('#next-btn').show();
                $('#finish-btn').hide();
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
                
                // Change color when time is running out
                if (timeLimit <= 60) {
                    $('.timer').addClass('animated pulse');
                }
                
                if (timeLimit <= 0) {
                    clearInterval(timerInterval);
                    finishPreview();
                }
            }, 1000);
        }

        function showAnswerFeedback(questionId) {
            var question = gameData.questions.find(q => q.id == questionId);
            var correctAnswer = question.correct_answer;
            var studentAnswer = answers[questionId];
            
            // Show correct/incorrect feedback
            $('.answer-option[data-question-id="' + questionId + '"]').each(function() {
                var optionValue = $(this).data('answer-value');
                
                if (optionValue == correctAnswer) {
                    $(this).addClass('correct');
                } else if (optionValue == studentAnswer && studentAnswer != correctAnswer) {
                    $(this).addClass('incorrect');
                }
            });
        }

        function finishPreview() {
            clearInterval(timerInterval);
            
            var timeTaken = Math.round((Date.now() - startTime) / 1000);
            var score = calculateScore();
            
            showResults(score, timeTaken);
        }

        function calculateScore() {
            var totalQuestions = gameData.questions.length;
            var correctAnswers = 0;
            
            gameData.questions.forEach(function(question) {
                var studentAnswer = answers[question.id];
                if (studentAnswer == question.correct_answer) {
                    correctAnswers++;
                }
            });
            
            return {
                score: Math.round((correctAnswers / totalQuestions) * 100),
                correct: correctAnswers,
                total: totalQuestions
            };
        }

        function showResults(scoreData, timeTaken) {
            var resultHtml = '<div class="text-center">';
            
            // Preview mode banner
            resultHtml += '<div class="alert alert-warning">';
            resultHtml += '<h4><i class="fa fa-info-circle"></i> Preview Mode Results</h4>';
            resultHtml += '<p>These results are for testing purposes only and will not be saved.</p>';
            resultHtml += '</div>';
            
            // Score display
            if (scoreData.score >= <?php echo $game['passing_score']; ?>) {
                resultHtml += '<div class="alert alert-success">';
                resultHtml += '<h3><i class="fa fa-check-circle"></i> Would Pass!</h3>';
                resultHtml += '<p>Students would pass this game with this performance.</p>';
            } else {
                resultHtml += '<div class="alert alert-info">';
                resultHtml += '<h3><i class="fa fa-info-circle"></i> Would Need More Practice</h3>';
                resultHtml += '<p>Students would need to improve to pass this game.</p>';
            }
            resultHtml += '</div>';
            
            // Statistics
            resultHtml += '<div class="row">';
            resultHtml += '<div class="col-md-6"><h4>Score: <span class="text-primary">' + scoreData.score + '%</span></h4></div>';
            resultHtml += '<div class="col-md-6"><h4>Passing Score: <span class="text-info">' + <?php echo $game['passing_score']; ?> + '%</span></h4></div>';
            resultHtml += '</div>';
            
            resultHtml += '<div class="row">';
            resultHtml += '<div class="col-md-6"><p>Correct Answers: <strong>' + scoreData.correct + '/' + scoreData.total + '</strong></p></div>';
            resultHtml += '<div class="col-md-6"><p>Time Taken: <strong>' + Math.floor(timeTaken / 60) + ':' + (timeTaken % 60).toString().padStart(2, '0') + '</strong></p></div>';
            resultHtml += '</div>';
            
            // Game assessment
            resultHtml += '<hr>';
            resultHtml += '<h4>Game Assessment:</h4>';
            resultHtml += '<div class="row">';
            resultHtml += '<div class="col-md-4"><div class="info-box bg-blue"><span class="info-box-icon"><i class="fa fa-question"></i></span><div class="info-box-content"><span class="info-box-text">Questions</span><span class="info-box-number">' + scoreData.total + '</span></div></div></div>';
            resultHtml += '<div class="col-md-4"><div class="info-box bg-green"><span class="info-box-icon"><i class="fa fa-clock-o"></i></span><div class="info-box-content"><span class="info-box-text">Time Limit</span><span class="info-box-number">' + <?php echo $game['time_limit']; ?> + ' min</span></div></div></div>';
            resultHtml += '<div class="col-md-4"><div class="info-box bg-yellow"><span class="info-box-icon"><i class="fa fa-trophy"></i></span><div class="info-box-content"><span class="info-box-text">Difficulty</span><span class="info-box-number">' + '<?php echo $game['difficulty_level']; ?>' + '</span></div></div></div>';
            resultHtml += '</div>';
            
            resultHtml += '</div>';
            
            $('#results-content').html(resultHtml);
            $('#results-modal').modal('show');
        }
    </script>
</body>
</html>
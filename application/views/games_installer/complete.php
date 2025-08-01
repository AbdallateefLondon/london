<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/font-awesome/css/font-awesome.min.css">
    <style>
        body { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); }
        .installer-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .installer-header {
            background: #27ae60;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .installer-content {
            padding: 40px;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #27ae60;
            color: white;
            margin: 0 5px;
            border-radius: 5px;
        }
        .success-icon {
            font-size: 5em;
            color: #27ae60;
            margin: 20px 0;
        }
        .btn-success-large {
            background: #27ae60;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            margin: 10px;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-success-large:hover {
            background: #229954;
            color: white;
            text-decoration: none;
        }
        .feature-checklist {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .feature-checklist li {
            margin: 10px 0;
            padding: 5px 0;
        }
        .celebration {
            text-align: center;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #ffeaa7);
            background-size: 300% 300%;
            animation: gradient 3s ease infinite;
            padding: 30px;
            border-radius: 10px;
            margin: 20px 0;
            color: white;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        .quick-link-card {
            text-align: center;
            padding: 20px;
            border: 2px solid #27ae60;
            border-radius: 8px;
            transition: all 0.3s;
            background: white;
        }
        .quick-link-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1><i class="fa fa-check-circle"></i> Installation Complete!</h1>
            <p class="lead">Educational Games System is ready to use</p>
        </div>
        
        <div class="installer-content">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step">
                    <i class="fa fa-check"></i><br>
                    <small>Welcome</small>
                </div>
                <div class="step">
                    <i class="fa fa-check"></i><br>
                    <small>Requirements</small>
                </div>
                <div class="step">
                    <i class="fa fa-check"></i><br>
                    <small>Database</small>
                </div>
                <div class="step">
                    <i class="fa fa-check"></i><br>
                    <small>Complete</small>
                </div>
            </div>

            <?php if ($success): ?>
                <!-- Success Content -->
                <div class="celebration">
                    <div class="success-icon">
                        <i class="fa fa-trophy"></i>
                    </div>
                    <h2>🎉 Congratulations! 🎉</h2>
                    <p class="lead">Your Educational Games System has been successfully installed!</p>
                </div>

                <!-- What Was Installed -->
                <div class="feature-checklist">
                    <h4><i class="fa fa-check-square"></i> What Was Installed:</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="fa fa-check text-success"></i> ✅ 7 Database tables created</li>
                                <li><i class="fa fa-check text-success"></i> ✅ Game categories initialized</li>
                                <li><i class="fa fa-check text-success"></i> ✅ Sidebar menus added</li>
                                <li><i class="fa fa-check text-success"></i> ✅ Permissions configured</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="fa fa-check text-success"></i> ✅ File upload directories created</li>
                                <li><i class="fa fa-check text-success"></i> ✅ Default settings applied</li>
                                <li><i class="fa fa-check text-success"></i> ✅ Super Admin permissions granted</li>
                                <li><i class="fa fa-check text-success"></i> ✅ System ready for use</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Quick Access Links -->
                <div class="quick-links">
                    <div class="quick-link-card">
                        <i class="fa fa-cog fa-2x text-primary"></i>
                        <h4>Admin Panel</h4>
                        <p>Manage games, categories, and settings</p>
                        <a href="<?php echo $admin_url; ?>" class="btn btn-primary btn-sm">
                            <i class="fa fa-external-link"></i> Access Admin
                        </a>
                    </div>
                    <div class="quick-link-card">
                        <i class="fa fa-gamepad fa-2x text-info"></i>
                        <h4>Student Games</h4>
                        <p>Play educational games and track progress</p>
                        <a href="<?php echo $student_url; ?>" class="btn btn-info btn-sm">
                            <i class="fa fa-play"></i> View Games
                        </a>
                    </div>
                    <div class="quick-link-card">
                        <i class="fa fa-trophy fa-2x text-warning"></i>
                        <h4>Leaderboards</h4>
                        <p>View rankings and achievements</p>
                        <a href="<?php echo $leaderboard_url; ?>" class="btn btn-warning btn-sm">
                            <i class="fa fa-trophy"></i> View Rankings
                        </a>
                    </div>
                </div>

                <!-- Getting Started Guide -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4><i class="fa fa-rocket"></i> Getting Started</h4>
                    </div>
                    <div class="panel-body">
                        <h5>Next Steps:</h5>
                        <ol>
                            <li><strong>Create Your First Game</strong>
                                <ul>
                                    <li>Go to Admin → Educational Games → Create Game</li>
                                    <li>Choose a game type (Math Quiz, Word Completion, etc.)</li>
                                    <li>Select class, subject, and difficulty level</li>
                                    <li>Generate questions and save</li>
                                </ul>
                            </li>
                            <li><strong>Test Student Experience</strong>
                                <ul>
                                    <li>Login as a student account</li>
                                    <li>Navigate to Educational Games section</li>
                                    <li>Play the game you created</li>
                                    <li>Check leaderboard updates</li>
                                </ul>
                            </li>
                            <li><strong>Configure Settings</strong>
                                <ul>
                                    <li>Review game categories</li>
                                    <li>Set up class-specific permissions</li>
                                    <li>Customize scoring and medal systems</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>

                <!-- Available Game Types -->
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4><i class="fa fa-list"></i> Available Game Types</h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6><i class="fa fa-calculator"></i> Math Quiz</h6>
                                <p><small>Mathematical problems and arithmetic challenges</small></p>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fa fa-font"></i> Word Completion</h6>
                                <p><small>Complete missing letters and unscramble words</small></p>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fa fa-th-large"></i> Memory Match</h6>
                                <p><small>Memory card matching games</small></p>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fa fa-sort"></i> List Sorting</h6>
                                <p><small>Drag and drop sorting activities</small></p>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fa fa-check-circle"></i> True/False Challenge</h6>
                                <p><small>Quick decision-making questions</small></p>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fa fa-puzzle-piece"></i> Picture Puzzle</h6>
                                <p><small>Visual puzzles and problem-solving</small></p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fa fa-book"></i> Vocabulary Builder</h6>
                                <p><small>Word definitions and language development</small></p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fa fa-trophy"></i> Leaderboard System</h6>
                                <p><small>Rankings, medals, and achievements</small></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center" style="margin-top: 30px;">
                    <a href="<?php echo $admin_url; ?>" class="btn-success-large">
                        <i class="fa fa-cog"></i> Go to Admin Panel
                    </a>
                    <a href="<?php echo $student_url; ?>" class="btn-success-large">
                        <i class="fa fa-gamepad"></i> View Games
                    </a>
                    <a href="<?php echo base_url('admin'); ?>" class="btn-success-large">
                        <i class="fa fa-home"></i> Main Dashboard
                    </a>
                </div>

            <?php else: ?>
                <!-- Error Content -->
                <div class="alert alert-danger text-center">
                    <h2><i class="fa fa-exclamation-triangle"></i> Installation Failed</h2>
                    <p class="lead"><?php echo $message; ?></p>
                    <div style="margin-top: 20px;">
                        <a href="<?php echo base_url('games_installer'); ?>" class="btn btn-danger">
                            <i class="fa fa-refresh"></i> Restart Installation
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Support Information -->
            <div class="alert alert-info">
                <h5><i class="fa fa-question-circle"></i> Need Help?</h5>
                <p>If you encounter any issues or need assistance:</p>
                <ul>
                    <li>Check the <strong>Complete Installation Guide</strong> for detailed instructions</li>
                    <li>Review the <strong>Troubleshooting</strong> section for common issues</li>
                    <li>Contact your system administrator or hosting provider</li>
                    <li>Access the <strong>uninstaller</strong> if you need to remove the system</li>
                </ul>
                <div class="text-center" style="margin-top: 15px;">
                    <a href="<?php echo base_url('games_installer/uninstall'); ?>" class="btn btn-outline-danger btn-sm">
                        <i class="fa fa-trash"></i> Uninstall System
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>backend/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/bootstrap/js/bootstrap.min.js"></script>
    <script>
        // Celebration effects
        $(document).ready(function() {
            // Add some confetti or celebration animations here if desired
            console.log('🎉 Educational Games System installed successfully! 🎉');
        });
    </script>
</body>
</html>
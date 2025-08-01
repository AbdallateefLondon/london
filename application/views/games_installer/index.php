<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/font-awesome/css/font-awesome.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .installer-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .installer-header {
            background: #2c3e50;
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
            background: #ecf0f1;
            margin: 0 5px;
            border-radius: 5px;
            position: relative;
        }
        .step.active {
            background: #3498db;
            color: white;
        }
        .step.completed {
            background: #27ae60;
            color: white;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .feature-card {
            text-align: center;
            padding: 20px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .feature-card:hover {
            border-color: #3498db;
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 3em;
            color: #3498db;
            margin-bottom: 15px;
        }
        .btn-installer {
            background: #3498db;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn-installer:hover {
            background: #2980b9;
            color: white;
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1><i class="fa fa-gamepad"></i> Educational Games System</h1>
            <p class="lead">Web-Based Installation Wizard</p>
        </div>
        
        <div class="installer-content">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active">
                    <i class="fa fa-home"></i><br>
                    <small>Welcome</small>
                </div>
                <div class="step">
                    <i class="fa fa-check-circle"></i><br>
                    <small>Requirements</small>
                </div>
                <div class="step">
                    <i class="fa fa-database"></i><br>
                    <small>Database</small>
                </div>
                <div class="step">
                    <i class="fa fa-flag-checkered"></i><br>
                    <small>Complete</small>
                </div>
            </div>

            <!-- Welcome Content -->
            <div class="text-center">
                <h2>Welcome to Educational Games Installation</h2>
                <p class="lead">Transform your Smart School with 7 interactive educational game types!</p>
            </div>

            <!-- Features Grid -->
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">🧮</div>
                    <h4>Math Quiz</h4>
                    <p>Interactive mathematical problems and arithmetic challenges</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h4>Word Completion</h4>
                    <p>Complete missing letters and unscramble words</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🃏</div>
                    <h4>Memory Match</h4>
                    <p>Memory card matching games for cognitive development</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📋</div>
                    <h4>List Sorting</h4>
                    <p>Drag and drop sorting activities</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">✅</div>
                    <h4>True/False Challenge</h4>
                    <p>Quick decision-making questions</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🧩</div>
                    <h4>Picture Puzzle</h4>
                    <p>Visual puzzles and problem-solving activities</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📚</div>
                    <h4>Vocabulary Builder</h4>
                    <p>Word definitions and language development</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🏆</div>
                    <h4>Leaderboards</h4>
                    <p>Competitive rankings and achievement tracking</p>
                </div>
            </div>

            <!-- Installation Information -->
            <div class="alert alert-info">
                <h4><i class="fa fa-info-circle"></i> What This Installer Will Do:</h4>
                <ul>
                    <li>✅ Check system requirements and compatibility</li>
                    <li>✅ Create all necessary database tables</li>
                    <li>✅ Set up menu items and permissions</li>
                    <li>✅ Configure file upload directories</li>
                    <li>✅ Initialize default game categories</li>
                    <li>✅ Verify installation integrity</li>
                </ul>
            </div>

            <div class="alert alert-warning">
                <h4><i class="fa fa-exclamation-triangle"></i> Before You Begin:</h4>
                <ul>
                    <li><strong>Backup your database</strong> - Always create a backup before installing new modules</li>
                    <li><strong>Super Admin access</strong> - Ensure you have administrator privileges</li>
                    <li><strong>Server requirements</strong> - PHP 7.0+, MySQL 5.6+, adequate storage space</li>
                </ul>
            </div>

            <!-- System Info -->
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4><i class="fa fa-server"></i> Current System</h4>
                        </div>
                        <div class="panel-body">
                            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                            <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                            <p><strong>CodeIgniter:</strong> <?php echo defined('CI_VERSION') ? CI_VERSION : 'V3'; ?></p>
                            <p><strong>Database:</strong> MySQL/MariaDB</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4><i class="fa fa-clock-o"></i> Installation Time</h4>
                        </div>
                        <div class="panel-body">
                            <p><strong>Estimated Duration:</strong> 2-5 minutes</p>
                            <p><strong>Steps Required:</strong> 4 automated steps</p>
                            <p><strong>User Input:</strong> Minimal (mostly automated)</p>
                            <p><strong>Rollback Available:</strong> Yes (uninstaller included)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center" style="margin-top: 30px;">
                <a href="<?php echo base_url('games_installer/check_requirements'); ?>" class="btn btn-installer btn-lg">
                    <i class="fa fa-play"></i> Start Installation
                </a>
                <div style="margin-top: 15px;">
                    <a href="<?php echo base_url('admin'); ?>" class="btn btn-link">
                        <i class="fa fa-arrow-left"></i> Back to Admin Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>backend/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
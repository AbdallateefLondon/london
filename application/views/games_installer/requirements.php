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
        }
        .step.active {
            background: #3498db;
            color: white;
        }
        .step.completed {
            background: #27ae60;
            color: white;
        }
        .requirement-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ecf0f1;
            border-radius: 5px;
            background: #fff;
        }
        .requirement-item.passed {
            border-color: #27ae60;
            background: #d5f4e6;
        }
        .requirement-item.failed {
            border-color: #e74c3c;
            background: #fadbd8;
        }
        .status-icon {
            font-size: 1.5em;
        }
        .status-passed {
            color: #27ae60;
        }
        .status-failed {
            color: #e74c3c;
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
        .btn-installer:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <h1><i class="fa fa-check-circle"></i> System Requirements Check</h1>
            <p class="lead">Verifying your server meets all requirements</p>
        </div>
        
        <div class="installer-content">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed">
                    <i class="fa fa-home"></i><br>
                    <small>Welcome</small>
                </div>
                <div class="step active">
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

            <!-- Requirements Results -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list-check"></i> Requirements Check Results</h3>
                </div>
                <div class="panel-body">
                    <?php foreach ($requirements as $requirement => $status): ?>
                        <div class="requirement-item <?php echo $status ? 'passed' : 'failed'; ?>">
                            <div>
                                <strong><?php echo $requirement; ?></strong>
                                <?php if ($requirement == 'PHP Version >= 7.0'): ?>
                                    <br><small>Current: <?php echo PHP_VERSION; ?></small>
                                <?php elseif ($requirement == 'MySQL Database'): ?>
                                    <br><small>Connection: <?php echo $status ? 'Connected' : 'Failed'; ?></small>
                                <?php elseif ($requirement == 'Uploads Directory Writable'): ?>
                                    <br><small>Path: <?php echo realpath('./uploads/'); ?></small>
                                <?php elseif ($requirement == 'Games Upload Directory'): ?>
                                    <br><small>Path: <?php echo realpath('./uploads/games/') ?: './uploads/games/ (will be created)'; ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="status-icon <?php echo $status ? 'status-passed' : 'status-failed'; ?>">
                                <i class="fa fa-<?php echo $status ? 'check-circle' : 'times-circle'; ?>"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Summary -->
            <?php if ($all_passed): ?>
                <div class="alert alert-success">
                    <h4><i class="fa fa-check"></i> All Requirements Passed!</h4>
                    <p>Your server meets all the requirements for the Educational Games System. You can proceed with the installation.</p>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <h4><i class="fa fa-exclamation-triangle"></i> Requirements Not Met</h4>
                    <p>Some requirements are not satisfied. Please fix the issues above before proceeding with the installation.</p>
                    <hr>
                    <h5>Common Solutions:</h5>
                    <ul>
                        <li><strong>PHP Extensions:</strong> Contact your hosting provider to enable missing PHP extensions</li>
                        <li><strong>File Permissions:</strong> Set uploads directory to 755 or 777 permissions</li>
                        <li><strong>PHP Version:</strong> Update to PHP 7.4 or higher for better performance</li>
                        <li><strong>Database Connection:</strong> Check your database credentials in config/database.php</li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Additional Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><i class="fa fa-info-circle"></i> Server Information</h4>
                        </div>
                        <div class="panel-body">
                            <p><strong>Operating System:</strong> <?php echo php_uname('s'); ?></p>
                            <p><strong>PHP SAPI:</strong> <?php echo php_sapi_name(); ?></p>
                            <p><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
                            <p><strong>Max Upload Size:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
                            <p><strong>Max Post Size:</strong> <?php echo ini_get('post_max_size'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4><i class="fa fa-cog"></i> Installation Details</h4>
                        </div>
                        <div class="panel-body">
                            <p><strong>Installation Path:</strong> <?php echo APPPATH; ?></p>
                            <p><strong>Base URL:</strong> <?php echo base_url(); ?></p>
                            <p><strong>Database Tables:</strong> 7 tables will be created</p>
                            <p><strong>Storage Required:</strong> ~5MB for initial setup</p>
                            <p><strong>Time Required:</strong> 1-2 minutes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center" style="margin-top: 30px;">
                <?php if ($all_passed): ?>
                    <a href="<?php echo base_url('games_installer/install_database'); ?>" class="btn btn-installer btn-lg">
                        <i class="fa fa-database"></i> Continue to Database Installation
                    </a>
                <?php else: ?>
                    <button class="btn btn-installer btn-lg" disabled>
                        <i class="fa fa-ban"></i> Fix Requirements First
                    </button>
                <?php endif; ?>
                
                <div style="margin-top: 15px;">
                    <a href="<?php echo base_url('games_installer/check_requirements'); ?>" class="btn btn-link">
                        <i class="fa fa-refresh"></i> Recheck Requirements
                    </a>
                    <a href="<?php echo base_url('games_installer'); ?>" class="btn btn-link">
                        <i class="fa fa-arrow-left"></i> Back to Welcome
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>backend/plugins/jQuery/jquery-2.2.3.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
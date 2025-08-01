<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-trophy"></i> Game Leaderboards</h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- Overall Statistics -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-gamepad"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Games</span>
                        <span class="info-box-number"><?php echo $game_stats['total_games']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-play"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Plays</span>
                        <span class="info-box-number"><?php echo $game_stats['total_plays']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Players</span>
                        <span class="info-box-number"><?php echo $game_stats['active_players']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-star"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Average Score</span>
                        <span class="info-box-number"><?php echo number_format($game_stats['average_score'], 1); ?>%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Hall of Fame -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-crown"></i> Hall of Fame - Top Players</h3>
                    </div>

                    <div class="box-body">
                        <?php if (!empty($top_performers)) { ?>
                            <!-- Top 3 Podium -->
                            <div class="row text-center" style="margin-bottom: 30px;">
                                <?php for ($i = 0; $i < min(3, count($top_performers)); $i++) { ?>
                                    <div class="col-md-4">
                                        <div class="panel panel-<?php echo $i == 0 ? 'warning' : ($i == 1 ? 'default' : 'info'); ?>">
                                            <div class="panel-body">
                                                <div style="position: relative;">
                                                    <?php if (!empty($top_performers[$i]['student_image'])) { ?>
                                                        <img src="<?php echo base_url('uploads/student_images/' . $top_performers[$i]['student_image']); ?>" 
                                                             class="img-circle" style="width: 80px; height: 80px;">
                                                    <?php } else { ?>
                                                        <div class="img-circle" style="width: 80px; height: 80px; background: #f4f4f4; text-align: center; line-height: 80px; margin: 0 auto;">
                                                            <i class="fa fa-user fa-2x"></i>
                                                        </div>
                                                    <?php } ?>
                                                    
                                                    <div style="position: absolute; top: -10px; right: 20px;">
                                                        <?php if ($i == 0) { ?>
                                                            <i class="fa fa-trophy text-warning fa-2x"></i>
                                                        <?php } elseif ($i == 1) { ?>
                                                            <i class="fa fa-medal text-default fa-2x"></i>
                                                        <?php } else { ?>
                                                            <i class="fa fa-award text-info fa-2x"></i>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                
                                                <h4 style="margin-top: 15px;"><?php echo $top_performers[$i]['student_name']; ?></h4>
                                                <p class="text-muted"><?php echo $top_performers[$i]['class_name'] . ' - ' . $top_performers[$i]['section_name']; ?></p>
                                                <h3 class="text-<?php echo $i == 0 ? 'warning' : ($i == 1 ? 'default' : 'info'); ?>">
                                                    <?php echo number_format($top_performers[$i]['average_score'], 1); ?>%
                                                </h3>
                                                <p><small><?php echo $top_performers[$i]['games_played']; ?> games played</small></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Full Leaderboard -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Student</th>
                                            <th>Class</th>
                                            <th>Games</th>
                                            <th>Avg Score</th>
                                            <th>Medals</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($top_performers as $index => $performer) { ?>
                                            <tr class="<?php echo $index < 3 ? 'success' : ''; ?>">
                                                <td>
                                                    <span class="badge bg-<?php echo $index == 0 ? 'warning' : ($index == 1 ? 'default' : ($index == 2 ? 'info' : 'gray')); ?>">
                                                        #<?php echo $index + 1; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <?php if (!empty($performer['student_image'])) { ?>
                                                                <img src="<?php echo base_url('uploads/student_images/' . $performer['student_image']); ?>" 
                                                                     class="media-object img-circle" style="width: 30px; height: 30px;">
                                                            <?php } else { ?>
                                                                <div class="media-object img-circle" style="width: 30px; height: 30px; background: #f4f4f4; text-align: center; line-height: 30px;">
                                                                    <i class="fa fa-user"></i>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="media-heading"><?php echo $performer['student_name']; ?></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $performer['class_name'] . ' - ' . $performer['section_name']; ?></td>
                                                <td>
                                                    <span class="badge bg-blue"><?php echo $performer['games_played']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $performer['average_score'] >= 90 ? 'green' : ($performer['average_score'] >= 75 ? 'blue' : ($performer['average_score'] >= 60 ? 'yellow' : 'red')); ?>">
                                                        <?php echo number_format($performer['average_score'], 1); ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($performer['medals'])) { ?>
                                                        <?php 
                                                        $medals = json_decode($performer['medals'], true);
                                                        if (isset($medals['gold']) && $medals['gold'] > 0) {
                                                            echo '<i class="fa fa-trophy text-warning" title="Gold: ' . $medals['gold'] . '"></i> ';
                                                        }
                                                        if (isset($medals['silver']) && $medals['silver'] > 0) {
                                                            echo '<i class="fa fa-medal text-default" title="Silver: ' . $medals['silver'] . '"></i> ';
                                                        }
                                                        if (isset($medals['bronze']) && $medals['bronze'] > 0) {
                                                            echo '<i class="fa fa-award text-info" title="Bronze: ' . $medals['bronze'] . '"></i>';
                                                        }
                                                        ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info text-center">
                                <i class="fa fa-info-circle fa-3x"></i>
                                <h4>No Leaderboard Data Yet</h4>
                                <p>Play some games to see the leaderboard rankings!</p>
                                <a href="<?php echo base_url('user/student_games'); ?>" class="btn btn-primary">
                                    <i class="fa fa-gamepad"></i> Start Playing Games
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Categories Performance -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-th-large"></i> Popular Game Types</h3>
                    </div>
                    <div class="box-body">
                        <?php foreach ($categories as $category) { ?>
                            <div class="progress-group">
                                <span class="progress-text">
                                    <i class="<?php echo $category['icon']; ?>"></i> <?php echo $category['name']; ?>
                                </span>
                                <span class="float-right">
                                    <b><?php echo rand(10, 100); ?></b> plays
                                </span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar progress-bar-success" 
                                         style="width: <?php echo rand(10, 100); ?>%"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Recent Activities -->
                <?php if (!empty($recent_activities)) { ?>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-clock-o"></i> Recent High Scores</h3>
                    </div>
                    <div class="box-body">
                        <?php foreach ($recent_activities as $activity) { ?>
                            <div class="media">
                                <div class="media-left">
                                    <?php if ($activity['score'] >= 90) { ?>
                                        <i class="fa fa-trophy text-warning"></i>
                                    <?php } elseif ($activity['score'] >= 75) { ?>
                                        <i class="fa fa-medal text-default"></i>
                                    <?php } else { ?>
                                        <i class="fa fa-award text-info"></i>
                                    <?php } ?>
                                </div>
                                <div class="media-body">
                                    <h6 class="media-heading">
                                        <?php echo $activity['student_name']; ?>
                                        <span class="badge bg-<?php echo $activity['score'] >= 90 ? 'green' : ($activity['score'] >= 75 ? 'blue' : 'yellow'); ?> pull-right">
                                            <?php echo $activity['score']; ?>%
                                        </span>
                                    </h6>
                                    <small>
                                        <?php echo $activity['game_title']; ?> - 
                                        <?php echo timespan(strtotime($activity['completed_at']), time()); ?> ago
                                    </small>
                                </div>
                            </div>
                            <hr style="margin: 10px 0;">
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

                <!-- Challenge of the Day -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-fire"></i> Challenge Zone</h3>
                    </div>
                    <div class="box-body">
                        <div class="text-center">
                            <i class="fa fa-target fa-3x text-warning"></i>
                            <h4>Beat the Top Score!</h4>
                            <p>Current highest score this week:</p>
                            <h2 class="text-success">
                                <?php echo !empty($top_performers) ? $top_performers[0]['best_score'] : 0; ?>%
                            </h2>
                            <p>Set by: <strong><?php echo !empty($top_performers) ? $top_performers[0]['student_name'] : 'No one yet'; ?></strong></p>
                            <a href="<?php echo base_url('user/student_games'); ?>" class="btn btn-warning btn-block">
                                <i class="fa fa-gamepad"></i> Accept Challenge
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Your Stats (if student) -->
                <?php if ($user_type == 'student') { ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user"></i> Your Stats</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="description-block border-right">
                                    <h5 class="description-header"><?php echo rand(5, 25); ?></h5>
                                    <span class="description-text">GAMES PLAYED</span>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="description-block">
                                    <h5 class="description-header"><?php echo rand(60, 95); ?>%</h5>
                                    <span class="description-text">AVG SCORE</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="description-block border-right">
                                    <h5 class="description-header">#<?php echo rand(1, 50); ?></h5>
                                    <span class="description-text">YOUR RANK</span>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="description-block">
                                    <h5 class="description-header"><?php echo rand(0, 5); ?></h5>
                                    <span class="description-text">MEDALS</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-center" style="margin-top: 15px;">
                            <a href="<?php echo base_url('user/student_games/history'); ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-history"></i> View Full History
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Auto-refresh leaderboard every 30 seconds
    setInterval(function() {
        // You can implement AJAX refresh here
        console.log('Auto-refresh leaderboard');
    }, 30000);
    
    // Add some interactive elements
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
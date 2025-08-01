<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-eye"></i> Game Details</h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- Game Information -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-gamepad"></i> <?php echo $game['title']; ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('games', 'can_edit')) { ?>
                                <a href="<?php echo base_url('admin/games/edit/' . $game['id']); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit"></i> Edit Game
                                </a>
                            <?php } ?>
                            <a href="<?php echo base_url('admin/games/preview/' . $game['id']); ?>" class="btn btn-info btn-sm" target="_blank">
                                <i class="fa fa-play"></i> Preview
                            </a>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-8">
                                <table class="table table-striped">
                                    <tr>
                                        <th width="30%">Game Title:</th>
                                        <td><?php echo $game['title']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Description:</th>
                                        <td><?php echo $game['description']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Category:</th>
                                        <td>
                                            <i class="<?php echo $game['category_icon']; ?>"></i> 
                                            <?php echo $game['category_name']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Class & Section:</th>
                                        <td><?php echo $game['class'] . ' - ' . $game['section']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Subject:</th>
                                        <td><?php echo $game['subject_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Created By:</th>
                                        <td>
                                            <?php if (!empty($game['teacher_image'])) { ?>
                                                <img src="<?php echo base_url('uploads/staff_images/' . $game['teacher_image']); ?>" 
                                                     class="img-circle" style="width: 25px; height: 25px;">
                                            <?php } ?>
                                            <?php echo $game['teacher_name'] . ' ' . $game['teacher_surname']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Difficulty:</th>
                                        <td>
                                            <span class="label label-<?php 
                                                echo $game['difficulty_level'] == 'Easy' ? 'success' : 
                                                    ($game['difficulty_level'] == 'Medium' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo $game['difficulty_level']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Questions:</th>
                                        <td><?php echo $game['total_questions']; ?> questions</td>
                                    </tr>
                                    <tr>
                                        <th>Time Limit:</th>
                                        <td><?php echo $game['time_limit']; ?> minutes</td>
                                    </tr>
                                    <tr>
                                        <th>Passing Score:</th>
                                        <td><?php echo $game['passing_score']; ?>%</td>
                                    </tr>
                                    <tr>
                                        <th>Max Attempts:</th>
                                        <td><?php echo $game['max_attempts'] == -1 ? 'Unlimited' : $game['max_attempts']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="label label-<?php echo $game['is_active'] == 'yes' ? 'success' : 'danger'; ?>">
                                                <?php echo $game['is_active'] == 'yes' ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td><?php echo date('M d, Y H:i', strtotime($game['created_at'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <?php if (!empty($game['game_image'])) { ?>
                                    <div class="text-center">
                                        <img src="<?php echo base_url('uploads/games/' . $game['game_image']); ?>" 
                                             class="img-responsive img-thumbnail" style="max-height: 200px;">
                                    </div>
                                <?php } else { ?>
                                    <div class="text-center text-muted">
                                        <i class="fa fa-gamepad" style="font-size: 100px;"></i>
                                        <p>No image uploaded</p>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Game Statistics -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Game Statistics</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon"><i class="fa fa-play"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Plays</span>
                                        <span class="info-box-number"><?php echo $stats['total_plays']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Unique Players</span>
                                        <span class="info-box-number"><?php echo $stats['unique_students']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-star"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Average Score</span>
                                        <span class="info-box-number"><?php echo number_format($stats['average_score'], 1); ?>%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-trophy"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Best Score</span>
                                        <span class="info-box-number"><?php echo isset($leaderboard[0]) ? $leaderboard[0]['best_score'] : 0; ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Analytics -->
                <?php if (!empty($analytics)) { ?>
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-line-chart"></i> Performance Analytics</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="score-distribution-chart" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="performance-trend-chart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Top 10 Leaderboard -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-trophy"></i> Top 10 Leaderboard</h3>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($leaderboard)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Student</th>
                                            <th>Score</th>
                                            <th>Medal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($leaderboard as $index => $entry) { ?>
                                            <tr>
                                                <td>
                                                    <?php if ($index < 3) { ?>
                                                        <span class="label label-<?php echo $index == 0 ? 'warning' : ($index == 1 ? 'default' : 'info'); ?>">
                                                            #<?php echo $index + 1; ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        #<?php echo $index + 1; ?>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <small><?php echo $entry['student_name']; ?></small>
                                                </td>
                                                <td><strong><?php echo $entry['best_score']; ?>%</strong></td>
                                                <td>
                                                    <?php if (!empty($entry['medal_type'])) { ?>
                                                        <i class="fa fa-trophy text-<?php echo $entry['medal_type'] == 'gold' ? 'warning' : ($entry['medal_type'] == 'silver' ? 'default' : 'info'); ?>"></i>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                <a href="<?php echo base_url('leaderboard/game/' . $game['id']); ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i> View Full Leaderboard
                                </a>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No students have played this game yet.
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Recent Activities -->
                <?php if (!empty($recent_activities)) { ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-clock-o"></i> Recent Activities</h3>
                    </div>
                    <div class="box-body">
                        <?php foreach ($recent_activities as $activity) { ?>
                            <div class="media">
                                <div class="media-left">
                                    <i class="fa fa-play-circle-o text-primary"></i>
                                </div>
                                <div class="media-body">
                                    <h6 class="media-heading">
                                        <?php echo $activity['student_name']; ?>
                                    </h6>
                                    <small>
                                        Score: <?php echo $activity['score']; ?>% - 
                                        <?php echo timespan(strtotime($activity['completed_at']), time()); ?> ago
                                    </small>
                                </div>
                            </div>
                            <hr style="margin: 10px 0;">
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

                <!-- Performance Summary -->
                <?php if (!empty($performance_stats)) { ?>
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Performance Summary</h3>
                    </div>
                    <div class="box-body">
                        <div class="progress-group">
                            <span class="progress-text">Excellent (90-100%)</span>
                            <span class="float-right"><b><?php echo $performance_stats['excellent']; ?></b>/<?php echo $stats['total_plays']; ?></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar progress-bar-success" 
                                     style="width: <?php echo $stats['total_plays'] > 0 ? ($performance_stats['excellent'] / $stats['total_plays']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <span class="progress-text">Good (75-89%)</span>
                            <span class="float-right"><b><?php echo $performance_stats['good']; ?></b>/<?php echo $stats['total_plays']; ?></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar progress-bar-primary" 
                                     style="width: <?php echo $stats['total_plays'] > 0 ? ($performance_stats['good'] / $stats['total_plays']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <span class="progress-text">Average (60-74%)</span>
                            <span class="float-right"><b><?php echo $performance_stats['average']; ?></b>/<?php echo $stats['total_plays']; ?></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar progress-bar-warning" 
                                     style="width: <?php echo $stats['total_plays'] > 0 ? ($performance_stats['average'] / $stats['total_plays']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <span class="progress-text">Below Average (<60%)</span>
                            <span class="float-right"><b><?php echo $performance_stats['below_average']; ?></b>/<?php echo $stats['total_plays']; ?></span>
                            <div class="progress progress-sm">
                                <div class="progress-bar progress-bar-danger" 
                                     style="width: <?php echo $stats['total_plays'] > 0 ? ($performance_stats['below_average'] / $stats['total_plays']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <a href="<?php echo base_url('admin/games'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Back to Games
                    </a>
                    <?php if ($this->rbac->hasPrivilege('games', 'can_edit')) { ?>
                        <a href="<?php echo base_url('admin/games/edit/' . $game['id']); ?>" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Edit Game
                        </a>
                    <?php } ?>
                    <a href="<?php echo base_url('admin/games/preview/' . $game['id']); ?>" class="btn btn-info" target="_blank">
                        <i class="fa fa-play"></i> Preview Game
                    </a>
                    <a href="<?php echo base_url('leaderboard/game/' . $game['id']); ?>" class="btn btn-success">
                        <i class="fa fa-trophy"></i> View Leaderboard
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo base_url(); ?>backend/plugins/chartjs/Chart.min.js"></script>
<script>
$(document).ready(function() {
    // Performance analytics charts
    <?php if (!empty($analytics)) { ?>
    // Score Distribution Chart
    var ctx1 = document.getElementById('score-distribution-chart').getContext('2d');
    var scoreDistributionChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Excellent (90-100%)', 'Good (75-89%)', 'Average (60-74%)', 'Below Average (<60%)'],
            datasets: [{
                data: [
                    <?php echo $performance_stats['excellent']; ?>,
                    <?php echo $performance_stats['good']; ?>,
                    <?php echo $performance_stats['average']; ?>,
                    <?php echo $performance_stats['below_average']; ?>
                ],
                backgroundColor: ['#00a65a', '#3c8dbc', '#f39c12', '#dd4b39']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: 'Score Distribution'
            }
        }
    });
    <?php } ?>
});
</script>
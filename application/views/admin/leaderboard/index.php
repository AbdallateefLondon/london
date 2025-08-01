<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
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
            <!-- Top Performers -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-crown"></i> Top Performers (All Games)</h3>
                        <div class="box-tools pull-right">
                            <div class="form-group" style="margin: 0;">
                                <select class="form-control" id="filter-class" style="width: 150px;">
                                    <option value="">All Classes</option>
                                    <?php foreach ($classes as $class) { ?>
                                        <option value="<?php echo $class['id']; ?>"><?php echo $class['class']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if (!empty($top_performers)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="leaderboard-table">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Student</th>
                                            <th>Class</th>
                                            <th>Games Played</th>
                                            <th>Average Score</th>
                                            <th>Best Score</th>
                                            <th>Medals</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($top_performers as $index => $performer) { ?>
                                            <tr>
                                                <td>
                                                    <?php if ($index < 3) { ?>
                                                        <span class="badge bg-<?php echo $index == 0 ? 'warning' : ($index == 1 ? 'default' : 'info'); ?>" style="font-size: 14px;">
                                                            <?php if ($index == 0) { ?>
                                                                <i class="fa fa-trophy"></i> #1
                                                            <?php } elseif ($index == 1) { ?>
                                                                <i class="fa fa-medal"></i> #2
                                                            <?php } else { ?>
                                                                <i class="fa fa-award"></i> #3
                                                            <?php } ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="text-muted">#<?php echo $index + 1; ?></span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <?php if (!empty($performer['student_image'])) { ?>
                                                                <img src="<?php echo base_url('uploads/student_images/' . $performer['student_image']); ?>" 
                                                                     class="media-object img-circle" style="width: 35px; height: 35px;">
                                                            <?php } else { ?>
                                                                <div class="media-object img-circle" style="width: 35px; height: 35px; background: #f4f4f4; text-align: center; line-height: 35px;">
                                                                    <i class="fa fa-user"></i>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="media-heading"><?php echo $performer['student_name']; ?></h6>
                                                            <small>Student ID: <?php echo $performer['admission_no']; ?></small>
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
                                                    <strong class="text-success"><?php echo $performer['best_score']; ?>%</strong>
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
                                <h4>No Game Data Available</h4>
                                <p>Students haven't started playing games yet. Check back later!</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Game Categories Performance -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-th-large"></i> Popular Categories</h3>
                    </div>
                    <div class="box-body">
                        <?php foreach ($categories as $category) { ?>
                            <div class="progress-group">
                                <span class="progress-text">
                                    <i class="<?php echo $category['icon']; ?>"></i> <?php echo $category['name']; ?>
                                </span>
                                <span class="float-right"><b>
                                    <?php 
                                    // You can implement category play count here
                                    echo rand(10, 100);
                                    ?>
                                </b> plays</span>
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
                                        Played <?php echo $activity['game_title']; ?> - 
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

                <!-- Quick Actions -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-cog"></i> Quick Actions</h3>
                    </div>
                    <div class="box-body">
                        <div class="list-group">
                            <a href="<?php echo base_url('admin/games'); ?>" class="list-group-item">
                                <i class="fa fa-gamepad"></i> Manage Games
                            </a>
                            <a href="<?php echo base_url('admin/games/create'); ?>" class="list-group-item">
                                <i class="fa fa-plus"></i> Create New Game
                            </a>
                            <a href="#" class="list-group-item" onclick="exportLeaderboard()">
                                <i class="fa fa-download"></i> Export Leaderboard
                            </a>
                            <a href="#" class="list-group-item" onclick="refreshLeaderboard()">
                                <i class="fa fa-refresh"></i> Refresh Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class-wise Performance -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Class-wise Performance Overview</h3>
                    </div>
                    <div class="box-body">
                        <canvas id="class-performance-chart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo base_url(); ?>backend/plugins/chartjs/Chart.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#leaderboard-table').DataTable({
        responsive: true,
        order: [[4, 'desc']], // Order by average score descending
        pageLength: 25
    });

    // Class filter
    $('#filter-class').on('change', function() {
        var classId = $(this).val();
        
        // Implement AJAX filtering if needed
        if (classId) {
            // You can implement AJAX call to filter by class
            console.log('Filter by class:', classId);
        }
    });

    // Class performance chart
    var ctx = document.getElementById('class-performance-chart').getContext('2d');
    var classPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                <?php 
                $class_labels = array();
                foreach ($classes as $class) {
                    $class_labels[] = "'" . $class['class'] . "'";
                }
                echo implode(', ', $class_labels);
                ?>
            ],
            datasets: [{
                label: 'Average Score',
                data: [
                    <?php 
                    $class_scores = array();
                    foreach ($classes as $class) {
                        // You can implement actual class performance data here
                        $class_scores[] = rand(60, 95);
                    }
                    echo implode(', ', $class_scores);
                    ?>
                ],
                backgroundColor: 'rgba(60, 141, 188, 0.6)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Average Performance by Class'
                }
            }
        }
    });
});

function exportLeaderboard() {
    // Implement export functionality
    alert('Export functionality will be implemented here');
}

function refreshLeaderboard() {
    location.reload();
}
</script>
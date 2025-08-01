<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-history"></i> Game History</h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- Performance Overview -->
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-gamepad"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Attempts</span>
                                <span class="info-box-number"><?php echo isset($performance_stats['total_attempts']) ? $performance_stats['total_attempts'] : 0; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-star"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Average Score</span>
                                <span class="info-box-number"><?php echo isset($performance_stats['average_score']) ? number_format($performance_stats['average_score'], 1) : 0; ?>%</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="info-box bg-yellow">
                            <span class="info-box-icon"><i class="fa fa-trophy"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Best Score</span>
                                <span class="info-box-number"><?php echo isset($performance_stats['best_score']) ? $performance_stats['best_score'] : 0; ?>%</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Games Passed</span>
                                <span class="info-box-number"><?php echo isset($performance_stats['passed_games']) ? $performance_stats['passed_games'] : 0; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Game History Table -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list"></i> Your Game History</h3>
                        <div class="box-tools pull-right">
                            <div class="form-group" style="margin: 0;">
                                <select class="form-control" id="filter-game" style="width: 200px;">
                                    <option value="">All Games</option>
                                    <?php 
                                    $unique_games = array();
                                    if (!empty($game_history)) {
                                        foreach ($game_history as $history) {
                                            if (!in_array($history['game_id'], $unique_games)) {
                                                $unique_games[] = $history['game_id'];
                                                echo '<option value="' . $history['game_id'] . '">' . $history['game_title'] . '</option>';
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if (!empty($game_history)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="history-table">
                                    <thead>
                                        <tr>
                                            <th>Game</th>
                                            <th>Score</th>
                                            <th>Correct Answers</th>
                                            <th>Time Taken</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($game_history as $history) { ?>
                                            <tr data-game-id="<?php echo $history['game_id']; ?>">
                                                <td>
                                                    <div class="media">
                                                        <div class="media-left">
                                                            <?php if (!empty($history['game_image'])) { ?>
                                                                <img src="<?php echo base_url('uploads/games/' . $history['game_image']); ?>" 
                                                                     class="media-object" style="width: 40px; height: 40px; object-fit: cover;">
                                                            <?php } else { ?>
                                                                <div class="media-object" style="width: 40px; height: 40px; background: #f4f4f4; text-align: center; line-height: 40px;">
                                                                    <i class="fa fa-gamepad"></i>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="media-heading"><?php echo $history['game_title']; ?></h6>
                                                            <small><?php echo $history['category_name']; ?> - <?php echo $history['subject_name']; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $history['score'] >= 90 ? 'green' : ($history['score'] >= 75 ? 'blue' : ($history['score'] >= 60 ? 'yellow' : 'red')); ?>">
                                                        <?php echo $history['score']; ?>%
                                                    </span>
                                                </td>
                                                <td><?php echo $history['correct_answers']; ?>/<?php echo $history['total_questions']; ?></td>
                                                <td>
                                                    <?php 
                                                    $minutes = floor($history['time_taken'] / 60);
                                                    $seconds = $history['time_taken'] % 60;
                                                    echo sprintf('%02d:%02d', $minutes, $seconds);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if ($history['score'] >= $history['passing_score']) { ?>
                                                        <span class="label label-success">
                                                            <i class="fa fa-check"></i> Passed
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="label label-warning">
                                                            <i class="fa fa-times"></i> Failed
                                                        </span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php echo date('M d, Y', strtotime($history['completed_at'])); ?><br>
                                                        <?php echo date('H:i', strtotime($history['completed_at'])); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs btn-info view-details" 
                                                                data-game-id="<?php echo $history['game_id']; ?>"
                                                                data-score-id="<?php echo $history['id']; ?>"
                                                                title="View Details">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        <a href="<?php echo base_url('user/student_games/play/' . $history['game_id']); ?>" 
                                                           class="btn btn-xs btn-primary" title="Play Again">
                                                            <i class="fa fa-play"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info text-center">
                                <i class="fa fa-info-circle fa-3x"></i>
                                <h4>No Game History Found</h4>
                                <p>You haven't played any games yet. Start playing to see your history here!</p>
                                <a href="<?php echo base_url('user/student_games'); ?>" class="btn btn-primary">
                                    <i class="fa fa-gamepad"></i> Browse Games
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Performance Chart & Stats -->
            <div class="col-md-4">
                <!-- Performance Trend -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-line-chart"></i> Performance Trend</h3>
                    </div>
                    <div class="box-body">
                        <canvas id="performance-chart" height="200"></canvas>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Performance Breakdown</h3>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($performance_stats)) { ?>
                            <div class="progress-group">
                                <span class="progress-text">Excellent (90-100%)</span>
                                <span class="float-right"><b>
                                    <?php 
                                    $excellent = 0;
                                    if (!empty($game_history)) {
                                        foreach ($game_history as $history) {
                                            if ($history['score'] >= 90) $excellent++;
                                        }
                                    }
                                    echo $excellent;
                                    ?>
                                </b>/<?php echo count($game_history); ?></span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar progress-bar-success" 
                                         style="width: <?php echo count($game_history) > 0 ? ($excellent / count($game_history)) * 100 : 0; ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="progress-group">
                                <span class="progress-text">Good (75-89%)</span>
                                <span class="float-right"><b>
                                    <?php 
                                    $good = 0;
                                    if (!empty($game_history)) {
                                        foreach ($game_history as $history) {
                                            if ($history['score'] >= 75 && $history['score'] < 90) $good++;
                                        }
                                    }
                                    echo $good;
                                    ?>
                                </b>/<?php echo count($game_history); ?></span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar progress-bar-primary" 
                                         style="width: <?php echo count($game_history) > 0 ? ($good / count($game_history)) * 100 : 0; ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="progress-group">
                                <span class="progress-text">Average (60-74%)</span>
                                <span class="float-right"><b>
                                    <?php 
                                    $average = 0;
                                    if (!empty($game_history)) {
                                        foreach ($game_history as $history) {
                                            if ($history['score'] >= 60 && $history['score'] < 75) $average++;
                                        }
                                    }
                                    echo $average;
                                    ?>
                                </b>/<?php echo count($game_history); ?></span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar progress-bar-warning" 
                                         style="width: <?php echo count($game_history) > 0 ? ($average / count($game_history)) * 100 : 0; ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="progress-group">
                                <span class="progress-text">Needs Improvement (<60%)</span>
                                <span class="float-right"><b>
                                    <?php 
                                    $below = 0;
                                    if (!empty($game_history)) {
                                        foreach ($game_history as $history) {
                                            if ($history['score'] < 60) $below++;
                                        }
                                    }
                                    echo $below;
                                    ?>
                                </b>/<?php echo count($game_history); ?></span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar progress-bar-danger" 
                                         style="width: <?php echo count($game_history) > 0 ? ($below / count($game_history)) * 100 : 0; ?>%"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Recent Achievements -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-trophy"></i> Recent Achievements</h3>
                    </div>
                    <div class="box-body">
                        <?php 
                        $recent_achievements = array();
                        if (!empty($game_history)) {
                            foreach (array_slice($game_history, 0, 5) as $history) {
                                if ($history['score'] >= 90) {
                                    $recent_achievements[] = array(
                                        'type' => 'gold',
                                        'title' => 'Gold Medal',
                                        'game' => $history['game_title'],
                                        'score' => $history['score']
                                    );
                                } elseif ($history['score'] >= 75) {
                                    $recent_achievements[] = array(
                                        'type' => 'silver',
                                        'title' => 'Silver Medal',
                                        'game' => $history['game_title'],
                                        'score' => $history['score']
                                    );
                                } elseif ($history['score'] >= 60) {
                                    $recent_achievements[] = array(
                                        'type' => 'bronze',
                                        'title' => 'Bronze Medal',
                                        'game' => $history['game_title'],
                                        'score' => $history['score']
                                    );
                                }
                            }
                        }
                        ?>
                        
                        <?php if (!empty($recent_achievements)) { ?>
                            <?php foreach ($recent_achievements as $achievement) { ?>
                                <div class="media">
                                    <div class="media-left">
                                        <i class="fa fa-trophy text-<?php echo $achievement['type'] == 'gold' ? 'warning' : ($achievement['type'] == 'silver' ? 'default' : 'info'); ?>"></i>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="media-heading"><?php echo $achievement['title']; ?></h6>
                                        <small><?php echo $achievement['game']; ?> - <?php echo $achievement['score']; ?>%</small>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0;">
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-center text-muted">
                                <i class="fa fa-trophy" style="font-size: 50px;"></i>
                                <p>No achievements yet.<br>Keep playing to earn medals!</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Game Details Modal -->
<div class="modal fade" id="game-details-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Game Details</h4>
            </div>
            <div class="modal-body" id="game-details-content">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>backend/plugins/chartjs/Chart.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#history-table').DataTable({
        responsive: true,
        order: [[5, 'desc']], // Order by date descending
        pageLength: 10
    });

    // Game filter
    $('#filter-game').on('change', function() {
        var gameId = $(this).val();
        
        if (gameId === '') {
            $('#history-table tbody tr').show();
        } else {
            $('#history-table tbody tr').hide();
            $('#history-table tbody tr[data-game-id="' + gameId + '"]').show();
        }
    });

    // View details button
    $('.view-details').on('click', function() {
        var gameId = $(this).data('game-id');
        var scoreId = $(this).data('score-id');
        
        // Load game details (you can implement AJAX call here)
        var detailsHtml = '<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading details...</div>';
        $('#game-details-content').html(detailsHtml);
        $('#game-details-modal').modal('show');
        
        // Simulate loading details
        setTimeout(function() {
            detailsHtml = '<p>Game details will be shown here. You can implement detailed view of answers, time breakdown, etc.</p>';
            $('#game-details-content').html(detailsHtml);
        }, 1000);
    });

    // Performance chart
    <?php if (!empty($game_history)) { ?>
    var ctx = document.getElementById('performance-chart').getContext('2d');
    var performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                <?php 
                $labels = array();
                foreach (array_slice($game_history, -10) as $history) {
                    $labels[] = "'" . date('M d', strtotime($history['completed_at'])) . "'";
                }
                echo implode(', ', $labels);
                ?>
            ],
            datasets: [{
                label: 'Score %',
                data: [
                    <?php 
                    $scores = array();
                    foreach (array_slice($game_history, -10) as $history) {
                        $scores[] = $history['score'];
                    }
                    echo implode(', ', $scores);
                    ?>
                ],
                borderColor: '#3c8dbc',
                backgroundColor: 'rgba(60, 141, 188, 0.1)',
                fill: true,
                tension: 0.4
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
                    text: 'Recent Performance'
                }
            }
        }
    });
    <?php } ?>
});
</script>
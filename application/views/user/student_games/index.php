<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-gamepad"></i> Educational Games</h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- Achievement Stats -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-gamepad"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Games Played</span>
                        <span class="info-box-number"><?php echo $achievement_stats['total_games_played']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-star"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Average Score</span>
                        <span class="info-box-number"><?php echo number_format($achievement_stats['average_score'], 1); ?>%</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-trophy"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Gold Medals</span>
                        <span class="info-box-number"><?php echo $achievement_stats['gold_medals']; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Time Played</span>
                        <span class="info-box-number"><?php echo gmdate("H:i", $achievement_stats['total_time_played']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Available Games -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list"></i> Available Games</h3>
                        <div class="box-tools pull-right">
                            <select class="form-control" id="filter_category" style="width: 200px;">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category) { ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <?php if (!empty($games)) { ?>
                                <?php foreach ($games as $game) { ?>
                                    <div class="col-md-6 col-sm-12 game-card" data-category="<?php echo $game['game_category_id']; ?>">
                                        <div class="box box-widget widget-user-2">
                                            <div class="widget-user-header bg-<?php echo $game['difficulty_level'] == 'Easy' ? 'green' : ($game['difficulty_level'] == 'Medium' ? 'yellow' : 'red'); ?>">
                                                <div class="widget-user-image">
                                                    <?php if (!empty($game['game_image'])) { ?>
                                                        <img class="img-circle" src="<?php echo base_url('uploads/games/' . $game['game_image']); ?>" alt="Game Image">
                                                    <?php } else { ?>
                                                        <span class="fa fa-gamepad img-circle bg-white" style="padding: 10px; color: #333;"></span>
                                                    <?php } ?>
                                                </div>
                                                <h3 class="widget-user-username"><?php echo $game['title']; ?></h3>
                                                <h5 class="widget-user-desc"><?php echo $game['category_name']; ?> - <?php echo $game['subject_name']; ?></h5>
                                            </div>
                                            <div class="box-footer no-padding">
                                                <ul class="nav nav-stacked">
                                                    <li><a href="#">Difficulty <span class="pull-right badge bg-<?php echo $game['difficulty_level'] == 'Easy' ? 'green' : ($game['difficulty_level'] == 'Medium' ? 'yellow' : 'red'); ?>"><?php echo $game['difficulty_level']; ?></span></a></li>
                                                    <li><a href="#">Questions <span class="pull-right badge bg-blue"><?php echo $game['total_questions']; ?></span></a></li>
                                                    <li><a href="#">Time Limit <span class="pull-right badge bg-gray"><?php echo $game['time_limit']; ?> min</span></a></li>
                                                    <?php if (!empty($game['best_score'])) { ?>
                                                        <li><a href="#">Your Best <span class="pull-right badge bg-green"><?php echo $game['best_score']; ?>%</span></a></li>
                                                    <?php } ?>
                                                </ul>
                                                <div class="row" style="padding: 10px;">
                                                    <div class="col-xs-6">
                                                        <a href="<?php echo base_url('user/student_games/play/' . $game['id']); ?>" class="btn btn-primary btn-block">
                                                            <i class="fa fa-play"></i> Play Game
                                                        </a>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <button class="btn btn-default btn-block toggle-favorite" data-game-id="<?php echo $game['id']; ?>">
                                                            <i class="fa fa-heart<?php echo $game['is_favorite'] ? '' : '-o'; ?>"></i>
                                                            <?php echo $game['is_favorite'] ? 'Favorited' : 'Favorite'; ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> No games available for your class yet. Check back later!
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Favorite Games -->
                <?php if (!empty($favorite_games)) { ?>
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-heart"></i> Favorite Games</h3>
                        </div>
                        <div class="box-body">
                            <?php foreach ($favorite_games as $favorite) { ?>
                                <div class="media">
                                    <div class="media-left">
                                        <?php if (!empty($favorite['game_image'])) { ?>
                                            <img src="<?php echo base_url('uploads/games/' . $favorite['game_image']); ?>" class="media-object" style="width: 40px; height: 40px;">
                                        <?php } else { ?>
                                            <div class="media-object" style="width: 40px; height: 40px; background: #f4f4f4; text-align: center; line-height: 40px;">
                                                <i class="fa fa-gamepad"></i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="media-heading">
                                            <a href="<?php echo base_url('user/student_games/play/' . $favorite['id']); ?>">
                                                <?php echo $favorite['title']; ?>
                                            </a>
                                        </h6>
                                        <small><?php echo $favorite['category_name']; ?></small>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0;">
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Recent Activities -->
                <?php if (!empty($recent_activities)) { ?>
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-clock-o"></i> Recent Activities</h3>
                        </div>
                        <div class="box-body">
                            <?php foreach ($recent_activities as $activity) { ?>
                                <div class="media">
                                    <div class="media-body">
                                        <h6 class="media-heading">
                                            Played <?php echo $activity['game_title']; ?>
                                        </h6>
                                        <small>Score: <?php echo $activity['score']; ?>% - <?php echo timespan(strtotime($activity['completed_at']), time()); ?> ago</small>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0;">
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Category filter
    $('#filter_category').on('change', function() {
        var categoryId = $(this).val();
        
        if (categoryId === '') {
            $('.game-card').show();
        } else {
            $('.game-card').hide();
            $('.game-card[data-category="' + categoryId + '"]').show();
        }
    });

    // Toggle favorite
    $('.toggle-favorite').on('click', function() {
        var gameId = $(this).data('game-id');
        var button = $(this);
        
        $.ajax({
            url: '<?php echo base_url("user/student_games/toggle_favorite"); ?>',
            type: 'POST',
            data: {
                game_id: gameId,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (response.is_favorite) {
                        button.html('<i class="fa fa-heart"></i> Favorited');
                    } else {
                        button.html('<i class="fa fa-heart-o"></i> Favorite');
                    }
                    
                    // Show success message
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });
});
</script>
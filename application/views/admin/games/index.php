<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-gamepad"></i> <?php echo $this->lang->line('educational_games'); ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('games_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('games', 'can_add')) { ?>
                                <a href="<?php echo base_url(); ?>admin/games/create" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_game'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg'); ?>
                        <?php } ?>

                        <!-- Filters -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Category</label>
                                    <select class="form-control" id="filter_category">
                                        <option value="">All Categories</option>
                                        <?php foreach ($categories as $category) { ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Class</label>
                                    <select class="form-control" id="filter_class">
                                        <option value="">All Classes</option>
                                        <?php foreach ($classlist as $class) { ?>
                                            <option value="<?php echo $class['id']; ?>"><?php echo $class['class']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Games Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Game</th>
                                        <th>Category</th>
                                        <th>Class/Section</th>
                                        <th>Subject</th>
                                        <th>Difficulty</th>
                                        <th>Total Plays</th>
                                        <th>Avg Score</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($games)) { ?>
                                        <?php foreach ($games as $game) { ?>
                                            <tr>
                                                <td>
                                                    <div class="media">
                                                        <?php if (!empty($game['game_image'])) { ?>
                                                            <div class="media-left">
                                                                <img src="<?php echo base_url('uploads/games/' . $game['game_image']); ?>" 
                                                                     class="media-object" style="width: 50px; height: 50px; object-fit: cover;">
                                                            </div>
                                                        <?php } ?>
                                                        <div class="media-body">
                                                            <h5 class="media-heading"><?php echo $game['title']; ?></h5>
                                                            <small><?php echo substr($game['description'], 0, 50) . '...'; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <i class="<?php echo $game['category_icon']; ?>"></i> 
                                                    <?php echo $game['category_name']; ?>
                                                </td>
                                                <td><?php echo $game['class'] . ' - ' . $game['section']; ?></td>
                                                <td><?php echo $game['subject_name']; ?></td>
                                                <td>
                                                    <span class="label label-<?php 
                                                        echo $game['difficulty_level'] == 'Easy' ? 'success' : 
                                                            ($game['difficulty_level'] == 'Medium' ? 'warning' : 'danger'); 
                                                    ?>">
                                                        <?php echo $game['difficulty_level']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $game['total_plays']; ?></td>
                                                <td><?php echo number_format($game['average_score'], 1); ?>%</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="<?php echo base_url('admin/games/view/' . $game['id']); ?>" 
                                                           class="btn btn-default btn-xs" title="View Details">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo base_url('admin/games/preview/' . $game['id']); ?>" 
                                                           class="btn btn-info btn-xs" title="Preview Game" target="_blank">
                                                            <i class="fa fa-play"></i>
                                                        </a>
                                                        <?php if ($this->rbac->hasPrivilege('games', 'can_edit')) { ?>
                                                            <a href="<?php echo base_url('admin/games/edit/' . $game['id']); ?>" 
                                                               class="btn btn-primary btn-xs" title="Edit">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($this->rbac->hasPrivilege('games', 'can_delete')) { ?>
                                                            <a href="<?php echo base_url('admin/games/delete/' . $game['id']); ?>" 
                                                               class="btn btn-danger btn-xs confirm-delete" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No games found</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('.example').DataTable({
        responsive: true,
        searching: true,
        lengthChange: true,
        ordering: true
    });

    // Filter functionality
    $('#filter_category, #filter_class').on('change', function() {
        // Implement AJAX filtering if needed
    });

    // Confirm delete
    $('.confirm-delete').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this game!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if (isConfirm) {
                window.location.href = url;
            } else {
                swal("Cancelled", "The game is safe :)", "error");
            }
        });
    });
});
</script>
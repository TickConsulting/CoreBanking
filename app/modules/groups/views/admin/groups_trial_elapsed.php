<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                   <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">
               <?php if($posts):?>
                    <div class="clearfix">
                        <a href="<?php echo site_url("admin/groups/groups_trial_elapsed?generate_excel=1") ?>" class="btn btn-xs blue pull-right">Export</a>
                        <br/><br/>
                    </div>
                    <?php echo form_open('admin/groups/action', ' id="form"  class="form-horizontal"'); ?> 
                        <?php if ( ! empty($pagination['links'])): ?>

                                <div class="row col-md-12">
                                <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Investment Groups</p>
                                <?php 
                                    echo '<div class ="top-bar-pagination">';
                                    echo $pagination['links']; 
                                    echo '</div></div>';
                                    endif; 
                                ?> 

                                <table class="table table-bordered table-condensed table-striped table-hover table-searchable">
                                    <thead>
                                        <tr>
                                            <th width="8px">
                                                #
                                            </th>
                                            <th width="25%">
                                              Sign Up Date
                                            </th>
                                            <th width="25%">
                                              Trial Expiry Date
                                            </th>
                                            <th width="25%">
                                              Group Name
                                            </th>
                                            <th>
                                              Contact
                                            </th>
                                            <th>
                                              Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=$this->uri->segment(5, 0);
                                        foreach($posts as $post):
                                        ?>
                                            <tr>
                                                <td><?php echo $i+1;?></td>
                                                <td><?php echo timestamp_to_date($post->created_on); ?></td>
                                                <td><?php echo timestamp_to_date($post->trial_days_end_date); ?> </td>
                                                <td><?php echo $post->name; ?></td>
                                                <td><?php echo $user_options[$post->owner]; ?></td>
                                                <td class="actions">
                                                    <a href="<?php echo site_url('admin/groups/edit/'.$post->id);?>" class="btn btn-xs default">
                                                        <i class="fa fa-edit"></i> Edit &nbsp;&nbsp;
                                                    </a>
                                                    <a href="<?php echo site_url('admin/groups/view/'.$post->id);?>" class="btn btn-xs btn-primary">
                                                        <i class="fa fa-eye"></i> View &nbsp;&nbsp;
                                                    </a>
                                                    <a target="_blank" href="<?php echo site_url('admin/groups/login_as_admin/'.$post->id); ?>" class="btn btn-xs btn-default">
                                                        <i class="fa fa-user-secret"></i> Login as Admin &nbsp;&nbsp;
                                                    </a>
                                                    <a href="<?php echo site_url('admin/groups/delete/'.$post->id);?>" class="btn prompt_confirmation_message_link btn-xs btn-danger" data-title="Enter the delete code to delete the group and its data permanently." >
                                                        <i class="fa fa-trash"></i> Delete Group &nbsp;&nbsp;
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php $i++; endforeach; 
                                        ?>
                                    </tbody>
                                </table>
                                <div class="clearfix"></div>
                                <div class="row col-md-12">
                                <?php 
                                    if( ! empty($pagination['links'])): 
                                    echo $pagination['links']; 
                                    endif; 
                                ?>  
                                </div>
                                <div class="clearfix"></div>
                    <?php echo form_close();?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                           There are no group's records to display
                        </p>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
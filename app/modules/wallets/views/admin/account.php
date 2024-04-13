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
               <?php if($post):?>
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
                                           
                                            <th>
                                              Account Name
                                            </th>
                                           
                                            <th>
                                              Balance (KES)
                                            </th>
                                            <th>
                                              Active
                                            </th>
                                            <th>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=$this->uri->segment(5, 0);
                                        ?>
                                            <tr>
                                                <td><?php echo $i+1;?></td>
                                                <td><?php echo $post->account_name;?></td>
                                                <td><?php echo number_to_currency($post->current_balance);?></td>
                                                <td><?php if($post->active==1)
                                            {
                                                echo '<span class="label label-sm label-primary"> Active</spam>';
                                            }else{
                                                echo '<span class="label label-sm label-danger"> Inactive</spam>';

                                                }?></td>
                                                <td class="actions">
                                                    <a href="<?php echo site_url('admin/languages/edit/'.$post->id);?>" class="btn btn-xs default">
                                                        <i class="fa fa-edit"></i> Edit &nbsp;&nbsp;
                                                    </a>
                                                    
                                                    
                                                    <a href="<?php echo site_url('admin/languages/delete/'.$post->id);?>" class="btn prompt_confirmation_message_link btn-xs btn-danger" data-title="Enter the delete code to delete the language and its data permanently." >
                                                        <i class="fa fa-trash"></i> Delete Language &nbsp;&nbsp;
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php 
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
                           There are no language records to display
                        </p>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
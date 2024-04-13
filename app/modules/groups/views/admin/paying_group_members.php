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
                    <?php echo form_open('admin/groups/action', ' id="form"  class="form-horizontal"'); ?> 

                        <div class="clearfix">
                            <a target="_blank" href="<?php echo site_url("admin/groups/paying_group_members?generate_excel=1") ?>" class="btn btn-xs blue pull-right">Export</a>
                            <br/><br/>
                        </div>
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

                                            <th width="">
                                              Group Name
                                            </th>
                                            <th>
                                              Sign Up Date
                                            </th>
                                            <th>
                                              Last Visit Date
                                            </th>
                                            <th>
                                              Member Name
                                            </th>
                                            <th>
                                              Phone
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=$this->uri->segment(4, 0);
                                        foreach($posts as $post):
                                        ?>
                                            <tr>
                                                <td><?php echo $i+1;?></td>
                                                <td><?php echo $group_options[$post->group_id]; ?></td>
                                                <td><?php echo timestamp_to_date($post->created_on); ?></td>
                                                <td>
                                                    <?php 
                                                    if($post->last_login){
                                                        echo timestamp_to_date($post->last_login); 
                                                    }else{
                                                        echo "Never logged in";
                                                    }

                                                    ?>        
                                                </td>
                                                <td><?php echo $post->first_name." ".$post->last_name; ?></td>
                                                <td><?php echo $post->phone; ?></td>
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
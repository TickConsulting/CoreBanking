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
            <?php echo form_open('admin/emails/action', ' id="form"  class="form-horizontal"'); ?> 

            <?php if ( ! empty($pagination['links'])): ?>
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Users</p>
                <?php 
                echo '<div class ="top-bar-pagination">';
                echo $pagination['links']; 
                echo '</div></div>';
                endif; 
            ?> 
                        
                
                <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed table-searchable">
                    <thead>
                        <tr>

                            <th width="8px">
                                <input type="checkbox" name="check" value="all" class="check_all">
                            </th>
                            <th width="8px">
                                #
                            </th>
                            <th>
                                Group Name
                            </th>
                            <th>
                               Sent To
                            </th>
                            <th>
                                Subject
                            </th>
                            <th>
                                Email From
                            </th>

                            <th>
                                Sent time
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
                                <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                
                                <td><?php echo $i+1;?></td>
                                <td><?php if($post->group_id){echo $groups[$post->group_id];}else{echo 'No Group';}?></td>
                                <td><?php
                                    $email_to = explode(',', $post->email_to);
                                     if(is_array($email_to)){
                                        for ($j=0;$j<count($email_to); $j++){ 
                                            echo $email_to[$j].'<br/>';
                                        }
                                     }else{
                                        echo $post->email_to;
                                     }
                                    ?>
                                </td>
                                <td><?php echo $post->subject;?></td>
                                <td><?php echo $post->email_from;?></td>
                                <td><?php echo timestamp_to_date($post->created_on);?></td>

                                <td >
                                    <a href="<?php echo site_url('admin/emails/view_queued/'.$post->id);?>" class="btn btn-xs default">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <a href="<?php echo site_url('admin/emails/delete/'.$post->id);?>" class="btn btn-xs btn-danger confirmation_link" ><i class="fa fa-trash"></i> Delete</a>

                                </td>
                            </tr>
                        <?php $i++; endforeach; 
                        ?>
                    </tbody>
                </table>

                <div class="clearfix"></div>
                <?php if($posts):?>
                    <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_delete' data-toggle="confirmation" data-placement="top"> <i class='fa fa-trash-o'></i> Bulk Delete</button>
                <?php endif;?>
                <div class="row col-md-12">
                <?php 
                    if( ! empty($pagination['links'])): 
                    echo $pagination['links']; 
                    endif; 
                ?>  
                </div>
                <div class="clearfix"></div>
                
                
                <?php echo form_close();?>
            <?php else:?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                           No queued emails to display
                        </p>
                    </div>
                <?php endif;?>

            </div>

        </div>



    </div>

</div>

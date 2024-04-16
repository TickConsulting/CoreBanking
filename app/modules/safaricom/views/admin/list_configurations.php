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
                <?php if(empty($posts)){ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No access token records to display.
                        </p>
                    </div>
                <?php }else{ ?>
                    <?php echo form_open('admin/safaricom/action', ' id="form"  class="form-horizontal"'); ?>
                        <?php if ( ! empty($pagination['links'])): ?>
                            <div class="row col-md-12">
                                <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Access Tokens</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-condensed ">
                                <thead>
                                    <tr>
                                        <th width='2%'>
                                             <input type="checkbox" name="check" value="all" class="check_all">
                                        </th>
                                        <th width='2%'>
                                            #
                                        </th>
                                        <th>
                                            Shortcode
                                        </th>
                                        <th>
                                            Username
                                        </th>
                                        <th>
                                            Password
                                        </th>
                                         <th>
                                            Header Authorization
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                        <th width="">
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
                                            <td><?php echo $post->shortcode; ?></td>
                                            <td><?php echo $post->username; ?></td>
                                            <td><?php echo $post->password; ?></td>
                                            <td><?php echo $post->api_key; ?></td>
                                            <td>
                                                <?php
                                                    if($post->active){
                                                        echo "<span class='label label-xs label-success'>Active</span>";
                                                    }else{
                                                        echo "<span class='label label-xs label-info'>Deactivated</span>";
                                                    }
                                                    echo '&nbsp;&nbsp;';
                                                    if($post->is_default){
                                                        echo "<span class='label label-xs label-primary'>Default</span>";
                                                    }

                                                ?>
                                            </td>
                                            <td>
                                                
                                                <?php
                                                    echo "<a class='btn blue btn-xs' href='".site_url('admin/safaricom/edit_configuration/'.$post->id)."'><i class='fa fa-edit'></i> Edit </a>";
                                                    echo '&nbsp;&nbsp;';
                                                    echo "<a class='btn red btn-xs confirmation_link' href='".site_url('admin/safaricom/delete_configuration/'.$post->id)."'><i class='icon-trash'></i> Delete </a>";
                                                ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        $i++;
                                        endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="clearfix"></div>
                        <div class="row col-md-12">
                        <?php 
                            if( ! empty($pagination['links'])): 
                            echo $pagination['links']; 
                            endif; 
                        ?>  
                        </div>
                        <div class="clearfix"></div>
                        <?php if($posts):?>
                            <button class="btn btn-sm btn-primary confirmation_bulk_action" name='btnAction' value='setDefault' data-toggle="confirmation" data-placement="top"> <i class='icon-check'></i> Set Default</button>
                        <?php endif; ?>  
                    <?php echo form_close(); ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
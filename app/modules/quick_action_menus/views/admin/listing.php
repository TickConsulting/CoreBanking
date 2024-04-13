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
               <?php if(!empty($posts)){ ?>
                    <?php echo form_open('admin/quick_action_menus/action', ' id="form"  class="form-horizontal"'); ?> 

                     <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed">
                            <thead>
                                <tr>
                                    <th width='2%'>
                                         <input type="checkbox" name="check" value="all" class="check_all">
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        URL
                                    </th>
                                    <th>
                                        Icon
                                    </th>
                                    <th>
                                        Parent Menu
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($posts as $post): ?>
                                    <tr>
                                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                        <td><?php echo $post->name; ?></td>
                                        <td><?php echo $post->url; ?></td>
                                        <td><i class="<?php echo $post->icon; ?>"></i></td>
                                        <td><?php echo isset($side_bar_menu_options[$post->parent_id])?$side_bar_menu_options[$post->parent_id]:'--'; ?></td>
                                        <td><?php echo $post->active==1?'<span class="label label-sm label-success">Active</span>':'<span class="label label-sm label-default">Hidden</span>'; ?></td>
                                        <td>
                                            <a href="<?php echo site_url('admin/quick_action_menus/edit/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            <?php if($post->active){ ?>
                                                <a href="<?php echo site_url('admin/quick_action_menus/hide/'.$post->id); ?>" class="btn btn-xs white confirmation_link">
                                                    <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
                                                </a>
                                            <?php }else{ ?>
                                                <a href="<?php echo site_url('admin/quick_action_menus/activate/'.$post->id); ?>" class="btn btn-xs green confirmation_link">
                                                    <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
                                                </a>
                                            <?php } ?>
                                            <a href="<?php echo site_url('admin/quick_action_menus/delete/'.$post->id); ?>" class="btn btn-xs red confirmation_link">
                                                <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
                                            </a>
                                        </td>
                                    </tr>
                                    <?php

                                    $children = $this->quick_action_menus_m->get_children_links($post->id);
                                        if(!empty($children)):
                                        foreach($children as $child):
                                    ?>
                                        <tr>
                                            <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $child->id; ?>" /></td>
                                            <td><?php echo $child->name; ?></td>
                                            <td><?php echo $child->url; ?></td>
                                            <td><i class="<?php echo $child->icon; ?>"></i></td>
                                            <td><?php echo isset($side_bar_menu_options[$child->parent_id])?$side_bar_menu_options[$child->parent_id]:'--'; ?></td>
                                            <td><?php echo $child->active==1?'<span class="label label-sm label-success">Active</span>':'<span class="label label-sm label-default">Hidden</span>'; ?></td>
                                            <td>
                                                <a href="<?php echo site_url('admin/quick_action_menus/edit/'.$child->id); ?>" class="btn btn-xs default">
                                                    <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                                </a>
                                                <?php if($post->active){ ?>
                                                    <a href="<?php echo site_url('admin/quick_action_menus/hide/'.$child->id); ?>" class="btn btn-xs white confirmation_link">
                                                        <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
                                                    </a>
                                                <?php }else{ ?>
                                                    <a href="<?php echo site_url('admin/quick_action_menus/activate/'.$child->id); ?>" class="btn btn-xs green confirmation_link">
                                                        <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
                                                    </a>
                                                <?php } ?>
                                                <a href="<?php echo site_url('admin/quick_action_menus/delete/'.$child->id); ?>" class="btn btn-xs red confirmation_link">
                                                    <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
                                                </a>
                                            </td>
                                        </tr>
                                        <?php

                                        $grand_children = $this->quick_action_menus_m->get_children_links($child->id);
                                        if(!empty($grand_children)):
                                            foreach($grand_children as $grand_child):
                                        ?>
                                    <tr>
                                            <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $grand_child->id; ?>" /></td>
                                            <td><?php echo $grand_child->name; ?></td>
                                            <td><?php echo $grand_child->url; ?></td>
                                            <td><i class="<?php echo $grand_child->icon; ?>"></i></td>
                                            <td><?php echo isset($side_bar_menu_options[$grand_child->parent_id])?$side_bar_menu_options[$grand_child->parent_id]:'--'; ?></td>
                                            <td><?php echo $grand_child->active==1?'<span class="label label-sm label-success">Active</span>':'<span class="label label-sm label-default">Hidden</span>'; ?></td>
                                            <td>
                                                <a href="<?php echo site_url('admin/quick_action_menus/edit/'.$grand_child->id); ?>" class="btn btn-xs default">
                                                    <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                                </a>
                                                <?php if($grand_child->active){ ?>
                                                    <a href="<?php echo site_url('admin/quick_action_menus/hide/'.$grand_child->id); ?>" class="btn btn-xs white confirmation_link">
                                                        <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
                                                    </a>
                                                <?php }else{ ?>
                                                    <a href="<?php echo site_url('admin/quick_action_menus/activate/'.$grand_child->id); ?>" class="btn btn-xs green confirmation_link">
                                                        <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
                                                    </a>
                                                <?php } ?>
                                                <a href="<?php echo site_url('admin/quick_action_menus/delete/'.$grand_child->id); ?>" class="btn btn-xs red confirmation_link">
                                                    <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
                                                </a>
                                            </td>
                                        </tr>

                                    <?php 
                                    endforeach;
                                    endif;
                                    endforeach;
                                    endif;
                                    endforeach; ?>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                        <?php if($posts):?>
                            <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_delete' data-toggle="confirmation" data-placement="top"> <i class='icon-trash'></i> Bulk Delete</button>
                        <?php endif;?>
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No User Quick Action Menus to display.
                        </p>
                    </div>
                <?php } ?>

            </div>

        </div>



    </div>

</div>








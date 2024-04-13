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
            <?php echo form_open('admin/setup_tasks/action', ' id="form"  class="form-horizontal"'); ?> 
                
                <table class="table table-searchable table-bordered table-condensed table-striped table-hover table-header-fixed">
                    <thead>
                        <tr>
                            <th width='2%'>
                                 <input type="checkbox" name="check" value="all" class="check_all">
                            </th>
                            <th>
                                #
                            </th>
                            <th>
                               Name
                            </th>
                            <th>
                                Slug 
                            </th>
                            <th>
                                Icon 
                            </th>
                            <th>
                                Call to Action Name
                            </th>
                            <th>
                                Call to Action Link
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i=$this->uri->segment(5, 0);
                        foreach($posts as $post):
                        ?>
                            <tr>
                                <td>
                                    <input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" />
                                </td>
                                <td><?php echo $i+1;?></td>
                                <td><?php echo $post->name;?></td>
                                <td><?php echo $post->slug;?></td>
                                <td><i class='<?php echo $post->icon;?>'></i></td>
                                <td><?php echo $post->call_to_action_name;?></td>
                                <td><?php echo $post->call_to_action_link;?></td>
                                <td>
                                    <a href="<?php echo site_url('admin/setup_tasks/edit/'.$post->id); ?>" class="btn btn-xs default">
                                        <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                    </a>
                                    <?php if($post->active){ ?>
                                        <a href="<?php echo site_url('admin/setup_tasks/hide/'.$post->id); ?>" class="btn btn-xs white confirmation_link">
                                            <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
                                        </a>
                                    <?php }else{ ?>
                                        <a href="<?php echo site_url('admin/setup_tasks/activate/'.$post->id); ?>" class="btn btn-xs green confirmation_link">
                                            <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
                                        </a>
                                    <?php } ?>
                                    <a href="<?php echo site_url('admin/setup_tasks/delete/'.$post->id); ?>" class="btn btn-xs red confirmation_link">
                                        <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
                                    </a>
                                </td>
                            </tr>
                        <?php $i++; endforeach; 
                        ?>
                    </tbody>
                </table>

                <div class="clearfix"></div>

                <?php if($posts):?>
                    <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_delete' data-toggle="confirmation" data-placement="top"> <i class='icon-trash'></i> Bulk Delete</button>
                <?php endif;?>
                
                <?php echo form_close();?>
            <?php else:?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                           No Setup Tasks to display
                        </p>
                    </div>
                <?php endif;?>

            </div>

        </div>



    </div>

</div>

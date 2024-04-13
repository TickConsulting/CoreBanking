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
                    <?php echo form_open('admin/themes/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Themes</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
                         <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed">
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
                                        Color Pallete
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th width="30%">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo $post->name; ?></td>
                                        <td><?php echo $post->slug; ?></td>
                                        <td>
                                            <strong>Background colors:</strong>
                                            <span class='label tooltips' data-placement="top" data-original-title="Primary Color: <?php echo $post->primary_background_color; ?>" style='background:<?php echo $post->primary_background_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Secondary Color: <?php echo $post->secondary_background_color; ?>" style='background:<?php echo $post->secondary_background_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Tertiary Color: <?php echo $post->tertiary_background_color; ?>" style='background:<?php echo $post->tertiary_background_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Quaternary Color: <?php echo $post->quaternary_background_color; ?>" style='background:<?php echo $post->quaternary_background_color; ?>;'> &nbsp;</span>
                                            <br/><br/>
                                            <strong>Text colors:</strong>
                                            <span class='label tooltips' data-placement="top" data-original-title="Primary Color: <?php echo $post->primary_text_color; ?>" style='background:<?php echo $post->primary_text_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Secondary Color: <?php echo $post->secondary_text_color; ?>" style='background:<?php echo $post->secondary_text_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Tertiary Color: <?php echo $post->tertiary_text_color; ?>" style='background:<?php echo $post->tertiary_text_color; ?>;'> &nbsp;</span>
                                            <br/><br/>
                                            <strong>Border colors:</strong>
                                            <span class='label tooltips' data-placement="top" data-original-title="Primary Color: <?php echo $post->primary_border_color; ?>" style='background:<?php echo $post->primary_border_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Secondary Color: <?php echo $post->secondary_border_color; ?>" style='background:<?php echo $post->secondary_border_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Tertiary Color: <?php echo $post->tertiary_border_color; ?>" style='background:<?php echo $post->tertiary_border_color; ?>;'> &nbsp;</span>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->active){
                                                    echo "<span class='label label-success'>Active</span>";
                                                }else{
                                                    echo "<span class='label label-default'>Hidden</span>";
                                                }

                                                if($post->default_theme){
                                                    echo "<span class='margin-left-5 label label-info'>Default</span>";
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('admin/themes/edit/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            <?php if($post->active){ ?>
                                                <a href="<?php echo site_url('admin/themes/hide/'.$post->id); ?>" class="btn btn-xs default confirmation_link">
                                                    <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
                                                </a>
                                            <?php }else{ ?>
                                                <a href="<?php echo site_url('admin/themes/activate/'.$post->id); ?>" class="btn btn-xs green confirmation_link">
                                                    <i class="icon-eye"></i> Activate &nbsp;&nbsp; 
                                                </a>
                                            <?php } ?>
                                            <a href="<?php echo site_url('admin/themes/delete/'.$post->id); ?>" class="btn btn-xs red confirmation_link">
                                                <i class="fa fa-trash"></i> Delete &nbsp;&nbsp; 
                                            </a>

                                            <?php 
                                                if($post->default_theme){
                                                }else{
                                            ?>
                                            <a href="<?php echo site_url('admin/themes/set_as_default/'.$post->id); ?>" class="btn btn-xs green confirmation_link">
                                                <i class="fa fa-check"></i> Set as Default &nbsp;&nbsp; 
                                            </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php $i++;
                                    endforeach; ?>
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

                        <div class="clearfix"></div>
                        <?php if($posts):?>
                            <button class="btn btn-sm btn-success confirmation_bulk_action" name='btnAction' value='bulk_activate' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye'></i> Bulk Activate</button>
                            <button class="btn btn-sm btn-default confirmation_bulk_action" name='btnAction' value='bulk_hide' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye-slash'></i> Bulk Hide</button>
                            <button class="btn btn-sm red confirmation_bulk_action" name='btnAction' value='bulk_delete' data-toggle="confirmation" data-placement="top"> <i class='fa fa-trash'></i> Bulk Delete</button>
                        <?php endif;?>
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No themes to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
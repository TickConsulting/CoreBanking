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
                    <?php echo form_open('admin/saccos/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Saccos</p>
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
                                        Country
                                    </th>
                                    <th>
                                        Color Pallete
                                    </th>
                                    <th>
                                        Partner
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
                                        <td><?php
                                            if(array_key_exists($post->country_id, $country_options)){
                                                echo $country_options[$post->country_id];
                                            }else{
                                                echo "<span class='label label-info'>Country not set</span>";  
                                            } 
                                            ?>                                            
                                        </td>
                                        <td>
                                            <span class='label tooltips' data-placement="top" data-original-title="Primary Color: <?php echo $post->primary_color; ?>" style='background:<?php echo $post->primary_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Secondary Color: <?php echo $post->secondary_color; ?>" style='background:<?php echo $post->secondary_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Tertiary Color: <?php echo $post->tertiary_color; ?>" style='background:<?php echo $post->tertiary_color; ?>;'> &nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Text Color: <?php echo $post->text_color; ?>" style='background:<?php echo $post->text_color; ?>;' >&nbsp;</span>
                                        </td>
                                        <td>
                                            <?php 
                                            if($post->partner){
                                                echo "<span class='label label-success'>Partner</span>";
                                            }else{
                                                echo "<span class='label label-info'>Non Partner</span>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->active){
                                                    echo "<span class='label label-success'>Active</span>";
                                                }else{
                                                    echo "<span class='label label-default'>Hidden</span>";
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('admin/saccos/edit/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            <?php if($post->active){ ?>
                                                <a href="<?php echo site_url('admin/saccos/hide/'.$post->id); ?>" class="btn btn-xs default confirmation_link">
                                                    <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
                                                </a>
                                            <?php }else{ ?>
                                                <a href="<?php echo site_url('admin/saccos/activate/'.$post->id); ?>" class="btn btn-xs green confirmation_link">
                                                    <i class="icon-eye"></i> Activate &nbsp;&nbsp; 
                                                </a>
                                            <?php } ?>
                                            <a href="<?php echo site_url('admin/sacco_branches/create/'.$post->id); ?>" class="btn btn-xs green">
                                                <i class="fa fa-plus-square-o"></i> Create Branch &nbsp;&nbsp; 
                                            </a>

                                            <a href="<?php echo site_url('admin/sacco_branches/import/'.$post->id); ?>" class="btn btn-xs blue">
                                                <i class="fa fa-cloud-upload"></i> Import Branches &nbsp;&nbsp; 
                                            </a>
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
                        <?php endif;?>
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No Saccos to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
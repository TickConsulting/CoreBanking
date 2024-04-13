<div class="row">
	<div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
               	<div class="caption">
                   <?php echo $this->menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">
				<?php if(!empty($posts)){ ?>
			        <?php echo form_open('admin/bank_menus/action', ' id="form"  class="form-horizontal"'); ?> 
			            <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed table-searchable">
		                    <thead>
		                        <tr>
		                            <th width='2%'>
		                                 <input type="checkbox" name="check" value="all" class="check_all">
		                            </th>
		                            <th>
		                                Name
		                            </th>
		                            <th>
		                                URLS
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
		                            <th width="30%">
		                                Actions
		                            </th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                        <?php foreach($posts as $post): ?>
		                            <tr>
		                                <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
		                                <td><?php if(preg_match('/]/',$post->name)){ echo str_replace('_', ' ', $post->name);}else{echo $post->name;} ?></td>
		                                <td>
		                                	<p>
		                                		Menu URL: <?php echo $post->url; ?>
		                                	</p>
		                                	<p>
			                                	Help Menu URL: <?php echo $post->help_url?:'Not Set'; ?>
		                                	</p>
		                                </td>
		                                <td><i class="<?php echo $post->icon; ?>"></i></td>
		                                <td><?php echo isset($side_bar_menu_options[$post->parent_id])?$side_bar_menu_options[$post->parent_id]:'--'; ?></td>
		                                <td><?php echo $post->active==1?'<span class="label label-sm label-success">Active</span>':'<span class="label label-sm label-default">Hidden</span>'; ?></td>
		                                <td>
		                                    <a href="<?php echo site_url('admin/bank_menus/edit/'.$post->id); ?>" class="btn btn-xs default">
		                                        <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
		                                    </a>
		                                    <?php if($post->active){ ?>
		                                        <a href="<?php echo site_url('admin/bank_menus/hide/'.$post->id); ?>" class="btn btn-xs white">
		                                            <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
		                                        </a>
		                                    <?php }else{ ?>
		                                        <a href="<?php echo site_url('admin/bank_menus/activate/'.$post->id); ?>" class="btn btn-xs green">
		                                            <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
		                                        </a>
		                                    <?php } ?>
		                                    <a href="<?php echo site_url('admin/bank_menus/delete/'.$post->id); ?>" class="btn btn-xs red confirmation_link">
		                                        <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
		                                    </a>
		                                </td>
		                            </tr>
		                            <?php

		                            $children = $this->bank_menus_m->get_children_links($post->id);
		                                if(!empty($children)):
		                                foreach($children as $child):
		                            ?>
		                                <tr>
		                                    <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $child->id; ?>" /></td>
		                                    <td><?php if(preg_match('/]/',$child->name)){ echo str_replace('_', ' ', $child->name);}else{echo $child->name;} ?></td>
		                                    <td>
		                                    	<p>
			                                		Menu URL: <?php echo $child->url; ?>
			                                	</p>
			                                	<p>
				                                	Help Menu URL: <?php echo $child->help_url?:'Not Set'; ?>
			                                	</p>
			                               </td>
		                                    <td><i class="<?php echo $child->icon; ?>"></i></td>   
		                                    <td>
		                                    <?php if(isset($side_bar_menu_options[$child->parent_id])){
		                                    	if(preg_match('/]/', $side_bar_menu_options[$child->parent_id]))
		                                    		echo str_replace('_',' ', $side_bar_menu_options[$child->parent_id]);
		                                    	}else{
		                                    		echo '--';
		                                    		}?>
		                                  </td>
		                                    <td><?php echo $child->active==1?'<span class="label label-sm label-success">Active</span>':'<span class="label label-sm label-default">Hidden</span>'; ?></td>
		                                    <td>
		                                        <a href="<?php echo site_url('admin/bank_menus/edit/'.$child->id); ?>" class="btn btn-xs default">
		                                            <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
		                                        </a>
		                                        <?php if($child->active){ ?>
		                                            <a href="<?php echo site_url('admin/bank_menus/hide/'.$child->id); ?>" class="confirmation_link btn btn-xs white">
		                                                <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
		                                            </a>
		                                        <?php }else{ ?>
		                                            <a href="<?php echo site_url('admin/bank_menus/activate/'.$child->id); ?>" class=" confirmation_link btn btn-xs green">
		                                                <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
		                                            </a>
		                                        <?php } ?>
		                                        <a href="<?php echo site_url('admin/bank_menus/delete/'.$child->id); ?>" class="btn btn-xs red confirmation_link">
		                                            <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
		                                        </a>
		                                    </td>
		                                </tr>
		                                <?php

		                                $grand_children = $this->bank_menus_m->get_children_links($child->id);
		                                if(!empty($grand_children)):
		                                    foreach($grand_children as $grand_child):
		                                ?>
		                            <tr>
		                                    <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $grand_child->id; ?>" /></td>
		                                    <td><?php echo $grand_child->name; ?></td>
		                                    <td>
		                                    	<p>
			                                		Menu URL: <?php echo $grand_child->url; ?>
			                                	</p>
			                                	<p>
				                                	Help Menu URL: <?php echo $grand_child->help_url?:'Not Set'; ?>
			                                	</p>
		                                    </td>
		                                    <td><i class="<?php echo $grand_child->icon; ?>"></i></td>
		                                    <td><?php echo isset($side_bar_menu_options[$grand_child->parent_id])?$side_bar_menu_options[$grand_child->parent_id]:'--'; ?></td>
		                                    <td><?php echo $grand_child->active==1?'<span class="label label-sm label-success">Active</span>':'<span class="label label-sm label-default">Hidden</span>'; ?></td>
		                                    <td>
		                                        <a href="<?php echo site_url('admin/bank_menus/edit/'.$grand_child->id); ?>" class="btn btn-xs default">
		                                            <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
		                                        </a>
		                                        <?php if($grand_child->active){ ?>
		                                            <a href="<?php echo site_url('admin/bank_menus/hide/'.$grand_child->id); ?>" class="btn btn-xs white confirmation_link">
		                                                <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
		                                            </a>
		                                        <?php }else{ ?>
		                                            <a href="<?php echo site_url('admin/bank_menus/activate/'.$grand_child->id); ?>" class="btn btn-xs green confirmation_link">
		                                                <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
		                                            </a>
		                                        <?php } ?>
		                                        <a href="<?php echo site_url('admin/bank_menus/delete/'.$grand_child->id); ?>" class="btn btn-xs red confirmation_link ">
		                                            <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
		                                        </a>
		                                    </td>
		                                </tr>

		                            <?php 
		                            	$great_grand_children = $this->bank_menus_m->get_children_links($grand_child->id);
		                            	if($great_grand_children):
		                            		foreach ($great_grand_children as $great_grand_child):?>
		                            			<tr>
				                                    <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $great_grand_child->id; ?>" /></td>
				                                    <td><?php echo $great_grand_child->name; ?></td>
				                                    <td><
				                                    	<p>
					                                		Menu URL: <?php echo $great_grand_child->url; ?>
					                                	</p>
					                                	<p>
						                                	Help Menu URL: <?php echo $great_grand_child->help_url?:'Not Set'; ?>
					                                	</p>
				                                    </td>
				                                    <td><i class="<?php echo $great_grand_child->icon; ?>"></i></td>
				                                    <td><?php echo isset($side_bar_menu_options[$great_grand_child->parent_id])?$side_bar_menu_options[$great_grand_child->parent_id]:'--'; ?></td>
				                                    <td><?php echo $great_grand_child->active==1?'<span class="label label-sm label-success">Active</span>':'<span class="label label-sm label-default">Hidden</span>'; ?></td>
				                                    <td>
				                                        <a href="<?php echo site_url('admin/bank_menus/edit/'.$great_grand_child->id); ?>" class="btn btn-xs default">
				                                            <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
				                                        </a>
				                                        <?php if($great_grand_child->active){ ?>
				                                            <a href="<?php echo site_url('admin/bank_menus/hide/'.$great_grand_child->id); ?>" class="btn btn-xs white confirmation_link">
				                                                <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
				                                            </a>
				                                        <?php }else{ ?>
				                                            <a href="<?php echo site_url('admin/bank_menus/activate/'.$great_grand_child->id); ?>" class="btn btn-xs green confirmation_link">
				                                                <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
				                                            </a>
				                                        <?php } ?>
				                                        <a href="<?php echo site_url('admin/bank_menus/delete/'.$great_grand_child->id); ?>" class="btn btn-xs red confirmation_link ">
				                                            <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
				                                        </a>
				                                    </td>
				                                </tr>
				                    <?php 
				                       $great_grand_grand_children = $this->bank_menus_m->get_children_links($great_grand_child->id);
				                       if($great_grand_grand_children):
				                       		foreach ($great_grand_grand_children as $great_grand_grand_child):?>
				                       			<tr>
				                                    <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $great_grand_grand_child->id; ?>" /></td>
				                                    <td><?php echo $great_grand_grand_child->name; ?></td>
				                                    <td><
				                                    	<p>
					                                		Menu URL: <?php echo $great_grand_grand_child->url; ?>
					                                	</p>
					                                	<p>
						                                	Help Menu URL: <?php echo $great_grand_grand_child->help_url?:'Not Set'; ?>
					                                	</p>
				                                    </td>
				                                    <td><i class="<?php echo $great_grand_grand_child->icon; ?>"></i></td>
				                                    <td><?php echo isset($side_bar_menu_options[$great_grand_grand_child->parent_id])?$side_bar_menu_options[$great_grand_grand_child->parent_id]:'--'; ?></td>
				                                    <td><?php echo $great_grand_grand_child->active==1?'<span class="label label-sm label-success">Active</span>':'<span class="label label-sm label-default">Hidden</span>'; ?></td>
				                                    <td>
				                                        <a href="<?php echo site_url('admin/bank_menus/edit/'.$great_grand_grand_child->id); ?>" class="btn btn-xs default">
				                                            <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
				                                        </a>
				                                        <?php if($great_grand_grand_child->active){ ?>
				                                            <a href="<?php echo site_url('admin/bank_menus/hide/'.$great_grand_grand_child->id); ?>" class="btn btn-xs white confirmation_link">
				                                                <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
				                                            </a>
				                                        <?php }else{ ?>
				                                            <a href="<?php echo site_url('admin/bank_menus/activate/'.$great_grand_grand_child->id); ?>" class="btn btn-xs green confirmation_link">
				                                                <i class="fa fa-eye"></i> Activate &nbsp;&nbsp; 
				                                            </a>
				                                        <?php } ?>
				                                        <a href="<?php echo site_url('admin/bank_menus/delete/'.$great_grand_grand_child->id); ?>" class="btn btn-xs red confirmation_link ">
				                                            <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
				                                        </a>
				                                    </td>
				                                </tr>	
				                    <?php	endforeach;
				                       endif;
				                    ?>
		                        <?php
	                            	endforeach;
	                            	endif;
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
			                No User Sidebar Menus to display.
			            </p>
			        </div>
			    <?php } ?>

			</div>

		</div>



	</div>

</div>
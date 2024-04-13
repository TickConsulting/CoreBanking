<div class="row">
	<div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
               	<div class="caption">
                    <?php echo $this->admin_menus_m->generate_page_title();?> 
                    <?php 
                    if($post){   
                        echo ' for '.$post->name;
                    }
                    ?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">
                <div class="filter-row">
                    <h4> Filter Saccos</h4>
                    <?php echo form_open(current_url(),'method="GET" class="form-inline" role="form"');?>
                        <div class="table-actions-wrapper">
                            <span> </span>
                            <?php echo form_dropdown('sacco_id',array(''=>'-- All Saccos --')+$sacco_options,$this->input->get('sacco_id'),'class="table-group-action-input select2 form-control input-inline input-small input-sm"');?>                
                            <button class="btn btn-sm btn-default table-group-action-submit">
                            <i class="fa fa-filter"></i> Filter</button>
                        </div>
                    <?php echo form_close();?>
                </div>

                <?php if(!empty($posts)){ ?>
                    <?php echo form_open('admin/sacco_branches/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Sacco Branches</p>
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
                                        Sacco
                                    </th>
                                    <th>
                                        Branch Code
                                    </th>
                                    <th>
                                        Name
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
                                        <td>
                                            <?php echo $sacco_options[$post->sacco_id]; ?>
                                        </td>
                                        <td><?php echo $post->code; ?></td>
                                        <td><?php echo $post->name; ?></td>
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
                                            <a href="<?php echo site_url('admin/sacco_branches/edit/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            <?php if($post->active){ ?>
                                                <a href="<?php echo site_url('admin/sacco_branches/hide/'.$post->id); ?>" class="btn btn-xs default confirmation_link">
                                                    <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
                                                </a>
                                            <?php }else{ ?>
                                                <a href="<?php echo site_url('admin/sacco_branches/activate/'.$post->id); ?>" class="btn btn-xs green confirmation_link">
                                                    <i class="icon-eye"></i> Activate &nbsp;&nbsp; 
                                                </a>
                                            <?php } ?>

                                            <a href="<?php echo site_url('admin/sacco_branches/delete/'.$post->id); ?>" class="btn btn-xs red confirmation_link">
                                                <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
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
                            <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_delete' data-toggle="confirmation" data-placement="top"> <i class='fa fa-trash'></i> Bulk Delete</button>
                        <?php endif;?>
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No sacco branches to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
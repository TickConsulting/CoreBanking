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
                <div class="btn-group search-button hold-on-click">
                    <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> Search
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">
                        <?php echo form_open(current_url(),'method="GET" class="filter"');?>
                            <div class="form-body">
                                <div class="form-group">
                                    <label>Investment Group Name</label>
                                    <div class="input-group">
                                        <?php echo form_dropdown('group_id',array(''=>'--All groups--')+$groups,$this->input->get('group_id'),'class="form-control select2 input-sm" placeholder="Investment Group Name"'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-search"></i></button>
                                <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                            </div>

                        <?php echo form_close();?>

                                    
                       
                    </div>
                </div>
               <?php if($posts):?>
            <?php echo form_open('admin/sms/action', ' id="form"  class="form-horizontal"'); ?> 

            <?php if ( ! empty($pagination['links'])): ?>
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> SMSes</p>
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
                                Sent To
                            </th>
                            <th>
                                Group Name
                            </th>
                            <th>
                                Message
                            </th>
                            <th>
                                System SMS
                            </th>

                            <th>
                                Queued time
                            </th>
                            <th>
                                Insufficient SMS
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
                                <td><?php echo $post->sms_to;?></td>
                                <td><?php echo $post->group_name;?></td>
                                <td><?php echo $post->message;?></td>
                                <td>
                                    <?php 
                                        if($post->system_sms == 1){
                                            echo '<span class="label label-sm label-primary"> System </span>';
                                        }else{
                                            echo '<span class="label label-sm label-default">Not System </span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php echo timestamp_to_date($post->created_on);?>
                                </td>
                                <td>
                                    <?php if($post->insufficent_group_sms_balance){echo '<span class="label label-xs label-warning">TRUE</span>';}?>
                                </td>
                                <td>
                                    <a href="<?php echo site_url('admin/sms/delete/'.$post->id);?>" class="btn btn-xs btn-danger confirmation_link" ><i class="fa fa-trash"></i> Delete</a>
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
                           No SMS's to send to display
                        </p>
                    </div>
                <?php endif;?>

            </div>

        </div>



    </div>

</div>

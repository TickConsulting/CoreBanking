<style type="text/css"> 
    hr, p {
        margin: 5px 0;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                   <?php echo $this->partner_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->partner_menus_m->generate_page_quick_action_menus();?>
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
                                        <span class="input-group-addon">
                                        <i class="fa fa-book"></i>
                                        </span>
                                        <?php echo form_input('name',$this->input->get('name'),'class="form-control input-sm" placeholder="Investment Group Name"'); ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Investment Group Owner's Name</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                            </span>
                                            <?php echo form_input('owner',$this->input->get('owner'),'class="form-control input-sm" placeholder="Investment Group Owners Name"'); ?>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Investment Group Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <i class="fa fa-mobile"></i>
                                            </span>
                                            <?php echo form_input('phone',$this->input->get('phone'),'class="form-control input-sm" placeholder="Investment Group Phone Number"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Investment Group Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <i class="fa fa-envelope"></i>
                                            </span>
                                            <?php echo form_input('email',$this->input->get('email'),'class="form-control input-sm" placeholder="Investment Group Email Address"'); ?>
                                        </div>
                                    </div>
                                     <div class="form-group col-md-6">
                                        <label>Group Status</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <i class="fa fa-envelope"></i>
                                            </span>
                                            <?php echo form_dropdown('status',array(''=>'--All Groups--')+$group_status_options,$this->input->get('status'),'class="form-control select2 input-sm" placeholder="Status"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                            <label>Investment Group Bank</label>
                                            <div class="input-group">
                                                <div class="input-group">
                                                    <?php echo form_dropdown('group_bank_options[]',$group_bank_options,$this->input->get('group_bank_options'),'class="form-control input-sm select2-multiple" multiple="multiple" placeholder="Select user group"'); ?>
                                                </div>
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
                <?php
                    $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
                    echo '
                    <div class="btn-group margin-bottom-20 search-button">
                        <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_groups_list" data-title="Generating Excel Document Groups Listing">
                            Export Current View to Excel <i class="fa fa-file-excel-o"></i>
                        </a>
                    </div>';
                    $query = $_SERVER['QUERY_STRING']?'?generate_excel=2&'.$_SERVER['QUERY_STRING']:'?generate_excel=2';
                    echo '
                    <div class="btn-group margin-bottom-20 search-button">
                        <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_groups_list" data-title="Generating Excel Document Groups Listing">
                            Export All Records to Excel <i class="fa fa-file-excel-o"></i>
                        </a>
                    </div>';
                ?>
               <?php if($posts):?>
            <?php echo form_open('partner/groups/action', ' id="form"  class="form-horizontal"'); ?> 
                
            <?php if ( ! empty($pagination['links'])): ?>
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Investment Groups</p>
                <?php 
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                    endif; 
                ?>  

                <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed table-searchable">
                    <thead>
                        <tr>
                            <th width='2%'>
                                 <input type="checkbox" name="check" value="all" class="check_all">
                            </th>
                            <th>
                                #
                            </th>
                            <th>
                              Group Details
                            </th>
                            <th>
                                Size
                            </th>
                            <th>
                                Billing Package
                            </th>
                            <th>
                                Join date
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
                        <?php 
                        $i=$this->uri->segment(5, 0);
                        foreach($posts as $post):
                            $user = $this->ion_auth->get_user($post->owner);
                        ?>
                            <tr>
                                <td>
                                    <input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" />
                                </td>
                                <td><?php echo $i+1;?></td>
                                <td><p><?php echo $post->name; if($post->trial_days && $post->status!=1){echo '<strong> ('.$post->trial_days.')</strong>';}?>
                                    <p/>
                                    <p><?php echo $user->first_name.' '.$user->last_name;?>
                                    <p/>
                                    <p><?php 
                                        if($post->email){echo $post->email.'<br/>';}else{if($user->email){echo $user->email.'<br/>';}}
                                        if($post->phone){echo $post->phone;}else{if($user->phone){echo $user->phone;}}
                                    ?>
                                    </p>
                                </td>
                                <td><?php echo $post->size; $active_size=$post->active_size?:1; echo '('.$active_size.')'; ?></td>
                                <td><?php if($post->billing_package_id){echo $billing_packages[$post->billing_package_id];}  if($post->billing_cycle){echo ' - '.$billing_cycles[$post->billing_cycle];}?></td>
                                <td ><?php echo str_replace(',','<br/>',timestamp_to_date_and_time($post->created_on));?></td>
                                <td class="actions">
                                    <?php 
                                        if($post->lock_access): echo '<span data-original-title="Account not yet Activated Activation code: '.$post->activation_code.'"  class="label label-xs label-warning tooltips">Locked</span>';
                                        else: $status = $post->status;
                                        if($status == 1)
                                        {
                                            echo '<span class="label label-xs label-success">Paying</span>';
                                        }
                                        else if($status == 2)
                                        {
                                            echo '<span class="label label-xs label-danger">Suspended</span>';
                                        }
                                        else
                                        {
                                            if($post->trial_days){
                                                echo '<span class="label label-sx label-primary">On Trial</span>';
                                            }else{
                                                echo '<span class="label label-sx label-default">'.timestamp_to_date($post->trial_days_end_date).'</span>';
                                            }
                                        }
                                        endif;
                                    ?>

                                </td>
                                <td class="actions">
                                    <a href="<?php echo site_url('partner/groups/view/'.$post->id);?>" class="btn btn-xs btn-primary">
                                        <i class="fa fa-eye"></i> View &nbsp;&nbsp;
                                    </a>
                                </td>
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

            <div class="clearfix"></div>
        

            <?php echo form_close();?>
            <?php else:?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No data found</h4>
                        <p>
                           No results found.
                        </p>
                    </div>
                <?php endif;?>

            </div>

        </div>



    </div>

</div>

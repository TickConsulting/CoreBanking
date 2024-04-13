<style type="text/css"> 
    hr, p {
        margin: 5px 0;
    }

    td>ul {
        padding-left: 15px !important;
    }
</style>
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
                                        <span class="input-group-addon">
                                        <i class="fa fa-book"></i>
                                        </span>
                                        <?php echo form_input('name',$this->input->get('name'),'class="form-control input-sm" placeholder="Investment Group Name"'); ?>
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
            <?php echo form_open('admin/groups/action', ' id="form"  class="form-horizontal"'); ?> 
                
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
                            <th>
                                #
                            </th>
                            <th>
                              Group Details
                            </th>
                            <th>
                                Admin Details
                            </th>
                            <th>
                                Bank Account Details
                            </th>
                            <th>
                                Setup Status
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
                            $username = '--Not Set--';
                            $phone = '';
                            $email = '';
                            if($post->owner){
                                $user = $this->ion_auth->get_user($post->owner);
                                $username = $user->first_name.' '.$user->last_name;
                                if($post->phone){
                                    $phone ='<strong>Phone: </strong>'.$post->phone.'<br/>';
                                }else{
                                    if($user->phone){
                                        $phone ='<strong>Phone: </strong>'.$user->phone.'<br/>';
                                    }
                                }
                                if($post->email){
                                    $email =  '<strong>Email: </strong>'.$post->email.'<br/>';
                                }else{
                                    if($user->email){
                                        $email = '<strong>Email: </strong>'.$user->email.'<br/>';
                                    }
                                }
                            }
                            $onboarder = $this->ion_auth->get_user($post->created_by);
                        ?>
                            <tr>
                                <td><?php echo $i+1;?></td>
                                <td>
                                    <strong>Name: </strong><?php echo $post->name;?><br/>
                                    <?php if($post->active_size){
                                        echo "<strong>Size: </strong>".$post->active_size.' members'."<br/>";
                                    }?>
                                    
                                    <strong>Onboarded by: </strong> <?php echo $onboarder->first_name.' '.$onboarder->last_name;?><br/>
                                    <strong>Join Date: </strong><?php echo timestamp_to_date($post->created_on);?><br/><br/>
                                </td>
                                <td>
                                    <strong> Name: </strong> <?php echo $username;?><br/>
                                    <?php 
                                        echo $phone;
                                        echo $email;                                        
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        if(isset($bank_accounts[$post->id])){
                                            $accounts = $bank_accounts[$post->id];
                                            echo '<ul>';
                                                foreach ($accounts as $key=>$account) {
                                                    echo '<li>'.$account->account_name.'('.$account->bank_branch.') - <strong>'.(isset($currency_options[$account->account_currency_id])?$currency_options[$account->account_currency_id]:'').' '.number_to_currency($account->current_balance).'</li></p>';
                                                }
                                            echo '</ul>';
                                        }else{

                                        }
                                    ?>
                                </td>
                                <td class="actions">
                                    <?php if($post->group_setup_status){
                                        echo '<span class="label label-sm label-success">Complete</span>';
                                    }else{
                                        echo '<span class="label label-sm label-danger">'.$setup_tasks[$post->group_setup_position].'</span>';
                                    }?>
                                </td>
                                <td class="actions">
                                    <a href="<?php echo site_url('admin/groups/edit/'.$post->id);?>" class="btn btn-xs default">
                                        <i class="fa fa-edit"></i> Edit &nbsp;&nbsp;
                                    </a>
                                    <a href="<?php echo site_url('admin/groups/search/'.$post->id);?>" class="btn btn-xs btn-primary">
                                        <i class="fa fa-eye"></i> View &nbsp;&nbsp;
                                    </a>
                                    <a target="_blank" href="<?php echo site_url('admin/groups/login_as_admin/'.$post->id)?>" class="btn btn-xs btn-default">
                                        <i class="fa fa-user-secret"></i> Login as Admin &nbsp;&nbsp;

                                    <a href="<?php echo site_url('admin/groups/export_group_data/'.$post->id);?>" class="btn btn-xs btn-primary " >
                                        <i class="fa fa-file-excel-o"></i> Export Group Data &nbsp;&nbsp;
                                    </a>

                                    <a href="<?php echo site_url('admin/groups/reset_group/'.$post->id);?>" class="btn btn-xs btn-warning prompt_confirmation_message_link" data-title="Enter the reset code to reset the group data." >
                                        <i class="fa fa-trash"></i> Reset Group Data &nbsp;&nbsp;
                                    </a>
                                    </a>
                                    <a href="<?php echo site_url('admin/groups/delete/'.$post->id);?>" class="btn prompt_confirmation_message_link btn-xs btn-danger" data-title="Enter the delete code to delete the group and its data permanently." >
                                        <i class="fa fa-trash"></i> Delete Group &nbsp;&nbsp;
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

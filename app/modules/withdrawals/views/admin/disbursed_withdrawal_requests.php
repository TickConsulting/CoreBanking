<style type="text/css">
    .table td, .table th {
        font-size: 11px;
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

                <?php if(!empty($posts)){ ?>
                    <?php echo form_open('admin/withdrawals/action', ' id="form"  class="form-horizontal"'); ?> 
                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Withdrawal Requests </p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>

                        <div class=''>
                            <table class="table table-striped table-bordered table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th width='2%'>
                                             <input type="checkbox" name="check" value="all" class="check_all">
                                        </th>
                                        <th width='2%'>
                                            #
                                        </th>
                                        <th>
                                            Disbursed On
                                        </th>
                                        <th>
                                            Group
                                        </th>
                                        
                                        <th class="text-left">
                                            Recipient
                                        </th>
                                        <th class="text-right">
                                            Amount
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>

                                </thead>
                                <tbody>
                                    <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                        <tr>
                                            <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                            <td><?php echo $i+1;?></td>
                                            <td>
                                                <?php echo timestamp_to_datetime($post->disbursed_on); ?>
                                            </td>
                                            <td>
                                                <?php echo $group_options[$post->group_id]; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $recipient = new StdClass;
                                                    if(preg_match('/bank-/', $post->recipient_id)){
                                                        $recipient_id = str_replace('bank-', '', $post->recipient_id);
                                                        $recipient = $this->recipients_m->get($recipient_id);
                                                        if($recipient){
                                                        }else{
                                                            $valid_recipient = FALSE;
                                                        } 
                                                    }else if(preg_match('/mobile-/', $post->recipient_id)){
                                                        $recipient_id = str_replace('mobile-', '', $post->recipient_id);
                                                        $recipient = $this->recipients_m->get($recipient_id);
                                                        if($recipient){
                                                        }else{
                                                            $valid_recipient = FALSE;
                                                        }
                                                    }else if(preg_match('/member-/', $post->recipient_id)){
                                                        $member_id = str_replace('member-', '', $post->recipient_id);
                                                        $member = $this->members_m->get_group_member($member_id,$post->group_id);
                                                        if($member){
                                                            $recipient->name = $member->first_name.' '.$member->last_name;
                                                            $recipient->phone_number = $member->phone;
                                                            $recipient->account_name = '';
                                                            $recipient->account_number = '';
                                                        }else{
                                                            $valid_recipient = FALSE;
                                                        }
                                                    }else{
                                                        $recipient = $this->recipients_m->get($post->recipient_id);
                                                        if($recipient){
                                                        }else{
                                                            $valid_recipient = FALSE;
                                                        }
                                                    }
                                                    echo '<strong>Name:</strong> '.$recipient->name.'<br/>';
                                                    if($recipient->phone_number){
                                                        echo '<strong>Phone Number:</strong> '.$recipient->phone_number.'<br/> <br/>';
                                                    }
                                                    if($recipient->account_name){
                                                        echo '<strong>Bank Account Name: </strong> '.$recipient->account_name.'<br/> <br/>';
                                                    }
                                                    if($recipient->account_number){
                                                        echo '<strong>Bank Account Number: </strong> '.$recipient->account_number.'<br/> <br/>';
                                                    }
                                                ?>
                                            </td>
                                            <td class="text-right">
                                                <?php echo number_to_currency($post->amount); ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo site_url('admin/withdrawals/retry_disbursement/'.$post->id)?>" class="btn btn-xs btn-primary"> <i class="fa fa-refresh"></i> Retry Disbursement
                                                </a>
                                            </td>
                                        </tr>
                                    <?php $i++; endforeach; ?>
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

                        <!--
                            <?php if($posts):?>
                                <button class="btn btn-sm btn-success confirmation_bulk_action" name='btnAction' value='bulk_activate' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye'></i> Bulk Activate</button>
                                <button class="btn btn-sm btn-default confirmation_bulk_action" name='btnAction' value='bulk_hide' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye-slash'></i> Bulk Hide</button>
                            <?php endif;?>
                        -->
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No withdrawal requests to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
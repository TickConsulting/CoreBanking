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
            <div class="portlet-body form logos">

                <?php if(!empty($posts)){ ?>
                    <?php echo form_open('admin/banks/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> B2C Requests</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
                        </div>
                        <div class="clearfix"></div>
                            <div class="table-responsive">
                             <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed table-searchable">
                                <thead>
                                    <tr>
                                        <th width='2%'>
                                             <input type="checkbox" name="check" value="all" class="check_all">
                                        </th>
                                        <th>
                                            #
                                        </th>
                                        <th>
                                            Phone
                                        </th>
                                        <th class="text-right">
                                            Amount
                                        </th>
                                        <th>
                                            Paybill
                                        </th>
                                        <th width="10%">
                                            Request Details
                                        </th>
                                        <th>
                                            Request Time
                                        </th>
                                        <th>
                                            Status
                                        </th>
                                        <th>
                                            Result Desc.
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = $this->uri->segment(5, 0);?>
                                    <?php foreach($posts as $post):
                                    ?>
                                        <tr>
                                            <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                            <td><?php echo ++$i;?></td>
                                            <td><?php echo $post->phone; ?></td>
                                            <td class="text-right">
                                                <?php echo number_to_currency($post->amount); ?><br/>
                                                <?php echo number_to_currency($post->disburse_charge); ?><br/>
                                                <?php echo $post->transaction_receipt; ?><br/>
                                            </td>
                                            <td>
                                                <?php echo $post->paybill; ?><br/>
                                                <?php echo 'CID: '.$post->command_id;?><br/>
                                                <?php echo 'REF NU: '.$post->reference_number;?><br/>
                                                <br/>
                                                <?php echo 'OCI: '. $post->originator_conversation_id;?><br/>
                                                <?php echo 'CI: '. $post->conversation_id;?>
                                                <?php echo 'AC: '. $post->account_id;?>
                                            </td>
                                            <td>
                                                Group Name :<?php echo $post->group_name; ?><br/>
                                                User ID :<?php echo $post->user_id; ?><br/>
                                                Callback URL :<?php //echo $post->callback_url; ?><br/>
                                                <?php echo $post->originator_conversation_id; ?><br/>
                                            </td>
                                            <td><?php echo timestamp_to_datetime_from_timestamp($post->request_time); ?></td>
                                            <td class="two-entry"><?php if(is_numeric($post->result_code) && $post->result_code==0){echo '<span class="label label-success label-xs">Success</span>';}else{echo '<span class="label label-warning label-xs">Failed</span>';} ?><br/><br/>
                                                
                                            <?php if(is_numeric($post->callback_result_code) && $post->callback_result_code==0){echo '<span class="label label-success label-xs">Success</span>';}else{echo '<span class="label label-danger label-xs">No Result</span>';} ?>
                                            </td>
                                            <td>
                                                <?php echo $post->result_description; ?><br/><br/>
                                                <?php echo $post->callback_result_description; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo site_url('admin/safaricom/view_b2c_request/'.$post->id);?>" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View</a>
                                                &nbsp;&nbsp; 
                                                <a href="<?php echo site_url('admin/safaricom/forward_b2c_request/'.$post->id);?>" class="btn btn-xs btn-success" style="margin-top: 5px;"><i class="fa fa-eye"></i> Forward Request</a>
                                                <?php if($post->is_reversed){
                                                    echo '<br/><br/>';
                                                    echo '<span class="label label-xs label-warning">Reversed</span>';
                                                }?>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
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
                        
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No Requests to display.
                        </p>
                    </div>
                <?php } ?>

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
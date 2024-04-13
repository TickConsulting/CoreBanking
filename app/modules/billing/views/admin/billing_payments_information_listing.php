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
                                <div class="form-group">
                                    <label>Payment Date</label>
                                    <div class="">
                                        <div class="input-group date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                            <?php echo form_input('receipt_date_from',$this->input->get('receipt_date_from'),' class="form-control" '); ?>
                                            <span class="input-group-addon"> to </span>
                                            <?php echo form_input('receipt_date_to',$this->input->get('receipt_date_to'),' class="form-control" '); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <div class="input-group">
                                        <div class="input-group">
                                            <?php echo form_dropdown('payment_method',array(''=>'--All mode of payments--')+$payment_methods,$this->input->get('payment_method'),'class="form-control input-sm select2" placeholder="Select payment method"'); ?>
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
                <?php if(!empty($posts)){ ?>
                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Payments </p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
                        <p class="margin-bottom-10">
                            <a href="<?php echo site_url("admin/billing/billing_payments_information_csv?group_id=".$this->input->get('group_id')."&receipt_date_from=".$this->input->get('receipt_date_from')."&receipt_date_to=".$this->input->get('receipt_date_to')."&payment_method=&filter=filter"); ?>" class="pull-right btn btn-xs green"><span><i class="fa fa-cloud-download"></i></span> Export to CSV</a>
                        </p>
                        <br/>
                        <div class='clearfix'></div>
                        <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed table-searchable">
                            <thead>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        Activation Date
                                    </th>
                                    <th>
                                       Group Number
                                    </th>
                                    <th>
                                        Bank Account Number
                                    </th>

                                    <th>
                                        Details
                                    </th>
                                    <th class="text-right">
                                        Amount (<?php echo $this->default_country->currency_code; ?>)
                                    </th>
                                    <th class="text-right">
                                        Tax (<?php echo $this->default_country->currency_code; ?>)
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><?php echo $i+1;?></td>
                                        <td>
                                            <?php
                                                echo timestamp_to_date($post->receipt_date);
                                            ?>
                                        </td>
                                        <td><?php echo $group_account_number_options[$post->group_id]; ?></td>
                                        <td>
                                            <?php
                                                if(isset($group_bank_account_options[$post->group_id])){
                                                    $bank_accounts = $group_bank_account_options[$post->group_id];
                                                    $count = 1;
                                                    foreach($bank_accounts as $bank_account):
                                                        if($count==1){
                                                            echo $bank_account;
                                                        }else{
                                                            echo ','.$bank_account;
                                                        }
                                                        $count++;
                                                    endforeach;
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php $payment_method = $post->payment_method?:1;
                                                echo $payment_methods[$payment_method].' payment';
                                                if($post->description){
                                                    echo ' - '.$post->description;
                                                }
                                            ?>
                                        </td>
                                        <td class="text-right">
                                             <?php echo number_to_currency($post->amount);?>
                                        </td>
                                        <td class="text-right">
                                             <?php echo number_to_currency($post->tax);?>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->active){
                                                    echo "<span class='label label-xs label-success'>Active</span>";
                                                }else{
                                                    echo "<span class='label label-xs label-default'>Voided</span>";
                                                }
                                            ?>
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
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No Billing invoices to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
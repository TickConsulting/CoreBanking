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

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Investment Group Bank Account Number</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                            </span>
                                            <?php echo form_input('bank_account_no',$this->input->get('bank_account_no'),'class="form-control input-sm" placeholder="Investment Group Bank Account Number"'); ?>
                                        </div>
                                    </div>
                                </div>

                                 <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Transaction Id</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                            </span>
                                            <?php echo form_input('transaction_id',$this->input->get('transaction_id'),'class="form-control input-sm" placeholder="Transaction ID"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Transaction Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                            </span>
                                            <?php echo form_input('amount',$this->input->get('amount'),'class="form-control currency input-sm" placeholder="Transaction Amount"'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label ">Sign Up Date Range</label>
                                        <div class="">
                                            <div class="input-group date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                             <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control" '); ?>
                                                <span class="input-group-addon"> to </span>
                                            <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control" '); ?>
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
                </div>

                <?php if(!empty($posts)){ ?>
                    <?php echo form_open('admin/transaction_alerts/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Transaction Alerts</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  

                        <div class=''>
                            <table class="table table-searchable  table-striped table-bordered table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th width='2%'>
                                            #
                                        </th>
                                        <th>
                                            Receipt time
                                        </th>
                                        <th width="">
                                            Transaction Description
                                        </th>

                                        <th class='text-right'>
                                            Amount
                                        </th>
                                        <th class='text-right'>
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                        <tr>
                                            <td><?php echo $i+1;?></td>
                                            <td><?php echo timestamp_to_date_and_time($post->created_on); ?></td>
                                            <td>
                                                <strong>Transaction Date: </strong> 
                                                <?php echo timestamp_to_date($post->transaction_date); ?>
                                                <br/>
                                                <strong>Bank / Mobile MP </strong> 
                                                <?php 
                                                    if($post->bank_id){
                                                        echo $bank_options[$post->bank_id];
                                                    }else if($post->mobile_money_provider_id){
                                                        echo $mobile_money_providers[$post->mobile_money_provider_id];
                                                    }else{
                                                        echo 'None';
                                                    }
                                                ?><br/>
                                                <strong>Account Number: </strong>
                                                <?php 
                                                    echo $post->account_number; 
                                                ?>
                                                <br/>
                                                <?php echo $post->description ?>
                                                <br/>
                                                <strong>IP Address: </strong> <?php echo $post->ip_address; ?>
                                            </td>
                                            <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                                            <td>
                                                <a href="<?php echo site_url('admin/transaction_alerts/forwards/'.$post->id); ?>" class="btn btn-xs blue">
                                                    <i class="icon-eye"></i> Alert Forwards  &nbsp;&nbsp; 
                                                </a>

                                                <a href="<?php echo site_url('admin/transaction_alerts/delete/'.$post->id); ?>" class="btn btn-xs btn-danger confirmation_link">
                                                    <i class="icon-eye"></i> Alert Delete  &nbsp;&nbsp; 
                                                </a>
                                            </td>
                                        </tr>
                                        <?php $i++;
                                        endforeach; ?>
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
                            No Transaction alerts to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
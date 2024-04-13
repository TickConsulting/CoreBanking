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
                                    <label>Billing Cycle</label>
                                    <div class="input-group">
                                        <?php echo form_dropdown('billing_cycle',array(''=>'--Select billing cycle--')+$billing_cycle,$this->input->get('billing_cycle'),'class="form-control select2 input-sm" placeholder="Select billing cycle"'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Billing Date</label>
                                    <div class="input-group date-time-range">
                                        <?php echo form_input('billing_date','','class="form-control date-picker input-sm" placeholder="Select billing date" data-date-format="dd-mm-yyyy" '); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <div class="input-group date-time-range">
                                        <?php echo form_input('due_date','','class="form-control date-picker input-sm"cdata-date-format="dd-mm-yyyy" placeholder="Select due date"'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Invoice status</label>
                                    <div class="input-group">
                                        <div class="input-group">
                                            <?php echo form_dropdown('invoice_status',array(''=>'--Select invoice status--','1'=>'Paid','2'=>'Unpaid'),$this->input->get('invoice_status'),'class="form-control input-sm select2" placeholder="Select invoice status"'); ?>
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
                    <?php echo form_open('admin/billing/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Countries</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
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
                                       Group Name
                                    </th>
                                    <th>
                                        Billing Cycle
                                    </th>
                                    <th>
                                        Billing Date
                                    </th>
                                    <th>
                                        Due Date
                                    </th>
                                    <th width="10%" class="text-right">
                                        Amount
                                    </th>
                                    <th class="text-right">
                                        Amount Paid
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
                                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo $groups[$post->group_id]; ?></td>
                                        <td><?php echo isset($billing_cycle[$post->billing_cycle])?$billing_cycle[$post->billing_cycle]:''; ?></td>
                                        <td ><?php
                                                echo timestamp_to_date($post->billing_date);
                                            ?>
                                        </td>
                                        <td ><?php
                                                echo timestamp_to_date($post->due_date);
                                            ?>
                                        </td>
                                        <td class="text-right">
                                             <?php 
                                             echo 'Pro-: '.number_to_currency($post->prorated_amount).'<br/>';
                                             echo 'Tax-: '.number_to_currency($post->tax).'<br/>';
                                             echo 'Sub: '.number_to_currency($post->amount - $post->tax - $post->prorated_amount).'<br/>';
                                             echo 'Total: '.number_to_currency($post->amount);?>
                                        </td>
                                        <td class="text-right">
                                             <?php echo number_to_currency($post->amount_paid);?>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->status){
                                                    echo "<span class='label label-xs label-success'>Paid</span>";
                                                }else{
                                                    echo "<span class='label label-xs label-info'>Unpaid</span>";
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('admin/billing/edit_billing_invoice/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            <a href="<?php echo site_url('admin/billing/receive_billing_payments?billing_invoice_id='.$post->id.'&billing_package_id='.$post->billing_package_id.'&group_id='.$post->group_id); ?>" class="btn btn-xs blue">
                                                <i class="fa fa-money"></i> Receive Payment &nbsp;&nbsp; 
                                            </a>
                                            <a href="<?php echo site_url('admin/billing/invoice_export_pdf/'.$post->id.'/'.$post->group_id); ?>" class="btn btn-xs green ">
                                                    <i class="fa fa-file"></i>Export &nbsp;&nbsp; 
                                            </a>
                                            <a href="<?php echo site_url('admin/billing/void_invoice/'.$post->id); ?>" class="btn btn-xs red confirmation_link">
                                                    <i class="fa fa-trash"></i>void &nbsp;&nbsp; 
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
                        <?php if($posts):?>
                            <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_void_invoice' data-toggle="confirmation" data-placement="top"> <i class='fa fa-trash'></i>Bulk Void</button>
                        <?php endif;?>
                    <?php echo form_close(); ?>
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
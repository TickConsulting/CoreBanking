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
                                        <?php echo form_dropdown('group_ids[]',array(''=>'--All groups--')+$groups,$this->input->get('group_ids'),'class="form-control select2 input-sm" placeholder="Investment Group Name" multiple="multiple"'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Billing Cycle</label>
                                    <div class="input-group">
                                        <?php echo form_dropdown('billing_cycle',array(''=>'--Select billing cycle--')+$billing_cycle,$this->input->get('billing_cycle'),'class="form-control select2 input-sm" placeholder="Select billing cycle"'); ?>
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
                <?php if(!empty($monthly_invoices)){ ?>
                    <?php echo form_open('admin/billing/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Billing Payments and Invoices</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
                        <span class="margin-top-10" style="margin-top:20px;"></span>
                        <?php 
                            $i = $this->uri->segment(5, 0); 
                            $total_total_number_of_invoices = 0;
                            $total_total_payable = 0;
                            $total_total_tax_payable = 0;
                            $total_total_prorated_amount_payable = 0;
                            $total_total_sub_amount_payable = 0;
                            $total_total_paid = 0;
                            foreach ($years as $year) {?>
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <td colspan="8">
                                                <?php echo '<strong>Year :  '.$year.'</strong>';?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th width="25%">
                                               Month Name
                                            </th>
                                            <th width="text-right">
                                               Number of Invoices
                                            </th>
                                            <th class="text-right">
                                                Subscription
                                            </th class="text-right">
                                            <th class="text-right">
                                                Tax Amount
                                            </th>
                                            <th class="text-right">
                                                Prorated Amount
                                            </th>
                                            <th class="text-right">
                                                Total Amount
                                            </th>
                                            <th class="text-right">
                                                Amount Paid
                                            </th>
                                            <th>
                                                % Success
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $i = 1;
                                            $total_number_of_invoices = 0;
                                            $total_payable = 0;
                                            $total_tax_payable = 0;
                                            $total_prorated_amount_payable = 0;
                                            $total_sub_amount_payable = 0;
                                            $total_paid = 0;
                                        if(isset($monthly_invoices[$year])):
                                           foreach ($monthly_invoices[$year] as $month => $values):
                                                $number_of_invoices = $values['number_of_invoices']?:0;
                                                $tax_payable=$values['tax_payable']?:0;
                                                $prorated_amount_payable = $values['prorated_amount_payable']?:0;
                                                $total_sub = $values['amount_payable']?:0;
                                                $payable = ($total_sub-$tax_payable-$prorated_amount_payable)?:0
                                            ?>
                                            <tr>
                                                <td><?php echo $i;?></td>
                                                <td><?php echo timestamp_to_monthtime(strtotime($month.'01'));?></td>
                                                <td class="text-right"><?php echo ($number_of_invoices); ?></td>
                                                <td class="text-right"><?php echo number_to_currency($payable); ?></td>
                                                <td class="text-right"><?php echo number_to_currency($tax_payable); ?></td>
                                                <td class="text-right"><?php echo number_to_currency($prorated_amount_payable); ?></td>
                                                <td class="text-right"><?php echo number_to_currency($total_sub); ?></td>
                                                <td class="text-right"><?php 
                                                    $paid = isset($monthly_payments[$month])?$monthly_payments[$month]:0;
                                                    echo number_to_currency($paid);?>    
                                                </td>
                                                <td class="text-right">
                                                    <?php 
                                                        $per = 0;
                                                        if($payable>0){
                                                            $per = ($paid/$total_sub)*100;
                                                        }
                                                        
                                                        echo round($per).'%';
                                                    ?>
                                                </td>
                                            </tr>
                                    <?php   $i++;
                                            $total_number_of_invoices += $number_of_invoices;
                                            $total_payable+=$payable;
                                            $total_tax_payable+=$tax_payable;
                                            $total_prorated_amount_payable+=$prorated_amount_payable;
                                            $total_sub_amount_payable+=$total_sub;
                                            $total_paid += $paid;
                                            endforeach;
                                        endif; 
                                    ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">Totals</td>
                                            <td class="text-right"><?php echo ($total_number_of_invoices);?></td>
                                            <td class="text-right"><?php echo number_to_currency($total_payable);?></td>
                                            <td class="text-right"><?php echo number_to_currency($total_tax_payable);?></td>
                                            <td class="text-right"><?php echo number_to_currency($total_prorated_amount_payable);?></td>
                                            <td class="text-right"><?php echo number_to_currency($total_sub_amount_payable);?></td>
                                            <td class="text-right"><?php echo number_to_currency($total_paid);?></td>
                                            <td class="text-right"><?php
                                                $per = 0;
                                                if($total_sub_amount_payable>0){
                                                    $per = ($total_paid/$total_sub_amount_payable)*100;
                                                }
                                                echo round($per).'%';
                                            ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="clearfix"></div>
                                <hr/>
                            <?php 
                                $total_total_number_of_invoices+=$total_number_of_invoices;
                                $total_total_payable+=$total_payable;
                                $total_total_tax_payable+=$total_tax_payable;
                                $total_total_prorated_amount_payable+=$total_prorated_amount_payable;
                                $total_total_sub_amount_payable+=$total_sub_amount_payable;
                                $total_total_paid+=$total_paid;
                            } ?>

                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <td colspan="8">
                                            <?php echo '<strong>Total Over Years</strong>';?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th width="25%">
                                           Month Name
                                        </th>
                                        <th class="text-right">
                                           Number of Invoices
                                        </th>
                                        <th class="text-right">
                                            Subscription
                                        </th class="text-right">
                                        <th class="text-right">
                                            Tax Amount
                                        </th>
                                        <th class="text-right">
                                            Prorated Amount
                                        </th>
                                        <th class="text-right">
                                            Total Amount
                                        </th>
                                        <th class="text-right">
                                            Amount Paid
                                        </th>
                                        <th>
                                            % Success
                                        </th>
                                    </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <td colspan="2">Totals</td>
                                    <td class="text-right"><?php echo $total_total_number_of_invoices;?></td>
                                    <td class="text-right"><?php echo number_to_currency($total_total_payable);?></td>
                                    <td class="text-right"><?php echo number_to_currency($total_total_tax_payable);?></td>
                                    <td class="text-right"><?php echo number_to_currency($total_total_prorated_amount_payable);?></td>
                                    <td class="text-right"><?php echo number_to_currency($total_total_sub_amount_payable);?></td>
                                    <td class="text-right"><?php echo number_to_currency($total_total_paid);?></td>
                                    <td class="text-right"><?php
                                        $per = 0;
                                        if($total_total_sub_amount_payable>0){
                                            $per = ($total_total_paid/$total_total_sub_amount_payable)*100;
                                        }
                                        echo round($per).'%';
                                    ?></td>
                                </tr>
                            </tfoot>
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
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No Billing invoices and payments to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
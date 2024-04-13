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
                                    <label>Depositor</label>
                                    <div class="input-group">
                                        <?php echo form_dropdown('ipn_depositor',array(''=>'--All Depositors--')+$ipn_depositor,$this->input->get('ipn_depositor'),'class="form-control select2 input-sm" placeholder="IPN Depositor"'); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="input-group">
                                        <?php echo form_dropdown('status',array(''=>'--All Status--')+$ipn_status,$this->input->get('status'),'class="form-control select2 input-sm" placeholder="Status"'); ?>
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
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        Depositor
                                    </th>
                                    <th>
                                       Code
                                    </th>
                                    <th>
                                        Date
                                    </th>
                                    <th>
                                        Account.
                                    </th>
                                    <th>
                                        Type 
                                        Particular
                                    </th>
                                    <th>
                                        Customer
                                    </th>
                                    <th>
                                        Currency
                                    </th>
                                    <th class="text-right">
                                        Amount (<?php echo $this->default_country->currency_code; ?>)
                                    </th>
                                     <th>
                                        Status
                                    </th>
                                    <!--<th>
                                        Actions
                                    </th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo $ipn_depositor[$post->ipn_depositor]; ?></td>
                                        <td ><?php
                                                echo $post->transaction_id;
                                            ?>
                                        </td>
                                        <td >
                                            <?php echo timestamp_to_date($post->transaction_date);?>
                                        </td>
                                        <td >
                                             <?php echo $post->account;?>
                                        </td>
                                        <td >
                                             <?php echo $post->reference_number;?> <br/>
                                             <?php echo $post->transaction_type;?> <br/>
                                             <?php echo $post->particulars;?>
                                        </td>
                                        <td >
                                             <?php echo $post->customer_name;?><br/>
                                             <?php echo $post->phone;?>
                                        </td>
                                        <td >
                                             <?php echo $post->currency;?>
                                        </td>
                                        <td class="text-right">
                                             <?php echo number_to_currency($post->amount);?>
                                             <br/><br/>
                                             Balance: <?php echo number_to_currency($post->paybill_balance);?>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->status==1){
                                                    echo '<span class="label label-xs label-success">SUccessful</span>';
                                                }
                                                else if($post->status==2){
                                                    echo '<span class="label label-xs label-warning">Parameter</span>';
                                                }
                                                else if($post->status==3){
                                                    echo '<span class="label label-xs label-warning">Group error</span>';
                                                }
                                                else if($post->status==4){
                                                    echo '<span class="label label-xs label-warning">SMS Purchase</span>';
                                                }else if($post->status==5){
                                                    echo '<span class="label label-xs label-warning">Entry</span>';
                                                }
                                            ?>
                                        </td>
                                        <!--<td>
                                            <a href="<?php echo site_url('admin/billing/delete_ipn/'.$post->id);?>">Delete</a>
                                        </td>-->
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
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No IPNs to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
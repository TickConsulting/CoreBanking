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
                    <?php echo form_open('admin/billing/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> C2B Requests</p>
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
                                        Paybill
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
                                    <th class="text-right">
                                        Amount (KES)
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
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo $post->shortcode; ?></td>
                                        <td ><?php
                                                echo $post->transaction_id;
                                            ?>
                                        </td>
                                        <td >
                                            <?php echo timestamp_to_datetime_from_timestamp($post->transaction_date);?>
                                        </td>
                                        <td >
                                             <?php echo $post->account;?>
                                        </td>
                                        <td >
                                             <?php echo $post->reference_number;?> <br/>
                                             <?php echo $post->transaction_type;?> <br/>
                                             <?php echo $post->transaction_particulars;?>
                                        </td>
                                        <td >
                                             <?php echo $post->customer_name;?><br/>
                                             <?php echo $post->phone;?>
                                        </td>
                                        <td class="text-right">
                                             <?php echo $post->currency;?>. <?php echo number_to_currency($post->amount);?>
                                             <br/><br/>
                                             Balance: <?php echo $post->currency;?>. <?php echo number_to_currency($post->organization_balance);?>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->status==1){
                                                    echo '<span class="label label-xs label-success">SUccessful</span>';
                                                }else{
                                                    echo '<span class="label label-xs label-success">No Yet</span>';
                                                }
                                                if($post->is_reversed){
                                                    echo '<br/><br/>';
                                                    echo '<span class="label label-xs label-warning">Reversed</span>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <!--<a href="<?php echo site_url('admin/safaricom/delete_c2b/'.$post->id);?>">Delete</a>-->
                                            <?php 
                                                if($post->status == 1){?>
                                                   <a href="<?php echo site_url('admin/safaricom/forward/'.$post->id);?>">Forward</a> 
                                                   &nbsp;&nbsp;
                                            <?php }?>
                                            <a href="<?php echo site_url('admin/safaricom/view/'.$post->id);?>">View</a>
                                            <?php 
                                                if(!$post->is_reversed){?>
                                                    &nbsp;&nbsp;
                                                   <a href="<?php echo site_url('admin/safaricom/reverse/'.$post->id);?>" data-title="Enter your password to reverse this transaction" data-content='This will reverse all records, this action is irreversible?' class="btn btn-xs red prompt_confirmation_message_link">Reverse</a>
                                            <?php }?>
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
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No C2B Payments to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
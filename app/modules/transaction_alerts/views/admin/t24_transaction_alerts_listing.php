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
                    <?php echo form_open('admin/transaction_alerts/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Equity Bank Transaction Alerts</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
                        <table class="table  table-striped table-bordered table-hover table-header-fixed table-condensed">
                            <thead>
                                <tr>
                                    <th width='2%'>
                                         <input type="checkbox" name="check" value="all" class="check_all">
                                    </th>
                                    <th width='2%'>
                                        #
                                    </th>
                                    <th>
                                        Receipt time
                                    </th>
                                    <th>
                                        Transaction Date
                                    </th>
                                    <th>
                                        Account Number
                                    </th>
                                    <th class='text-right'>
                                        Amount
                                    </th>
                                    <th width="40%">
                                        Transaction Details
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo timestamp_to_date_and_time($post->created_on); ?></td>
                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                        <td><?php echo $post->account_number; ?></td>
                                        <td class='text-right'><?php echo $post->currency.' '.number_to_currency($post->transaction_amount); ?></td>
                                        <td>
                                            <strong>Transaction ID:</strong> <?php echo $post->transaction_id; ?><br/>
                                            <strong>Transaction Transaction Type:</strong> <?php echo $post->transaction_type; ?><br/>
                                            <strong>Transaction Narrative:</strong> <?php echo $post->transaction_narrative; ?>
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
                            No Equity Bank transaction alerts to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
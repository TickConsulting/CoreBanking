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
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> STK Requests</p>
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
                                        Shortcode
                                    </th>
                                    <th>
                                        Request ID
                                    </th>
                                    <th>
                                       Phone Number
                                    </th>
                                    <!--<th>
                                        Callback
                                    </th>-->
                                    <th>
                                        Requested On
                                    </th>
                                    <th class="text-right">
                                        Amount
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                     <th>
                                        Response / Result
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
                                        <td class="two-entry">
                                            <?php echo $post->shortcode.' ('.$post->id.')'; ?>                                            
                                        </td>
                                        <td style="font-size:11px;">
                                            R-ID: <?php echo $post->request_id;?><br/>
                                            C-R-ID: <?php echo $post->checkout_request_id;?><br/>
                                            T-ID: <?php echo $post->transaction_id;?><br/>
                                            RN: <?php echo $post->reference_number;?><br/>
                                            ACC-ID:<?php echo $this->accounts_m->get_account_number($post->account_id);?><br/>

                                            M-R-ID:<?php echo $post->merchant_request_id;?>
                                        </td>
                                        <td>
                                            <?php echo $post->phone; ?>
                                        </td>
                                        <!--<td class="two-entry">
                                            <?php echo $post->request_callback_url; ?>                                            
                                        </td>-->
                                        <td><?php echo timestamp_to_datetime_from_timestamp($post->created_on); ?></td>
                                        <td class="text-right">
                                            <?php 
                                                echo number_to_currency($post->amount).'<br/><br/>';
                                                echo number_to_currency($post->charge).'<br/><br/>';
                                             ?>
                                        </td>
                                        <td class="two-entry">
                                            <?php if(is_numeric($post->response_code) && $post->response_code==0){echo '<span class="label label-success label-xs">Success</span>';}else{echo '<span class="label label-warning label-xs">Failed</span>';} ?><br/><br/>
                                        <?php if(is_numeric($post->result_code) && $post->result_code==0){echo '<span class="label label-success label-xs">Success</span>';}else{if($post->result_code){echo '<span class="label label-danger label-xs">Failed</span>';}else{echo '<span class="label label-warning label-xs">No Result</span>';}} ?>

                                            <?php 
                                                if($post->is_reversed){
                                                    echo '<br/><br/>';
                                                    echo '<span class="label label-xs label-warning">Reversed</span>';
                                                }
                                            ?>
                                        </td>
                                        <td >
                                             <?php echo $post->response_description; ?><br/><br/>
                                            <?php echo $post->result_description; ?><br/><br/>
                                            Callback Sent: <?php echo ($post->callback_sent); ?>
                                            ID: <?php echo $post->id;?>
                                        </td>
                                        <td>
                                            <?php if(is_numeric($post->result_code)){?>
                                                   <a href="<?php echo site_url('admin/safaricom/forward_request/'.$post->id);?>">Forward</a> 
                                                   <br/>
                                                   <br/>
                                            <?php }?>

                                            Callback: <?php echo ($post->request_callback_url); ?><br/><br/>
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
                            No B2B Transactions to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
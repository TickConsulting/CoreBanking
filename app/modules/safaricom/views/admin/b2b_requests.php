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
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> B2B Requests</p>
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
                                        Sender
                                    </th>
                                    <th>
                                       Receiver
                                    </th>
                                    <th>
                                        Command ID
                                    </th>
                                    <th>
                                        Request Time.
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
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><?php echo $i+1;?></td>
                                        <td class="two-entry">
                                            <?php echo $post->sender_party; ?>
                                            <br/><br/>
                                            <?php echo $post->sender_party_public_name; ?>
                                        </td>
                                        <td class="two-entry">
                                            <?php echo $post->receiver_party; ?>
                                            <br/><br/>
                                            <?php echo $post->receiver_party_public_name; ?>
                                        </td>
                                        <td>
                                            <?php echo $post->command_id?>
                                        </td>
                                        <td class="two-entry">
                                            <?php echo timestamp_to_datetime_from_timestamp($post->request_time);?>
                                        </td>
                                        <td class="text-right">
                                             <?php if(strlen($post->currency)<5){
                                                echo $post->currency.'.';
                                            }?> <?php echo number_to_currency($post->request_amount);?>
                                        </td>
                                        <td class="two-entry">
                                            <?php if(is_numeric($post->response_code) && $post->response_code==0){echo '<span class="label label-success label-xs">Success</span>';}else{echo '<span class="label label-warning label-xs">Failed</span>';} ?><br/><br/>
                                            
                                        <?php if(is_numeric($post->result_code) && $post->result_code==0){echo '<span class="label label-success label-xs">Success</span>';}else{echo '<span class="label label-danger label-xs">No Result</span>';} ?>
                                        </td>
                                        <td >
                                             <?php echo $post->response_description; ?><br/><br/>
                                            <?php echo $post->result_description; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('admin/safaricom/view_b2b_request/'.$post->id);?>" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View</a>
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
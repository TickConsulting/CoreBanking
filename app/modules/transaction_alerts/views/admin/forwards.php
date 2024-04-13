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
                                            Created On
                                        </th>
                                        <th width="">
                                            Transaction Description
                                        </th>
                                        <th class='text-left'>
                                            Status
                                        </th>
                                        <th class='text-left'>
                                            Response
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                        <tr>
                                            <td><?php echo $i+1;?></td>
                                            <td><?php echo timestamp_to_date_and_time($post->created_on); ?></td>
                                            <td>
                                                <strong>URL:</strong><?php echo $post->url; ?><br/>
                                                <strong>Transaction Currency: </strong><?php echo $post->tranCurrency; ?><br/>
                                                <strong>Transaction Date: </strong><?php echo $post->tranDate; ?><br/>
                                                <strong>Transaction ID: </strong><?php echo $post->tranid; ?><br/>
                                                <strong>Transaction Amount: </strong><?php echo $post->tranAmount; ?><br/>
                                                <strong>C/D: </strong><?php echo $post->trandrcr; ?><br/>
                                                <strong>Account Number: </strong><?php echo $post->accid; ?><br/>
                                                <strong>Reference Number: </strong><?php echo $post->refNo; ?><br/>
                                                <strong>Transaction Type: </strong><?php echo $post->tranType; ?><br/>
                                                <strong>Transaction Particulars: </strong><?php echo $post->tranParticular; ?><br/>
                                                <strong>Transaction Remarks: </strong><?php echo $post->tranRemarks; ?>
                                            </td>
                                            <td class='text-left'>
                                                <?php 
                                                    if($post->is_forwarded){
                                                        echo "
                                                            <span class=\"label label-success\">Forwarded</span>
                                                        ";
                                                    }else{
                                                        echo "
                                                            <span class=\"label label-default\">Forward Pending</span>
                                                        ";
                                                    }
                                                ?>    
                                            </td>
                                            <td>
                                                <?php echo $post->response; ?>
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
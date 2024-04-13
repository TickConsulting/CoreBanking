<?php if(!empty($posts)){ ?>
<?php echo form_open('group/loans/action', ' id="form"  class="form-horizontal"'); ?> 

<?php if ( ! empty($pagination['links'])): ?>
    <div class="row col-md-12">
        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Contributions</p>
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
                <th width='2%'>
                    #
                </th>
                <th>
                    <?php
                        $default_message='Member Name';
                        $this->languages_m->translate('member_name',$default_message);
                    ?>
                </th>
                <th>
                    Send Date
                </th>
                <th class='text-right'>
                    <?php
                        $default_message='Amount';
                        $this->languages_m->translate('amount',$default_message);
                    ?>
                     (<?php echo $this->group_currency; ?>)
                </th>  
                <th>
                    <?php
                        $default_message='Invoice Type';
                        $this->languages_m->translate('invoice_type',$default_message);
                    ?>
                </th>
                <th>
                    <?php
                        $default_message='Notifications';
                        $this->languages_m->translate('notifications',$default_message);
                    ?>
                </th>
                <th>
                    View Loan
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                <tr>
                    <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                    <td><?php echo $i+1;?></td>
                    <td>
                        <?php echo $post->first_name.' '.$post->last_name; ?>
                    </td>
                    <td>
                      <?php echo timestamp_to_date_and_time($post->created_on);?>  
                    </td>
                    <td class='text-right'><?php echo number_to_currency($post->amount_payable); ?></td>
                    <td>
                        <?php 
                            if($post->invoice_type==1){
                                echo "<span class='label label-default'>Invoice</span>";
                            }else if($post->invoice_type==2){
                                echo "<span class='label label-success'>Fine Invoice</span>";
                            }else if($post->invoice_type==3){
                                 echo "<span class='label label-primary'>Outstanding balance fine invoice</span>";
                            }
                        ?>
                    </td>
                    <td>
                        <?php 
                            if($post->notification_created){
                                echo "<span class='label label-success'>Notification</span>";
                            }if($post->email_sent){
                                echo "<span class='label label-success'>Email</span>";
                            }if($post->sms_sent){
                                echo "<span class='label label-success'>SMS</span>";
                            }
                        ?>
                    </td>
                    <td>
                        <a href="<?php echo site_url('group/loans/loan_statement/'.$post->loan_id);?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i> View Loan</a>
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

    <div class="clearfix"></div>
<?php echo form_close(); ?>
<?php }else{ ?>
    <div class="alert alert-info">
        <h4 class="block">
            <?php
                $default_message='Information! No records to display';
                $this->languages_m->translate('no_records_to_display',$default_message);
            ?>
            
        </h4>
        <p>
            There are no invoices or fines sent.
        </p>
    </div>
<?php } ?>
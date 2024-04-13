<?php if(!empty($posts)){ ?>
<?php echo form_open('group/withdrawals/action', ' id="form"  class="form-horizontal"'); ?> 

<?php if ( ! empty($pagination['links'])): ?>
    <div class="row col-md-12">
        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Withdrawals</p>
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
                    Withdrawal Date
                </th>
                <th>
                    <?php
                        $default_message='Description';
                        $this->languages_m->translate('description',$default_message);
                    ?>
                </th>
                <th class='text-right'>
                    <?php
                        $default_message='Amount';
                        $this->languages_m->translate('amount',$default_message);
                    ?>

                     (<?php echo $this->group_currency; ?>)
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $i = $this->uri->segment(5, 0); $i++; foreach($posts as $post): ?>
                <tr>
                    <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo timestamp_to_date($post->withdrawal_date); ?></td>
                    <td>
                        <?php
                            if($post->type==1||$post->type==2||$post->type==3||$post->type==4){
                                echo $withdrawal_transaction_names[$post->type].' for '.$expense_category_options[$post->expense_category_id];
                            }else if($post->type==5||$post->type==6||$post->type==7||$post->type==8){
                                echo $withdrawal_transaction_names[$post->type].' for '.$asset_options[$post->asset_id];
                            }else if($post->type==9||$post->type==10||$post->type==11||$post->type==12){
                                echo $withdrawal_transaction_names[$post->type];
                                if($post->member_id){ echo ' to '.$this->group_member_options[$post->member_id];}
                            }else if($post->type==21||$post->type==22||$post->type==23||$post->type==24){
                                echo $withdrawal_transaction_names[$post->type].' to '.$this->group_member_options[$post->member_id].' for '.$contribution_options[$post->contribution_id];
                            }else if($post->type==13||$post->type==14||$post->type==15||$post->type==16){
                                echo $withdrawal_transaction_names[$post->type];
                            }else if($post->type==25||$post->type==26||$post->type==27||$post->type==28){
                                echo $withdrawal_transaction_names[$post->type];
                            }else if($post->type==29||$post->type==30||$post->type==31||$post->type==32){
                                echo $withdrawal_transaction_names[$post->type];
                            }
                            if($post->description){
                                echo ' : '.$post->description;
                            }

                            if($post->transaction_alert_id){
                                echo '  <span class="label label-sm label-success"> Reconciled </span> ';
                            }
                        ?>
                    </td>
                    <td  class='text-right'>
                        <?php echo number_to_currency($post->amount); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
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
        <h4 class="block">
            <?php
                $default_message='Information! No records to display';
                $this->languages_m->translate('no_records_to_display',$default_message);
            ?>
        </h4>
        <p>
            No withdrawals to display.
        </p>
    </div>
<?php } ?>
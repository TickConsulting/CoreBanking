<?php if(isset($pdf_true) && $pdf_true){?>
    <link href="<?php echo site_url();?>assets/styling/style.css" rel="stylesheet" type="text/css" /> 
    <link href="<?php echo site_url();?>templates/admin_themes/groups/css/custom.css" rel="stylesheet" type="text/css" /> 
    <style type="text/css">
        .statement_types,.filter_header,.print_layout{
            display: none;
        }
        #statement_paper .table td {
            font-size: 9px;
            padding: .25rem;
        }
        .statement-header-content {
            font-size: 9px;
        }
        #statement_header,.header_paper,.contribution_name,#statement_paper .table th,#statement_footer{
            font-size: 10px;
        }
        #statement_paper {
            padding: none;
            box-shadow: none;
        }
        .pdf_layout{
            display: none;  
        }
        @media print
        {    
            .no-print, .no-print *
            {
                display: none !important;
            }
        }
    </style>
<?php } ?>
<div class="">
    <div class="col-md-12">
        <div id="statement_paper">
            <div class="row" id="statement_header">
                <div class="col-xs-6 col-sm-6">
                    <div class="invoice-logo">
                        <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo); ?>" alt="" class='group-logo image-responsive' /> 
                    </div>
                </div> 
                <div class="col-sm-6 text-right" >
                    <div class="company-address">
                        <span class="bold uppercase"><?php echo $this->group->name; ?></span>
                        <?php echo nl2br($this->group->address); ?><br/>
                        <span class="bold"><?php echo translate('Telephone')?>: </span> <?php echo $this->group->phone; ?>
                        <br/>
                        <span class="bold"><?php echo translate('E-mail Address')?>: </span> <?php echo $this->group->email; ?>
                        <br/>
                    </div>
                </div>
            </div>
            <div class="row header_paper">
                <div class="col-xs-6 col-sm-6">
                    <div>
                        <strong><?php echo translate('Name')?>: </strong> <?php echo $member->first_name.' '.$member->last_name?>
                        <br>
                        <strong><?php echo translate('Phone')?>: </strong> <?php echo $member->phone;?>
                        <br>
                        <strong><?php echo translate('Email Address')?>: </strong> <?php echo $member->email;?>
                        <br>
                        <strong><?php echo translate('Member Number')?>: </strong> <?php echo $member->membership_number;?>
                        <br>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <div>
                        <strong><?php echo translate('Statement as at')?>: </strong> <?php echo timestamp_to_date(time()); ?>
                        <br>
                        <strong><?php echo translate('Statement Period')?>: </strong> <?php echo timestamp_to_date($from); ?> <span class="bold">to</span> <?php echo timestamp_to_date($to); ?>
                        <br>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-7 col-xs-6 statement-header-content">
                    <span class="bold"><?php echo translate('Total Member Investments')?>: </span> <?php echo $this->group_currency; ?> <?php echo number_to_currency(array_sum($total_member_deposit_amounts)); ?><br/>
                    <?php 
                        foreach($contribution_display_options as $contribution_id => $name): 
                            $amount = isset($total_member_deposit_amounts[$contribution_id])?$total_member_deposit_amounts[$contribution_id]:0;
                            if($amount):
                                echo '<span class="bold">'.$name.':</span>'.$this->group_currency.' '.number_to_currency($amount).'<br/>';
                            endif;
                        endforeach; 
                    ?>
                </div>
                <div class="col-md-5 col-xs-6">
                </div>
            </div>
            <div class="clearfix"></div>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <?php 
                        $html = '';
                        if(empty($posts)){ 
                    ?>
                        <p>
                            <div class="m-alert m-alert--outline alert alert-info fade show mr-3" role="alert">
                                <strong><?php echo translate('No Records');?>!</strong>
                                <br/><br/>
                                <?php echo translate('No transaction records to display')?>
                            </div>
                        </p>
                    <?php 
                        }else{
                            $count = 0;
                            foreach($contribution_display_options as $contribution_id => $name): 
                                $amount = isset($total_member_deposit_amounts[$contribution_id])?$total_member_deposit_amounts[$contribution_id]:0;
                                if($amount) {
                                    if(isset($posts[$contribution_id])){
                                        if($count>0){
                                            echo '<br/>';
                                        }
                                        ++$count;
                    ?>
                                        <span class="bold contribution_name"><?php echo $contribution_options[$contribution_id]; ?></span>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-condensed table-statement">
                                                <thead>
                                                    <tr>
                                                        <th nowrap width="15%" class="text-left">Posting Date</th>
                                                        <th width="40%" class="text-left">Description</th>
                                                        <th width="15%" class="text-right">Debit Amount</th>
                                                        <th width="15%" class="text-right">Credit Amount</th>
                                                        <th width="15%" class="text-right">Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                        $opening_balance = isset($opening_balances[$contribution_id])?$opening_balances[$contribution_id]:0; 
                                                        $closing_balance = 0; 
                                                        $closing_balance += $opening_balance; 
                                                    ?>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-right"><?php echo number_to_currency($opening_balance);?></td>
                                                    </tr>
                                                    <?php foreach($posts[$contribution_id] as $post){
                                                            if($post->transaction_type==25):
                                                                $closing_balance -= $post->amount;
                                                        ?>
                                                                <tr>
                                                                    <td nowrap><?php echo timestamp_to_date($post->transaction_date);?></td>
                                                                    <td nowrap>
                                                                        <?php if($post->member_to_id){
                                                                            $member_to_id = translate('Another member');
                                                                            if($post->member_to_id){
                                                                                $member_to_id = $this->group_member_options[$post->member_to_id];
                                                                            }
                                                                            echo translate("Share transfer from")." ".$this->group_member_options[$post->member_id].' to '.$member_to_id;
                                                                        }else{
                                                                            if($post->transfer_to==1){
                                                                                echo "Contribution transfer to ".$contribution_options[$post->contribution_to_id];
                                                                            }else if($post->transfer_to==2){
                                                                                if($post->contribution_to_id){
                                                                                    echo translate("Contribution transfer to")." ".$contribution_options[$post->contribution_to_id];
                                                                                }else if($post->fine_category_to_id){
                                                                                    echo translate("Contribution transfer to")." ".$fine_category_options[$post->fine_category_to_id];
                                                                                }
                                                                                if($post->fine_category_to_id){
                                                                                    echo '- '.translate('For').' '.$fine_category_options[$post->fine_category_to_id];
                                                                                }else{
                                                                                    echo ' - '.translate('For').' '.$contribution_options[$post->contribution_to_id];
                                                                                }
                                                                            }else if($post->transfer_to==3){
                                                                                echo ' '.translate('To loan').' '.$this->loans_m->get_loan_details($post->loan_to_id);
                                                                            }else if($post->transfer_to==4){
                                                                                echo $this->group_member_options[$post->member_id].translate('to').' '.$this->group_member_options[$post->share_transfer_recipient_member_id];
                                                                            }
                                                                        }
                                                                    ?>
                                                                    </td>
                                                                    <td class="text-right"><?php echo number_to_currency($post->amount);?></td>
                                                                    <td></td>
                                                                    <td class="text-right"><?php echo number_to_currency($closing_balance);?></td>
                                                                </tr>
                                                    <?php 
                                                            elseif ($post->transaction_type==26):
                                                                $closing_balance += $post->amount;
                                                        ?>
                                                                <tr>
                                                                    <td><?php echo timestamp_to_date($post->transaction_date);?></td>
                                                                    <td>
                                                                    <?php 
                                                                        if($post->member_from_id){
                                                                            $member_from = translate('Another member');
                                                                            if($post->member_from_id){
                                                                                $member_from = $this->group_member_options[$post->member_from_id];
                                                                            }
                                                                            echo translate("Share transfer from").' '.$member_from.' to '.$this->group_member_options[$post->member_id];
                                                                        }else{
                                                                            echo translate('Contribution transfer from').' ';
                                                                            if($post->transfer_to==1){
                                                                                if($post->contribution_from_id=='loan'){
                                                                                    echo translate('From loan').' -'.$this->loans_m->get_loan_details($post->loan_from_id);
                                                                                }else{
                                                                                    echo "'".$contribution_options[$post->contribution_from_id];
                                                                                }
                                                                            }else if($post->transfer_to==2){
                                                                                if($post->contribution_to_id){
                                                                                    if($post->contribution_from_id=='loan'){
                                                                                        echo translate('From loan').' -'.$this->loans_m->get_loan_details($post->loan_from_id);
                                                                                    }else{
                                                                                        echo "'".$contribution_options[$post->contribution_from_id];
                                                                                    }
                                                                                }else if($post->fine_category_to_id){
                                                                                    if($post->contribution_from_id=='loan'){
                                                                                        echo translate('From loan').' -'.$this->loans_m->get_loan_details($post->loan_from_id);
                                                                                    }else{
                                                                                        echo "'".$contribution_options[$post->contribution_from_id];
                                                                                    }
                                                                                }
                                                                                if($post->fine_category_to_id){
                                                                                    echo '- '.translate('For').$fine_category_options[$post->fine_category_to_id];
                                                                                }else{
                                                                                    echo ' - For '.$contribution_options[$post->contribution_to_id];
                                                                                }
                                                                            }else if($post->transfer_to==3){
                                                                                echo ' To loan '.$this->loans_m->get_loan_details($post->loan_to_id);
                                                                            }else if($post->transfer_to==4){
                                                                                echo $this->group_member_options[$post->member_id].' to '.$this->group_member_options[$post->share_transfer_recipient_member_id];
                                                                            }
                                                                        }
                                                                    ?>
                                                                    </td>
                                                                    <td></td>
                                                                    <td class="text-right"><?php echo number_to_currency($post->amount);?></td>
                                                                    <td class="text-right"><?php echo number_to_currency($closing_balance);?></td>
                                                                </tr>
                                                    <?php
                                                            elseif ($post->transaction_type==21 || $post->transaction_type==22 || $post->transaction_type==23 || $post->transaction_type==24 ):
                                                                $closing_balance -= $post->amount;
                                                    ?>
                                                                <tr>
                                                                    <td><?php echo timestamp_to_date($post->transaction_date);?></td>
                                                                    <td>
                                                                        <?php echo translate('Contribution Refund'); ?>
                                                                    </td>
                                                                    <td class="text-right"><?php echo number_to_currency($post->amount);?></td>
                                                                    <td></td>
                                                                    <td class="text-right"><?php echo number_to_currency($closing_balance);?></td>
                                                                </tr>

                                                    <?php   elseif ($post->transaction_type==30):
                                                                $closing_balance -= $post->amount;

                                                    ?>
                                                                <tr>
                                                                    <td><?php echo timestamp_to_date($post->transaction_date);?></td>
                                                                    <td>
                                                                        <?php echo translate('Contribution Transfer to loan'); ?>
                                                                    </td>
                                                                    <td class="text-right"><?php echo number_to_currency($post->amount);?></td>
                                                                    <td></td>
                                                                    <td class="text-right"><?php echo number_to_currency($closing_balance);?></td>
                                                                </tr>
                                                    <?php
                                                            else:
                                                                $closing_balance += $post->amount;
                                                    ?>
                                                            <tr>
                                                                <td><?php echo timestamp_to_date($post->transaction_date);?></td>
                                                                <td>   
                                                                    <?php
                                                                     echo translate('Contribution Payment').' '.(isset($account_options[$post->account_id])?' to '.$account_options[$post->account_id]:''); ?>
                                                                </td>
                                                                <td></td>
                                                                <td class="text-right"><?php echo number_to_currency($post->amount);?></td>
                                                                <td class="text-right"><?php echo number_to_currency($closing_balance);?></td>
                                                            </tr>
                                                    <?php 
                                                            endif;
                                                    }?>
                                                </tbody>
                                            </table>
                                        </div>
                    <?php
                                    }elseif(!in_array($contribution_id,$contribution_ids)&&!empty($contribution_ids)){

                                    }else{
                    ?>
                                        <?php
                                            $html.='
                                                <br/>
                                                <span class="bold">'.$contribution_options[$contribution_id].'</span>
                                                <p>
                                                    <div class="m-alert m-alert--outline alert alert-info fade show mr-3" role="alert">
                                                        <strong>'. translate('No Records').'!</strong>
                                                        <br/><br/>
                                                        '.translate('No transaction records have been posted for the period').' '.timestamp_to_date($from).' <span class="bold">to</span>'.timestamp_to_date($to).'
                                                    </div>
                                                </p>';
                                        ?>
                    <?php
                                    }
                                }
                            endforeach;
                            echo $html;
                        }
                    ?>
                </div>
            </div>

            <div id="statement_footer">
                <p style="text-align:center;">© <?php echo date('Y')?>. <?php echo translate('This statement was issued with no alteration')?> </p>
                <p style="text-align:center;">
                     <strong> <?php echo translate('Powered by')?>:</strong>
                    <br>
                    <img width="150px" src="<?php echo site_url('uploads/logos/'.$this->application_settings->paper_header_logo);?>" alt="<?php echo $application_settings->application_name;?> Logo" ?="">
                </p>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <hr>
    <div class="row print_layout mt-3">
        <div class="col-xs-12">
            <button class="btn btn-sm btn-info hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> 
                <?php echo translate('Print'); ?>
            </button>
            &nbsp;&nbsp;&nbsp;
            <?php $search_string = substr(basename($_SERVER['REQUEST_URI']),strpos(basename($_SERVER['REQUEST_URI']), "?"));
            ?>
            <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.TRUE;?>" target="_blank"><i class='fa fa-file'></i>
                <?php echo translate('Generate PDF'); ?>
            </a>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready( function(){
        $(".m-select2-search").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            width: "100%"
        });
        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
    });
</script>
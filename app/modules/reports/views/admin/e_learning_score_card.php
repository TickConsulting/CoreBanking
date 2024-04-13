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
                    <div class="clearfix">
                        <a href="<?php echo site_url("admin/reports/e_learning_score_card?generate_excel=1") ?>" class="btn btn-xs blue pull-right">Export</a>
                        <br/><br/>
                    </div>
                    <?php echo form_open(current_url(),' id="form"  class="form-horizontal" '); ?> 

                        <table class="table table-condensed table-striped table-header-fixed">
                            <thead>
                                <tr>
                                    <th width="8px">
                                        #
                                    </th>
                                    <th width="10%">
                                        Branch
                                    </th>
                                    <th width="50%">
                                        Group
                                    </th>
                                    <th>
                                        Score Card
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($posts as $post): ?>
                                    <tr>
                                        <td><?php echo $i++;?></td>
                                        <td>
                                            <?php
                                                $count = 1; 
                                                foreach($group_bank_branch_pairing_arrays[$post->id] as $bank_branch_id):
                                                    if($count==1){
                                                        echo $bank_branch_options[$bank_branch_id];
                                                    }else{
                                                        echo ",".$bank_branch_options[$bank_branch_id];
                                                    }
                                                endforeach;
                                            ?>
                                        </td>
                                        
                                        <td>

                                            <a target="_blank" href="<?php echo site_url("admin/groups/view/".$post->id); ?>"><?php echo $post->name; ?></a>
                                            <h6>Sign Up Date: <?php echo timestamp_to_date($post->created_on); ?></h6>
                                            <h6>Membership Details</h6>
                                            <table class="table table-condensed table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td width="70%">Group Members</td>
                                                        <td width="30%"><?php echo $post->size; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="70%">Registered</td>
                                                        <td width="30%"><?php echo $post->active_size?$post->active_size:1; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="70%">Logged In</td>
                                                        <td width="30%"><?php echo $group_member_logged_in_counts_array[$post->id]; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td width="70%">Logged In %</td>
                                                        <td width="30%"><?php echo $group_member_logged_in_percentage = $group_member_logged_in_percentages_array[$post->id]; ?> %</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <h6>Transaction Details</h6>
                                            <table class="table table-condensed table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td width="70%">Number of Transactions</td>
                                                        <td width="30%">
                                                            <?php 
                                                                if(isset($group_transaction_alert_counts_array[$post->id])){
                                                                    echo $transaction_alert_count = $group_transaction_alert_counts_array[$post->id];
                                                                }else{
                                                                    echo $transaction_alert_count = 0;
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="70%">Number of Transactions matched to Members</td>
                                                        <td width="30%">
                                                            <?php 
                                                                if(isset($group_member_deposit_reconciled_counts_array[$post->id])){
                                                                    echo $group_member_deposit_reconciled_count = $group_member_deposit_reconciled_counts_array[$post->id];
                                                                }else{
                                                                    echo $group_member_deposit_reconciled_count = 0;
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="70%">% of Transactions matched to Members</td>
                                                        <td width="30%">
                                                            <?php 
                                                                $transaction_alert_count = $transaction_alert_count>$post->size?$post->size:$transaction_alert_count;
                                                                echo $group_member_deposit_reconciled_percentage = round($transaction_alert_count/$post->size*100,2)." % ";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>
                                            <?php $score = 0; ?>
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th class="text-right">Score</th>
                                                        <th  class="text-right">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Correct Name</td>
                                                        <td class="text-right">
                                                            <?php
                                                                $group_score = 0;

                                                                $group_score += 3;
                             
                                                                if (strpos(strtolower($post->name),'test')) {
                                                                    $group_score += 2;
                                                                }
                                                                echo $group_score;
                                                            ?>
                                                        </td>
                                                        <td class="text-right">5</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Members Logged In</td>
                                                        <td class="text-right">
                                                            <?php
                                                                $member_score = 0;
                                                                if($group_member_logged_in_percentage>=95){
                                                                    $member_score += 10;
                                                                }else if($group_member_logged_in_percentage>=85){
                                                                    $member_score += 8;
                                                                }else if($group_member_logged_in_percentage>=75){
                                                                    $member_score += 6;
                                                                }else if($group_member_logged_in_percentage>=65){
                                                                    $member_score += 4;
                                                                }else if($group_member_logged_in_percentage>=55){
                                                                    $member_score += 2;
                                                                }else{
                                                                    $member_score += 0;
                                                                }
                                                                echo $member_score;
                                                            ?>
                                                        </td>
                                                        <td class="text-right">10</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Transactions by Members</td>
                                                        <td class="text-right">
                                                            <?php
                                                                $transaction_score = 0;
                                                                if($group_member_deposit_reconciled_percentage>=95){
                                                                    $transaction_score += 15;
                                                                }else if($group_member_deposit_reconciled_percentage>=85){
                                                                    $transaction_score += 12;
                                                                }else if($group_member_deposit_reconciled_percentage>=75){
                                                                    $transaction_score += 9;
                                                                }else if($group_member_deposit_reconciled_percentage>=65){
                                                                    $transaction_score += 6;
                                                                }else if($group_member_deposit_reconciled_percentage>=55){
                                                                    $transaction_score += 3;
                                                                }else{
                                                                    $transaction_score += 0;
                                                                }
                                                                echo $transaction_score;
                                                            ?>
                                                        </td>
                                                        <td class="text-right">15</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Totals</td>
                                                        <td class="text-right"><?php echo $group_score + $member_score + $transaction_score; ?></td>
                                                        <td class="text-right">30</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No results found.
                        </p>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
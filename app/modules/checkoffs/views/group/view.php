 <table class="table table-hover table-sm table-bordered">
 <!-- <table class="table table-hover table-sm table-borderless"> -->
    <tbody>
        <tr>
            <th nowrap>
                <?php echo translate('Checkoff Date'); ?>
            </th>
            <td>
                : <?php echo timestamp_to_date($post->checkoff_date); ?>
            </td>
        </tr>
        <tr>
            <th nowrap>
                <?php echo translate('Account'); ?>
            </th>
            <td>
                : <?php echo $accounts[$post->account_id]; ?>
            </td>
        </tr>
        <tr>
            <th nowrap>
                <?php echo translate('Grand Total Checkoff Amount:'); ?>
            </th>
            <td class="checkoff_amount_grandtotal">
                
            </td>
        </tr>
    </tbody>
</table>

<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th nowrap><?php echo translate('Member'); ?></th>
                <?php
                    if(!empty($membership_numbers)){
                        echo '<th nowrap>'. translate('Membership Number').'</th>';
                    }
                
                    $totals = array(); $grand_total = 0;foreach($checkoff_amounts as $contribution_id => $members): $totals[$contribution_id] = 0; ?>
                    <th class='text-right' nowrap><?php echo $contribution_options[$contribution_id]; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach($member_ids as $member_id): ?>
                <tr>
                    <th scope="row"><?php echo $i++;?></th>
                    <td nowrap><?php echo $this->group_member_options[$member_id]; ?></td>
                    <?php
                        if(!empty($membership_numbers)){
                            echo '<td nowrap>'.$membership_numbers[$member_id].'</td>';
                        }
                        foreach($checkoff_amounts as $contribution_id => $members): 
                            $totals[$contribution_id] += isset($checkoff_amounts[$contribution_id][$member_id])?$checkoff_amounts[$contribution_id][$member_id]:0; 
                            $grand_total += isset($checkoff_amounts[$contribution_id][$member_id])?$checkoff_amounts[$contribution_id][$member_id]:0; 
                    ?>
                    <th class='text-right' nowrap>
                        <?php 
                            echo isset($checkoff_amounts[$contribution_id][$member_id])?' '.number_to_currency($checkoff_amounts[$contribution_id][$member_id]):' '.number_to_currency(0); ?></th>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Totals</td>
                <?php
                    foreach($checkoff_amounts as $contribution_id => $members):?>
                        <th class="text-right"><?php echo $this->group_currency.' '.number_to_currency($totals[$contribution_id]);?></th>
                <?php
                    endforeach;
                ?>
            </tr>
        </tfoot>
    </table>
</div>

<div class="row pt-2">
    <div class="col-lg-12 m--align-left">
        <a class="btn btn-sm btn-primary" href="<?php echo current_url().'?generate_pdf=1' ?>" target="_blank"><i class='la la-file-pdf-o'></i>
            <?php echo translate('Generate PDF'); ?>
        </a>
        &nbsp;&nbsp;&nbsp;
        <a class="btn btn-sm btn-primary" href="<?php echo current_url().'?generate_excel=1'?>" target="_blank"><i class='la la-file-excel-o'></i>
            <?php echo translate('Generate EXCEL'); ?>
        </a>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.checkoff_amount_grandtotal').text(': <?php echo $this->group_currency." ".number_to_currency($grand_total)?>');
    });
</script>
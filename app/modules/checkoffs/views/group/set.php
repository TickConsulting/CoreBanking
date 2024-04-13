<?php echo form_open($this->uri->uri_string(), ' id="form"  class="form-horizontal form_submit set_checkoff"'); ?> 
<?php if ( ! empty($pagination['links'])): ?>
    <div class="row col-md-12">
        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Members</p>
    <?php 
        echo '<div class ="top-bar-pagination">';
        echo $pagination['links']; 
        echo '</div></div>';
        endif; 
    ?>
    <span class="error"></span>
    <div class="table-responsive">
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>
                        #
                    </th>
                    <th>
                        <?php echo translate('Name'); ?>
                    </th>
                    <?php if(!empty($membership_numbers)){ ?>
                    <th>
                        <?php echo translate('Membership Number'); ?>
                    </th>
                    <?php } ?>
                    <?php 
                        $totals = array(); 
                        foreach($contribution_options as $contribution_id => $contribution_name): 
                            $totals[$contribution_id] = 0;
                        ?>
                        <th class='text-right'><?php echo $contribution_name; ?> (<?php echo $this->group_currency; ?>)</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php $i = $this->uri->segment(5, 0); foreach($this->active_group_member_options as $member_id => $member_name): ?>
                <tr>
                    <th scope="row"><?php echo $i+1;?></h>
                    <td><?php echo $member_name; ?></td>
                    <td><?php echo $membership_numbers[$member_id]; ?></td>
                    <?php 

                    foreach($contribution_options as $contribution_id => $contribution_name): 
                        $amount = isset($member_checkoff_contribution_amount_pairings[$contribution_id][$member_id])?$member_checkoff_contribution_amount_pairings[$contribution_id][$member_id]:0;
                        $totals[$contribution_id] += $amount;
                    ?>
                        <th class='text-right'><?php echo form_input('checkoff_amounts['.$contribution_id.']['.$member_id.']',$amount,' data-contribution-id = "'.$contribution_id.'"  class="form-control amount checkoff-input currency input-sm" placeholder="Checkoff Amount"'); ?></th>
                    <?php endforeach; ?>
                </tr>
                <?php $i++;
                endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">
                        <?php echo translate('Totals'); ?>
                    </th>
                    <?php $grand_total = 0; foreach($contribution_options as $contribution_id => $contribution_name): $grand_total += $totals[$contribution_id]; ?>
                        <th data-contribution-id = "<?php echo $contribution_id; ?>" class=" totals text-right"><?php echo number_to_currency($totals[$contribution_id]); ?></th>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <th colspan="3">
                        <?php echo translate('Grand Total'); ?>
                    </th>
                    <th class="text-right" id='grand_total' colspan="<?php echo count($contribution_options); ?>">
                        <?php echo number_to_currency($grand_total); ?>
                    </th>
                </tr>
            </tfoot>    
        </table>
    </div>

    <!-- <div class="clearfix"></div> -->
    <div class="row col-md-12">
        <?php 
            if( ! empty($pagination['links'])): 
            echo $pagination['links']; 
            endif; 
        ?>  
    </div>
    <!-- <div class="clearfix"></div> -->
    <div class="row">
        <div class="col-lg-12 m--align-right">
            <a href="<?php echo $this->agent->referrer()?>">
                <button type="button" class="btn btn-secondary btn-sm">
                    <?php echo translate('Cancel'); ?>
                </button>
            </a>
            <button type="submit" class="btn btn-primary btn-sm submit_check_offs" name="submit">
                <?php echo translate('Set Checkoff Amounts'); ?>
            </button>
        </div>
    </div>

     <div class="row">
        <div class="col-lg-12 m--align-left">
            <a class="btn btn-sm btn-primary" href="<?php echo current_url().'?generate_pdf=1'?>" target="_blank"><i class='la la-file-pdf-o'></i>
                <?php echo translate('Generate PDF'); ?>
            </a>
            &nbsp;&nbsp;&nbsp;
            <a class="btn btn-sm btn-primary" href="<?php echo current_url().'?generate_excel=1'?>" target="_blank"><i class='la la-file-excel-o'></i>
                <?php echo translate('Generate EXCEL'); ?>
            </a>
        </div>
    </div>

<?php echo form_close(); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $('.checkoff-input').on('keyup',function(){
            var total = 0;
            var grand_total = 0;
            var contribution_id = $(this).attr('data-contribution-id');
            $('.checkoff-input[data-contribution-id="'+contribution_id+'"]').each(function(){
                var amount = $(this).val();
                total+=parseFloat(amount.replace(/,/g,''));
            });
            $('.totals[data-contribution-id="'+contribution_id+'"]').html(total.number_to_currency(2));

            $('.checkoff-input').each(function(){
                var amount = $(this).val();
                grand_total+=parseFloat(amount.replace(/,/g,''));
            });
            $('#grand_total').html(grand_total.number_to_currency(2));
        });

        $(document).on('submit','.set_checkoff',function(e){
            e.preventDefault();
            $('.submit_check_offs').addClass("m-loader m-loader--right m-loader--light").attr("disabled", true);
            $('.set_checkoff .error').slideUp();
            mApp.block('.set_checkoff', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('.set_checkoff .error').html('').slideUp();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("ajax/checkoffs/set_checkoff"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            toastr['success']('Member check off amounts successfully saved.',data.message);
                            window.location.href = data.refer
                        }else{
                            $('.set_checkoff .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry! </strong>'+data.message+'</div>').slideDown();
                                mUtil.scrollTop();
                        }
                    }else{
                        $('.set_checkoff .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                                mUtil.scrollTop();
                    }
                    mApp.unblock('.set_checkoff');
                    $('.submit_check_offs').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false);
                }
            });
        });
    });
    Number.prototype.number_to_currency = function(c, d, t){
    var n = this, 
        c = isNaN(c = Math.abs(c)) ? 2 : c, 
        d = d == undefined ? "." : d, 
        t = t == undefined ? "," : t, 
        s = n < 0 ? "-" : "", 
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
        j = (j = i.length) > 3 ? j % 3 : 0;
       return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };
</script>
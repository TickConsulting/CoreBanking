<?php echo form_open($this->uri->uri_string(), ' id="form"  class="form submit_checkoff form_submit" autocomplete="off"'); ?> 
    <span class="error"></span>
    <div class="form-group m-form__group row">
        <div class="col-lg-6">
            <label>
                <?php echo translate('Checkoff Date'); ?> <span class="required">*</span>
            </label>
            <?php echo form_input('checkoff_date',$this->input->post('checkoff_date')?$this->input->post('checkoff_date'):'','class="form-control date-picker" data-date-end-date="0d" autocomplete="off" id="checkoff_date" placeholder="Check Off Date" data-date-format="dd-mm-yyyy" data-date-viewmode="years"'); ?>
        </div>
        <div class="col-lg-6">
            <label class="">
                <?php echo translate('Checkoff Account'); ?> <span class="required">*</span>
            </label>
            <?php echo form_dropdown('account_id',array(''=>'--Select account to send check off to--')+$account_options,$this->input->post('account_id')?:'','class="form-control m-select2" id="account_id" placeholder="Select Account"');?>
        </div>
    </div>

    <div id="submit_checkoff" style="display:none;">
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
                            <th class='text-right' nowrap><?php echo $contribution_name; ?> (<?php echo $this->group_currency; ?>)</th>
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

        <div class="row">
            <div class="col-lg-12 m--align-right">
                <a href="<?php echo $this->agent->referrer()?>">
                    <button type="button" class="btn btn-secondary btn-sm">
                        <?php echo translate('Cancel'); ?>
                    </button>
                </a>
                <button type="submit" class="btn btn-primary btn-sm submit_check_offs" name="submit">
                    <?php echo translate('Submit Checkoff'); ?>
                </button>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function(){
        $('.m-select2').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        $('#account_id').on('change',function(){
            if($(this).val()==''||$('#checkoff_date').val()==''){
                $('#submit_checkoff').slideUp();
            }else{
                $('#submit_checkoff').slideDown('fast');
            }
        });

        $('.date-picker').datepicker({autoclose:true}).on('changeDate',function(e){ 
            if($(this).val()==''||$('#account_id').val()==''){
                $('#submit_checkoff').slideUp();
            }else{
                $('#submit_checkoff').slideDown('fast');
            }
        });

        $('#checkoff_date').on('blur',function(){
            if($(this).val()==''||$('#account_id').val()==''){
                $('#submit_checkoff').slideUp();
            }else{
                $('#submit_checkoff').slideDown('fast');
            }
        });
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

    });

     $(document).on('submit','.submit_checkoff',function(e){
            e.preventDefault();
            $('.submit_check_offs').addClass("m-loader m-loader--right m-loader--light").attr("disabled", true);
            $('.submit_checkoff .error').slideUp();
            mApp.block('.submit_checkoff', {
                message: 'Submitting checkoffs...',
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
            });

            var form = $(this);
            RemoveDangerClass(form);
            $('.submit_checkoff .error').html('').slideUp();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("ajax/checkoffs/submit_checkoff"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            toastr['success']('Member check offs successfully submitted.',data.message);
                            window.location.href = data.refer
                        }else{
                            $('.submit_checkoff .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry! </strong>'+data.message+'</div>').slideDown();
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('.submit_checkoff input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('.submit_checkoff select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                            mUtil.scrollTop();
                        }
                    }else{
                        $('.submit_checkoff .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                                mUtil.scrollTop();
                    }
                    mApp.unblock('.submit_checkoff');
                    $('.submit_check_offs').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false);
                }
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
<div>
    <div class="m-portlet__body m-demo__preview">
        <h4>
            <?php echo translate('Upload Loan Repayments Instructions'); ?>
        </h4>
        <?php echo form_open_multipart(current_url(),'class="form_submit m-form m-form--state" role="form" id="upload_excel_file" '); ?>
            <ol style="line-height: 2.2rem;">
                <li>
                    <?php echo translate('Download your loan repayment list import template file, by clicking the following link'); ?>.
                    <a class="m-badge m-badge--metal m-badge--wide" href='<?php echo site_url("group/deposits/loan_repayment_template_download"); ?>'><i class="la la-download"></i>
                         <?php echo translate('Download'); ?>
                    </a>
                </li>
                <li>
                    <?php echo translate('Open the downloaded template and open it with your favourite Excel sheet editor'); ?>.
                </li>
                <li>
                    <?php echo translate('Fill in each members repayment per loan'); ?>.
                </li>
                <li>
                    <?php echo translate('Save the Excel sheet and select it below to upload it'); ?>.
                </li>
            </ol>
            <div class="form-group m-form__group row">
                <div class="col-lg-6">
                    <label>
                        <?php echo translate('Loan Repayment File'); ?>
                        <span class="required">*</span>
                    </label>
                    <div class="custom-file">
                        <?php echo form_upload('loan_repayment_template','',' type="file" class="custom-file-input loan_repayment_template" id="customFile" name="loan_repayment_template" required');?>
                        <label class="custom-file-label" for="customFile">
                            <?php echo translate('Choose excel file'); ?>.
                        </label>
                    </div>
                    <span class="m-form__help">
                        <?php echo translate('Choose your loan repayment list file here'); ?>.
                    </span>
                </div>
                <div class="col-lg-6">
                    <label class="">
                        <?php echo translate('Account'); ?>
                        <span class="required">*</span>
                    </label>
                    <?php echo form_dropdown('account_id',array(''=>'Select an Account')+$account_options,$this->input->post('account_id')?:$post->account_id?:'','class="form-control m-select2 account_id" id = "account_id" required') ?>
                    <span class="m-form__help">
                        <?php echo translate('Select the account the money was deposited to'); ?>.
                    </span>
                </div>
            </div>

            <div class="form-group m-form__group row">
                <div class="col-lg-6">
                    <label>
                        <?php echo translate('Repayment Date'); ?>
                        <span class="required">*</span>
                    </label>
                    <?php echo form_input('repayment_date',$this->input->post('repayment_date')?timestamp_to_datepicker(strtotime($this->input->post('repayment_date'))):timestamp_to_datepicker($post->repayment_date)?:timestamp_to_datepicker(time()),'class="form-control date-picker repayment_date text-center" required');?> 
                    <span class="m-form__help">
                        <?php echo translate('Select the repayment date'); ?>.
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon" id="upload_excel_file_button">
                            <?php echo translate('UPLOAD LOAN REPAYMENTS'); ?>
                        </button>
                    </span>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script>
    $(document).ready(function(){
        $(document).on('changeDate','input.repayment_date',function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
            }else{
                $(this).parent().removeClass('has-danger');
            }
        });

        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true}).on('changeDate', function(e) {
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });
    });
</script>
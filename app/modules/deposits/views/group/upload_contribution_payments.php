<div>
    <div class="m-portlet__body m-demo__preview">
        <h4>
            <?php echo translate('Upload Contribution Payments Instructions'); ?>
        </h4>
        <?php echo form_open($this->uri->uri_string(),'class="m-form form_submit m-form--state upload_contribution_payments" id="upload_contribution_payments" role="form"'); ?>

            <ol style="line-height: 2.2rem;">
                <li>
                    <?php echo translate('Download your contribution list import template file by clicking the following link'); ?>.
                    <a class="m-badge m-badge--metal m-badge--wide" href='<?php echo site_url("group/deposits/contribution_template_download"); ?>'><i class="la la-download"></i>
                         <?php echo translate('Download'); ?>
                    </a>
                </li>
                <li>
                    <?php echo translate('Open the downloaded template with your favourite Excel sheet editor'); ?>.
                </li>
                <li>
                    <?php echo translate('Fill in each members contribution per contribution'); ?>.
                </li>
                <li>
                    <?php echo translate('Save the Excel Sheet and select it below to upload it'); ?>.
                </li>
            </ol>
            <div class="form-group m-form__group row">
                <div class="col-lg-6">
                    <label>
                        <?php echo translate('Contribution Payment File'); ?>
                        <span class="required">*</span>
                    </label>
                    <div class="custom-file">
                        <?php echo form_upload('contribution_import_file','',' type="file" class="custom-file-input contribution_import_file" id="customFile" name="contributions_template" required');?>
                        <label class="custom-file-label" id="choose_contributi_file" for="customFile">
                            <?php echo translate('Choose excel file'); ?>.
                        </label>
                    </div>
                    <span class="m-form__help">
                        <?php echo translate('Choose your contribution payment list file here'); ?>.
                    </span>
                </div>
                <div class="col-lg-6">
                    <label class="">
                        <?php echo translate('Account'); ?>.
                        <span class="required">*</span>
                    </label>
                    <?php echo form_dropdown('account_id',array(''=>translate('Select an Account'))+$account_options,$this->input->post('account_id')?:$post->account_id?:'','class="form-control m-select2 account_id" id = "account_id" required') ?>
                    <span class="m-form__help">
                        <?php echo translate('Select the account the money was deposited to'); ?>.
                    </span>
                </div>
            </div>
            <div class="form-group m-form__group row p-0 m--padding-top-10">
                <div class="col-lg-12 col-md-12">
                    <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="upload_contribution_payments_button" type="button">
                            <?php echo translate('Upload Contribution Payments');?>
                        </button>
                        &nbsp;&nbsp;
                        <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_member_loan_button">
                            <?php echo translate('Cancel');?>
                        </button>
                    </span>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.m-select2').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            width: "100%",
        });
        SnippetUploadContributionPayments.init();
    });

</script>
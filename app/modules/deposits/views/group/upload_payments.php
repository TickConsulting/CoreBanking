<!-- <div class="form-body">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Upload Contribution Payments Instructions</h3>
        </div>
        <div class="panel-body"> 
            <ol>
                <li><?php echo $transaction_alert->description; ?></li>
                <li>Download your contribution list import template file, by clicking the following link. <a class='btn blue btn-xs' href='<?php echo site_url("group/deposits/payments_template_download"); ?>'><i class='fa fa-cloud-download'></i> Download</a></li>
                <li>Open the downloaded template and open it with your favourite Excel sheet editor, fill in each members contribution per contribution.          </li>
                <li>
                        <?php
                            $default_message='Save the Excel sheet and select it below to upload it.';
                            $this->languages_m->translate('save_upload_excel',$default_message);
                        ?>
                </li>
                <li>
                        <?php
                            $default_message='The payments entered in the excel must add upto '.$this->group_currency.' '.number_to_currency($transaction_alert->amount).'.';
                            $this->languages_m->translate('upload_amount_instructions',$default_message);
                        ?>
                </li>
            </ol>
        </div>
    </div>
    <?php echo form_open_multipart(current_url(),'class="form_submit" role="form"'); ?>
        <div class="form-group">
            <label for="branch_list_file" class=""><?php echo translate("Upload Contribution Payments Instructions");?>/label>
            <div class="input-group">
                <input type="file" name="contributions_template">
                <p class="help-block"> Upload Contribution Payments Instructions </p>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name='import' value='1' class="btn blue submit_form_button">Upload Contribution Payments</button>
            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
            <button type="button" class="btn default">Cancel</button>
        </div>
    <?php echo form_close(); ?>
</div> -->

<div class="m-portlet m-portlet--creative m-portlet--bordered-semi mt-0 pt-0">
    <div class="m-portlet__body m-demo__preview">
        <h4><?php echo translate('Upload Contribution Payments Instructions') ?></h4>
        <?php echo form_open_multipart(current_url(), ' class="form_submit m-form m-form--state" role="form" id="upload_excel_file"'); ?>
        <ol style="">
            <li><?php echo $transaction_alert->description; ?></li>
            <li><?php echo translate("Download your contribution list import template file, by clicking the following link");?>. <a class='m-badge m-badge--metal m-badge--wide' href='<?php echo site_url("group/deposits/payments_template_download"); ?>'><i class='la la-download'></i>  <?php echo translate("Download");?></a></li>

            <li>
                <?php echo translate('Open downloaded file and fill in the details for each member, contribution and loan as described');?>
            </li>
            <li>
                <?php
                    $default_message='Save the Excel Sheet and select it below to upload it.';
                    $this->languages_m->translate('save_upload_excel',$default_message);?>
            </li>
        </ol>
        <div class="row">
            <div class="col-lg-12">
                <label for="exampleInputEmail1"><?php echo translate('Upload payments') ?></label>
            </div>
            <div class="col">
                <div class="form-group m-form__group">
                    <div></div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input member_import_file" id="customFile" name="contributions_template">
                        <label class="custom-file-label" id="choose_member_file" for="customFile"><?php echo translate('Choose excel file') ?></label>
                    </div>
                </div>
            </div>
            <div class="col">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button class="btn btn-primary m-btn m-btn--custom m-btn--icon" value='1'  name="import" id="upload_excel_file_button">
                        <?php echo translate('Submit File');?>
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-metal m-btn m-btn--custom m-btn--icon cancel_form"  type="button" id="cancel_upload_members_excel">
                        <?php echo translate('Cancel');?>
                    </button>
                </span>
            </div>
        </div>
        <?php echo form_close();?>
    </div>
</div>
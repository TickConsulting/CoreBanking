<div class="m-portlet m-portlet--creative m-portlet--bordered-semi mt-0 pt-0">
    <div class="m-portlet__body m-demo__preview">
        <h4><?php echo translate('Import Member Instructions') ?></h4>
        <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="upload_excel_file"'); ?>
        <ol style="line-height: 2.2rem;">
            <li>
                <?php echo translate('Download sample import template');?>
                <a class='m-badge m-badge--metal m-badge--wide' href='<?php echo site_url('uploads/files/csvs/import_member_template.xlsx'); ?>'><i class='la la-download'></i> <?php echo $this->lang->line("download")?:"Download";?></a>
            </li>
            <li>
                <?php echo translate('Open downloaded file and fill in the details for each member as described');?>
            </li>
            <li>
                <?php
                    $default_message='Save the Excel Sheet and select it below to upload it.';
                    $this->languages_m->translate('save_upload_excel',$default_message);?>
            </li>
        </ol>
        <div class="row">
            <div class="col-lg-12">
                <label for="exampleInputEmail1"><?php echo translate('Member list file') ?></label>
            </div>
            <div class="col">
                <div class="form-group m-form__group">
                    <div></div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input member_import_file" id="customFile">
                        <label class="custom-file-label" id="choose_member_file" for="customFile"><?php echo translate('Choose excel file') ?></label>
                    </div>
                </div>
            </div>
            <div class="col">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button class="btn btn-primary m-btn m-btn--custom m-btn--icon" id="upload_excel_file_button">
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

<script type="text/javascript">
    $(document).ready(function(){
        SnippetImportMembers.init(true);
    });
    

</script>
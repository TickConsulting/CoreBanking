<div class="form-body">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Update Member Instructions</h3>
        </div>
        <div class="panel-body"> 
            <ol>
                <li>
                    <?php
                        $default_message='Download our member list import template file, by clicking the following link. ';
                        $this->languages_m->translate('download_member_list_template',$default_message);
                    ?>
                    <a class='btn blue btn-xs' href='<?php echo site_url('group/members/export_update_member_template'); ?>'><i class='fa fa-cloud-download'></i> Download</a></li>
                <li>
                    <?php
                        $default_message='Open the downloaded template and open it with your favourite Excel sheet editor, fill in the following details for each member as described below;';
                        $this->languages_m->translate('fill_template_instruction',$default_message);
                    ?>

                    <ol>
                        <li>
                            <?php
                                $default_message='Enter the or edit the information you wish to be updated per member';
                                $this->languages_m->translate('enter or edit information',$default_message);
                            ?> 
                        </li>
                       
                    </ol>
                </li>
                <li>
                                <?php
                                    $default_message='Save the Excel Sheet and select it below to upload it.';
                                    $this->languages_m->translate('save_upload_excel',$default_message);
                                ?>
                </li>
            </ol>
        </div>
    </div>
    <?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
        <div class="form-group">
            <label for="branch_list_file" class="">
                <?php
                    $default_message='Member list file';
                    $this->languages_m->translate('member_list_file',$default_message);
                ?>
            </label>
            <div class="input-group">
                <input type="file" name="member_list_file">
                <p class="help-block">
                    <?php
                        $default_message='Choose your member list file here';
                        $this->languages_m->translate('choose_your_member_list_file_here',$default_message);
                    ?>
                </p>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name='import' value='1' class="btn blue submit_form_button">
                <?php
                    $default_message='Update Members';
                    $this->languages_m->translate('update_members',$default_message);
                ?>
            </button>
            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                <?php
                    $default_message='Processing';
                    $this->languages_m->translate('processing',$default_message);
                ?>
            </button> 
            <button type="button" class="btn default">
                <?php
                    $default_message='Cancel';
                    $this->languages_m->translate('cancel',$default_message);
                ?>
            </button>
        </div>
    <?php echo form_close(); ?>
</div>
<?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
    <div class="form-body">
        <div class="loan_application_form_holder" id="loan_application_form_holder">
            
        </div>
        <div class="form-actions" id="form_action_holder" style="display: none;">
            <input type="submit" class="btn blue submit_form_button" name="approve" value=" <?php
                    $default_message='Approve  Request';
                    $this->languages_m->translate('Approve  Request',$default_message);
                ?>">                   
            </input>
            <input type="submit" class="btn red submit_form_button" name="decline" value="<?php
                    $default_message='Decline  Request';
                    $this->languages_m->translate('Decline  Request',$default_message);
                ?>">                 
            </input>
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
    </div>    
<?php echo form_close(); ?>

<script>
    $(window).on('load',function(){
        loan_application_details();
    });

    function loan_application_details(){
        var loan_application_id = '<?php echo $this->uri->segment(4) ?>';
        App.blockUI({
            target: '#loan_application_form_holder',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            data:{loan_application_id: loan_application_id},
            url: '<?php echo base_url("ajax/loan_applications/pending_eazzyclub_loans"); ?>',
                success: function(response){
                    $('#loan_application_form_holder').html(response);                         
                    App.unblockUI('#invoice_body');
                }
            }
        );
    }

    function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

</script>
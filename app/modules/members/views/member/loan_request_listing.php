<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered" id="members_loan_requests">
            <div class="members_loan_requests" >
                <div class="data_error alert alert-danger" style="display: none;">
                    
                </div>
                
            </div>            
         </div>

        <div class=" portlet  light bordered" style="display: none;" id="form_holder_portlet" >
           <?php echo form_open(current_url(),'class="form_submit" role="form"');?>
            <div class="form-body">
                <fieldset>
                    <div class="form-group">
                        <label>
                            <?php
                                $default_message='Loan Request Comment';
                                $this->languages_m->translate('loan_request_comment',$default_message);
                            ?>                                    
                        </label>
                        <div class="form-group">
                            <?php echo form_textarea('loan_request_comment',$this->input->post('loan_request_comment')?$this->input->post('loan_request_comment'):$post->loan_request_comment,'class="form-control" id="loan_request_comment" placeholder="Loan Request Comment"');?>                          
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="form-actions">
                <input type="submit" class="btn blue submit_form_button" name="approve" value=" <?php
                        $default_message='Approve Loan Request';
                        $this->languages_m->translate('Approve Loan Request',$default_message);
                    ?>">                   
                </input>
                <input type="submit" class="btn red submit_form_button" name="decline" value="<?php
                        $default_message='Decline Loan Request';
                        $this->languages_m->translate('Decline Loan Request',$default_message);
                    ?>">                 
                </input>
                <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                    <?php
                        $default_message='Processing';
                        $this->languages_m->translate('processing',$default_message);
                    ?>
                </button> 
                <a href="<?php echo $this->agent->referrer()?>">
                    <button type="button" class="btn default">
                        <?php
                            $default_message='Cancel';
                            $this->languages_m->translate('cancel',$default_message);
                        ?>
                    </button>
                </a>
            </div>
            <?php echo form_close();?> 
        </div>    
        
    </div>
</div>

<script>
    $(window).on('load',function(){
        loan_application_details();
    });

    function loan_application_details(){
        var loan_application_id = '<?php echo $this->uri->segment(4) ?>';
        App.blockUI({
            target: '#members_loan_requests',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            data:{loan_application_id: loan_application_id},
            url: '<?php echo base_url("ajax/loans/ajax_loan_details"); ?>',
                success: function(response){
                    if(isJson(response)){    
                       var result = $.parseJSON(response);                 
                        if(result.status == '200'){
                            $('#members_loan_requests').html(result.html); 
                            $('#form_holder_portlet').slideDown();
                            //$('#form_action_holder').slideDown();
                        }else if(result.status == '0'){
                          $('.data_error').html(data.message).slideDown();
                           $('#form_holder_portlet').slideUp();  
                        }
                    }else{
                        alert(response);
                    }                          
                    App.unblockUI('#members_loan_requests');
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

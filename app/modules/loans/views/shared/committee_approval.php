<?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
    <div class="form-body">
        <div class="loan_application_form_holder" id="loan_application_form_holder">
            
        </div>
        <div class="committe_form_holder" id="committe_form_holder" style="display: none;">
            <fieldset>  
                <div class="form-group">
                    <label>Select  Account<span class="required">*</span></label>            
                     <div class="input-group col-xs-12">
                        <?php echo form_dropdown('account_id',array(''=>'--Select account Type--')+$active_accounts,$this->input->post('account_id')?$this->input->post('account_id'):$post->account_id,'class="form-control select2 account_id" id ="account_id"  ') ?>
                    </div>
                </div>             
                <div class="form-group">
                    <label> Please choose your response  to this loan application <span class="required"> *</span> </label>
                    <div class="margin-top-10">
                        <label class="radio-inline radio-padding-0" id="committee_action_button">
                            <div class="radio" id="">
                                <span class=""> 
                                     <?php echo form_radio('action_id',1,$this->input->post('action_id')?$post->action_id:''," id=''"); ?>
                                </span>
                            </div> Approve 
                        </label>
                       
                        <label class="radio-inline radio-padding-0" id="committee_action_button">
                            <div class="radio" id="">
                                <span class="">
                                    <?php echo form_radio('action_id',2,$this->input->post('action_id')?$post->action_id:''," id=''"); ?>
                                </span>
                            </div> Defer
                        </label>
                        <label class="radio-inline radio-padding-0" id="committee_action_button">
                            <div class="radio" id="">
                                <span class="">
                                    <?php echo form_radio('action_id',3,$this->input->post('action_id')?$post->action_id:''," id=''"); ?>                                 
                                </span>
                            </div> Reject
                        </label>
                    </div>
                </div>
                <div class="form-group comment comment_form_holder" style="display: none;">
                    <label>Defer/Reject Comment <span class="required">*</span> </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-comments"></i>
                        </span>
                        <?php
                            $textarea = array(
                                'name'=>'comment',
                                'id'=>'',
                                'value'=> $this->input->post('comment')?:'',
                                'cols'=>30,
                                'rows'=>8,
                                'maxlength'=>300,
                                'class'=>'form-control maxlength',
                                'placeholder'=>'Reason for deferring or rejecting '
                            ); 
                            echo form_textarea($textarea); 
                        ?>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="form-actions" id="form_action_holder" style="display: none;">
            <input type="submit" class="btn blue submit_form_button" name="approve" value=" <?php
                    $default_message='Approve  Request';
                    $this->languages_m->translate('Approve  Request',$default_message);
                ?>">                   
            </input>
           <!--  <input type="submit" class="btn red submit_form_button" name="decline" value="<?php
                    $default_message='Decline  Request';
                    $this->languages_m->translate('Decline  Request',$default_message);
                ?>">                 
            </input> -->
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

    $(document).ready(function(){
        $('#committee_action_button input[type="radio"').on("click", function(){
            var element = $(this).val();
            if(element == 1){
                $('.comment_form_holder').slideUp();
            }else if(element == 2){
                $('.comment_form_holder').slideDown(); 
            }else if(element == 3){
               $('.comment_form_holder').slideDown(); 
            }else{
               $('.comment_form_holder').slideUp();  
            }
        });


    });

    /*$('#enable_loan_guarantors_details input[type="radio"]').on("click",function () {
            var element = $(this).val();
            alert(element);
            if(element == 1){
                $('.loan_guarantor_additionla_details').slideDown();
                $.uniform.update($('input[id="enable_loan_guarantors_details"]').val("1").prop('checked',true));
                $.uniform.update($('input[id="enable_loan_guarantors_exceeds_loan_amount"]').val("2").prop('checked',false));
            }else if(element == 2){
                $('.guarantor_settings').slideDown();
                $.uniform.update($('input[id="enable_loan_guarantors"]').val("1").prop('checked',false));
                $.uniform.update($('input[id="enable_loan_guarantors_exceeds_loan_amount"]').val("2").prop('checked',true));
            }else if(element ==3 ){
                $.uniform.update($('input[id="enable_loan_guarantors"]').val("1").prop('checked',false));
                $.uniform.update($('input[id="enable_loan_guarantors_exceeds_loan_amount"]').val("2").prop('checked',false));
                $('.guarantor_settings').slideUp();
            }
        });*/

    function loan_application_details(){
        var signatory_id = '<?php echo $this->uri->segment(4) ?>';
        App.blockUI({
            target: '#loan_application_form_holder',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            data:{signatory_id: signatory_id},
            url: '<?php echo base_url("ajax/loans/ajax_signatory_details"); ?>',
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);                       
                        if(result.status == '200'){
                            $('#loan_application_form_holder').html(result.html); 
                            $('#committe_form_holder').slideDown();
                            $('#form_action_holder').slideDown();
                        }else if(result.status == '0'){
                           // $('#loan_type_details').html(result.message);  
                        }
                    }else{
                        alert(response);
                    }                          
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
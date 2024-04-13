<div class="row">
    <div class="col-md-12">
        <?php echo form_hidden('id',$id,'  class="form-control cusignatory_id" id="signatory_id" '); ?>
        <div class="portlet light bordered" id="members_loan_requests">
            <div class="members_loan_requests" >
                <div class="data_error alert alert-danger" style="display: none;">
                    
                </div>
                
            </div>            
         </div>

        <div class=" portlet  light bordered" style="display: none;" id="form_holder_portlet" >
           <?php echo form_open(current_url(),'class="form_submit" role="form"');?> 
           <div class="form-group">
                <label>Bank Account<span class="required">*</span></label>
                
                 <div class="input-group col-xs-12">
                    <?php echo form_dropdown('account_id',array(''=>'--Select Bank account--')+$account_numbers,$this->input->post('account_id')?$this->input->post('account_id'):$post->account_id,'class="form-control select2 account_id" id ="account_id"  ') ?>
                </div>
            </div>
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
                            <?php echo form_textarea('comment',$this->input->post('comment')?$this->input->post('comment'):'','class="form-control" id="comment" placeholder="Loan Request Comment"');?>                          
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
    $(document).ready(function(){
        members_loan_requests();
    });

    var id = "<?php echo $this->uri->segment(4, 0); ?>";    
    function members_loan_requests(){
        var signatory_id = '<?php echo $id ?>';
        $('.data_error').html();
        $('.data_error').slideUp();
        App.blockUI({
            target: '#members_loan_requests',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            data:{signatory_loan_request_id:signatory_id},
            url: '<?php echo base_url("member/members/ajax_get_signatory_loan_requests/"); ?>',
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == '200'){                            
                            $('#members_loan_requests').html(data.html);
                            $('#form_holder_portlet').slideDown();
                        }else if(data.status== '0'){
                           $('.data_error').html(data.message).slideDown();
                           $('#form_holder_portlet').slideUp()
                        }
                    }else{
                        $('.data_error').html('Data is not JSON').slideDown();
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
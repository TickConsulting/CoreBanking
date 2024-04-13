<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered" id="signatory_loan_requests">
            <div class="signatory_loan_requests" >
                
            </div>            
         </div>

        <div class=" loan_signatory_form_holder portlet light bordered" style="display: none;">
           <?php echo form_open(current_url(),'class="form_submit" role="form"');?>
           <?php  // print_r($this->data['account_id']);           
                if($this->data['account_id']){   ?>
                    <div class="form-group">
                        <label>Choose Account For Loan Disbursment<span class="required">*</span></label>
                        <div class="input-group col-xs-12 ">
                            <?php echo form_dropdown('account_id',array(''=>'--Select an Account--')+$active_accounts+array('0'=>"Add Account"),$this->input->post('account_id')?:$this->data['account_id']?:'','class="form-control select2 account_id" id = "account_id"  ') ?>
                            <span class="help-block"> Select the account to disburse this loan. </span>
                        </div>
                    </div> 
               <?php  }else{ ?>
                <div class="form-group">
                    <label>Choose Account For Loan Disbursment<span class="required">*</span></label>
                    <div class="input-group col-xs-12 ">
                        <?php echo form_dropdown('account_id',array(''=>'--Select an Account--')+$active_accounts+array('0'=>"Add Account"),$this->input->post('account_id')?:$this->data['account_id']?:'','class="form-control select2 account_id" id = "account_id"  ') ?>
                        <span class="help-block"> Select the account to disburse this loan. </span>
                    </div>
                </div> 
               <?php }
            ?>
            <div class="form-body">
                <fieldset>
                       <div class="form-group">
                        <label>Loan Request Comment</label>
                        <div class="form-group">
                            <?php echo form_textarea('loan_request_comment',$this->input->post('loan_request_comment')?$this->input->post('loan_request_comment'):$post->loan_request_comment,'class="form-control" id="loan_request_comment" placeholder="Loan Request Comment"');?>
                          
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="form-actions">
                <input type="submit" class="btn blue submit_form_button" name="approve" value="approve">                   
                </input>
                <input type="submit" class="btn red submit_form_button" name="decline" value="decline">                 
                </input>
                <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>

            </div>
            <?php echo form_close();?> 
        </div>      
        
    </div>
</div>


<script>

    function disable(){
        $('#decline-btn').text('');
        $('#decline-btn').append("<i class='fa fa-spinner fa-spin fa-2x'></i> processing ...</span>");
        $('#decline-btn').attr('disabled',true);
    }
    function enable(){
        $('#decline-btn').text('');
        $('#decline-btn').append("Decline Loan Request");
        $('#decline-btn').attr('disabled',false);
    }

    function decline($loan_application_id){
        disable();
        var loan_request_comment = $('#loan_request_comment').val();
        $.ajax({
            type:'POST',
            url:'<?php echo base_url("member/members/decline_loan_requests/"); ?>',
            dataType:'html',
            data:{loan_application_id:loan_application_id,loan_request_comment:loan_request_comment },
            success: function(data){
                alert(data);
                enable();
            },
            error: function(data){
                enable();
                alert(data);
            }
        });

    }

    $(document).ready(function(){    

    });

    $(window).on('load',function(){

        signatory_loan_requests();

    });

    var loan_signatory_id = "<?php echo $this->uri->segment(4, 0); ?>";


    function signatory_loan_requests(){
        App.blockUI({
            target: '#signatory_loan_requests',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("member/members/ajax_get_signatory_loan_requests/'+loan_signatory_id+'/"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#signatory_loan_requests').html(response);
                    $('.loan_signatory_form_holder').slideDown();
                    App.unblockUI('#signatory_loan_requests');
                }
            }
        );
    }

</script>
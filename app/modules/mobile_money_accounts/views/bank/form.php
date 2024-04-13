<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state" role="form" id="mobile_money_form"'); ?>
    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-sm-12 m-form__group-sub">
            <label><?php echo translate('Mobile Money Account Name');?><span class="required">*</span></label>
            <?php echo form_input('account_name',$this->input->post('account_name')?$this->input->post('account_name'):$post->account_name,'class="form-control first_name m-input--air" placeholder="Mobile Money Account Name"'); ?>
        </div>
    </div>

    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-sm-12 m-form__group-sub">
            <label><?php echo translate('Mobile Money Provider Name');?><span class="required">*</span></label>
            <?php echo form_dropdown('mobile_money_provider_id',array(''=>'--Select Mobile Money Provider--')+$mobile_money_providers,$this->input->post('mobile_money_provider_id')?:$post->mobile_money_provider_id,'class="form-control m-select2 " id = ""  ') ?>
        </div>
    </div>
    <?php echo form_hidden('id',$id);?>
    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-sm-12 m-form__group-sub">
            <label><?php echo translate('Account Number');?><span class="required">*</span></label>
            <?php echo form_input('account_number',$this->input->post('account_number')?$this->input->post('account_number'):$post->account_number,'class="form-control first_name m-input--air" placeholder="Account Number / Phone Number / Till Number"'); ?>
        </div>
    </div>
    <div class="m-form__actions m-form__actions">                            
        <div class="row">
            <div class="col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_mobile_money_form_button" type="button">
                        Save Changes                                
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_mobile_money_form">
                        Cancel                              
                    </button>
                </span>
            </div>
        </div>
    </div>    

 <?php echo form_close() ?>


<script>
$(document).ready(function(){
    $(".m-select2").select2({
        placeholder:{
            id: '-1',
            text: "--Select option--",
        }, 
    });
    var id = $('input[name=id]').val();
    if(id == ''){
        SnippetCreateMobileMoneyAccount.init();
    }else{
        SnippetEditMobileMoneyAccount.init();
    }

    

    $('input[name=account_name]').keyup(function(){
        txt = $(this).val();
        var re = /\W/gi; 
        var rew = /\s/gi; 

        txt2=txt.replace(rew,'-');
        txt2=txt2.replace(re,'-');
        $('input[name=account_slug]').val(txt2.toLowerCase());
    });
    $('input[name=account_name]').blur(function(){
        txt = $(this).val();
        var re = /\W/gi; 
        var rew = /\s/gi; 

        txt2=txt.replace(rew,'-');
        txt2=txt2.replace(re,'-');
        $('input[name=account_slug]').val(txt2.toLowerCase());
    });
});
</script>

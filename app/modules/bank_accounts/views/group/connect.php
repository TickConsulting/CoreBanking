<link rel="stylesheet" href="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/css/intlTelInput.min.css');?>">
<style type="text/css">
    .intl-tel-input{
        display: block;
    }
    .intl-tel-input.separate-dial-code .selected-dial-code {
        padding-left: 27px !important;
    }
    .intl-tel-input .selected-flag .iti-arrow {
        right: -3px !important;
        margin-top: -1px !important;
    }
</style>
<div class="m-wizard m-wizard--2 m-wizard--success" id="m_wizard">
    <label class="m-option">
        <div class="mt-element-step">
            <div class="row step-line">
            </div>
        </div>
        <span class="m-option__label">
            <span class="m-option__head">                                                
                <span class="m-option__title font-dark bold uppercase">
                    Step 1: Request Verification Code               
                </span>                                             
            </span>
            <span class="m-option__body">
                Select channel to receive One time pin
            </span>
        </span>     
    </label>
    

    <div class="mt-element-step">
        <!--begin: Form Wizard Head -->
        <div class="m-wizard__head m-portlet__padding-x">
            <!--begin: Form Wizard Progress -->
            <div class="m-wizard__progress">
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <!--end: Form Wizard Progress -->
            <!--begin: Form Wizard Nav -->
            <div class="m-wizard__nav">
                <div class="m-wizard__steps ">

                    <div class="m-wizard__step m-wizard__step--current" m-wizard-target="m_wizard_form_step_1">
                        <a href="#" class="m-wizard__step-number">
                            <span> <i class="fa fa-lock"></i></span>
                        </a>
                        <div class="col-xs-6 mt-step-col active m-wizard__step-info "  style="width: 250px;">
                            <div class="mt-step-title uppercase bold font-grey-cascade">REQUEST VERIFICATION CODE</div>
                        </div>
                    </div>
                    <div class="m-wizard__step" m-wizard-target="m_wizard_form_step_2">
                        <a href="#" class="m-wizard__step-number">
                            <span><i class="fa fa-thumbs-up"></i></span>
                        </a>
                        <div class="col-xs-6 mt-step-col active m-wizard__step-info "  style="width: 250px;">
                            <div class="mt-step-title uppercase bold font-grey-cascade">VERIFY CODE</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end: Form Wizard Nav -->
        </div>
        <!--end: Form Wizard Head -->
    </div>
</div>
<div id="link-error-alert" style="display:none">
    <div class="m-alert m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        <strong>Error!</strong>
        <p class="text-panel">There is no information here</p>                      
    </div>
</div>
<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="connect_bank_account_form"'); ?>
    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-sm-12 m-form__group-sub m-input--air">
            <label>
                <?php echo translate('Bank Account');?>
                <span class="required">*</span>
            </label>
            <?php echo form_dropdown('account_number',array(''=>'Select Bank Account')+translate($bank_account_options),$this->input->get_post('account_number')?$this->input->get_post('account_number'):$account->account_number,'class="form-control m-input m-select2 account_number" id="account_number" placeholder="Account Number" disabled="disabled"'); ?>
        </div>
        <?php echo form_hidden('id',$id); ?>
        <?php echo form_hidden('bank_id',$account->bank_id); ?>
        <?php echo form_hidden('linkage_type',0); ?>
        <div class="col-sm-12 mt-5 m-form__group-sub m-input--air" style="display:none" id="linkage-not">
            <label>Send OTP confirmation code to:<span class="required">*</span> </label>
            <div id="account_notificaton_space">

            </div>
        </div>
        <div class="col-sm-12 mt-5 m-form__group-sub m-input--air"  style="display:none" id="otp-not">
            <label>
                <?php echo translate('Signatory Phone Number');?>
                <span class="required">*</span>
            </label>
            <?php echo form_input('phone',$this->input->post('phone')?$this->input->post('phone'):'','class="form-control cust_login_phone phone m-input--air" style="padding-left: 71px !important;" placeholder="Signatory phone number"'); ?>
        </div>
    </div>
    <div class="form-group m-form__group row pt-0 m--padding-10" id="form_actions_holder" style="display:none;">
        <div class="col-lg-12 col-md-12">
            <span class="float-lg-right float-md-left float-sm-left">
                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="connect_bank_account_button1" type="button">
                    <?php echo translate('Request Verification Code');?>
                </button>
                &nbsp;&nbsp;
                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_member_loan_button">
                    <?php echo translate('Cancel');?>
                </button> 
            </span>

        </div>
    </div>
<?php echo form_close();?>

<script src="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/js/intlTelInput.min.js');?>"></script>
<script type="text/javascript">
    $(document).ready(function(){
        // PhoneInputCountry.init();
        SnippetLinkBankAccount.init(true);
    });
</script>
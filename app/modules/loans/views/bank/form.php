<div id="loan_type_form_holder" class="loan_type_form_holder">
    <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_member_loan_form"'); ?>
    <fieldset>
        <legend><?php echo translate('Loan Details') ?></legend>
        <div class="form-group m-form__group row pt-0 m--padding-10" >
            <div class="col-sm-12 m-form__group-sub m-input--air" id="error_div">
                <!-- Errors appear here -->
            </div>
        </div>
        <div class="form-group m-form__group row pt-0 m--padding-10">
            <div class="col-sm-12 m-form__group-sub m-input--air">
                <label>
                    <?php echo translate('Loan Type');?>
                    <span class="required">*</span>
                </label>
                <?php echo form_dropdown('loan_type_id',array(''=>translate('Select Loan Type'))+translate($loan_types),$this->input->post('loan_type_id')?:$post->loan_type_id?:'',' class="form-control m-input m-select2 loan_type" id="loan_type"');?>
            </div>
        </div>
        <div class="form-group m-form__group row pt-0 m--padding-10" id="member_to_amount_holder" style="display: none;">
            <!-- <div class="col-sm-4 m-form__group-sub m-input--air" id="member_to_form_holder">
                <label><?php echo translate('Create member Loan To');?><span class="required">*</span></label>
                <?php echo form_dropdown('loan_to',translate($loan_to_options),$this->input->post('loan_to')?:$post->loan_to?:'',' class="form-control m-input m-select2 loan_to " id = "loan_to" ');?>
            </div> -->
            <div class="col-sm-6 m-form__group-sub m-input--air " id="member_ids">
                <label><?php echo translate('Select User to create Loan');?><span class="required">*</span></label>
                <?php echo form_dropdown('member_id',array(''=>translate('Select User to Create Loan'))+translate($active_group_member_options),$this->input->post('member_id')?:$post->member_id?:'',' class="form-control m-input m-select2 member_id" id="member_id" ');?>
            </div>
            <div class="col-md-6 m-form__group-sub m-input--air">
                <label>
                    <?php echo translate('Loan Amount');?>
                    <span class="required">*</span>
                </label>
                <?php echo form_input('loan_amount',$this->input->post('loan_amount')?:$post->loan_amount?:'','  class="form-control currency m-input--air" placeholder="Loan amount" id="loan_application_amount" '); ?>
            </div>
        </div>
        <div class="form-group m-form__group row pt-0 m--padding-10" id="disbursement_account_holder" style="display: none;">
            <div class="col-sm-12 m-form__group-sub m-input--air">
                <label>
                    <?php echo translate('Disbursement Account');?>
                    <span class="required">*</span>
                </label>
                <?php echo form_dropdown('account_id',array(''=>translate('Select Disbursment Account'))+translate($active_accounts)+translate($bank_account_options),$this->input->post('account_id')?:$post->account_id?:'',' class="form-control m-input m-select2 account_id" id="account_id"');?>
            </div>
        </div>
        <div class="form-group m-form__group row pt-0 m--padding-10" id="disbursement_date_holder" style="margin-left: 0px;margin-right: 0px;display: none">
            <label>
                <?php echo translate('Disbursement Date');?>
                <span class="required">*</span>
            </label>
            <div class="input-group ">
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="la la-calendar-check-o"></i>
                    </span>
                </div>
                <?php echo form_input('disbursement_date',$this->input->post('disbursement_date')?timestamp_to_datepicker(strtotime($this->input->post('disbursement_date'))):timestamp_to_datepicker(time()),'class="form-control m-input m-input--air date-picker" readonly data-date-end-date="0d" data-date-format="dd-mm-yyyy" data-date-viewmode="years" id="disbursement_date"');?>
            </div>
        </div>


        <div class="form-group m-form__group row pt-0 m--padding-10" id="disbursement_option_holder" style="display: none">
            <div class="col-md-6 m-form__group-sub m-input--air">
                <label>
                    <?php echo translate('Select Disbursement Option');?><span class="required">*</span>
                </label>
                <?php echo form_dropdown('disbursement_option_id',translate($disbursement_options),$this->input->post('disbursement_option_id')?:$post->disbursement_option_id?:'',' class="form-control m-input m-select2 disbursement_option_id" id="disbursement_option_id" ');?>    
            </div>
            <div class="col-md-6 m-form__group-sub m-input--air" id="mobile_money_wallet_holder" style="display: none">
                <label>
                    <?php echo translate('Select Mobile Money Wallet Account');?><span class="required">*</span>
                </label>
                <?php echo form_dropdown('mobile_money_wallet_id',array(''=>translate('--Select Recipient--'),0 =>translate('Create New Recipient'))+translate($mobile_money_account_recipients),$this->input->post('mobile_money_wallet_id')?:$post->mobile_money_wallet_id?:'',' class="form-control m-input m-select2 mobile_money_wallet_id" id="mobile_money_wallet_id" ');?>    
            </div>

            <div class="col-md-6 m-form__group-sub m-input--air" id="equity_bank_account_holder" style="display: none">
                <label>
                    <?php echo translate('Select Equity Bank Account');?><span class="required">*</span>
                </label>
                <?php echo form_dropdown('equity_bank_account_id',array(''=>translate('--Select Recipient--'),0 =>translate('Create New Recipient'))+translate($bank_account_recipients),$this->input->post('equity_bank_account_id')?:$post->equity_bank_account_id?:'',' class="form-control m-input m-select2 equity_bank_account_id" id="equity_bank_account_id" ');?>    
            </div>
        </div>

        <div class="form-group m-form__group row pt-0 m--padding-10" id="repayment_period_holder" style="display: none;">
            <div class="col-md-12 m-form__group-sub m-input--air">
                <label id="repayment_period_details_holder">
                    <?php echo translate('Enter Repayment Period in months');?>
                    <span class="required">*</span>
                </label>
                <?php echo form_input('repayment_period',$this->input->post('repayment_period')?$this->input->post('repayment_period'):'','  class="form-control currency m-input--air" placeholder="Loan repayment period" id="repayment_period" '); ?>
            </div>
        </div>
        <div class="form-group m-form__group  pt-0 m--padding-10" id="guarantor_settings_holder" style="display: none;">
            <!-- <div id="append-new-guarantor-setting"></div> -->
        </div>
        <!-- <div class="form-group m-form__group row pt-0 m--padding-10" id="gurantors_period_holder" style="display: none;">
            <div class="col-md-12 m-form__group-sub m-input--air">
                <label>
                    <?php echo translate('Choose gurantors for the loan');?>
                    <span class="required">*</span>
                </label>
                <?php echo form_dropdown('gurantors_id[]',translate($active_group_member_options),$this->input->post('gurantors_id')?:$post->gurantors_id?:'',' class="form-control m-input m-select2 gurantors_id" multiple="multiple" placeholder="--Select Gurantors--" id="gurantors_id"');?>
            </div>
        </div> -->

        <div class="form-group m-form__group row pt-0 m--padding-10" style="display: none;" id="show_loan_breakdown">
            <div class="col-lg-12 col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="loan_btn_amortization" type="button">
                        <?php echo form_hidden('gurantors',"1",'');?>
                        <?php echo translate('Continue');?>
                        <a href="javascript:;" class="see_amortization_and_loan_breakdown d-none" data-toggle="modal" data-target="#see_amortization_and_loan_breakdown"></a>
                    </button>
                </span>
            </div>
        </div>

        <div class="append_guarantor_settings" id="loan_guarantor_row" style="display: none;">
            <div class="row new_guarantor mt-4">
                <div class="col-md-4 m-form__group-sub">
                    <label>
                        <?php echo translate('Guarantor Name');?>
                        <span class="required">*</span>
                    </label>
                    <?php echo form_dropdown('guarantor_ids[]',array(''=>'--Select a Guarantor--')+translate($active_group_member_options),'',' class="form-control m-input m-select2-append guarantor_ids" ');?>
                </div>
                <div class="col-md-4 m-form__group-sub">
                    <label>
                        <?php echo translate('Guaranteed Amount');?>
                        <span class="required">*</span>
                    </label>
                    <?php echo form_input('guaranteed_amounts[]','','  type="currency" class="form-control m-input--air currency guaranteed_amounts" placeholder="Guarantor Amount" '); ?>
                </div>
                <div class="col-md-4 m-form__group-sub">
                    <label>
                        <?php echo translate('Comment');?>
                    </label>
                    <?php echo form_input('guarantor_comments[]','','  class="form-control  m-input--air guarantor_comments" placeholder="Guarantor comment" '); ?>
                </div>
            </div>
        </div>

        <?php echo form_close(); ?>

        <div class="modal fade" id="see_amortization_and_loan_breakdown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="options_holder">
                            <h5>
                                <?php echo translate("Amortization and loan breakdown");?>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </h5>
                            <div class="row page_menus pt-4" style="min-height: 150px;">
                                <div class="col-md-12">
                                    <table class="table table-sm m-table m-table--head-separator-primary table table--hover table-borderless table-condensed loan-types-table">
                                        <thead>
                                            <tr>
                                                <th class="m--align-right" width="20%">
                                                    Loan Creation Details
                                                </th>
                                                <th width="80%">
                                                    &nbsp;
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="1_active_row">
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Loan Type');?>
                                                    </strong>
                                                </td>
                                                <td id="loan_breakdown_loan_type_id"></td>
                                            </tr>
                                            <tr class="1_active_row">
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Member');?>
                                                    </strong>
                                                </td>
                                                <td id="loan_breakdown_member_id"></td>
                                            </tr>
                                            <tr class="1_active_row">
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Loan Amount');?>
                                                    </strong>
                                                </td>
                                                <td id="loan_breakdown_loan_amount"></td>
                                            </tr>
                                            <tr>
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Disbursement Account');?>
                                                    </strong>
                                                </td>
                                                <td id="loan_breakdown_account_id"></td>
                                            </tr>
                                            <tr>
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Disbursement Date');?>
                                                    </strong>
                                                </td>
                                                <td id="loan_breakdown_disbursement_date"></td>
                                            </tr>
                                            <tr id="loan_breakdown_recipient_account_id_view">
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Recipient Account');?> 
                                                    </strong>
                                                </td>
                                                <td id="loan_breakdown_recipient_account_id"></td>
                                            </tr>
                                            <tr>
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Repayment Period');?> 
                                                    </strong>
                                                </td>
                                                <td id="loan_breakdown_repayment_period"></td>
                                            </tr>
                                            <tr id="loan_breakdown_gurantors_id_view">
                                                <td class="m--align-right" nowrap>
                                                    <strong>
                                                        <?php echo translate('Gurantors');?> 
                                                    </strong>
                                                </td>
                                                <td id="loan_breakdown_gurantors_id"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="add_mobile_money_recipient_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
            <div role="document" class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Mobile Money Account Recipient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo form_open(current_url(),'class="m-form m-form--state m-form--label-align-right" id="new_mobile_money_recipient_form"');?>
                        <span class="error"></span>
                        <div class="form-group m-form__group row">
                            <div class="col-lg-6">
                                <label>Recipient Name:</label>
                                <?php echo form_hidden('type',"1",'');?>
                                <?php echo form_hidden('recipient_name'," ",'');?>
                                <?php echo form_input('name',"",'  class="form-control  m-input" placeholder="Recipient Name" id="name"');?>
                                <span class="m-form__help">Enter recipient's name</span>
                            </div>
                            <div class="col-lg-6">
                                <label>Phone Number:</label>
                                <div class="m-input">
                                    <?php echo form_input('phone_number',"",'  class="form-control m-input" placeholder="Phone Number" id="phone_number"');?>
                                </div>
                                <span class="m-form__help">Enter the recipients phone number</span>
                            </div>
                        </div>

                        <div class="mobile_money_account">
                            <div class="form-group m-form__group row">
                                <div class="col-lg-12">
                                    <label class="">Description:</label>
                                    <div class="m-input">
                                        <?php 
                                            $textarea = array(
                                                'name' => 'description',
                                                'id' => '',
                                                'value' => '',
                                                'cols' => 25,
                                                'rows' => 5,
                                                'maxlength '=> '',
                                                'class' => 'form-control  m-input',
                                                'placeholder' => ''
                                            ); 
                                            echo form_textarea($textarea);
                                        ?>
                                    </div>
                                    <span class="m-form__help">Write an optional description</span>
                                </div>
                            </div>   
                        </div>

                        <div class="m-form__actions pl-0 pr-0">
                            <div class="row">
                                <div class="col-lg-6">
                                </div>
                                <div class="col-lg-6 m--align-right">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary submit_new_recipient">Save</button>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </form>
                </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="add_bank_recipient_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
            <div role="document" class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo translate('New Equity Bank Account Recipient');?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo form_open(current_url(),'class="m-form m-form--state m-form--label-align-right" id="new_bank_recipient_form"');?>
                        <span class="error"></span>
                        <div class="form-group m-form__group row account_name_form_field d-none">
                            <div class="col-lg-6">
                                <label>Account Name:</label>
                                <?php echo form_hidden('type',"3",'');?>
                                <?php echo form_input('account_name',"",'  class="form-control  m-input" placeholder="Account Name" id="name"');?>
                            </div>
                            <div class="col-lg-6">
                                <label>Account Currency:</label>
                                <?php echo form_input('account_currency',"",'  class="form-control  m-input" placeholder="Account Currency" id="name"');?>
                            </div>
                        </div>

                        <div class="bank_account">
                            <div class="form-group m-form__group row">
                                <div class="col-lg-6">
                                    <label>Recipient Country:</label>
                                    <div class="m-input">
                                        <?php echo form_dropdown('recipient_bank_id',array(''=>'--Select--',54=>'Kenya',55=>"Tanzania"),55,'  class="form-control m-select2 m-input" placeholder="Account Name" id="account_name"');?>
                                    </div>
                                    <span class="m-form__help">Select recipient country</span>
                                </div>
                                <div class="col-lg-6">
                                    <label class="">Account Number:</label>
                                    <div class="m-input">
                                        <?php echo form_input('account_number',"",'  class="form-control  m-input" placeholder="Account Number" id="account_number"');?>
                                    </div>
                                    <span class="m-form__help">Enter the bank account number</span>
                                </div>
                            </div>
                            <!-- <div class="form-group m-form__group row">
                                <div class="col-lg-12">
                                    <label>Description:</label>
                                    <div class="m-input">
                                        <?php 
                                            $textarea = array(
                                                'name' => 'description',
                                                'id' => '',
                                                'value' => '',
                                                'cols' => 25,
                                                'rows' => 5,
                                                'maxlength '=> '',
                                                'class' => 'form-control  m-input',
                                                'placeholder' => ''
                                            ); 
                                            echo form_textarea($textarea);
                                        ?>
                                    </div>
                                    <span class="m-form__help">Write an optional description</span>
                                </div>
                            </div> -->
                            <button class="account_lookup btn btn-primary" type="button" >Look Up Account Details</button>   
                        </div>

                        <div class="m-form__actions pl-0 pr-0 d-none">
                            <div class="row">
                                <div class="col-lg-6">
                                </div>
                                <div class="col-lg-6 m--align-right">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary submit_new_recipient">Save</button>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </form>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div> -->
                </div>
            </div>
        </div>

        <div class="modal fade" id="amortization-list" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo translate('Loan Details Amortization Schedule');?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <div class="loan_amortization" id="loan_amortization"> </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary m-btn  m-btn m-btn--icon" data-dismiss="modal" id="submit_application_button"><?php echo translate('Save & Submit');?></button>
                </div>
                </div>
            </div>
        </div>

        <a class="inline d-none add_bank_recipient_popup" data-toggle="modal" data-backdrop="false" data-keyboard="false" data-target="#add_bank_recipient_popup" data-title="Add BankRecipient" data-id="add_bank_recipient_popup"><?php echo translate('Add bank Recipient');?></a>

        <a class="inline d-none add_mobile_money_recipient_popup" data-backdrop="false" data-keyboard="false" data-toggle="modal" data-target="#add_mobile_money_recipient_popup" data-title="Add Mobile Money Recipient" data-id="add_mobile_money_recipient_popup"><?php echo translate('Add mobile money Recipient');?></a>

    </fieldset>
</div>


<script type="text/javascript">
    $(window).on('load',function(){


        <?php if($this->input->post('loan_type_id') || $post->loan_type_id){?>
            $('#disbursement_date_holder').slideDown();
        <?php }else{ ?>
            $('#disbursement_date_holder').slideUp(); 
        <?php } ?>

        <?php if($this->input->post('loan_type_id') || $post->loan_type_id){?>
            $('#member_to_amount_holder').slideDown();
        <?php }else{ ?>
            $('#member_to_amount_holder').slideUp(); 
        <?php } ?>

        <?php if($this->input->post('loan_type_id') || $post->loan_type_id){?>
            $('#disbursement_account_holder').slideDown();
        <?php }else{ ?>
            $('#disbursement_account_holder').slideUp(); 
        <?php } ?>

        <?php if($this->input->post('loan_type_id') || $post->loan_type_id){?>
            $('#disbursement_option_holder').slideDown();
        <?php }else{ ?>
            $('#disbursement_option_holder').slideUp(); 
        <?php } ?>

        <?php if($this->input->post('loan_type_id') || $post->loan_type_id){?>
            $('#repayment_period_holder').slideDown();
        <?php }else{ ?>
            $('#repayment_period_holder').slideUp(); 
        <?php } ?>

        <?php if(($this->input->post('loan_type_id')|| $post->loan_type_id) ){?>
            $('.interest_type_input_group').slideDown();
        <?php }else{?>
            $('.interest_type_input_group').slideUp();
        <?php }?>
        
        <?php if(($this->input->post('loan_type_id')|| $post->loan_type_id) ){?>
            $('.gurantors_period_holder').slideDown();
        <?php }else{?>
            $('.gurantors_period_holder').slideUp();
        <?php }?>

        <?php if(($this->input->post('loan_type_id')|| $post->loan_type_id) ){?>
            $('#show_loan_breakdown_table').slideDown();
        <?php }else{?>
            $('#show_loan_breakdown_table').slideUp();
        <?php }?>

        <?php if(($this->input->post('loan_type_id')|| $post->loan_type_id) ){?>
            $('#show_loan_breakdown').slideDown();
        <?php }else{?>
            $('#show_loan_breakdown').slideUp();
        <?php }?>

        <?php if($this->input->post('grace_period') || $post->grace_period){?>
            $('#grace_period').slideDown();
            $('select[name="grace_period"]').trigger('change');
        <?php }else{?>
            $('#grace_period').slideUp();
        <?php }?>


        <?php if($this->input->post('interest_type') || $post->interest_type){?>
                var interest_type = "<?php echo $this->input->post('interest_type')?:$post->interest_type?:'' ?>";
                if(interest_type == 1){
                    $('.for_custom_settings').slideUp();  
                    $('.not_for_custom_settings').slideDown();
                    $('#enable_reducing_balance_installment_recalculation').slideUp();
                }else if(interest_type == 2){
                    $('.not_for_custom_settings').slideDown();
                    $('.for_custom_settings').slideUp();  
                    $('#enable_reducing_balance_installment_recalculation').slideDown();
                }else if(interest_type == 3){
                    $('.not_for_custom_settings').slideUp();
                    $('#enable_reducing_balance_installment_recalculation').slideUp();
                    $('.for_custom_settings').slideUp();  
                    $('.for_custom_settings').slideDown();
                    $('#grace_period').slideDown();
                }else{
                    $('.not_for_custom_settings').slideUp();
                    $('#enable_reducing_balance_installment_recalculation').slideUp();
                    $('.for_custom_settings').slideUp();  
                }
                $('.interest_type_input_group').slideDown();
            
        <?php }else{?>
            $('.interest_type_input_group').slideUp();

            
        <?php }?>
        <?php if($this->input->post('enable_loan_fines') || $post->enable_loan_fines){?>
                var enable_loan_fines = "<?php echo $this->input->post('enable_loan_fines')?:$post->enable_loan_fines?:'' ?>";
                if(enable_loan_fines == 1){
                    $('.enable_loan_fines_radio').prop('checked',true).trigger('change'); 
                }else{
                    $('.disable_loan_fines_radio').prop('checked',true).trigger('change'); 
                }
            
        <?php }else{?>
        <?php }?>
        <?php if($this->input->post('loan_fine_type') || $post->loan_fine_type){?>
                var loan_fine_type = "<?php echo $this->input->post('loan_fine_type')?:$post->loan_fine_type?:'' ?>";
                if(loan_fine_type==1){
                    $('.late_loan_payment_fixed_fine').slideDown();
                    $('.late_loan_payment_percentage_fine').slideUp();
                    $('.late_loan_repayment_one_off_fine').slideUp();
                }else if(loan_fine_type==2){
                    $('.late_loan_payment_percentage_fine').slideDown();
                    $('.late_loan_payment_fixed_fine').slideUp();
                    $('.late_loan_repayment_one_off_fine').slideUp();
                }else if(loan_fine_type==3){
                    $('.late_loan_repayment_one_off_fine').slideDown();
                    $('.late_loan_payment_percentage_fine').slideUp();
                    $('.late_loan_payment_fixed_fine').slideUp();
                }else{
                    $('.late_loan_payment_percentage_fine').slideUp();
                    $('.late_loan_payment_fixed_fine').slideUp();
                    $('.late_loan_repayment_one_off_fine').slideUp();
                }
            
        <?php } ?>
        <?php if($this->input->post('enable_outstanding_loan_balance_fines') || $post->enable_outstanding_loan_balance_fines){?>
                var enable_outstanding_loan_balance_fines = "<?php echo $this->input->post('enable_outstanding_loan_balance_fines')?:$post->enable_outstanding_loan_balance_fines?:'' ?>";
                if(enable_outstanding_loan_balance_fines == 1){
                    $('.enable_outstanding_loan_balances_fines_settings').slideDown();
                    $('.enable_outstanding_loan_balance_fines_radio').prop('checked',true).trigger('change');   
                }else{
                    $('.disable_outstanding_loan_balance_fines_radio').prop('checked',true).trigger('change');
                    $('.enable_outstanding_loan_balances_fines_settings').slideUp();
                }
            
        <?php } ?>
        <?php if($this->input->post('outstanding_loan_balance_fine_type') || $post->outstanding_loan_balance_fine_type){?>
                var outstanding_loan_balance_fine_type = "<?php echo $this->input->post('outstanding_loan_balance_fine_type')?:$post->outstanding_loan_balance_fine_type?:'' ?>";
                    if(outstanding_loan_balance_fine_type==1){
                        $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
                        $('.outstanding_loan_balance_percentage_settings').slideUp();
                        $('.outstanding_loan_balance_fixed_fine').slideDown();
                   }else if(outstanding_loan_balance_fine_type==2){
                        $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
                        $('.outstanding_loan_balance_fixed_fine').slideUp();
                        $('.outstanding_loan_balance_percentage_settings').slideDown();
                   }else if(outstanding_loan_balance_fine_type==3){
                        $('.outstanding_loan_balance_percentage_settings').slideUp();
                        $('.outstanding_loan_balance_fixed_fine').slideUp();
                        $('.outstanding_loan_balance_fine_one_off_settings').slideDown();
                   }else{
                        $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
                        $('.outstanding_loan_balance_percentage_settings').slideUp();
                        $('.outstanding_loan_balance_fixed_fine').slideUp();
                   }
            
        <?php } ?>
        <?php if($this->input->post('enable_loan_guarantors') || $post->enable_loan_guarantors){?>
                var enable_loan_guarantors = "<?php echo $this->input->post('enable_loan_guarantors')?:$post->enable_loan_guarantors?:'' ?>";
                //alert(enable_loan_guarantors);
                    if(enable_loan_guarantors == 1){
                        $('.enable_loan_guarantors_radio').prop('checked',true).trigger('change'); 
                        $('.loan_guarantor_member_details').slideDown();
                    }else if(enable_loan_guarantors == 2){
                        $('.loan_guarantor_member_details').slideUp();
                    }else{
                        $('.disable_loan_guarantors_radio').prop('checked',true).trigger('change'); 
                        $('.loan_guarantor_member_details').slideUp(); 
                    }
            
        <?php } ?>
        <?php if($this->input->post('enable_loan_processing_fee') || $post->enable_loan_processing_fee){?>
                    var loan_processing_fee_type = "<?php echo $this->input->post('loan_processing_fee_type')?:$post->loan_processing_fee_type?:'' ?>";
                    //alert(loan_processing_fee_type)
                    if(loan_processing_fee_type==1){
                        $('.enable_loan_processing_fee_radio').prop('checked',true).trigger('change'); 
                        $('.percentage_loan_processing_fee').slideUp();
                        $('.fixed_amount_processing_fee_settings').slideDown();
                    }else if(loan_processing_fee_type==2){
                        $('.enable_loan_processing_fee_radio').prop('checked',true).trigger('change'); 
                        $('.fixed_amount_processing_fee_settings').slideUp();
                        $('.percentage_loan_processing_fee').slideDown();
                    }else{
                        $('.disable_loan_processing_fee_radio').prop('checked',true).trigger('change');
                        $('.fixed_amount_processing_fee_settings').slideUp();
                        $('.percentage_loan_processing_fee').slideUp()
                    }
            
        <?php } ?> 
        
    });

    $(document).ready(function(){ 
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            width:'100%',
        });
        //$('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
        $('#add-new-line-guarantor').on('click',function(){
            var html = $('#loan_guarantor_row').html();
            html = html.replace_all('checker','');
            $('#append-new-guarantor-setting').append('<div class="loan_guarantor_settings_values_templates">'+html+'</div>');
            $('.tooltips').tooltip();
            FormInputMask.init();
            var number = 0;
            $('.guarantor_id').each(function(){
                $(this).attr('name','guarantor_id['+(number)+']');
                $(this).parent().parent().parent().find('input.guaranteed_amount').attr('name','guaranteed_amount['+(number)+']');
                $(this).parent().parent().parent().find('input.guarantor_comment').attr('name','guarantor_comment['+(number)+']');
                number++;
            });
            $('#append-new-guarantor-setting .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
                allowClear: !0
            });
        });        

        /******Remove line script***/
        $(document).on('click','.remove-guarantor-settings',function(){ 
            $(this).parent().parent().remove();
            update_guarantor_counts();
        });
        
        function update_guarantor_counts(){
            var count = 0;
            $('.guarantor_id').each(function(){
                $(this).attr('name','guarantor_id['+(count)+']');
                count++
            });
        } 

        <?php if($loan_id){?>
            var loan_id = <?php echo $loan_id;?>;
            SnippetEditMemberLoan.init(loan_id );
        <?php }else{?>
            SnippetCreateMemberLoan.init();
        <?php }?>

        $(document).on('change','#loan_to',function(){
            var id = $(this).val();
            if(id == 1){
                $('#member_ids').slideUp();
            }else if(id == 2 ){
                $('#member_ids').slideDown();
            }else{
                $('#member_ids').slideUp();
            }
        });

        $(document).on('change','.loan_type',function(){
            $('#guarantor_settings_holder').html('');
            hide_input_fields();
            var loan_type_id = $('.loan_type').val();
            if($(this).val()==''){
            }else{
                loan_type_details($(this).val());
                get_maximum_savings($(this).val());
            }
            FormInputMask.init();
        });

        $(document).on('change', '.account_id',function(){
            var details = $(this).val();
            if(details.startsWith("petty")){
                // Display disbursment date holder
                $('#disbursement_date_holder').slideDown();
                $('#disbursement_option_holder').slideUp();
            }else {
                // Display recipient account
                $('#disbursement_option_holder').slideDown();
                $('#disbursement_date_holder').slideUp();
            }
        });

        $(document).on('change', '.disbursement_option_id',function(){
            var details = $(this).val();
            if(details == "1"){
                // Display mobile money recepients
                $('#mobile_money_wallet_holder').slideDown();
                $('#equity_bank_account_holder').slideUp();
            }else if(details == "2") {
                $('#mobile_money_wallet_holder').slideUp();
                $('#equity_bank_account_holder').slideDown();
            }
        });

        $(document).on('change', '.mobile_money_wallet_id',function(){
            var details = $(this).val();
            if(details == "0") {
                $('.add_mobile_money_recipient_popup').trigger('click');
            }
        });

        $(document).on('change', '.equity_bank_account_id',function(){
            var details = $(this).val();
            if(details == "0") {
                $('.add_bank_recipient_popup').trigger('click');
            }
        });

        $('#create_member_loan_form').on('submit',function(e){
            e.preventDefault();
            var data = $(this).serializeArray();
            var resultLoans = $.parseJSON(JSON.stringify('<?php echo json_encode($loan_types)?>'));
            // var resultMembers = $.parseJSON(JSON.stringify('<?php echo json_encode($this->active_group_member_options) ?>'));
            // var resultDisbursementAccounts = $.parseJSON(JSON.stringify('<?php echo json_encode(array(''=>translate('Select Disbursment Account'))+translate($active_accounts)+translate($bank_account_options)) ?>'));
            // var resultMobileMoneyAccounts = $.parseJSON(JSON.stringify('<?php echo json_encode($mobile_money_account_recipients)?>'));
            // var resultEquityBankAccounts = $.parseJSON(JSON.stringify(<?php echo json_encode($bank_account_recipients,TRUE)?>));
            
            // console.log(data, resultLoans, resultMembers, resultDisbursementAccounts, resultMobileMoneyAccounts, resultEquityBankAccounts);
            
            $('#loan_breakdown_loan_type_id').text(resultLoans[data[0].value]);
            // $('#loan_breakdown_member_id').text(resultMembers[data[1].value]);
            $('#loan_breakdown_loan_amount').text(data[2].value);
            // $('#loan_breakdown_account_id').text(resultDisbursementAccounts[data[3].value]);
            $('#loan_breakdown_disbursement_date').text(data[4].value);
            $('#loan_breakdown_repayment_period').text(data[8].value + " months");

            // Check if petty accounts was picked
            if(data[3].value.startsWith("petty")) {
                // Hide recipient account
                $('#loan_breakdown_recipient_account_id_view').css("display","none");
            }else {
                // Check if mobile money account was picked
                if( data[6].value && data[6].value !== "0" ) {
                    if( data[6].value.startsWith('member') ) {
                        // $('#loan_breakdown_recipient_account_id').text( resultMobileMoneyAccounts.Members[data[6].value] );
                    }
                    
                    if( data[6].value.startsWith('mobile') ) {
                        // $('#loan_breakdown_recipient_account_id').text( resultMobileMoneyAccounts["Mobile Money Recipients"][data[6].value] );
                    }
                }
                // Check if equity bank account was picked
                if( data[7].value && data[7].value !== "0" ) {
                    $('#loan_breakdown_recipient_account_id').text( resultEquityBankAccounts["Bank Recipients"][data[7].value] );
                }    
            }

            // // Check if gurantors exist
            // var gurantors = "";
            // if( data.length > 9 ) {
            //     for( var i = 9; i < data.length; ++i ) {
            //         gurantors += resultMembers[data[i].value] + ", ";
            //     }
            //     $('#loan_breakdown_gurantors_id').text(gurantors);
            // }else {
            //     // Hide gurantors field
            //     $('#loan_breakdown_gurantors_id_view').css("display","none");
            // }
            

            // Show the modal
            $('.see_amortization_and_loan_breakdown').click();
        });

        $('#loan_btn_amortization').on('click',function(e){
            $(this).addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0)
            $('#submit_application_button').removeAttr("disabled");
            mApp.block('#create_member_loan_form',{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Generating Amortization Schedule....'
            });
            var loan_type_id = $('#loan_type').val() ;
            var member_id = $('#member_id').val() ;
            var loan_amount  = $('#loan_application_amount').val();
            var account_id  = $('#account_id').val();
            var disbursement_option_id  = $('#disbursement_option_id').val();
            var repayment_period = $('#repayment_period').val();
            var guarantor_ids = $('.guarantor_ids').map((i, e) => e.value).get().slice(0,-1);
            var guaranteed_amounts = $('.guaranteed_amounts').map((i, e) => e.value).get().slice(0,-1);
            var guarantor_comments = $('.guarantor_comments').map((i, e) => e.value).get().slice(0,-1);
            var form_error = false;

            if(loan_type_id =="" ){  
                $('#loan_type').parent().addClass('has-danger').append('<div class="form-control-feedback">Please select the loan type</div>');
                mApp.unblock('#create_member_loan_form');
                $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                form_error = true;     
            }
            if(member_id== ""){
                $('#member_id').parent().addClass('has-danger').append('<div class="form-control-feedback">Please select a member</div>');
                mApp.unblock('#create_member_loan_form');
                $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                form_error = true;
            }
            if(loan_amount== ""){
                $('#loan_application_amount').parent().addClass('has-danger').append('<div class="form-control-feedback">Please enter a valid loan amount</div>');
                mApp.unblock('#create_member_loan_form');
                $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                form_error = true;
            }
            if(account_id== ""){
                $('#account_id').parent().addClass('has-danger').append('<div class="form-control-feedback">Please choose a disbursement account</div>');
                mApp.unblock('#create_member_loan_form');
                $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false);
                form_error = true;
            }else if( account_id.startsWith("petty") == false ) {
                // Check if account_id is not petty, hence get disbursement_option_id
                if( disbursement_option_id == " " ) {
                    $('#disbursement_option_id').parent().addClass('has-danger').append('<div class="form-control-feedback">Please choose a disbursement account</div>');
                    mApp.unblock('#create_member_loan_form');
                    $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false);
                    form_error = true;
                }else if(disbursement_option_id == "1") {
                    // Check if mobile money account is selected
                    var mobile_money_wallet_id = $("#mobile_money_wallet_id").val();
                    
                    if(mobile_money_wallet_id == "" || mobile_money_wallet_id == "0") {
                        $('#mobile_money_wallet_id').parent().addClass('has-danger').append('<div class="form-control-feedback">Please choose a mobile money account</div>');
                        mApp.unblock('#create_member_loan_form');
                        $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false);
                        form_error = true;
                    }
                }else if(disbursement_option_id == "2") {
                    // Check if equity account is selected
                    var equity_bank_account_id = $("#equity_bank_account_id").val();
                    
                    if(equity_bank_account_id == "" || equity_bank_account_id == "0") {
                        $('#equity_bank_account_id').parent().addClass('has-danger').append('<div class="form-control-feedback">Please choose an Equity account</div>');
                        $('#equity_bank_account_id').parent().addClass('has-danger').append('<div class="form-control-feedback">Please choose an Equity account</div>');
                        mApp.unblock('#create_member_loan_form');
                        $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false);
                        form_error = true;
                    }
                }
            }
            if( guarantor_ids.length > 0 ) {
                // Means we have gurantors
                for( var i = 0; i < guarantor_ids.length; ++i ) {
                    if( guarantor_ids[i] == "" ) {
                        $(".guarantor_ids").parent().addClass('has-danger').append('<div class="form-control-feedback">Please choose a guarantor</div>');
                        form_error = true;
                        mApp.unblock('#create_member_loan_form');
                        $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false);
                    }

                    if( guaranteed_amounts[i] == "" ) {
                        $(".guaranteed_amounts").parent().addClass('has-danger').append('<div class="form-control-feedback">Please enter a guarantor amount</div>');
                        form_error = true;
                        mApp.unblock('#create_member_loan_form');
                        $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false);
                    }
                    
                    if( guarantor_comments[i] == "" ) {
                        $(".guarantor_comments").parent().addClass('has-danger').append('<div class="form-control-feedback">Please enter a guarantor comment</div>');
                        form_error = true;
                        mApp.unblock('#create_member_loan_form');
                        $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false);
                    }
                }
            }

            // console.log("Form Error "+form_error);
            
            if( !form_error ) {
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url("ajax/loans/loan_calculator"); ?>',
                    data:{loan_type_id:loan_type_id,loan_amount:loan_amount,repayment_period:repayment_period,guarantor_ids:guarantor_ids},
                    dataType : "html",
                        success: function(response) {
                            $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                            $('#amortization-list').modal('show'); // show bootstrap modal when complete loaded
                            $('#loan_amortization').html(response);                    
                            mApp.unblock('#create_member_loan_form');
                        },
                        error:function(response){
                            $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1) 
                            $('#loan_amortization').html(response);                    
                            mApp.unblock('#create_member_loan_form');                           
                        }
                    }
                );  
            }
        });

        $('#submit_application_button').on('click',function(e){
            $(this).addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0)
            mApp.block('#amortization-list',{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Creating Loan Application....'
            });
            var loan_type_id = $('#loan_type').val();
            var loan_rules_check_box = 1;
            var loan_application_amount  = $('#loan_application_amount').val();
            var repayment_period = $('#repayment_period').val();
            var guarantor_ids = $('.guarantor_ids').map((i, e) => e.value).get().slice(0,-1);
            var guaranteed_amounts = $('.guaranteed_amounts').map((i, e) => e.value).get().slice(0,-1);
            var guarantor_comments = $('.guarantor_comments').map((i, e) => e.value).get().slice(0,-1);

            if(loan_type_id =="" ){  
                $('#loan_type').parent().addClass('has-danger').append('<div class="form-control-feedback">Please select the loan type</div>');
                mApp.unblock('#amortization-list');
                $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)           
            }else if(loan_application_amount== ""){
                $('#loan_application_amount').parent().addClass('has-danger').append('<div class="form-control-feedback">Please enter a valid loan amount</div>');
                mApp.unblock('#amortization-list');
                $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
            }else{
                var formData = $('#create_member_loan_form').serializeArray();
                //$('#submit_application_button').removeAttr('disabled');
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url("ajax/loans/create_withdrawal_request"); ?>',
                    data: formData,
                    dataType : "html",
                        success: function(response) {
                            if(response){
                                if(isJson(response)){
                                    var res = JSON.parse(response);
                                    if(res.status == 1) {
                                        Toastr.show("Success",res.message,'success');
                                        window.location = res.refer;
                                    }else if(res.status == 0) {
                                        var error_msg = '';
                                        $('#error_div').addClass('has-danger').append('<div class="form-control-feedback"><Label>Error: '+ res.message +'</Label></div>');
                                        $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                                        $('#amortization-list').modal('hide'); // show bootstrap modal when complete loaded
                                        // $('#loan_amortization').html(res.message);                    
                                        mApp.unblock('#amortization-list');
                                    }else if(res.status == '202'){
                                        Toastr.show("Session Expired",res.message,'error');
                                        window.location.href = res.refer;
                                    }else {
                                        $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                                        $('#amortization-list').modal('show'); // show bootstrap modal when complete loaded
                                        // $('#loan_amortization').html(res.message);                    
                                        mApp.unblock('#amortization-list');
                                    }
                                }else{
                                    $('#error_div').addClass('has-danger').append('<div class="form-control-feedback"><Label>Error: We could not complete the process at the moment, try again later</Label></div>');
                                    $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                                    $('#amortization-list').modal('hide');                 
                                    mApp.unblock('#amortization-list'); 
                                }
                            }else{
                                $('#error_div').addClass('has-danger').append('<div class="form-control-feedback"><Label>Error: We could not complete the process at the moment, try again later</Label></div>');
                                $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                                $('#amortization-list').modal('hide');                 
                                mApp.unblock('#amortization-list'); 
                            }
                        },
                        error:function(){
                            $('#error_div').addClass('has-danger').append('<div class="form-control-feedback"><Label>Error: We could not complete the process at the moment, try again later</Label></div>');
                            $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                            $('#amortization-list').modal('hide');                 
                            mApp.unblock('#amortization-list');
                        },always: function(){
                            $('#error_div').addClass('has-danger').append('<div class="form-control-feedback"><Label>Error: We could not complete the process at the moment, try again later</Label></div>');
                            $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                            $('#amortization-list').modal('hide');                 
                            mApp.unblock('#amortization-list'); 
                        }
                    }
                );  
            }

        });

        $('#new_mobile_money_recipient_form').on('submit',function(e){
            e.preventDefault();
            mApp.block('#new_mobile_money_recipient_form', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            $('#new_mobile_money_recipient_form #submit_new_recipient').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            var form = $("#new_mobile_money_recipient_form");
            $.ajax({
                url:'<?php echo site_url('ajax/recipients/create')?>',
                type:'POST',
                data:form.serialize(),
                success:function(response){
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        // console.log(data);

                        if(data.status == 1){
                            var recipient = data.recipient;
                            var optiongroups = [];
                            var optiongroup = "Mobile Money Recipients";
                            $('.mobile_money_disbursement_form #mobile_money_wallet_id optgroup').each(function(){
                                var val = $(this).attr("label");
                                optiongroups.push(val);
                                if(val == optiongroup){
                                    $(this).append(
                                        '<option value="' + recipient.id + '">' + recipient.name+ '('+recipient.phone_number+') </option>'
                                    ).parent().trigger('change');
                                    $('.mobile_money_disbursement_form #mobile_money_wallet_id').val(recipient.id).trigger('change');
                                }
                            });

                            if($.inArray(optiongroup,optiongroups) == -1){
                                $('.mobile_money_disbursement_form #mobile_money_wallet_id').append(
                                    '<optgroup label="'+optiongroup+'"><option value="' + recipient.id + '">' + recipient.name+ '('+recipient.phone_number+') </option></optgroup>').trigger('change');
                                $('.mobile_money_disbursement_form #mobile_money_wallet_id').val(recipient.id).trigger('change');
                            }
                            $('#add_mobile_money_recipient_popup .close').trigger('click');
                        }else{
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#add_mobile_money_recipient_popup input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#add_mobile_money_recipient_popup select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                                $('.m-form__help').hide();
                            }
                            $('#add_mobile_money_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>');
                        }
                        mApp.unblock('#new_mobile_money_recipient_form');
                    }else{
                        $('#add_mobile_money_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error was encountered processing your request. Please refresh and try again</div>');
                        mApp.unblock('#new_mobile_money_recipient_form');
                        $('#add_mobile_money_recipient_popup #submit_new_recipient').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                    }
                },
                always:function(){
                    $('#add_mobile_money_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error was encountered processing your request. Please try again</div>');
                    mApp.unblock('#new_mobile_money_recipient_form');
                    $('#add_mobile_money_recipient_popup #submit_new_recipient').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                },
                error:function(){
                    $('#add_mobile_money_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error was encountered processing your request. Please try again</div>');
                    mApp.unblock('#new_mobile_money_recipient_form');
                    $('#add_mobile_money_recipient_popup #submit_new_recipient').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                }
            });
        });

        $('#new_bank_recipient_form').on('submit',function(e){
            e.preventDefault();
            mApp.block('#new_bank_recipient_form', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            $('#new_bank_recipient_form #submit_new_recipient').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            var form = $("#new_bank_recipient_form");
            $.ajax({
                url:'<?php echo site_url('ajax/recipients/create')?>',
                type:'POST',
                data:form.serialize(),
                success:function(response){
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            var recipient = data.recipient;
                            var optiongroups = [];
                            var optiongroup = "Bank Recipients";
                            $('.bank_disbursement_form #equity_bank_account_id optgroup').each(function(){
                                var val = $(this).attr("label");
                                optiongroups.push(val);
                                if(val == optiongroup){
                                    $(this).append(
                                        '<option value="' + recipient.id + '">' + recipient.name + ' ('+recipient.account_name + ' - '+recipient.account_number + ' ) </option>').parent().trigger('change');
                                    $('.bank_disbursement_form #equity_bank_account_id').val(recipient.id).trigger('change');
                                }
                            });

                            if($.inArray(optiongroup,optiongroups) == -1){
                                $('.bank_disbursement_form #equity_bank_account_id').append(
                                    '<optgroup label="'+optiongroup+'"><option value="' + recipient.id + '">' + recipient.name + ' ('+recipient.account_name + ' - '+recipient.account_number + ' ) </option></optgroup>').trigger('change');
                                $('.bank_disbursement_form #equity_bank_account_id').val(recipient.id).trigger('change');
                            }
                            $('#add_bank_recipient_popup .close').trigger('click');
                        }else{
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#add_bank_recipient_popup input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#add_bank_recipient_popup select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                                $('.m-form__help').hide();
                            }
                            $('#add_bank_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>');
                        }
                        mApp.unblock('#new_bank_recipient_form');
                    }else{
                        $('#add_bank_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error was encountered processing your request. Please refresh and try again</div>');
                        mApp.unblock('#new_bank_recipient_form');
                        $('#add_bank_recipient_popup #submit_new_recipient').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                    }
                },
            });
        });

        $(document).on('click','.account_lookup',function(){
            var account_number = $('#new_bank_recipient_form input[name="account_number"]').val();
            var recipient_bank_id = $('#new_bank_recipient_form select[name="recipient_bank_id"]').val();
            if(recipient_bank_id){
                $('#new_bank_recipient_form .error').html('');
                $('#new_bank_recipient_form input[name="recipient_bank_id"]').parent().parent().removeClass('has-danger');
                if(account_number){
                    $('#new_bank_recipient_form .error').html('');
                    $('#new_bank_recipient_form input[name="account_number"]').parent().parent().removeClass('has-danger');
                    var content = $('#new_bank_recipient_form');
                    mApp.block(content, {
                        overlayColor: 'grey',
                        animate: true,
                        type: 'loader',
                        state: 'primary',
                        message: 'Looking up account details...'
                    });
                    $.ajax({
                        type: "POST",
                        url: '<?php echo base_url("ajax/recipients/lookup_account_details"); ?>',
                        data: {"account_number":account_number,"recipient_bank_id":recipient_bank_id},
                        success:function(response){
                            if(isJson(response)){
                                var res = $.parseJSON(response);
                                if(res){
                                    if(res.status == 1){
                                        var account_name = res.account_name;
                                        var account_currency = res.account_currency;
                                        $('#new_bank_recipient_form input[name="account_name"]').val(account_name);
                                        $('#new_bank_recipient_form input[name="account_currency"]').val(account_currency);
                                        $('.account_lookup').hide();
                                        $('.account_name_form_field,#new_bank_recipient_form .m-form__actions').removeClass('d-none');
                                        $('#new_bank_recipient_form input[name="account_name"], #new_bank_recipient_form input[name="account_currency"], #new_bank_recipient_form input[name="account_number"]').attr('readonly','readonly');
                                    }else{
                                        $('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+res.message+'</div>');
                                    }
                                }else{
                                    $('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>Error encounted on account lookup. Try again later</div>');
                                }
                            }else{
                                $('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>Error encounted on account lookup. Try again later</div>');
                            }
                            mApp.unblock(content);
                        },
                        error:function(){
                            $('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>We are experiencing an error at the moment. Kindly refresh and try again</div>');
                            mApp.unblock(content);
                        },
                        always: function(){
                            $('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>We are experiencing an error at the moment. Kindly refresh and try again</div>');
                            mApp.unblock(content);
                        },
                    });
                }else{
                    $('#new_bank_recipient_form input[name="account_number"]').parent().parent().addClass('has-danger');
                    $('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>Account number field is required</div>');
                }
            }else{
                $('#new_bank_recipient_form select[name="recipient_bank_id"]').parent().parent().addClass('has-danger');
                $('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>Recipient Country field is required</div>');
                
            }
        });

        $(document).on('keyup keydown','input[name="loan_amount"]',function(){
            add_guarantors();
        });

        $(document).on('change','.member_id',function(){
            var loan_type_id = $('.loan_type').val();
            var member_id = $('#member').val();
            if($(this).val()==''){
            }else{
                if(loan_type_id){
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('ajax/loan_types/get_maximum_savings'); ?>",
                        data: {'loan_type_id':loan_type_id,'member_id':member_id},
                        success: function(response){
                            if(isJson(response)){
                                var result = $.parseJSON(response);
                                if(result){
                                    $('input[name="maximum_loan_amount_from_savings"]').val(result);   
                                    add_guarantors();                                 
                                }
                            }
                        }
                    });
                }
            }
        });

        $(document).on('change keydown keyup','input[name="loan_amount"], select[name="account_id"]',function(){
            if($(this).val()){
                $('.interest_type_input_group').slideDown();
                $('select[name="grace_period"]').trigger('change');
            }
        });


        $(document).on('change','select[name="interest_type"]',function(){
            var interest_type = $(this).val();
            if(interest_type == 1){
                $('.for_custom_settings').slideUp();  
                $('.not_for_custom_settings').slideDown();
                $('#enable_reducing_balance_installment_recalculation').slideUp();
            }else if(interest_type == 2){
                $('.not_for_custom_settings').slideDown();
                $('.for_custom_settings').slideUp();  
                $('#enable_reducing_balance_installment_recalculation').slideDown();
            }else if(interest_type == 3){
                $('.not_for_custom_settings').slideUp();
                $('#enable_reducing_balance_installment_recalculation').slideUp();
                $('.for_custom_settings').slideUp();  
                $('.for_custom_settings').slideDown();
                $('#grace_period').slideDown();
            }else{
                $('.not_for_custom_settings').slideUp();
                $('#enable_reducing_balance_installment_recalculation').slideUp();
                $('.for_custom_settings').slideUp();  
            }
        });

        if($('select[name="interest_type"]').val()){
            $(this).trigger('change');
        }

        $(document).on('keydown keyup','input[name="interest_rate"]',function(){
            $('#grace_period').slideDown();
        });

        if($('select[name="interest_rate"]').val()){
            $(this).trigger('change');
        }

        $(document).on('change','select[name="grace_period"]',function(){
            if($(this).val()){
                if($(this).is(':visible')){
                    $('.addition_loan_types_form_details').slideDown();
                }
                $('.loan_repayment_period_input_group').slideDown();
            }else{
                $('.loan_repayment_period_input_group').slideUp();
                $('.addition_loan_types_form_details').slideUp();
            }
        });

        if($('select[name="grace_period"]').val()){
            $(this).trigger('change');
        }

        $(document).on('change','.loan_repayment_period_type',function(){
            var loan_type_options_id =  $(this).val();
            if(loan_type_options_id == 1){
                $('.fixed_repayment_period').slideDown();
                $('.varying_repayment_period').slideUp();
            }else if(loan_type_options_id == 2){
                $('.fixed_repayment_period').slideUp();
                $('.varying_repayment_period').slideDown();
            }
        });

        $(document).on('change','input[name="enable_loan_fines"]',function(){
            if($(this).val()==1){
                $('.enable_loan_fines_settings').slideDown();
            }else{
                $('.enable_loan_fines_settings').slideUp();
            }
        });

        $(document).on('change','select[name="loan_fine_type"]',function(){
            var loan_fine_type = $(this).val();
            if(loan_fine_type==1){
                $('.late_loan_payment_fixed_fine').slideDown();
                $('.late_loan_payment_percentage_fine').slideUp();
                $('.late_loan_repayment_one_off_fine').slideUp();
            }else if(loan_fine_type==2){
                $('.late_loan_payment_percentage_fine').slideDown();
                $('.late_loan_payment_fixed_fine').slideUp();
                $('.late_loan_repayment_one_off_fine').slideUp();
            }else if(loan_fine_type==3){
                $('.late_loan_repayment_one_off_fine').slideDown();
                $('.late_loan_payment_percentage_fine').slideUp();
                $('.late_loan_payment_fixed_fine').slideUp();
            }else{
                $('.late_loan_payment_percentage_fine').slideUp();
                $('.late_loan_payment_fixed_fine').slideUp();
                $('.late_loan_repayment_one_off_fine').slideUp();
            }
        });

        $(document).on('change','select[name="one_off_fine_type"]',function(){
            var one_off_fine_type = $(this).val();
            if(one_off_fine_type==1){
                $('.one_off_fine_type_settings').show();
                $('.one_off_percentage_setting').hide();
                $('.one_off_fixed_amount_setting').show();
            }else if(one_off_fine_type==2){
                $('.one_off_fine_type_settings').show();
                $('.one_off_fixed_amount_setting').hide();
                $('.one_off_percentage_setting').show();
            }else if(one_off_fine_type=='')
            {
                $('.one_off_percentage_setting').hide();
                $('.one_off_fixed_amount_setting').hide();
                $('.one_off_fine_type_settings').hide();
            }
        });

        $(document).on('change','input[name="enable_outstanding_loan_balance_fines"]',function(){
            var enable_outstanding_loan_balance_fines = $(this).val();
            if(enable_outstanding_loan_balance_fines == 1){
                $('.enable_outstanding_loan_balances_fines_settings').slideDown();
            }else{
                $('.enable_outstanding_loan_balances_fines_settings').slideUp();
            }
        });

        $(document).on('change','select[name=outstanding_loan_balance_fine_type]',function(){
           var outstanding_loan_balance_fine_type =$(this).val();
           if(outstanding_loan_balance_fine_type==1){
                $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
                $('.outstanding_loan_balance_percentage_settings').slideUp();
                $('.outstanding_loan_balance_fixed_fine').slideDown();
           }else if(outstanding_loan_balance_fine_type==2){
                $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
                $('.outstanding_loan_balance_fixed_fine').slideUp();
                $('.outstanding_loan_balance_percentage_settings').slideDown();
           }else if(outstanding_loan_balance_fine_type==3){
                $('.outstanding_loan_balance_percentage_settings').slideUp();
                $('.outstanding_loan_balance_fixed_fine').slideUp();
                $('.outstanding_loan_balance_fine_one_off_settings').slideDown();
           }else{
                $('.outstanding_loan_balance_fine_one_off_settings').slideUp();
                $('.outstanding_loan_balance_percentage_settings').slideUp();
                $('.outstanding_loan_balance_fixed_fine').slideUp();
           }
        });

        $(document).on('change','input[name="loan_guarantors_type"]',function(){
            var loan_guarantors_type = $(this).val();
            if(loan_guarantors_type == '1'){
                $('.guarantor_settings_holder_every_savings').slideUp();
                $('.guarantor_settings_holder_every_time').slideDown();
            }else if(loan_guarantors_type == '2'){
                $('.guarantor_settings_holder_every_time').slideUp();
                $('.guarantor_settings_holder_every_savings').slideDown();
            }else{
                $('.guarantor_settings_holder_every_savings').slideUp();
                $('.guarantor_settings_holder_every_time').slideUp();
            }
        });

        $(document).on('change','input[name="enable_loan_guarantors"]',function(){
            var element = $(this).val();
            if(element == 1){
                $('.loan_guarantor_member_details').slideDown();
            }else if(element == 2){
                $('.loan_guarantor_member_details').slideUp();
            }else{
                $('.loan_guarantor_member_details').slideUp(); 
            }

        });

        $(document).on('change','select[name="loan_processing_fee_type"]',function(){
            var loan_processing_fee_type = $(this).val();
            if(loan_processing_fee_type == 1){
                $('.percentage_loan_processing_fee').slideUp();
                $('.fixed_amount_processing_fee_settings').slideDown();
            }else if(loan_processing_fee_type==2){
                $('.fixed_amount_processing_fee_settings').slideUp();
                $('.percentage_loan_processing_fee').slideDown();
            }else{
                $('.fixed_amount_processing_fee_settings').slideUp();
                $('.percentage_loan_processing_fee').slideUp();
            }
        });

        $(document).on('change','input[name="enable_loan_processing_fee"]',function(){
            var enable_loan_processing_fee = $(this).val();
            if(enable_loan_processing_fee==1){
                $('.loan_processing_fee_settings').slideDown();
            }else{
                $('.loan_processing_fee_settings').slideUp();
            }
        });

        $(document).on('change','input[name="disbursement_date"]',function(){
            var grace_period = $('select[name="grace_period"]').val();
            var disbursement_date = $(this).val();
            date = new Date(disbursement_date.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            if(grace_period == 'date'){
                $('input[name="grace_period_date"]').removeAttr('disabled');
            }else{
                grace_period = parseInt(grace_period);
                grace_period_date = new Date(date.setMonth(date.getMonth()+grace_period));
                $('input[name="grace_period_date"]').datepicker('setDate',grace_period_date);
                $('input[name="grace_period_date"]').attr('disabled',true);
            }
        });

        $(document).on('change','select[name="grace_period"]',function(){
            var grace_period = $(this).val();
            var disbursement_date = $('input[name="disbursement_date"]').val();
            date = new Date(disbursement_date.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
            if(grace_period == 'date'){
                $('input[name="grace_period_date"]').removeAttr('disabled');
            }else{
                grace_period = parseInt(grace_period);
                grace_period_date = new Date(date.setMonth(date.getMonth()+grace_period));
                $('input[name="grace_period_date"]').datepicker('setDate',grace_period_date);
                $('input[name="grace_period_date"]').attr('disabled',true);
            }
        });
    });

    function hide_input_fields(){
        $('#loan_to').val($('#loan_to').prop('defaultSelected'));
        // $('#member_ids').slideUp();
        $('#member_to_amount_holder').slideUp();
        $('#disbursement_account_holder').slideUp();
        $('#disbursement_option_holder').slideUp();
        $('#loan_repayment_form_holder').slideUp();
        $('#disbursement_date_holder').slideUp();
        $('#repayment_period_holder').slideUp();
        $('#show_loan_breakdown_table').slideUp();
        $('#show_loan_breakdown').slideUp();
        $('#grace_period').slideUp();
        $('input[name="repayment_period"]').trigger('change')
        $('.interest_type_input_group').slideUp();
        $('select[name=interest_type]').val('').trigger('change');
        $('select[name="grace_period"]').val('').trigger('change');
        $('.addition_loan_types_form_details').slideUp();
        $('.disable_loan_fines_radio').prop('checked',true).trigger('change');
        $('select[name="loan_fine_type"]').val('').trigger('change');
        $('select[name="one_off_fine_type"]').val('').trigger('change');
        $('input[name="one_off_fixed_amount"]').val('');
        $('input[name="fixed_fine_amount"]').val(''); 
        $('#fixed_amount_fine_frequency').val('').trigger('change');
        $('select[name="fixed_amount_fine_frequency_on"]').val('').trigger('change');
        $('input[name="percentage_fine_rate"]').val('');
        $('select[name="percentage_fine_frequency"]').val('').trigger('change');
        $('select[name="percentage_fine_on"]').val('').trigger('change');
        $('.disable_outstanding_loan_balance_fines_radio').prop('checked',true).trigger('change');
        $('.disable_loan_processing_fee_radio').prop('checked',true).trigger('change');                
        $('#outstanding_loan_balance_fine_type').val('').trigger('change');
        $('input[name="outstanding_loan_balance_fine_fixed_amount"]').val();
        $('select[name="outstanding_loan_balance_fixed_fine_frequency"]').val('').trigger('change');
        $('.disable_loan_guarantors_radio').prop('checked',true).trigger('change'); 
        $('.loan_guarantor_member_details').slideUp(); 
        $('.disable_loan_processing_fee_radio').prop('checked',true).trigger('change'); 
        $('select[name="loan_processing_fee_type"]').val('').trigger('change');
        $('input[name="loan_processing_fee_fixed_amount"]').val();
        $('input[name="loan_processing_fee_percentage_rate"]').val();
        $('select[name="loan_processing_fee_percentage_charged_on"]').val('').trigger('change');   
    }

    function loan_type_details(loan_type_id){
        get_maximum_savings(loan_type_id);       
        $('.loan_details_holder').html("");
            mApp.block('.loan_type_form_holder', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Processing...'
        });
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('ajax/loan_types/ajax_get_loan_details'); ?>",
            data: {'loan_type_id':loan_type_id},
            success: function(response){
                if(isJson(response)){
                    var result = $.parseJSON(response);

                    if(result){
                        $('input[name="loan_guarantor_type"]').val(result.loan_guarantors_type);
                        $('input[name="maximum_guarantors_from_settings"]').val(result.maximum_guarantors);
                        $('input[name="loan_repayment_type"]').val(result.loan_repayment_period_type);
                        $('input[name="loan_amount_type"]').val(result.loan_amount_type);
                        $('input[name="maximum_loan_amount"]').val(result.maximum_loan_amount);
                        
                        $('#member_form_holder').slideDown();
                        $('#loan_repayment_form_holder').slideDown();
                        $('#member_to_amount_holder').slideDown();
                        $('#disbursement_account_holder').slideDown();
                        $('#show_loan_breakdown').slideDown();

                        if( result.fixed_repayment_period === "" || result.fixed_repayment_period === null ) {
                            var min_months = result.minimum_repayment_period;
                            var max_months = result.maximum_repayment_period;
                            var text_to_show = "Enter Repayment Period in months ( between " + min_months + " - " + max_months + " months )";

                            $('#repayment_period_details_holder').text(text_to_show);
                            $('#repayment_period_holder').slideDown();
                        }
                        
                        if( Number(result.enable_loan_guarantors) > 0 ){
                            $('#gurantors_period_holder').slideDown();
                        }

                        if( Number(result.minimum_guarantors) > 0 ) {
                            var html = $('.append_guarantor_settings').html();
                            for(var i = 0; i < result.minimum_guarantors; i++) {
                                $('#guarantor_settings_holder').append(html);
                            }
                            update_guarantor_field_names();
                            $('.m-select2-append').select2({
                                placeholder:{
                                    id: '-1',
                                    text: "--Select option--",
                                },
                                width:"100%",
                            });
                            $('#guarantor_settings_holder').slideDown();
                            // document.querySelector('input[name=loan_application_amount]').removeEventListener('blur',() => {});
                        }

                        if($('input[name="loan_amount"]').val() || $('select[name="account_id"]').val()){
                            $('.interest_type_input_group').slideDown();
                            $('select[name="grace_period"]').trigger('change');
                        }
                        if(Number(result.loan_repayment_period_type) == 1){
                            $('input[name="repayment_period"]').val(result.fixed_repayment_period).trigger('keyup keydown');
                        }                         

                        $('select[name=interest_type]').val(result.interest_type).trigger('change');
                        $('input[name="interest_rate"]').val(result.interest_rate);
                        $('select[name="loan_interest_rate_per"]').val(result.loan_interest_rate_per).trigger('change');
                        if($('input[name="interest_rate"]').val()){
                            $('#grace_period').slideDown();
                        }
                        $('select[name="grace_period"]').val(result.grace_period).trigger('change');
                        //$('.addition_loan_types_form_details').slideDown();
                        if(result.enable_loan_fines == 1){                           
                            $('.enable_loan_fines_radio').prop('checked',true).trigger('change');                      
                        }else{
                            $('.disable_loan_fines_radio').prop('checked',true).trigger('change');
                        }
                        $('select[name="loan_fine_type"]').val(result.loan_fine_type).trigger('change');
                        $('select[name="one_off_fine_type"]').val(result.one_off_fine_type).trigger('change');
                        $('input[name="one_off_fixed_amount"]').val(result.one_off_fixed_amount);
                        $('input[name="fixed_fine_amount"]').val(result.fixed_fine_amount); 

                        $('#fixed_amount_fine_frequency').val(result.fixed_amount_fine_frequency).trigger('change');
                        $('select[name="fixed_amount_fine_frequency_on"]').val(result.fixed_amount_fine_frequency_on).trigger('change');
                        $('input[name="percentage_fine_rate"]').val(result.percentage_fine_rate);
                        $('select[name="percentage_fine_frequency"]').val(result.percentage_fine_frequency).trigger('change');
                        $('select[name="percentage_fine_on"]').val(result.percentage_fine_on).trigger('change');

                        if(result.enable_outstanding_loan_balance_fines == 1){                           
                            $('.enable_outstanding_loan_balance_fines_radio').prop('checked',true).trigger('change');                      
                        }else{
                            $('.disable_outstanding_loan_balance_fines_radio').prop('checked',true).trigger('change');
                        }
                        $('#outstanding_loan_balance_fine_type').val(result.outstanding_loan_balance_fine_type).trigger('change');
                        $('input[name="outstanding_loan_balance_percentage_fine_rate"]').val(result.outstanding_loan_balance_percentage_fine_rate).trigger('change');
                        $('select[name="outstanding_loan_balance_percentage_fine_frequency"]').val(result.outstanding_loan_balance_percentage_fine_frequency).trigger('change');
                        $('select[name="outstanding_loan_balance_percentage_fine_on"]').val(result.outstanding_loan_balance_percentage_fine_on).trigger('change');
                        $('input[name="outstanding_loan_balance_fine_one_off_amount"]').val(result.outstanding_loan_balance_fine_one_off_amount).trigger('change');
                        $('input[name="outstanding_loan_balance_fine_fixed_amount"]').val(result.outstanding_loan_balance_fine_fixed_amount);

                        $('select[name="outstanding_loan_balance_fixed_fine_frequency"]').val(result.outstanding_loan_balance_fixed_fine_frequency).trigger('change');

                        if(Number(result.loan_guarantors_type) == 2){            
                            $('.loan_guarantor_member_details').addClass("d-none"); 
                        }else{
                            if(result.enable_loan_guarantors == 1){
                                $('.enable_loan_guarantors_radio').prop('checked',true).trigger('change');
                                $('.loan_guarantor_member_details').slideDown();
                            }else{
                                $('.disable_loan_guarantors_radio').prop('checked',true).trigger('change'); 
                                $('.loan_guarantor_member_details').slideUp();
                            } 
                        }

                                                
                       
                        $('select[name="outstanding_loan_balance_fixed_fine_frequency"]').val(result.outstanding_loan_balance_fixed_fine_frequency).trigger('change');
                        if(result.enable_loan_processing_fee == 1){
                            $('.enable_loan_processing_fee_radio').prop('checked',true).trigger('change');
                        }else{
                            $('.disable_loan_processing_fee_radio').prop('checked',true).trigger('change'); 
                        }
                        $('select[name="loan_processing_fee_type"]').val(result.loan_processing_fee_type).trigger('change');
                        $('input[name="loan_processing_fee_fixed_amount"]').val(result.loan_processing_fee_fixed_amount);
                        $('input[name="loan_processing_fee_percentage_rate"]').val(result.loan_processing_fee_percentage_rate);
                        $('select[name="loan_processing_fee_percentage_charged_on"]').val(result.loan_processing_fee_percentage_charged_on).trigger('change');
                        add_guarantors()
                    }else{
                        $('.data_error').each(function(){
                            $('#data_message_holder').slideDown().html(result.message);
                        });
                    }
                }else{
                    //alert(response);
                }                 
                mApp.unblock('.loan_type_form_holder');
            }
        }); 
        Select2.init();
        FormInputMask.init();
    } 

    function get_maximum_savings(loan_type_id){
        var member_id = $('#member').val();
        if(member_id){
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('ajax/loan_types/get_maximum_savings'); ?>",
                data: {'loan_type_id':loan_type_id,'member_id':member_id},
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);
                        if(result){
                            $('input[name="maximum_loan_amount_from_savings"]').val(result.member_savings);       
                            add_guarantors()
                        }
                    }
                }
            });
        }
    } 

    function add_guarantors(){      
        var sms_templates =  $("div[class*='loan_guarantor_settings_values_templates']").length;     
        var loan_guarantor_type =  $('input[name="loan_guarantor_type"]').val();      
        if(loan_guarantor_type == 1){
            var count = $('input[name="maximum_guarantors_from_settings"]').val(); 
            $('.loan_guarantor_member_details').removeClass("d-none");
            if(sms_templates == 0){
                for(var i = 0; i < count; i++) {
                   $('#add-new-line-guarantor').trigger('click');
                }                   
            }
        }else if(loan_guarantor_type == 2){                       
            var maximum_amount_from_savings = $("input[name=maximum_loan_amount_from_savings]").val();   
            var maximum_loan_amount = $("input[name=maximum_loan_amount]").val();     
            var loan_amount = $("input[name=loan_amount]").val();
            var amount_string =  parseFloat(loan_amount.replace(/,/g, ""));
            var count = $('input[name="maximum_guarantors_from_settings"]').val();  
            var loan_amount_type = $("input[name=loan_amount_type]").val();
            // console.log("maximum_loan_amount "+maximum_loan_amount) 
            // console.log("loan_amount "+amount_string)  
            // console.log("loan_amount_type "+loan_amount_type) 
            // console.log("sms_templates "+sms_templates) 
                   
            if(amount_string){
                if(loan_amount_type == 2){
                    if(maximum_amount_from_savings){
                        if(amount_string > maximum_amount_from_savings){                        
                            $('.loan_guarantor_member_details').removeClass("d-none");
                            if(sms_templates == 0){
                                for(var i = 0; i < count; i++) {
                                   $('#add-new-line-guarantor').trigger('click');
                                }                   
                            }else{ 

                            }
                        }else{
                            $('#append-new-guarantor-setting').html('');
                        }
                    }else{
                        $('#append-new-guarantor-setting').html('');
                    }
                }else if(loan_amount_type == 1){
                    if(amount_string > maximum_loan_amount){
                        $('.loan_guarantor_member_details').removeClass("d-none");
                         $('.loan_guarantor_member_details').slideUp();
                        if(sms_templates == 0){
                            $('.enable_loan_guarantors_radio').prop('checked',true).trigger('change');
                            for(var i = 0; i < count; i++) {
                               $('#add-new-line-guarantor').trigger('click');
                            }                   
                        }else{ 

                        } 
                    }
                }
            }else{
                $('#append-new-guarantor-setting').html('');
            }
        }else{
            $('#append-new-guarantor-setting').html('');
        }
    }  

    function update_guarantor_field_names(){
        var number = 0;
        $('.new_guarantor').each(function(){
            $(this).find('select.guarantor_ids').attr('name','guarantor_ids['+number+']');
            $(this).find('input.guaranteed_amounts').attr('name','guaranteed_amounts['+number+']');
            $(this).find('input.guarantor_comments').attr('name','guarantor_comments['+number+']');
            number++;
        });
    }

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    } 

    function guarantor_click(){
        var html = $('#loan_guarantor_row').html();
        html = html.replace_all('checker','');
        $('#append-new-guarantor-setting').append('<div class="loan_guarantor_settings_values_templates">'+html+'</div>');
        $('.tooltips').tooltip();
        var number = 0;
        $('.guarantor_ids').each(function(){
            $(this).attr('name','guarantor_ids['+number+']');
            $(this).parent().parent().parent().find('input.guaranteed_amounts').attr('name','guaranteed_amounts['+number+']');
            $(this).parent().parent().parent().find('input.guarantor_comments').attr('name','guarantor_comments['+number+']');
            number++;
        });
        FormInputMask.init();
        // $('#append-new-guarantor-setting .m-select2-append').select2({
        //     placeholder:{
        //         id: '-1',
        //         text: "--Select option--",
        //     }, 
        //     // allowClear: !0
        // });
    }

    String.prototype.replace_all = function(search,replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };



</script>
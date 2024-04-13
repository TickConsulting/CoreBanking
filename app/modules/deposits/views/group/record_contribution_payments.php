<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state" role="form" id="contribution_payments_form"'); ?>
    <span class="error"></span>
    <div class="table-responsive">
        <table class="table table-condensed contribution-table multiple_payment_entries">
            <thead>
                <tr> 
                    <th width="1%">
                        #
                    </th>
                    <th width="11%">
                        <?php echo translate('Date');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="16%">
                        <?php echo translate('Member');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="17%">
                        <?php echo translate('Payment For');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="17%">
                        <?php echo translate('Account');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="13%">
                        <?php echo translate('Channel');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="12%">
                        <?php echo translate('Amount');?>
                         (<?php echo $this->group_currency; ?>) 
                        <span class='required'>*</span>
                    </th>
                    <th width="7%">
                        <?php echo translate('Alerts');?>
                    </th>
                    <th width="3%">
                       &nbsp;
                    </th>
                </tr>
            </thead>
            <tbody id='append-place-holder'>
                <tr>
                    <th scope="row" class="count">
                        1
                    </th>
                    <td>
                        <?php echo form_input('deposit_dates[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input deposit_date date-picker" readonly="readonly" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" data-date-start-date="-20y" autocomplete="off" ');?>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                             <?php echo form_dropdown('members[0]',array(''=>'Select member')+translate($this->active_group_member_options)+array('0'=>"Add Member"),'',' class="form-control m-input m-select2 member" ');?>
                        </span>
                        <br/>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('contributions[0]',array(''=>'Select contribution')+translate($contribution_options)+array('0'=>"Add Contribution"),'',' class="form-control m-input m-select2  contribution" ');?>
                        </span>
                        <br>
                        <a href="javascript:;" class=" btn btn-sm m-btn--square btn-default btn-xs inline-table-button add_deposit_description" id="" style="margin-top:2px; width: 100%;">
                            <i class="la la-plus"></i>
                            <span class="hidden-380">
                            <?php echo translate('Add description');?>
                            </span>
                        </a>
                        <div class="margin-top-5 deposit_description" data-original-title="" data-container="body" style="display:none;"><i class="" ></i>
                            <?php 
                                $textarea = array(
                                    'name' => 'deposit_descriptions[0]',
                                    'id' => '',
                                    'value' => '',
                                    'cols' => 25,
                                    'rows' => 5,
                                    'maxlength '=> '',
                                    'class' => 'form-control',
                                    'placeholder' => ''
                                ); 
                                echo form_textarea($textarea);
                            ?>
                        </div>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('accounts[0]',array(''=>'Select account option')+translate($account_options),'',' class="form-control m-input m-select2  account" ');?>
                        </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('deposit_methods[0]',array(''=>'Select deposit method')+translate($deposit_method_options),'',' class="form-control m-input deposit_method m-select2" ');?>
                        </span>
                    </td>
                    <td>
                        <?php echo form_input('amounts[0]','',' class="form-control input-sm amount currency text-right" ');?>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-lg-6 col-sm-6">
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand" data-toggle="m-tooltip" title="" data-original-title="Send SMS notification">
                                    <?php echo form_checkbox('send_sms_notification[0]',1,FALSE,' class = "send_sms_notification" '); ?>
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-lg-6 col-sm-6" style="padding-left: 5px;">
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand" data-toggle="m-tooltip" title="" data-original-title="Send Email notification">
                                    <?php echo form_checkbox('send_email_notification[0]',1,FALSE,' class = "send_email_notification" '); ?>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <a class="remove-line">
                            <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-right" colspan=7>
                        <?php echo translate('Totals');?>
                    </td>
                    <td class="text-right total-amount" colspan="4"><?php echo number_to_currency();?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn-default btn-sm add-new-line" id="add-new-line">
                <i class="la la-plus"></i><?php echo translate('Add New Payment Line');?>
            </a>
        </div>
    </div>

    <div class="m-form__actions m-form__actions p-0 pt-5 m--margin-top-10">                            
        <div class="row">
            <div class="col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm submit_form_button" id="" type="submit">
                       <?php echo translate('Record Contribution Payments');?>                              
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="">
                        <?php echo translate('Cancel') ?>                              
                    </button>
                </span>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>


<div id='append-new-line' class="d-none">
    <table>
        <tbody>
            <tr>
                <th scope="row" class="count">
                    1
                </th>
                <td>
                    <?php echo form_input('deposit_dates[0]',timestamp_to_datepicker(time()),' class="form-control m-input deposit_date date-picker input-sm" readonly="readonly" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" data-date-start-date="-20y" autocomplete="off"');?>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('members[0]',array(''=>'Select member')+translate($this->active_group_member_options)+array('0'=>"Add Member"),'',' class="form-control m-input m-select2-append member" ');?>
                    </span>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('contributions[0]',array(''=>'Select contribution')+translate($contribution_options)+array('0'=>"Add Contribution"),'',' class="form-control m-input m-select2-append contribution" ');?>
                    </span>
                    <a href="javascript:;" class="btn btn-sm m-btn--square btn-default btn-xs inline-table-button add_deposit_description" id="">
                        <i class="la la-plus"></i>
                        <span class="hidden-380">
                            <?php echo translate('Add description');?>
                        </span>
                    </a>
                    <div class="margin-top-5 deposit_description" data-original-title="" data-container="body" style="display:none;"><i class="" ></i>
                        <?php 
                            $textarea = array(
                                'name' => 'deposit_descriptions[0]',
                                'id' => '',
                                'value' => '',
                                'cols' => 25,
                                'rows' => 5,
                                'maxlength '=> '',
                                'class' => 'form-control deposit_description_input',
                                'placeholder' => ''
                            ); 
                            echo form_textarea($textarea);
                        ?>
                    </div>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('accounts[0]',array(''=>'Select account')+translate($account_options),'',' class="form-control m-input m-select2-append account"');?>
                    </span>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('deposit_methods[0]',array(''=>'Select deposit method')+translate($deposit_method_options),'',' class="form-control m-input m-select2-append deposit_method" ');?>
                    </span>
                </td>
                <td>
                    <?php echo form_input('amounts[0]','',' class="form-control input-sm amount currency text-right" ');?>
                </td>
                <td>
                    <div class="row">
                        <div class="col-lg-6">
                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand" data-toggle="m-tooltip" title="" data-original-title="Send SMS notification">
                                <?php echo form_checkbox('send_sms_notification[0]',1,FALSE,' class = "send_sms_notification" '); ?>
                                <span></span>
                            </label>
                        </div>
                        <div class="col-lg-6" style="padding-left: 5px;">
                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand" data-toggle="m-tooltip" title="" data-original-title="Send Email notification">
                                <?php echo form_checkbox('send_email_notification[0]',1,FALSE,' class = "send_email_notification" '); ?>
                                <span></span>
                            </label>
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    <a href='javascript:;' class="remove-line">
                        <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="modal fade" id="create_new_account_pop_up" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Create New Account');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active show" data-toggle="tab" href="#bank_account_tab" onClick="handle_tab_switch('bank_account')">
                            <?php echo translate('Bank');?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#sacco_account_tab" onClick="handle_tab_switch('sacco_account')">
                            <?php echo translate('Group');?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#mobile_money_account_tab" onClick="handle_tab_switch('mobile_money_account')">
                            <?php echo translate('Mobile Money');?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#petty_cash_account_tab" onClick="handle_tab_switch('petty_cash_account')">
                            <?php echo translate('Petty Cash');?>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active show" id="bank_account_tab" role="tabpanel">
                        <?php echo form_open($this->uri->uri_string(),'class=" bank_account_form form_submit m-form m-form--state" id="bank_account_form" role="form"'); ?>
                            <div class="m-form__section m-form__section--first">
                                <span class="error"></span>
                                <div class="form-group m-form__group">
                                    <label for="example_input_full_name">
                                        <?php echo translate('Account Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_name','','id="bank_account_name" class="form-control" placeholder="Account Name"'); ?>
                                  <!--   <span class="m-form__help">
                                        <?php echo translate('Enter your account name as registered');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group">
                                    <label>
                                        <?php echo translate('Bank Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('bank_id',array(''=>'--Select Bank--')+$banks,'','id="bank_id" class="form-control m-select2"  ') ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Select the bank your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group bank_branch_id" style="display: none;">
                                    <label>
                                        <?php echo translate('Bank Branch');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('bank_branch_id',array(''=>'--Select Bank Name First--'),'','class="form-control m-select2" id = "bank_branch_id"  ') ?>
                                    <!-- <span class="m-form__help">
                                        <?php echo translate('Select the bank branch your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group bank_account_number" style="display: none;">
                                    <label>
                                        <?php echo translate('Account Number');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_number','',' id="bank_account_number" class="form-control" placeholder="Account Number"'); ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account number as registered');?>
                                    </span> -->
                                </div>

                                <div class="row">
                                    <div class="col-md-12 m--align-right">
                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="create_bank_account">
                                            <?php echo translate('Submit');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>

                    <div class="tab-pane" id="sacco_account_tab" role="tabpanel">
                        <?php echo form_open($this->uri->uri_string(),'class=" sacco_account_form form_submit m-form m-form--state" id="sacco_account_form" role="form"'); ?>
                            <div class="m-form__section m-form__section--first">
                                <span class="error"></span>
                                <div class="form-group m-form__group">
                                    <label for="example_input_full_name">
                                        <?php echo translate('Account Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_name','','class="form-control" placeholder="Account Name" id="sacco_account_name" '); ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account name as registered');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group">
                                    <label>
                                        <?php echo translate('Group Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('sacco_id',array(''=>'--Select Sacco--')+$saccos,'','class="form-control m-select2" id="sacco_id"  ') ?>
                                  <!--   <span class="m-form__help">
                                        <?php echo translate('Select the Group your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group sacco_branch_id" style="display: none;">
                                    <label>
                                        <?php echo translate('Group Branch');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('sacco_branch_id',array(''=>'--No branch records found--'),'','class="form-control m-select2" id = "sacco_branch_id"  ') ?>
                                    <!-- <span class="m-form__help">
                                        <?php echo translate('Select the Group your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group sacco_account_number" style="display: none;">
                                    <label>
                                        <?php echo translate('Account Number');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_number','','class="form-control" placeholder="Account Number" id="sacco_account_number"'); ?>
                                    <!-- <span class="m-form__help">
                                        <?php echo translate('Enter your account number as registered');?>
                                    </span> -->
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 m--align-right">
                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="create_sacco_account">
                                            <?php echo translate('Submit');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>

                    <div class="tab-pane" id="mobile_money_account_tab" role="tabpanel">
                        <?php echo form_open($this->uri->uri_string(),'class=" mobile_money_account_form form_submit m-form m-form--state" id="mobile_money_account_form" role="form"'); ?>
                            <div class="m-form__section m-form__section--first">
                                <span class="error"></span>
                                <div class="form-group m-form__group">
                                    <label for="example_input_full_name">
                                        <?php echo translate('Mobile Money Account Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_name','','class="form-control" placeholder="Mobile Money Account Name" id="mobile_money_account_name" '); ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account name');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group">
                                    <label>
                                        <?php echo translate('Mobile Money Provider');?>
                                        <span class="required">*</span>
                                    </label>
                                     <?php echo form_dropdown('mobile_money_provider_id',array(''=>'--Select Mobile Money Provider--')+$mobile_money_providers,'','class="form-control  m-select2" id="mobile_money_provider_id"  ') ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Select the mobile money provider your account is registered to');?>
                                    </span> -->
                                </div>
                                <div class="form-group m-form__group mobile_money_account_number" style="display: none;">
                                    <label>
                                        <?php echo translate('Account Number');?>/
                                        <?php echo translate('Till Number');?>/
                                        <?php echo translate('Phone Number');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('account_number','','class="form-control" placeholder="Account Number / Phone Number / Till Number" id="mobile_money_account_number"'); ?>
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account number as registered');?>
                                    </span> -->
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 m--align-right">
                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                        <button type="submit" id="create_mobile_money_account" class="btn btn-primary">
                                            <?php echo translate('Submit');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>

                    <div class="tab-pane" id="petty_cash_account_tab" role="tabpanel">
                        <?php echo form_open($this->uri->uri_string(),'class=" petty_cash_account_form form_submit m-form m-form--state" id="petty_cash_account_form" role="form"'); ?>
                            <div class="m-form__section m-form__section--first">
                                <span class="error"></span>
                                <div class="form-group m-form__group">
                                    <label for="example_input_full_name">
                                        <?php echo translate('Petty Cash Account Name');?>
                                        <span class="required">*</span>                                            
                                    </label>
                                    <?php echo form_input('account_name','','class="form-control slug_parent" placeholder="Petty Cash Account Name " id="petty_cash_account_name"'); ?>
                                    <?php echo form_hidden('slug','','class="form-control slug"'); ?>     
                                   <!--  <span class="m-form__help">
                                        <?php echo translate('Enter your account name');?>
                                    </span> -->
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 m--align-right">
                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                        <button type="submit" id="create_petty_cash_account" class="btn btn-primary">
                                            <?php echo translate('Submit');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_new_member_pop_up" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Add Member');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
                <div class="modal-body">
                    <?php echo form_open($this->uri->uri_string(),'class=" add_new_member_form form_submit m-form m-form--state" id="add_new_member_form" role="form"'); ?>
                        <span class="error"></span>
                        <div class="m-form__section m-form__section--first">
                            <div class="form-group m-form__group row">
                                <div class="col-md-4">
                                    <label>
                                        <?php echo translate('First Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('first_name','',' id="first_name" class="form-control" placeholder="First Name"');?>
                                </div>

                                <div class="col-md-4">
                                    <label>
                                        <?php echo translate('Middle Name');?>
                                    </label>
                                    <?php echo form_input('middle_name','',' id="middle_name" class="form-control" placeholder="Middle Name"');?>
                                </div>

                                <div class="col-md-4">
                                    <label>
                                        <?php echo translate('Last Name');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('last_name','',' id="last_name" class="form-control" placeholder="Last Name"');?>
                                </div>
                            </div>

                            <div class="form-group m-form__group row">
                                <div class="col-md-4">
                                    <label>
                                        <?php echo translate('Phone Number');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('phone','',' id="phone" class="form-control" placeholder="Phone Number"');?>
                                </div>

                                <div class="col-md-4">
                                    <label>
                                        <?php echo translate('Email Address');?>
                                    </label>
                                    <?php echo form_input('email','',' id="email" class="form-control" placeholder="Email Address"');?>
                                </div>

                                <div class="col-md-4">
                                    <label>
                                        <?php echo translate('Member Group Role');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('group_role_id',array(''=>'Select Group Role')+$group_role_options,'','class="form-control m-select2" id="group_role"'); ?>
                                </div>
                            </div>

                            <div class="m-form__group form-group">
                                <label for="">
                                    <?php echo translate('Invitation Notifications');?>
                                </label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                        <?php echo form_checkbox('send_sms_notification',1,'',' id="send_sms_notification" '); ?>
                                        <?php echo translate('Send SMS Invitation');?>
                                        <span></span>
                                    </label>
                                    <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                        <?php echo form_checkbox('send_email_notification',1,'',' id="send_email_notification" '); ?>
                                        <?php echo translate('Send Email Invitation');?>
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 m--align-right">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <?php echo translate('Close');?>
                                    </button>
                                    <button type="submit" id="add_member_submit" class="btn btn-primary submit modal_submit_form_button">
                                        <?php echo translate('Save changes');?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
        </div>
    </div>
</div>

<a class="inline d-none" data-toggle="modal" data-target="#create_new_member_pop_up" data-title="Add Member" data-id="create_member" id="add_new_member"  data-backdrop="static" data-keyboard="false">
    <?php echo translate('Add Member');?>
</a>

<div class="modal fade" id="contributions_form" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Add Contribution');?>
                </h5>
                <button type="button" class="close" id="cancel_create_contribution_form" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open($this->uri->uri_string(),' id="create_contribution" class="m-form m-form--state"'); ?>
                    <div  class="form-body">
                        <div class="form-group m-form__group row">
                            <div class="col-lg-6">
                                <label>
                                    <?php echo translate('Contribution Name');?>
                                    <span class="required">*</span>
                                </label>
                                <?php echo form_input('name',"",'class="form-control m-input--air" placeholder="Contribution Name" id="name"'); ?>
                            </div>
                            <div class="col-lg-6">
                                <label>
                                    <?php echo translate('Contribution Category');?>
                                    <span class="required">*</span>
                                </label>
                                <?php echo form_dropdown('category',array(''=>'--Select Contribution Category--')+translate($contribution_category_options),"",'class="form-control m-input--air select2-append" id = "category"  ') ?>
                            </div>
                        </div>

                        <div class="form-group m-form__group pt-0 row">
                            <div class="col-md-6">
                                <label>
                                    <?php echo translate('Contribution Amount per Member');?>
                                    <span class="required">*</span>
                                </label>
                                <?php echo form_input('amount',"",'  class="form-control m-input--air currency" placeholder="Contribution Amount" id="amount"');?>
                            </div>
                            <span class="m-input--air col-md-6" style="width:100%;">
                                <label>
                                    <?php echo translate('Contribution Type');?>
                                    <span class="required">*</span>
                                </label>
                                <?php echo form_dropdown('type',array(''=>'--Select Contribution Type--')+translate($contribution_type_options),"",'class="form-control m-input--air select2-append" id = "type"  ') ?>
                            </span>
                        </div>
                        <div class="form-group m-form__group pt-0 row" id='regular_invoicing_active_holder' style="display: none;">
                            <div class="col-lg-12">
                                <label for="">
                                    <?php echo translate('Do you wish to activate automatic invoicing');?>?
                                </label>
                                <div class="m-radio-inline">
                                    <label class="m-radio m-radio--solid m-radio--brand">
                                        <?php echo form_radio('regular_invoicing_active',TRUE,FALSE,' id="regular_invoicing_active" class="enable_setting" '); ?>
                                        <?php echo translate('Yes');?>
                                        <span></span>
                                    </label>

                                    <label class="m-radio m-radio--solid m-radio--brand">
                                        
                                        <?php echo form_radio('regular_invoicing_active',FALSE,TRUE,' id="regular_invoicing_inactive" class="disable_setting" '); ?>
                                        <?php echo translate('No');?>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group m-form__group pt-0 row" id='one_time_invoicing_active_holder' style="display: none;">
                            <div class="col-lg-12">
                                <label for="">
                                    <?php echo translate('Do you wish to activate automatic invoicing');?>?
                                </label>
                                <div class="m-radio-inline">
                                    <label class="m-radio m-radio--solid m-radio--brand">
                                        <?php echo form_radio('one_time_invoicing_active',TRUE,FALSE,' id="one_time_invoicing_active" '); ?> 
                                        <?php echo translate('Yes');?>
                                        <span></span>
                                    </label>

                                    <label class="m-radio m-radio--solid m-radio--brand">
                                        <?php echo form_radio('one_time_invoicing_active',FALSE,TRUE,' id="one_time_invoicing_inactive" '); ?> 
                                        <?php echo translate('No');?>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id='regular_invoicing_settings' style="display:none;">
                            <div class="form-group m-form__group row">
                                <div class="col-lg-12">
                                    <label>
                                        <?php echo translate('How often do members contribute');?>?
                                        <span class="required">*</span>
                                    </label>
                                    <span class="m-input--air" style="width:100%;">
                                        <?php echo form_dropdown('contribution_frequency',translate($contribution_frequency_options),"",' id="contribution_frequency" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group m-form__group pt-0 row" id='once_a_month' style="display: none;">
                                <div class="col-lg-12">
                                    <label>
                                        <?php echo translate('When do members contribute');?>?
                                        <span class="required">*</span>
                                    </label>
                                    <div class='row'>
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <?php echo form_dropdown('month_day_monthly',translate($days_of_the_month),"",' id="month_day_monthly" class=" form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <?php echo form_dropdown('week_day_monthly',translate($month_days),"",' id="week_day_monthly" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id='twice_every_one_month'>
                                <div class="form-group m-form__group row pt-0">
                                    <div class="col-lg-12">
                                        <label><?php echo translate('When do members contribute');?>?<span class="required">*</span></label>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-4 col-xs-4">
                                                <?php echo form_dropdown('after_first_contribution_day_option',translate($contribution_days_option),'','id="after_first_contribution_day_option" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                            </div>

                                            <div class="col-md-4 col-sm-4 col-xs-4">
                                                <?php echo form_dropdown('after_first_day_week_multiple',translate($month_days),'','id="after_first_day_week_multiple" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                            </div>

                                            <div class="col-md-4 col-sm-4 col-xs-4">
                                                <?php echo form_dropdown('after_first_starting_day',translate($starting_days),'','id="after_first_starting_day" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group m-form__group row pt-0 pb-4">
                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <?php echo form_dropdown('after_second_contribution_day_option',translate($contribution_days_option),'','id="after_second_contribution_day_option" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                    </div>

                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <?php echo form_dropdown('after_second_day_week_multiple',translate($month_days),'','id="after_second_day_week_multiple" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                    </div>

                                    <div class="col-md-4 col-sm-4 col-xs-4">
                                        <?php echo form_dropdown('after_second_starting_day',translate($starting_days),'','id="after_second_starting_day" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group m-form__group pt-0 row" id='once_a_week' style="display: none;">
                                <div class='col-md-12'>
                                    <label>
                                        <?php echo translate('When do members contribute');?>?
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_dropdown('week_day_weekly',translate($week_days),"",'id="week_day_weekly" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                </div>
                            </div>

                            <div class="form-group m-form__group pt-0 row" id='once_every_two_weeks' style="display: none;">
                                <div class='col-md-12'>
                                    <label>
                                        <?php echo translate('When do members contribute');?>?
                                        <span class="required">*</span>
                                    </label>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <?php echo form_dropdown('week_day_fortnight',translate($every_two_week_days),"",'id="week_day_fortnight" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                        </div>

                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                             <?php echo form_dropdown('week_number_fortnight',translate($week_numbers),"",'id="week_number_fortnight" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                        </div>  
                                    </div>
                                </div>
                            </div>

                            <div class="form-group m-form__group pt-0 row" id='once_every_multiple_months' style="display: none;">
                                <div class='col-md-12'>
                                    <label>
                                        <?php echo translate('When do members contribute');?>?
                                       <span class="required">*</span>
                                    </label>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4 col-xs-4">
                                            <?php echo form_dropdown('month_day_multiple',translate($days_of_the_month),"",'id="month_day_multiple" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-4">
                                            <?php echo form_dropdown('week_day_multiple',translate($month_days),"",'id="week_day_multiple" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-4">
                                            <?php echo form_dropdown('start_month_multiple',translate($starting_months),"",'id="start_month_multiple" class="form-control m-input--air select2-append" data-placeholder="Select..."'); ?>
                                        </div>  
                                    </div>
                                </div>
                            </div>

                            <?php echo form_hidden('invoice_days',7,translate($invoice_days),' id="invoice_days" class=" form-control select2-append" data-placeholder="Select..."'); ?>
                        </div>

                        <div id='one_time_invoicing_settings' style="display: none;">
                            <div class="form-group m-form__group pb-4 row">
                                <div class="col-lg-6">
                                    <label>
                                        <?php echo translate('Invoice Date');?>:
                                    </label>
                                    <?php echo form_input('invoice_date',timestamp_to_datepicker(time()),' id="invoice_date" class="form-control m-input--air m-input invoice_date date-picker text-center" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off" readonly');?>
                                </div>
                                <div class="col-lg-6">
                                    <label>
                                        <?php echo translate('Contribution Date');?>/<?php echo translate('Due Date');?>
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('contribution_date',timestamp_to_datepicker(time()),' id="contribution_date" class="form-control m-input--air m-input contribution_date date-picker text-center" data-date-format="dd-mm-yyyy" data-date-viewmode="years" autocomplete="off" readonly');?>
                                </div>
                            </div>
                        </div>

                        <div id='invoice_notifications' style="display:none;">
                            <div class="form-group m-form__group row">
                                <div class="col-lg-12">
                                    <label for="">
                                        <?php echo translate('Do you wish to enable invoice notifications');?>?
                                    </label>
                                    <div class="m-radio-inline">
                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('invoice_notifications_active',TRUE,FALSE,' id="invoice_notifications_active" class="enable_setting" '); ?> 
                                            <?php echo translate('Yes');?>
                                            <span></span>
                                        </label>

                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('invoice_notifications_active',FALSE,TRUE,' id="invoice_notifications_inactive" class="disable_setting" '); ?>
                                            <?php echo translate('No');?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="m-checkbox-inline" id="invoice_notifications_settings" style="display:none;">
                                <div class="form-group m-form__group pb-4 row">
                                    <div class="col-lg-12">
                                        <label for="">
                                            <?php echo translate('Select notifications to enable');?>
                                        </label>

                                        <div class="m-checkbox-inline">
                                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                                <?php echo form_checkbox('sms_notifications_enabled',1,FALSE,' id="sms_notifications_enabled" '); ?>
                                                <?php echo translate('SMS Notifications');?>
                                                <span></span>
                                            </label>
                                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                                <?php echo form_checkbox('email_notifications_enabled',1,FALSE,' id="email_notifications_enabled" '); ?>
                                                <?php echo translate('Email Notifications');?>
                                                <span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id='contribution_member_list_settings' style="display:none;">
                            <div class="form-group m-form__group row">
                                <div class="col-lg-12">
                                    <label for="">
                                        <?php echo translate('Do you wish to limit invoicing for this contribution to specific members');?>?
                                    </label>
                                    <div class="m-radio-inline">
                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('enable_contribution_member_list',TRUE,FALSE,' id="enable_contribution_member_list" class="enable_setting"'); ?>
                                            <?php echo translate('Yes');?>
                                            <span></span>
                                        </label>

                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('enable_contribution_member_list',FALSE,TRUE,' id="disable_contribution_member_list" class="disable_setting" '); ?>
                                            <?php echo translate('No');?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id='contribution_member_list' style="display:none;">
                                <div class="form-group m-form__group pb-4 row">
                                    <div class="col-lg-12">
                                        <label>
                                            <?php echo translate('Select Contributing Members');?>
                                            <span class="required">*</span>
                                        </label>
                                        <?php echo form_dropdown('contribution_member_list[]',translate($this->active_group_member_options),array(),' id="" class=" form-control m-input--air select2-append" multiple="multiple" data-placeholder="Select..."'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="fines" style="display:none;">
                            <div class="form-group m-form__group row">
                                <div class="col-lg-12">
                                    <label for="">
                                        <?php echo translate('Do you charge fines for late payment');?>?
                                    </label>
                                    <div class="m-radio-inline">
                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('enable_fines',TRUE,FALSE,' id="enable_fines" class="enable_setting"'); ?> 
                                                <?php echo translate('Yes');?>
                                            <span></span>
                                        </label>

                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('enable_fines',FALSE,TRUE,' id="disable_fines" class="disable_setting" '); ?> 
                                            <?php echo translate('No');?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id='fine_settings' style="display: none;">
                                <div class='fine_setting_row'>
                                    <div class='m-form__group form-group row'>
                                        <div class="col-lg-12">
                                            <label>
                                                <?php echo translate('We charge a');?>
                                                <span class="required">*</span>
                                            </label>
                                            <div class='row'>
                                                <div class="col-md-4 col-sm-4 col-xs-4">
                                                    <?php echo form_dropdown('fine_type[]',array(''=>'Select fine type')+translate($fine_types),'','id="" class="form-control m-input--air fine_types select2-append" data-placeholder="Select..."'); ?>
                                                </div>
                                                <div id='' class="col-md-4 col-sm-4 col-xs-4 percentage_fine_settings fine_percentage_rate">
                                                    <?php echo form_input('percentage_rate[]','','class="form-control m-input--air percentage_rates" placeholder="Percentage Rate"'); ?>
                                                </div>
                                                <div id='' class="col-md-4 col-sm-4 col-xs-4 fixed_fine_settings fine_fixed_amount">
                                                    <?php echo form_input('fixed_amount[]','','class="form-control m-input--air currency fixed_amounts" placeholder="Fixed Amount"'); ?>
                                                </div>
                                                <div class="col-md-4 col-sm-4 col-xs-4 fixed_fine_settings fixed_fine_mode">
                                                    <?php echo form_dropdown('fixed_fine_mode[]',array(''=>'Select how fines behave')+translate($fine_mode_options),'','id="" class="form-control m-input--air select2-append fixed_fine_modes" data-placeholder="Select..."'); ?>
                                                </div>
                                                <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_settings percentage_fine_on">
                                                    <?php echo form_dropdown('percentage_fine_on[]',array(''=>'Select when fines is calculated based on')+translate($percentage_fine_on_options),'','id="" class="form-control m-input--air percentage_fine_ons select2-append" data-placeholder="Select..."'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='percentage_fine_settings'>
                                        <div class='m-form__group form-group row'>
                                            <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_chargeable_on">
                                                <?php echo form_dropdown('percentage_fine_chargeable_on[]',array(''=>'Select when fines are charged')+translate($fine_chargeable_on_options),'','id="" class="form-control m-input--air percentage_fine_chargeable_ons select2-append" data-placeholder="Select..."'); ?>
                                            </div>

                                            <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_mode">
                                                <?php echo form_dropdown('percentage_fine_mode[]',array(''=>'Select how fines behave')+translate($fine_mode_options),'','id="" class="form-control m-input--air percentage_fine_modes select2-append" data-placeholder="Select..."'); ?>
                                            </div>

                                            <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_frequency">
                                                <?php echo form_dropdown('percentage_fine_frequency[]',array(''=>'Select fine frequency')+translate($fine_frequency_options),'','id="" class="form-control m-input--air percentage_fine_frequencies select2-append" data-placeholder="Select..."'); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='fixed_fine_settings'>
                                        <div class='m-form__group form-group row'>
                                            <!-- <div class='row'> -->
                                            <div class="col-md-6 col-sm-6 col-xs-6 fixed_fine_chargeable_on">
                                                <?php echo form_dropdown('fixed_fine_chargeable_on[]',array(''=>'Select when fines are charged')+translate($fine_chargeable_on_options),'','id="" class="form-control m-input--air fixed_fine_chargeable_ons select2-append" data-placeholder="Select..."'); ?>
                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6 fixed_fine_frequency">
                                                <?php echo form_dropdown('fixed_fine_frequency[]',array(''=>'Select fine frequency')+translate($fine_frequency_options),'','id="" class="form-control m-input--air fixed_fine_frequencies select2-append" data-placeholder="Select..."'); ?>
                                            </div>
                                            <!-- </div> -->
                                        </div>
                                    </div>

                                    <div class='fine_limit' style="display: none;">
                                        <div class='m-form__group form-group pt-4 pb-2 row'>
                                            <div class="col-lg-12">
                                                <?php echo form_dropdown('fine_limit[]',translate($fine_limit_options),'','id="" class="form-control m-input--air fine_limits select2-append" data-placeholder="Select..."'); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='fine_notifications'>
                                        <div class='m-form__group form-group pt-2 row'>
                                            <div class='col-lg-12'>
                                                <label>
                                                    <?php echo translate('Do you wish to notify members when they are fined');?>?
                                                </label>

                                                <div class="m-radio-inline">
                                                    <label class="m-radio m-radio--solid m-radio--brand">
                                                        <?php echo form_radio('fine_notifications_enabled[]',TRUE,FALSE,' id="fine_notifications_enabled" class="enable_setting fine_sms_notifications_enableds"'); ?> 
                                                            <?php echo translate('Yes');?>
                                                        <span></span>
                                                    </label>

                                                    <label class="m-radio m-radio--solid m-radio--brand">
                                                        <?php echo form_radio('fine_notifications_enabled[]',FALSE,TRUE,' id="fine_notifications_enabled" class="disable_setting fine_sms_notifications_disableds" '); ?> 
                                                        <?php echo translate('No');?>
                                                        <span></span>
                                                    </label>
                                                </div>
                                               
                                            </div>
                                        </div>
                                        <div class="fine_notifications_settings" style="display: none;">
                                            <div class="m-checkbox-inline m-form__group form-group pb-4 row">
                                                <div class="col-lg-12">
                                                    <label>
                                                        <?php echo translate('Select notification to enable');?>
                                                    </label>
                                                    <div class="m-checkbox-inline">
                                                        <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                                            <?php echo form_checkbox('fine_sms_notifications_enabled[]',1,'',' class="fine_sms_notifications_enableds" '); ?> 
                                                            <?php echo translate('SMS Notifications');?>
                                                            <span></span>
                                                        </label>
                                                        <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                                            <?php echo form_checkbox('fine_email_notifications_enabled[]',1,'',' class="fine_email_notifications_enableds" '); ?>
                                                            <?php echo translate('Email Notifications');?>
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                </div>
                            </div>
                        </div>

                        <div class="contribution_options" style="display:none;">
                            <div class="form-group m-form__group row">
                                <div class="col-lg-12">
                                    <label>
                                        <?php echo translate('Do you wish to disable contribution arrears for this contribution');?>?
                                    </label>

                                    <div class="m-radio-inline">
                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('display_contribution_arrears_cumulatively',TRUE,FALSE,' id="display_contribution_arrears_cumulatively" class="enable_setting display_contribution_arrears_cumulatively"'); ?> 
                                                <?php echo translate('Yes');?>
                                            <span></span>
                                        </label>

                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('display_contribution_arrears_cumulatively',FALSE,TRUE,' id="display_contribution_arrears_cumulatively" class="disable_setting disable_display_contribution_arrears_cumulatively" '); ?> 
                                            <?php echo translate('No');?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group m-form__group pt-0 row">
                                <div class="col-lg-12">
                                    <label>
                                        <?php echo translate('Disable contribution refunds for this contribution');?>?
                                    </label>

                                    <div class="m-radio-inline">
                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('is_non_refundable',TRUE,FALSE,' id="is_non_refundable" class="enable_setting is_non_refundable"'); ?> 
                                                <?php echo translate('Yes');?>
                                            <span></span>
                                        </label>

                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('is_non_refundable',FALSE,TRUE,' id="is_non_refundable" class="disable_setting is_refundable" '); ?> 
                                            <?php echo translate('No');?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group m-form__group pt-0 row">
                                <div class="col-lg-12">
                                    <label>
                                        <?php echo translate('Is this contribution considered Equity');?>?
                                    </label>

                                    <div class="m-radio-inline">
                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('is_equity',TRUE,FALSE,' id="is_equity" class="enable_setting enable_is_equity"'); ?> 
                                                <?php echo translate('Yes');?>
                                            <span></span>
                                        </label>

                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('is_equity',FALSE,TRUE,' id="is_equity" class="disable_setting disable_is_equity" '); ?> 
                                            <?php echo translate('No');?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group pt-0 row">
                                <div class="col-lg-12">
                                    <label>
                                        <?php echo translate('Enable contribution checkoff');?>?
                                    </label>

                                    <div class="m-radio-inline">
                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('enable_checkoff',TRUE,FALSE,' id="enable_checkoff" class="enable_checkoff enable_checkoff"'); ?> 
                                                <?php echo translate('Yes');?>
                                            <span></span>
                                        </label>

                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('enable_checkoff',FALSE,TRUE,' id="enable_checkoff" class="disable_checkoff disable_checkoff" '); ?> 
                                            <?php echo translate('No');?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group pt-0 row">
                                <div class="col-lg-12">
                                    <label>
                                        <?php echo translate('Do you wish to display this contribution in the member deposit statement');?>?
                                    </label>

                                    <div class="m-radio-inline">
                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('enable_deposit_statement_display',TRUE,FALSE,' id="enable_deposit_statement_display" class="enable_deposit_statement_display enable_deposit_statement_display"'); ?> 
                                                <?php echo translate('Yes');?>
                                            <span></span>
                                        </label>

                                        <label class="m-radio m-radio--solid m-radio--brand">
                                            <?php echo form_radio('enable_deposit_statement_display',FALSE,TRUE,' id="disable_deposit_statement_display" class="disable_deposit_statement_display disable_deposit_statement_display" '); ?> 
                                            <?php echo translate('No');?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancel_create_contribution_form" class="btn btn-secondary" data-dismiss="modal">
                    <?php echo translate('Close');?>
                </button>
                <button type="button" class="btn btn-primary" id='create_contribution_button'>
                    <?php echo translate('Save changes');?>
                </button>
            </div>
        </div>
    </div>
</div>

<a class="inline d-none" data-toggle="modal" data-target="#contributions_form" data-title="Add Contribution" data-id="add_contribution" id="add_contribution" data-backdrop="static" data-keyboard="false"><?php echo translate('Add Contribution');?></a>

<!-- <a class="inline d-none" data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account" data-backdrop="static" data-keyboard="false"><?php echo translate('Add Account');?></a> -->

<script>
    $(document).ready(function(){
        SnippetCreateContribution.init(false,true);
        $(document).on('click','.add_deposit_description',function(){
            $(this).parent().find('.deposit_description').toggle();
        });

        //add member modal close eventt
        $('#create_new_member_pop_up').on('hidden.bs.modal', function () {
            $("#create_new_member_pop_up input[type=text],#create_new_member_pop_up textarea").val("");
            $("#create_new_member_pop_up input[type=checkbox]").prop('checked',false);
        });

        //add contribution modal close eventt
        $('#contributions_form').on('hidden.bs.modal', function () {
            //clear input data
            $(':input','#contributions_form')
                .not(':button, :submit, :reset, textarea, :radio, #invoice_days, #week_day_monthly,#week_day_multiple,#start_month_multiple')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');

            //reset radio buttons
            $('#contributions_form .enable_setting').prop('checked',false).trigger('change');
            $('#contributions_form .disable_setting').prop('checked',true).trigger('change');
            //restore form to default
            $('#contributions_form #regular_invoicing_active_holder, #contributions_form #one_time_invoicing_active_holder, #contributions_form #one_time_invoicing_settings, #contributions_form #regular_invoicing_settings, #contributions_form #invoice_notifications, #contributions_form #fines, #contributions_form #contribution_member_list_settings, #contributions_form #contribution_member_list, #contributions_form #invoicing_setting, #contributions_form .data_error, #contributions_form .contribution_options, #contributions_form #twice_every_one_month').slideUp();
            console.log('add contribution modal close eventt');
        });

        //add account modal close eventt
        $('#create_new_account_pop_up').on('hidden.bs.modal', function () {
            $(':input','#create_new_account_pop_up')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number,#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number,#mobile_money_account_tab .mobile_money_account_number').slideUp();
        });

        $('#contributions_form').on('click','button.remove-line',function(event){
            $(this).parent().parent().remove();
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                number++;
            });
            TotalAmount.init();
        });

        $('.contribution-table').on('click','a.remove-line',function(event){
            if($('.contribution-table .count').length == 1){
                $('#contribution_payments_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> You cannot remove the last line</div>').slideDown();
            }else{
                $(this).parent().parent().remove();
                var number = 1;
                $('.count').each(function(){
                    $(this).text(number);
                    number++;
                });
                TotalAmount.init();
            }
        });

        $('#contribution_payments_form .add-new-line').on('click',function(){
            var html = $('#append-new-line tbody').html();
            html = html.replace_all('checker','');
            $('#append-place-holder').append(html);
            $('.tooltips').tooltip();
            $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                $(this).parent().find('.deposit_date').attr('name','deposit_dates['+(number-1)+']');
                $(this).parent().find('.member').attr('name','members['+(number-1)+']');
                $(this).parent().find('.contribution').attr('name','contributions['+(number-1)+']');
                $(this).parent().find('.deposit_description_input').attr('name','deposit_descriptions['+(number-1)+']');
                $(this).parent().find('.account').attr('name','accounts['+(number-1)+']');
                $(this).parent().find('.deposit_method').attr('name','deposit_methods['+(number-1)+']');
                $(this).parent().find('.amount').attr('name','amounts['+(number-1)+']');
                $(this).parent().find('.send_sms_notification').attr('name','send_sms_notifications['+(number-1)+']');
                $(this).parent().find('.send_email_notification').attr('name','send_email_notifications['+(number-1)+']');
                number++;
            });
            $('.contribution-table .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
            });
            FormInputMask.init();
        });

        $(document).on('change','.contribution-table select.member',function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('change','.contribution-table select.contribution',function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('change','.contribution-table select.account',function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('change','.contribution-table select.deposit_method',function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('blur','.contribution-table input.amount',function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
            }else{
                var amount = $(this).val();
                regex = /^[0-9.,\b]+$/;;
                if(regex.test(amount)){
                    if(amount < 1){
                        $(this).parent().addClass('has-danger');
                    }else{
                        $(this).parent().removeClass('has-danger');
                    }
                }else{ 
                    $(this).parent().addClass('has-danger');
                }
            }
        });

        $(document).on('changeDate','.contribution-table input.deposit_date',function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
            }else{
                $(this).parent().removeClass('has-danger');
            }
        });

        $('.date-picker').datepicker({ 
            dateFormat: 'dd-mm-yy' ,
            autoclose: true,
            changeYear: true,
            changeMonth: true,
            minDate:0,
            yearRange: "-100:+20",
        }).on('changeDate', function(e) {
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('change','.account',function(){
            if($(this).val()=='0'){
                $('#add_new_account').trigger('click');
                $(this).val("").trigger('change');
                $('#create_new_account_pop_up .select2-append').select2({
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });
            }
        });
        
        $(document).on('change','select[name="bank_id"]',function(){
            var empty_branch_list = $('#bank_branch_id').find('select').html();
            var branch_id = '';
            var bank_id = $(this).val();
            $('.bank_branch_id, .bank_account_number').slideUp();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            if(bank_id){
                $.post('<?php echo site_url('group/bank_accounts/ajax_get_bank_branches');?>',{'bank_id':bank_id,'branch_id':branch_id},
                function(data){
                    $('#bank_branch_id').html(data);
                    $('#create_new_account_pop_up .select2-append').select2({
                        width: "100%",
                        placeholder:{
                            id: '-1',
                            text: "--Select option--",
                        }, 
                    });
                    $('.bank_branch_id').slideDown();
                    mApp.unblock('.modal-body');
                });
            }else{
                $('#bank_branch_id').html('<select name="bank_id" class="form-control select2" id="bank_branch_id">'+empty_branch_list+'</select>');
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('change','select[name="bank_branch_id"]',function(){
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            var bank_branch_id = $(this).val();
            if(bank_branch_id){
                $('.bank_account_number').slideDown();
                mApp.unblock('.modal-body');
            }else{
                $('.bank_account_number').slideUp();
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('change','select[name="sacco_id"]',function(){
            var empty_branch_list =$('#sacco_branch_id').find('select').html();
            var branch_id = '';
            var sacco_id = $(this).val();
            $('.sacco_branch_id, .sacco_account_number').slideUp();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            if(sacco_id){
                $.post('<?php echo site_url('group/sacco_accounts/ajax_get_sacco_branches');?>',{'sacco_id':sacco_id,'branch_id':''},
                function(data){
                    $('#sacco_branch_id').html(data);
                    $('#create_new_account_pop_up .select2-append').select2({
                        width: "100%",
                        placeholder:{
                            id: '-1',
                            text: "--Select option--",
                        }, 
                    });
                    $('.sacco_branch_id').slideDown();
                    mApp.unblock('.modal-body');
                });
            }else{
                $('#sacco_branch_id').html('<select name="bank_id" class="form-control select2" id="bank_branch_id">'+empty_branch_list+'</select>');
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('change','select[name="sacco_branch_id"]',function(){
            var element = $(this);
            var sacco_branch_id = $(this).val();
            $('.sacco_account_number').slideUp();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            if(sacco_branch_id){
                $('.sacco_account_number').slideDown();
                mApp.unblock('.modal-body');
            }else{
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('change','select[name="mobile_money_provider_id"]',function(){
            var mobile_money_provider_id = $(this).val();
            $('.mobile_money_account_number').slideUp();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            if(mobile_money_provider_id){
                $('.mobile_money_account_number').slideDown();
                mApp.unblock('.modal-body');
            }else{
                mApp.unblock('.modal-body');
            }
        });

        $(document).on('submit','#bank_account_form',function(e){
            e.preventDefault();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('#bank_account_tab .error').html('').slideUp();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("ajax/bank_accounts/create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.account').each(function(){
                                $(this).append('<option value="bank-' + data.bank_account.id + '">'+data.bank_account.bank_details+' - ' + data.bank_account.account_name + ' ('+data.bank_account.account_number+')</option>').trigger('change');
                            });
                            $('.contribution-table select[name="accounts['+current_row+']"]').val("bank-"+data.bank_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new bank account, you can now select it in the accounts dropdown.','Bank account added successfully');
                        }else{
                            $('#bank_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#bank_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#bank_account_tab select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                        mApp.unblock('.modal-body');
                    }else{
                        $('#bank_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                        mApp.unblock('.modal-body');
                    }
                }
            });
        });

        $(document).on('submit','#sacco_account_form',function(e){
            e.preventDefault();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('#create_new_account_pop_up .error').html('');
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/sacco_accounts/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.account').each(function(){
                                $(this).append('<option value="sacco-' + data.sacco_account.id + '">'+data.sacco_account.sacco_details+' - ' + data.sacco_account.account_name + ' ('+data.sacco_account.account_number+')</option>').trigger('change');
                            });
                            $('.contribution-table select[name="accounts['+current_row+']"]').val("sacco-"+data.sacco_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new sacco account, you can now select it in the accounts dropdown.','Sacco account added successfully');
                        }else{
                            $('#sacco_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#sacco_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#sacco_account_tab select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                        
                    }else{
                        $('#sacco_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                    }
                    mApp.unblock('.modal-body');
                }
            });
        });

        $(document).on('submit','#mobile_money_account_form',function(e){
            e.preventDefault();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('#create_new_account_pop_up .error').html('').slideUp();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/mobile_money_accounts/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.account').each(function(){
                                $(this).append('<option value="mobile-' + data.mobile_money_account.id + '">'+data.mobile_money_account.mobile_money_provider_details+' - ' + data.mobile_money_account.account_name + ' ('+data.mobile_money_account.account_number+')</option>').trigger('change');
                            });
                            $('.contribution-table select[name="accounts['+current_row+']"]').val("mobile-"+data.mobile_money_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new mobile money account, you can now select it in the accounts dropdown.','Mobile money account added successfully');
                        }else{
                            $('#mobile_money_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#mobile_money_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#mobile_money_account_tab select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                    }else{
                        $('#mobile_money_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                    }
                    mApp.unblock('.modal-body');
                }
            });
        });

        $(document).on('submit','#petty_cash_account_form',function(e){
            e.preventDefault();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('#create_new_account_pop_up .error').html('').slideUp();
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/petty_cash_accounts/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.account').each(function(){
                                $(this).append('<option value="petty-' + data.petty_cash_account.id + '">' + data.petty_cash_account.account_name + '</option>').trigger('change');
                            });
                            $('.contribution-table select[name="accounts['+current_row+']"]').val("petty-"+data.petty_cash_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new petty cash account, you can now select it in the accounts dropdown.','Petty cash account added successfully');
                        }else{
                            $('#petty_cash_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    if(key == 'account_slug'){
                                        //skip
                                    }else{
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('#petty_cash_account_tab input[name="account_name"]').parent().addClass('has-danger').append(error_message);
                                    }
                                   
                                });
                            }
                        }
                    }else{
                        $('#petty_cash_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
                    }
                    mApp.unblock('.modal-body');
                }
            });
        });

        $(document).on('submit','#add_new_member_form',function(e){
            e.preventDefault();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            var form = $(this);
            RemoveDangerClass(form);
            $('#create_new_member_pop_up .error').html('').slideUp();
            $('#add_member_submit').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/members/ajax_add_member"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.member').each(function(){
                                $(this).append('<option value="' + data.member.id + '">' + data.member.first_name +' '+ data.member.last_name + '</option>').trigger('change');
                            });
                            $('.contribution-table select[name="members['+current_row+']"]').val(data.member.id).trigger('change');
                            $('#create_new_member_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new member to your group, you can now select him/her in the members dropdown.','Member added successfully');
                        }else{
                            $('#create_new_member_pop_up .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>');
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#create_new_member_pop_up input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#create_new_member_pop_up select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                    }else{
                        $('#create_new_member_pop_up .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> Could not process your request at the moment</div>').slideDown('slow');
                    }
                    $('#add_member_submit').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                    mApp.unblock('.modal-body', {});
                }
            });
        });

        $(document).on('click','#add_new_member',function(){
            $(".contribution-table .member").select2({
                language: 
                    {
                    noResults: function() {
                        return '<a class="inline" data-toggle="modal" data-content="#create_new_member_pop_up" data-title="Add Member" data-id="create_member" id="add_new_member"  >Add Member</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger("select2:close");
        });

        $(document).on('click','#add_contribution',function(){
            $(".contribution-table .contribution").select2({
                language: 
                    {
                     noResults: function() {
                        return '<a class="inline pop_up" data-toggle="modal" data-target="#contributions_form" id="add_contribution" href="#">Add Contribution</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger("select2:close");
        });

        $(document).on('click','#add_new_account',function(){
            $(".contribution-table .account").select2({
                language: 
                    {
                     noResults: function() {
                        return '<a class="inline " data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account"  >Add Account</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger("select2:close");
        });
        
        $('#contribution_payments_form').on('submit',function(e){
            e.preventDefault();
            var a = $('#contribution_payments_form');
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            $('#contribution_payments_form .submit_form_button').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            if(validate_contribution_payments_form()){
                var form = $('#contribution_payments_form');
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url("ajax/deposits/record_contribution_payments"); ?>',
                    data: form.serialize(),
                    success: function(data) {
                        var response = $.parseJSON(data);
                        if(response.status == 1){
                            toastr['success'](response.message);
                            window.location.href = response.refer;
                        }else{
                            $('#contribution_payments_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong>'+response.message+'</div>').slideDown();
                        }
                        mApp.unblock('#contribution_payments_form');
                        $('#contribution_payments_form .submit_form_button').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                    },error: function(){
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                        }, 2e3)
                    }
                });

            }else{
                $('#contribution_payments_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are errors on the form, please review the highlighted fields and try submitting again.</div>').slideDown();
                mApp.unblock('#contribution_payments_form');
                $('#contribution_payments_form .submit_form_button').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
            }

        });

        $(document).on('change','.contribution',function(){
            if($(this).val()=='0'){
                $('#add_contribution').trigger('click');
                $(this).val("").trigger('change');
                $('#contributions_form .select2-append').select2({
                    width: "100%",
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });
            }
        });

        $(document).on('change','.member',function(){
            if($(this).val()=='0'){
                $('#add_new_member').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        var current_row = 0;
        $(document).on('select2:open','.member', function(e) {
            // do something
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('select2:open','.contribution', function(e) {
            // do something
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('select2:open','.account', function(e) {
            // do something
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('change','#type',function(){
            if($(this).val()==1){
                $('#contributions_form #regular_invoicing_active_holder,#contributions_form #invoicing_setting, #contributions_form .contribution_options').slideDown();
                $('#contributions_form #one_time_invoicing_active_holder,#contributions_form #sms_template,#contributions_form #one_time_invoicing_settings,#contributions_form #regular_invoicing_settings,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form  #contribution_member_list_settings, #contributions_form #contribution_member_list,#contributions_form #invoice_notifications_settings').slideUp();
                // $('#contributions_form #one_time_invoicing_active').parent().removeClass('checked');
                $('#contributions_form #one_time_invoicing_active,#contributions_form #enable_contribution_member_list').prop('checked',false);
                $('#contributions_form #disable_contribution_member_list,#contributions_form #regular_invoicing_inactive, #contributions_form #invoice_notifications_inactive').prop('checked',true);  
            }else if($(this).val()==2){
                $('#contributions_form  #one_time_invoicing_active_holder, #contributions_form .contribution_options').slideDown();
                $('#contributions_form  #regular_invoicing_active_holder,#contributions_form #sms_template,#contributions_form #one_time_invoicing_settings,#contributions_form #regular_invoicing_settings,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#submit_form #contribution_member_list_settings, #contributions_form #contribution_member_list, #contributions_form #invoice_notifications_settings').slideUp(); 
                // $('#contributions_form #regular_invoicing_active').parent().removeClass('checked');
                $('#contributions_form #disable_contribution_member_list,#contributions_form #one_time_invoicing_inactive, #contributions_form #invoice_notifications_inactive').prop('checked',true);         
                // $('#contributions_form #regular_invoicing_active,#contributions_form #enable_contribution_member_list').prop('checked',false);
                $('#contributions_form #disable_contribution_member_list').prop('checked',true);  
            }else{
                if($(this).val() == 3){
                    $('#contributions_form .contribution_options').slideDown();
                }else{
                    $('#contributions_form .contribution_options').slideUp();
                }
                $('#contributions_form #regular_invoicing_active_holder,#contributions_form #sms_template,#contributions_form #one_time_invoicing_active_holder,#contributions_form #one_time_invoicing_settings,#contributions_form #regular_invoicing_settings,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings, #contributions_form #contribution_member_list,#contributions_form #invoicing_setting,#contributions_form #invoice_notifications_settings').slideUp();
                // $('#contributions_form #regular_invoicing_active,#contributions_form #one_time_invoicing_active').parent().removeClass('checked'); 
                $('#contributions_form #regular_invoicing_active,#contributions_form #one_time_invoicing_active,#contributions_form #enable_contribution_member_list').prop('checked',false);
                $('#contributions_form #disable_contribution_member_list,#contributions_form #one_time_invoicing_inactive, #contributions_form #invoice_notifications_inactive').prop('checked',true);
            }
        });
            
        $(document).on('change','input[name="regular_invoicing_active"]',function(){
            if($(this).val()){
                $('#contributions_form #regular_invoicing_settings,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings').slideDown();
            }else{
                $('#contributions_form #regular_invoicing_settings,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings, #contributions_form #invoice_notifications_settings').slideUp();
            }
        });

        $(document).on('change','input[name="one_time_invoicing_active"]',function(){
            if($(this).val()){
                $('#contributions_form #one_time_invoicing_settings,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings').slideDown();
            }else{
                $('#contributions_form #one_time_invoicing_settings,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings, #contributions_form #invoice_notifications_settings').slideUp();
            }
        });

        $(document).on('change','#month_day_monthly',function(){
            $('#contributions_form .select2-append').select2({
                width:'100%',
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
            });
            if($(this).val()>4){
                $('#contributions_form #week_day_monthly').val(0).trigger("change").attr('disabled','disabled');
            }else{
                $('#contributions_form #week_day_monthly').removeAttr('disabled','disabled');
            }
        });

        $(document).on('change','#month_day_multiple',function(){
            if($(this).val()>4){
                $('#contributions_form #week_day_multiple').val(0).trigger("change").attr('disabled','disabled');
            }else{
                $('#contributions_form #week_day_multiple').removeAttr('disabled','disabled');
            }
        });

        $(document).on('change','#contribution_frequency',function(){
            if($(this).val()==1){
                //once a month
                $('#contributions_form #once_a_month').slideDown();
                $('#contributions_form #invoice_days,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings').slideDown();
                $('#contributions_form #once_a_week,#contributions_form #once_every_two_weeks,#contributions_form #once_every_multiple_months, #contributions_form #twice_every_one_month').slideUp();
            }else if($(this).val()==6){
                //once a week
                $('#contributions_form #invoice_days,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings').slideDown();
                $('#contributions_form #once_a_week').slideDown();
                $('#contributions_form #once_every_two_weeks,#contributions_form #once_a_month,#contributions_form #once_every_multiple_months, #contributions_form #twice_every_one_month').slideUp();
            }else if($(this).val()==7){
                //once every two weeks
                $('#contributions_form #invoice_days,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings').slideDown();
                $('#contributions_form #once_every_two_weeks').slideDown();
                $('#contributions_form #once_every_multiple_months,#contributions_form #once_a_week,#contributions_form #once_a_month, #contributions_form #twice_every_one_month').slideUp();
            }else if($(this).val()==2||$(this).val()==3||$(this).val()==4||$(this).val()==5){
                //once every two months, once every three months,once every six months, once a year
                $('#contributions_form #invoice_days,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings').slideDown();
                $('#contributions_form #once_every_multiple_months').slideDown();
                $('#contributions_form #once_a_month,#contributions_form #once_every_two_weeks,#contributions_form #once_a_week, #contributions_form #twice_every_one_month').slideUp();
            }else if($(this).val()==8){
                //$('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideUp();
                //hide all
                $('#contributions_form select[name=invoice_days]').val(1).trigger('change');
                $('#contributions_form #invoice_days').slideDown();
                $('#contributions_form #once_a_month,#contributions_form #once_every_two_weeks,#contributions_form #once_a_week,#contributions_form #once_every_multiple_months, #contributions_form #twice_every_one_month').slideUp();
            }else if($(this).val()==9){
                $('#contributions_form select[name=invoice_days]').val(1).trigger('change');
                $('#contributions_form #invoice_days,#contributions_form #twice_every_one_month').slideDown();
                $('#contributions_form #once_a_month,#contributions_form #once_every_two_weeks,#contributions_form #once_a_week,#contributions_form #once_every_multiple_months').slideUp();
            }else{
                //hide all
                $('#contributions_form #invoice_days,#contributions_form #invoice_notifications,#contributions_form #fines,#contributions_form #advanced_settings,#contributions_form #contribution_member_list_settings, #contributions_form #twice_every_one_month,#contributions_form #once_a_month,#contributions_form #once_every_two_weeks,#contributions_form #once_a_week,#contributions_form #once_every_multiple_months').slideUp();
            }
        });

        $(document).on('change','input[name="invoice_notifications_active"]',function(){
            if($(this).val()){
                $('#contributions_form #invoice_notifications_settings').slideDown();
            }else{
                $('#contributions_form #invoice_notifications_settings').slideUp();
            }
        });

        $(document).on('change','#enable_contribution_summary_display_configuration',function(){
            if($(this).val()){
                $('#contributions_form #contribution_summary_display_configuration_settings').slideDown();
            }else{
                $('#contributions_form #contribution_summary_display_configuration_settings').slideUp();
            }
        });

        $(document).on('change','input[name="enable_contribution_member_list"]',function(){
            if($(this).val()){
                $('#contributions_form #contribution_member_list').slideDown();
            }else{
                $('#contributions_form #contribution_member_list').slideUp();
            }
        });

        $(document).on('change','input[name="enable_fines"]',function(){
            if($(this).val()){
                $('#contributions_form #fine_settings').slideDown();
            }else{
                $('#contributions_form #fine_settings').slideUp();
            }
        });

        $(document).on('change','#fine_notifications_enabled',function(){
            if($(this).val()){
                $('#contributions_form .fine_notifications_settings').slideDown()
            }else{
                $('#contributions_form .fine_notifications_settings').slideUp()
            }
        });

        $(document).on('change','.fine_types',function(){
            var fine_setting_row_element = $(this).parent().parent().parent().parent().parent();
            fine_setting_row_element.find('.fixed_fine_settings,.percentage_fine_settings,.fine_limit').slideUp('fast');
            if($(this).val()==1){
                fine_setting_row_element.find('.fixed_fine_settings').slideDown();
            }else if($(this).val()==2){
                fine_setting_row_element.find('.percentage_fine_settings').slideDown();
            }
        });

        $(document).on('change','.fixed_fine_chargeable_ons',function(){
            var fine_setting_row_element = $(this).parent().parent().parent().parent();
            if($(this).val()=='first_day_of_the_month'||$(this).val()=='last_day_of_the_month'){
                fine_setting_row_element.find('.fixed_fine_frequencies').val(3).trigger('change');
            }else{
                fine_setting_row_element.find('.fixed_fine_frequencies').removeAttr('disabled','disabled');
            }
        });

        $(document).on('change','.percentage_fine_chargeable_ons',function(){ 
            var fine_setting_row_element = $(this).parent().parent().parent().parent();
            if($(this).val()=='first_day_of_the_month'||$(this).val()=='last_day_of_the_month'){
                fine_setting_row_element.find('.percentage_fine_frequencies').val(3).trigger('change');
            }else{
                fine_setting_row_element.find('.percentage_fine_frequencies').removeAttr('disabled','disabled');
            }
        });

        $(document).on('change','.fixed_fine_modes',function(){ 
            var fine_setting_row_element = $(this).parent().parent().parent().parent().parent();
            if($(this).val()==1){
                fine_setting_row_element.find('.fine_limit').slideDown();
            }else{
                fine_setting_row_element.find('.fine_limit').slideUp();
            }
        });

        $(document).on('change','.percentage_fine_modes',function(){ 
            var fine_setting_row_element = $(this).parent().parent().parent().parent().parent();
            if($(this).val()==1){
                fine_setting_row_element.find('.fine_limit').slideDown();
            }else{
                fine_setting_row_element.find('.fine_limit').slideUp();
            }
        });

    });
    
    $(window).on('load',function() {
        $('.contribution-table .member').select2({
            language: 
                {
                 noResults: function() {
                    return '<a class="inline" data-toggle="modal" data-target="#create_new_member_pop_up" data-title="Add Member" data-id="create_member" id="add_new_member"  >Add Member</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $(".contribution-table .contribution").select2({
            language: 
                {
                 noResults: function() {
                    return '<a class="inline pop_up" data-row="" data-toggle="modal" data-target="#contributions_form" data-title="Add Contribution" data-id="add_contribution" id="add_contribution" href="#">Add Contribution</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $(".contribution-table .account").select2({
            language: 
                {
                 noResults: function() {
                    return '<a class="inline " data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account"  >Add Account</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
        
        $(".contribution-table .deposit_method").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
    });

    function calculated_width(e){
        tdWidth = ($(e).width())*1.4
        return "";
    }

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    function handle_tab_switch(tab){
        //check tab
        //clear values on other tabs
        //slide up on other tabs
        $('#create_new_account_pop_up .error').html('').slideUp();
        if(tab == 'bank_account'){
            $(':input','#mobile_money_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#mobile_money_account_tab .mobile_money_account_number').slideUp();
            $(':input','#sacco_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }else if(tab == 'sacco_account'){
            $(':input','#mobile_money_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#mobile_money_account_tab .mobile_money_account_number').slideUp();
            $(':input','#bank_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }else if(tab == 'mobile_money_account'){
            $(':input','#sacco_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number').slideUp();
            $(':input','#bank_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }else if(tab == 'petty_cash_account'){
            $(':input','#sacco_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number').slideUp();
            $(':input','#bank_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number').slideUp();
            $(':input','#mobile_money_account_tab')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#mobile_money_account_tab .mobile_money_account_number').slideUp();
            $(':input','#petty_cash_account_tab').val('');
        }
    }

    String.prototype.replace_all = function(search,replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };

    function validate_contribution_payments_form(){
        var entries_are_valid = true;
        $('.contribution-table select.member').each(function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $('.contribution-table select.contribution').each(function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $('.contribution-table select.deposit_method').each(function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $('.contribution-table input.amount').each(function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                var amount = $(this).val();
                regex = /^[0-9.,\b]+$/;;
                if(regex.test(amount)){
                    if(amount < 1){
                        $(this).parent().addClass('has-danger');
                        entries_are_valid = false;
                    }else{
                        $(this).parent().removeClass('has-danger');
                    }
                }else{ 
                    $(this).parent().addClass('has-danger');
                    entries_are_valid = false;
                }
            }
        });

        $('.contribution-table input.deposit_date').each(function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                $(this).parent().removeClass('has-danger');
            }
        });

        $('.contribution-table select.account').each(function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                $(this).parent().removeClass('has-danger');
            }
        });

        if(entries_are_valid){
            return true;
        }else{
            return false;
        }
    }
</script>

<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit income_form m-form--state" role="form"'); ?>
    <span class="error"></span>
    <div class="table-responsive">
        <table class="table table-condenseddered table-hover table-checkable table-multiple-items multiple_payment_entries">
            <thead>
                <tr> 
                    <th width="1%">
                        #
                    </th>
                    <th width="12%">
                        <?php echo translate('Date');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="17%">
                        <?php echo translate('Depositor');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="18%">
                        <?php echo translate('Payment For');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="18%">
                        <?php echo translate('Account');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="14%">
                        <?php echo translate('Channel');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="13%">
                        <?php echo translate('Amount');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="4%">
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
                        <?php echo form_input('deposit_dates[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input deposit_date date-picker" readonly="readonly" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d"  data-date-start-date="-20y" autocomplete="off" ');?>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('depositors[0]',array(''=>translate('Select depositor'))+$depositor_options+array('0'=>"Add Depositor"),'',' class="form-control select2 depositor" ');?>
                         </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('income_categories[0]',array(''=>translate('Select income category'))+$income_category_options+array('0'=>"Add Income Category"),'',' class="form-control select2 income_category" ');?>
                        </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('accounts[0]',array(''=>translate('Select account'))+$account_options,'',' class="form-control m-input m-select2 account" ');?>
                        </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('deposit_methods[0]',array(''=>translate('Select method'))+$deposit_method_options,'',' class="form-control m-input m-select2 deposit_method" ');?>
                        </span>
                    </td>
                    <td>
                        <?php echo form_input('amounts[0]','',' class="form-control input-sm amount currency text-right" ');?>
                    </td>
                    <td class="text-right">
                        <a href='javascript:;' class="remove-line">
                            <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-right" colspan=6>
                        <?php echo translate('Totals');?>
                    </td>
                    <td class="text-right total-amount"><?php echo number_to_currency();?></td>
                    <td colspan="3"></td>
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
                       <?php echo translate('Record Income');?>                              
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

<div class="d-none" id='append-new-line'>
    <table>
        <tbody>
            <tr>
                <th scope="row" class="count">
                    1
                </th>
                <td>
                    <?php echo form_input('deposit_dates[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input deposit_date date-picker" readonly="readonly" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" data-date-start-date="-20y" autocomplete="off" ');?>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                         <?php echo form_dropdown('depositors[0]',array(''=>translate('Select depositor'))+$depositor_options+array('0'=>"Add Depositor"),'',' class="form-control select2 depositor" ');?>
                    </span>
                </td>
                <td>
                    <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('income_categories[0]',array(''=>translate('Select income category'))+$income_category_options+array('0'=>"Add Income Category"),'',' class="form-control select2 income_category" ');?>
                        </span>
                    </div>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('accounts[0]',array(''=>translate('Select account'))+$account_options,'',' class="form-control m-input m-select2-append account" ');?>
                    </span>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('deposit_methods[0]',array(''=>translate('Select deposit method'))+$deposit_method_options,'',' class="form-control m-input m-select2-append deposit_method" ');?>
                    </span>
                </td>
                <td>
                    <?php echo form_input('amounts[0]','',' class="form-control input-sm amount currency text-right" ');?>
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

<div class="modal fade" id="create_new_depositor_pop_up" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Add New Depositor');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open($this->uri->uri_string(),'class=" add_depositor_form m-form m-form--state" id="add_depositor_form" role="form"'); ?>
                    <span class="error"></span>
                    <div class="m-form__section m-form__section--first">
                        <div class="form-group m-form__group">
                            <label>
                                <?php echo translate('Name');?>
                                <span class='required'>*</span>
                            </label>
                            <?php echo form_input('name','',' id="name" class="form-control" placeholder="Name"');?>
                        </div>

                        <div class="form-group m-form__group row">
                            <div class="col-lg-6">
                                <label>
                                    <?php echo translate('Phone');?>
                                </label>
                                <?php echo form_input('phone','','class="form-control" placeholder="Phone Number" id="phone"');?>
                            </div>
                            <div class="col-lg-6">
                                <label>
                                    <?php echo translate('Email');?>
                                </label>
                                <?php echo form_input('email','','class="form-control" placeholder="Email Address" id="email"');?>
                            </div>
                           
                        </div>
                    
                        <div class="form-group m-form__group">
                            <label>
                                <?php echo translate('Description');?>
                            </label>
                            <?php echo form_textarea('description','',' id="description" class="form-control" placeholder="Description"');?>
                        </div>
                    </div>
                    <div class="m-form__actions m-form__actions m--align-right p-0">
                        <button class="btn btn-secondary" data-dismiss="modal">
                            <?php echo translate('Cancel');?>
                        </button>
                        <button type="submit" class="btn btn-primary" id="add_new_depositor_submit">
                            <?php echo translate('Submit');?>
                        </button>
                    </div>
                <?php echo form_close(); ?>

            </div>
        </div>
    </div>
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

<div class="modal fade" id="create_new_income_category_pop_up" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Add Income Category');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open($this->uri->uri_string(),'class=" add_income_category_form form_submit m-form m-form--state" id="add_income_category_form" role="form"'); ?>
                    <span class="error"></span>
                    <div class="m-form__section m-form__section--first">
                        <div class="form-group m-form__group row">
                            <div class="col-lg-12">
                                <label>
                                    <?php echo translate('Name');?>
                                    <span class='required'>*</span>
                                </label>
                                <?php echo form_input('name','',' id="name" class="form-control slug_parent" placeholder="Name"');?>
                                <?php echo form_hidden('slug','','');?>
                            </div>
                        </div>

                        <div class="form-group m-form__group">
                            <label>
                                <?php echo translate('Description');?>
                            </label>
                            <?php echo form_textarea('description','',' id="description" class="form-control" placeholder="Description"');?>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 m--align-right">
                                <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                    <?php echo translate('Cancel');?>
                                </button>
                                <button type="submit" class="btn btn-primary" id="add_income_category_submit">
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

<a class="d-none inline" data-toggle="modal" data-target="#create_new_depositor_pop_up" data-title="Add Depositor" data-id="create_depositor" id="add_new_depositor" data-backdrop="static" data-keyboard="false">
    <?php echo translate('Add Depositor');?>
</a>

<a class="inline d-none" data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account" data-backdrop="static" data-keyboard="false">
    <?php echo translate('Add Account');?>
</a>

<a class="d-none inline" data-toggle="modal" data-target="#create_new_income_category_pop_up" data-title="Add Income Category" data-id="create_income_category" id="add_new_income_category"  data-backdrop="static" data-keyboard="false"  >
    <?php echo translate('Add Income Category');?>
</a>


<script>
    $(document).ready(function(){
        //add account modal close eventt
        $('#create_new_account_pop_up').on('hidden.bs.modal', function () {
            $(':input','#create_new_account_pop_up')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number,#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number,#mobile_money_account_tab .mobile_money_account_number,#create_new_account_pop_up .data_error').slideUp();
            console.log('add account modal close event');
        });

        //add member modal close eventt
        $('#create_new_member_pop_up').on('hidden.bs.modal', function () {
            $("#create_new_member_pop_up input[type=text],#create_new_member_pop_up textarea").val("");
            $("#create_new_member_pop_up input[type=checkbox]").prop('checked',false);
            $("#create_new_member_pop_up .data_error").html('').slideUp();
        });

        //create_new_depositor_pop_up modal close eventt
        $('#create_new_depositor_pop_up').on('hidden.bs.modal', function () {
            $("#create_new_depositor_pop_up input[type=text],#create_new_depositor_pop_up textarea").val("");
            $("#create_new_depositor_pop_up .data_error").html('').slideUp();
        });

        //create_new_depositor_pop_up modal close eventt
        $('#create_new_income_category_pop_up').on('hidden.bs.modal', function () {
            $("#create_new_income_category_pop_up input[type=text],#create_new_income_category_pop_up textarea").val("");
            $("#create_new_income_category_pop_up .data_error").html('').slideUp();
        });

        $('#add-new-line').on('click',function(){
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
                $(this).parent().find('.depositor').attr('name','depositors['+(number-1)+']');
                $(this).parent().find('.income_category').attr('name','income_categories['+(number-1)+']');
                $(this).parent().find('.account').attr('name','accounts['+(number-1)+']');
                $(this).parent().find('.deposit_method').attr('name','deposit_methods['+(number-1)+']');
                $(this).parent().find('.amount').attr('name','amounts['+(number-1)+']');
                $(this).parent().find('.send_sms_notification').attr('name','send_sms_notifications['+(number-1)+']');
                $(this).parent().find('.send_email_notification').attr('name','send_email_notifications['+(number-1)+']');
                number++;
            });

            $('.table-multiple-items .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
            });

            $('.table-multiple-items .depositor').select2({
                language: 
                    {
                    noResults: function() {
                        return '<a class="inline" data-toggle="modal" data-content="#create_new_depositor_pop_up" data-title="Add Depositor" data-id="create_depositor" id="add_new_depositor"  >Add Depositor </a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            $('.table-multiple-items .income_category').select2({
                language: 
                    {
                    noResults: function() {
                        return '<a class="inline" data-toggle="modal" data-content="#create_new_income_category_pop_up" data-title="Add Income Category" data-id="create_income_category" id="add_new_income_category"  > Add Income Category </a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });

            $(".table-multiple-items .account").select2({
                
                language: 
                    {
                     noResults: function() {
                        return '<a class="inline " data-toggle="modal" data-content="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account"  >Add Account</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
            FormInputMask.init();
        });

        $('.table-multiple-items').on('click','a.remove-line',function(event){
            $(this).parent().parent().remove();
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                number++;
            });
            TotalAmount.init();
        });

        $(document).on('changeDate','.table-multiple-items input.deposit_date',function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
            }else{
                $(this).parent().removeClass('has-danger');
            }
        });

        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true}).on('changeDate', function(e) {
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('change','.table-multiple-items select.depositor',function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('change','.table-multiple-items select.income_category',function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('change','.table-multiple-items select.account',function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('change','.table-multiple-items select.deposit_method',function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $(document).on('blur','.table-multiple-items input.amount',function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
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
                }
            }
        });

        $(document).on('click','.account',function(){
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
                            $('..table-multiple-items select[name="accounts['+current_row+']"]').val("bank-"+data.bank_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new bank account, you can now select it in the accounts dropdown.','Bank account added successfully');
                        }else{
                            $('#bank_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#bank_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#bank_account_tab select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                        
                    }else{
                        $('#bank_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown('slow');
                    }
                    mApp.unblock('.modal-body');
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
            $('#create_new_account_pop_up .error').html('').slideUp();
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
                            $('.table-multiple-items select[name="accounts['+current_row+']"]').val("sacco-"+data.sacco_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new sacco account, you can now select it in the accounts dropdown.','Sacco account added successfully');
                        }else{
                            $('#sacco_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#sacco_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#sacco_account_tab select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                        
                    }else{
                        $('#sacco_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown('slow');
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
                            $('.table-multiple-items select[name="accounts['+current_row+']"]').val("mobile-"+data.mobile_money_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new mobile money account, you can now select it in the accounts dropdown.','Mobile money account added successfully');
                        }else{
                            $('#mobile_money_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#mobile_money_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#mobile_money_account_tab select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                            }
                        }
                    }else{
                        $('#mobile_money_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown('slow');
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
                            $('.table-multiple-items select[name="accounts['+current_row+']"]').val("petty-"+data.petty_cash_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new petty cash account, you can now select it in the accounts dropdown.','Petty cash account added successfully');
                        }else{
                            $('#petty_cash_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
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
                        $('#petty_cash_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown('slow');
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
                            $('.table-multiple-items select[name="members['+current_row+']"]').val(data.member.id).trigger('change');
                            $('#create_new_member_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new member to your group, you can now select him/her in the members dropdown.','Member added successfully');
                        }else{
                            $('#create_new_member_pop_up .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
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

        $(document).on('submit','#add_income_category_form',function(e){
            e.preventDefault();
            var form = $(this);
            mApp.block('#create_new_income_category_pop_up .modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            $("#add_income_category_submit .error").html('').slideUp();
            $('#add_income_category_submit').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/income_categories/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.income_category').each(function(){
                                $(this).append('<option value="' + data.income_category.id + '">' + data.income_category.name +' </option>').trigger('change');
                            });
                            $('.table-multiple-items  select[name="income_categories['+current_row+']"]').val(data.income_category.id).trigger('change');
                            $('#create_new_income_category_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new income category to your group, you can now select it in the income category dropdown.','Income category added successfully');
                        }else{
                            $('#create_new_income_category_pop_up .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    if(data.validation_errors.slug && data.validation_errors.name){
                                        if(key == 'slug'){
                                            //ignore
                                        }else{
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('#create_new_income_category_pop_up input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('#create_new_income_category_pop_up select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        }
                                    }else{
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('#create_new_income_category_pop_up input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('#create_new_income_category_pop_up select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    }
                                });
                            }
                        }
                    }else{
                        $('#create_new_income_category_pop_up .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem submitting the data. Please try again.</div>').slideDown('slow');
                        $(".modal-body").animate({ scrollTop: 0 }, 600);;
                    }
                    mApp.unblock('.modal-body');
                    $('#add_income_category_submit').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                }
            });
        });

        $(document).on('submit','#add_depositor_form',function(e){
            e.preventDefault();
            var form = $(this);
            mApp.block('#create_new_depositor_pop_up .modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            $("#create_new_depositor_pop_up .error").html('').slideUp();
            $('#add_new_depositor_submit').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/depositors/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.depositor').each(function(){
                                $(this).append('<option value="' + data.depositor.id + '">' + data.depositor.name +' </option>').trigger('change');
                            });
                            $('.table-multiple-items  select[name="depositors['+current_row+']"]').val(data.depositor.id).trigger('change');
                            $('#create_new_depositor_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new depositor to your group, you can now select him/her from the group depositors dropdown.','Income category added successfully');
                        }else{
                            $('#create_new_depositor_pop_up .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    if(key == 'slug'){
                                        //ignore
                                    }else{
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('#create_new_depositor_pop_up input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('#create_new_depositor_pop_up select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    }
                                });
                            }
                        }
                    }else{
                        $('#create_new_depositor_pop_up .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem submitting the data. Please try again.</div>').slideDown('slow');
                        $(".modal-body").animate({ scrollTop: 0 }, 600);;
                    }
                    mApp.unblock('.modal-body');
                    $('#add_new_depositor_submit').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                }
            });
        });

        $(document).on('click','#add_new_depositor',function(){
            $(".table-multiple-items .depositor").select2({
                
                    language: 
                        {
                        noResults: function() {
                            return '<a class="inline" data-toggle="modal" data-content="#create_new_depositor_pop_up" data-title="Add Depositor" data-id="create_depositor" id="add_new_depositor"  >Add Depositor </a>';
                        }
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }
            }).trigger("select2:close");
        });

        $(document).on('click','#add_new_income_category',function(){
            $(".table-multiple-items .income_category").select2({
                
                    language: 
                        {
                        noResults: function() {
                            return '<a class="inline" data-toggle="modal" data-content="#create_new_income_category_pop_up" data-title="Add Income Category" data-id="create_income_category" id="add_new_income_category"  > Add Income Category </a>';
                        }
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }
            }).trigger("select2:close");
        });

        $(document).on('click','#add_new_account',function(){
            $(".table-multiple-items .account").select2({
                
                language: 
                    {
                     noResults: function() {
                        return '<a class="inline " data-toggle="modal" data-content="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account"  >Add Account</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger("select2:close");
            $('#create_new_account_pop_up .select2-append').select2({
                
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        });

        $(document).on('change','.depositor',function(){
            if($(this).val()=='0'){
                $('#add_new_depositor').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.income_category',function(){
            if($(this).val()=='0'){
                $('#add_new_income_category').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.account',function(){
            if($(this).val()=='0'){
                $('#add_new_account').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $('.income_form').on('submit',function(e){
            e.preventDefault();
            mApp.block('.income_form', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            $('.income_form .submit_form_button').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            if(validate_income_payments_form()){
                var form = $('.income_form');
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url("ajax/deposits/record_income"); ?>',
                    data: form.serialize(),
                    success: function(data) {
                        var response = $.parseJSON(data);
                        if(response.status == 1){
                            toastr['success'](response.message);
                            window.location.href = response.refer;
                        }else{
                            $('.income_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong>'+response.message+'</div>');
                        }
                        mApp.unblock('.income_form');
                        $('.income_form .submit_form_button').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                    }
                });

            }else{
                $('.income_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are errors on the form, please review the highlighted fields and try submitting again.</div>');
                mApp.unblock('.income_form');
                $('.income_form .submit_form_button').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
            }

        });

        var current_row = 0;
        $(document).on('select2:open','.member', function(e) {
            // do something
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('select2:open','.income_category', function(e) {
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
    });
    
    $(window).on('load',function() {
        $(".table-multiple-items .depositor").select2({
            language: 
                {
                noResults: function() {
                    return '<a class="inline" data-toggle="modal" data-content="#create_new_depositor_pop_up" data-title="Add Depositor" data-id="create_depositor" id="add_new_depositor"  >Add Depositor </a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $(".table-multiple-items .income_category").select2({
                language: 
                    {
                    noResults: function() {
                        return '<a class="inline" data-toggle="modal" data-content="#create_new_income_category_pop_up" data-title="Add Income Category" data-id="create_income_category" id="add_new_income_category"  > Add Income Category </a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
        });

        $(".table-multiple-items .account").select2({
            language: 
                {
                 noResults: function() {
                    return '<a class="inline " data-toggle="modal" data-content="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account"  >Add Account</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $(".table-multiple-items .deposit_method").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        
    });
    
    function handle_tab_switch(tab){
        //check tab
        //clear values on other tabs
        //slide up on other tabs
        console.log(tab);
        $('#create_new_account_pop_up .data_error').html('').slideUp();
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

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    String.prototype.replace_all = function(search,replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };
    
    function validate_income_payments_form(){
        var entries_are_valid = true;
        $('.table-multiple-items input.deposit_date').each(function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                $(this).parent().removeClass('has-danger');
            }
        });

        $('.table-multiple-items select.depositor').each(function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $('.table-multiple-items select.income_category').each(function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $('.table-multiple-items select.deposit_method').each(function(){
            if($(this).val()==''){
                $(this).parent().parent().addClass('has-danger');
                entries_are_valid = false;
            }else{
                $(this).parent().parent().removeClass('has-danger');
            }
        });

        $('.table-multiple-items input.amount').each(function(){
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

        $('.table-multiple-items select.account').each(function(){
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

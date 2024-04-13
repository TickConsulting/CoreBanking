<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state record_asset_purchase" id="record_asset_purchase"'); ?>
    <div class="table-responsive">
        <table class="table table-condensed table-multiple-items multiple_payment_entries">
            <thead>
                <tr>
                    <th width="1%">
                        #
                    </th>
                    <th width="12%">
                        <?php echo translate('Date');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="18%">
                        <?php echo translate('Asset');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="18%">
                        <?php echo translate('Account');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="16%">
                        <?php echo translate('Payment method');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="16%">
                        <?php echo translate('Description');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="16%">
                        <?php echo translate('Amount');?>
                         (<?php echo $this->group_currency; ?>) 
                        <span class='required'>*</span>
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
                        <?php echo form_input('payment_dates[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input payment_dates date-picker input-sm" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off"');?>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('assets[0]',array(''=>'Select asset')+translate($asset_options)+array('0'=>"Add Asset"),'',' class="form-control input-sm m-select2 assets" ');?>
                        </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('accounts[0]',array(''=>'Select account')+translate($account_options),'',' class="form-control input-sm m-input m-select2 accounts" ');?>
                        </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('payment_methods[0]',array(''=>'Select payment method')+translate($withdrawal_method_options),'',' class="form-control input-sm m-input m-select2 payment_methods"');?>
                        </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php 
                                $textarea = array(
                                    'name'=>'payment_descriptions[0]',
                                    'id'=>'',
                                    'value'=> '',
                                    'cols'=>25,
                                    'rows'=>5,
                                    'maxlength'=>'',
                                    'class'=>'form-control payment_descriptions',
                                    'placeholder'=>''
                                ); 
                                echo form_textarea($textarea);
                            ?>
                        </span>
                    </td>
                    <td>
                        <?php echo form_input('amounts[0]',0,' class="form-control input-sm m-input amount currency text-right amounts" ');?>
                    </td>
                    <td>
                        <a href='javascript:;' class="remove-line">
                            <i class="text-danger la la-trash"></i>
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
                <i class="la la-plus"></i><?php echo translate('Add new payment line');?>
            </a>
        </div>
    </div>
    <div class="m-form__actions m-form__actions p-0 pt-5 m--margin-top-30">                            
        <div class="row">
            <div class="col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button name="submit" value="1" class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm  submit_form_button" id="submit_form_button" type="button">
                        <?php echo translate('Record Asset Purchase Payments');?>                            
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button"  >
                        Cancel                              
                    </button>
                </span>
            </div>
        </div>
    </div>

<?php echo form_close() ?>
<div id='append-new-line' class="d-none">
    <table>
        <tbody>
            <tr>
                <th scope="row" class="count">
                    1
                </th>
                <td>
                    <?php echo form_input('payment_dates[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input payment_dates date-picker input-sm" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off"');?>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('assets[0]',array(''=>'Select asset')+translate($asset_options)+array('0'=>"Add Asset"),'',' class="form-control m-select2-append assets" ');?>
                    </span>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('accounts[0]',array(''=>'Select account')+translate($account_options),'',' class="form-control m-select2-append accounts" ');?>
                    </span>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('payment_methods[0]',array(''=>'Select payment method')+translate($withdrawal_method_options),'',' class="form-control m-select2-append payment_methods"');?>
                    </span>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php 
                            $textarea = array(
                                'name'=>'payment_descriptions[0]',
                                'id'=>'',
                                'value'=> '',
                                'cols'=>25,
                                'rows'=>5,
                                'maxlength'=>'',
                                'class'=>'form-control payment_descriptions',
                                'placeholder'=>''
                            ); 
                            echo form_textarea($textarea);
                        ?>
                    </span>
                </td>
                <td>
                    <?php echo form_input('amounts[0]',0,' class="form-control input-sm m-input amount currency text-right amounts" ');?>
                </td>
                <td>
                    <a href='javascript:;' class="remove-line">
                        <i class="text-danger la la-trash"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<a class="inline d-none" data-toggle="modal" data-target="#add_asset_popup" data-title="Add Asset category " data-id="add_asset" id="add_asset"  data-backdrop="static" data-keyboard="false"><?php echo translate('Add Asset Category');?></a>
    <?php echo translate('Add Asset');?></a>

<a class="inline d-none" data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account" data-backdrop="static" data-keyboard="false"><?php echo translate('Add Account');?></a>

<div class="modal fade" id="add_asset_popup" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Add Asset');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open($this->uri->uri_string(),'class=" add_asset m-form--group-seperator-dashe-d form_submit m-form m-form--state" id="add_asset" role="form"'); ?>
                    <span class="error"></span>
                    <div class="form-group m-form__group row">
                        <div class="col-md-12">
                            <label>
                                <?php echo translate('Asset Name');?>
                                <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <?php echo form_input('name',"",'class="form-control" placeholder="Asset Name" id="name"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <div class="col-md-6">
                            <label>
                                <?php echo translate('Asset Category');?>
                                <span class="required">*</span>
                            </label>
                            <div class="input-group m-input-group">
                                <?php echo form_dropdown('asset_category_id',array(''=>'Select asset category')+$asset_category_options+array('0'=>'Add Asset category'),'',' class="form-control m-input m-select2 asset_category_id" ');?>
                            </div> 
                        </div>
                        <div class="col-md-6">
                            <label>
                                <?php echo translate('Asset Cost');?>
                                <span class="required">*</span>
                            </label>
                            <div class="input-group m-input-group">
                                <?php echo form_input('cost','','id="asset_cost" class="text-right form-control m-input currency" placeholder="Amount"'); ?>
                            </div> 
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <div class="col-md-12">
                            <label>
                                <?php echo translate('Description');?>
                                <span class="required">*</span>
                            </label>
                            <div class="input-group m-input-group">
                                <?php 
                                    $textarea = array(
                                        'name'=>'description',
                                        'id'=>'',
                                        'value'=> '',
                                        'cols'=>25,
                                        'rows'=>5,
                                        'maxlength'=>'',
                                        'class'=>'form-control m-input',
                                        'placeholder'=>'Group Asset Description'
                                    ); 
                                    echo form_textarea($textarea);
                                ?>
                            </div> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 m--align-right">
                            <button type="button" class="btn btn-secondary" id="close_modal" data-dismiss="modal">
                                <?php echo translate('Cancel');?>                                     
                            </button>
                            <button type="submit" class="btn btn-primary" id="add_asset_submit">
                                <?php echo translate('Save Changes');?>                            
                            </button>
                        </div>
                    </div>
                <?php echo form_close() ?>
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

<a class="inline d-none" data-toggle="modal" data-target="#asset_category_form" data-title="Add Asset category " data-id="asset_category_form" id="add_asset_category">
<?php echo translate('Add Asset Category');?></a>

<div class="modal fade" id="asset_category_form" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Add Asset Category');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
               <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_income_categories"'); ?>
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-sm-12 m-form__group-sub">
                            <label><?php echo translate('Name');?><span class="required">*</span></label>
                            <?php echo form_input('name','','class="form-control name m-input--air slug_parent" placeholder="Group Asset Category Name"'); ?>
                        </div>
                    </div>
                    <div class="form-group m-form__group row pt-0 m--padding-0">
                        <div class="col-sm-12 m-form__group-sub">
                            <?php echo form_hidden('slug','');?>
                            <!-- <?php echo form_hidden('id',$id);?> -->
                        </div>
                    </div>
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-sm-12 m-form__group-sub">
                            <label><?php echo translate('Group Asset Category Description');?> </label>
                            <?php echo form_textarea('description','','class="form-control m-input--air" placeholder="Group Asset Category Description"');?>
                        </div>
                    </div>
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-12 col-md-12">
                            <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_income_categories_button" type="button">
                                    <?php echo translate('Save Changes');?>
                                </button>
                                &nbsp;&nbsp;
                                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_income_categories_button">
                                    <?php echo translate('Cancel');?>
                                </button> 
                            </span>
                        </div>
                    </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.m-select2').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        SnippetAssetCreateCategory.init(false,true);
        SnippetRecordeAssetPurchase.init();
        $('.record_asset_purchase .add-new-line').on('click',function(){
            var html = $('#append-new-line tbody').html();
            html = html.replace_all('checker','');
            $('#append-place-holder').append(html);
            $('.tooltips').tooltip();
            $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                $(this).parent().find('.payment_dates').attr('name','payment_dates['+(number-1)+']');
                $(this).parent().find('.assets').attr('name','assets['+(number-1)+']');
                $(this).parent().find('.accounts').attr('name','accounts['+(number-1)+']');
                $(this).parent().find('.payment_methods').attr('name','payment_methods['+(number-1)+']');
                $(this).parent().find('.payment_descriptions').attr('name','payment_descriptions['+(number-1)+']');
                $(this).parent().find('.amounts').attr('name','amounts['+(number-1)+']');               
                number++;
            });
            $('.table-multiple-items .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
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

        $(document).on('change','.assets',function(){
            if($(this).val()=='0'){
                $('#add_asset').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.asset_category_id',function(){
            if($(this).val() == '0'){
                $('#add_asset_category').trigger('click');
                $(this).val('').trigger('change');
            }
        });

        var current_row = 0;
        $(document).on('select2:open','.assets', function(e) {
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('select2:open','.accounts', function(e) {
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('submit','#add_asset',function(e){
            e.preventDefault();
            var form = $(this);
            mApp.block('#add_asset_popup .modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            $("#add_asset_popup .error").html('').slideUp();
            $('#add_asset_submit').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("ajax/assets/create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status==1){
                            $('select.assets').each(function(){
                                $(this).append('<option value="'+ data.asset.id + '">' + data.asset.name +' </option>').trigger('change');
                            });
                            $('.table-multiple-items select[name="assets['+current_row+']"]').val(data.asset.id).trigger('change');
                            toastr['success']('You have successfully added a new asset to your group, you can now select it in the assets dropdown.','Asset added successfully');
                            $('#add_asset_popup .close').trigger('click');
                        }else{
                            $('#add_asset_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#add_asset_popup input[name="'+key+'"]').parent().addClass('has-danger');
                                    $('#add_asset_popup select[name="'+key+'"]').parent().addClass('has-danger');
                                });
                            }
                        }
                        mApp.unblock('#add_asset_popup .modal-body');
                        $('#add_asset_submit').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                    }else{
                        $('#add_asset_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong>An error was encountered, please refresh the page and try again</div>').slideDown('slow');
                    }
                }
            });
        });

        $(document).on('change','.accounts',function(){
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
                            $('select.accounts').each(function(){
                                $(this).append('<option value="bank-' + data.bank_account.id + '">'+data.bank_account.bank_details+' - ' + data.bank_account.account_name + ' ('+data.bank_account.account_number+')</option>').trigger('change');
                            });
                            $('.table-multiple-items select[name="accounts['+current_row+']"]').val("bank-"+data.bank_account.id).trigger('change');
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
                        
                    }else{
                        $('#bank_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown();
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
            $('#create_new_account_pop_up .error').html('');
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/sacco_accounts/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.accounts').each(function(){
                                $(this).append('<option value="sacco-' + data.sacco_account.id + '">'+data.sacco_account.sacco_details+' - ' + data.sacco_account.account_name + ' ('+data.sacco_account.account_number+')</option>').trigger('change');
                            });
                            $('.table-multiple-items select[name="accounts['+current_row+']"]').val("sacco-"+data.sacco_account.id).trigger('change');
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
                            $('select.accounts').each(function(){
                                $(this).append('<option value="mobile-' + data.mobile_money_account.id + '">'+data.mobile_money_account.mobile_money_provider_details+' - ' + data.mobile_money_account.account_name + ' ('+data.mobile_money_account.account_number+')</option>').trigger('change');
                            });
                            $('.table-multiple-items select[name="accounts['+current_row+']"]').val("mobile-"+data.mobile_money_account.id).trigger('change');
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
                            $('select.accounts').each(function(){
                                $(this).append('<option value="petty-' + data.petty_cash_account.id + '">' + data.petty_cash_account.account_name + '</option>').trigger('change');
                            });
                            $('.table-multiple-items select[name="accounts['+current_row+']"]').val("petty-"+data.petty_cash_account.id).trigger('change');
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
    });
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
</script>
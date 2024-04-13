<div class="row">
    <div class="col-md-12">
        <span class="error"></span>
        <div id="unreconciled_deposits_listing">
        </div>

        <div id='append-new-line' style="display:none;">
            <table>
                <tbody>
                    <tr>
                        <th scope="row" class='count' width="1%">1</th>
                        <td class="deposit_for_cell">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('deposit_fors[0]',array(''=>'Select deposit for')+translate($deposit_for_options),'',' class="form-control m-input m-select2-append deposit_for"');?>
                            </span>
                        </td>
                        <td class='particulars_place_holder' colspan="3">
                        </td>
                        <td class="text-right" width="1%">
                            <a href='javascript:;' class="remove-line">
                                <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="contribution_payment_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="m-select2-append form-control member" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('contributions[0]',array(''=>'Select contribution')+$contribution_options+array('0'=>"Add Contribution"),'',' class="m-select2-append form-control contribution" ');?>
                            </span>
                            <a href="javascript:;" class="btn btn-sm btn-default btn-xs inline-table-button toggle_deposit_description" id="">
                                <i class="fa fa-plus"></i>
                                <?php echo translate('Add description');?>:
                            </a>
                            <div data-original-title="" data-container="body">
                                <i class="" ></i>
                                <?php 
                                    $textarea = array(
                                        'name'=>'descriptions[0]',
                                        'id'=>'',
                                        'value'=> '',
                                        'cols'=>25,
                                        'rows'=>5,
                                        'maxlength'=>'',
                                        'class'=>'form-control modal_description_textarea',
                                        'style'=>'display:none',
                                        'placeholder'=>''
                                    ); 
                                    echo form_textarea($textarea);
                                ?>
                            </div>
                        </td>
                        <td class="payment_fields" width="20%">
                            <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="fine_payment_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="m-select2-append form-control member" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('fine_categories[0]',array(''=>'Select fine category')+$fine_category_options+array('0'=>"Add Fine Category"),'',' class="m-select2-append form-control fine_category" ');?>
                            </span>
                            <a href="javascript:;" class="btn btn-sm btn-default btn-xs inline-table-button toggle_deposit_description" id="">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-380">
                                    <?php echo translate('Add description');?>:
                                </span>
                            </a>
                            <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                                <?php 
                                    $textarea = array(
                                        'name'=>'descriptions[0]',
                                        'id'=>'',
                                        'value'=> '',
                                        'cols'=>25,
                                        'rows'=>5,
                                        'maxlength'=>'',
                                        'class'=>'form-control modal_description_textarea',
                                        'style'=>'display:none',
                                        'placeholder'=>''
                                    ); 
                                    echo form_textarea($textarea);
                                ?>
                            </div>
                        </td>
                        <td class="payment_fields" width="20%">
                            <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                                <i class="" ></i>
                                <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="miscellaneous_payment_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="m-select2-append form-control member" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="25%">
                            <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                                <?php 
                                    $textarea = array(
                                        'name'=>'descriptions[0]',
                                        'id'=>'',
                                        'value'=>'',
                                        'cols'=>25,
                                        'rows'=>5,
                                        'maxlength'=>'',
                                        'class'=>'form-control miscellaneous_deposit_description',
                                        'placeholder'=>'Type payment description...'
                                    ); 
                                    echo form_textarea($textarea);
                                ?>
                            </div>
                        </td>
                        <td class="payment_fields" width="20%">
                            <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                                <i class="" ></i>
                                <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="income_deposit_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('depositors[0]',array(''=>'Select depositor')+$depositor_options+array('0'=>"Add Depositor"),'',' class="m-select2-append form-control depositor" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('income_categories[0]',array(''=>'Select income category')+$income_category_options+array('0'=>"Add Income Category"),'',' class="m-select2-append form-control income_category" ');?>
                            </span>
                            <a href="javascript:;" class="btn btn-sm btn-default btn-xs inline-table-button toggle_deposit_description" id="">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-380">
                                    <?php echo translate('Add Description');?>:
                                </span>
                            </a>
                            <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                                <?php 
                                    $textarea = array(
                                        'name'=>'descriptions[0]',
                                        'id'=>'',
                                        'value'=> '',
                                        'cols'=>25,
                                        'rows'=>5,
                                        'maxlength'=>'',
                                        'class'=>'form-control modal_description_textarea',
                                        'style'=>'display:none',
                                        'placeholder'=>''
                                    ); 
                                    echo form_textarea($textarea);
                                ?>
                            </div>
                        </td>
                        <td class="payment_fields" width="20%">
                            <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                                <i class="" ></i>
                                <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="loan_repayment_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="m-select2-append form-control member" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="25%">
                                <span class="change-loan loan-to-populate m-select2-sm m-input--air">
                                    <?php echo form_dropdown('loans[0]',array(''=>'Select loan'),'',' class="m-select2-append form-control loan" ');?>
                                </span>
                            </div>
                        </td>
                        <td class="payment_fields" width="20%">
                            <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                                <i class="" ></i>
                                <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                            </div>
                        </td>   
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="bank_loan_disbursement_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%">
                            <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                                <?php 
                                    $textarea = array(
                                        'name'=>'descriptions[0]',
                                        'id'=>'',
                                        'value'=>'',
                                        'cols'=>25,
                                        'rows'=>5,
                                        'maxlength'=>'',
                                        'class'=>'form-control bank_loan_disbursement_description',
                                        'placeholder'=>'Type bank loan description...'
                                    ); 
                                    echo form_textarea($textarea);
                                ?>
                            </div>
                        </td>
                        <td class="payment_fields" width="25%">
                            <div class="input-icon tooltips right" data-original-title="Enter Amount Payable" data-container="body">
                                <i class="" ></i>
                                <?php echo form_input('amount_payables[0]','',' class="currency form-control tooltips amount_payable input-sm text-right" placeholder="Amount Payable" ');?>
                            </div>
                        </td>
                        <td class="payment_fields" width="20%">
                            <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                                <i class="" ></i>
                                <?php echo form_input('amounts[0]','',' class="currency form-control tooltips input-sm amount text-right" placeholder="Amount Disbursed" ');?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="incoming_money_transfer_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%" style="max-width: 150px; overflow: hidden;">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('from_account_ids[0]',array(''=>'Select from account')+$from_account_options+array('0'=>"Add Account"),'',' class="m-select2-append form-control tooltips from_account_id" placeholder="" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="25%">
                            <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                                <?php
                                    $textarea = array(
                                        'name'=>'descriptions[0]',
                                        'id'=>'',
                                        'value'=>'',
                                        'cols'=>25,
                                        'rows'=>5,
                                        'maxlength'=>'',
                                        'class'=>'form-control incoming_money_transfer_description',
                                        'placeholder'=>'Type money transfer description...'
                                    ); 
                                    echo form_textarea($textarea);
                                ?>
                            </div>
                        </td>
                        <td class="payment_fields" width="20%">
                            <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                                <i class="" ></i>
                                <?php echo form_input('amounts[0]','',' class="currency form-control tooltips input-sm amount text-right" placeholder="Amount Transferred" ');?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="stock_sale_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('stock_ids[0]',array(''=>'Select stock')+$stock_options,'',' class="m-select2-append form-control stock_id" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="25%">
                            <span>
                                <?php echo form_input('price_per_shares[0]','',' class=" form-control currency tooltips price_per_share input-sm" placeholder="Price per share" ');?>
                            </span>
                            <span>
                                <?php echo form_input('number_of_shares_solds[0]','',' class=" form-control tooltips number_of_shares_sold input-sm" placeholder="Number of shares sold" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="20%">
                            <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                                <i class="" ></i>
                                <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="asset_sale_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row" width="35%">
                        <td class="payment_fields" colspan="2">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('asset_ids[0]',array(''=>'Select asset')+$asset_options+array('0'=>"Add Asset"),'',' class="m-select2-append form-control asset_id" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="30%">
                            <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                        </td>   
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="money_market_cash_in_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="35%" colspan="2">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('money_market_investment_ids[0]',array(''=>'Select money market investment ')+$money_market_investment_options,'',' class="m-select2-append form-control money_market_investment_id" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="30%">
                            <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                        </td>   
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="loan_processing_income_fields" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="m-select2-append form-control member" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air change-loan loan-to-populate">
                                <?php echo form_dropdown('loans[0]',array(''=>'Select loan'),'',' class="m-select2-append form-control loan" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="20%">
                            <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                        </td>   
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="external_loan_repayment_fields" class="hidden" style="display:none;">
            <table>
                <tbody>
                    <tr class="row">
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air">
                                <?php echo form_dropdown('debtors[0]',array(''=>'Select borrower')+$this->active_group_debtor_options,'',' class="m-select2-append form-control debtor" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="25%">
                            <span class="m-select2-sm m-input--air change-loan external-loan-to-populate">
                                <?php echo form_dropdown('external_loans[0]',array(''=>'Select loan'),'',' class="m-select2-append form-control external_loan" ');?>
                            </span>
                        </td>
                        <td class="payment_fields" width="20%">
                            <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount input-sm text-right" placeholder="Amount" ');?>
                        </td>   
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="reconcile_deposit_pop_up" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body" style="min-height: 150px;">
                        <div class="alert alert-dismissible fade show m-0 m-alert m-alert--outline m-alert--air transaction_details" role="alert">
                            <table class="table table-sm table-borderless table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="m--font-brand bold-400">
                                            <?php echo translate('TRANSACTION DETAILS');?>:
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row" width="1%"><i class="flaticon-calendar m--font-warning"></i></th>
                                        <td>
                                            <?php echo translate('Transaction Date');?>:
                                        </td>
                                        <td>
                                            <span id="transaction_date"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" width="1%">
                                            <i class="flaticon-share m--font-success"></i>
                                        </th>
                                        <td>
                                            <?php echo translate('Transaction Particulars');?>:
                                        </td>
                                        <td>
                                            <span id="transaction_particulars"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" width="1%">
                                            <i class="la la-money m--font-info"></i>
                                        </th>
                                        <td>
                                            <?php echo translate('Transaction Amount');?>:
                                        </td>
                                        <td>
                                            <span id="transaction_amount"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-danger alert-dismissible fade show data_error" role="alert" style="display: none;">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            </button>
                            <span class="message"></span>
                        </div>
                       
                        <?php echo form_open($this->uri->uri_string(),' class="m-form m-form--state reconcile_deposit_form"'); ?>
                            <span class="error"></span>
                            <?php echo form_hidden('transaction_alert_id','','');?>
                            <?php echo form_hidden('transaction_alert_amount','','');?>
                            <div class="mt-4 mb-4 member_notifications">
                                <?php echo translate('Send SMS & E-mail Notifications');?>:
                                <?php echo form_checkbox("enable_notifications",1,TRUE," data-switch='true' checked='checked' data-on-text='".translate('Yes')."' data-off-text='".translate('No')."' data-on-color='brand' data-off-color='danger' data-size='small' "); ?>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-multiple-items">
                                    <thead>
                                        <tr>
                                            <th width="2%">#</th>
                                            <th width="20%">
                                                <?php echo translate('Deposit For');?>
                                                <span class="required">*</span>
                                            </th>
                                            <th colspan="3" width="70%">
                                                <?php echo translate('Deposit Particulars');?>
                                                <span class="required">*</span>
                                            </th>
                                            <th width="5%">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody id='append-place-holder'>
                                        <tr>
                                            <th scope="row" class='count' width="1%">1</th>
                                            <td class="deposit_for_cell">
                                                <span class="m-select2-sm m-input--air">
                                                    <?php echo form_dropdown('deposit_fors[]',array(''=>'Select deposit for')+translate($deposit_for_options),'',' class="form-control m-input m-select2 deposit_for"');?>
                                                </span>
                                            </td>
                                            <td class='particulars_place_holder' colspan="3">
                                            </td>
                                            <td class="text-right" width="5%">
                                                <a href='javascript:;' class="remove-line">
                                                    <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="add-new-line">
                                <i class="la la-plus"></i>
                                <?php echo translate('ADD NEW LINE');?>
                            </button>
                            <hr>
                            <div class="row">
                                <div class="col-lg-12 m--align-right">
                                    <button type="button" class="btn btn-secondary btn-sm" id="close_modal" data-dismiss="modal">
                                        <?php echo translate('Cancel');?>
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm" id="submit_reconcile_deposit">
                                        <?php echo translate('Save Changes');?>
                                    </button>
                                </div>
                            </div>
                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
        
        <a class="inline d-none" data-toggle="modal" data-target="#contributions_form" data-title="Add Contribution" data-id="add_contribution" id="add_contribution" data-backdrop="static" data-keyboard="false"><?php echo translate('Add Recipient');?></a>

        <a class="inline d-none" data-toggle="modal" data-target="#create_new_member_pop_up" data-title="Add Member" data-id="create_member" id="add_new_member"  data-backdrop="static" data-keyboard="false">
            <?php echo translate('Add Member');?>
        </a>
        
        <a class="inline d-none" data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account"  data-backdrop="static" data-keyboard="false"><?php echo translate('Add Account');?></a>

        <a class="inline d-none" data-toggle="modal" data-target="#add_fine_category_popup" data-title="Add Fine Category" data-id="add_fine_category" id="add_fine_category"  data-backdrop="static" data-keyboard="false"><?php echo translate('Add Fine Category');?></a>

        <a class="d-none inline" data-toggle="modal" data-target="#create_new_income_category_pop_up" data-title="Add Income Category" data-id="create_income_category" id="add_new_income_category"  data-backdrop="static" data-keyboard="false"  >
            <?php echo translate('Add Income Category');?>
        </a>

        <a class="inline d-none" data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account"  data-backdrop="static" data-keyboard="false"><?php echo translate('Add Account');?></a>

        <a class="inline d-none" data-toggle="modal" data-target="#add_asset_popup" data-title="Add Asset category " data-id="add_asset" id="add_asset"  data-backdrop="static" data-keyboard="false"><?php echo translate('Add Asset Category');?></a>

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
                            <?php echo form_hidden('slug','','');?>
                            <?php echo form_hidden('id','','');?>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="modal fade" id="add_fine_category_popup" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            <?php echo translate('Add Fine Category');?>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                ×
                            </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php echo form_open($this->uri->uri_string(),'class=" add_fine_category_form m-form--group-seperator-dashe-d form_submit m-form m-form--state" id="add_fine_category_form" role="form"'); ?>
                            <span class="error"></span>
                            <div class="m-form__section m-form__section--first">
                                <div class="form-group m-form__group">
                                    <label for="example_input_full_name">
                                        <?php echo translate('Fine Category Name');?>:
                                        <span class="required">*</span>
                                    </label>
                                    <?php echo form_input('name','',' id="name" class="form-control slug_parent" placeholder="Fine Category Name"');?>
                                    <?php echo form_hidden("slug",""); ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 m--align-right">
                                        <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                            <?php echo translate('Cancel');?>
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="create_fine_category">
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

        <div id='append_fine_setting_row' style="display:none;">
            <div class='fine_setting_row'>
                <label>We charge a</label>
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-4">
                        <div class='col-md-12 input-group'>
                            <?php echo form_dropdown('fine_type[]',array(''=>'Select fine type')+$fine_types,'','id="" class="form-control fine_types append_select2" data-placeholder="Select..."'); ?>
                        </div>
                    </div>

                    <div id='' class="col-md-4 col-sm-4 col-xs-4 percentage_fine_settings fine_percentage_rate">
                        <?php echo form_input('percentage_rate[]','','class="form-control percentage_rates" placeholder="Percentage Rate"'); ?>
                    </div>

                    <div id='' class="col-md-4 col-sm-4 col-xs-4 fixed_fine_settings fine_fixed_amount">
                        <?php echo form_input('fixed_amount[]','','class="form-control currency fixed_amounts" placeholder="Fixed Amount"'); ?>
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-4 fixed_fine_settings fixed_fine_mode">
                        <div class='col-md-12 input-group'>
                            <?php echo form_dropdown('fixed_fine_mode[]',array(''=>'Select how fines behave')+$fine_mode_options,'','id="" class="form-control append_select2 fixed_fine_modes" data-placeholder="Select..."'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_settings percentage_fine_on">
                        <div class='col-md-12 input-group'>
                            <?php echo form_dropdown('percentage_fine_on[]',array(''=>'Select when fines is calculated based on')+$percentage_fine_on_options,'','id="" class="form-control percentage_fine_ons append_select2" data-placeholder="Select..."'); ?>
                        </div>
                    </div>
                </div>

                <div class='row percentage_fine_settings margin-top-10'>
                    <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_chargeable_on">
                        <div class='col-md-12 input-group'>
                            <?php echo form_dropdown('percentage_fine_chargeable_on[]',array(''=>'Select when fines are charged')+$fine_chargeable_on_options,'','id="" class="form-control percentage_fine_chargeable_ons append_select2" data-placeholder="Select..."'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_mode">
                        <div class='col-md-12 input-group'>
                            <?php echo form_dropdown('percentage_fine_mode[]',array(''=>'Select how fines behave')+$fine_mode_options,'','id="" class="form-control percentage_fine_modes append_select2" data-placeholder="Select..."'); ?>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_frequency">
                        <div class='col-md-12 input-group'>
                            <?php echo form_dropdown('percentage_fine_frequency[]',array(''=>'Select fine frequency')+$fine_frequency_options,'','id="" class="form-control percentage_fine_frequencies append_select2" data-placeholder="Select..."'); ?>
                        </div>
                    </div>

                </div>

                <div class='row fixed_fine_settings margin-top-10'>
                    <div class="col-md-6 col-sm-6 col-xs-6 fixed_fine_chargeable_on">
                        <div class='col-md-12 input-group'>
                            <?php echo form_dropdown('fixed_fine_chargeable_on[]',array(''=>'Select when fines are charged')+$fine_chargeable_on_options,'','id="" class="form-control fixed_fine_chargeable_ons append_select2" data-placeholder="Select..."'); ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 fixed_fine_settings fixed_fine_frequency">
                        <div class='col-md-12 input-group'>
                            <?php echo form_dropdown('fixed_fine_frequency[]',array(''=>'Select fine frequency')+$fine_frequency_options,'','id="" class="form-control fixed_fine_frequencies append_select2" data-placeholder="Select..."'); ?>
                        </div>
                    </div>
                </div>

                <div class='row margin-top-10 margin-bottom-10'>
                    <div class="col-md-12 col-sm-12 col-xs-12 fine_limit">
                        <div class='col-md-12 input-group'>
                            <?php echo form_dropdown('fine_limit[]',$fine_limit_options,'','id="" class="form-control fine_limits append_select2" data-placeholder="Select..."'); ?>
                        </div>
                    </div>
                </div>
                <div class='fine_notifications form-group margin-bottom-20' >
                    <label>Fine Notifications</label>
                    <div class="checkbox-list">
                        <label class="checkbox-inline">
                            <?php echo form_checkbox('fine_sms_notifications_enabled[]',1,'',' class="fine_sms_notifications_enableds" '); ?> Enable SMS Notifications
                        </label>
                        <label class="checkbox-inline">
                            <?php echo form_checkbox('fine_email_notifications_enabled[]',1,'',' class="fine_email_notifications_enableds" '); ?> Enable Email Notifications
                        </label>
                    </div>
                </div>
                <div class="row margin-top-10">
                    <div class='col-md-12 margin-bottom-10 text-left'>
                        <a data-original-title="Remove fine setting line" href="javascript:;" class="btn btn-danger tooltips btn-xs remove-line" id="">
                            <i class="fa fa-times"></i>
                            <span class="hidden-380">
                                Remove fine setting
                            </span>
                        </a>
                    </div>
                </div>
                <hr/>
            </div>
        </div>
    </div>
</div>

<script>
var current_row = 0;
$(document).ready(function(){
    SnippetCreateContribution.init(false,true);
    mApp.block('#unreconciled_deposits_listing',{
        overlayColor: 'white',
        animate: true
    });

    var unreconciled_deposit_id;

    var form;

    $(document).on('click','.toggle_transaction_alert_details',function(){
        var element = $(this).parent().parent();
        var element2 = $(this);
        element.find('.transaction_alert_details').slideToggle(function(){
            if($(this).is(':visible')){
                element2.html('Less..');
            }else{
                element2.html('More..');
            }
        });
    });
    
    $('#reconcile_deposit_pop_up').on('shown.bs.modal',function(){   
        mApp.block('#reconcile_deposit_pop_up .modal-body', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Loading transaction...'
        });
        $('.reconcile_deposit_form').find('*').each(function(){
            $(this).removeClass('has-danger');
        });
        FormInputMask.init();
        Select2.init();
        var form  = $("#reconcile_deposit_pop_up");
        $.post('<?php echo base_url("group/transaction_alerts/ajax_get"); ?>',{'id':unreconciled_deposit_id,},function(data){
            if(isJson(data)){
                var transaction_alert = $.parseJSON(data);
                form.find('.transaction_details #transaction_date').html(transaction_alert.formatted_transaction_date);
                form.find('.transaction_details #transaction_particulars').html(transaction_alert.particulars);
                form.find('.transaction_details #transaction_amount').html(transaction_alert.formatted_amount);
                form.find('input[name="transaction_alert_id"]').val(transaction_alert.id);
                form.find('input[name="transaction_alert_amount"]').val(transaction_alert.amount);
                $("#reconcile_deposit_pop_up .table-multiple-items #append-place-holder").html($('#append-new-line tbody').html());
                $('#reconcile_deposit_pop_up .m-select2-append').select2({
                    width:'100%',
                    placeholder:{
                        id: '-1',
                        text: "--Select option--",
                    }, 
                });
                mApp.unblock('#reconcile_deposit_pop_up .modal-body', {});
                $('#reconcile_deposit_pop_up .transaction_details,#reconcile_deposit_pop_up .reconcile_deposit_form,#reconcile_deposit_pop_up .member_notifications, #reconcile_deposit_pop_up .modal-footer').slideDown();
                $('#reconcile_deposit_pop_up #submit_reconcile_deposit,#reconcile_deposit_pop_up #close_modal').attr('disabled',false);
            }else{
                alert('Error: Could not find transaction alert.');
                $('.close').click();
            }
        });
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
        console.log('add account modal close event');
    });

    //reconcile deposit_pop_up modal close eventt
    $('#reconcile_deposit_pop_up').on('hidden.bs.modal', function () {
        // //clear input data
        $(':input','#reconcile_deposit_pop_up')
            .val('')
            .prop('checked',false)
            .removeAttr('selected')
            .trigger('change');
    });

    $(document).on('click','.toggle_deposit_description',function(){
        var element = $(this).parent();
        var element2 = $(this);
        element.find('.modal_description_textarea').slideToggle(function(){
           if($(this).is(':visible')){
                element2.html('<i class="fa fa-eye-slash"></i><span class="hidden-380">Hide description</span>');
            }else{
                element2.html('<i class="fa fa-eye"></i><span class="hidden-380">Show description</span>');
            }
        });
    });

    $(document).on('click','#reconcile_deposit_pop_up #add-new-line',function(){
        var html = $('#append-new-line tbody').html();
        $('#reconcile_deposit_pop_up #append-place-holder').append(html);
        update_field_names($('.reconcile_deposit_form'));
        $('#reconcile_deposit_pop_up .m-select2-append').each(function(){
            if($(this).hasClass('select2-hidden-accessible')){
            }else{
                $(this).select2({
                    placeholder:{
                        id: '-1',
                        text: "--Select option--",
                    }, 
                });
            }
        });
        var table = $("#reconcile_deposit_pop_up .table-multiple-items");
        var number = 0;
        table.find('.count').each(function(){
            $(this).text(number+1);
            $(this).parent().find('.deposit_for').attr('name','deposit_fors['+number+']')
            number++;
        });
        FormInputMask.init();
    });

    $(document).on('click','#reconcile_deposit_pop_up a.remove-line',function(event){
        if($('#reconcile_deposit_pop_up .count').length == 1){
            $('.reconcile_deposit_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry:</strong> You cannot remove the last line</div>').slideDown();
        }else{
            $(this).parent().parent().remove();
            var number = 1;
            $('#reconcile_deposit_pop_up .count').each(function(){
                $(this).text(number);
                number++;
            });
        }
    });

    $('#reconcile_deposit_pop_up').on('change','.deposit_for',function(){
        $(this).parent().parent().parent().find('.payment_fields').each(function(){
            $(this).remove();
        });
        var element = $(this).parent().parent().parent().find('.deposit_for_cell');
        if($(this).val()==1){
            var html = $('#contribution_payment_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else if($(this).val()==2){
            var html = $('#fine_payment_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else if($(this).val()==3){
            var html = $('#miscellaneous_payment_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else if($(this).val()==4){
            var html = $('#income_deposit_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else if($(this).val()==5){
            var html = $('#loan_repayment_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else if($(this).val()==6){
            var html = $('#bank_loan_disbursement_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else if($(this).val()==7){
            var html = $('#incoming_money_transfer_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            $('.table-multiple-items .from_account_id').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#accounts_form" data-title="Add Account" data-id="add_account" id="add_account_link" href="#">Add Account</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        }else if($(this).val()==8){
            var html = $('#stock_sale_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else if($(this).val()==9){
            var html = $('#asset_sale_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            $('.table-multiple-items .asset_id').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_asset" data-title="Add Asset" data-id="add_asset" id="add_asset_link" href="#">Add Asset</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        }else if($(this).val()==10){
            var html = $('#money_market_cash_in_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else if($(this).val()==11){
            var html = $('#loan_processing_income_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else if($(this).val()==12){
            var html = $('#external_loan_repayment_fields .row').html();
            $(element).after(html);
            $(this).parent().parent().parent().find('.particulars_place_holder').remove();
        }else{
            $(element).after('<td class="particulars_place_holder" colspan="3"></td>');
        }
        update_field_names($('.reconcile_deposit_form'));
        FormInputMask.init();
        $('#reconcile_deposit_pop_up .m-select2-append').each(function(){
            if($(this).hasClass('select2-hidden-accessible')){
            }else{
                $(this).select2({
                    placeholder:{
                        id: '-1',
                        text: "--Select option--",
                    }, 
                });
            }
        });
    });

    $(document).on('select2:open','.fine_category', function(e) {
        // do something
        var name = $(this).attr("name");
        current_row = parseInt($(this).parent().parent().parent().find('.count').text()) - 1;
    });

    $(document).on('select2:open','.income_category', function(e) {
        // do something
        var name = $(this).attr("name");
        current_row = parseInt($(this).parent().parent().parent().find('.count').text()) - 1;
    });

    // var current_row = 0;
    $(document).on('select2:open','.member', function(e) {
        // do something
        var name = $(this).attr("name");
        current_row = parseInt($(this).parent().parent().parent().find('.count').text()) - 1;
    });

    $(document).on('select2:open','.asset_id', function(e) {
        // do something
        var name = $(this).attr("name");
        current_row = parseInt($(this).parent().parent().parent().find('.count').text()) - 1;
    });

    $(document).on('select2:open','.contribution', function(e) {
        // do something
        var name = $(this).attr("name");
        current_row = parseInt($(this).parent().parent().parent().find('.count').text()) - 1;
    });

    $(document).on('select2:open','.loan_id', function(e) {
        // do something
        var name = $(this).attr("name");
        current_row = parseInt($(this).parent().parent().parent().find('.count').text()) - 1;
    });

    $(document).on('select2:open','.from_account_id', function(e) {
        // do something
        var name = $(this).attr("name");
        current_row = parseInt($(this).parent().parent().parent().find('.count').text()) - 1;
    });

    $(document).on('change','.member',function(){
        if($(this).val()=='0'){
            $('#add_new_member').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('change','.member',function(){
        var me = $(this);
        var row = me.parent().parent().parent().parent();
        if(row.find('.deposit_for').val() == 5 || row.find('.deposit_for').val() == 11){
            mApp.block('.loan-to-populate .select2', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            var member_id = me.val();
            var attribute = me.attr('name');
            var url = '<?php echo site_url('group/loans/ajax_get_active_member_loans')?>';
                $.ajax({
                type: "POST",
                url: url,
                dataType: "html",
                data: {'member_id': member_id , 'attribute':attribute,'no_add_loan': true},
                success: function(res) 
                {
                    me.parent().parent().parent().find('.change-loan').html(res);
                    me.parent().parent().parent().find('.loan').select2();
                    mApp.unblock(row.find(".loan-to-populate .select2"));
                },
                error: function(xhr, ajaxOptions, thrownError)
                {
                    
                }
            });
        }
    });

    $(document).on('change','.debtor',function(){
        var me = $(this);
        var row = me.parent().parent().parent().parent();
        if(row.find('.deposit_for').val() == 12){
            mApp.block('.external-loan-to-populate .select2', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            var debtor_id = me.val();
            var attribute = me.attr('name');
            var url = '<?php echo site_url('group/loans/ajax_get_active_debtor_loans')?>';
            $.ajax({
                type: "POST",
                url: url,
                dataType: "html",
                data: {'debtor_id':debtor_id,'attribute':attribute,'no_add_loan':true},
                success: function(res) {
                    me.parent().parent().parent().find('.change-loan').html(res);
                    me.parent().parent().parent().find('.loan').select2();
                    mApp.unblock(row.find(".external-loan-to-populate .select2"));
                },
                error: function(xhr, ajaxOptions, thrownError){
                    
                }
            });
        }
    });

    function update_field_names(form){
        if (typeof form == 'undefined') {
            //do nothing
        }else{
            var number = 1;
            form.find('.count').each(function(){
                $(this).text(number);
                $(this).parent().find('.deposit_for').attr('name','deposit_fors['+(number-1)+']');
                $(this).parent().find('.member').attr('name','members['+(number-1)+']');
                $(this).parent().find('.debtor').attr('name','debtors['+(number-1)+']');
                $(this).parent().find('.fine_category').attr('name','fine_categories['+(number-1)+']');
                $(this).parent().find('.contribution').attr('name','contributions['+(number-1)+']');
                $(this).parent().find('.income_category').attr('name','income_categories['+(number-1)+']');
                $(this).parent().find('.depositor').attr('name','depositors['+(number-1)+']');
                $(this).parent().find('.amount').attr('name','amounts['+(number-1)+']');
                $(this).parent().find('.modal_description_textarea').attr('name','descriptions['+(number-1)+']');
                $(this).parent().find('.miscellaneous_deposit_description').attr('name','descriptions['+(number-1)+']');
                $(this).parent().find('.miscellaneous_deposit_description').attr('name','descriptions['+(number-1)+']');
                $(this).parent().find('.amount_payable').attr('name','amount_payables['+(number-1)+']');
                $(this).parent().find('.bank_loan_disbursement_description').attr('name','descriptions['+(number-1)+']');
                $(this).parent().find('.from_account_id').attr('name','from_account_ids['+(number-1)+']');
                $(this).parent().find('.incoming_money_transfer_description').attr('name','descriptions['+(number-1)+']');
                $(this).parent().find('.price_per_share').attr('name','price_per_shares['+(number-1)+']');
                $(this).parent().find('.number_of_shares_sold').attr('name','number_of_shares_solds['+(number-1)+']');
                $(this).parent().find('.stock_id').attr('name','stock_ids['+(number-1)+']');
                $(this).parent().find('.asset_id').attr('name','asset_ids['+(number-1)+']');
                $(this).parent().find('.money_market_investment_id').attr('name','money_market_investment_ids['+(number-1)+']');
                $(this).parent().find('.loan').attr('name','loans['+(number-1)+']');
                $(this).parent().find('.external_loan').attr('name','external_loans['+(number-1)+']');
                number++;
            }); 
        }
    }

    $(document).on('click','.reconcile_deposit',function(){
        $('#reconcile_deposit_pop_up #submit_reconcile_deposit,#reconcile_deposit_pop_up #close_modal').attr('disabled',true);
        $('#reconcile_deposit_pop_up .transaction_details,#reconcile_deposit_pop_up .reconcile_deposit_form,#reconcile_deposit_pop_up .member_notifications,#reconcile_deposit_pop_up .modal-footer').hide();
        unreconciled_deposit_id = $(this).attr('id');
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
                    width:'100%',
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
                        $('select.from_account_id').each(function(){
                            $(this).append('<option value="bank-' + data.bank_account.id + '">'+data.bank_account.bank_details+' - ' + data.bank_account.account_name + ' ('+data.bank_account.account_number+')</option>').trigger('change');
                        });
                        $('#reconcile_deposit_pop_up select[name="from_account_ids['+current_row+']"]').val("bank-"+data.bank_account.id).trigger('change');
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
                    width:'100%',
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
                        $('select.from_account_id').each(function(){
                            $(this).append('<option value="sacco-' + data.sacco_account.id + '">'+data.sacco_account.sacco_details+' - ' + data.sacco_account.account_name + ' ('+data.sacco_account.account_number+')</option>').trigger('change');
                        });
                        $('#reconcile_deposit_pop_up select[name="from_account_ids['+current_row+']"]').val("sacco-"+data.sacco_account.id).trigger('change');
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
                        $('select.from_account_id').each(function(){
                            $(this).append('<option value="mobile-' + data.mobile_money_account.id + '">'+data.mobile_money_account.mobile_money_provider_details+' - ' + data.mobile_money_account.account_name + ' ('+data.mobile_money_account.account_number+')</option>').trigger('change');
                        });
                        $('#reconcile_deposit_pop_up select[name="from_account_ids['+current_row+']"]').val("mobile-"+data.mobile_money_account.id).trigger('change');
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
                        $('select.from_account_id').each(function(){
                            $(this).append('<option value="petty-' + data.petty_cash_account.id + '">' + data.petty_cash_account.account_name + '</option>').trigger('change');
                        });
                        $('#reconcile_deposit_pop_up select[name="from_account_ids['+current_row+']"]').val("petty-"+data.petty_cash_account.id).trigger('change');
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
                        console.log(data);
                    if(data.status == 1){
                        $('select.member').each(function(){
                            $(this).append('<option value="' + data.member.id + '">' + data.member.first_name +' '+ data.member.last_name + '</option>').trigger('change');
                        });
                        $('#reconcile_deposit_pop_up select[name="members['+current_row+']"]').val(data.member.id).trigger('change');
                        $('#create_new_member_pop_up .close').trigger('click');
                        toastr['success']('You have successfully added a new member to your group, you can now select him/her in the members dropdown.','Member added successfully');
                    }else{
                        console.log(data);
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
  
    $(document).on('submit','#add_fine_category_form',function(e){
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
        $('#add_fine_category_popup .error').html('');
        $('#create_fine_category').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
        $.ajax({
            type: "POST",
            url: '<?php echo base_url("ajax/fine_categories/create"); ?>',
            data: form.serialize(),
            success: function(response) {
                if(isJson(response)){
                    var data = $.parseJSON(response);
                    if(data.status == 1){
                        toastr['success']('You have successfully added a new fine category to your group, you can now select it in the fine categories dropdown.','Fine category added successfully');
                        $('select.fine_category optgroup').each(function(){
                            if($(this).attr("label") == "Fine Categories"){
                                $(this).append('<option value="fine_category-' + data.fine_category.id + '">' + data.fine_category.name + ' - ' + data.fine_category.currency +data.fine_category.amount + '</option>').trigger('change');
                            }
                        });
                        $('#reconcile_deposit_pop_up select[name="fine_categories['+current_row+']"]').val('fine_category-'+data.fine_category.id).trigger('change');
                        $('#add_fine_category_popup .close').trigger('click');
                    }else{
                        $('#add_fine_category_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
                        if(data.validation_errors){
                            $.each(data.validation_errors, function( key, value ) {
                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                $('#add_fine_category_popup input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                $('#add_fine_category_popup select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                            });
                        }
                    }
                }else{
                    $('#add_fine_category_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem submitting the data. Please try again.</div>').slideDown('slow');
                    $(".modal-body").animate({ scrollTop: 0 }, 600);;
                }
                $('#create_fine_category').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                mApp.unblock('.modal-body');
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
                        $('#reconcile_deposit_pop_up select[name="income_categories['+current_row+']"]').val(data.income_category.id).trigger('change');
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
        $("#add_asset_popup .error").html('').hide();
        $('#add_asset_submit').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
        $.ajax({
            type: "POST",
            url: '<?php echo base_url("ajax/assets/create"); ?>',
            data: form.serialize(),
            success: function(response) {
                if(isJson(response)){
                    var data = $.parseJSON(response);
                    if(data.status==1){
                        $('select.asset_id').each(function(){
                            $(this).append('<option value="' + data.asset.id + '">' + data.asset.name +' </option>').trigger('change');
                        });
                        $('#reconcile_deposit_pop_up select[name="asset_ids['+current_row+']"]').val(data.asset.id).trigger('change');
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

    $('.reconcile_deposit_form').on('submit',function(e){
        e.preventDefault();
        var form = $(this);
        var entries_are_valid = true;
        var totals_are_valid = true;
        $('#reconcile_deposit_pop_up .error').html('').slideUp();

        $('#reconcile_deposit_pop_up select.deposit_for').each(function(){
            if(check_deposit_for($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.member').each(function(){
            if(check_member($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.debtor').each(function(){
            if(check_debtor($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.contribution').each(function(){
            if(check_contribution($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        var total_amount = 0;
        $('#reconcile_deposit_pop_up input.amount').each(function(){
            if(check_amount($(this))){
                total_amount += parseFloat($(this).val().replace(/,/g, ''));
            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.fine_category').each(function(){
            if(check_fine_category($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up textarea.miscellaneous_deposit_description').each(function(){
            if(check_miscellaneous_deposit_description($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.depositor').each(function(){
            if(check_depositor($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.income_category').each(function(){
            if(check_income_category($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.loan').each(function(){
            if(check_loan($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.external_loan').each(function(){
            if(check_external_loan($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up textarea.bank_loan_disbursement_description').each(function(){
            if(check_bank_loan_disbursement_description($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up input.amount_payable').each(function(){
            if(check_amount_payable($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.from_account_id').each(function(){
            if(check_from_account_id($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.stock_id').each(function(){
            if(check_stock($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up input.number_of_shares_sold').each(function(){
            if(check_number_of_shares_sold($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up input.price_per_share').each(function(){
            if(check_price_per_share($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.asset_id').each(function(){
            if(check_asset($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#reconcile_deposit_pop_up select.money_market_investment_id').each(function(){
            if(check_money_market_investment($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        var transaction_alert_amount = parseFloat(form.find('input[name="transaction_alert_amount"]').val());
        var error_message = "";
        if(total_amount==transaction_alert_amount){
          //continue
        }else{
            totals_are_valid = false;
            error_message = "<p>Kindly make sure the amount reconciled adds up to the amount deposited of "+transaction_alert_amount+".</p>";
            $('.amount').each(function() {
                $(this).parent().addClass('has-danger');
            });
        }

        if(entries_are_valid&&totals_are_valid){
            swal({
                title:"Are you sure?",
                text:"You will be able to void this reconciliation later",
                type:"info",
                showCancelButton:1,
                reverseButtons:1,
                confirmButtonText:"Yes, reconcile it!"
            }).then(function(e){
                if(e.value){
                    mApp.block('.modal-body', {
                        overlayColor: 'grey',
                        animate: true,
                        type: 'loader',
                        state: 'primary',
                        message: 'Loading...'
                    });
                    $('#submit_reconcile_deposit').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
                    $.ajax({
                        type: "POST",
                        url: '<?php echo base_url("group/transaction_alerts/ajax_reconcile_deposit"); ?>',
                        data: form.serialize(),
                        success: function(response) {
                            if(response=='success'){
                                $('#reconcile_deposit_pop_up #close_modal').click();
                                swal("Reconciled!","Your transaction has been reconciled.","success");
                                mApp.unblock('.modal-body');
                                $('#submit_reconcile_deposit').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                                $('#unreconciled_deposit_row_'+unreconciled_deposit_id+' .reconcile_action ').html('<span class="m-badge m-badge--primary m-badge--wide"> Reconciled </span>');
                                $('#unreconciled_deposit_row_'+unreconciled_deposit_id).addClass('success').removeClass('unreconciled_deposit_count').delay(3000).fadeOut(3000,function(){
                                    update_notification_counts();
                                });
                            }else{
                                e.value&&swal("Error!","There were errors reconciling this transaction.","error")
                                $('.reconcile_deposit_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response+'</div>').slideDown('slow');
                                mApp.unblock('.modal-body');
                                $('#submit_reconcile_deposit').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                            }
                        }
                    });
                }
            });
        }else{
            //show message
            $('.reconcile_deposit_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><p>Kindly review the fields marked in red, enter the values requested before you can proceed.</p>'+error_message+'</div>').slideDown('slow');
        }
    });

    $(document).on('click','#add_contribution',function(){
        $(".contribution-table .contribution").select2({
            width:'100%',
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
            width:'100%',
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
        console.log($(this).val());
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

    $(document).on('change','.contribution',function(){
        if($(this).val()=='0'){
            $('#add_contribution').trigger('click');
            $(this).val("").trigger('change');
            $('#contributions_form .select2-append').select2({
                width:'100%',
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        }
    });

    $(document).on('change','.from_account_id',function(){
        if($(this).val()=='0'){
            $('#add_new_account').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('change','.income_category',function(){
        if($(this).val()=='0'){
            $('#add_new_income_category').trigger('click');
            $(this).val("").trigger('change');
            $('#contributions_form .select2-append').select2({
                width:'100%',
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

    $(document).on('change','.fine_category',function(){
        if($(this).val()=='0'){
            $('#add_fine_category').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('change','.asset_id',function(){
        if($(this).val()=='0'){
            $('#add_asset').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('click','#add_new_member',function(){
        $(".contribution-table .member").select2({
            width:'100%',
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

    $(document).on('blur keyup','#reconcile_deposit_pop_up input.amount',function(){
        check_amount($(this));
    });

    $(document).on('blur keyup','#reconcile_deposit_pop_up input.number_of_shares_sold',function(){
        check_number_of_shares_sold($(this));
    });

    $(document).on('blur keyup','#reconcile_deposit_pop_up input.price_per_share',function(){
        check_price_per_share($(this));
    });
    
    $(document).on('blur keyup','#reconcile_deposit_pop_up input.amount_payable',function(){
        check_amount_payable($(this));
    });
    
    $(document).on('change','#reconcile_deposit_pop_up select.contribution',function(){
        check_contribution($(this));
    });
    
    $(document).on('change','#reconcile_deposit_pop_up select.loan',function(){
        check_loan($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.external_loan',function(){
        check_external_loan($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.fine_category',function(){
        check_fine_category($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.income_category',function(){
        check_income_category($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.depositor',function(){
        check_depositor($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.member',function(){
        check_member($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.debtor',function(){
        check_debtor($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.deposit_for',function(){
        check_deposit_for($(this));
    });

    $(document).on('blur','textarea.miscellaneous_deposit_description',function(){
        check_miscellaneous_deposit_description($(this));
    });

    $(document).on('blur','textarea.bank_loan_disbursement_description',function(){
        check_bank_loan_disbursement_description($(this));
    });

    $(document).on('blur','input.price_per_share',function(){
        var price_per_share = $(this).val();
        var element = $(this).parent().parent().parent();
        var number_of_shares_sold = element.find('.number_of_shares_sold').val();
        if(isNumeric(price_per_share)&&isNumeric(number_of_shares_sold)){
            var product = price_per_share * number_of_shares_sold;
            element.find('.amount').val(product);
        }else{
            element.find('.amount').val(0);
        }
    });

    $(document).on('blur','input.number_of_shares_sold',function(){
        var number_of_shares_sold = $(this).val();
        var element = $(this).parent().parent().parent();
        var price_per_share = element.find('.price_per_share').val();
        if(isNumeric(price_per_share)&&isNumeric(number_of_shares_sold)){
            var product = price_per_share * number_of_shares_sold;
            element.find('.amount').val(product);
        }else{
            element.find('.amount').val(0);
        }
    });

    $(document).on('change','#reconcile_deposit_pop_up select.from_account_id',function(){
        check_from_account_id($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.stock_id',function(){
        check_stock($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.asset_id',function(){
        check_asset($(this));
    });

    $(document).on('change','#reconcile_deposit_pop_up select.money_market_investment_id',function(){
        check_money_market_investment($(this));
    });

    $(document).on('change','.contribution',function(){
        if($(this).val()=='0'){
            $('#add_contribution').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('change','.fine_category',function(){
        if($(this).val()=='0'){
            $('#add_fine_category_link').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('change','.loan',function(){
        if($(this).val()=='0'){
            $('#add_loan').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('change','.depositor',function(){
        if($(this).val()=='0'){
            $('#add_depositor_link').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('change','.income_category',function(){
        if($(this).val()=='0'){
            $('#add_income_category_link').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('change','.from_account_id',function(){
        if($(this).val()=='0'){
            $('#add_account_link').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    $(document).on('change','.asset_id',function(){
        if($(this).val()=='0'){
            $('#add_asset_link').trigger('click');
            $(this).val("").trigger('change');
        }
    });

    // when the bulk reconcile button is clicked.
    $(document).on('click','#reconcile_confirmation',function(){
        bootbox.confirm("Are you sure, you want to proceed? The select transactions will be marked as reconciled without being allocated.", function(result) {
        if(result==true){
            mApp.block('#form',{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            $('#reconcile_confirmation').text('');
            $('#reconcile_confirmation').append('<span><i class="fa fa-spinner fa-spin"></i> Processing </span>');
            $('#reconcile_confirmation').attr('disabled',true);
            var selected_ids = []; // initialize empty array
            $("input.action_to_reconcile:checked").each(function(){
            selected_ids.push($(this).val());
            });
            var unreconciled_deposit_id = $(this).attr('data-id');
            $.ajax({      
            url:'<?php echo site_url('group/transaction_alerts/bulk_mark_as_reconciled')?>',
            type:'POST',
            data:{'transaction_alert_ids':selected_ids},
            success: function(response){

                var result = $.parseJSON(response);

                if(result.status ==1){
                toastr['success'](result.message);
                $.each(selected_ids, function(key,unreconciled_deposit_id) {
                    $('#unreconciled_deposit_row_'+unreconciled_deposit_id).addClass('success').slideDown('slow').delay(3000).fadeOut(3000,function(){
                    update_notification_counts();
                    });
                });
                }else{
                toastr['error'](result.error_message);
                }
                mApp.unblock('#form')
                $('#reconcile_confirmation').text('');
                $('#reconcile_confirmation').append('<span><i class="la la-trash-o"></i> Bulk mark as Reconciled </span>');
                $('#reconcile_confirmation').attr('disabled',false);                
            }
            });
        }else{
            return true;
        }
        });
    });

    if(typeof(EventSource) !== "undefined") {
        var timestamp = Math.floor(Date.now() / 1000);
        var source = new EventSource("<?php echo site_url('group/transaction_alerts/check_new_unreconciled_deposits/"+timestamp+"'); ?>");
        source.onmessage = function(event) {
            $('#unreconciled_deposits_loading_row').slideDown('slow');
            mApp.block('#unreconciled_deposits_loading_row td', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Receiving transaction alert...'
            });
            var data = $.parseJSON(event.data);
            var unreconciled_deposits = data.unreconciled_deposits;
            var unreconciled_deposit;
            var amount;
            var account;
            for(var i = 0; i < unreconciled_deposits.length; i++){
                unreconciled_deposit = unreconciled_deposits[i];
                amount = (unreconciled_deposit.amount);
                if(typeof data.bank_account_options[unreconciled_deposit.account_number] !== 'undefined') {
                    account = data.bank_account_options[unreconciled_deposit.account_number];
                }else if(typeof data.mobile_money_account_options[unreconciled_deposit.account_number] !== 'undefined') {
                    account = data.mobile_money_account_options[unreconciled_deposit.account_number];
                }else{
                    account = unreconciled_deposit.account_number;
                }
                toastr['info']('A new deposit of '+unreconciled_deposit.currency+' '+amount.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+' has been made to your group account '+account,'Transaction information.');
            }
            $.ajax({
                type: "GET",
                url: '<?php echo base_url("group/transaction_alerts/ajax_get_new_unreconciled_deposits/'+data.timestamp+'"); ?>',
                dataType : "html",
                success: function(response) {
                    $('#unreconciled_deposits_loading_row').slideUp();
                    $(response).insertAfter( "#unreconciled_deposits_loading_row" );
                    mApp.unblock('#unreconciled_deposits_loading_row td');
                    update_notification_counts(true);
                }
            });

        };
    }

});

$(window).on('load',function(){
    load_unreconciled_deposits_listing();
});

function update_notification_counts(notification_counter_increment = false){
    var number = 1;
    $('.unreconciled_deposit_count').each(function(){
        $(this).html(number);
        number++;
    });
    var count = number - 1;
    $('.deposits_count,.notification_deposits_count').each(function(){
        $(this).html(count);
    });
    if(count == 1){
        $('.notification_deposits_count_descriptor').each(function(){
            $(this).html("deposit");
        });
    }else{
        $('.notification_deposits_count_descriptor').each(function(){
            $(this).html("deposits");
        });
    }

    if(count == 0){
        $('#unreconciled_deposits_listing').html(" ");
        mApp.block({
            target: '#unreconciled_deposits_listing',
            overlayColor: 'white',
            animate: true
        });
        load_unreconciled_deposits_listing(notification_counter_increment);
        if(notification_counter_increment){
            //do nothing for now
        }else{
            var pending_bank_account_tasks_count = parseFloat($('#pending_bank_account_tasks_count').attr('data-pending-bank-account-tasks'));
            var new_pending_bank_account_tasks_count = pending_bank_account_tasks_count - 1;
            if(new_pending_bank_account_tasks_count==0){
                $('#pending_bank_account_tasks_notification_count').hide();
            }else if(new_pending_bank_account_tasks_count==1){
                $('#pending_bank_account_tasks_count_descriptor').html("task");
            }
            $('#pending_bank_account_tasks_count').attr('data-pending-bank-account-tasks',new_pending_bank_account_tasks_count);
            $('.pending_bank_account_tasks_count').each(function(){
                $(this).html(new_pending_bank_account_tasks_count);
            }); 
            $('#unreconciled_deposits_notification_holder').hide();
            $('.deposits_count').each(function(){
                $(this).hide();
            }); 
        
        }
    }
}

function load_unreconciled_deposits_listing(notification_counter_increment = false){
    $.ajax({
        context: this,
        type: "GET",
        url: '<?php echo base_url("group/transaction_alerts/ajax_get_unreconciled_deposits_listing"); ?>',
        dataType : "html",
            success: function(response) {
                $('#unreconciled_deposits_listing').html(response);
                mApp.unblock('#unreconciled_deposits_listing');
                if(notification_counter_increment){
                    var pending_bank_account_tasks_count = parseFloat($('#pending_bank_account_tasks_count').attr('data-pending-bank-account-tasks'));
                    var new_pending_bank_account_tasks_count = pending_bank_account_tasks_count + 1;
                    if(new_pending_bank_account_tasks_count==0){
                        $('#pending_bank_account_tasks_notification_count').show();
                    }else if(new_pending_bank_account_tasks_count==1){
                        $('#pending_bank_account_tasks_count_descriptor').html("task");
                    }
                    $('#pending_bank_account_tasks_count').attr('data-pending-bank-account-tasks',new_pending_bank_account_tasks_count);
                    $('.pending_bank_account_tasks_count').each(function(){
                        $(this).html(new_pending_bank_account_tasks_count);
                    }); 
                    $('#unreconciled_deposits_notification_holder').show();
                    $('.deposits_count').each(function(){
                        $(this).show();
                    });
                    var number = 1;
                    $('.unreconciled_deposit_count').each(function(){
                        $(this).html(number);
                        number++;
                    });
                    var count = number - 1;
                    $('.deposits_count,.notification_deposits_count').each(function(){
                        $(this).html(count);
                    });
                    if(count == 1){
                        $('.notification_deposits_count_descriptor').each(function(){
                            $(this).html("deposit");
                        });
                    }else{
                        $('.notification_deposits_count_descriptor').each(function(){
                            $(this).html("deposits");
                        });
                    }
                }
            }
        }
    );
}

function check_deposit_for(deposit_for_select){
    if(deposit_for_select.val()==''){
        deposit_for_select.parent().addClass('has-danger');
        return false;
    }else{
        deposit_for_select.parent().removeClass('has-danger');
        return true;
    }
}

function check_member(member_select){
    if(member_select.val()==''){
        member_select.parent().addClass('has-danger');
        return false;
    }else{
        member_select.parent().removeClass('has-danger');
        return true;
    }
}
function check_debtor(debtor_select){
    if(debtor_select.val()==''){
        debtor_select.parent().addClass('has-danger');
        return false;
    }else{
        debtor_select.parent().removeClass('has-danger');
        return true;
    }
}

function check_contribution(contribution_select){
    if(contribution_select.val()==''){
        contribution_select.parent().addClass('has-danger');
        return false;
    }else{
        contribution_select.parent().removeClass('has-danger');
        return true;
    }
}

function check_fine_category(fine_category_select){
    if(fine_category_select.val()==''){
        fine_category_select.parent().addClass('has-danger');
        return false;
    }else{
        fine_category_select.parent().removeClass('has-danger');
        return true;
    }
}

function check_amount(amount_input){
    if(amount_input.val()==''){
        amount_input.parent().addClass('has-danger');
        return false;
    }else{
        var amount = amount_input.val();
        regex = /^[0-9.,\b]+$/;;
        if(regex.test(amount)){
            amount_input.parent().removeClass('has-danger');
            return true;
        }else{ 
            amount_input.parent().addClass('has-danger');
            return false;
        }
    }
}

function check_miscellaneous_deposit_description(miscellaneous_deposit_description_textarea){
    if(miscellaneous_deposit_description_textarea.val()==''){
        miscellaneous_deposit_description_textarea.parent().addClass('has-danger');
        return false;
    }else{
        miscellaneous_deposit_description_textarea.parent().removeClass('has-danger');
        return true;
    }
}

function check_depositor(depositor_select){
    if(depositor_select.val()==''){
        depositor_select.parent().parent().addClass('has-danger');
        return false;
    }else{
        depositor_select.parent().parent().removeClass('has-danger');
        return true;
    }
}

function check_income_category(income_category_select){
    if(income_category_select.val()==''){
        income_category_select.parent().parent().addClass('has-danger');
        return false;
    }else{
        income_category_select.parent().parent().removeClass('has-danger');
        return true;
    }
}

function check_amount_payable(amount_payable_input){
    if(amount_payable_input.val()==''){
        amount_payable_input.parent().addClass('has-danger');
        return false;
    }else{
        var amount = amount_payable_input.val();
        regex = /^[0-9.,\b]+$/;;
        if(regex.test(amount)){
            amount_payable_input.parent().removeClass('has-danger');
            return true;
        }else{ 
            amount_payable_input.parent().addClass('has-danger');
            return false;
        }
    }
}

function check_bank_loan_disbursement_description(bank_loan_disbursement_description_textarea){
    if(bank_loan_disbursement_description_textarea.val()==''){
        return false;
    }else{
        bank_loan_disbursement_description_textarea.parent().parent().removeClass('has-danger');
        return true;
    }
}

function check_loan(loan_select){
    if(loan_select.val()==''){
        loan_select.parent().parent().addClass('has-danger');
        return false;
    }else{
        loan_select.parent().parent().removeClass('has-danger');
        return true;
    }
}

function check_external_loan(external_loan_select){
    if(external_loan_select.val()==''){
        external_loan_select.parent().parent().addClass('has-danger');
        return false;
    }else{
        external_loan_select.parent().parent().removeClass('has-danger');
        return true;
    }
}

function check_from_account_id(from_account_id_select){
    if(from_account_id_select.val()==''){
        from_account_id_select.parent().parent().addClass('has-danger');
        return false;
    }else{
        from_account_id_select.parent().parent().removeClass('has-danger');
        return true;
    }
}

function check_stock(stock_select){
    if(stock_select.val()==''){
        stock_select.parent().parent().addClass('has-danger');
        return false;
    }else{
        stock_select.parent().parent().removeClass('has-danger');
        return true;
    }
}

function check_number_of_shares_sold(number_of_shares_sold_input){
    if(number_of_shares_sold_input.val()==''){
        number_of_shares_sold_input.parent().addClass('has-danger');
        return false;
    }else{
        var amount = number_of_shares_sold_input.val();
        regex = /^[0-9\b]+$/;
        if(regex.test(amount)){
            number_of_shares_sold_input.parent().removeClass('has-danger');
            return true;
        }else{ 
            number_of_shares_sold_input.parent().addClass('has-danger').append('<div class="form-control-feedback">Please enter a valid number of shares sold, only numbers are allowed</div>');
            return false;
        }
    }
}

function check_price_per_share(price_per_share_input){
    if(price_per_share_input.val()==''){
        price_per_share_input.parent().addClass('has-danger');
        return false;
    }else{
        var amount = price_per_share_input.val();
        regex = /^[0-9.,\b]+$/;
        if(regex.test(amount)){
            price_per_share_input.parent().removeClass('has-danger');
            return true;
        }else{ 
            price_per_share_input.parent().addClass('has-danger').append('<div class="form-control-feedback">Please enter a valid number of shares sold, only numbers are allowed</div>');
            return false;
        }
    }
}

function check_asset(asset_select){
    if(asset_select.val()==''){
        asset_select.parent().parent().addClass('has-danger');
        return false;
    }else{
        asset_select.parent().parent().removeClass('has-danger');
        return true;
    }
}

function check_money_market_investment(money_market_investment_select){
    if(money_market_investment_select.val()==''){
        money_market_investment_select.parent().parent().addClass('has-danger');
        return false;
    }else{
        money_market_investment_select.parent().parent().removeClass('has-danger');
        return true;
    }
}

function isNumeric(num){
    return !isNaN(num)
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

</script>


<div class="row">
    <div class="col-md-12">
        <span class="error"></span>
        <div id="unreconciled_withdrawals_listing">
        </div>
    </div>
</div>
<div id="expense_fields" style="display:none;">
    <table>
        <tbody>
            <tr class="row">
                <td class="payment_fields" width="25%">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('expense_categories[0]',array(''=>'Select expense category')+$expense_category_options+array("0"=>"Add Expense Category"),'',' class="m-select2-append form-control expense_category" ');?>
                    </span>
                </td>
                <td class="payment_fields" width="25%">
                    <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                        <?php 

                            $textarea = array(
                                'name'=>'descriptions[0]',
                                'id'=>'',
                                'value'=> '',
                                'cols'=>25,
                                'rows'=>5,
                                'maxlength'=>'',
                                'class'=>'form-control expense_description_textarea m-input--air',
                                'placeholder'=>'Type expense description...'
                            ); 
                            echo form_textarea($textarea);
                        ?>
                    </div>
                </td>
                <td class="payment_fields" width="20%">
                    <?php echo form_input('amounts[0]','',' class="currency form-control m-input--air input-sm tooltips amount text-right" placeholder="Amount" ');?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="asset_purchase_payment_fields" style="display:none;">
    <table>
        <tbody>
            <tr class="row">
                <td class="payment_fields" width="25%">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('asset_ids[0]',array(''=>'Select asset')+$asset_options+array('0'=>"Add Asset"),'',' class="m-select2-append form-control asset_id" ');?>
                    </span>
                </td>
                <td class="payment_fields" width="25%">
                    <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                        <?php 

                            $textarea = array(
                                'name'=>'descriptions[0]',
                                'id'=>'',
                                'value'=> '',
                                'cols'=>25,
                                'rows'=>5,
                                'maxlength'=>'',
                                'class'=>'form-control asset_purchase_payment_description_textarea',
                                'placeholder'=>'Type asset purchase payment description...'
                            ); 
                            echo form_textarea($textarea);

                        ?>
                    </div>
                </td>
                <td class="payment_fields" width="20%">
                    <?php echo form_input('amounts[0]','',' class="currency form-control m-input--air tooltips amount input-sm text-right" placeholder="Amount" ');?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="stock_purchase_payment_fields" style="display:none;">
    <table>
        <tbody>
            <tr class="row">
                <td class="payment_fields" width="25%">
                    <?php echo form_input('stock_names[0]','',' class=" form-control tooltips stock_name m-input--air input-sm" placeholder="Stock Name" ');?>
                </td>
                <td class="payment_fields" width="25%">
                    <span>
                        <?php echo form_input('number_of_shares[0]','',' class=" form-control tooltips number_of_share m-input--air input-sm" placeholder="Number of shares" ');?>
                    </span>
                    <span>
                        <?php echo form_input('price_per_shares[0]','',' class=" form-control tooltips price_per_share m-input--air input-sm" placeholder="Price per share" ');?>
                    </span>
                </td>
                <td class="payment_fields" width="20%">
                    <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount text-right m-input--air input-sm" placeholder="Amount" ');?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="money_market_investment_fields" style="display:none;">
    <table>
        <tbody>
            <tr class="row">
                <td class="payment_fields" width="25%">
                    <?php echo form_input('money_market_investment_names[0]','',' class=" form-control tooltips money_market_investment_name m-input--air input-sm" placeholder="Money Market Investment" ');?>
                </td>
                <td class="payment_fields" width="25%">
                    <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                        <?php 

                            $textarea = array(
                                'name'=>'descriptions[0]',
                                'id'=>'',
                                'value'=> '',
                                'cols'=>25,
                                'rows'=>5,
                                'maxlength'=>'',
                                'class'=>'form-control money_market_investment_description_textarea m-input--air',
                                'placeholder'=>'Type asset purchase payment description...'
                            ); 
                            echo form_textarea($textarea);

                        ?>
                    </div>
                </td>
                <td class="payment_fields" width="20%">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount text-right m-input--air input-sm" placeholder="Amount" ');?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="money_market_investment_top_up_fields" style="display:none;">
    <table>
        <tbody>
            <tr class="row">
                <td class="payment_fields" width="25%">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('money_market_investment_ids[0]',array(''=>'Select Money Market Investment')+$money_market_investment_options,'',' class="m-select2-append form-control tooltips money_market_investment_id" placeholder="Money Market Investment" ');?>
                    </span>
                </td>
                <td class="payment_fields" width="25%">
                    <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                        <?php 

                            $textarea = array(
                                'name'=>'descriptions[0]',
                                'id'=>'',
                                'value'=> '',
                                'cols'=>25,
                                'rows'=>5,
                                'maxlength'=>'',
                                'class'=>'form-control money_market_investment_top_up_description_textarea',
                                'placeholder'=>'Type money market top up description...'
                            ); 
                            echo form_textarea($textarea);

                        ?>
                    </div>
                </td>
                </td>
                <td class="payment_fields" width="20%">
                    <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount text-right m-input--air input-sm" placeholder="Amount" ');?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="contribution_refund_fields" style="display:none;">
    <table>
        <tbody>
            <tr class="row">
                <td class="payment_fields" width="25%">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('members[0]',array(''=>'Select Member')+$this->active_group_member_options+array("0"=>"Add Member"),'',' class="m-select2-append form-control tooltips member" placeholder="" ');?>
                    </span>
                </td>
                <td class="payment_fields" width="25%">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('contributions[0]',array(''=>'Select Contribution')+$contribution_options,'',' class="m-select2-append form-control tooltips contribution" placeholder="" ');?>
                    </span>
                </td>
                </td>
                <td class="payment_fields" width="20%">
                    <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount text-right m-input--air input-sm" placeholder="Amount" ');?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="bank_loan_repayment_fields" style="display:none;">
    <table>
        <tbody>
            <tr class="row">
                <td class="payment_fields" width="25%">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('bank_loan_ids[0]',array(''=>'Select Bank Loan')+$bank_loan_options,'',' class="m-select2-append form-control tooltips bank_loan_id" placeholder="" ');?>
                    </span>
                </td>
                <td class="payment_fields" width="25%">
                    <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                        <?php 

                            $textarea = array(
                                'name'=>'descriptions[0]',
                                'id'=>'',
                                'value'=> '',
                                'cols'=>25,
                                'rows'=>5,
                                'maxlength'=>'',
                                'class'=>'form-control bank_loan_payment_description_textarea m-input--air',
                                'placeholder'=>'Type bank loan payment description...'
                            ); 
                            echo form_textarea($textarea);
                        ?>
                    </div>
                </td>
                <td class="payment_fields" width="20%">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount text-right m-input--air input-sm" placeholder="Amount" ');?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="outgoing_bank_transfer_fields" style="display:none;">
    <table>
        <tbody>
            <tr class="row">
                <td class="payment_fields" width="25%">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('to_account_ids[0]',array(''=>'Select Recipient Account')+$to_account_options+array('0'=>"Add Account"),'',' class="m-select2-append form-control tooltips to_account_id" placeholder="" ');?>
                    </span>
                </td>
                <td class="payment_fields" width="25%">
                    <div class="margin-top-5" data-original-title="" data-container="body">
                        <?php 
                            $textarea = array(
                                'name'=>'descriptions[0]',
                                'id'=>'',
                                'value'=> '',
                                'cols'=>25,
                                'rows'=>5,
                                'maxlength'=>'',
                                'class'=>'form-control outgoing_bank_transfer_description_textarea',
                                'placeholder'=>'Type funds transfer description...'
                            ); 
                            echo form_textarea($textarea);
                        ?>
                    </div>
                </td>
                <td class="payment_fields" width="20%">
                    <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount text-right m-input--air input-sm" placeholder="Amount" ');?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="loan_disbursement_fields" style="display:none;">
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
                    <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount text-right m-input--air input-sm" placeholder="Amount" ');?>
                </td>   
            </tr>
        </tbody>
    </table>
</div>
<div id="dividend_fields" style="display:none;">
    <table>
        <tbody>
            <tr class="row">
                <td class="payment_fields" width="25%">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="m-select2-append form-control member" ');?>
                    </span>
                </td>
                <td class="payment_fields" width="25%">
                    <div class="margin-top-5" data-original-title="" data-container="body">
                        <?php 
                            $textarea = array(
                                'name'=>'descriptions[0]',
                                'id'=>'',
                                'value'=> '',
                                'cols'=>25,
                                'rows'=>5,
                                'maxlength'=>'',
                                'class'=>'form-control dividend_description_textarea',
                                'placeholder'=>'Type dividend description...'
                            ); 
                            echo form_textarea($textarea);
                        ?>
                    </div>
                </td>
                <td class="payment_fields" width="20%">
                    <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount text-right m-input--air input-sm" placeholder="Amount" ');?>
                </td>   
            </tr>
        </tbody>
    </table>
</div>
<div id='append-new-line' style="display:none;">
    <table>
        <tbody id='append-place-holder'>
            <tr>
                <th scope="row" class='count' width="2%">1</th>
                <td class="withdrawal_for_cell" width="20%">
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('withdrawal_fors[]',array(''=>'Select withdrawal for')+translate($withdrawal_for_options),'',' class="form-control m-input m-select2-append withdrawal_for"');?>
                    </span>
                </td>
                <td class='particulars_place_holder' width="70%"  colspan="3">
                </td>
                <td width="5%">
                    <a href='javascript:;' class="remove-line">
                        <i class="text-danger la la-trash"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="modal fade" id="reconcile_withdrawal_pop_up" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
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
               
                <?php echo form_open($this->uri->uri_string(),' class="m-form m-form--state reconcile_withdrawal_form"'); ?>
                    <span class="error"></span>
                    <?php echo form_hidden('transaction_alert_id','','');?>
                    <?php echo form_hidden('transaction_alert_amount','','');?>
                    <div class="table-responsive">
                        <table class="table table-multiple-items">
                            <thead>
                                <tr>
                                    <th width="2%">#</th>
                                    <th nowrap width="20%">
                                        <?php echo translate('Withdrawal For');?>
                                        <span class="required">*</span>
                                    </th>
                                    <th colspan="3" width="70%">
                                        <?php echo translate('Withdrawal Particulars');?>
                                        <span class="required">*</span>
                                    </th>
                                    <th width="5%">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id='append-place-holder'>
                                <tr>
                                    <th scope="row" class='count' width="1%">1</th>
                                    <td class="withdrawal_for_cell" width="5%">
                                        <span class="m-select2-sm m-input--air">
                                            <?php echo form_dropdown('withdrawal_fors[]',array(''=>'Select withdrawal for')+translate($withdrawal_for_options),'',' class="form-control m-input m-select2 withdrawal_for"');?>
                                        </span>
                                    </td>
                                    <td class='particulars_place_holder' colspan="3">
                                    </td>
                                    <td width="5%">
                                        <a href='javascript:;' class="remove-line">
                                            <i class="text-danger la la-trash"></i>
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
                            <button type="submit" class="btn btn-primary btn-sm" id="submit_reconcile_withdrawal">
                                <?php echo translate('Save Changes');?>
                            </button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<a class="inline d-none" data-toggle="modal" data-target="#add_expense_category_popup" data-title="Add Asset category" id="add_expense_category"  data-backdrop="static" data-keyboard="false"><?php echo translate('Add Asset Category');?></a>

<a class="inline d-none" data-toggle="modal" data-target="#add_asset_popup" data-title="Add Asset category " data-id="add_asset" id="add_asset"  data-backdrop="static" data-keyboard="false"><?php echo translate('Add Asset Category');?></a>

<a class="inline d-none" data-toggle="modal" data-target="#create_new_member_pop_up" data-title="Add Member" data-id="create_member" id="add_new_member"  data-backdrop="static" data-keyboard="false">
    <?php echo translate('Add Member');?>
</a>

<a class="inline d-none" data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account"  data-backdrop="static" data-keyboard="false"><?php echo translate('Add Account');?></a>

<a class="inline d-none" data-toggle="modal" data-target="#asset_category_form" data-title="Add Asset category " data-id="asset_category_form" id="add_asset_category">
<?php echo translate('Add Asset Category');?></a>

<div class="modal fade" id="add_expense_category_popup" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Add Group Expense Category');?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open($this->uri->uri_string(),'class="add_expense_category_form m-form--group-seperator-dashe-d form_submit m-form m-form--state" id="add_expense_category_form" role="form"'); ?>
                    <span class="error"></span>
                    <div class="form-group m-form__group row">
                        <div class="col-md-12">
                            <label>
                                <?php echo translate('Group Expense Category Name');?>
                                <span class="required">*</span>
                            </label>
                            <?php echo form_hidden('slug','',''); ?>
                            <?php echo form_input('name',"",'class="form-control slug_parent" placeholder="Expense Category Name" id="name"'); ?>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <div class="col-md-12">
                            <label>
                                <?php echo translate('Group Expense Category Description');?>
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
                                        'placeholder'=>'Group Expense Category Description'
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
                            <button type="submit" class="btn btn-primary" id="add_expense_category_submit">
                                <?php echo translate('Save Changes');?>                            
                            </button>
                        </div>
                    </div>
                <?php echo form_close() ?>
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

<script>
    $(document).ready(function(){
        SnippetAssetCreateCategory.init(false,true);
        mApp.block('#unreconciled_withdrawals_listing',{
            overlayColor: 'white',
            animate: true
        });

        var form;

        var unreconciled_withdrawal_id;
        //add member modal close eventt
        $('#create_new_member_pop_up').on('hidden.bs.modal', function () {
            $("#create_new_member_pop_up input[type=text],#create_new_member_pop_up textarea").val("");
            $("#create_new_member_pop_up input[type=checkbox]").prop('checked',false);
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

        $(document).on('click','.toggle_withdrawal_description',function(){
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

        //reconcile withdrawal_pop_up modal close eventt
        $('#reconcile_withdrawal_pop_up').on('hidden.bs.modal', function () {
            // //clear input data
            $(':input','#reconcile_withdrawal_pop_up')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
        });

        $(document).on('click','.reconcile_withdrawal',function(){
            $('#reconcile_withdrawal_pop_up #submit_reconcile_withdrawal,#reconcile_withdrawal_pop_up #close_modal').attr('disabled',true);
            $('#reconcile_withdrawal_pop_up .transaction_details,#reconcile_withdrawal_pop_up .reconcile_withdrawal_form,#reconcile_withdrawal_pop_up .modal-footer').hide();
            unreconciled_withdrawal_id = $(this).attr('id');
        });

        $('#reconcile_withdrawal_pop_up').on('shown.bs.modal',function(){   
            mApp.block('#reconcile_withdrawal_pop_up .modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading transaction...'
            });
            $('.reconcile_withdrawal_form').find('*').each(function(){
                $(this).removeClass('has-danger');
            });
            FormInputMask.init();
            var form  = $("#reconcile_withdrawal_pop_up");
            $.post('<?php echo base_url("group/transaction_alerts/ajax_get"); ?>',{'id':unreconciled_withdrawal_id,},function(data){
                if(isJson(data)){
                    var transaction_alert = $.parseJSON(data);
                    form.find('.transaction_details #transaction_date').html(transaction_alert.formatted_transaction_date);
                    form.find('.transaction_details #transaction_particulars').html(transaction_alert.particulars);
                    form.find('.transaction_details #transaction_amount').html(transaction_alert.formatted_amount);
                    form.find('input[name="transaction_alert_id"]').val(transaction_alert.id);
                    form.find('input[name="transaction_alert_amount"]').val(transaction_alert.amount);
                    $("#reconcile_withdrawal_pop_up .table-multiple-items #append-place-holder").html($('#append-new-line tbody').html());
                    $('#reconcile_withdrawal_pop_up .m-select2-append').select2({
                        width:'100%',
                        placeholder:{
                            id: '-1',
                            text: "--Select option--",
                        }, 
                    });
                    mApp.unblock('#reconcile_withdrawal_pop_up .modal-body', {});
                    $('#reconcile_withdrawal_pop_up .transaction_details,#reconcile_withdrawal_pop_up .reconcile_withdrawal_form,#reconcile_withdrawal_pop_up .member_notifications, #reconcile_withdrawal_pop_up .modal-footer').slideDown();
                    $('#reconcile_withdrawal_pop_up #submit_reconcile_withdrawal,#reconcile_withdrawal_pop_up #close_modal').attr('disabled',false);
                }else{
                    alert('Error: Could not find transaction alert.');
                    $('.close').click();
                }
            });
        });

        $(document).on('click','#add-new-line',function(){
            var html = $('#append-new-line tbody').html();
            html = html.replace_all('checker','');
            $('.reconcile_withdrawal_form').find('#append-place-holder').append(html);
            $('#reconcile_withdrawal_pop_up .m-select2-append').each(function(){
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
            update_field_names($('.reconcile_withdrawal_form'));
            FormInputMask.init();
        });

        $(document).on('click','#reconcile_withdrawal_pop_up a.remove-line',function(event){
            if($('#reconcile_withdrawal_pop_up .count').length == 1){
                $('.reconcile_withdrawal_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry:</strong> You cannot remove the last line</div>').slideDown('slow');
            }else{
                $(this).parent().parent().remove();
                var number = 1;
                $('#reconcile_withdrawal_pop_up .count').each(function(){
                    $(this).text(number);
                    number++;
                });
            }
        });

        $(document).on('change','.withdrawal_for',function(){
            $(this).parent().parent().parent().find('.payment_fields').each(function(){
                $(this).remove();
            });
            var element = $(this).parent().parent().parent().find('.withdrawal_for_cell');
            if($(this).val()==1){
                var html = $('#expense_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            }else if($(this).val()==2){
                var html = $('#asset_purchase_payment_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            }else if($(this).val()==3){
                var html = $('#loan_disbursement_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
                $(this).parent().parent().parent().find('.loan').prop('disabled',true);
            }else if($(this).val()==4){
                var html = $('#stock_purchase_payment_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            }else if($(this).val()==5){
                var html = $('#money_market_investment_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            }else if($(this).val()==6){
                var html = $('#money_market_investment_top_up_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            }else if($(this).val()==7){
                var html = $('#contribution_refund_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            }else if($(this).val()==8){
                var html = $('#bank_loan_repayment_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            }else if($(this).val()==9){
                var html = $('#outgoing_bank_transfer_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            }else if($(this).val()==11){
                var html = $('#dividend_fields .row').html();
                $(element).after(html);
                $(this).parent().parent().parent().find('.particulars_place_holder').remove();
            }else{
                $(element).after('<td class="particulars_place_holder" colspan="3"></td>');
            }
            update_field_names($('.reconcile_withdrawal_form'));
            FormInputMask.init();
            $('#reconcile_withdrawal_pop_up .m-select2-append').each(function(){
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

        $(document).on('change','.member',function(){
            var me = $(this);
            var row = me.parent().parent().parent().parent();
            if(row.find('.withdrawal_for').val() == 3){
                mApp.block(row.find('.loan-to-populate .select2'), {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                var member_id = me.val();
                var attribute = me.attr('name');
                var url = '<?php echo site_url('group/loans/ajax_get_active_member_loans')?>';
                    $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "html",
                    data: {'member_id': member_id , 'attribute':attribute},
                    success: function(res) 
                    {
                        me.parent().parent().parent().find('.change-loan').html(res);
                        me.parent().parent().parent().find('.loan').select2();
                        mApp.unblock(row.find(".loan-to-populate .select2"));
                        if(member_id==""){
                            me.parent().parent().parent().find('.loan').prop('disabled',true);
                        }else{
                            $(".table-multiple-items .loan").select2({
                                width:'100%',
                                language: 
                                    {
                                     noResults: function() {
                                        return '<a class="stacked_inline" data-row="" data-toggle="modal" data-content="#loans_form" data-title="Add Loan" data-id="add_loan" id="add_loan" href="#">Add Loan</a>';
                                    }
                                },
                                escapeMarkup: function (markup) {
                                    return markup;
                                }
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError)
                    {
                        
                    }

                });
            }
        });

        function update_field_names(form){

            if (typeof form == 'undefined') {
                //do nothin for now
            }else{
                var number = 1;
                form.find('.count').each(function(){
                    $(this).text(number);
                    $(this).parent().find('.withdrawal_for').attr('name','withdrawal_fors['+(number-1)+']');
                    $(this).parent().find('.expense_category').attr('name','expense_categories['+(number-1)+']');
                    $(this).parent().find('.amount').attr('name','amounts['+(number-1)+']');
                    $(this).parent().find('.asset_id').attr('name','asset_ids['+(number-1)+']');
                    $(this).parent().find('.bank_loan_id').attr('name','bank_loan_ids['+(number-1)+']');
                    $(this).parent().find('.asset_purchase_payment_description_textarea').attr('name','descriptions['+(number-1)+']');
                    $(this).parent().find('.expense_description_textarea').attr('name','descriptions['+(number-1)+']');
                    $(this).parent().find('.stock_name').attr('name','stock_names['+(number-1)+']');
                    $(this).parent().find('.number_of_share').attr('name','number_of_shares['+(number-1)+']');
                    $(this).parent().find('.price_per_share').attr('name','price_per_shares['+(number-1)+']');
                    $(this).parent().find('.money_market_investment_name').attr('name','money_market_investment_names['+(number-1)+']');
                    $(this).parent().find('.money_market_investment_id').attr('name','money_market_investment_ids['+(number-1)+']');
                    $(this).parent().find('.money_market_investment_description_textarea').attr('name','descriptions['+(number-1)+']');
                    $(this).parent().find('.money_market_investment_top_up_description_textarea').attr('name','descriptions['+(number-1)+']');
                    $(this).parent().find('.bank_loan_payment_description_textarea').attr('name','descriptions['+(number-1)+']');
                    $(this).parent().find('.member').attr('name','members['+(number-1)+']');
                    $(this).parent().find('.contribution').attr('name','contributions['+(number-1)+']');
                    $(this).parent().find('.outgoing_bank_transfer_description_textarea').attr('name','descriptions['+(number-1)+']');
                    $(this).parent().find('.to_account_id').attr('name','to_account_ids['+(number-1)+']');
                    $(this).parent().find('.loan').attr('name','loans['+(number-1)+']');
                    number++;
                }); 
            }
        }

        $(document).on('submit','.reconcile_withdrawal_form',function(e){
            e.preventDefault();
            $('#reconcile_withdrawal_pop_up .error').html('').slideUp();
            RemoveDangerClass(form);
            var form = $(this);
            var entries_are_valid = true;
            var totals_are_valid = true;
            var total_amount = 0;
            var error_message = "";
           
            $('.reconcile_withdrawal_form input.amount').each(function(){
                if(check_amount($(this))){
                    total_amount += parseFloat($(this).val().replace(/,/g, ''));
                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form select.expense_category').each(function(){
                if(check_expense_category($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form select.asset_id').each(function(){
                if(check_asset($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form input.stock_name').each(function(){
                if(check_stock_name($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form input.number_of_share').each(function(){
                if(check_number_of_shares($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form input.price_per_share').each(function(){
                if(check_price_per_share($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form input.money_market_investment_name').each(function(){
                if(check_money_market_investment_name($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form input.amount_payable').each(function(){
                if(check_amount_payable($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form select.money_market_investment_id').each(function(){
                if(check_money_market_investment($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form select.member').each(function(){
                if(check_member($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form select.contribution').each(function(){
                if(check_contribution($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form select.loan').each(function(){
                if(check_loan($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form select.bank_loan_id').each(function(){
                if(check_bank_loan($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form select.to_account_id').each(function(){
                if(check_account($(this))){

                }else{
                    entries_are_valid = false;
                }
            });
            $('.reconcile_withdrawal_form select.withdrawal_for').each(function(){
                if(check_withdrawal_for($(this))){

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
                error_message = "<p>Kindly make sure the amount reconciled adds up to the amount withdrawn of "+transaction_alert_amount+".</p>";
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
                        $('#submit_reconcile_withdrawal').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
                        $.ajax({
                            type: "POST",
                            url: '<?php echo base_url("group/transaction_alerts/ajax_reconcile_withdrawal"); ?>',
                            data: form.serialize(),
                            success: function(response) {
                                if(response=='success'){
                                    $('#reconcile_withdrawal_pop_up #close_modal').click();
                                    swal("Reconciled!","Your transaction has been reconciled.","success");
                                    mApp.unblock('.modal-body');
                                    $('#submit_reconcile_withdrawal').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                                    $('#unreconciled_withdrawal_row_'+unreconciled_withdrawal_id+' .reconcile_action ').html('<span class="m-badge m-badge--primary m-badge--wide"> Reconciled </span>');
                                    $('#unreconciled_withdrawal_row_'+unreconciled_withdrawal_id).addClass('success').slideDown('slow').removeClass('unreconciled_withdrawal_count').delay(3000).fadeOut(3000,function(){
                                        var number = 1;
                                        $('.unreconciled_withdrawal_count').each(function(){
                                            $(this).html(number);
                                            number++;
                                        });
                                        update_notification_counts();
                                    });
                                }else{
                                    e.value&&swal("Error!","There were errors reconciling this transaction.","error")
                                    $('.reconcile_withdrawal_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response+'</div>').slideDown('slow');
                                    mApp.unblock('.modal-body');
                                    $('#submit_reconcile_withdrawal').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                                }
                            }
                        });
                    }
                });
            }else{
                //show message
                $('.reconcile_withdrawal_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><p>Kindly review the fields marked in red, enter the values requested before you can proceed.</p>'+error_message+'</div>').slideDown('slow');
            }
        });
        
        $(document).on('submit','#add_expense_category_form',function(e){
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
            $('#add_expense_category_popup .error').html('').slideUp();
            $('#add_expense_category_submit').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/expense_categories/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var data = $.parseJSON(response);
                        if(data.status == 1){
                            $('select.expense_category').each(function(){
                                $(this).append('<option value="' + data.expense_category.id + '">' + data.expense_category.name + '</option>').trigger('change');
                            });
                            $('#reconcile_withdrawal_pop_up select[name="expense_categories['+current_row+']"]').val(""+data.expense_category.id).trigger('change');
                            $('#add_expense_category_popup .close').trigger('click');
                            toastr['success']('You have successfully added a new expense category, you can now select it in the accounts dropdown.','Expense category added successfully');
                        }else{
                            $('#add_expense_category_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
                            if(data.validation_errors){
                                if(data.validation_errors.name && data.validation_errors.slug){

                                    $.each(data.validation_errors, function( key, value ) {
                                        if(key == 'name'){
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('#add_expense_category_popup input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        }
                                    });
                                }else{
                                    $.each(data.validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('#add_expense_category_popup input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                            }
                        }
                    }else{
                        $('#add_expense_category_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').slideDown('slow');
                    }
                    mApp.unblock('.modal-body');
                    $('#add_expense_category_submit').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
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
                            $('select.asset_id').each(function(){
                                $(this).append('<option value="' + data.asset.id + '">' + data.asset.name +' </option>').trigger('change');
                            });
                            $('#reconcile_withdrawal_pop_up select[name="asset_ids['+current_row+']"]').val(data.asset.id).trigger('change');
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
                            $('#reconcile_withdrawal_pop_up select[name="members['+current_row+']"]').val(data.member.id).trigger('change');
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
                            $('select.to_account_id').each(function(){
                                $(this).append('<option value="bank-' + data.bank_account.id + '">'+data.bank_account.bank_details+' - ' + data.bank_account.account_name + ' ('+data.bank_account.account_number+')</option>').trigger('change');
                            });
                            $('#reconcile_withdrawal_pop_up select[name="to_account_ids['+current_row+']"]').val("bank-"+data.bank_account.id).trigger('change');
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
                            $('select.to_account_id').each(function(){
                                $(this).append('<option value="sacco-' + data.sacco_account.id + '">'+data.sacco_account.sacco_details+' - ' + data.sacco_account.account_name + ' ('+data.sacco_account.account_number+')</option>').trigger('change');
                            });
                            $('#reconcile_withdrawal_pop_up select[name="to_account_ids['+current_row+']"]').val("sacco-"+data.sacco_account.id).trigger('change');
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
                            $('select.to_account_id').each(function(){
                                $(this).append('<option value="mobile-' + data.mobile_money_account.id + '">'+data.mobile_money_account.mobile_money_provider_details+' - ' + data.mobile_money_account.account_name + ' ('+data.mobile_money_account.account_number+')</option>').trigger('change');
                            });
                            $('#reconcile_withdrawal_pop_up select[name="to_account_ids['+current_row+']"]').val("mobile-"+data.mobile_money_account.id).trigger('change');
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
                            $('select.to_account_id').each(function(){
                                $(this).append('<option value="petty-' + data.petty_cash_account.id + '">' + data.petty_cash_account.account_name + '</option>').trigger('change');
                            });
                            $('#reconcile_withdrawal_pop_up select[name="to_account_ids['+current_row+']"]').val("petty-"+data.petty_cash_account.id).trigger('change');
                            $('#create_new_account_pop_up .close').trigger('click');
                            toastr['success']('You have successfully added a new petty cash account, you can now select it in the accounts dropdown.','Petty cash account added successfully');
                        }else{
                            $('#petty_cash_account_tab .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown('slow');
                            if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    if(data.validation_errors.slug && data.validation_errors.account_name){
                                        if(data.validation_errors.account_name){
                                            if(key == 'account_name'){
                                                //skip
                                            }else{
                                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                                $('#petty_cash_account_tab input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            }
                                        }
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

        $(document).on('blur','.reconcile_withdrawal_form input.amount',function(){
            check_amount($(this));
        });

        $(document).on('change','.reconcile_withdrawal_form select.expense_category',function(){
            check_expense_category($(this));
        });

        $(document).on('change','.reconcile_withdrawal_form select.asset_id',function(){
            check_asset($(this));
        });

        $(document).on('blur','.reconcile_withdrawal_form input.stock_name',function(){
            check_stock_name($(this));
        });

        $(document).on('blur','.reconcile_withdrawal_form input.number_of_share',function(){
            check_number_of_shares($(this));
        });

        $(document).on('blur','.reconcile_withdrawal_form input.price_per_share',function(){
            check_price_per_share($(this));
        });

        $(document).on('blur','.reconcile_withdrawal_form input.money_market_investment_name',function(){
            check_money_market_investment_name($(this));
        });
        
        $(document).on('blur','.reconcile_withdrawal_form input.amount_payable',function(){
            check_amount_payable($(this));
        });

        $(document).on('change','.reconcile_withdrawal_form select.money_market_investment_id',function(){
            check_money_market_investment($(this));
        });

        $(document).on('change','.reconcile_withdrawal_form select.member',function(){
            check_member($(this));
        });

        $(document).on('change','.reconcile_withdrawal_form select.contribution',function(){
            check_contribution($(this));
        });

        $(document).on('change','.reconcile_withdrawal_form select.loan',function(){
            check_loan($(this));
        });

        $(document).on('change','.reconcile_withdrawal_form select.bank_loan_id',function(){
            check_bank_loan($(this));
        });

        $(document).on('change','.reconcile_withdrawal_form select.to_account_id',function(){
            check_account($(this));
        });

        $(document).on('change','select.withdrawal_for',function(){
            check_withdrawal_for($(this));
        });

        var current_row = 0;
        
        $(document).on('select2:open','.expense_category', function(e) {
            // do something
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('select2:open','.asset_id', function(e) {
            // do something
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('select2:open','.member', function(e) {
            // do something
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('select2:open','.loan', function(e) {
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

        $(document).on('change','.expense_category',function(){
            if($(this).val()=='0'){
                $('#add_expense_category').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('select2:open','.to_account_id', function(e) {
            // do something
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        });

        $(document).on('change','.asset_id',function(){
            if($(this).val()=='0'){
                $('#add_asset').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.member',function(){
            if($(this).val()=='0'){
                $('#add_new_member').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.asset_category_id',function(){
            console.log($(this).val());
            if($(this).val() == '0'){
                $('#add_asset_category').trigger('click');
                $(this).val('').trigger('change');
            }
        });

        $(document).on('change','.loan',function(){
            if($(this).val()=='0'){
                $('#add_loan').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.contribution',function(){
            if($(this).val()=='0'){
                $('#add_contribution').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.to_account_id',function(){
            if($(this).val()=='0'){
                $('#add_new_account').trigger('click');
                $(this).val("").trigger('change');
            }
        });

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
            var unreconciled_withdrawal_id = $(this).attr('data-id');
            $.ajax({      
            url:'<?php echo site_url('group/transaction_alerts/bulk_mark_as_reconciled')?>',
            type:'POST',
            data:{'transaction_alert_ids':selected_ids},
            success: function(response){

                var result = $.parseJSON(response);

                if(result.status ==1){
                toastr['success'](result.message);
                $.each(selected_ids, function(key,unreconciled_withdrawal_id) {
                    $('#unreconciled_withdrawal_row_'+unreconciled_withdrawal_id).addClass('success').slideDown('slow').delay(3000).fadeOut(3000,function(){
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
            var source = new EventSource("<?php echo site_url('group/transaction_alerts/check_new_unreconciled_withdrawals/"+timestamp+"'); ?>");
            source.onmessage = function(event) {
                $('#unreconciled_withdrawals_loading_row').slideDown('slow');
                mApp.block('#unreconciled_withdrawals_loading_row td', {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Loading...'
                });
                var data = $.parseJSON(event.data);
                var unreconciled_withdrawals = data.unreconciled_withdrawals;
                var unreconciled_withdrawal;
                var amount;
                var account;
                for(var i = 0; i < unreconciled_withdrawals.length; i++){
                    unreconciled_withdrawal = unreconciled_withdrawals[i];
                    amount = (unreconciled_withdrawal.amount);
                    if(typeof data.bank_account_options[unreconciled_withdrawal.account_number] !== 'undefined') {
                        account = data.bank_account_options[unreconciled_withdrawal.account_number];
                    }else if(typeof data.mobile_money_account_options[unreconciled_withdrawal.account_number] !== 'undefined') {
                        account = data.mobile_money_account_options[unreconciled_withdrawal.account_number];
                    }else{
                        account = unreconciled_withdrawal.account_number;
                    }
                    toastr['info']('A new withdrawal of '+unreconciled_withdrawal.currency+' '+amount.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+' has been made to your group account '+account,'Transaction information.');
                }
                $.ajax({
                    type: "GET",
                    url: '<?php echo base_url("group/transaction_alerts/ajax_get_new_unreconciled_withdrawals/'+data.timestamp+'"); ?>',
                    dataType : "html",
                    success: function(response) {
                        $('#unreconciled_withdrawals_loading_row').slideUp();
                        $(response).insertAfter( "#unreconciled_withdrawals_loading_row" );
                        mApp.unblock('#unreconciled_withdrawals_loading_row td');
                        update_notification_counts(true);
                    }
                });

            };
        }

    });

    $(window).on('load',function(){
        load_unreconciled_withdrawals_listing();
    });

    function update_notification_counts(notification_counter_increment = false){
        var number = 1;
        $('.unreconciled_withdrawal_count').each(function(){
            $(this).html(number);
            number++;
        });
        var count = number - 1;
        $('.withdrawals_count,.notification_withdrawals_count').each(function(){
            $(this).html(count);
        });
        if(count == 1){
            $('.notification_withdrawals_count_descriptor').each(function(){
                $(this).html("withdrawal");
            });
        }else{
            $('.notification_withdrawals_count_descriptor').each(function(){
                $(this).html("withdrawals");
            });
        }

        if(count == 0){
            $('#unreconciled_withdrawals_listing').html(" ");
            mApp.block('#unreconciled_withdrawals_listing', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            load_unreconciled_withdrawals_listing(notification_counter_increment);
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
                $('#unreconciled_withdrawals_notification_holder').hide();
                $('.withdrawals_count').each(function(){
                    $(this).hide();
                }); 
            
            }
        }
    }

    function load_unreconciled_withdrawals_listing(notification_counter_increment = false){
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("group/transaction_alerts/ajax_get_unreconciled_withdrawals_listing"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#unreconciled_withdrawals_listing').html(response);
                    mApp.unblock('#unreconciled_withdrawals_listing');
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
                        $('#unreconciled_withdrawals_notification_holder').show();
                        $('.withdrawals_count').each(function(){
                            $(this).show();
                        });
                        var number = 1;
                        $('.unreconciled_withdrawal_count').each(function(){
                            $(this).html(number);
                            number++;
                        });
                        var count = number - 1;
                        $('.withdrawals_count,.notification_withdrawals_count').each(function(){
                            $(this).html(count);
                        });
                        if(count == 1){
                            $('.notification_withdrawals_count_descriptor').each(function(){
                                $(this).html("withdrawal");
                            });
                        }else{
                            $('.notification_withdrawals_count_descriptor').each(function(){
                                $(this).html("withdrawals");
                            });
                        }
                    }
                }
            });
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

    function check_expense_category(expense_category_select){
        if(expense_category_select.val()==''){
            expense_category_select.parent().parent().addClass('has-danger');
            return false;
        }else{
            expense_category_select.parent().parent().removeClass('has-danger');
            return true;
        }
    }

    function check_asset(asset_select){
        if(asset_select.val()==''){
            asset_select.parent().addClass('has-danger');
            return false;
        }else{
            asset_select.parent().removeClass('has-danger');
            return true;
        }
    }

    function check_stock_name(stock_name_input){
        if(stock_name_input.val()==''){
            stock_name_input.parent().addClass('has-danger');
            return false;
        }else{
            stock_name_input.parent().removeClass('has-danger');
            return true;
        }
    }

    function check_number_of_shares(number_of_shares_input){
        if(number_of_shares_input.val()==''){
            number_of_shares_input.parent().addClass('has-danger');
            return false;
        }else{
            var amount = number_of_shares_input.val();
            regex = /^[0-9\b]+$/;;
            if(regex.test(amount)){
                number_of_shares_input.parent().removeClass('has-danger');
                return true;
            }else{ 
                number_of_shares_input.parent().addClass('has-danger').append('<div class="form-control-feedback">Please enter a valid number of shares sold, only numbers are allowed</div>');

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
            regex = /^[0-9.,\b]+$/;;
            if(regex.test(amount)){
                price_per_share_input.parent().removeClass('has-danger');
                return true;
            }else{ 
                price_per_share_input.parent().addClass('has-danger').append('<div class="form-control-feedback">Please enter a valid number of shares sold, only numbers are allowed</div>');
                return false;
            }
        }
    }

    function check_money_market_investment_name(money_market_investment_name_input){
        if(money_market_investment_name_input.val()==''){
            money_market_investment_name_input.parent().addClass('has-danger');
            return false;
        }else{
            money_market_investment_name_input.parent().removeClass('has-danger');
            return true;
        }
    }

    function check_amount_payable(amount_payable_input){
        if(amount_payable_input.val()==''){
            amount_payable_input.parent().addClass('has-danger');
            return false;
        }else{
            var amount = $(this).val();
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

    function check_money_market_investment(money_market_investment_select){
        if(money_market_investment_select.val()==''){
            money_market_investment_select.parent().addClass('has-danger');
            return false;
        }else{
            money_market_investment_select.parent().removeClass('has-danger');
            return true;
        }
    }

    function check_member(member_select){
        if(member_select.val()==''){
            member_select.parent().parent().addClass('has-danger');
            return false;
        }else{
            member_select.parent().parent().removeClass('has-danger');
            return true;
        }
    }

    function check_contribution(contribution_select){
        if(contribution_select.val()==''){
            contribution_select.parent().parent().addClass('has-danger');
            return false;
        }else{
            contribution_select.parent().parent().removeClass('has-danger');
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

    function check_bank_loan(bank_loan_select){
        if(bank_loan_select.val()==''){
            bank_loan_select.parent().parent().addClass('has-danger');
            return false;
        }else{
            bank_loan_select.parent().parent().removeClass('has-danger');
            return true;
        }
    }

    function check_account(account_select){
        if(account_select.val()==''){
            account_select.parent().parent().addClass('has-danger');
            return false;
        }else{
            account_select.parent().parent().removeClass('has-danger');
            return true;
        }
    }

    function check_withdrawal_for(withdrawal_for_select){
        if(withdrawal_for_select.val()==''){
            withdrawal_for_select.parent().parent().addClass('has-danger');
            return false;
        }else{
            withdrawal_for_select.parent().parent().removeClass('has-danger');
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

    String.prototype.replace_all = function(search,replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };

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


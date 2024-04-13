<div id="unreconciled_deposits_listing">
    
</div>
<div id="reconcile_deposit" class="modal-form-contents ">
    <div class="alert alert-danger data_error" id="" style="display:none;">
    </div>
    <div id="transaction_details" class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php
                    if($this->lang->line('transaction_details')){
                        echo $this->lang->line('transaction_details');
                    }else{
                        echo 'Transaction Details';
                    }
                ?>
            </h3>
        </div>
        <div class="panel-body delay-modal-form-processing-button"> 
            <ul>
                <li id="transaction_date"><strong>
                    <?php
                        if($this->lang->line('transaction_date')){
                            echo $this->lang->line('transaction_date');
                        }else{
                            echo 'Transaction Date';
                        }
                    ?>
                : </strong><span></span></li>
                <li id="transaction_particulars"><strong>
                    <?php
                        if($this->lang->line('transaction_particulars')){
                            echo $this->lang->line('transaction_particulars');
                        }else{
                            echo 'Transaction particulars';
                        }
                    ?>
                    : </strong><span></span></li>
                <li id="transaction_amount"><strong>
                        <?php
                            if($this->lang->line('transaction_amount')){
                                echo $this->lang->line('transaction_amount');
                            }else{
                                echo 'Transaction amount';
                            }
                        ?>
                : </strong> <?php echo $this->group_currency; ?> <span></span></li>
                <?php echo form_hidden('transaction_alert_id',''); ?>
                <?php echo form_hidden('transaction_alert_amount',''); ?>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            SMS & E-mail Notifications:
            <?php echo form_checkbox("enable_notifications",1,TRUE," class = 'make-switch' data-size='small' "); ?>
        </div>
    </div>

    <table class="table table-striped table-condensed table-hover table-multiple-items table-layout-fixed" id="">
        <thead>
            <tr>
                <th width='4%'>#</th>
                <th width='2%'>#</th>
                <th width='25%'>
                    <?php
                        if($this->lang->line('deposit_for')){
                            echo $this->lang->line('deposit_for');
                        }else{
                            echo 'Deposit For';
                        }
                    ?>
                <span class='required'>*</span></th>
                <th id="deposit_particulars_header" colspan="">
                    <?php
                        if($this->lang->line('deposit_particulars')){
                            echo $this->lang->line('deposit_particulars');
                        }else{
                            echo 'Deposit Particulars';
                        }
                    ?>

                <span class='required'>*</span></th>
            </tr>
        </thead>
        <tbody id='append-place-holder'>
            <tr>
                <td width='4%'>
                    <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                        <i class="fa fa-times"></i>
                    </a>
                </td>
                <td class='count' width='2%'>1</td>
                <td class="deposit_for_cell" width='25%'>
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('deposit_fors[]',array(''=>'Select deposit for')+$deposit_for_options,'',' class="modal_select2 deposit_for" '); ?>
                    </div>
                </td>
                <td class='particulars_place_holder'>
                </td>
            </tr>
        </tbody>
    </table>
    <a href="javascript:;" class="btn margin-right-10 btn-default btn-xs" id="add-new-line">
        <i class="fa fa-plus"></i>
        <span class="hidden-380">
           <?php
                if($this->lang->line('add_new_line')){
                    echo $this->lang->line('add_new_line');
                }else{
                    echo 'Add New Line';
                }
            ?>
        </span>
    </a>
</div>
<div id='append-new-line'>
    <table>
        <tbody>
            <tr>
                <td width='4%'>
                    <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                        <i class="fa fa-times"></i>
                    </a>
                </td>
                <td width='2%' class='count'>1</td>
                <td class="deposit_for_cell" width='25%'>
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('deposit_fors[0]',array(''=>'Select deposit for')+$deposit_for_options,'',' class="modal_select2 deposit_for" ');?>
                    </div>
                </td>
                <td class='particulars_place_holder'>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="contribution_payment_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="contribution_payment_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="modal_select2 form-control member" ');?>
                    </div>
                </td>
                <td class="contribution_payment_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('contributions[0]',array(''=>'Select contribution')+$contribution_options+array('0'=>"Add Contribution"),'',' class="modal_select2 form-control contribution" ');?>
                    </div>
                    <a href="javascript:;" class="btn btn-default btn-xs inline-table-button toggle_deposit_description" id="">
                        <i class="fa fa-plus"></i>
                        <span class="hidden-380">
            <?php
                if($this->lang->line('add_description')){
                    echo $this->lang->line('add_description');
                }else{
                    echo 'Add Description';
                }
            ?>
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
                                'placeholder'=>''
                            ); 
                            echo form_textarea($textarea);

                        ?>
                    </div>
                </td>
                <td class="contribution_payment_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="fine_payment_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="fine_payment_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="modal_select2 form-control member" ');?>
                    </div>
                </td>
                <td class="fine_payment_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('fine_categories[0]',array(''=>'Select fine category')+$fine_category_options+array('0'=>"Add Fine Category"),'',' class="modal_select2 form-control fine_category" ');?>
                    </div>
                    <a href="javascript:;" class="btn btn-default btn-xs inline-table-button toggle_deposit_description" id="">
                        <i class="fa fa-plus"></i>
                        <span class="hidden-380">
            <?php
                if($this->lang->line('add_description')){
                    echo $this->lang->line('add_description');
                }else{
                    echo 'Add Description';
                }
            ?>
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
                                'placeholder'=>''
                            ); 
                            echo form_textarea($textarea);

                        ?>
                    </div>
                </td>
                <td class="fine_payment_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="miscellaneous_payment_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="miscellaneous_payment_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="modal_select2 form-control member" ');?>
                    </div>
                </td>
                <td class="miscellaneous_payment_fields">
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
                <td class="miscellaneous_payment_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="income_deposit_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="income_deposit_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('depositors[0]',array(''=>'Select depositor')+$depositor_options+array('0'=>"Add Depositor"),'',' class="modal_select2 form-control depositor" ');?>
                    </div>
                </td>
                <td class="income_deposit_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('income_categories[0]',array(''=>'Select income category')+$income_category_options+array('0'=>"Add Income Category"),'',' class="modal_select2 form-control income_category" ');?>
                    </div>
                    <a href="javascript:;" class="btn btn-default btn-xs inline-table-button toggle_deposit_description" id="">
                        <i class="fa fa-plus"></i>
                        <span class="hidden-380">
                    <?php
                        if($this->lang->line('add_description')){
                            echo $this->lang->line('add_description');
                        }else{
                            echo 'Add Description';
                        }
                    ?>
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
                                'placeholder'=>''
                            ); 
                            echo form_textarea($textarea);

                        ?>
                    </div>
                </td>
                <td class="income_deposit_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="bank_loan_disbursement_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="bank_loan_disbursement_fields">
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
                <td class="bank_loan_disbursement_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount Payable" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amount_payables[0]','',' class="currency form-control tooltips amount_payable " placeholder="Amount Payable" ');?>
                    </div>
                </td>
                <td class="bank_loan_disbursement_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount Disbursed" ');?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="incoming_money_transfer_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="incoming_money_transfer_fields" style="max-width: 150px; overflow: hidden;">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount Payable" data-container="body">
                        <i class="" ></i>
                        <?php echo form_dropdown('from_account_ids[0]',array(''=>'Select from account')+$from_account_options+array('0'=>"Add Account"),'',' class="modal_select2 form-control tooltips from_account_id" placeholder="" ');?>
                    </div>
                </td>
                <td class="incoming_money_transfer_fields">
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
                <td class="incoming_money_transfer_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount Transferred" ');?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div id="stock_sale_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="stock_sale_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('stock_ids[0]',array(''=>'Select stock')+$stock_options,'',' class="modal_select2 form-control stock_id" ');?>
                    </div>
                </td>
                <td class="stock_sale_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('price_per_shares[0]','',' class=" form-control currency tooltips price_per_share" placeholder="Price per share" ');?>
                    </div>
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('number_of_shares_solds[0]','',' class=" form-control tooltips number_of_shares_sold" placeholder="Number of shares sold" ');?>
                    </div>
                </td>

                <td class="stock_sale_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>
                    
            </tr>
        </tbody>
    </table>
</div>
<div id="asset_sale_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="asset_sale_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('asset_ids[0]',array(''=>'Select asset')+$asset_options+array('0'=>"Add Asset"),'',' class="modal_select2 form-control asset_id" ');?>
                    </div>
                </td>
                <td class="asset_sale_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>   
            </tr>
        </tbody>
    </table>
</div>
<div id="money_market_cash_in_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="money_market_cash_in_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('money_market_investment_ids[0]',array(''=>'Select money market investment ')+$money_market_investment_options,'',' class="modal_select2 form-control money_market_investment_id" ');?>
                    </div>
                </td>
                <td class="money_market_cash_in_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>   
            </tr>
        </tbody>
    </table>
</div>
<div id="loan_repayment_fields">
    <table>
        <tbody>
            <tr class="row">
                <td class="loan_repayment_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="modal_select2 form-control member" ');?>
                    </div>
                </td>
                <td class="loan_repayment_fields">
                    <div class="input-icon tooltips change-loan loan-to-populate right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('loans[0]',array(''=>'Select loan'),'',' class="modal_select2 form-control loan" ');?>
                    </div>
                </td>
                <td class="loan_repayment_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>   
            </tr>
        </tbody>
    </table>
</div>
<div id="loan_processing_income_fields" class="hidden">
    <table>
        <tbody>
            <tr class="row">
                <td class="loan_disbursement_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('members[0]',array(''=>'Select member')+$this->active_group_member_options+array('0'=>"Add Member"),'',' class="modal_select2 form-control member" ');?>
                    </div>
                </td>
                <td class="loan_disbursement_fields">
                    <div class="input-icon tooltips change-loan loan-to-populate right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('loans[0]',array(''=>'Select loan'),'',' class="modal_select2 form-control loan" ');?>
                    </div>
                </td>
                <td class="loan_disbursement_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>   
            </tr>
        </tbody>
    </table>
</div>
<div id="external_loan_repayment_fields" class="hidden">
    <table>
        <tbody>
            <tr class="row">
                <td class="external_loan_repayment_fields">
                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('debtors[0]',array(''=>'Select borrower')+$this->active_group_debtor_options,'',' class="modal_select2 form-control debtor" ');?>
                    </div>
                </td>
                <td class="external_loan_repayment_fields">
                    <div class="input-icon tooltips change-loan external-loan-to-populate right" data-original-title="" data-container="body"><i class="" ></i>
                        <?php echo form_dropdown('external_loans[0]',array(''=>'Select loan'),'',' class="modal_select2 form-control external_loan" ');?>
                    </div>
                </td>
                <td class="external_loan_repayment_fields">
                    <div class="input-icon tooltips right" data-original-title="Enter Amount" data-container="body">
                        <i class="" ></i>
                        <?php echo form_input('amounts[0]','',' class="currency form-control tooltips amount" placeholder="Amount" ');?>
                    </div>
                </td>   
            </tr>
        </tbody>
    </table>
</div>
<div id="add_member" class="modal-form-contents">
    <div class="alert alert-danger data_error" id="" style="display:none;">
    </div>
    <div id="add_member_form" >
        <div class="form-group">
            <label>

                <?php
                        if($this->lang->line('first_name')){
                            echo $this->lang->line('first_name');
                        }else{
                            echo 'First Name';
                        }
                    ?>

                <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                <?php echo form_input('first_name','',' id="first_name" class="form-control" placeholder="First Name"');?>
            </div>
        </div>
        <div class="form-group">
            <label>

                <?php
                        if($this->lang->line('last_name')){
                            echo $this->lang->line('last_name');
                        }else{
                            echo 'Last Name';
                        }
                    ?>

                <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                <?php echo form_input('last_name','',' id="last_name" class="form-control" placeholder="Last Name"');?>
            </div>
        </div>
        <div class="form-group">
            <label>

                <?php
                        if($this->lang->line('phone_number')){
                            echo $this->lang->line('phone_number');
                        }else{
                            echo 'Phone Number';
                        }
                    ?>

                <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                <?php echo form_input('phone','','class="form-control" placeholder="Phone Number" id="phone"');?>
            </div>
        </div>
        <div class="form-group">
            <label>
                
                <?php
                        if($this->lang->line('email_address')){
                            echo $this->lang->line('email_address');
                        }else{
                            echo 'Email Address';
                        }
                    ?>

            </label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                </span>
                <?php echo form_input('email','','class="form-control" placeholder="Email Address" id="email"');?>
            </div>
        </div>
        <div id='member_invitation_notifications' class="form-group">
            <label>Invitation Notifications</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('send_sms_notification',1,'',' id="send_sms_notification" '); ?> Send SMS Notification
                </label>
                <label class="checkbox-inline">
                    <?php echo form_checkbox('send_email_notification',1,'',' id="send_email_notification" '); ?> Send Email Notification
                </label>
            </div>
        </div>
    </div>
</div>
<a class="stacked_inline pop_up hidden" data-row="" data-toggle="modal" data-content="#add_member" data-title="Add Member" data-id="add_new_member" id="add_new_member" href="#">
    
                    <?php
                        if($this->lang->line('add_member')){
                            echo $this->lang->line('add_member');
                        }else{
                            echo 'Add Member';
                        }
                    ?>
</a>
<div id="contributions_form" class="modal-form-contents">
    <div id="contributions_form_holder" class="form-body">
        <div class="form-group">
            <div class="alert alert-danger data_error" id="" style="display:none;">
            </div>
        </div>
        <div class="form-group">
            <label>Contribution Name<span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                <i class="fa fa-bank"></i>
                </span>
                <?php echo form_input('name',"",'class="form-control" placeholder="Contribution Name" id="name"'); ?>
            </div>
        </div>
        <div class="form-group">
            <label>Contribution Amount per Member<span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                <i class="fa fa-bank"></i>
                </span>
                <?php echo form_input('amount',"",'  class="form-control currency" placeholder="Contribution Amount" id="amount"'); ?>
            </div>
        </div>
        <div class="form-group">
            <label for="bank-branches">

                <?php
                    if($this->lang->line('contribution_type')){
                        echo $this->lang->line('contribution_type');
                    }else{
                        echo 'Contribution Type';
                    }
                ?>

                <span class="required">*</span></label>
            <div class="input-group col-md-12 col-sm-12 col-xs-12 ">
                <?php echo form_dropdown('type',array(''=>'--Select Contribution Type--')+$contribution_type_options,"",'class="form-control modal_select2" id = "type"  ') ?>
            </div>
        </div>
         
        <div id='regular_invoicing_active_holder' class="form-group">
            <label>Automatic Invoicing</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('regular_invoicing_active',1,"",' id="regular_invoicing_active" '); ?> Activate Automatic Invoicing
                </label>
            </div>
        </div>

        <div id='one_time_invoicing_active_holder' class="form-group">
            <label>Automatic Invoicing</label>
            <div class="checkbox-list">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('one_time_invoicing_active',1,"",' id="one_time_invoicing_active" '); ?> Activate Automatic Invoicing
                </label>
            </div>
        </div>

        <div id='regular_invoicing_settings'>
                <div class="form-group">
                    <label>How often do members contribute?<span class="required">*</span></label>
                    <div class="col-md-12 input-group">
                        <?php echo form_dropdown('contribution_frequency',$contribution_frequency_options,"",' id="contribution_frequency" class="form-control modal_select2" data-placeholder="Select..."'); ?>
                    </div>      
                    <span class="help-block"> e.g. Once a Month</span>
                </div>
                <div class="form-group">
                    <div id='once_a_month'>
                        <label>When do members contribute?<span class="required">*</span></label>
                        <div class='row'>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="col-md-12 input-group">
                                    <?php echo form_dropdown('month_day_monthly',$days_of_the_month,"",' id="month_day_monthly" class=" form-control modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class="col-md-12 input-group">
                                    <?php echo form_dropdown('week_day_monthly',$month_days,"",' id="week_day_monthly" class="form-control modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div id='once_a_week'>
                        <div class="row">
                            <div class="col-md-12">
                                <div class='col-md-12 input-group'>
                                    <label>When do members contribute?<span class="required">*</span></label>
                                    <?php echo form_dropdown('week_day_weekly',$week_days,"",'id="week_day_weekly" class="form-control modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>  
                        </div>
                    </div> 
                    <div id='once_every_two_weeks'>
                        <label>When do members contribute?<span class="required">*</span></label>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class='col-md-12  input-group'>
                                    <?php echo form_dropdown('week_day_fortnight',$every_two_week_days,"",'id="week_day_fortnight" class="form-control modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('week_number_fortnight',$week_numbers,"",'id="week_number_fortnight" class="form-control modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>  
                        </div>
                    </div> 
                    <div id='once_every_multiple_months'>
                        <label>When do members contribute?<span class="required">*</span></label>
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('month_day_multiple',$days_of_the_month,"",'id="month_day_multiple" class="form-control modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('week_day_multiple',$month_days,"",'id="week_day_multiple" class="form-control modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('start_month_multiple',$starting_months,"",'id="start_month_multiple" class="form-control modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>  
                        </div>
                    </div>                        
                </div>
                <div id='invoice_days' class="form-group">
                    <div>
                        <label>When do want to send invoices to members?<span class="required">*</span></label>
                        <div class='row'>
                            <div class="col-md-12">
                                <div class="col-md-12 input-group">
                                    <?php echo form_dropdown('invoice_days',$invoice_days,7,' id="invoice_days" class=" form-control modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <div id='one_time_invoicing_settings'>
            <div class="form-group">
                <label>Invoice Date<span class="required">*</span></label>
                <div class="input-group  date date-picker" data-date="<?php echo timestamp_to_datepicker(time());?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                    <?php echo form_input('invoice_date',timestamp_to_datepicker(time()),'id="invoice_date" class="form-control" readonly');?> 
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                </div>  
            </div>

            <div class="form-group">
                <label>Contribution Date/

                    <?php
                    if($this->lang->line('due_date')){
                        echo $this->lang->line('due_date');
                    }else{
                        echo 'Due Date';
                    }
                ?>

                    <span class="required">*</span></label>
                <div class="input-group date date-picker" data-date="<?php echo timestamp_to_datepicker(time());?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                    <?php echo form_input('contribution_date',timestamp_to_datepicker(time()),'id="contribution_date" class="form-control" readonly');?> 
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                </div>  
            </div>
        </div>



            <div id='invoice_notifications' class="form-group">
                <label>Invoice Notifications</label>
                <div class="checkbox-list">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('sms_notifications_enabled',1,FALSE,' id="sms_notifications_enabled" '); ?> Enable SMS Notifications
                    </label>
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('email_notifications_enabled',1,FALSE,' id="email_notifications_enabled" '); ?> Enable Email Notifications
                    </label>
                </div>
            </div>

            <div id='sms_template' class="form-group">
                <label>SMS Template<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                    </span>
                    <?php
                        $textarea = array(
                            'name'=>'sms_template',
                            'id'=>'',
                            'value'=> $sms_template_default,
                            'cols'=>40,
                            'rows'=>5,
                            'maxlength'=>200,
                            'class'=>'form-control maxlength',
                            'placeholder'=>'SMS Template'
                        ); 
                        echo form_textarea($textarea); 
                    ?>
                </div>
                <span class="help-block"> Required placeholders: 
                    <?php 
                        $i=1;
                        foreach($placeholders as $placeholder): 
                            if($i==1){
                                echo $placeholder;
                            }else{
                                echo ','.$placeholder;
                            }
                            $i++;
                        endforeach; 
                    ?>
                </span>
            </div>

            <div id='contribution_member_list_settings' class="form-group">
                <label>Do you wish to limit invoicing for this contribution to specific members?</label>
                <div class="checkbox-list">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('enable_contribution_member_list',1,FALSE,' id="enable_contribution_member_list" '); ?> Enable this contribution for specific members only
                    </label>
                </div>

                <div id='contribution_member_list' class="margin-top-10">
                    <div class='row'>
                        <div class="col-md-12">
                            <div class="col-md-12 input-group">
                                <?php echo form_dropdown('contribution_member_list[]',$this->active_group_member_options,array(),' id="" class=" form-control modal_select2" multiple="multiple" data-placeholder="Select..."'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="fines">
                <div class="form-group">
                    <label>Do you charge fines for late payment?</label>
                    <div class="checkbox-list">
                        <label class="checkbox-inline">
                            <?php echo form_checkbox('enable_fines',1,FALSE,' id="enable_fines" '); ?> Enable Fines
                        </label>
                    </div>
                    <span class="help-block"> 
                    </span>
                </div>

                <div id='fine_settings' class="form-group">
                    <div class='fine_setting_row'>
                        <label>We charge a</label>
                        <div class="row">
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('fine_type[]',array(''=>'Select fine type')+$fine_types,'','id="" class="form-control fine_types modal_select2" data-placeholder="Select..."'); ?>
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
                                    <?php echo form_dropdown('fixed_fine_mode[]',array(''=>'Select how fines behave')+$fine_mode_options,'','id="" class="form-control modal_select2 fixed_fine_modes" data-placeholder="Select..."'); ?>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_settings percentage_fine_on">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('percentage_fine_on[]',array(''=>'Select when fines is calculated based on')+$percentage_fine_on_options,'','id="" class="form-control percentage_fine_ons modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>
                        </div>

                        <div class='row percentage_fine_settings margin-top-10'>
                        
                            <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_chargeable_on">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('percentage_fine_chargeable_on[]',array(''=>'Select when fines are charged')+$fine_chargeable_on_options,'','id="" class="form-control percentage_fine_chargeable_ons modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_mode">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('percentage_fine_mode[]',array(''=>'Select how fines behave')+$fine_mode_options,'','id="" class="form-control percentage_fine_modes modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-4 percentage_fine_frequency">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('percentage_fine_frequency[]',array(''=>'Select fine frequency')+$fine_frequency_options,'','id="" class="form-control percentage_fine_frequencies modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>

                        </div>

                        <div class='row fixed_fine_settings margin-top-10'>
                            <div class="col-md-6 col-sm-6 col-xs-6 fixed_fine_chargeable_on">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('fixed_fine_chargeable_on[]',array(''=>'Select when fines are charged')+$fine_chargeable_on_options,'','id="" class="form-control fixed_fine_chargeable_ons modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 fixed_fine_settings fixed_fine_frequency">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('fixed_fine_frequency[]',array(''=>'Select fine frequency')+$fine_frequency_options,'','id="" class="form-control fixed_fine_frequencies modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>
                        </div>

                        <div class='row margin-top-10 margin-bottom-10'>
                            <div class="col-md-12 col-sm-12 col-xs-12 fine_limit">
                                <div class='col-md-12 input-group'>
                                    <?php echo form_dropdown('fine_limit[]',$fine_limit_options,'','id="" class="form-control fine_limits modal_select2" data-placeholder="Select..."'); ?>
                                </div>
                            </div>
                        </div>
                        <div class='fine_notifications form-group '>
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
                                <a data-original-title="Remove fine setting line" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-line" >
                                    <i class="fa fa-times"></i>
                                    <span class="hidden-380">
                                        Remove fine setting
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id='append-place-holder'></div>
                    <div class="row margin-top-10">
                        <div class='col-md-12 margin-bottom-10 text-left'>
                            <a href="javascript:;" class="btn btn-default btn-xs" id="add-new-line">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-380">
                                    Add new fine setting
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="contribution_summary_display_configuration" class="form-group">
                <label>Do you wish to configure how this contribution's summary is displayed?</label>
                <div class="checkbox-list">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('enable_contribution_summary_display_configuration',1,FALSE,' id="enable_contribution_summary_display_configuration" '); ?> Enable contribution summary report display
                    </label>
                </div>
            </div>

            <div id="contribution_summary_display_configuration_settings">
                <div class="form-group">
                    <label>Display arrears for this contribution?</label>
                    <div class="checkbox-list">
                        <label class="checkbox-inline">
                            <?php echo form_checkbox('display_contribution_arrears_cumulatively',1,FALSE,' id="display_contribution_arrears_cumulatively" '); ?> Enable display of contribution arrears as a cumulative
                        </label>
                    </div>
                </div>
            </div>
            <?php echo form_hidden('id',''); ?>
            <?php echo form_hidden('action','create'); ?>
    </div>
</div>
<div id="loans_form" class="modal-form-contents">
    <div id="loans_form_holder" class="form-body">
        <div class="form-group">
            <div class="alert alert-danger data_error" id="" style="display:none;">
            </div>
        </div>
        <div class="automated_group_loan">
            <?php echo form_hidden('loan_type',1); ?>
            <div class="form-group read" readonly="readonly">
                <label>
                    <?php
                        $default_message='Member Name';
                        $this->languages_m->translate('member_name',$default_message);
                    ?>
                    <span class="required">*</span></label>
                <div class="input-group col-xs-12 ">
                    <?php echo form_dropdown('member_id',array(''=>'--Select a Member--')+$this->active_group_member_options,"",'class="form-control modal_select2 member_id" id = "member_id"  ') ?>
                    <span class="help-block"> Select the name of Member taking the loan. </span>
                </div>
                <?php echo form_hidden('member_id',""); ?>
            </div>
            <div class="form-group row col-md-6">
                <label>Loan Disbursement Date<span class="required">*</span></label>
                <div class="input-group date date-picker" data-date="<?php echo timestamp_to_datepicker(time());?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years"  data-date-end-date="0d" >
                    <?php echo form_input('disbursement_date',timestamp_to_datepicker(time()),'class="form-control" readonly');?> 
                    <span class="input-group-btn">
                        <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </span>
                </div>  
            </div>
            <div class="clearfix"></div>

            <div class="form-group">
                <label>
                    <?php
                        $default_message='Loan Amount';
                        $this->languages_m->translate('loan_amount',$default_message);
                    ?>
                    <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-money"></i>
                    </span>
                    <?php echo form_input('loan_amount',"",'  class="form-control currency" placeholder="Loan Amount"'); ?>
                </div>
                <span class="help-block"> eg 50,000. </span>
            </div>

            <div class="form-group">
                <label>Loan Disbursing Account<span class="required">*</span></label>
                <div class="input-group col-xs-12 ">
                    <?php echo form_dropdown('account_id',array(''=>'--Select an Account--')+$account_options,'','class="form-control modal_select2 account_id" id = "account_id"  ') ?>
                    <span class="help-block"> Select the account to disburse this loan. </span>
                </div>
                <?php echo form_hidden('account_id',""); ?>
            </div>

            <div class="form-group">
                <label>Loan Repayment Period (In Months)<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                    </span>
                    <?php echo form_input('repayment_period',"",'  class="form-control numeric" placeholder="Loan Repayment Period"'); ?>
                </div>
                <span class="help-block"> What is the repayment period for this loan in Months? </span>
            </div>

            <div class="form-group">
                <label>Interest Type<span class="required">*</span></label>
                <div class="input-group col-xs-12 ">
                    <?php echo form_dropdown('interest_type',array(''=>'--Select Loan Interest Type--')+$interest_types,"",'class="form-control modal_select2 interest_type" id = "interest_type"  ') ?>
                    <span class="help-block"> Select an interest rate type to administer. </span>
                </div>
            </div>

            <div id='enable_reducing_balance_installment_recalculation' class="form-group">
                <label>Enable Reducing Balance Recalculation on Early Installment Repayment</label>
                <div class="checkbox-list">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('enable_reducing_balance_installment_recalculation',1,FALSE,' id="enable_reducing_balance_installment_recalculation" '); ?> Enable Reducing Balance Recalculation on Early Installment Repayment
                    </label>
                </div>
                <span class="help-block"> Once enabled interest charged will be waived for any early payments to the loan for each installment. </span>
            </div>

            <div class="not_for_custom_settings">

                <div class="row" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-12">Loan Interest Rate<span class="required">*</span></label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calculator"></i>
                                </span>
                                <?php echo form_input('interest_rate',"",'  class="form-control numeric" placeholder="Loan Interest Rate"'); ?>
                            </div>
                            <span class="help-block"> Key in the Loan Interest Rate </span>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group col-xs-12 ">
                                <?php echo form_dropdown('loan_interest_rate_per',$loan_interest_rate_per,4,'class="form-control modal_select2 interest_type" id = "loan_interest_rate_per"  ') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Loan Grace Period<span class="required">*</span></label>
                    <div class="input-group col-xs-12 ">
                        <?php echo form_dropdown('grace_period',array(''=> '--Select Loan Grace Period--')+$loan_grace_periods,1,'class="form-control modal_select2 grace_period" id = "grace_period"  ') ?>
                        <span class="help-block"> Select period offered before the first Loan installment repayment . </span>
                    </div>
                </div>
            </div>

            <div class="for_custom_settings">
                <div class="form-group">
                    <label>Loan Repayment Breakdown Procedure</label>
                    <div>
                        <div class="radio-list">
                            <label class="radio-inline">
                                <input type="radio" name="custom_interest_procedure" id="custom_interest_rate_breakdown" value="2" checked="checked" > Custom Installment(s) Breakdown 
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="custom_interest_procedure" id="custom_interest_rate_breakdown" value="1" > Custom Interest Rates Breakdown 
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row interest_rates_breakdown">
                    <?php if((isset($_POST['interest_rate_date_from']))):
                    foreach($_POST['interest_rate_date_from'] as $key=>$value):?>
                        <div class="loan_interest_rates_breakdown_values">
                            <div class="form-group col-md-4 col-sm-12">
                                <label>Apply Rate From date<span class="required">*</span></label>
                                <div class="input-group col-xs-12 ">
                                    <?php echo form_dropdown('interest_rate_date_from[]',array(''=>'--Select date from--')+$loan_days,$value,'class="form-control select2 interest_rate_date_from"  ') ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label>Apply Rate To Date<span class="required">*</span></label>
                                <div class="input-group col-xs-12 ">
                                    <?php echo form_dropdown('interest_rate_date_to[]',array(''=>'--Select date to--')+$loan_days,$_POST['interest_rate_date_to'][$key],'class="form-control select2 interest_rate_date_to"  ') ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label>Interest Rate charged (%)<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('custom_interest_rate[]',$_POST['custom_interest_rate'][$key],'  class="form-control custom_interest_rate" placeholder="Custom Interest Rate Charged"'); ?>
                                </div>
                            </div>

                            

                            <div class='col-md-12 margin-bottom-10 text-left'>
                                <a data-original-title="Remove rate breakdown" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-rate-breakdown" >
                                    <i class="fa fa-times"></i>
                                    <span class="hidden-380">
                                        Remove Rate Breakdown
                                    </span>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                            <hr/>

                        </div>
                    <?php endforeach;else:?>
                        <div class="loan_interest_rates_breakdown_values">
                            <div class="form-group col-md-4 col-sm-12">
                                <label>Apply Rate From date<span class="required">*</span></label>
                                <div class="input-group col-xs-12 ">
                                    <?php echo form_dropdown('interest_rate_date_from[]',array(''=>'--Select date from--')+$loan_days,1,'class="form-control modal_select2 interest_rate_date_from"  ') ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label>Apply Rate To Date<span class="required">*</span></label>
                                <div class="input-group col-xs-12 ">
                                    <?php echo form_dropdown('interest_rate_date_to[]',array(''=>'--Select date to--')+$loan_days,10000,'class="form-control modal_select2 interest_rate_date_to"  ') ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-sm-12">
                                <label>Interest Rate charged (%)<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('custom_interest_rate[]','','  class="form-control custom_interest_rate" placeholder="Custom Interest Rate Charged"'); ?>
                                </div>
                            </div>

                            

                            <div class='col-md-12 margin-bottom-10 text-left'>
                                <a data-original-title="Remove rate breakdown" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-rate-breakdown" >
                                    <i class="fa fa-times"></i>
                                    <span class="hidden-380">
                                        Remove Rate Breakdown
                                    </span>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                            <hr/>

                        </div>
                    <?php endif;?>        

                    <div id='append-place-holder-rates-breakdown'></div>

                    <div class='col-md-12 margin-bottom-10 text-left'>
                        <a href="javascript:;" class="btn btn-default btn-xs" id="add-new-line-rate-breakdown">
                            <i class="fa fa-plus"></i>
                            <span class="hidden-380">
                                Add New rate breakdown
                            </span>
                        </a>
                    </div>


                </div>

                <div class="row custom_invoice_breakdown_settings">
                    <?php if((isset($_POST['custom_payment_date']))){
                    foreach($_POST['custom_payment_date'] as $key=>$value):?>
                        <div class="custom_invoice_breakdown_values">
                            <div class="form-group col-md-6">
                                <label>Loan Payment Date<span class="required">*</span></label>
                                <div class="input-group date date-picker" data-date="<?php echo timestamp_to_datepicker(strtotime($value));?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                    <?php echo form_input('custom_payment_date[]',timestamp_to_datepicker(strtotime($value)),'class="form-control custom_payment_date" readonly');?> 
                                    <span class="input-group-btn">
                                        <button class="btn default custom_payment_date" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>  
                            </div>

                            <div class="form-group col-md-6 col-sm-12">
                                <label>
                                        <?php
                                            $default_message='Amount Payable';
                                            $this->languages_m->translate('amount_payable',$default_message);
                                        ?>
                                    <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('custom_amount_payable[]',$_POST['custom_amount_payable'][$key],'  class="form-control custom_amount_payable currency" placeholder="Amount Payable"'); ?>
                                </div>
                            </div>


                            
                            <div class='col-md-12 margin-bottom-10 text-left'>
                                <a data-original-title="Remove invoice breakdown" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-invoice-breakdown" >
                                    <i class="fa fa-times"></i>
                                    <span class="hidden-380">
                                        Remove Invoice Breakdown
                                    </span>
                                </a>
                            </div>
                            <div class="clearfix"></div><hr/>

                        </div>             

                    <?php endforeach;
                    } else if(isset($loan_custom_invoices)){
                        if($loan_custom_invoices){
                            foreach($loan_custom_invoices as $value):?>
                                <div class="custom_invoice_breakdown_values">
                                    <div class="form-group col-md-6">
                                        <label>Loan Payment Date<span class="required">*</span></label>
                                        <div class="input-group date date-picker" data-date="<?php echo timestamp_to_datepicker($value->invoice_date);?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                            <?php echo form_input('custom_payment_date[]',timestamp_to_datepicker($value->invoice_date),'class="form-control custom_payment_date" readonly');?> 
                                            <span class="input-group-btn">
                                                <button class="btn default custom_payment_date" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>  
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12">
                                        <label>
                                            <?php
                                                $default_message='Amount Payable';
                                                $this->languages_m->translate('amount_payable',$default_message);
                                            ?>
                                            <span class="required">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calculator"></i>
                                            </span>
                                            <?php echo form_input('custom_amount_payable[]',$value->amount_payable,'  class="form-control custom_amount_payable currency" placeholder="Amount Payable"'); ?>
                                        </div>
                                    </div>


                                    
                                    <div class='col-md-12 margin-bottom-10 text-left'>
                                        <a data-original-title="Remove invoice breakdown" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-invoice-breakdown" >
                                            <i class="fa fa-times"></i>
                                            <span class="hidden-380">
                                                Remove Invoice Breakdown
                                            </span>
                                        </a>
                                    </div>
                                    <div class="clearfix"></div><hr/>

                                </div>             

                            <?php endforeach;
                        }else{?>
                        <div class="custom_invoice_breakdown_values">
                            <div class="form-group col-md-6">
                                <label>Loan Payment Date<span class="required">*</span></label>
                                <div class="input-group date date-picker" data-date="<?php echo timestamp_to_datepicker();?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years" >
                                    <?php echo form_input('custom_payment_date[]',timestamp_to_datepicker(),'class="form-control custom_payment_date" readonly');?> 
                                    <span class="input-group-btn">
                                        <button class="btn default custom_payment_date" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>  
                            </div>

                            <div class="form-group col-md-6 col-sm-12">
                                <label>
                                        <?php
                                            $default_message='Amount Payable';
                                            $this->languages_m->translate('amount_payable',$default_message);
                                        ?>
                                    <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('custom_amount_payable[]','','  class="form-control custom_amount_payable currency" placeholder="Amount Payable"'); ?>
                                </div>
                            </div>


                            
                            <div class='col-md-12 margin-bottom-10 text-left'>
                                <a data-original-title="Remove invoice breakdown" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-invoice-breakdown" >
                                    <i class="fa fa-times"></i>
                                    <span class="hidden-380">
                                        Remove Invoice Breakdown
                                    </span>
                                </a>
                            </div>
                            <div class="clearfix"></div><hr/>

                        </div>
                    <?php }
                    }else{?>
                        <div class="custom_invoice_breakdown_values">
                            <div class="form-group col-md-6">
                                <label>Loan Payment Date<span class="required">*</span></label>
                                <div class="input-group date date-picker" data-date="<?php echo $this->input->post('custom_payment_date');?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                    <?php echo form_input('custom_payment_date[]',timestamp_to_datepicker(time()),'class="form-control custom_payment_date" readonly');?> 
                                    <span class="input-group-btn">
                                        <button class="btn default custom_payment_date" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>  
                            </div>

                            <div class="form-group col-md-6 col-sm-12">
                                <label>
                                        <?php
                                            $default_message='Amount Payable';
                                            $this->languages_m->translate('amount_payable',$default_message);
                                        ?>
                                    <span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('custom_amount_payable[]',$this->input->post('custom_amount_payable[]'),'  class="form-control custom_amount_payable currency" placeholder="Amount Payable"'); ?>
                                </div>
                            </div>


                            
                            <div class='col-md-12 margin-bottom-10 text-left'>
                                <a data-original-title="Remove invoice breakdown" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-invoice-breakdown" >
                                    <i class="fa fa-times"></i>
                                    <span class="hidden-380">
                                        Remove Invoice Breakdown
                                    </span>
                                </a>
                            </div>
                            <div class="clearfix"></div><hr/>

                        </div>             

                    <?php }?>
                    
                    <div id='append-place-holder-invoice-breakdown'></div>

                    <div class='col-md-12 margin-bottom-10 text-left'>
                        <a href="javascript:;" class="btn btn-default btn-xs" id="add-new-line-invoice-breakdown">
                            <i class="fa fa-plus"></i>
                            <span class="hidden-380">
                                Add New invoice breakdown
                            </span>
                        </a>
                    </div>

                </div>
            </div>

            <div id='loan_installment_notifications' class="form-group">
                <label>Loan Installments Notifications</label>
                <div class="checkbox-list">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('sms_notifications_enabled',1,FALSE,' id="sms_notifications_enabled" '); ?> Enable SMS Notifications
                    </label>
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('email_notifications_enabled',1,1,' id="email_notifications_enabled" '); ?> Enable Email Notifications
                    </label>
                </div>
            </div>

            <div id='sms_template' class="form-group">
                <label>SMS Template<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                    </span>
                    <?php
                        $textarea = array(
                            'name'=>'sms_template',
                            'id'=>'',
                            'value'=> $sms_template_default,
                            'cols'=>40,
                            'rows'=>5,
                            'maxlength'=>200,
                            'class'=>'form-control maxlength',
                            'placeholder'=>'SMS Template'
                        ); 
                        echo form_textarea($textarea); 
                    ?>
                </div>
                <span class="help-block"> Required placeholders: 
                    <?php 
                        $i=1;
                        foreach($placeholders as $placeholder): 
                            if($i==1){
                                echo $placeholder;
                            }else{
                                echo ','.$placeholder;
                            }
                            $i++;
                        endforeach; 
                    ?>
                </span>
            </div>

            <div class="form-group">
                <label>Do you charge fines for late loan installment payments?</label>
                <div class="input-group checkbox-list col-xs-12 ">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('enable_loan_fines',1,'',' id="enable_loan_fines" class="enable_loan_fines" '); ?> Enable late loan repayment fines
                    </label>
                </div>
            </div>

            <div class="enable_loan_fines_settings">

                <div class="form-group">
                    <label>What type of Late Loan Payment fine do you Charge? <span class="required">*</span></label>
                    <div class="input-group col-xs-12 ">
                        <?php echo form_dropdown('loan_fine_type',array(''=>'--Select  the Type of Fine Charged--')+$late_loan_payment_fine_types,"",'class="form-control modal_select2 loan_fine_type" id = "loan_fine_type"  ') ?>
                        <span class="help-block"> Select the type of fine you adminster. </span>
                    </div>
                </div>

                <div class="row late_loan_payment_fixed_fine">
                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Fixed Amount charge<span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <?php echo form_input('fixed_fine_amount','','  class="form-control currency fixed_fine_amount" placeholder="Fixed Fine Amount"'); ?>
                        </div>
                        <span class="help-block"> Enter the fine's fixed amount payable. </span>
                    </div>

                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Fixed Amount Fine Frequecy<span class="required">*</span></label>
                        <?php echo form_dropdown('fixed_amount_fine_frequency',array(''=>'--Select  the fine frequency--')+$late_payments_fine_frequency,'','class="form-control modal_select2 fixed_amount_fine_frequency" id = "fixed_amount_fine_frequency"  ') ?>
                        <span class="help-block"> Select the fine frequecy. </span>
                    </div>

                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Fixed Amount Fine Frequecy On<span class="required">*</span></label>
                        <?php echo form_dropdown('fixed_amount_fine_frequency_on',array(''=>'--Select  the fine frequency On--')+$fixed_amount_fine_frequency_on,'','class="form-control modal_select2 fixed_amount_fine_frequency_on" id = "fixed_amount_fine_frequency_on"  ') ?>
                        <span class="help-block"> Select where to apply fine. </span>
                    </div>

                </div>

                <div class="row late_loan_payment_percentage_fine">
                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Fine Percentage Rate (%)<span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <?php echo form_input('percentage_fine_rate','','  class="form-control numeric percentage_fine_rate" placeholder="Fine Percentage Rate"'); ?>
                        </div>
                        <span class="help-block"> Enter the Fine's % rate charged. </span>
                    </div>

                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Fine Frequecy<span class="required">*</span></label>
                        <?php echo form_dropdown('percentage_fine_frequency',array(''=>'--Select  the fine frequecy--')+$late_payments_fine_frequency,'','class="form-control modal_select2 percentage_fine_frequency" id = "percentage_fine_frequency"  ') ?>
                        <span class="help-block"> Select the frequency of the fine. </span>
                    </div>

                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Fine Charge on <span class="required">*</span></label>
                        <?php echo form_dropdown('percentage_fine_on',array(''=>'--Select where is Fine Charged On--')+$percentage_fine_on,'','class="form-control modal_select2 percentage_fine_on" id = "percentage_fine_on"  ') ?>
                        <span class="help-block"> Select where the fine is charge on. </span>
                    </div>

                </div>

                <div class="row late_loan_repayment_one_off_fine">

                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Select One Off Fine Type <span class="required">*</span></label>
                        <?php echo form_dropdown('one_off_fine_type',array(''=>'--Select one Off fine Type--')+$one_off_fine_types,'','class="form-control modal_select2 one_off_fine_type" id = "one_off_fine_type"  ') ?>
                    </div>

                    <div class="col-sm-8 col-xs-12 one_off_fine_type_settings">
                        
                        <div class="form-group col-sm-6 col-xs-12 one_off_fixed_amount_setting">
                            <label>One Off Fixed Amount<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </span>
                                <?php echo form_input('one_off_fixed_amount','','  class="form-control currency fixed_fine_amount" placeholder="One Off Fixed Amount"'); ?>
                            </div>
                        </div>



                        <div class="one_off_percentage_setting">

                            <div class="form-group col-sm-6 col-xs-12">
                                <label>One Off Percentage (%)<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calculator"></i>
                                    </span>
                                    <?php echo form_input('one_off_percentage_rate','','  class="form-control numeric one_off_percentage_rate" placeholder="One Off Percentage Rate"'); ?>
                                </div>
                            </div>

                            <div class="form-group col-sm-6 col-xs-12">
                                <label>One Off Percentage on<span class="required">*</span></label>
                                <?php echo form_dropdown('one_off_percentage_rate_on',array(''=>'--Select One Off Percentage on--')+$one_off_percentage_rate_on,'','class="one_off_percentage_rate_on form-control modal_select2" id = "one_off_percentage_rate_on"  ') ?>
                            </div>

                        </div>

                     </div>

                </div>


            </div>

            <div class="form-group">
                <label>Do you charge fines for any outstanding loan balances at the end of the Loan?</label>
                <div class="input-group checkbox-list col-xs-12 ">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('enable_outstanding_loan_balance_fines',1,"",' id="enable_outstanding_loan_balance_fines" class="enable_outstanding_loan_balance_fines" '); ?> Enable Fines for outstanding balances
                    </label>
                </div>
            </div>


            <div class="enable_outstanding_loan_balances_fines_settings">

                <div class="form-group">
                    <label>What type of fine do you charge for outstanding balances? <span class="required">*</span></label>
                    <div class="input-group col-xs-12 ">
                        <?php echo form_dropdown('outstanding_loan_balance_fine_type',array(''=>'--Select Oustanding Loan Balance fine type--')+$late_loan_payment_fine_types,"",'class="form-control modal_select2 outstanding_loan_balance_fine_type" id = "outstanding_loan_balance_fine_type"  ') ?>
                        <span class="help-block"> eg A Fixed Fine. </span>
                    </div>
                </div>

                <div class="row outstanding_loan_balance_fixed_fine">
                    <div class="form-group col-sm-6">
                        <label>Fixed Fine Amount Charged for Outstanding Balances<span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <?php echo form_input('outstanding_loan_balance_fine_fixed_amount',"",'  class="form-control currency outstanding_loan_balance_fine_fixed_amount" placeholder="Outsanding Loan Balance Fixed Fine Amount"'); ?>
                        </div>
                        <span class="help-block"> eg 500. </span>
                    </div>

                    <div class="form-group col-sm-6 col-xs-12">
                        <label>Frequecy to be Charged on Fixed Amount<span class="required">*</span></label>
                        <?php echo form_dropdown('outstanding_loan_balance_fixed_fine_frequency',array(''=>'--Select  the fine frequency--')+$late_payments_fine_frequency,"",'class="form-control modal_select2 outstanding_loan_balance_fixed_fine_frequency" id = "outstanding_loan_balance_fixed_fine_frequency"  ') ?>
                        <span class="help-block"> Select the fine frequency. </span>
                    </div>
                </div>


                <div class="row outstanding_loan_balance_percentage_settings">
                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Percentage Fine Rate<span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <?php echo form_input('outstanding_loan_balance_percentage_fine_rate',"",'  class="form-control numeric outstanding_loan_balance_percentage_fine_rate" placeholder="Percentage Fine Rate"'); ?>
                        </div>
                        <span class="help-block"> Enter the Fine's % rate charged. </span>
                    </div>

                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Fine Frequecy<span class="required">*</span></label>
                        <?php echo form_dropdown('outstanding_loan_balance_percentage_fine_frequency',array(''=>'--Select  the fine frequecy--')+$late_payments_fine_frequency,"",'class="form-control select2 outstanding_loan_balance_percentage_fine_frequency" id = "outstanding_loan_balance_percentage_fine_frequency"  ') ?>
                        <span class="help-block"> Select the frequency of the fine. </span>
                    </div>

                    <div class="form-group col-sm-4 col-xs-12">
                        <label>Fine Charge on <span class="required">*</span></label>
                        <?php echo form_dropdown('outstanding_loan_balance_percentage_fine_on',array(''=>'--Select where is Fine Charged On--')+$percentage_fine_on,"",'class="form-control select2 outstanding_loan_balance_percentage_fine_on" id = "outstanding_loan_balance_percentage_fine_on"  ') ?>
                        <span class="help-block"> Select where the fine is charge on. </span>
                    </div>
                </div>


                <div class="row outstanding_loan_balance_fine_one_off_settings">

                    <div class="form-group col-sm-6">
                        <label>One Off Amount Charged for Oustanding Balances<span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <?php echo form_input('outstanding_loan_balance_fine_one_off_amount',"",'  class="form-control currency outstanding_loan_balance_fine_one_off_amount" placeholder="Outsanding Loan Balance One Off Fine Amount"'); ?>
                        </div>
                        <span class="help-block"> eg 500. </span>
                    </div>

                </div>

            </div>

            <div class="form-group">
                <label>Defer loan penalty payments untill all the loan installments are paid for</label>
                <div class="input-group checkbox-list col-xs-12 ">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('enable_loan_fine_deferment',1,"",' id="enable_loan_fine_deferment" class="enable_loan_fine_deferment" '); ?> Enable loan fine deferment.
                    </label>
                </div>
            </div>



            <div class="form-group">
                <label>Do you charge loan processing fee?</label>
                <div class="input-group checkbox-list col-xs-12 ">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('enable_loan_processing_fee',1,"",' id="enable_oustanding_loan_payment" class="enable_loan_processing_fee" '); ?> Charge loan processing fee.
                    </label>
                </div>
            </div>

            <div class="loan_processing_fee_settings">
                <div class="form-group">
                    <label>loan processing fee type <span class="required">*</span></label>
                    <div class="input-group col-xs-12 ">
                        <?php echo form_dropdown('loan_processing_fee_type',array(''=>'--Select Loan Processing Fee Type--')+$loan_processing_fee_types,"",'class="form-control modal_select2 loan_processing_fee_type" id = "loan_processing_fee_type"  ') ?>
                        <span class="help-block"> eg A Fixed Amount. </span>
                    </div>
                </div>

                <div class="row fixed_amount_processing_fee_settings">
                    <div class="form-group col-sm-6">
                        <label>Enter amount to be charged as processing fee<span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <?php echo form_input('loan_processing_fee_fixed_amount',"",'  class="form-control currency loan_processing_fee_fixed_amount" id="loan_processing_fee_fixed_amount" placeholder="Enter processing fee amount"'); ?>
                        </div>
                        <span class="help-block"> eg 500. </span>
                    </div>
                </div>

                <div class="row percentage_fee_processing_fee_settings">
                    <div class="form-group col-sm-6 col-xs-12">
                        <label>Processing fee percentage (%)<span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calculator"></i>
                            </span>
                            <?php echo form_input('loan_processing_fee_percentage_rate',"",'  class="form-control numeric loan_processing_fee_percentage_rate" placeholder="Processing Fee Percentage"'); ?>
                        </div>
                        <span class="help-block"> eg. 5%. </span>
                    </div>

                    <div class="form-group col-sm-6 col-xs-12">
                        <label>Percentage charged on <span class="required">*</span></label>
                        <?php echo form_dropdown('loan_processing_fee_percentage_charged_on',array(''=>'--Select where Percentage is charged on--')+$loan_processing_fee_percentage_charged_on,"",'class="form-control modal_select2 loan_processing_fee_percentage_charged_on" id = "loan_processing_fee_percentage_charged_on"  ') ?>
                        <span class="help-block"> eg Total Loan Amount. </span>
                    </div>
                </div>

            </div>


            <div class="form-group">
                <label>Check the box below to add guarantor(s) to this Loan?</label>
                <div class="input-group checkbox-list col-xs-12 ">
                    <label class="checkbox-inline">
                        <?php echo form_checkbox('enable_loan_guarantors',1,"",' id="enable_loan_guarantors" class="enable_loan_guarantors" '); ?> Enable loan guarantors.
                    </label>
                </div>
            </div>

            <div class="row loan_guarantor_settings">

                <?php if((isset($_POST['guarantor_id']) || isset($guarantors_details)) && !empty($guarantors_details)):
                if(!$this->input->post('')):
                    $_POST+= $guarantors_details;
                endif;
                foreach($_POST['guarantor_id'] as $key=>$value):?>
                <div class="loan_guarantor_settings_values">
                    <div class="form-group col-md-4">
                        <label>Guarantor's Name<span class="required">*</span></label>
                        <div class="input-group col-xs-12 ">
                            <?php echo form_dropdown('guarantor_id[]',array(''=>'--Select a Guarantor--')+$members,$value,'class="form-control select2 guarantor_id"  ') ?>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Guaranteed Amount</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <?php echo form_input('guaranteed_amount[]',$_POST['guaranteed_amount'][$key],'  class="form-control currency guaranteed_amount" placeholder="Enter guaranteed amount"'); ?>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Comment</label>
                        <?php echo form_input('guarantor_comment[]',$_POST['guarantor_comment'][$key],'  class="form-control guarantor_comment" placeholder="Enter comment"'); ?>
                    </div>

                    <div class='col-md-12 margin-bottom-10 text-left'>
                        <a data-original-title="Remove Guarantor" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-line" >
                            <i class="fa fa-times"></i>
                            <span class="hidden-380">
                                Remove Guarantor
                            </span>
                        </a>
                    </div>
                    <div class="clearfix"></div><hr/>
                </div>
                <?php endforeach;else:?>  
                <div class="loan_guarantor_settings_values">
                    <div class="form-group col-md-4">
                        <label>Guarantor's Name<span class="required">*</span></label>
                        <div class="input-group col-xs-12 ">
                            <?php echo form_dropdown('guarantor_id[]',array(''=>'--Select a Guarantor--')+$this->active_group_member_options,'','class="form-control select2 guarantor_id"  ') ?>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Guaranteed Amount</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            <?php echo form_input('guaranteed_amount[]','','  class="form-control currency guaranteed_amount" placeholder="Enter guaranteed amount"'); ?>
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Comment</label>
                        <?php echo form_input('guarantor_comment[]','','  class="form-control guarantor_comment" placeholder="Enter comment"'); ?>
                    </div>

                    <div class='col-md-12 margin-bottom-10 text-left'>
                        <a data-original-title="Remove Guarantor" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-line" >
                            <i class="fa fa-times"></i>
                            <span class="hidden-380">
                                Remove Guarantor
                            </span>
                        </a>
                    </div>
                    <div class="clearfix"></div><hr/>
                </div>
                <?php endif;?>              

                <div id='append-place-holder'></div>

                <div class='col-md-12 margin-bottom-10 text-left'>
                    <a href="javascript:;" class="btn btn-default btn-xs" id="add-new-line-guarantor">
                        <i class="fa fa-plus"></i>
                        <span class="hidden-380">
                            Add another Guarantor
                        </span>
                    </a>
                </div>
            </div>
        </div>   
    </div>
</div>
<div id='append_fine_setting_row'>
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
<a class="stacked_inline pop_up hidden" data-row="" data-toggle="modal" data-content="#contributions_form" data-title="Add Contribution" data-id="add_contribution" id="add_contribution" href="#">Add Contribution</a>
<div id="add_fine_category" class="modal-form-contents">
    <div class="alert alert-danger data_error" id="" style="display:none;">
    </div>
    <div id="add_fine_category_form" >
        <div class="form-group">
            <label>Fine Category Name<span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                <?php echo form_input('name','',' id="name" class="form-control" placeholder="Fine Category Name"');?>
                <?php echo form_hidden("slug",""); ?>
                <?php echo form_hidden("id",""); ?>
            </div>
        </div>
    </div>
</div>
<a class="stacked_inline pop_up hidden" data-row="" data-toggle="modal" data-content="#add_fine_category" data-title="Add Fine Category" data-id="add_fine_category" id="add_fine_category_link" href="#">Add Fine Category</a>
<div id="add_depositor" class="modal-form-contents">
    <div class="alert alert-danger data_error" id="" style="display:none;">
    </div>
    <div id="add_depositor_form" >
        <div class="form-group">
            <label>

                <?php
                    if($this->lang->line('name')){
                        echo $this->lang->line('name');
                    }else{
                        echo 'Name';
                    }
                ?>
                <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                <?php echo form_input('name','',' id="name" class="form-control" placeholder="Name"');?>
            </div>
        </div>
        <div class="form-group">
            <label>
                <?php
                    if($this->lang->line('phone_number')){
                        echo $this->lang->line('phone_number');
                    }else{
                        echo 'Phone Number';
                    }
                ?>
            </label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </span>
                <?php echo form_input('phone','','class="form-control" placeholder="Phone Number" id="phone"');?>
            </div>
        </div>
        <div class="form-group">
            <label>
                <?php
                    if($this->lang->line('email_address')){
                        echo $this->lang->line('email_address');
                    }else{
                        echo 'Email Address';
                    }
                ?>
            </label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                </span>
                <?php echo form_input('email','','class="form-control" placeholder="Email Address" id="email"');?>
            </div>
        </div>
        <div class="form-group">
            <label>
                <?php
                    if($this->lang->line('description')){
                        echo $this->lang->line('description');
                    }else{
                        echo 'Description';
                    }
                ?>
            </label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-book"></i>
                </span>
                <?php echo form_textarea('description','',' id="description" class="form-control" placeholder="Description"');?>
            </div>
        </div>
    </div>
</div>
<a class="stacked_inline pop_up hidden" data-row="" data-toggle="modal" data-content="#add_member" data-title="Add Member" data-id="add_member" id="add_member" href="#">
        <?php
                    if($this->lang->line('add_members')){
                        echo $this->lang->line('add_members');
                    }else{
                        echo 'Add Members';
                    }
                ?>
        </a>
<div id="add_income_category" class="modal-form-contents">
    <div class="alert alert-danger data_error" id="" style="display:none;">
    </div>
    <div id="add_income_category_form" >
        <div class="form-group">
            <label> <?php
                    if($this->lang->line('name')){
                        echo $this->lang->line('name');
                    }else{
                        echo 'Name';
                    }
                ?><span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                <?php echo form_input('name','',' id="name" class="form-control" placeholder="Name"');?>
            </div>
        </div>
        <div class="form-group">
            <label>
                
                 <?php
                    if($this->lang->line('description')){
                        echo $this->lang->line('description');
                    }else{
                        echo 'Description';
                    }
                ?>
            </label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-book"></i>
                </span>
                <?php echo form_textarea('description','',' id="description" class="form-control" placeholder="Description"');?>
            </div>
        </div>
        <?php echo form_hidden('id','');?>       
        <?php echo form_hidden('slug','','class="form-control slug"'); ?> 
    </div>
</div>
<a class="stacked_inline pop_up hidden" data-row="" data-toggle="modal" data-content="#add_income_category" data-title="Add Income Category" data-id="add_income_category" id="add_income_category_link" href="#">Add Income Category</a>
<div id="accounts_form" class="modal-form-contents">  
    <div class="alert alert-danger data_error" id="" style="display:none;">
    </div>
    <div class="">
        <div id='account_options' class="">
            <div class="row">
                <div class="col-sm-6">
                    <div class="mt-element-ribbon bg-grey-steel">
                        <div class="ribbon ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-info uppercase">
                            <div class="ribbon-sub ribbon-clip "></div> Bank Account </div>
                        <p class="ribbon-content"><strong>Banking with a Banking Institution?</strong> 
                        </p>
                        <span class="pull-right">
                            <a id='create_bank_account' href="#" class="btn btn-xs btn-primary action-link">Create Account <i class="fa fa-angle-double-right"></i></a>
                        </span>
                    </div>
                </div>


                 <div class="col-sm-6">
                    <div class="mt-element-ribbon bg-grey-steel">
                        <div class="ribbon ribbon-border-hor ribbon-clip ribbon-color-danger uppercase">
                            <div class="ribbon-sub ribbon-clip"></div> Sacco Account </div>
                        <p class="ribbon-content">
                            <strong>Banking with a Sacco?</strong>
                        </p>
                        <span class="pull-right">
                            <a id='create_sacco_account' href="#" class="btn btn-xs btn-primary action-link">Create Account <i class="fa fa-angle-double-right"></i></a>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-6">
                    <div class="mt-element-ribbon bg-grey-steel">
                        <div class="ribbon ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-success uppercase">
                            <div class="ribbon-sub ribbon-clip"></div> Mobile Money Cash Account</div>
                        <p class="ribbon-content">
                            <strong>Using a Till Number or Mobile Number Account to Bank?</strong>
                        </p>

                        <span class="pull-right">
                            <a id='create_mobile_money_account' href="#" class="btn btn-xs btn-primary action-link">Create Account <i class="fa fa-angle-double-right"></i></a>
                        </span>

                    </div>
                </div>

                 <div class="col-sm-6">
                     <div class="mt-element-ribbon bg-grey-steel">
                        <div class="ribbon ribbon-border-hor ribbon-clip ribbon-color-info uppercase">
                            <div class="ribbon-sub ribbon-clip"></div> Petty Cash Account </div>
                        <p class="ribbon-content">
                            <strong>Does your group perform Cash at Hand Transactions?</strong>
                        </p>
                        <span class="pull-right">
                            <a id='create_petty_cash_account' href="#" class="btn btn-xs btn-primary action-link">Create Account <i class="fa fa-angle-double-right"></i></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div id='bank_account_form'>
            <div class="form-body">

                <div class="form-group">
                    <a href="#" class="back_to_account_options btn btn-xs btn-primary action-link"> <i class="fa fa-hand-o-left"></i> Back to Account Options </a>
                </div>

                <div class="form-group">
                    <label>Account Name<span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-bank"></i>
                        </span>
                        <?php echo form_input('account_name','','id="bank_account_name" class="form-control" placeholder="Account Name"'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bank-name">
                        <?php
                            if($this->lang->line('bank_name')){
                            echo $this->lang->line('bank_name');
                            }else{
                            echo "Bank Name";
                        }
                        ?>
                <span class="required">*</span></label>
                        <div class="input-group col-md-12 col-sm-12 col-xs-12">
                            <?php echo form_dropdown('bank_id',array(''=>'--Select Bank--')+$banks,'','id="bank_id" class="form-control modal_select2"  ') ?>
                        </div>
                </div>

                <div id="bank_branch_input_group" class="form-group">
                    <label for="bank-branches">

                        <?php
                            if($this->lang->line('bank_branch')){
                            echo $this->lang->line('bank_branch');
                            }else{
                            echo "Bank Branch";
                        }
                        ?>

                        <span class="required">*</span></label>
                        <div class="input-group col-md-12 col-sm-12 col-xs-12 bank_branches_space">
                            <?php echo form_dropdown('bank_branch_id',array(''=>'--Select Bank Name First--'),'','class="form-control modal_select2" id = "bank_branch_id"  ') ?>
                        </div>
                </div>

                <div class="form-group">
                    <label>

                        <?php
                            if($this->lang->line('account_number')){
                            echo $this->lang->line('account_number');
                            }else{
                            echo "Account Number";
                        }
                        ?><span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-bars"></i>
                        </span>
                        <?php echo form_input('account_number','',' id="bank_account_number" class="form-control" placeholder="Account Number"'); ?>
                    </div>
                </div>
                <?php echo form_hidden('id','');?> 
            </div>
        </div>

        <div id='sacco_account_form'>
            <div class="form-group">
                <a href="#" class="back_to_account_options btn btn-xs btn-primary action-link"> <i class="fa fa-hand-o-left"></i> Back to Account Options </a>
            </div>
            <div class="form-group">
                <label>

                    <?php
                            if($this->lang->line('account_name')){
                            echo $this->lang->line('account_name');
                            }else{
                            echo "Account Name";
                        }
                        ?><span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-bank"></i>
                    </span>
                    <?php echo form_input('account_name','','class="form-control" placeholder="Account Name" id="sacco_account_name" '); ?>
                </div>
            </div>

            <div class="form-group">
                <label for="bank-name">

                    <?php
                            if($this->lang->line('sacco_name')){
                            echo $this->lang->line('sacco_name');
                            }else{
                            echo "Group Name";
                        }
                        ?>
                    <span class="required">*</span></label>
                    <div class="input-group col-md-12 col-sm-12 col-xs-12">
                        <?php echo form_dropdown('sacco_id',array(''=>'--Select Sacco--')+$saccos,'','class="form-control modal_select2" id="sacco_id"  ') ?>
                    </div>
            </div>

            <div id="sacco_branch_input_group"  class="form-group">
                <label for="bank-branches">Sacco Branch<span class="required">*</span></label>
                    <div class="input-group col-md-12 col-sm-12 col-xs-12 sacco_branches_space">
                        <?php echo form_dropdown('sacco_branch_id',array(''=>'--Select Sacco Name First--'),'','class="form-control modal_select2" id = "sacco_branch_id"  ') ?>
                    </div>
            </div>

            <div class="form-group">
                <label>
                    <?php
                            if($this->lang->line('account_number')){
                            echo $this->lang->line('account_number');
                            }else{
                            echo "Account Number";
                        }
                        ?>
                    <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-bars"></i>
                    </span>
                    <?php echo form_input('account_number','','class="form-control" placeholder="Account Number" id="sacco_account_number"'); ?>
                </div>
            </div>

            <?php echo form_hidden('id','');?>   
        </div>

        <div id='mobile_money_account_form'>
            <div class="form-body">
                <div class="form-group">
                    <a href="#" class="back_to_account_options btn btn-xs btn-primary action-link"> <i class="fa fa-hand-o-left"></i> Back to Account Options </a>
                </div>

                <div class="form-group">
                    <label>

                        <?php
                            if($this->lang->line('mobile_money_account_name')){
                            echo $this->lang->line('mobile_money_account_name');
                            }else{
                            echo "Mobile Money Account Name";
                        }
                        ?>
                        <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-bank"></i>
                        </span>
                        <?php echo form_input('account_name','','class="form-control" placeholder="Mobile Money Account Name" id="mobile_money_account_name" '); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bank-name">

                        <?php
                            if($this->lang->line('mobile_money_provider_name')){
                            echo $this->lang->line('mobile_money_provider_name');
                            }else{
                            echo "Mobile Money Provider Name";
                        }
                        ?>

                        <span class="required">*</span></label>
                        <div class="input-group col-md-12 col-sm-12 col-xs-12">
                            <?php echo form_dropdown('mobile_money_provider_id',array(''=>'--Select Mobile Money Provider--')+$mobile_money_providers,'','class="form-control modal_select2" id="mobile_money_provider_id"  ') ?>
                        </div>
                </div>

                <div class="form-group">
                    <label>

                        <?php
                            if($this->lang->line('account_number')){
                            echo $this->lang->line('account_number')."/";
                            }else{
                            echo "Account Number/";
                        }
                        
                            if($this->lang->line('phone_number')){
                            echo $this->lang->line('phone_number')."/";
                            }else{
                            echo "Phone Number/";
                        }
                        
                            if($this->lang->line('till_number')){
                            echo $this->lang->line('till_number');
                            }else{
                            echo "Till Number";
                        }
                        ?>
                    
                    <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-mobile-phone"></i>
                        </span>
                        <?php echo form_input('account_number','','class="form-control" placeholder="Account Number / Phone Number / Till Number" id="mobile_money_account_number"'); ?>
                    </div>
                </div>
            
                <?php echo form_hidden('id','');?>   
            </div>
        </div>

        <div id='petty_cash_account_form'>
            <div class="form-body">

                <div class="form-group">
                    <a href="#" class="back_to_account_options btn btn-xs btn-primary action-link"> <i class="fa fa-hand-o-left"></i> Back to Account Options </a>
                </div>
                <div class="form-group">
                    <label>
                        <?php
                            if($this->lang->line('petty_cash_account_name')){
                            echo $this->lang->line('petty_cash_account_name');
                            }else{
                            echo "Petty Cash Account Name";
                        }
                        ?>
                        <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-bank"></i>
                        </span>
                        <?php echo form_input('account_name','','class="form-control slug_parent" placeholder="Petty Cash Account Name " id="petty_cash_account_name"'); ?>
                    </div>
                </div>
                <?php echo form_hidden('id','');?>       
                <?php echo form_hidden('slug','','class="form-control slug"'); ?>     
    
            </div>
        </div>
    </div>
</div>
<a class="stacked_inline pop_up hidden" data-row="" data-toggle="modal" data-content="#add_depositor" data-title="Add Depositor" data-id="add_depositor" id="add_depositor_link" href="#">Add Depositor</a>
<div id="accounts_form" class="modal-form-contents">  
    <div class="alert alert-danger data_error" id="" style="display:none;">
    </div>
    <div class="">
        <div id='account_options' class="">
            <div class="row">
                <div class="col-sm-6">
                    <div class="mt-element-ribbon bg-grey-steel">
                        <div class="ribbon ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-info uppercase">
                            <div class="ribbon-sub ribbon-clip "></div>

                             <?php
                            if($this->lang->line('account_number')){
                            echo $this->lang->line('account_number');
                            }else{
                            echo "Account Number";
                        }
                        ?>

                    </div>
                        <p class="ribbon-content"><strong>Banking with a Banking Institution?</strong> 
                        </p>
                        <span class="pull-right">
                            <a id='create_bank_account' href="#" class="btn btn-xs btn-primary action-link">Create Account <i class="fa fa-angle-double-right"></i></a>
                        </span>
                    </div>
                </div>


                 <div class="col-sm-6">
                    <div class="mt-element-ribbon bg-grey-steel">
                        <div class="ribbon ribbon-border-hor ribbon-clip ribbon-color-danger uppercase">
                            <div class="ribbon-sub ribbon-clip"></div> Sacco Account </div>
                        <p class="ribbon-content">
                            <strong>Banking with a Sacco?</strong>
                        </p>
                        <span class="pull-right">
                            <a id='create_sacco_account' href="#" class="btn btn-xs btn-primary action-link">Create Account <i class="fa fa-angle-double-right"></i></a>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-6">
                    <div class="mt-element-ribbon bg-grey-steel">
                        <div class="ribbon ribbon-clip ribbon-shadow ribbon-round ribbon-border-dash-hor ribbon-color-success uppercase">
                            <div class="ribbon-sub ribbon-clip"></div> Mobile Money Cash Account</div>
                        <p class="ribbon-content">
                            <strong>Using a Till Number or Mobile Number Account to Bank?</strong>
                        </p>

                        <span class="pull-right">
                            <a id='create_mobile_money_account' href="#" class="btn btn-xs btn-primary action-link">Create Account <i class="fa fa-angle-double-right"></i></a>
                        </span>

                    </div>
                </div>

                 <div class="col-sm-6">
                     <div class="mt-element-ribbon bg-grey-steel">
                        <div class="ribbon ribbon-border-hor ribbon-clip ribbon-color-info uppercase">
                            <div class="ribbon-sub ribbon-clip"></div>
                        
                        <?php
                            if($this->lang->line('petty_cash_accounts')){
                            echo $this->lang->line('petty_cash_accounts');
                            }else{
                            echo "Petty Cash Account";
                        }
                        ?>
                    
                        </div>
                        <p class="ribbon-content">
                            <strong>Does your group perform Cash at Hand Transactions?</strong>
                        </p>
                        <span class="pull-right">
                            <a id='create_petty_cash_account' href="#" class="btn btn-xs btn-primary action-link">Create Account<i class="fa fa-angle-double-right"></i></a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div id='bank_account_form'>
            <div class="form-body">

                <div class="form-group">
                    <a href="#" class="back_to_account_options btn btn-xs btn-primary action-link"> <i class="fa fa-hand-o-left"></i> Back to Account Options </a>
                </div>

                <div class="form-group">
                    <label><?php
                            if($this->lang->line('account_name')){
                            echo $this->lang->line('account_name');
                            }else{
                            echo "Account Name";
                        }
                        ?><span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-bank"></i>
                        </span>
                        <?php echo form_input('account_name','','id="bank_account_name" class="form-control" placeholder="Account Name"'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bank-name"><?php
                            if($this->lang->line('bank_name')){
                            echo $this->lang->line('bank_name');
                            }else{
                            echo "Bank Name";
                        }
                        ?><span class="required">*</span></label>
                        <div class="input-group col-md-12 col-sm-12 col-xs-12">
                            <?php echo form_dropdown('bank_id',array(''=>'--Select Bank--')+$banks,'','id="bank_id" class="form-control modal_select2"  ') ?>
                        </div>
                </div>

                <div id="bank_branch_input_group" class="form-group">
                    <label for="bank-branches"><?php
                            if($this->lang->line('bank_branch')){
                            echo $this->lang->line('bank_branch');
                            }else{
                            echo "Bank Name";
                        }
                        ?><span class="required">*</span></label>
                        <div class="input-group col-md-12 col-sm-12 col-xs-12 bank_branches_space">
                            <?php echo form_dropdown('bank_branch_id',array(''=>'--Select Bank Name First--'),'','class="form-control modal_select2" id = "bank_branch_id"  ') ?>
                        </div>
                </div>

                <div class="form-group">
                    <label>Account Number<span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-bars"></i>
                        </span>
                        <?php echo form_input('account_number','',' id="bank_account_number" class="form-control" placeholder="Account Number"'); ?>
                    </div>
                </div>
                <?php echo form_hidden('id','');?> 
            </div>
        </div>

        <div id='sacco_account_form'>
            <div class="form-group">
                <a href="#" class="back_to_account_options btn btn-xs btn-primary action-link"> <i class="fa fa-hand-o-left"></i> Back to Account Options </a>
            </div>
            <div class="form-group">
                <label>Account Name<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-bank"></i>
                    </span>
                    <?php echo form_input('account_name','','class="form-control" placeholder="Account Name" id="sacco_account_name" '); ?>
                </div>
            </div>

            <div class="form-group">
                <label for="bank-name">Sacco Name<span class="required">*</span></label>
                    <div class="input-group col-md-12 col-sm-12 col-xs-12">
                        <?php echo form_dropdown('sacco_id',array(''=>'--Select Sacco--')+$saccos,'','class="form-control modal_select2" id="sacco_id"  ') ?>
                    </div>
            </div>

            <div id="sacco_branch_input_group"  class="form-group">
                <label for="bank-branches">Sacco Branch<span class="required">*</span></label>
                    <div class="input-group col-md-12 col-sm-12 col-xs-12 sacco_branches_space">
                        <?php echo form_dropdown('sacco_branch_id',array(''=>'--Select Sacco Name First--'),'','class="form-control modal_select2" id = "sacco_branch_id"  ') ?>
                    </div>
            </div>

            <div class="form-group">
                <label>Account Number<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-bars"></i>
                    </span>
                    <?php echo form_input('account_number','','class="form-control" placeholder="Account Number" id="sacco_account_number"'); ?>
                </div>
            </div>

            <?php echo form_hidden('id','');?>   
        </div>

        <div id='mobile_money_account_form'>
            <div class="form-body">
                <div class="form-group">
                    <a href="#" class="back_to_account_options btn btn-xs btn-primary action-link"> <i class="fa fa-hand-o-left"></i> Back to Account Options </a>
                </div>

                <div class="form-group">
                    <label><?php
                            if($this->lang->line('mobile_money_account_name')){
                            echo $this->lang->line('mobile_money_account_name');
                            }else{
                            echo "Mobile Money Account Name";
                        }
                        ?><span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-bank"></i>
                        </span>
                        <?php echo form_input('account_name','','class="form-control" placeholder="Mobile Money Account Name" id="mobile_money_account_name" '); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bank-name"><?php
                            if($this->lang->line('mobile_money_provider_name')){
                            echo $this->lang->line('mobile_money_provider_name');
                            }else{
                            echo "Mobile Money Provider Name";
                        }
                        ?><span class="required">*</span></label>
                        <div class="input-group col-md-12 col-sm-12 col-xs-12">
                            <?php echo form_dropdown('mobile_money_provider_id',array(''=>'--Select Mobile Money Provider--')+$mobile_money_providers,'','class="form-control modal_select2" id="mobile_money_provider_id"  ') ?>
                        </div>
                </div>

                <div class="form-group">
                    <label>

                        <?php
                            if($this->lang->line('account_number')){
                            echo $this->lang->line('account_number')."/";
                            }else{
                            echo "Account Number/";
                        }

                            if($this->lang->line('phone_number')){
                            echo $this->lang->line('phone_number')."/";
                            }else{
                            echo "Phone Number/";
                        }

                            if($this->lang->line('till_number')){
                            echo $this->lang->line('till_number');
                            }else{
                            echo "Till Number";
                        }
                        ?>
                    
                        <span class="required">*</span></label>
                        <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-mobile-phone"></i>
                        </span>
                        <?php echo form_input('account_number','','class="form-control" placeholder="Account Number / Phone Number / Till Number" id="mobile_money_account_number"'); ?>
                    </div>
                </div>
            
                <?php echo form_hidden('id','');?>   
            </div>
        </div>

        <div id='petty_cash_account_form'>
            <div class="form-body">

                <div class="form-group">
                    <a href="#" class="back_to_account_options btn btn-xs btn-primary action-link"> <i class="fa fa-hand-o-left"></i> Back to Account Options </a>
                </div>
                <div class="form-group">
                    <label>
                        <?php
                            if($this->lang->line('petty_cash_acoount_name')){
                            echo $this->lang->line('petty_cash_account_name');
                            }else{
                            echo "Petty Cash Account Name";
                        }
                        ?>
                        <span class="required">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-bank"></i>
                        </span>
                        <?php echo form_input('account_name','','class="form-control slug_parent" placeholder="Petty Cash Account Name " id="petty_cash_account_name"'); ?>
                    </div>
                </div>
                <?php echo form_hidden('id','');?>       
                <?php echo form_hidden('slug','','class="form-control slug"'); ?>     
    
            </div>
        </div>
    </div>
</div>
<a class="stacked_inline pop_up hidden" data-row="" data-toggle="modal" data-content="#accounts_form" data-title="Add Account" data-id="add_account" id="add_account_link" href="#">Add Account</a>
<div id="add_asset" class="modal-form-contents">
    <div class="alert alert-danger data_error" id="" style="display:none;">
    </div>
    <div id="add_asset_form" >
        <div class="form-group">
            <label>Asset Name<span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                <i class="fa fa-building"></i>
                </span>
                <?php echo form_input('name','','id="asset_name" class="form-control" placeholder="Asset Name"'); ?>
            </div>
        </div>
        <div class="form-group">
            <label>

                <?php
                    if($this->lang->line('asset_category')){
                    echo $this->lang->line('asset_category');
                    }else{
                    echo "Asset Category";
                    }
                ?>
                        <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                <i class="fa fa-building"></i>
                </span>
                <?php echo form_dropdown('asset_category_id',array(''=>'Select asset category')+$asset_category_options,'','id="asset_category_id" class="form-control asset_category modal_select2" placeholder="Asset Category"'); ?>
            </div>
        </div>
        <div class="form-group">
            <label>Asset Cost<span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                <i class="fa fa-money"></i>
                </span>
                <?php echo form_input('cost','','id="asset_cost" class="form-control currency" placeholder="Asset Cost"'); ?>
            </div>
        </div>
        <div class="form-group">
            <label>
                <?php
                    if($this->lang->line('description')){
                    echo $this->lang->line('description');
                    }else{
                    echo "Description";
                    }
                ?>
            </label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-book"></i>
                </span>
                <?php echo form_textarea('description','','id="asset_description" class="form-control" placeholder="Asset Description"');?>
            </div>
        </div>
    </div>
</div>
<a class="stacked_inline pop_up hidden" data-row="" data-toggle="modal" data-content="#add_asset" data-title="Add Asset" data-id="add_asset" id="add_asset_link" href="#">Add Asset</a>

<a class="stacked_inline hidden" data-row="" data-toggle="modal" data-content="#loans_form" data-title="Add Loan" data-id="add_loan" id="add_loan" href="#">
    <?php
        $default_message='Add Loan';
        $this->languages_m->translate('add_loan',$default_message);
    ?>
<script>
$(document).ready(function(){

    App.blockUI({
        target: '#unreconciled_deposits_listing',
        overlayColor: 'white',
        animate: true
    });

    var unreconciled_deposit_id;

    var form;

    //$('.toggle_transaction_alert_details').on('click',function(){
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

    $(document).on('click','#full-width-modal #add-new-line',function(){
        form = $(this).parent().parent().parent().find("#modal_submit_form");
        var html = $('#append-new-line tbody').html();
        html = html.replace_all('checker','');
        form.find('#append-place-holder').append(html);
        $('input[type=checkbox]:not(.make-switch)').uniform();
        $('.tooltips').tooltip();
        update_field_names(form);
        form.find('.modal_select2').select2({width:'100%'});
        FormInputMask.init();
    });

    $(document).on('click','#full-width-modal a.remove-line',function(event){
        var table = $(this).parent().parent().parent().parent().parent().find(".table-multiple-items");;
        $(this).parent().parent().remove();
        var number = 1;
        table.find('.count').each(function(){
            $(this).text(number);
            number++;
        });
    });

    $(document).on('change','.deposit_for',function(){
        $(this).parent().parent().nextAll().remove();
        var element = $(this).parent().parent().parent().find('.deposit_for_cell');
        if($(this).val()==1){
            var html = $('#contribution_payment_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            $('.table-multiple-items .member').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_member" data-title="Add Member" data-id="add_member" id="add_member" href="#">Add Member</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
            $('.table-multiple-items .contribution').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#contributions_form" data-title="Add Contribution" data-id="add_contribution" id="add_contribution" href="#">Add Contribution</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==2){
            var html = $('#fine_payment_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            $('.table-multiple-items .member').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_member" data-title="Add Member" data-id="add_member" id="add_member" href="#">Add Member</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
            $('.table-multiple-items .fine_category').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_fine_category" data-title="Add Fine Category" data-id="add_fine_category" id="add_fine_category_link" href="#">Add Fine Category</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==3){
            var html = $('#miscellaneous_payment_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            $('.table-multiple-items .member').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_member" data-title="Add Member" data-id="add_member" id="add_member" href="#">Add Member</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==4){
            var html = $('#income_deposit_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            $('.table-multiple-items .depositor').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_depositor" data-title="Add Depositor" data-id="add_depositor" id="add_depositor_link" href="#">Add Depositor</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
            $('.table-multiple-items .income_category').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_income_category" data-title="Add Income Category" data-id="add_income_category" id="add_income_category_link" href="#">Add Income Category</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==5){
            var html = $('#loan_repayment_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            $('.table-multiple-items .member').select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_member" data-title="Add Member" data-id="add_member" id="add_member" href="#">Add Member</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==6){
            var html = $('#bank_loan_disbursement_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==7){
            var html = $('#incoming_money_transfer_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
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
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==8){
            var html = $('#stock_sale_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==9){
            var html = $('#asset_sale_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
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
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==10){
            var html = $('#money_market_cash_in_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==11){
            var html = $('#loan_processing_income_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            FormInputMask.init();
            update_field_names(form);
        }else if($(this).val()==12){
            var html = $('#external_loan_repayment_fields .row').html();
            $(html).insertAfter(element);
            $('.table-multiple-items .modal_select2').select2({width:'100%'});
            $('.table-multiple-items #deposit_particulars_header ,#append-new-line .particulars_place_holder').attr('colspan',3);
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',3);
            });
            FormInputMask.init();
            update_field_names(form);
        }else{
            $(this).parent().parent().parent().find('.contribution_payment_fields').each(function(count){
                count++;
                if(count==1){
                    $(this).replaceWith('<td class="particulars_place_holder"></td>').attr('colspan',3);
                }else{
                    $(this).remove();
                }
            });
            $('.table-multiple-items .particulars_place_holder').each(function(){
                $(this).attr('colspan',$('.table-multiple-items #deposit_particulars_header').attr('colspan'));
            });
        }
    });

    var current_row = 0;
    
    $(document).on('select2:open','.member', function(e) {
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

    $(document).on('select2:open','.contribution_id', function(e) {
        // do something
        var name = $(this).attr("name");
        var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
        current_row = row;
    });

    $(document).on('select2:open','.loan_id', function(e) {
        // do something
        var name = $(this).attr("name");
        var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
        current_row = row;
    });

    $(document).on('select2:open','.from_account_id', function(e) {
        // do something
        var name = $(this).attr("name");
        var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
        current_row = row;
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
            App.blockUI({
                target: row.find('.loan-to-populate .select2'),
                overlayColor: 'grey',
                animate: true
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
                     App.unblockUI(row.find(".loan-to-populate .select2"));
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
            App.blockUI({
                target: row.find('.external-loan-to-populate .select2'),
                overlayColor: 'grey',
                animate: true
            });
            var debtor_id = me.val();
            var attribute = me.attr('name');
            var url = '<?php echo site_url('group/loans/ajax_get_active_debtor_loans')?>';
                $.ajax({
                type: "POST",
                url: url,
                dataType: "html",
                data: {'debtor_id':debtor_id,'attribute':attribute,'no_add_loan':true},
                success: function(res) 
                {
                    me.parent().parent().parent().find('.change-loan').html(res);
                    me.parent().parent().parent().find('.loan').select2();
                     App.unblockUI(row.find(".external-loan-to-populate .select2"));
                },
                error: function(xhr, ajaxOptions, thrownError)
                {
                    
                }

            });
        }
    });

    function update_field_names(form){
        if (typeof form == 'undefined') {
            //d onothin for now
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
        $('.full-width-modal-body').hide();
        App.blockUI({
            target: '.full-width-modal-body-loading',
            overlayColor: 'grey',
            animate: true
        });
        $('.full-width-modal-body-loading').css('min-height',"35px").slideDown();
        unreconciled_deposit_id = $(this).attr('data-id');
    });

    $('#full-width-modal').on('shown.bs.modal',function(){   
        var form  = $("#modal_submit_form:visible");
        $.post('<?php echo base_url("group/transaction_alerts/ajax_get"); ?>',{'id':unreconciled_deposit_id,},function(data){
            if(isJson(data)){
                var transaction_alert = $.parseJSON(data);
                form.find('#transaction_details #transaction_date span').html(transaction_alert.formatted_transaction_date);
                form.find('#transaction_details #transaction_particulars span').html(transaction_alert.particulars);
                form.find('#transaction_details #transaction_amount span').html(transaction_alert.formatted_amount);
                form.find('input[name="transaction_alert_id"]').val(transaction_alert.id);
                form.find('input[name="transaction_alert_amount"]').val(transaction_alert.amount);
                $('.full-width-modal-body').slideDown();
                App.unblockUI('.full-width-modal-body-loading');
                $('.full-width-modal-body-loading').slideUp();
            }else{
                alert('Could not find transaction alert.');
            }
        });
    });

    $('#full-width-modal').on('hidden.bs.modal',function(){
        $(".modal_select2").select2('destroy'); 
        $('.data_error').each(function(){
            $(this).hide().html('');
        });
    });

    $('#modal_submit_form').on('submit',function(e){
        var form = $(this);
        var entries_are_valid = true;
        var totals_are_valid = true;
        $('.data_error').each(function(){
            $(this).slideUp().html("");
        });
        $('#modal_submit_form select.deposit_for').each(function(){
            if(check_deposit_for($(this))){

            }else{
                entries_are_valid = false;
            }
        });
        $('#modal_submit_form select.member').each(function(){
            if(check_member($(this))){

            }else{
                entries_are_valid = false;
            }
        });
        $('#modal_submit_form select.debtor').each(function(){
            if(check_debtor($(this))){

            }else{
                entries_are_valid = false;
            }
        });
        $('#modal_submit_form select.contribution').each(function(){
            if(check_contribution($(this))){

            }else{
                entries_are_valid = false;
            }
        });
        var total_amount = 0;
        $('#modal_submit_form input.amount').each(function(){
            if(check_amount($(this))){
                total_amount += parseFloat($(this).val().replace(/,/g, ''));
            }else{
                entries_are_valid = false;
            }
        });
        $('#modal_submit_form select.fine_category').each(function(){
            if(check_fine_category($(this))){

            }else{
                entries_are_valid = false;
            }
        });
        $('#modal_submit_form textarea.miscellaneous_deposit_description').each(function(){
            if(check_miscellaneous_deposit_description($(this))){

            }else{
                entries_are_valid = false;
            }
        });
        $('#modal_submit_form select.depositor').each(function(){
            if(check_depositor($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form select.income_category').each(function(){
            if(check_income_category($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form select.loan').each(function(){
            if(check_loan($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form select.external_loan').each(function(){
            if(check_external_loan($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form textarea.bank_loan_disbursement_description').each(function(){
            if(check_bank_loan_disbursement_description($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form input.amount_payable').each(function(){
            if(check_amount_payable($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form select.from_account_id').each(function(){
            if(check_from_account_id($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form select.stock_id').each(function(){
            if(check_stock($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form input.number_of_shares_sold').each(function(){
            if(check_number_of_shares_sold($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form input.price_per_share').each(function(){
            if(check_price_per_share($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form select.asset_id').each(function(){
            if(check_asset($(this))){

            }else{
                entries_are_valid = false;
            }
        });

        $('#modal_submit_form select.money_market_investment_id').each(function(){
            if(check_money_market_investment($(this))){

            }else{
                entries_are_valid = false;
            }
        });
        var transaction_alert_amount = parseFloat(form.find('input[name="transaction_alert_amount"]').val());
        var error_message = "";
        if(total_amount==transaction_alert_amount){

        }else{
            totals_are_valid = false;
            error_message = "<p>Kindly make sure the amount reconciled adds up to the amount deposited of "+form.find('#transaction_details #transaction_amount span').html()+".</p>";
            $('#modal_submit_form input.amount').each(function(){
                $(this).parent().parent().addClass('has-error');
                $(this).parent().prepend('<i class="fa fa-exclamation "></i>');
                $(this).parent().parent().find('.tooltips').attr('data-original-title','Please enter an amount that adds up to the amount deposited.');
                $('.tooltips').tooltip();
            });
        }

        if(entries_are_valid&&totals_are_valid){
            form.find('.modal_processing_form_button').hide();
            form.find('.submit').show();
            bootbox.confirm("Are you sure, you want to proceed?", function(result) {
                $('.modal_submit_form_button').hide();
                $('.modal_processing_form_button').show(); 
                if(result==true){
                    App.blockUI({
                        target: '.full-width-modal-body',
                        overlayColor: 'grey',
                        animate: true
                    });
                    $.ajax({
                        type: "POST",
                        url: '<?php echo base_url("group/transaction_alerts/ajax_reconcile_deposit"); ?>',
                        data: form.serialize(),
                        success: function(response) {
                            if(response=='success'){
                                form.find('.submit').show();
                                form.find('.modal_processing_form_button').hide();
                                $('#unreconciled_deposit_row_'+unreconciled_deposit_id+' .unreconciled_deposit_count ').removeClass('unreconciled_deposit_count');
                                $('.modal').modal('hide');
                                toastr['success']('You have successfully reconciled the group deposit.','Deposit successfully reconciled');
                                $('#unreconciled_deposit_row_'+unreconciled_deposit_id).addClass('success').slideDown('slow').delay(3000).fadeOut(3000,function(){
                                    update_notification_counts();
                                });
                                $('#unreconciled_deposit_row_'+unreconciled_deposit_id+' .reconcile_action ').html('<span class="label label-sm label-success"> Reconciled </span>');
                            }else{
                                $('.data_error').each(function(){
                                    $(this).slideDown().html(response);
                                });
                                form.find('.modal_processing_form_button').hide();
                                form.find('.submit').show();
                            }
                            App.unblockUI('.full-width-modal-body');
                        }
                    });
                }else{
                    $('.data_error').each(function(){
                        $(this).slideUp();
                    });
                    form.find('.modal_processing_form_button').hide();
                    form.find('.submit').show();
                }
            });
        }else{
            //show message
            $('#modal_submit_form .data_error').each(function(){
                $(this).html("<p>Kindly review the fields marked in red, enter the values requested before you can proceed.</p>"+error_message).slideDown();
            });
        }
    });

    $('#stackable_submit_form').on('submit',function(e){
        //alert(current_row);
        var form = $(this);
        if(form.find('#add_member_form').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            var first_name = $(this).find('#first_name').val();
            var last_name = $(this).find('#last_name').val();
            var email = $(this).find('#email').val();
            var phone = $(this).find('#phone').val();
            var send_sms_notification = $(this).find('#send_sms_notification').val();
            var send_email_notification = $(this).find('#send_email_notification').val();
            $.post('<?php echo base_url("group/members/ajax_add_member"); ?>',{'first_name':first_name,'last_name':last_name,'email':email,'phone':phone,'send_sms_notification':send_sms_notification,'send_email_notification':send_email_notification,},function(data){
                if(isJson(data)){
                    var member = $.parseJSON(data);
                    $('select.member').each(function(){
                        $(this).append('<option value="' + member.id + '">' + member.first_name +' '+ member.last_name + '</option>').trigger('change');
                    });
                    $('#modal_submit_form select[name="members['+current_row+']"]').val(member.id).trigger('change');
                    form.find('.submit').show();
                    form.find('.modal_processing_form_button').hide();
                    $('#stacked_modal').modal('hide');
                    toastr['success']('You have successfully added a new member to your group, you can now select him/her in the members dropdown.','Member added successfully');
                }else{
                    $('#stackable_submit_form .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                    form.find('.modal_processing_form_button').hide();
                    form.find('.submit').show();
                }
                App.unblockUI('.stacked-modal-body');
            });
        }else if(form.find('#contributions_form_holder').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/contributions/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var contribution = $.parseJSON(response);
                        $('select.contribution').each(function(){
                            $(this).append('<option value="' + contribution.id + '">' + contribution.name + '</option>').trigger('change');
                        });
                        $('#modal_submit_form select[name="contributions['+current_row+']"]').val(contribution.id).trigger('change');
                        form.find('.submit').show();
                        form.find('.modal_processing_form_button').hide();
                        $('#stacked_modal').modal('hide');
                        toastr['success']('You have successfully added a new contribution to your group, you can now select it in the contributions dropdown.','Contribution added successfully');
                    }else{
                        $('#stackable_submit_form .data_error').each(function(){
                            $(this).slideDown().html(response);
                        });
                        $(".stacked-modal-body").animate({ scrollTop: 0 }, 600);;
                    }
                    form.find('.modal_submit_form_button').show();
                    form.find('.modal_processing_form_button').hide(); 
                    App.unblockUI('.stacked-modal-body');
                }
            });
        }else if(form.find('#add_fine_category_form').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/fine_categories/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var fine_category = $.parseJSON(response);
                        $('select.fine_category').each(function(){
                            $(this).append('<option value="fine_category-' + fine_category.id + '">' + fine_category.name + '</option>').trigger('change');
                        });
                        $('#modal_submit_form select[name="fine_categories['+current_row+']"]').val('fine_category-'+fine_category.id).trigger('change');
                        form.find('.submit').show();
                        form.find('.modal_processing_form_button').hide();
                        $('#stacked_modal').modal('hide');
                        toastr['success']('You have successfully added a new fine category to your group, you can now select it in the fine categories dropdown.','Fine category added successfully');
                    }else{
                        $('#stackable_submit_form .data_error').each(function(){
                            $(this).slideDown().html(response);
                        });
                        $(".stacked-modal-body").animate({ scrollTop: 0 }, 600);;
                    }
                    form.find('.modal_submit_form_button').show();
                    form.find('.modal_processing_form_button').hide(); 
                    App.unblockUI('.stacked-modal-body');
                }
            });
        }else if(form.find('#add_depositor_form').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/depositors/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var depositor = $.parseJSON(response);
                        $('select.depositor').each(function(){
                            $(this).append('<option value="' + depositor.id + '">' + depositor.name + '</option>').trigger('change');
                        });
                        $('#modal_submit_form select[name="depositors['+current_row+']"]').val(depositor.id).trigger('change');
                        form.find('.submit').show();
                        form.find('.modal_processing_form_button').hide();
                        $('#stacked_modal').modal('hide');
                        toastr['success']('You have successfully added a new depositor to your group, you can now select it in the depositors dropdown.','Depositor added successfully');
                    }else{
                        $('#stackable_submit_form .data_error').each(function(){
                            $(this).slideDown().html(response);
                        });
                        $(".stacked-modal-body").animate({ scrollTop: 0 }, 600);;
                    }
                    form.find('.modal_submit_form_button').show();
                    form.find('.modal_processing_form_button').hide(); 
                    App.unblockUI('.stacked-modal-body');
                }
            });
        }else if(form.find('#add_income_category_form').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("group/income_categories/ajax_create"); ?>',
                data: form.serialize(),
                success: function(response) {
                    if(isJson(response)){
                        var income_category = $.parseJSON(response);
                        $('select.income_category').each(function(){
                            $(this).append('<option value="' + income_category.id + '">' + income_category.name + '</option>').trigger('change');
                        });
                        $('#modal_submit_form select[name="income_categories['+current_row+']"]').val(income_category.id).trigger('change');
                        form.find('.submit').show();
                        form.find('.modal_processing_form_button').hide();
                        $('#stacked_modal').modal('hide');
                        toastr['success']('You have successfully added a new income category to your group, you can now select it in the income category dropdown.','Income category added successfully');
                    }else{
                        $('#stackable_submit_form .data_error').each(function(){
                            $(this).slideDown().html(response);
                        });
                        $(".stacked-modal-body").animate({ scrollTop: 0 }, 600);;
                    }
                    form.find('.modal_submit_form_button').show();
                    form.find('.modal_processing_form_button').hide(); 
                    App.unblockUI('.stacked-modal-body');
                }
            });
        }else if(form.find('#bank_account_form').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            var account_name = $(this).find('#bank_account_name').val();
            var bank_id = $(this).find('#bank_id').val();
            var bank_branch_id = $(this).find('#bank_branch_id').val();
            var account_number = $(this).find('#bank_account_number').val();
            var initial_balance = $(this).find('#bank_initial_balance').val();
            var id = $(this).find('input[name=id]').val();
            $.post('<?php echo base_url("group/bank_accounts/ajax_create"); ?>',{'account_name':account_name,'bank_id':bank_id,'bank_branch_id':bank_branch_id,'account_number':account_number,'initial_balance':initial_balance,'id':id,},function(data){
                if(isJson(data)){
                    var bank_account = $.parseJSON(data);
                    $('select.from_account_id').each(function(){
                        $(this).append('<option value="bank-' + bank_account.id + '">'+bank_account.bank_details+' - ' + bank_account.account_name + ' ('+bank_account.account_number+')</option>').trigger('change');
                    });
                    $('#modal_submit_form select[name="from_account_ids['+current_row+']"]').val("bank-"+bank_account.id).trigger('change');
                    form.find('.submit').show();
                    form.find('.modal_processing_form_button').hide();
                    $('#stacked_modal').modal('hide');
                    toastr['success']('You have successfully added a new bank account, you can now select it in the accounts dropdown.','Bank account added successfully');
                }else{
                    $('#stackable_submit_form .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                    form.find('.modal_processing_form_button').hide();
                    form.find('.submit').show();
                }
                App.unblockUI('.stacked-modal-body');
            });
        }else if(form.find('#sacco_account_form').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            var account_name = $(this).find('#sacco_account_name').val();
            var sacco_id = $(this).find('#sacco_id').val();
            var sacco_branch_id = $(this).find('#sacco_branch_id').val();
            var account_number = $(this).find('#sacco_account_number').val();
            var initial_balance = $(this).find('#sacco_initial_balance').val();
            var id = $(this).find('input[name=id]').val();
            $.post('<?php echo base_url("group/sacco_accounts/ajax_create"); ?>',{'account_name':account_name,'sacco_id':sacco_id,'sacco_branch_id':sacco_branch_id,'account_number':account_number,'initial_balance':initial_balance,'id':id,},function(data){
                if(isJson(data)){
                    var sacco_account = $.parseJSON(data);
                    $('select.from_account_id').each(function(){
                        $(this).append('<option value="sacco-' + sacco_account.id + '">'+sacco_account.sacco_details+' - ' + sacco_account.account_name + ' ('+sacco_account.account_number+')</option>').trigger('change');
                    });
                    $('#modal_submit_form select[name="from_account_ids['+current_row+']"]').val("sacco-"+sacco_account.id).trigger('change');
                    form.find('.submit').show();
                    form.find('.modal_processing_form_button').hide();
                    $('#stacked_modal').modal('hide');
                    toastr['success']('You have successfully added a new sacco account, you can now select it in the accounts dropdown.','Sacco account added successfully');
                }else{
                    $('#stackable_submit_form .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                    form.find('.modal_processing_form_button').hide();
                    form.find('.submit').show();
                }
                App.unblockUI('.stacked-modal-body');
            });
        }else if(form.find('#mobile_money_account_form').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            var account_name = $(this).find('#mobile_money_account_name').val();
            var mobile_money_provider_id = $(this).find('#mobile_money_provider_id').val();
            var account_number = $(this).find('#mobile_money_account_number').val();
            var initial_balance = $(this).find('#mobile_money_initial_balance').val();
            var id = $(this).find('input[name=id]').val();
            $.post('<?php echo base_url("group/mobile_money_accounts/ajax_create"); ?>',{'account_name':account_name,'mobile_money_provider_id':mobile_money_provider_id,'account_number':account_number,'initial_balance':initial_balance,'id':id,},function(data){
                if(isJson(data)){
                    var mobile_money_account = $.parseJSON(data);
                    $('select.from_account_id').each(function(){
                        $(this).append('<option value="mobile-' + mobile_money_account.id + '">'+mobile_money_account.mobile_money_provider_details+' - ' + mobile_money_account.account_name + ' ('+mobile_money_account.account_number+')</option>').trigger('change');
                    });
                    $('#modal_submit_form select[name="from_account_ids['+current_row+']"]').val("mobile-"+mobile_money_account.id).trigger('change');
                    form.find('.submit').show();
                    form.find('.modal_processing_form_button').hide();
                    $('#stacked_modal').modal('hide');
                    toastr['success']('You have successfully added a new mobile money account, you can now select it in the accounts dropdown.','Mobile money account added successfully');
                }else{
                    $('#stackable_submit_form .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                    form.find('.modal_processing_form_button').hide();
                    form.find('.submit').show();
                }
                App.unblockUI('.stacked-modal-body');
            });
        }else if(form.find('#petty_cash_account_form').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            var account_name = $(this).find('#petty_cash_account_name').val();
            var initial_balance = $(this).find('#petty_cash_initial_balance').val();
            var slug = $(this).find('input[name=slug]').val();
            var id = $(this).find('input[name=id]').val();
            $.post('<?php echo base_url("group/petty_cash_accounts/ajax_create"); ?>',{'account_name':account_name,'account_slug':slug,'account_number':account_number,'initial_balance':initial_balance,'id':id,},function(data){
                if(isJson(data)){
                    var petty_cash_account = $.parseJSON(data);
                    $('select.from_account_id').each(function(){
                        $(this).append('<option value="petty-' + petty_cash_account.id + '">' + petty_cash_account.account_name + '</option>').trigger('change');
                    });
                    $('#modal_submit_form select[name="from_account_ids['+current_row+']"]').val("petty-"+petty_cash_account.id).trigger('change');
                    form.find('.submit').show();
                    form.find('.modal_processing_form_button').hide();
                    $('#stacked_modal').modal('hide');
                    toastr['success']('You have successfully added a new petty cash account, you can now select it in the accounts dropdown.','Petty cash account added successfully');
                }else{
                    $('#stackable_submit_form .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                    form.find('.modal_processing_form_button').hide();
                    form.find('.submit').show();
                }
                App.unblockUI('.stacked-modal-body');
            });
        }else if(form.find('#add_asset_form').is(':visible')){
            App.blockUI({
                target: '.stacked-modal-body',
                overlayColor: 'grey',
                animate: true
            });
            var name = $(this).find('#asset_name').val();
            var asset_category_id = $(this).find('#asset_category_id').val();
            var cost = $(this).find('#asset_cost').val();
            var description = $(this).find('#asset_description').val();
            $.post('<?php echo base_url("group/assets/ajax_create"); ?>',{'name':name,'asset_category_id':asset_category_id,'cost':cost,'description':description,},function(data){
                if(isJson(data)){
                    var asset = $.parseJSON(data);
                    $('select.asset_id').each(function(){
                        $(this).append('<option value="' + asset.id + '">' + asset.name +'</option>').trigger('change');
                    });
                    $('#modal_submit_form select[name="asset_ids['+current_row+']"]').val(asset.id).trigger('change');
                    form.find('.submit').show();
                    form.find('.modal_processing_form_button').hide();
                    $('#stacked_modal').modal('hide');
                    toastr['success']('You have successfully added a new asset to your group, you can now select it in the assets dropdown.','Asset added successfully');
                }else{
                    $('#stackable_submit_form .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                    form.find('.modal_processing_form_button').hide();
                    form.find('.submit').show();
                }
                App.unblockUI('.stacked-modal-body');
            });
        }
        e.preventDefault();
    });

    $(document).on('blur keyup','#modal_submit_form input.amount',function(){
        check_amount($(this));
    });

    $(document).on('blur keyup','#modal_submit_form input.number_of_shares_sold',function(){
        check_number_of_shares_sold($(this));
    });

    $(document).on('blur keyup','#modal_submit_form input.price_per_share',function(){
        check_price_per_share($(this));
    });
    
    $(document).on('blur keyup','#modal_submit_form input.amount_payable',function(){
        check_amount_payable($(this));
    });
    
    $(document).on('change','#modal_submit_form select.contribution',function(){
        check_contribution($(this));
    });
    
    $(document).on('change','#modal_submit_form select.loan',function(){
        check_loan($(this));
    });

    $(document).on('change','#modal_submit_form select.external_loan',function(){
        check_external_loan($(this));
    });

    $(document).on('change','#modal_submit_form select.fine_category',function(){
        check_fine_category($(this));
    });

    $(document).on('change','#modal_submit_form select.income_category',function(){
        check_income_category($(this));
    });

    $(document).on('change','#modal_submit_form select.depositor',function(){
        check_depositor($(this));
    });

    $(document).on('change','#modal_submit_form select.member',function(){
        check_member($(this));
    });

    $(document).on('change','#modal_submit_form select.debtor',function(){
        check_debtor($(this));
    });

    $(document).on('change','#modal_submit_form select.deposit_for',function(){
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

    $(document).on('change','#modal_submit_form select.from_account_id',function(){
        check_from_account_id($(this));
    });

    $(document).on('change','#modal_submit_form select.stock_id',function(){
        check_stock($(this));
    });

    $(document).on('change','#modal_submit_form select.asset_id',function(){
        check_asset($(this));
    });

    $(document).on('change','#modal_submit_form select.money_market_investment_id',function(){
        check_money_market_investment($(this));
    });

    $(document).on('change','#type',function(){
        if($(this).val()==1){
            $('#stackable_submit_form #regular_invoicing_active_holder').slideDown();
            $('#stackable_submit_form #one_time_invoicing_active_holder,#stackable_submit_form #sms_template,#stackable_submit_form #one_time_invoicing_settings,#stackable_submit_form #regular_invoicing_settings,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form  #contribution_member_list_settings').slideUp();
            $('#stackable_submit_form #one_time_invoicing_active').parent().removeClass('checked');
            $('#stackable_submit_form #one_time_invoicing_active').prop('checked',false);             
        }else if($(this).val()==2){
            $('#stackable_submit_form  #one_time_invoicing_active_holder').slideDown();
            $('#stackable_submit_form  #regular_invoicing_active_holder,#stackable_submit_form #sms_template,#stackable_submit_form #one_time_invoicing_settings,#stackable_submit_form #regular_invoicing_settings,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideUp(); 
            $('#stackable_submit_form #regular_invoicing_active').parent().removeClass('checked');              
            $('#stackable_submit_form #regular_invoicing_active').prop('checked',false);                
        }else{
            $('#stackable_submit_form #regular_invoicing_active_holder,#stackable_submit_form #sms_template,#stackable_submit_form #one_time_invoicing_active_holder,#stackable_submit_form #one_time_invoicing_settings,#stackable_submit_form #regular_invoicing_settings,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideUp();
            $('#stackable_submit_form #regular_invoicing_active,#stackable_submit_form #one_time_invoicing_active').parent().removeClass('checked'); 
            $('#stackable_submit_form #regular_invoicing_active,#stackable_submit_form #one_time_invoicing_active').prop('checked',false);  
        }
    });
            
    $(document).on('change','#regular_invoicing_active',function(){
        if($(this).prop('checked')){
            $('#stackable_submit_form #regular_invoicing_settings,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideDown();
        }else{
            $('#stackable_submit_form #regular_invoicing_settings,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideUp();
        }
    });

    $(document).on('change','#one_time_invoicing_active',function(){
        if($(this).prop('checked')){
            $('#stackable_submit_form #one_time_invoicing_settings,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideDown();
        }else{
            $('#stackable_submit_form #one_time_invoicing_settings,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideUp();
        }
    });

    $(document).on('change','#month_day_monthly',function(){
        if($(this).val()>4){
            $('#stackable_submit_form #week_day_monthly').val(0).attr('disabled','disabled');
        }else{
            $('#stackable_submit_form #week_day_monthly').removeAttr('disabled','disabled');
        }
    });

    $(document).on('change','#month_day_multiple',function(){
        if($(this).val()>4){
            $('#stackable_submit_form #week_day_multiple').val(0).attr('disabled','disabled');
        }else{
            $('#stackable_submit_form #week_day_multiple').removeAttr('disabled','disabled');
        }
    });

    $(document).on('change','#contribution_frequency',function(){
        if($(this).val()==1){
            //once a month
            $('#stackable_submit_form #once_a_month').slideDown();
            $('#stackable_submit_form #invoice_days,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideDown();
            $('#stackable_submit_form #once_a_week,#stackable_submit_form #once_every_two_weeks,#stackable_submit_form #once_every_multiple_months').slideUp();
        }else if($(this).val()==6){
            //once a week
            $('#stackable_submit_form #invoice_days,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideDown();
            $('#stackable_submit_form #once_a_week').slideDown();
            $('#stackable_submit_form #once_every_two_weeks,#stackable_submit_form #once_a_month,#stackable_submit_form #once_every_multiple_months').slideUp();
        }else if($(this).val()==7){
            //once every two weeks
            $('#stackable_submit_form #invoice_days,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideDown();
            $('#stackable_submit_form #once_every_two_weeks').slideDown();
            $('#stackable_submit_form #once_every_multiple_months,#stackable_submit_form #once_a_week,#stackable_submit_form #once_a_month').slideUp();
        }else if($(this).val()==2||$(this).val()==3||$(this).val()==4||$(this).val()==5){
            //once every two months, once every three months,once every six months, once a year
            $('#stackable_submit_form #invoice_days,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideDown();
            $('#stackable_submit_form #once_every_multiple_months').slideDown();
            $('#stackable_submit_form #once_a_month,#stackable_submit_form #once_every_two_weeks,#stackable_submit_form #once_a_week').slideUp();
        }else if($(this).val()==8){
            //$('#invoice_days,#invoice_notifications,#fines,#advanced_settings,#contribution_member_list_settings').slideUp();
            //hide all
            $('#stackable_submit_form select[name=invoice_days]').val(1).trigger('change');
            $('#stackable_submit_form #invoice_days').slideDown();
            $('#stackable_submit_form #once_a_month,#stackable_submit_form #once_every_two_weeks,#stackable_submit_form #once_a_week,#stackable_submit_form #once_every_multiple_months').slideUp();
        }else{
            $('#stackable_submit_form #invoice_days,#stackable_submit_form #invoice_notifications,#stackable_submit_form #fines,#stackable_submit_form #advanced_settings,#stackable_submit_form #contribution_member_list_settings').slideUp();
            //hide all
            $('#stackable_submit_form #once_a_month,#stackable_submit_form #once_every_two_weeks,#stackable_submit_form #once_a_week,#stackable_submit_form #once_every_multiple_months').slideUp();
        }
    });

    $(document).on('change','#sms_notifications_enabled',function(){
        if($(this).prop('checked')){
            $('#stackable_submit_form #sms_template').slideDown();
        }else{
            $('#stackable_submit_form #sms_template').slideUp();
        }
    });

    $(document).on('change','#enable_contribution_summary_display_configuration',function(){
        if($(this).prop('checked')){
            $('#stackable_submit_form #contribution_summary_display_configuration_settings').slideDown();
        }else{
            $('#stackable_submit_form #contribution_summary_display_configuration_settings').slideUp();
        }
    });

    $(document).on('change','#enable_contribution_member_list',function(){
        if($(this).prop('checked')){
            $('#stackable_submit_form #contribution_member_list').slideDown();
        }else{
            $('#stackable_submit_form #contribution_member_list').slideUp();
        }
    });

    $(document).on('change','#enable_fines',function(){
        if($(this).prop('checked')){
            $('#stackable_submit_form #fine_settings').slideDown();
        }else{
            $('#stackable_submit_form #fine_settings').slideUp();
        }
    });

    $(document).on('change','.fine_types',function(){
        var fine_setting_row_element = $(this).parent().parent().parent().parent();
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
        var fine_setting_row_element = $(this).parent().parent().parent().parent();
        if($(this).val()==1){
            fine_setting_row_element.find('.fine_limit').slideDown();
        }else{
            fine_setting_row_element.find('.fine_limit').slideUp();
        }
    });

    $(document).on('change','.percentage_fine_modes',function(){ 
        var fine_setting_row_element = $(this).parent().parent().parent().parent();
        if($(this).val()==1){
            fine_setting_row_element.find('.fine_limit').slideDown();
        }else{
            fine_setting_row_element.find('.fine_limit').slideUp();
        }
    });

    $(document).on('click','#add_contribution',function(){
        $("#full-width-modal .contribution").select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#contributions_form" data-title="Add Contribution" data-id="add_contribution" id="add_contribution" href="#">Add Contribution</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).trigger("select2:close");
    });

    $(document).on('click','#add_member',function(){
        $("#full-width-modal .member").select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_member" data-title="Add Member" data-id="add_member" id="add_member" href="#">Add Member</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).trigger("select2:close");
    });

    $(document).on('click','#add_fine_category_link',function(){
        $("#full-width-modal .fine_category").select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_fine_category" data-title="Add Fine Category" data-id="add_fine_category" id="add_fine_category_link" href="#">Add Fine Category</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).trigger("select2:close");
    });

    $(document).on('click','#add_depositor_link',function(){
        $("#full-width-modal .depositor").select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_depositor" data-title="Add Depositor" data-id="add_depositor" id="add_depositor_link" href="#">Add Depositor</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).trigger("select2:close");
    });

    $(document).on('click','#add_income_category_link',function(){
        $("#full-width-modal .income_category").select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_income_category" data-title="Add Income Category" data-id="add_income_category" id="add_income_category_link" href="#">Add Income Category</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).trigger("select2:close");
    });

    /*$(document).on('click','#add_income_category_link',function(){
        $("#full-width-modal .income_category").select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="stacked_inline pop_up" data-row="" data-toggle="modal" data-content="#add_income_category" data-title="Add Income Category" data-id="add_income_category" id="add_income_category_link" href="#">Add Income Category</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).trigger("select2:close");
    });*/

    $(document).on('click','#add_account_link',function(){
        $("#full-width-modal .from_account_id").select2({
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
        }).trigger("select2:close");
        $('#stackable_submit_form .modal-footer').hide();
    });

    $(document).on('click','#add_asset_link',function(){
        $("#full-width-modal .asset_id").select2({
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
        }).trigger("select2:close");
    });

    $(document).on('click','#add_loan',function(){
        $(".table-multiple-items .loan").select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="stacked_inline hidden" data-row="" data-toggle="modal" data-content="#loans_form" data-title="Add Loan" data-id="add_loan" id="add_loan" href="#">Add Loan</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        }).trigger("select2:close");
        var member_id = $('.table-multiple-items select[name="members['+current_row+']"]').val();
        //var disbursement_date = $('#modal_submit_form input[name="transaction_date"]').val();
        //var account_id = $('#modal_submit_form input[name="account_id"]').val();
        //alert(account_id);
        //$(document).find('#submit_form select[name="member_id"]').val(member_id).trigger('change').prop('readonly',true);
        //alert($(document).find('#submit_form select[name="member_id"]').html());
        $('#stacked_modal').on('shown.bs.modal', function(){
            //alert("pipe water");
            $(document).find('#stackable_submit_form .date-picker').datepicker({autoclose:true});
            $(document).find('#stackable_submit_form select[name="member_id"]').val(member_id).trigger('change').prop('disabled',true);
            $(document).find('#stackable_submit_form input[name="member_id"]').val(member_id);
            //$(document).find('#stackable_submit_form input[name="disbursement_date"]').val(disbursement_date);
            //$(document).find('#stackable_submit_form select[name="account_id"]').val(account_id).trigger('change').prop('disabled',true);
            //$(document).find('#stackable_submit_form input[name="account_id"]').val(account_id);
        });
    });


    $(document).on('click','#stackable_submit_form #add-new-line',function(){
        var html = $('#append_fine_setting_row').html();
        html = html.replace_all('checker','');
        $('#stackable_submit_form #append-place-holder').append(html);
        $('#stackable_submit_form #fine_settings .append_select2').select2();
        $('input[type=checkbox]').uniform();
        $('.tooltips').tooltip();
        FormInputMask.init();
        var number = 0;
        update_fine_setting_counts();
    });
    
    $(document).on('click','#stackable_submit_form .remove-line',function(){ 
        $(this).parent().parent().parent().remove();
        update_fine_setting_counts();
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

    $(document).on('click','#create_mobile_money_account',function(){
        var element = $(this).parent().parent().parent().parent().parent().parent();
        element.find('#mobile_money_account_form').slideDown();
        element.find('.data_error,#account_options,#bank_account_form,#sacco_account_form,#petty_cash_account_form').slideUp();
        $('#stackable_submit_form .modal-footer').show();
        return false;
    });

    $(document).on('click','#create_petty_cash_account',function(){
        var element = $(this).parent().parent().parent().parent().parent().parent();
        element.find('#petty_cash_account_form').slideDown();
        element.find('.data_error,#account_options,#bank_account_form,#sacco_account_form,#mobile_money_account_form').slideUp();
        $('#stackable_submit_form .modal-footer').show();
        return false;
    });

    $(document).on('click','.back_to_account_options',function(){
        var element = $(this).parent().parent().parent().parent().parent().parent();
        element.find('#account_options').slideDown();
        element.find('.data_error,#petty_cash_account_form,#bank_account_form,#sacco_account_form,#mobile_money_account_form').slideUp();
        $('#stackable_submit_form .modal-footer').hide();
        return false;
    });

    $(document).on('click','#create_bank_account',function(){
        var element = $(this).parent().parent().parent().parent().parent().parent();
        element.find('#bank_account_form').slideDown();
        element.find('.data_error,#account_options,#sacco_account_form,#mobile_money_account_form,#petty_cash_account_form').slideUp();
        element.find('#bank_account_form .append_select2').each(function(){
            //$(this).select2();
        });
        $('#stackable_submit_form .modal-footer').show();
        return false;
    });

    $(document).on('click','#create_sacco_account',function(){
        var element = $(this).parent().parent().parent().parent().parent().parent();
        element.find('#sacco_account_form').slideDown();
        element.find('.data_error,#account_options,#bank_account_form,#mobile_money_account_form,#petty_cash_account_form').slideUp();
        $('#stackable_submit_form .modal-footer').show();
        return false;
    });

    var empty_branch_list = $('.bank_branches_space').find('select').html();
    var branch_id = '';
    $(document).on('change','select[name="bank_id"]',function(){
        var element = $(this);
        var bank_id = $(this).val();
        element.parent().parent().parent().parent().find('#bank_branch_input_group').slideUp();
        App.blockUI({
            target: '.stacked-modal-body',
            overlayColor: 'grey',
            animate: true
        });
        if(bank_id){
            $.post('<?php echo site_url('group/bank_accounts/ajax_get_bank_branches');?>',{'bank_id':bank_id,'branch_id':branch_id},
            function(data){
                element.parent().parent().parent().parent().find('.bank_branches_space').html(data);
                element.parent().parent().parent().parent().find('#bank_branch_id').select2({width:'100%'});
                element.parent().parent().parent().parent().find('#bank_branch_input_group').slideDown();
                App.unblockUI('.stacked-modal-body');
            });
        }else{
            $('.bank_branches_space').html('<select name="bank_id" class="form-control modal_select2" id="bank_branch_id">'+empty_branch_list+'</select>');
            //$('#bank_branch_id').select2();
        }
    });

    var empty_branch_list =$('.sacco_branches_space').find('select').html();
    var branch_id = '';
    $(document).on('change','select[name="sacco_id"]',function(){
        var element = $(this);
        var sacco_id = $(this).val();
        element.parent().parent().parent().parent().find('#sacco_branch_input_group').slideUp();
        App.blockUI({
            target: '.stacked-modal-body',
            overlayColor: 'grey',
            animate: true
        });
        if(sacco_id){
            $.post('<?php echo site_url('group/sacco_accounts/ajax_get_sacco_branches');?>',{'sacco_id':sacco_id,'branch_id':''},
            function(data){
                element.parent().parent().parent().parent().find('.sacco_branches_space').html(data);
                element.parent().parent().parent().parent().find('#sacco_branch_id').select2({width:'100%'});
                element.parent().parent().parent().parent().find('#sacco_branch_input_group').slideDown();
                App.unblockUI('.stacked-modal-body');
                //$('#sacco_branch_id').select2();
            });
        }else{
            $('.sacco_branches_space').html('<select name="sacco_id" class="form-control select2" id="sacco_branch_id">'+empty_branch_list+'</select>');
            //$('#sacco_branch_id').select2();
        }
    });

    if(typeof(EventSource) !== "undefined") {
        var timestamp = Math.floor(Date.now() / 1000);
        var source = new EventSource("<?php echo site_url('group/transaction_alerts/check_new_unreconciled_deposits/"+timestamp+"'); ?>");
        source.onmessage = function(event) {
            $('#unreconciled_deposits_loading_row').slideDown('slow');
            App.blockUI({
                target: '#unreconciled_deposits_loading_row td',
                overlayColor: 'grey',
                animate: true
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
                    //$('#unreconciled_deposits_listing').html(response);
                    App.unblockUI('#unreconciled_deposits_loading_row td');
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
        App.blockUI({
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
        type: "GET",
        url: '<?php echo base_url("group/transaction_alerts/ajax_get_admin_unreconciled_deposits_listing"); ?>',
        dataType : "html",
            success: function(response) {
                $('#unreconciled_deposits_listing').html(response);
                App.unblockUI('#unreconciled_deposits_listing');
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
        });
}

function check_deposit_for(deposit_for_select){
    if(deposit_for_select.val()==''){
        deposit_for_select.parent().parent().addClass('has-error');
        deposit_for_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        deposit_for_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a deposit for');
        $('.tooltips').tooltip();
        return false;
    }else{
        deposit_for_select.parent().parent().removeClass('has-error');
        deposit_for_select.parent().find('i').remove();
        deposit_for_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_member(member_select){
    if(member_select.val()==''){
        member_select.parent().parent().addClass('has-error');
        member_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        member_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a member');
        $('.tooltips').tooltip();
        return false;
    }else{
        member_select.parent().parent().removeClass('has-error');
        member_select.parent().find('i').remove();
        member_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}
function check_debtor(debtor_select){
    if(debtor_select.val()==''){
        debtor_select.parent().parent().addClass('has-error');
        debtor_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        debtor_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a borrower');
        $('.tooltips').tooltip();
        return false;
    }else{
        debtor_select.parent().parent().removeClass('has-error');
        debtor_select.parent().find('i').remove();
        debtor_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_contribution(contribution_select){
    if(contribution_select.val()==''){
        contribution_select.parent().parent().addClass('has-error');
        contribution_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        contribution_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a contribution');
        $('.tooltips').tooltip();
        return false;
    }else{
        contribution_select.parent().parent().removeClass('has-error');
        contribution_select.parent().find('i').remove();
        contribution_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_fine_category(fine_category_select){
    if(fine_category_select.val()==''){
        fine_category_select.parent().parent().addClass('has-error');
        fine_category_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        fine_category_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a fine category');
        $('.tooltips').tooltip();
        return false;
    }else{
        fine_category_select.parent().parent().removeClass('has-error');
        fine_category_select.parent().find('i').remove();
        fine_category_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_amount(amount_input){
    if(amount_input.val()==''){
        amount_input.parent().parent().addClass('has-error');
        amount_input.parent().prepend('<i class="fa fa-exclamation "></i>');
        amount_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter an amount');
        $('.tooltips').tooltip();
        return false;
    }else{
        var amount = amount_input.val();
        regex = /^[0-9.,\b]+$/;;
        if(regex.test(amount)){
            amount_input.parent().parent().removeClass('has-error');
            amount_input.parent().find('i').remove();
            amount_input.parent().parent().find('.tooltips').attr('data-original-title','');
            $('.tooltips').tooltip();
            return true;
        }else{ 
            amount_input.parent().parent().addClass('has-error');
            amount_input.parent().prepend('<i class="fa fa-exclamation "></i>');
            amount_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter a valid amount');
            $('.tooltips').tooltip();
            return false;
        }
    }
}

function check_miscellaneous_deposit_description(miscellaneous_deposit_description_textarea){
    if(miscellaneous_deposit_description_textarea.val()==''){
        miscellaneous_deposit_description_textarea.parent().parent().addClass('has-error');
        miscellaneous_deposit_description_textarea.parent().prepend('<i class=""></i>');
        miscellaneous_deposit_description_textarea.parent().parent().find('.tooltips').attr('data-original-title','Please enter a description');
        $('.tooltips').tooltip();
        return false;
    }else{
        miscellaneous_deposit_description_textarea.parent().parent().removeClass('has-error');
        miscellaneous_deposit_description_textarea.parent().find('i').remove();
        miscellaneous_deposit_description_textarea.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_depositor(depositor_select){
    if(depositor_select.val()==''){
        depositor_select.parent().parent().addClass('has-error');
        depositor_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        depositor_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a depositor');
        $('.tooltips').tooltip();
        return false;
    }else{
        depositor_select.parent().parent().removeClass('has-error');
        depositor_select.parent().find('i').remove();
        depositor_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_income_category(income_category_select){
    if(income_category_select.val()==''){
        income_category_select.parent().parent().addClass('has-error');
        income_category_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        income_category_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a income category');
        $('.tooltips').tooltip();
        return false;
    }else{
        income_category_select.parent().parent().removeClass('has-error');
        income_category_select.parent().find('i').remove();
        income_category_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_amount_payable(amount_payable_input){
    if(amount_payable_input.val()==''){
        amount_payable_input.parent().parent().addClass('has-error');
        amount_payable_input.parent().prepend('<i class="fa fa-exclamation "></i>');
        amount_payable_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter an amount payable');
        $('.tooltips').tooltip();
        return false;
    }else{
        var amount = amount_payable_input.val();
        regex = /^[0-9.,\b]+$/;;
        if(regex.test(amount)){
            amount_payable_input.parent().parent().removeClass('has-error');
            amount_payable_input.parent().find('i').remove();
            amount_payable_input.parent().parent().find('.tooltips').attr('data-original-title','');
            $('.tooltips').tooltip();
            return true;
        }else{ 
            amount_payable_input.parent().parent().addClass('has-error');
            amount_payable_input.parent().prepend('<i class="fa fa-exclamation "></i>');
            amount_payable_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter a valid amount');
            $('.tooltips').tooltip();
            return false;
        }
    }
}

function check_bank_loan_disbursement_description(bank_loan_disbursement_description_textarea){
    if(bank_loan_disbursement_description_textarea.val()==''){
        bank_loan_disbursement_description_textarea.parent().parent().addClass('has-error');
        bank_loan_disbursement_description_textarea.parent().prepend('<i class=""></i>');
        bank_loan_disbursement_description_textarea.parent().parent().find('.tooltips').attr('data-original-title','Please enter a description');
        $('.tooltips').tooltip();
        return false;
    }else{
        bank_loan_disbursement_description_textarea.parent().parent().removeClass('has-error');
        bank_loan_disbursement_description_textarea.parent().find('i').remove();
        bank_loan_disbursement_description_textarea.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_loan(loan_select){
    if(loan_select.val()==''){
        loan_select.parent().parent().addClass('has-error');
        loan_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        loan_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a loan');
        $('.tooltips').tooltip();
        return false;
    }else{
        loan_select.parent().parent().removeClass('has-error');
        loan_select.parent().find('i').remove();
        loan_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_external_loan(external_loan_select){
    if(external_loan_select.val()==''){
        external_loan_select.parent().parent().addClass('has-error');
        external_loan_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        external_loan_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a loan');
        $('.tooltips').tooltip();
        return false;
    }else{
        external_loan_select.parent().parent().removeClass('has-error');
        external_loan_select.parent().find('i').remove();
        external_loan_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_from_account_id(from_account_id_select){
    if(from_account_id_select.val()==''){
        from_account_id_select.parent().parent().addClass('has-error');
        from_account_id_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        from_account_id_select.parent().parent().find('.tooltips').attr('data-original-title','Please select an account');
        $('.tooltips').tooltip();
        return false;
    }else{
        from_account_id_select.parent().parent().removeClass('has-error');
        from_account_id_select.parent().find('i').remove();
        from_account_id_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_stock(stock_select){
    if(stock_select.val()==''){
        stock_select.parent().parent().addClass('has-error');
        stock_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        stock_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a stock');
        $('.tooltips').tooltip();
        return false;
    }else{
        stock_select.parent().parent().removeClass('has-error');
        stock_select.parent().find('i').remove();
        stock_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_number_of_shares_sold(number_of_shares_sold_input){
    if(number_of_shares_sold_input.val()==''){
        number_of_shares_sold_input.parent().parent().addClass('has-error');
        number_of_shares_sold_input.parent().prepend('<i class="fa fa-exclamation "></i>');
        number_of_shares_sold_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter the number of shares sold');  
        $('.tooltips').tooltip();
        return false;
    }else{
        var amount = number_of_shares_sold_input.val();
        regex = /^[0-9\b]+$/;
        if(regex.test(amount)){
            number_of_shares_sold_input.parent().parent().removeClass('has-error');
            number_of_shares_sold_input.parent().find('i').remove();
            number_of_shares_sold_input.parent().parent().find('.tooltips').attr('data-original-title','');
            $('.tooltips').tooltip();
            return true;
        }else{ 
            number_of_shares_sold_input.parent().parent().addClass('has-error');
            number_of_shares_sold_input.parent().prepend('<i class="fa fa-exclamation "></i>');
            number_of_shares_sold_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter a valid number of shares sold, only numbers are allowed'); 
            $('.tooltips').tooltip();
            return false;
        }
    }
}

function check_price_per_share(price_per_share_input){
    if(price_per_share_input.val()==''){
        price_per_share_input.parent().parent().addClass('has-error');
        price_per_share_input.parent().prepend('<i class="fa fa-exclamation "></i>');
        price_per_share_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter a price per share');
        $('.tooltips').tooltip();
        return false;
    }else{
        var amount = price_per_share_input.val();
        regex = /^[0-9.,\b]+$/;
        if(regex.test(amount)){
            price_per_share_input.parent().parent().removeClass('has-error');
            price_per_share_input.parent().find('i').remove();
            price_per_share_input.parent().parent().find('.tooltips').attr('data-original-title','');
            $('.tooltips').tooltip();
            return true;
        }else{ 
            price_per_share_input.parent().parent().addClass('has-error');
            price_per_share_input.parent().prepend('<i class="fa fa-exclamation "></i>');
            price_per_share_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter a valid amount');
            $('.tooltips').tooltip();
            return false;
        }
    }
}

function check_asset(asset_select){
    if(asset_select.val()==''){
        asset_select.parent().parent().addClass('has-error');
        asset_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        asset_select.parent().parent().find('.tooltips').attr('data-original-title','Please select an asset');
        $('.tooltips').tooltip();
        return false;
    }else{
        asset_select.parent().parent().removeClass('has-error');
        asset_select.parent().find('i').remove();
        asset_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
        return true;
    }
}

function check_money_market_investment(money_market_investment_select){
    if(money_market_investment_select.val()==''){
        money_market_investment_select.parent().parent().addClass('has-error');
        money_market_investment_select.parent().prepend('<i class="fa fa-exclamation "></i>');
        money_market_investment_select.parent().parent().find('.tooltips').attr('data-original-title','Please select a money market investment');
        $('.tooltips').tooltip();
        return false;
    }else{
        money_market_investment_select.parent().parent().removeClass('has-error');
        money_market_investment_select.parent().find('i').remove();
        money_market_investment_select.parent().parent().find('.tooltips').attr('data-original-title','');
        $('.tooltips').tooltip();
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

</script>


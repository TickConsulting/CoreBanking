<div  id="">
    <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="record_transfer"'); ?>
        <div class="form-group m-form__group row pt-0 m--padding-10">
            
            <div class="col-md-6 m-form__group-sub ">
                <label class="form-control-label"><?php echo translate('Transfer Date');?>?: <span class="required">*</span></label>
                <div class="input-group ">
                    <?php echo form_input('transfer_date',$this->input->post('transfer_date')?timestamp_to_datepicker(strtotime($this->input->post('transfer_date'))):timestamp_to_datepicker(time()),'class="form-control m-input datepicker" data-date-end-date="0d" data-date-start-date="-20y" readonly');?>
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar-check-o"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-sm-12 m-form__group-sub">
                <label class="form-control-label"><?php echo translate('Account To Transfer From');?> (<?php echo $this->group_currency;?>): <span class="required">*</span></label>
                <?php echo form_dropdown('from_account_id',array(''=>'--'.translate('Select account').'--')+translate($account_options),$this->input->post('from_account_id')?$this->input->post('from_account_id'):$post->from_account_id,'class="form-control m-select2 account"');?>
            </div>
            
            <div class="col-lg-6 col-sm-12 m-form__group-sub pt-3">
                <label class="form-control-label"><?php echo translate('Account To Transfer To');?> (<?php echo $this->group_currency;?>): <span class="required">*</span></label>
                <?php echo form_dropdown('to_account_id',array(''=>'--'.translate('Select account').'--')+translate($account_options),$this->input->post('from_account_id')?$this->input->post('to_account_id'):$post->to_account_id,'class="form-control m-select2 account"');?>
            </div>

            <div class="col-lg-6 col-sm-12 m-form__group-sub pt-3">
                <label><?php echo translate(' Amount');?><span class="required">*</span></label>                
                <?php echo form_input('amount',$this->input->post('amount')?:$post->amount?:'','  class="form-control currency"  '); ?>
            </div>

            <div class="col-lg-12 col-sm-12 m-form__group-sub pt-3">
                <label><?php echo translate(' Description');?></label>                
                <?php echo form_textarea('description',$this->input->post('description')?:$post->description,'class="form-control description" placeholder=""');?>
            </div>

        </div>
        <div class="form-group m-form__group row pt-0 m--padding-10">
            <div class="col-lg-12 col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="record_transfer_btn" type="button">
                        <?php echo translate('Save Changes & Submit');?>
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_member_loan_button">
                        <?php echo translate('Cancel');?>
                    </button> 
                </span>
            </div>
        </div>
    <?php echo form_close() ?>       
</div>

<a class="inline d-none" data-toggle="modal" data-target="#create_new_account_pop_up" data-title="Create New Account" data-id="create_account" id="add_new_account"><?php echo translate('Add Account');?></a>

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
                        <div class="alert alert-danger data_error" id="create_bank_account_alert" style="display:none;">
                        </div>
                        <div class="m-form__section m-form__section--first">
                            <div class="form-group m-form__group">
                                <label for="example_input_full_name">
                                    <?php echo translate('Account Name');?>
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2">
                                           <i class="la la-bank"></i>
                                        </span>
                                    </div>
                                    <?php echo form_input('account_name','','id="bank_account_name" class="form-control" placeholder="Account Name"'); ?>
                                </div>
                                <span class="m-form__help">
                                    <?php echo translate('Enter your account name as registered');?>
                                </span>
                            </div>
                            <div class="form-group m-form__group">
                                <label>
                                    <?php echo translate('Bank Name');?>
                                    <span class="required">*</span>
                                </label>
                                <?php echo form_dropdown('bank_id',array(''=>'--Select Bank--')+$banks,'','id="bank_id" class="form-control select2-append"  ') ?>
                                <span class="m-form__help">
                                    <?php echo translate('Select the bank your account is registered to');?>
                                </span>
                            </div>
                            <div class="form-group m-form__group bank_branch_id" style="display: none;">
                                <label>
                                    <?php echo translate('Bank Branch');?>
                                    <span class="required">*</span>
                                </label>
                                <?php echo form_dropdown('bank_branch_id',array(''=>'--Select Bank Name First--'),'','class="form-control select2-append" id = "bank_branch_id"  ') ?>
                                <span class="m-form__help">
                                    <?php echo translate('Select the bank branch your account is registered to');?>
                                </span>
                            </div>
                            <div class="form-group m-form__group bank_account_number" style="display: none;">
                                <label>
                                    <?php echo translate('Account Number');?>
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2">
                                           <i class="la la-slack"></i>
                                        </span>
                                    </div>
                                     <?php echo form_input('account_number','',' id="bank_account_number" class="form-control" placeholder="Account Number"'); ?>
                                </div>
                                <span class="m-form__help">
                                    <?php echo translate('Enter your account number as registered');?>
                                </span>
                            </div>
                        </div>
                        <div class="m-form__actions m-form__actions m--align-right">
                            <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                <?php echo translate('Cancel');?>
                            </button>
                            <button class="btn btn-primary" id="create_bank_account">
                                <?php echo translate('Submit');?>
                            </button>
                        </div>
                    </div>

                    <div class="tab-pane" id="sacco_account_tab" role="tabpanel">
                        <div class="alert alert-danger data_error" id="create_petty_cash_account_alert" style="display:none;">
                        </div>
                        <div class="m-form__section m-form__section--first">
                            <div class="form-group m-form__group">
                                <label for="example_input_full_name">
                                    <?php echo translate('Account Name');?>
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2">
                                           <i class="la la-bank"></i>
                                        </span>
                                    </div>
                                    <?php echo form_input('account_name','','class="form-control" placeholder="Account Name" id="sacco_account_name" '); ?>
                                </div>
                                <span class="m-form__help">
                                    <?php echo translate('Enter your account name as registered');?>
                                </span>
                            </div>
                            <div class="form-group m-form__group">
                                <label>
                                    <?php echo translate('Group Name');?>
                                    <span class="required">*</span>
                                </label>
                                <?php echo form_dropdown('sacco_id',array(''=>'--Select Sacco--')+$saccos,'','class="form-control select2-append" id="sacco_id"  ') ?>
                                <span class="m-form__help">
                                    <?php echo translate('Select the Group your account is registered to');?>
                                </span>
                            </div>
                            <div class="form-group m-form__group sacco_branch_id" style="display: none;">
                                <label>
                                    <?php echo translate('Group Branch');?>
                                    <span class="required">*</span>
                                </label>
                                <?php echo form_dropdown('sacco_branch_id',array(''=>'--No branch records found--'),'','class="form-control select2-append" id = "sacco_branch_id"  ') ?>
                                <span class="m-form__help">
                                    <?php echo translate('Select the Group your account is registered to');?>
                                </span>
                            </div>

                            <div class="form-group m-form__group sacco_account_number" style="display: none;">
                                <label>
                                    <?php echo translate('Account Number');?>
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2">
                                           <i class="la la-slack"></i>
                                        </span>
                                    </div>
                                    <?php echo form_input('account_number','','class="form-control" placeholder="Account Number" id="sacco_account_number"'); ?>
                                </div>
                                <span class="m-form__help">
                                    <?php echo translate('Enter your account number as registered');?>
                                </span>
                            </div>
                        </div>
                        <div class="m-form__actions m-form__actions m--align-right">
                            <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                <?php echo translate('Cancel');?>
                            </button>
                            <button class="btn btn-primary" id="create_sacco_account">
                                <?php echo translate('Submit');?>
                            </button>
                        </div>
                    </div>

                    <div class="tab-pane" id="mobile_money_account_tab" role="tabpanel">
                        <div class="alert alert-danger data_error" id="create_mobile_money_account_alert" style="display:none;">
                        </div>
                        <div class="m-form__section m-form__section--first">
                            <div class="form-group m-form__group">
                                <label for="example_input_full_name">
                                    <?php echo translate('Mobile Money Account Name');?>
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2">
                                           <i class="la la-bank"></i>
                                        </span>
                                    </div>
                                    <?php echo form_input('account_name','','class="form-control" placeholder="Mobile Money Account Name" id="mobile_money_account_name" '); ?>
                                </div>
                                <span class="m-form__help">
                                    <?php echo translate('Enter your account name');?>
                                </span>
                            </div>
                            <div class="form-group m-form__group">
                                <label>
                                    <?php echo translate('Mobile Money Provider Name');?>
                                    <span class="required">*</span>
                                </label>
                                 <?php echo form_dropdown('mobile_money_provider_id',array(''=>'--Select Mobile Money Provider--')+$mobile_money_providers,'','class="form-control  select2-append" id="mobile_money_provider_id"  ') ?>
                                <span class="m-form__help">
                                    <?php echo translate('Select the mobile money provider your account is registered to');?>
                                </span>
                            </div>

                            <div class="form-group m-form__group mobile_money_account_number" style="display: none;">
                                <label>
                                    <?php echo translate('Account Number');?>/
                                    <?php echo translate('Till Number');?>/
                                    <?php echo translate('Phone Number');?>
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2">
                                           <i class="la la-slack"></i>
                                        </span>
                                    </div>
                                    <?php echo form_input('account_number','','class="form-control" placeholder="Account Number / Phone Number / Till Number" id="mobile_money_account_number"'); ?>
                                </div>
                                <span class="m-form__help">
                                    <?php echo translate('Enter your account number as registered');?>
                                </span>
                            </div>
                        </div>
                        <div class="m-form__actions m-form__actions m--align-right">
                            <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                <?php echo translate('Cancel');?>
                            </button>
                            <button id="create_mobile_money_account" class="btn btn-primary">
                                <?php echo translate('Submit');?>
                            </button>
                        </div>
                    </div>

                    <div class="tab-pane" id="petty_cash_account_tab" role="tabpanel">
                        <div class="alert alert-danger data_error" id="create_petty_cash_account_alert"  style="display:none;">
                        </div>
                        <div class="m-form__section m-form__section--first">
                            <div class="form-group m-form__group">
                                <label for="example_input_full_name">
                                    <?php echo translate('Petty Cash Account Name');?>
                                    <span class="required">*</span>                                            
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2">
                                           <i class="la la-bank"></i>
                                        </span>
                                    </div>
                                    <?php echo form_input('account_name','','class="form-control slug_parent" placeholder="Petty Cash Account Name " id="petty_cash_account_name"'); ?>
                                    <?php echo form_hidden('id','');?>       
                                    <?php echo form_hidden('slug','','class="form-control slug"'); ?>     
                                </div>
                                <span class="m-form__help">
                                    <?php echo translate('Enter your account name');?>
                                </span>
                            </div>
                        </div>
                        <div class="m-form__actions m-form__actions m--align-right">
                            <button type="reset" class="btn btn-secondary" data-dismiss="modal">
                                <?php echo translate('Cancel');?>
                            </button>
                            <button id="create_petty_cash_account" class="btn btn-primary">
                                <?php echo translate('Submit');?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        SnippetRecordAccountTransfer.init();
        $(document).on('change','select.account',function(){
            if($(this).val()==''){
                $(this).parent().addClass('has-danger');
            }else{
                if($(this).val()=='0'){
                    $('#add_new_account').trigger('click');
                    $(this).val("").trigger('change');
                    $('#create_new_account_pop_up .select2-append').select2({
                        width:'100%',
                        escapeMarkup: function (markup) {
                            return markup;
                        }
                    });
                }
                $(this).parent().removeClass('has-danger');
            }
        });

        $(document).on('click','#add_new_account',function(){
            $(".account").select2({
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

        //add account modal close eventt
        $('#create_new_account_pop_up').on('hidden.bs.modal', function () {
            $(':input','#create_new_account_pop_up')
                .val('')
                .prop('checked',false)
                .removeAttr('selected')
                .trigger('change');
            $('#bank_account_tab #bank_branch_id,#bank_account_tab .bank_account_number,#sacco_account_tab #sacco_branch_id,#sacco_account_tab .sacco_account_number,#mobile_money_account_tab .mobile_money_account_number,#create_new_account_pop_up .data_error').slideUp();
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

        $(document).on('click','#create_bank_account',function(){
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            $('#create_bank_account').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            var account_name = $('#bank_account_name').val();
            var bank_id = $('#bank_id').val();
            var bank_branch_id = $('#bank_branch_id').val();
            var account_number = $('#bank_account_number').val();
            var initial_balance = $('#bank_initial_balance').val();
            $.post('<?php echo base_url("group/bank_accounts/ajax_create"); ?>',{'account_name':account_name,'bank_id':bank_id,'bank_branch_id':bank_branch_id,'account_number':account_number,'initial_balance':initial_balance},function(data){
                if(isJson(data)){
                    var bank_account = $.parseJSON(data);
                    $('select.account').each(function(){
                        $(this).append('<option value="bank-' + bank_account.id + '">'+bank_account.bank_details+' - ' + bank_account.account_name + ' ('+bank_account.account_number+')</option>').trigger('change');
                    });
                    $('.fine-table select[name="accounts['+current_row+']"]').val("bank-"+bank_account.id).trigger('change');
                    $('.modal').modal('hide');
                    toastr['success']('You have successfully added a new bank account, you can now select it in the accounts dropdown.','Bank account added successfully');
                }else{
                    $('#bank_account_tab .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                }
                $('#create_bank_account').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                mApp.unblock('.modal-body');
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

        $(document).on('click','#create_sacco_account',function(){
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            $('#create_sacco_account').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            var account_name = $('#sacco_account_name').val();
            var sacco_id = $('#sacco_id').val();
            var sacco_branch_id = $('#sacco_branch_id').val();
            var account_number = $('#sacco_account_number').val();
            var initial_balance = $('#sacco_initial_balance').val();
            var id = $('input[name=id]').val();
            $.post('<?php echo base_url("group/sacco_accounts/ajax_create"); ?>',{'account_name':account_name,'sacco_id':sacco_id,'sacco_branch_id':sacco_branch_id,'account_number':account_number,'initial_balance':initial_balance,'id':id,},function(data){
                if(isJson(data)){
                    var sacco_account = $.parseJSON(data);
                    $('select.account').each(function(){
                        $(this).append('<option value="sacco-' + sacco_account.id + '">'+sacco_account.sacco_details+' - ' + sacco_account.account_name + ' ('+sacco_account.account_number+')</option>').trigger('change');
                    });
                    $('.modal').modal('hide');
                    toastr['success']('You have successfully added a new sacco account, you can now select it in the accounts dropdown.','Sacco account added successfully');
                }else{
                    $('#sacco_account_tab .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                }
                $('#create_sacco_account').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                mApp.unblock('.modal-body');
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

        $(document).on('click','#create_mobile_money_account',function(){
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            $('#create_mobile_money_account').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            var account_name = $('#mobile_money_account_name').val();
            var mobile_money_provider_id = $('#mobile_money_provider_id').val();
            var account_number = $('#mobile_money_account_number').val();
            var initial_balance = $('#mobile_money_initial_balance').val();
            $.post('<?php echo base_url("group/mobile_money_accounts/ajax_create"); ?>',{'account_name':account_name,'mobile_money_provider_id':mobile_money_provider_id,'account_number':account_number,'initial_balance':initial_balance},function(data){
                if(isJson(data)){
                    var mobile_money_account = $.parseJSON(data);
                    $('select.account').each(function(){
                        $(this).append('<option value="mobile-' + mobile_money_account.id + '">'+mobile_money_account.mobile_money_provider_details+' - ' + mobile_money_account.account_name + ' ('+mobile_money_account.account_number+')</option>').trigger('change');
                    });                  
                    $('.modal').modal('hide');
                    toastr['success']('You have successfully added a new mobile money account, you can now select it in the accounts dropdown.','Mobile money account added successfully');
                }else{
                    $('#mobile_money_account_tab .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                }
                $('#create_mobile_money_account').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                mApp.unblock('.modal-body');
            });
        });

        $(document).on('click','#create_petty_cash_account',function(){
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Loading...'
            });
            $('#create_petty_cash_account').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            var account_name = $('#petty_cash_account_name').val();
            var slug = $('input[name=slug]').val();
            var initial_balance = $('#petty_cash_initial_balance').val();
            var slug = $('input[name=slug]').val();
            $.post('<?php echo base_url("group/petty_cash_accounts/ajax_create"); ?>',{'account_name':account_name,'account_slug':slug,'initial_balance':initial_balance},function(data){
                if(isJson(data)){
                    var petty_cash_account = $.parseJSON(data);
                    $('select.account').each(function(){
                        $(this).append('<option value="petty-' + petty_cash_account.id + '">' + petty_cash_account.account_name + '</option>').trigger('change');
                    });
                    $('.modal').modal('hide');
                    toastr['success']('You have successfully added a new petty cash account, you can now select it in the accounts dropdown.','Petty cash account added successfully');
                }else{
                    $('#petty_cash_account_tab .data_error').each(function(){
                        $(this).slideDown().html(data);
                    });
                }
                $('#create_petty_cash_account').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                mApp.unblock('.modal-body');
            });
        }); 

        var current_row = 0;
        $(document).on('select2:open','.member', function(e) {
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

        $(document).on('select2:open','.fine_category', function(e) {
            // do something
            var name = $(this).attr("name");
            var row = name.substring(name.lastIndexOf("[")+1,name.lastIndexOf("]"));
            current_row = row;
        }); 

    });

    function handle_tab_switch(tab){
        //clear values on other tabs
        //slide up on other tabs
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
    
</script>
<div id="">
    <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="add_bank_account"'); 
        $readonly = $post->is_verified?'readonly="readonly"':'';
        $readonly = $this->ion_auth->is_admin()?"":$readonly;
    ?>

        <div class="bank_change_options">
            <?php if(isset($default_bank)){ ?>
                <div class="form-group m-form__group row pt-0 m--padding-10 ">
                    <div class="col-sm-12 m-form__group-sub m-input--air">
                        <label><?php echo translate('Bank Name');?><span class="required">*</span></label>
                        <?php echo form_dropdown('bank_id',array('--Select bank --')+$banks,$post->bank_id?:$default_bank->id,'class="form-control bank_id m-select2" '.$readonly.' placeholder="Bank Name"'); ?>
                        <span class="m-form__help"><?php echo translate('Select option');?></span>
                    </div>
                </div>            
            <?php }else{ ?>
                <div class="form-group m-form__group row pt-0 m--padding-10 ">
                    <div class="col-sm-12 m-form__group-sub m-input--air">
                        <label><?php echo translate('Bank Name');?><span class="required">*</span></label>
                        <?php echo form_dropdown('bank_id',array('--Select bank --')+$banks,$this->input->post('bank_id')?:$post->bank_id,'class="form-control bank_id m-select2" '.$readonly.' placeholder="Bank Name"'); ?>
                        <span class="m-form__help"><?php echo translate('Select option');?></span>
                    </div>
                </div>            
            <?php } ?>
            <?php echo form_hidden('id',$post->id); ?>
            
            <div class="form-group m-form__group row pt-0 m--padding-10 ">
                <div class="col-sm-6 m-form__group-sub m-input--air branch_form">
                    <label><?php echo translate('Bank Branch');?><span class="required">*</span></label>
                    <?php echo form_dropdown('bank_branch_id',array(''=>'--Select bank first--')+$bank_branches,$this->input->post('bank_branch_id')?$this->input->post('bank_branch_id'):$post->bank_branch_id,'class="form-control bank_branch_id m-select2 bank_branches_space" '.$readonly.' placeholder="Bank Branch Name"'); ?>
                    <span class="m-form__help"><?php echo translate('Select option');?></span>
                </div>
                <div class="col-sm-6">
                    <label><?php echo translate('Bank Account Name');?><span class="required">*</span></label>
                    <?php echo form_input('account_name',$this->input->post('account_name')?$this->input->post('account_name'):$post->account_name,'class="form-control account_name m-input--air" '.$readonly.' placeholder="Bank Account Name"'); ?>
                </div>
            </div>
            <div class="form-group m-form__group row pt-0 m--padding-10">
                <div class="col-sm-6">
                    <label><?php echo translate('Bank Account Number');?><span class="required">*</span></label>
                    <?php echo form_input('account_number',$this->input->post('account_number')?$this->input->post('account_number'):$post->account_number,'class="form-control account_name m-input--air" '.$readonly.' placeholder="Bank Account Number"'); ?>
                </div>

                <div class="col-sm-6">
                    <label><?php echo translate('Initial Account Balances');?><span class="required">*</span></label>
                    <?php echo form_input('initial_balance',$post->initial_balance?:'','class="form-control initial_balance currency m-input--air" '.$readonly.' placeholder="Bank Account Balance"'); ?>
                </div>
            </div>
            <?php
                if(!$bank_account_signatories){
                    $readonly = '';
                }
            ?>
            <div  class="form-group m-form__group pt-0 m--padding-10 row">
                <div class="col-sm-6 m-form__group-sub m-input--air">
                    <label><?php echo translate('Account Currency');?><span class="required">*</span></label>
                    <?php echo form_dropdown('account_currency_id',array(''=>'--Select account currency--')+$currencies,$post->account_currency_id,'class="form-control account_currency_id m-select2 account_currency_id_space" placeholder="Account Currency" id="account_currency_id"'); ?>
                    <span class="m-form__help"><?php echo translate('Select option');?></span>
                </div>
                <div class="col-lg-6 m-form__group-sub m-input--air">
                    <label><?php echo translate('Select Bank Account Signatories');?></label>
                    <?php echo form_dropdown('account_signatories[]',$this->active_group_member_options,$this->input->post('bank_account_signatories')?:$bank_account_signatories,' id="" class=" form-control m-select2" '.$readonly.' multiple="multiple" data-placeholder="Select..." '); ?>
                </div>
            </div>

            <div class="form-group m-form__group pt-0 m--padding-10 row" id="enable_sms_transaction_alerts_to_members">
                <div class="col-lg-12 m-form__group-sub m-input--air">
                    <label ><?php echo translate('SMS members on transaction alert');?></label>
                   <div class="m-radio-inline">
                        <?php 
                            if($this->input->post('enable_sms_transaction_alerts_to_members')?$this->input->post('enable_sms_transaction_alerts_to_members'):$post->enable_sms_transaction_alerts_to_members==1){
                                $enable_sms_transaction_alerts_to_members = TRUE;
                                $disabled_loan_processing_fee = FALSE;
                            }else if($this->input->post('enable_sms_transaction_alerts_to_members')?$this->input->post('enable_sms_transaction_alerts_to_members'):$post->enable_sms_transaction_alerts_to_members==0){
                                $enable_sms_transaction_alerts_to_members = FALSE;
                                $disabled_loan_processing_fee = TRUE;
                            }else{
                                $enable_sms_transaction_alerts_to_members = TRUE;
                                $disabled_loan_processing_fee = FALSE;
                            }
                        ?>
                        <label class="m-radio m-radio--solid m-radio--brand">
                            <?php echo form_radio('enable_sms_transaction_alerts_to_members',1,$enable_sms_transaction_alerts_to_members,""); ?>
                            <?php echo translate('Yes');?>
                            <span></span>
                        </label>

                        <label class="m-radio m-radio--solid m-radio--brand">
                            <?php echo form_radio('enable_sms_transaction_alerts_to_members',0,$disabled_loan_processing_fee,""); ?>
                            <?php echo translate('No');?>
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
            <div  class="form-group m-form__group pt-0 m--padding-10 row bank_account_sms_transaction_alert_member" style="display:none;">
                <div class="col-lg-12 m-form__group-sub m-input--air">
                    <label><?php echo translate('Select members to send sms');?></label>
                    <?php echo form_dropdown('bank_account_sms_transaction_alert_member_list[]',$this->active_group_member_options,$this->input->post('bank_account_sms_transaction_alert_member_list')?:$sms_selected_group_members,' id="" class=" form-control m-select2" multiple="multiple" data-placeholder="Select..." '); ?>
                </div>
            </div>

            <div class="form-group m-form__group pt-0 m--padding-10 row" id="enable_email_transaction_alerts_to_members">
                <div class="col-lg-12 m-form__group-sub m-input--air">
                    <label ><?php echo translate('Email members on transaction alert');?></label>
                   <div class="m-radio-inline">
                        <?php 
                            if($this->input->post('enable_email_transaction_alerts_to_members')?$this->input->post('enable_email_transaction_alerts_to_members'):$post->enable_email_transaction_alerts_to_members==1){
                                $enable_email_transaction_alerts_to_members = TRUE;
                                $disable_email_transaction_alerts_to_members = FALSE;
                            }else if($this->input->post('enable_email_transaction_alerts_to_members')?$this->input->post('enable_email_transaction_alerts_to_members'):$post->enable_email_transaction_alerts_to_members==0){
                                $enable_email_transaction_alerts_to_members = FALSE;
                                $disable_email_transaction_alerts_to_members = TRUE;
                            }else{
                                $enable_email_transaction_alerts_to_members = TRUE;
                                $disable_email_transaction_alerts_to_members = FALSE;
                            }
                        ?>
                        <label class="m-radio m-radio--solid m-radio--brand">
                            <?php echo form_radio('enable_email_transaction_alerts_to_members',1,$enable_email_transaction_alerts_to_members,""); ?>
                            <?php echo translate('Yes');?>
                            <span></span>
                        </label>

                        <label class="m-radio m-radio--solid m-radio--brand">
                            <?php echo form_radio('enable_email_transaction_alerts_to_members',0,$disable_email_transaction_alerts_to_members,""); ?>
                            <?php echo translate('No');?>
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
            <div  class="form-group m-form__group pt-0 m--padding-10 row bank_account_email_transaction_alert_member" style="display:none;">
                <div class="col-lg-12 m-form__group-sub m-input--air">
                    <label><?php echo translate('Select members to send email');?></label>
                    <?php echo form_dropdown('bank_account_email_transaction_alert_member_list[]',$this->active_group_member_options,$this->input->post('bank_account_email_transaction_alert_member_list')?:$email_selected_group_members,' id="" class=" form-control m-select2" multiple="multiple" data-placeholder="Select..." '); ?>
                </div>
            </div>


            <div class="m--margin-top-50">
                <div class="col-lg-12 col-md-12">
                    <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="add_bank_account_button" type="button">
                            <?php echo translate('Save Changes');?>
                        </button>
                        &nbsp;&nbsp;
                        <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_add_bank_account_form">
                            <?php echo translate('Cancel');?>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    <?php echo form_close();?>
</div>


<script type="text/javascript">

    $(document).ready(function(){
        var empty_branch_list =$('.bank_branches_space').find('select').html();
        var branch_id = '<?php echo $post->bank_branch_id;?>';
        var id =  $('input[name="id"]').val();

        <?php if($post->bank_id){?>
            var bank_id = '<?php echo $post->bank_id;?>';
            $('select[name="bank_id"]').val(bank_id);
        <?php }?>

        if(id==''){
            SnippetAddBankAccount.init(true,false);
        }else{
            SnippetEditBankAccount.init(true,false);
        }
        <?php if($post->enable_sms_transaction_alerts_to_members == 1){?>
            $('.bank_account_sms_transaction_alert_member').slideDown();
        <?php }?>

        <?php if($post->enable_email_transaction_alerts_to_members == 1){?>
            $('.bank_account_email_transaction_alert_member').slideDown();
        <?php }?>

        $(document).on('change','select[name="bank_id"]',function(){
            var bank_id = $(this).val();
            var content = $(".branch_form");
            if(bank_id){
                mApp.block(content, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Getting bank branches...'
                });
                $.post('<?php echo site_url('ajax/bank_accounts/ajax_get_bank_branches');?>',{'bank_id':bank_id,'branch_id':branch_id},
                function(data){
                    $('.bank_branches_space').html(data);
                    $('#bank_branch_id').select2();
                    mApp.unblock(content)
                });
            }else{
                $('.bank_branches_space').html('<select name="bank_id" class="form-control select2" id="bank_branch_id">'+empty_branch_list+'</select>');
                $('#bank_branch_id').select2();
            }
        });

        $(document).on('change','input[name="enable_sms_transaction_alerts_to_members"]',function(){
            var enable_sms_transaction_alerts_to_members = $(this).val();

            console.log('clicked'+enable_sms_transaction_alerts_to_members);
            if(enable_sms_transaction_alerts_to_members == 1){
                $('.bank_account_sms_transaction_alert_member').slideDown();
            }else{
                $('.bank_account_sms_transaction_alert_member').slideUp();
            }
             $(".bank_account_sms_transaction_alert_member .m-select2").select2({
                width: "100%",
                placeholder: {
                    id: '-1',
                    text: "--Select option--",
                },
                allowClear: !0
            });
        });

        $(document).on('change','input[name="enable_email_transaction_alerts_to_members"]',function(){
            var enable_email_transaction_alerts_to_members = $(this).val();

            console.log('clicked'+enable_email_transaction_alerts_to_members);
            if(enable_email_transaction_alerts_to_members == 1){
                $('.bank_account_email_transaction_alert_member').slideDown();
            }else{
                $('.bank_account_email_transaction_alert_member').slideUp();
            }
            $(".bank_account_email_transaction_alert_member .m-select2").select2({
                width: "100%",
                placeholder: {
                    id: '-1',
                    text: "--Select option--",
                },
                allowClear: !0
            });
        });

        $('.m-select2').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
    });

</script>


<?php 
if(empty($loan_types)){ ?>
    <div style="background:#fff!important;border:none;" class="col-md-12 m--align-center">
        <div class=" alert m-alert--outline alert-metal">
            <h4 class="block"><?php echo translate('Sorry! No loan types to apply for')?></h4>
            <p>
                <?php echo translate('There are no loan types configured for this group')?>.
            </p>
        </div>
    </div>
<?php 
}else{
    echo "<div class='row page_menus'>";
        foreach($loan_types as $loan_type): ?>
            <div class="col-md-6">
                <a href="javascript:;">
                    <div class="withdrawal_item">
                        <div class="menu_img">
                            <i class="img mdi mdi-cash-multiple"></i>
                        </div>
                        <div class="menu_cont">
                            <div class="menu_cont_hdr">
                                <div class="overflow_text">
                                    <?php echo translate($loan_type->name); ?>
                                </div>
                            </div>
                            <div class="menu_cont_descr">
                                <span>
                                    <?php  if($loan_type->loan_amount_type == 2  ){
                                        echo  translate('Get upto ').' '.$loan_type->loan_times_number.translate(' times your savings'); 
                                    }else if($loan_type->loan_amount_type == 1){
                                        echo translate('Get between ').$this->group_currency.' '.number_to_currency($loan_type->minimum_loan_amount).' - '.number_to_currency($loan_type->maximum_loan_amount);
                                    }?>
                                </span><br>
                                <?php if(is_numeric($loan_type->grace_period)){?>
                                    <span>
                                        <?php if($loan_type->grace_period == 12){
                                                echo translate('Start paying after').' 1'.' '.translate('Year');
                                            }elseif($loan_type->grace_period>=1 || $loan_type->grace_period <=12){
                                                echo translate('Start paying after').' '.$loan_type->grace_period.' '.translate('Months');
                                            }
                                        ;?>
                                    </span><br>
                                <?php } ;?>
                                <span>
                                    <?php if($loan_type->loan_repayment_period_type == 1){
                                            echo translate('Pay in').' &nbsp;'.$loan_type->fixed_repayment_period.' Months';
                                        }else if ($loan_type->loan_repayment_period_type == 2) {
                                            echo translate('Pay in').' &nbsp;'.$loan_type->minimum_repayment_period.' - '.$loan_type->maximum_repayment_period.' Months';
                                        }
                                    ?>
                                </span><br>
                            </div>
                            <div class="mb-2">
                                <span class="m-badge m-badge--info m-badge--wide loan_type_more_details" data-toggle="modal" data-target="#loan_type_details_modal" id="<?php echo $loan_type->id; ?>">More...</span>
                                <a href="javascript:;" class="btn btn-sm btn-primary action-link float-right apply_now" id="<?php echo $loan_type->id; ?>"><?php echo translate('Apply Now'); ?> <i class="mdi mdi-plus icon_md"></i></a>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="loan_type_form_holder row" class="loan_type_form_holder" style="display: none;">
        <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="apply_member_loan_form"'); ?>
            <fieldset>
                <legend><?php echo translate('Loan Details');?></legend>
                <div class="form-group m-form__group row pt-2 m--padding-0">
                    <div class="col-sm-12 m-form__group-sub m-input--air  d-none">
                        <label>
                            <?php echo translate('Choose Loan Type');?>
                            <span class="required">*</span>
                        </label>
                        <?php echo form_dropdown('loan_type_id',array(''=>'Select Loan Type')+translate($group_loan_types_options),$this->input->post('loan_type_id')?:$post->loan_type_id?:'',' class="form-control m-input m-select2 loan_type" id="loan_type"');?>
                    </div> 
                    <div class="col-sm-12 m-form__group-sub m-input--air pt-2">
                        <div class="loan_type_details" id="loan_type_details"> </div>
                    </div>          
                </div>
                <div class="form-group m-form__group row pt-0 m--padding-0" id="amount_repayment_holder">
                    <div class="col-sm-6 m-form__group-sub m-input--air " id="repayment_periods">
                        <label>
                            <?php echo translate('Repayment Period');?>
                            <span class="required">*</span>
                        </label>
                        <?php echo form_input('repayment_period',$this->input->post('repayment_period')?$this->input->post('repayment_period'):'','  class="form-control m-input--air numeric" placeholder=" Repayment Period" id="repayment_period"'); ?>
                    </div>

                    <div class="col-sm-6 m-form__group-sub m-input--air " id="amount_holder" style="display: none;">
                        <label>
                            <?php echo translate('Amount');?>
                            <span class="required">*</span>
                        </label>
                        <?php echo form_input('loan_application_amount',$this->input->post('loan_application_amount')?$this->input->post('loan_application_amount'):$post->loan_application_amount,'  class="form-control m-input--air currency" placeholder="Loan Application Amount" id="loan_application_amount"'); ?>
                    </div>
                </div>

                <?php echo form_hidden('maximum_loan_amount_from_savings','');?>
                <?php echo form_hidden('maximum_guarantors_from_settings','');?> 
                <?php echo form_hidden('loan_guarantor_type',''); ?>
                <?php echo form_hidden('loan_repayment_type',''); ?>

                <div class="form-group m-form__group row pt-0 m--padding-0" id="form_amortization_holder" style="display: none;">
                    <div class="col-lg-12 col-md-12">
                        <span class="float-lg-left float-md-left float-sm-left float-xl-left">
                            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="loan_btn_amortization" type="button">
                                <?php echo translate('View Loan Amortization');?>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="form-group m-form__group  pt-0 m--padding-0" id="guarantor_settings_holder" style="display: none;">
                    <!-- <div id="append-new-guarantor-setting"></div> -->
                </div>
                
                <div class="form-group m-form__group row pt-0 m--padding-0 " id="agree_to_rules_holder" style="display: none;">
                    <div class="form-group m-form__group m-form__group--sm">
                        <div class="col-xl-12">
                            <div class="m-checkbox-inline ">
                                <label class="m-checkbox m-checkbox--solid m-checkbox--brand form-control-label">
                                    <input type="checkbox" name="loan_rules_check_box" value="1"> 
                                        <?php echo translate('I agree to the') ?> <?php echo ucwords($this->group->name)?> <?php echo translate('loan rules') ?>. 
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row pt-0 m--padding-0" id="form_actions_holder" style="display: none;">
                    <div class="col-lg-12 col-md-12">
                        <span class="float-lg-left float-md-left float-sm-left float-xl-left">
                            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="apply_member_loan_button" type="button">
                                <?php echo translate('Submit Application');?>
                            </button>
                            &nbsp;&nbsp;
                            <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_apply_loan_button">
                                <?php echo translate('Cancel');?>
                            </button> 
                        </span>
                    </div>
                </div>
            </fieldset>
        <?php echo form_close() ?>
    </div>

    <div class="append_guarantor_settings" id="loan_guarantor_row" style="display: none;">
        <div class="row new_guarantor mt-4">
            <div class="col-md-4 m-form__group-sub">
                <label>
                    <?php echo translate('Guarantor Name');?>
                    <span class="required">*</span>
                </label>
                <?php echo form_dropdown('guarantor_ids[]',array(''=>'--Select a Guarantor--')+translate($this->active_group_member_options),'',' class="form-control m-input m-select2-append guarantor_ids"');?>
            </div>
            <div class="col-md-4 m-form__group-sub">
                <label>
                    <?php echo translate('Guaranteed Amount');?>
                    <span class="required">*</span>
                </label>
                <?php echo form_input('guaranteed_amounts[]','','  class="form-control m-input--air currency guaranteed_amounts" placeholder="Guarantor Amount" id="guaranteed_amounts" '); ?>
            </div>
            <div class="col-md-4 m-form__group-sub">
                <label>
                    <?php echo translate('Comment');?>
                </label>
                <?php echo form_input('guarantor_comments[]','','  class="form-control  m-input--air guarantor_comments" placeholder="Guarantor comment" id="guarantor_comments" '); ?>
            </div>
        </div>
    </div>

<!--  <div class='m-form__group form-group m-1 pt-0 m--padding-10'>
                <a data-original-title="Remove Guarantor" href="javascript:;" class="btn-sm m-btn--square btn-danger btn-xs tooltips remove-guarantor-settings">
                    <i class="fa fa-times"></i>
                    <span class="hidden-380">
                        Remove Guarantor
                    </span>
                </a>
            </div> -->
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
            <button type="button" class="btn btn-sm btn-primary m-btn  m-btn m-btn--icon" data-dismiss="modal"><?php echo translate('Close');?></button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="loan_type_details_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body" style="min-height: 150px;">
                    <div class="table-responsive loan_type_details_holder">
                        
                    </div>
                </div>
                <div class="modal-footer" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function(){
        SnippetMemberApplyLoan.init(); 
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        // loan_type_details_holder
        $(document).on('click','.apply_now',function(){
            $('.loan_type').val($(this).attr('id')).trigger('change');
        });

        $(document).on('click','#cancel_apply_loan_button',function(){
            $('.page_menus').slideDown();
            $('.loan_type_form_holder').slideUp();
            $('#loan_type_details').html(""); 
            $('#form_actions_holder').slideUp();
            $('#agree_to_rules_holder').slideUp();
            $('#form_amortization_holder').slideUp();
            $('#amount_holder').slideUp();
            $('#guarantor_settings_holder').slideUp() 
            $('#repayment_periods').slideUp();
        });
        //add account modal close eventt
        $('#loan_type_details_modal').on('hidden.bs.modal', function () {
            $('#loan_type_details_modal .loan_type_details_holder').html('').slideUp();
            $('#loan_type_details_modal .modal-footer').slideUp();
        });

        $('#loan_type_details_modal').on('shown.bs.modal', function () {
            mApp.block('#loan_type_details_modal .modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Getting information...'
            });
        });

        $(document).on('click','.loan_type_more_details',function(){
            var loan_type_id = $(this).attr('id');
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('ajax/loan_types/get_loan_type_information1'); ?>",
                data: {'loan_type_id':loan_type_id},
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);
                        if(result.status == 1){
                            $('#loan_type_details_modal .loan_type_details_holder').html(result.table).slideDown();
                        }else{
                            $('#loan_type_details .loan_type_details_holder').html('<div style="background:#fff!important;border:none;" class="col-md-12 m--align-center"><div class=" alert m-alert--outline alert-metal"><h4 class="block">Error encountered</h4><p>'+result.message+'</p></div></div>').slideDown();  
                        }
                    }else{
                        $('#loan_type_details .loan_type_details_holder').html('<div style="background:#fff!important;border:none;" class="col-md-12 m--align-center"><div class=" alert m-alert--outline alert-metal"><h4 class="block">Error encountered</h4><p>Please refresh page and try again</p></div></div>').slideDown();  
                    }
                    $('#loan_type_details_modal .modal-footer').slideDown();
                    mApp.unblock('#loan_type_details_modal .modal-body');
                }
            });  
        });

        $(document).on('change','.loan_type',function(){
            $('.page_menus').slideUp();
            $('.loan_type_form_holder').slideDown();
            $('#loan_type_details').html(""); 
            $('#form_actions_holder').slideUp();
            $('#agree_to_rules_holder').slideUp();
            $('#form_amortization_holder').slideUp();
            $('#amount_holder').slideUp();
            $('#guarantor_settings_holder').slideUp() 
            $('#repayment_periods').slideUp();
            var loan_type_id = $('.loan_type').val();
            if($(this).val()==''){

            }else{
                load_loan_type_option_details($(this).val());
                //get_maximum_savings($(this).val());
            }
        });

        $(document).on('click','.cancel_apply_loan_button',function(){
            $('.page_menus').slideDown();
            $('.loan_type_form_holder').slideUp();
            $('#loan_type_details').html(""); 
            $('#form_actions_holder').slideUp();
            $('#agree_to_rules_holder').slideUp();
            $('#form_amortization_holder').slideUp();
            $('#amount_holder').slideUp();
            $('#guarantor_settings_holder').slideUp() 
            $('#repayment_periods').slideUp();
        });

        $(document).on('click','#loan_btn_amortization',function(){
            $(this).addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0)
            mApp.block('#apply_member_loan_form',{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Generating Amortization Schedule....'
            });
            var loan_type_id = $('#loan_type').val() ;
            var loan_amount  = $('#loan_application_amount').val();
            var repayment_period = $('#repayment_period').val();    
            if(loan_type_id =="" ){  
                $('#loan_type').parent().addClass('has-danger').append('<div class="form-control-feedback">Please select the loan type</div>');
                mApp.unblock('#apply_member_loan_form');
                $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)           
            }else if(loan_amount== ""){
                $('#loan_application_amount').parent().addClass('has-danger').append('<div class="form-control-feedback">Please enter a valid loan amount</div>');
                mApp.unblock('#apply_member_loan_form');
                $(this).removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
            }else{
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url("ajax/loans/loan_calculator"); ?>',
                    data:{loan_type_id:loan_type_id,loan_amount:loan_amount,repayment_period:repayment_period},
                    dataType : "html",
                        success: function(response) {
                            $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", false)
                            $('#exampleModal').modal('show'); // show bootstrap modal when complete loaded
                            $('#loan_amortization').html(response);                    
                            mApp.unblock('#apply_member_loan_form');
                        },
                        error:function(response){
                            $('#loan_btn_amortization').removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1) 
                            $('#loan_amortization').html(response);                    
                            mApp.unblock('#apply_member_loan_form');                           
                        }
                    }
                );  
            }

        });

        $('#add-new-line-guarantor').on('click',function(){
            var html = $('#loan_guarantor_row').html();
            html = html.replace_all('checker','');
            $('#append-new-guarantor-setting').append('<div class="loan_guarantor_settings_values_templates">'+html+'</div>');
            $('.tooltips').tooltip();
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

    });
    
    function load_loan_type_option_details(loan_type_id){
        if(loan_type_id){
            $('#loan_type_details').html("");
            $('#loan_type_details').css("min-height","70px");
            $('.loan_details_holder').html("");
            mApp.block('#loan_type_details', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Just a moment...'
            });
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('ajax/loan_types/get_loan_type_information'); ?>",
                data: {'loan_type_id':loan_type_id},
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);
                        if(result.status == '200'){
                            $('input[name="maximum_loan_amount_from_savings"]').val(result.member_savings);
                            $('input[name="loan_guarantor_type"]').val(result.loan_guarantor_type);
                            $('input[name="maximum_guarantors_from_settings"]').val(result.minimum_guarantors);
                            $('input[name="loan_repayment_type"]').val(result.repayment_period_type);
                            $('#loan_type_details').html(result.html);
                            if(result.loan_guarantor_type == 1){
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
                                document.querySelector('input[name=loan_application_amount]').removeEventListener('blur',() => {});
                            }else if(result.loan_guarantor_type == 2){
                                document.querySelector('input[name=loan_application_amount]').addEventListener('blur',({target}) => {
                                    if(target.value){
                                        if(result.loan_amount_type == 1){ //amount range
                                            if(parseFloat(target.value.replace(/,/g, "")) > parseFloat(result.maximum_loan_amount)){
                                                $('#guarantor_settings_holder').html('')
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
                                            }else{
                                                $('#guarantor_settings_holder').slideUp();
                                            }
                                        }else if(result.loan_amount_type == 2){//savings
                                            if(parseFloat(target.value.replace(/,/g, "")) > result.member_savings){
                                                $('#guarantor_settings_holder').html('')
                                                var html = $('.append_guarantor_settings').html();
                                                for(var i = 0; i < result.minimum_guarantors; i++) {
                                                    $('#guarantor_settings_holder').append(html);
                                                }
                                                $('#guarantor_settings_holder .m-select2-append').select2({
                                                    placeholder:{
                                                        id: '-1',
                                                        text: "--Select option--",
                                                    }, 
                                                });
                                                update_guarantor_field_names();
                                                $('#guarantor_settings_holder').slideDown();
                                            }else{
                                                $('#guarantor_settings_holder').slideUp();
                                            }
                                        }
                                    }else{
                                        $('#guarantor_settings_holder').slideUp();
                                    }
                                });
                            }else{
                                $('#guarantor_settings_holder').slideUp();
                            }
                            add_repayment_type();
                            // add_guarantors();
                        }else if(result.status == '0'){
                            $('#loan_type_details').html(result.message);  
                        }
                    }else{
                        alert(response);
                    } 
                    //$('#amount_holder').slideDown(); 
                    //$('#repayment_periods').slideDown();
                    $('#form_actions_holder').slideDown(); 
                    $('#agree_to_rules_holder').slideDown(); 
                    $('#form_amortization_holder').slideDown();                                  
                   mApp.unblock('#loan_type_details');
                }
            });  
        }
    }

    function add_repayment_type(){
        var repayment_type = $('input[name="loan_repayment_type"]').val();
        if(repayment_type == '2'){
            $('#amount_holder').removeClass("col-sm-12 m-form__group-sub m-input--air") 
            $('#amount_holder').addClass("col-sm-6 m-form__group-sub m-input--air")
            $('#repayment_periods').addClass("col-sm-6 m-form__group-sub m-input--air ")
            $('#amount_holder').slideDown();
            $('#repayment_periods').slideDown();
        }else{           
           $('#amount_holder').removeClass("col-sm-6 m-form__group-sub m-input--air") 
           $('#amount_holder').addClass("col-sm-12 m-form__group-sub m-input--air ")
           $('#amount_holder').slideDown();
           $('#repayment_periods').slideUp();
        }
    } 

    function add_guarantors(){      
        var sms_templates =  $("div[class*='loan_guarantor_settings_values_templates']").length; 
        var loan_guarantor_type =  $('input[name="loan_guarantor_type"]').val(); 
        var count = $('input[name="maximum_guarantors_from_settings"]').val();
        var guarantor_information = $('.guarantor_information');   

        if(loan_guarantor_type == 1){
            if(sms_templates == 0){
                $('append_guarantor_settings')
                for(var i = 0; i < count; i++) {
                    guarantor_click()
                   $('#add-new-line-guarantor').trigger('click');
                }
                $('#guarantor_settings_holder').slideDown() 
            }
        }else if(loan_guarantor_type == 2){                       
            var maximum_amount_from_savings = $("input[name=maximum_loan_amount_from_savings]").val();        
            var loan_amount = $("input[name=loan_amount]").val();
            var amount_string =  parseFloat(loan_amount.replace(/,/g, ""));
            var count = $('input[name="maximum_guarantors_from_settings"]').val();           
            if(amount_string){
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
        // $('#append-new-guarantor-setting .m-select2-append').select2({
        //     placeholder:{
        //         id: '-1',
        //         text: "--Select option--",
        //     }, 
        //     // allowClear: !0
        // });
    }
 
  
</script>
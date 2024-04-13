<?php echo form_open(current_url(),'class="form_submit" role="form"');?>   
    <div class="form-body">
        <div class="form-group">
            <label>Select Loan Type<span class="required">*</span></label>
            
             <div class="input-group col-xs-12">
                <?php echo form_dropdown('loan_type_id',array(''=>'--Select Loan Type--')+$group_loan_types_options,$this->input->post('loan_type_id')?$this->input->post('loan_type_id'):$post->loan_type_id,'class="form-control select2 loan_type_id" id ="loan_type_id"  ') ?>
            </div>
        </div>
        <div class="loan_type_details" id="loan_type_details"> </div>
        
        <div class="row " id='repayment_periods' style="display: none;">
            <div class="col-md-12">
                 <div class="form-group ">
                    <label> Repayment Period<span class="required">*</span></label>
                        <div class="input-group col-xs-12  ">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                             <?php echo form_input('repayment_period',$this->input->post('repayment_period')?$this->input->post('repayment_period'):'','  class="form-control numeric" placeholder=" Repayment Period" id="repayment_period"'); ?>
                        </div>                   
                        <span class="help-block"> Value in months eg.2 </span>
                 </div>
            </div> 
        </div>      

        <div class="form-group" id="loan_amount_form_holder" style="display: none;">
            <label>Loan Amount<span class="required">*</span></label>
             <div class="input-group input-group-md">
                <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                </span>
                 <?php echo form_input('loan_application_amount',$this->input->post('loan_application_amount')?$this->input->post('loan_application_amount'):$post->loan_application_amount,'  class="form-control currency" placeholder="Loan Application Amount" id="loan_application_amount"'); ?>
            </div>
        </div>

        <?php echo form_hidden('maximum_loan_amount_from_savings','');?>
        <?php echo form_hidden('maximum_guarantors_from_settings','');?> 
        <?php echo form_hidden('loan_guarantor_type',''); ?>
        <?php echo form_hidden('loan_repayment_type',''); ?>
        <div class="form-actions">
            <button type="button" onclick="view_amortization()" class="btn blue submit_form_button" id="btnloan_amortization" style="margin-bottom: 10px; display: none;">View loan Amortization</button> 

             <div id="guarantor_numbers_form_holder" class="  submit_form_button" style="margin-bottom: 10px; display: none;"></div>               
        </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Loan Details Amortization Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                   <div class="loan_amortization" id="loan_amortization"> </div> 
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

                      
        <div class="row guarantors" id="row guarantors">
            <?php if(!isset($_POST['guarantor_id'])){    
                echo '
                <div class="col-md-12 guarantor_information" style="display: none;">
                    <h4> Kindly select atleast <span class="guarantors_number"> </span> to guarantee your <span class="additional_information"> </span> loan.</h4>
                </div>';
            }
            ?>    
            <div id='append-place-holder'>
               <?php
                if(isset($_POST['guarantor_id'])){
                    $guarantor_ids = $this->input->post('guarantor_id');
                    $guaranteed_amounts = $this->input->post('guaranteed_amount');
                    $guarantor_comments = $this->input->post('guarantor_comment');
                    echo '<div class="post_guarantor_row" style="display:none;" >';
                        echo '<div class="loan_guarantor_settings_values_templates" id="loan_guarantor_settings_values_templates">
                                <div class="col-md-12 guarantor_information">
                                    <h4> Kindly select atleast <span class="guarantors_number"> </span> to guarantee your <span class="additional_information"> </span> loan.</h4>
                                </div>                
                            </div>';

                    foreach ($guarantor_ids as $key => $guarantor_id) { ?>
                        <div class="loan_guarantor_settings_values_templates">
                            <div class="form-group col-md-4">
                                <label>Guarantor's Name<span class="required">*</span></label>
                                <div class="input-group col-xs-12 ">
                                    <?php echo form_dropdown('guarantor_id[]',array(''=>'--Select a Guarantor--')+$this->active_group_member_options,$guarantor_id,'class="form-control select2 guarantor_id"') ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Guaranteed Amount<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    <?php echo form_input('guaranteed_amount[]',$guaranteed_amounts[$key],'  class="form-control currency guaranteed_amount" placeholder="Enter guaranteed amount"'); ?>
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Comment</label>
                                <?php echo form_input('guarantor_comment[]',$guarantor_comments[$key],'  class="form-control guarantor_comment" placeholder="Enter comment"'); ?>
                            </div>
                            <div class='col-md-12 margin-bottom-10 text-left'>
                                <a data-original-title="Remove Guarantor" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-line" >
                                    <i class="fa fa-times"></i>
                                    <span class="hidden-380">
                                        Remove Guarantor
                                    </span>
                                </a>
                            </div>

                        </div>
                        
                        <div class="clearfix"></div>
                    <?php        
                    }
                    echo '</div>';
                }else{
                }
               ?>
            </div>
            <div id='append-place-holder'></div>

            <div class='col-md-12 margin-bottom-10 text-left' style="display: none;" id="gurantor_form_holder">
                <a href="javascript:;" class="btn btn-default btn-xs" id="add-new-line-guarantor">
                    <i class="fa fa-plus"></i>
                    <span class="hidden-380">
                        Add another Guarantor
                    </span>
                </a>
            </div>
        </div>

        <div class="form-group" id="supervisor_form_holder" style="display: none;">
            <label>Choose Your Supervisor<span class="required">*</span></label>
             <div class="input-group input-group-md">
                <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                </span>
                <div class="input-group col-xs-12 ">
                    <?php echo form_dropdown('supervisor_id',array(''=>'--Select a Supervisor--')+$this->active_group_member_options,$post->supervisor_id,'class="form-control select2 supervisor_id"') ?>
                </div>
            </div>
        </div>

        <div class="form-group agree_to_rules_holder" style="display: none;">
            <div class="input-group checkbox-list col-xs-12 ">
                <label class="checkbox-inline">
                    <?php echo form_checkbox('loan_rules_check_box',1,$this->input->post('loan_rules_check_box')?$this->input->post('loan_rules_check_box'):$post->loan_rules_check_box,' id="loan_rules_check_box" class="loan_rules_check_box" '); ?> Agree to group loan rules.
                </label>
            </div>
        </div>
    </div>

<div class="form-actions form_action_form_holder" style="display: none;">
    <button type="submit"  class="btn blue submit_form_button">Apply</button>
    <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
    <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
</div>
 
 

<?php echo form_close() ?>

<div class="" id="loan_guarantor_row"  style="display: none;">
    <div class="form-group col-md-4">
        <label>Guarantor's Name<span class="required">*</span></label>
        <div class="input-group col-xs-12 ">
            <?php echo form_dropdown('guarantor_id[]',array(''=>'--Select a Guarantor--')+$this->active_group_member_options,'','class="form-control  guarantor_id"') ?>
        </div>
    </div>

    <div class="form-group col-md-4">
        <label>Guaranteed Amount<span class="required">*</span></label>
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

    <div class='col-md-12  margin-bottom-10  text-left'>
        <a data-original-title="Remove Guarantor" href="javascript:;" class="btn btn-danger btn-xs tooltips remove-line" >
            <i class="fa fa-times"></i>
            <span class="hidden-380">
                Remove Guarantor
            </span>
        </a>
    </div>
    <div class="clearfix"></div>
</div>


<script>
    $(document).ready(function(){
        <?php
        $loan_type_id = ""; 
        if($loan_type_id){ ?>
            var loan_type_id = "<?php echo $loan_type_id; ?>";
            load_loan_type_option_details(loan_type_id);
            
        <?php } ?>

        $('.loan_type_id').on('change',function(){
            var loan_type_id = $('#loan_type_id').val();
            if(loan_type_id){
                $('.loan_guarantor_settings_values_templates').slideDown(); 
                $('.guarantors').slideUp();
                var loan_temp =  $('#guarantor_numbers_form_holder').html();
                $("div #guarantor_numbers_form_holder").html("");
                $("div #guarantor_numbers_form_holder").empty("");
                $('#loan_amount_form_holder').slideUp();
                $('#supervisor_form_holder').slideUp();
                $('#btnloan_amortization').slideUp(); 
                $('#loan_type_details').slideDown();
                $('#repayment_periods').slideUp();
                $('.agree_to_rules_holder').slideUp();
                $('.form_action_form_holder').slideUp(); 
                $('#gurantor_form_holder').slideUp();
                $('input[name="if_guarantors_enabled"]').val('');
                $('#append-place-holder').each(function(){
                    $('#append-place-holder').html(''); 
                });
                load_loan_type_option_details($(this).val());                

                /*check_if_exceed_amount($(this).val());
                load_loan_type_of_repayment($(this).val());
                check_if_guarantors_enabled($(this).val());
                get_no_of_guarantors($(this).val());*/  
                
            }else{
                $('#loan_type_details').slideUp();
                $('#loan_amount_form_holder').slideUp();
                $('#supervisor_form_holder').slideUp();
                $('#btnloan_amortization').slideUp(); 
                $('#repayment_periods').slideUp();
                $('.agree_to_rules_holder').slideUp();
                $('.form_action_form_holder').slideUp(); 
                $('input[name="if_guarantors_enabled"]').val('');
                $('#append-place-holder').each(function(){
                    $('#append-place-holder').html(''); 
                });
                $('input[name="if_guarantors_enabled"]').val('');
                $("div #guarantor_numbers_form_holder").html("");
                $("div #guarantor_numbers_form_holder").empty("");
                $('.loan_guarantor_settings_values_templates').slideUp(); 
                $('.guarantors').slideUp();
            }
                        
        });

        /*******Add new line script****/
        $(document).on('click','#add-new-line-guarantor',function(){
            $('.guarantor_id').each(function(){
                if($(this).data('select2')){
                    $(this).select2('destroy');
                }
            });
            var html = $('#loan_guarantor_row').html();
            $('#append-place-holder').append('<div class="loan_guarantor_settings_values_templates">'+html+'</div>');
            $('.loan_guarantor_settings_values_templates .guarantor_id').select2({
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
            });
            update_guarantor_counts();
            FormInputMask.init();
            $('.tooltips').tooltip();
        });

        /******Remove line script***/
        $(document).on('click','.remove-line',function(){ 
            $(this).parent().parent().remove();
            update_guarantor_counts();
        });
    });

    String.prototype.replace_all = function(search,replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };

    function update_guarantor_counts(){
        var count = 0;
        $('.guarantor_id').each(function(){
            $(this).attr('name','guarantor_id['+(count)+']');
            count++
            //console.log($(this).attr('name','guarantor_id['+(count)+']'));
        });
        
    }

    $(window).on('load',function(){
        var loan_type_id = $('select[name="loan_type_id"]').val();
        load_loan_type_option_details(loan_type_id); 
        //add_guarantors();
        $('.post_guarantor_row').slideDown();
    });

    function check_if_exceed_amount(loan_type_id){
        //var loan_type_id = $('#loan_type_id').val();
        $.ajax({
            type:'POST',
            url: '<?php echo base_url("ajax/loan_types/check_if_exceed_amount"); ?>',
            data:{loan_type_id:loan_type_id},
            success: function(response){
                $("input[name=maximum_loan_amount_from_savings]").val(response);
                $("#loan_application_amount").trigger('keyup');
            },
            error: function(response){

            }
        });
    }
    function get_no_of_guarantors(loan_type_id){        
        $.ajax({
            type:'POST',
            url: '<?php echo base_url("ajax/loan_types/get_no_of_guarantors"); ?>',
            data:{loan_type_id:loan_type_id},
            success: function(response){
                var data = $.parseJSON(response);
                if(isJson(data)){
                  $("input[name=maximum_guarantors_from_settings]").val(data);
                }                 
            },
            error: function(response){

            }
        });

    }


    $(document).on('keyup keydown','#loan_application_amount',function(){
        add_guarantors();
    });

    function view_amortization(){
     $('#btnloan_amortization').text('');
     $('#btnloan_amortization').append('<span><i class="fa fa-spinner fa-spin"></i> Processing  </span>');
     $('#btnloan_amortization').attr('disabled',true);
     var loan_type_id = $('#loan_type_id').val() ;
     var loan_amount  = $('#loan_application_amount').val();
     var repayment_period = $('#repayment_period').val();
       $('#loan_amortization').html("");
            $('#loan_amortization').css("min-height","70px");
            App.blockUI({
                target: '#loan_amortization',
                overlayColor: 'grey',
                animate: true
            });
            if(loan_type_id =="" ){  
                $('#exampleModal').modal('show');               
                $('#loan_amortization').append('<span><div class="alert alert-danger"><button class="close"data-dismiss="alert"></button><p>Loan type is required</p></div> </span>');
                 App.unblockUI('#loan_amortization');
                  $('#btnloan_amortization').text('');
                  $('#btnloan_amortization').append('<span> View loan Amortization </span>');
                  $('#btnloan_amortization').attr('disabled',false);
            }else if(loan_amount== ""){
                $('#exampleModal').modal('show'); 
                $('#loan_amortization').append('<span><div class="alert alert-danger"><button class="close"data-dismiss="alert"></button><p>Loan  Amount is  required</p></div> </span>');
                 App.unblockUI('#loan_amortization');
                  $('#btnloan_amortization').text('');
                  $('#btnloan_amortization').append('<span> View loan Amortization </span>');
                  $('#btnloan_amortization').attr('disabled',false);
            }else{
              $.ajax({
                    type: "POST",
                    url: '<?php echo base_url("member/loans/loan_calculator"); ?>',
                    data:{loan_type_id:loan_type_id,loan_amount:loan_amount,repayment_period:repayment_period},
                    dataType : "html",
                        success: function(response) {
                            $('#exampleModal').modal('show'); // show bootstrap modal when complete loaded
                            $('#loan_amortization').html(response);                    
                            App.unblockUI('#loan_amortization');
                             $('#btnloan_amortization').text('');
                             $('#btnloan_amortization').append('<span> View loan Amortization </span>');
                             $('#btnloan_amortization').attr('disabled',false);
                        },
                        error:function(response){
                           $('#loan_amortization').html(response);                    
                            App.unblockUI('#loan_amortization'); 
                        }
                    }
                );  
            } 
    }  

    function add_repayment_type(){
        var repayment_type = $('input[name="loan_repayment_type"]').val();
        if(repayment_type == '2'){
            $('#repayment_periods').slideDown();
        }else{
           $('#repayment_periods').slideUp(); 
        }
    } 

    function add_guarantors(){
        var loan_guarantor_type =  $('input[name="loan_guarantor_type"]').val();
        var sms_templates =  $("div[class*='loan_guarantor_settings_values_templates']").length;
        var count = $('input[name="maximum_guarantors_from_settings"]').val();
        var guarantor_information = $('.guarantor_information');
        if(loan_guarantor_type == 1){
            guarantor_information.children().find('.guarantors_number').html(count);
            guarantor_information.slideDown();

            if(sms_templates ==0){
                for(var i = 0; i < count; i++) {
                   $('#add-new-line-guarantor').trigger('click');
                } 
            }else{
                
            }
        }else if(loan_guarantor_type == 2){
            var maximum_amount_from_savings = parseFloat($("input[name=maximum_loan_amount_from_savings]").val());        
            var loan_amount = $("input[name=loan_application_amount]").val();
            var amount_string = parseFloat(loan_amount.replace(/,/g, ""));
            if(amount_string > maximum_amount_from_savings){
                guarantor_information.children().find('.guarantors_number').html(count);
                var difference = number_to_currency(amount_string-maximum_amount_from_savings);
                $('.additional_information').text('additional <?php echo $this->group_currency;?>. '+difference);
                guarantor_information.slideDown();
                if(sms_templates ==0){
                    for(var i = 0; i < count; i++) {
                       $('#add-new-line-guarantor').trigger('click');
                    }                   
                }else{ 

                }
            }else{
                $('#append-place-holder').html('');
                guarantor_information.slideUp();
            }
        }else{
            $('#append-place-holder').html('');
            guarantor_information.slideUp();
        }
    }    

    function number_to_currency(number){
        return number.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    }

    function load_loan_type_option_details(loan_type_id){
        if(loan_type_id){
            $('#loan_type_details').html("");
            $('#loan_type_details').css("min-height","70px");
            App.blockUI({
                target: '#loan_type_details',
                overlayColor: 'grey',
                animate: true
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
                            $('input[name="maximum_guarantors_from_settings"]').val(result.maximum_guarantors);
                            $('input[name="loan_repayment_type"]').val(result.repayment_period_type);
                            $('#loan_type_details').html(result.html);  
                            add_repayment_type();
                            add_guarantors();
                        }else if(result.status == '0'){
                            $('#loan_type_details').html(result.message);  
                        }
                    }else{
                        alert(response);
                    }           
                    $('.guarantors').slideDown();
                    $('#gurantor_form_holder').slideDown();
                    $('#loan_amount_form_holder').slideDown();
                    $('#btnloan_amortization').show(); 
                    var loan_amount = $('#loan_application_amount').val();
                    $("input[name=loan_application_amount]").trigger('keyup');
                    $('.agree_to_rules_holder').slideDown();
                    $('.form_action_form_holder').slideDown();
                    $('#supervisor_form_holder').slideDown();        
                    App.unblockUI('#loan_type_details');
                }
            });  
        }
    }

    function load_loan_type_of_repayment(loan_type_id){
     var id = loan_type_id;
     $.ajax({
        url:'<?php echo base_url("ajax/loan_types/get_loan_repayment_type");?>',
        type:'POST',
        data:{loan_type_id:id},
        dataType:'JSON',
        success: function(data){
            var response = $.parseJSON(data);
            if(response == 1){
                $('#btnloan_amortization').show();
                $('#loan_amount_form_holder').slideDown();
                $('#repayment_periods').slideUp();
            }else if(response == 2){
              $('#loan_amount_form_holder').slideDown();
              $('#repayment_periods').slideDown();
              $('#btnloan_amortization').show();
            }
            //$('.agree_to_rules_holder').slideDown();
            //$('.form_action_form_holder').slideDown();
        },
        error: function(data){
            console.log(data);
        }
     });
    }

    function check_if_guarantors_enabled(loan_type_id){
        $.ajax({
            type:'POST',
            url:'<?php echo site_url("ajax/loan_types/get_loan_types_guarantors_option");?>',
            data:{loan_type_id:loan_type_id},
            success: function(response){
                if(isJson(response)){               
                    if(response == 2){                        
                        $('input[name="if_guarantors_enabled"]').val(response);
                        $('.loan_guarantor_settings_values_templates').slideUp();
                        $('.append-add-button').slideUp();
                    }else if(response == 1){
                        $('input[name="if_guarantors_enabled"]').val(response);
                       $('.append-add-button').slideUp();
                        $('select[name=guarantor_id').val('');
                        var count = $("input[name=maximum_guarantors_from_settings]").val();
                        var sms_templates =  $("div[class*='loan_guarantor_settings_values_templates']").length;
                        var guarantor_no = parseFloat(count) - (1);
                       // alert(count);
                        //alert(sms_templates);
                        if(sms_templates == 1){
                            for(var i = 0; i < count; i++) {
                               $('#add-new-line-guarantor').trigger('click');
                            } 
                        }
                        alert($_POST[$guarantor_id]);
                        $('.loan_guarantor_settings_values_templates').slideDown();                                                
                    }                    
                }else{
                    console.log('response is not json');
                    //$('.loan_guarantor_settings_values_templates').slideUp();
                }             
            },
            error: function(response){
              console.log(response);
            }
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

    function toCurrency(num){
        num.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
        return num.toFixed(2);
       
    }    
</script>
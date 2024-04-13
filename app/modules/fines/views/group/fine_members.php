<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state" role="form" id="fine_members_form"'); ?>
    <div class="table-responsive">
        <table class="table table-condensed  fine-category-table multiple_payment_entries">
            <thead>
                <tr>
                    <th width="2%">
                        #
                    </th>
                    <th width="15%">
                        <?php echo translate('Fine Date');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="30%">
                        <?php echo translate('Members');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="25%">
                        <?php echo translate('Fine category');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="20%">
                        <?php echo translate('Amount');?>
                         (<?php echo $this->group_currency; ?>) 
                        <span class='required'>*</span>
                    </th>
                    <th width="5%">
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
                        <?php echo form_input('fine_dates[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input fine_dates date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" ');?>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                             <?php echo form_dropdown('members[0][]',$this->active_group_member_options,'',' class="form-control m-input m-select2 member" multiple="multiple"');?>
                         </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_dropdown('fine_categories[0]',array(''=>'Select fine category')+translate($fine_category_options)+array('0'=>"Add fine category "),'',' class="form-control fine_category m-input m-select2 fine_category" ');?>
                        </span>
                    </td>                                   
                    <td>
                        <?php echo form_input('amounts[0]','',' class="form-control m-input--air input-sm amount currency text-right" ');?>
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
                <i class="la la-plus"></i><?php echo translate('Add New Fine Line');?>
            </a>
        </div>
    </div>
    <div class="m-form__actions m-form__actions p-0 pt-5 m--margin-top-30">                            
        <div class="row">
            <div class="col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button name="submit" value="1" class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm submit_form_button" id="fine_members" type="button">
                        <?php echo translate('Fine members');?>                            
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button"  >
                        Cancel                              
                    </button>
                </span>
            </div>
        </div>
    </div>
    <!-- <div class="m-form__actions m-form__actions">
        
        <div class="row">
            <div class="col-md-12">
                <button type="button" name="submit" id="fine_members" value="1" class="btn m-btn--square  btn-success submit_form_button">
                    <?php echo translate('Fine members');?>
                </button>
                <button type="button" class="btn m-btn--square  btn-secondary">
                    <?php echo translate('cancel');?>
            </div>
        </div>
    </div> -->
<?php echo form_close() ?>
<div id='append-new-line' class="d-none">
    <table>
        <tbody>
            <tr>
                <th scope="row" class="count">
                    1
                </th>
                <td>
                    <?php echo form_input('fine_dates[0]',timestamp_to_datepicker(time()),' class="form-control input-sm m-input fine_dates date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" data-date-end-date="0d" autocomplete="off" ');?>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                         <?php echo form_dropdown('members[0][]',$this->active_group_member_options,'',' class="form-control m-input m-select2-append member" multiple="multiple"');?>
                     </span>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('fine_categories[0]',array(''=>'Select fine category')+translate($fine_category_options)+array('0'=>"Add Fine Category"),'',' class="form-control m-input m-select2-append fine_category" ');?>
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
                    <a href='javascript:;' class="remove-line">
                        <i class="text-danger la la-trash" style="margin-top:25%;"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="modal fade" id="create_new_member_pop_up" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
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
                    <div class="alert alert-danger data_error" id="alert_add_member" style="display:none;">
                    </div>
                    <div id="add_new_member_form" >
                        <div class="m-form__section m-form__section--first">

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group m-form__group">
                                        <label>
                                            <?php echo translate('First Name');?>
                                            <span class="required">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon2">
                                                    <i class="fa fa-user"></i>
                                                </span>
                                            </div>
                                            <?php echo form_input('first_name','',' id="first_name" class="form-control" placeholder="First Name"');?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group m-form__group">
                                        <label>
                                            <?php echo translate('Middle Name');?>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon2">
                                                    <i class="fa fa-user"></i>
                                                </span>
                                            </div>
                                            <?php echo form_input('middle_name','',' id="middle_name" class="form-control" placeholder="Middle Name"');?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group m-form__group">
                                        <label>
                                            <?php echo translate('Last Name');?>
                                            <span class="required">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon2">
                                                    <i class="fa fa-user"></i>
                                                </span>
                                            </div>
                                            <?php echo form_input('last_name','',' id="last_name" class="form-control" placeholder="Last Name"');?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="form-group m-form__group col-md-6">
                                    <label>
                                        <?php echo translate('Phone Number');?>
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2">
                                                <i class="fa fa-phone"></i>
                                            </span>
                                        </div>
                                        <?php echo form_input('phone','',' id="phone" class="form-control" placeholder="Phone Number"');?>
                                    </div>
                                </div>

                                <div class="form-group m-form__group col-md-6">
                                    <label>
                                        <?php echo translate('Email Address');?>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                        <?php echo form_input('email','',' id="email" class="form-control" placeholder="Email Address"');?>
                                    </div>
                                </div>
                            </div>

                            <div class="m-form__group form-group">
                                <label for="">
                                    <?php echo translate('Invitation Notifications');?>
                                </label>
                                <div class="m-checkbox-inline">
                                    <label class="m-checkbox">
                                        <?php echo form_checkbox('send_sms_notification',1,'',' id="send_sms_notification" '); ?>
                                        <?php echo translate('Send SMS Invitation');?>
                                        <span></span>
                                    </label>
                                    <label class="m-checkbox">
                                        <?php echo form_checkbox('send_email_notification',1,'',' id="send_email_notification" '); ?>
                                        <?php echo translate('Send Email Invitation');?>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <?php echo translate('Close');?>
                    </button>
                    <button type="submit" id="add_member_submit" class="btn btn-primary submit modal_submit_form_button">
                        <?php echo translate('Save changes');?>
                    </button>
                </div>
        </div>
    </div>
</div>
<a class="d-none inline" data-toggle="modal" data-target="#create_new_member_pop_up" data-title="Add Member" data-id="create_member" id="add_new_member"  >
    <?php echo translate('Add Member');?>
</a>

<div class="modal fade" id="create_new_fine_category_pop_up" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger data_error" id="alert_result" style="display:none;">
                            </div>
                            <div class="form-group m-form__group">
                                <label>
                                    <?php echo translate('Fine Category');?>
                                    <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2">
                                            <i class="fa fa-user"></i>
                                        </span>
                                    </div>
                                    <?php echo form_input('name','',' id="name" class="form-control" placeholder="Fine Category Name"');?>
                                    <?php echo form_hidden('slug',$this->input->post('slug'), 'id="slug" ');?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <?php echo translate('Close');?>
                    </button>
                    <button type="submit" id="add_fine_category_submit" class="btn btn-primary submit modal_submit_form_button">
                        <?php echo translate('Save changes');?>
                    </button>
                </div>
        </div>
    </div>
</div>

<a class="d-none inline" data-toggle="modal" data-target="#create_new_fine_category_pop_up" data-title="Add Fine category" data-id="create_fine_category" id="add_new_fine_category"  >
    <?php echo translate('Add Fine Category');?>
</a>




<script>
    $(document).ready(function(){
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });
        $(document).on('change','.member',function(){
            if($(this).val()=='0'){
                $('#add_new_member').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.fine_category',function(){
            if($(this).val()=='0'){
                $('#add_new_fine_category').trigger('click');
                $(this).val("").trigger('change');
            }
        });
      
        $(document).on('click','#add_new_member',function(){
            $(".member").select2({
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


        $(document).on('click','#add_new_fine_category',function(){
            $(".fine_category").select2({
                language: 
                    {
                    noResults: function() {
                        return '<a class="inline" data-toggle="modal" data-content="#create_new_fine_category_pop_up" data-title="Add Fine Category" data-id="create_fine_category" id="add_new_fine_category"  >Add Fine Category</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger("select2:close");
        })

        $('#add_member_submit').on('click',function(e){
            $('#alert_add_member').slideUp().html();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            var first_name = $('#create_new_member_pop_up #first_name').val();
            var middle_name = $('#create_new_member_pop_up #middle_name').val();
            var last_name = $('#create_new_member_pop_up #last_name').val();
            var email = $('#create_new_member_pop_up #email').val();
            var phone = $('#create_new_member_pop_up #phone').val();
            var send_sms_notification = $('#create_new_member_pop_up #send_sms_notification').val();
            var send_email_notification = $('#create_new_member_pop_up #send_email_notification').val();
            $.post('<?php echo base_url("group/members/ajax_add_member"); ?>',{'first_name':first_name,'last_name':last_name,'email':email,'phone':phone,'send_sms_notification':send_sms_notification,'send_email_notification':send_email_notification,},function(data){
                if(isJson(data)){
                    var member = $.parseJSON(data);
                    $('select.member').each(function(){
                        $(this).append('<option value="' + member.id + '">' + member.first_name +' '+ member.last_name + '</option>').trigger('change');
                    });
                    $('.contribution-table select[name="members['+current_row+']"]').val(member.id).trigger('change');
                    $('#alert_add_member').hide().html('');
                    $('.modal').modal('hide');
                    toastr['success']('You have successfully added a new member to your group, you can now select him/her in the members dropdown.','Member added successfully');
                }else{
                    $('.data_error').each(function(){
                        $('#alert_result').show().html(data);
                        console.log(data);
                    });
                }
                mApp.unblock('.modal-body', {});
            });
        });

        $('#add_fine_category_submit').on('click',function(e){
            $('.data_error').slideUp().html();
            mApp.block('.modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            var name = $('#create_new_fine_category_pop_up #name').val();
            var slug = $( "input[name='slug']" ).val();
            $.post('<?php echo base_url("group/fine_categories/ajax_create"); ?>',{'name':name,'slug':slug},function(data){
                if(isJson(data)){
                    var result = $.parseJSON(data);
                    if(result.status == '1'){
                        $('.modal').modal('hide');
                        toastr['success'](result.message);
                    }else{
                        $('.data_error').each(function(){
                            $('#alert_result').show().html(result.message);
                        });     
                    }
                }else{
                    $('.data_error').each(function(){
                        $('#alert_result').show().html(result.message);
                    });
                }
                mApp.unblock('.modal-body', {});
            });

        });

        $(document).on('click','.remove-line',function(event){
            $(this).parent().parent().remove();
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                number++;
            });
            TotalAmount.init();
        });

        $('.add-new-line').on('click',function(){
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
                $(this).parent().find('.fine_categories').attr('name','fine_categories['+(number-1)+']');
                $(this).parent().find('.amount').attr('name','amounts['+(number-1)+']');
                $(this).parent().find('.send_sms_notification').attr('name','send_sms_notifications['+(number-1)+']');
                $(this).parent().find('.send_email_notification').attr('name','send_email_notifications['+(number-1)+']');
                number++;
            });

            $('.fine-category-table .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
            });
        });

        SnippetFineMembers.init();

    });

</script>
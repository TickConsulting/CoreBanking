<link rel="stylesheet" href="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/css/intlTelInput.min.css');?>">
<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state" role="form" id="add_new_members_line"'); ?>
    <table class="table table-checkable member-table table-condensed" id="multiple-entries">
        <thead>
            <tr>
                <th width="2%">#</th>
                <th width="25%"><?php echo translate('Full Names');?> <span class="required">*</span></th>
                <th width="20%"><?php echo translate('Phone Number');?> <span class="required">*</span></th>
                <th width="20%"><?php echo translate('Email Address');?> </th>
                <th width="20%"><?php echo translate('Group Role');?> <span class="required">*</span></th>
                <th width="5%"><?php echo translate('Alerts'); ?></th>
                <th width="5%">&nbsp;</th>
            </tr>
        </thead>
        <tbody id="append-place-holder">
            <tr>
                <td class="number" style="padding: 1.0rem;">1.</td>
                <td>
                    <?php echo form_input('names[]','','class="form-control form-control-sm m-input names m-input--air capitalize" placeholder=""');?>
                </td>
                <td>
                    <?php echo form_input("phones[]",'','class="form-control form-control-sm m-input--air cust_login_phone phones"');?>
                </td>
                <td>
                    <?php echo form_input('email_addresses[]','','class="form-control form-control-sm m-input m-input--air email_addresses lowercase" placeholder=""');?>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('group_role_ids[0]',translate($group_role_options),'','class="form-control m-input m-select2 m-input--air group_role_ids"');?>
                    </span>
                </td>
                <td>
                    <div class="row">
                        <span class="col-md-6" data-toggle="m-tooltip" title="" data-original-title="Send SMS notification">
                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                <?php echo form_checkbox('send_sms_notification[0]',1,FALSE,' class = "send_sms_notification" '); ?>
                                <span></span>
                            </label>
                        </span>
                        <span class="col-md-6" data-toggle="m-tooltip" title="" data-original-title="Send Email notification">
                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                <?php echo form_checkbox('send_email_notification[0]',1,FALSE,' class = "send_email_notification" '); ?>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </td>
                <td class="text-right">
                    <a href='javascript:;' class="remove-line">
                        <i class="text-danger la la-trash" style="margin-top:15%;"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
    <a type="button" class="btn btn-primary myModal d-none" data-keyboard="false" data-backdrop="static" data-toggle="modal" data-target="#add_new_role_modal" id="add_new_role_hidden"> Add New Role</a>
    <div class="m-form__actions m-form__actions p-0 pt-2 m--margin-top-10">
        <a href="javascript:;" class="btn btn-default btn-sm margin-right-10 add-new-line" id="add-new-line">
            <i class="fa fa-plus"></i>
            <span class="hidden-380">
                <?php echo translate('Add new member line');?>
            </span>
        </a>
    </div>

    <div class="m-form__actions m-form__actions p-0 pt-5 m--margin-top-30">                            
        <div class="row">
            <div class="col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="add_new_members_line_button" type="button">
                        <?php echo translate('Save Changes & Submit') ?>                                
                    </button>
                    &nbsp;&nbsp;
                    <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_add_members_form">
                        <?php echo translate('Cancel') ?>                              
                    </button>
                </span>
            </div>
        </div>
    </div>

<?php echo form_close() ?>
<!--begin::Modal-->
<div class="modal fade" id="add_new_role_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Group Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="add_new_member_role_form"'); ?>
                    <div class="form-group m-form__group row pt-0">
                        <div class="col-lg-12 m-input--air">
                            <label class="form-control-label"><?php echo translate('Group Role Name');?>: <span class="required">*</span></label>
                            <?php echo form_input('name','','class="form-control"');?>
                        </div>
                    </div>
                    <div class="form-group m-form__group row pt-0">
                        <div class="col-lg-12 m-input--air">
                            <label class="form-control-label"><?php echo translate('Description');?></label>
                            <?php
                                $textarea = array(
                                    'name'=>'description',
                                    'id'=>'',
                                    'value'=> '',
                                    'cols'=>40,
                                    'rows'=>4,
                                    'maxlength'=>160,
                                    'class'=>'form-control maxlength',
                                    'placeholder'=>'Group role description'
                                ); 
                            echo form_textarea($textarea); ?>
                        </div>
                    </div>    
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="button" class="btn btn-secondary" id="close_add_role" data-dismiss="modal">
                                <?php echo translate('Close');?>
                            </button>
                            <button type="submit" id="add_new_role_btn" class="btn btn-primary submit modal_submit_form_button">
                                <?php echo translate('Save changes');?>
                            </button>
                        </div>
                    </div>
                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->
<div id="add_new_members_settings" style=" display: none;">
    <table>
        <tbody>
            <tr>
                <td class="number" style="padding: 1.0rem;"> 1. </td>
                <td>
                    <?php echo form_input('names','','class="form-control form-control-sm m-input names m-input--air capitalize " placeholder=""');?>
                </td>
                <td>
                    <?php echo form_input("phones[]",'','class="form-control form-control-sm m-input--air cust_login_phone phones"');?>
                </td>
                <td>
                    <?php echo form_input('email_addresses','','class="form-control form-control-sm m-input email_addresses m-input--air lowercase" placeholder=""');?>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_dropdown('group_role_ids',translate($group_role_options),'','class="form-control m-input m-select2-append group_role_ids m-input--air"');?>
                    </span>
                </td>
                <td>
                    <div class="row">
                        <span class="col-md-6" data-toggle="m-tooltip" title="" data-original-title="Send SMS notification">
                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                <?php echo form_checkbox('send_sms_notification[0]',1,FALSE,' class = "send_sms_notification" '); ?>
                                <span></span>
                            </label>
                        </span>
                        <span class="col-md-6" data-toggle="m-tooltip" title="" data-original-title="Send Email notification">
                            <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                                <?php echo form_checkbox('send_email_notification[0]',1,FALSE,' class = "send_email_notification" '); ?>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </td>
                <td class="text-right">
                    <a href='javascript:;' class="remove-line">
                        <i class="text-danger la la-trash" style="margin-top:15%;"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script src="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/js/intlTelInput.min.js');?>"></script>
<script type="text/javascript">
    $(document).ready(function(){ 
        $(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });

        $(document).on('click','#add-new-line',function(){
            
            var html = $('#add_new_members_settings tbody').html();
            html = html.replace_all('checker','');
            $('#append-place-holder').append(html);
            var number = 1;
            $('.number').each(function(){
                $(this).text(number);
                $(this).parent().find('input.names').attr('name','names['+(number-1)+']');
                $(this).parent().find('input.phones').attr('name','phones['+(number-1)+']');
                $(this).parent().find('input.email_addresses').attr('name','email_addresses['+(number-1)+']');
                $(this).parent().find('select.group_role_ids').attr('name','group_role_ids['+(number-1)+']');
                ++number;
            });
            $('#multiple-entries .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }
            });
            PhoneInputCountry.init();
        });
      

        $(document).on('click','#multiple-entries a.remove-line',function(){
            $(this).parent().parent().remove();
            var number = 1;
            $('.number').each(function(){
                $(this).text(number);
                $(this).parent().find('input.names').attr('name','names['+(number-1)+']');
                $(this).parent().find('input.phones').attr('name','phones['+(number-1)+']');
                $(this).parent().find('input.email_addresses').attr('name','email_addresses['+(number-1)+']');
                $(this).parent().find('select.group_role_ids').attr('name','group_role_ids['+(number-1)+']');
                number++;
            });
        });

        $(document).on('change click','.group_role_ids',function(){
            var group_role_id = $(this).val();
            if(group_role_id == "0"){
                add_new_role_position = parseInt($(this).parent().parent().parent().find('.number').html().replace('.','')) - 1;
                $('#add_new_member_role_form input[name=name]').val('');
                $('#add_new_member_role_form textarea[name=description]').val('');
                $(this).val('').trigger('change');
                $('#add_new_role_hidden').trigger('click');
            }
        });
        
        PhoneInputCountry.init();
        SnippetAddMembersLine.init();
        SnippetCreateGroupRole.init(false,true);
    });
    

</script>
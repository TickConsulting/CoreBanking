<?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state" role="form" id="add_group_account_managers_form"'); ?>
    <div class="table-responsive">
        <table class="table table-condensed account-managers-table">
            <thead>
                <tr>
                    <th width="2%">
                        #
                    </th>
                    <th width="20%">
                        <?php echo translate('First Name');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="20%">
                        <?php echo translate('Last Name');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="20%">
                        <?php echo translate('Phone Number');?>
                        <span class='required'>*</span>
                    </th>
                    <th width="25%">
                        <?php echo translate('Email Address');?>
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
                        <?php echo form_input('first_names[0]','',' class="form-control m-input--air input-sm first_name text-right" ');?>
                    </td>
                    <td>
                        <?php echo form_input('last_names[0]','',' class="form-control m-input--air input-sm last_name text-right" ');?>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_input('phones[0]','',' class="form-control m-input--air input-sm phone text-right" ');?>
                         </span>
                    </td>
                    <td>
                        <span class="m-select2-sm m-input--air">
                            <?php echo form_input('emails[0]','',' class="form-control m-input--air input-sm emails text-right" ');?>
                        </span>
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
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn-default btn-sm add-new-line" id="add-new-line">
                <i class="la la-plus"></i><?php echo translate('Add New Group Account Manager Line');?>
            </a>
        </div>
    </div>

    <div class="m-form__actions m-form__actions p-0 pt-5 m--margin-top-30">                            
        <div class="row">
            <div class="col-md-12">
                <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                    <button name="submit" value="1" class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm submit_form_button" id="add_group_account_managers" type="button">
                        <?php echo translate('Add Group Account Managers');?>                            
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
                    <?php echo form_input('first_names[0]','',' class="form-control m-input--air input-sm first_name text-right" ');?>
                </td>
                <td>
                    <?php echo form_input('last_names[0]','',' class="form-control m-input--air input-sm last_name text-right" ');?>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_input('phones[0]','',' class="form-control m-input--air input-sm phone text-right" ');?>
                     </span>
                </td>
                <td>
                    <span class="m-select2-sm m-input--air">
                        <?php echo form_input('emails[0]','',' class="form-control m-input--air input-sm emails text-right" ');?>
                    </span>
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

<script>

    $(document).ready(function(){

        $('.add-new-line').on('click',function(){
            var html = $('#append-new-line tbody').html();
            html = html.replace_all('checker','');
            $('#append-place-holder').append(html);
            $('.tooltips').tooltip();
            $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                $(this).parent().find('.first_names').attr('name','first_names['+(number-1)+']');
                $(this).parent().find('.last_names').attr('name','last_names['+(number-1)+']');
                $(this).parent().find('.phones').attr('name','phones['+(number-1)+']');
                $(this).parent().find('.emails').attr('name','emails['+(number-1)+']');
                $(this).parent().find('.send_sms_notification').attr('name','send_sms_notifications['+(number-1)+']');
                $(this).parent().find('.send_email_notification').attr('name','send_email_notifications['+(number-1)+']');
                number++;
            });

            $('.account-managers-table .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
            });
        });

        $(document).on('click','.remove-line',function(event){
            $(this).parent().parent().remove();
            var number = 1;
            $('.count').each(function(){
                $(this).text(number);
                number++;
            });
        });

        SnippetAddGroupAccountManagers.init();

    });

</script>
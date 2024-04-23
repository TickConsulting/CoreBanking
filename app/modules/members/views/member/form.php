<link rel="stylesheet" href="<?php echo site_url('templates/admin_themes/admin/intl-tel-input/css/intlTelInput.min.css');?>">
<div id="general_user_details">
    <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="edit_members_line"'); ?>
        <fieldset class="m--margin-top-10 ">                                
            <?php echo form_hidden('id',$post->id); ?>
            <legend class="m-3 p-2"><?php echo translate('Personal Details');?></legend>

            <div class="form-group m-form__group row pt-0 m--padding-10">
                <div class="col-sm-4 m-form__group-sub m-input--air">
                    <label><?php echo translate('First Name');?><span class="required">*</span></label>
                    <?php echo form_input('first_name',$this->input->post('first_name')?$this->input->post('first_name'):$post->first_name,'class="form-control name m-input--air" placeholder="First Name"'); ?>
                </div>
                <div class="col-sm-4 m-form__group-sub m-input--air">
                    <label><?php echo translate('Middle Name');?></label>
                    <?php echo form_input('middle_name',$this->input->post('middle_name')?$this->input->post('middle_name'):$post->middle_name,'class="form-control name m-input--air" placeholder="Middle Name"'); ?>
                     
                </div>
                <div class="col-sm-4 m-form__group-sub m-input--air">
                    <label><?php echo translate('Last Name');?><span class="required">*</span></label>
                    <?php echo form_input('last_name',$this->input->post('last_name')?$this->input->post('last_name'):$post->last_name,'class="form-control name m-input--air" placeholder="Last Name"'); ?>
                     
                </div>
            </div>
            <div class="form-group m-form__group row pt-0 m--padding-10">
                <div class="col-sm-6 m-form__group-sub m-input--air">
                    <label><?php echo translate('Phone Number');?><span class="required">*</span></label>
                    <?php echo form_input('phone',$this->input->post('phone')?$this->input->post('phone'):$post->phone,'class="form-control phone m-input--air" placeholder="Phone"'); ?>
                     
                </div>
                <div class="col-sm-6 m-form__group-sub m-input--air">
                    <label><?php echo translate('Email Address');?><span class="required">*</span></label>

                    <?php echo form_input('email',$this->input->post('email')?$this->input->post('email'):$post->email,'class="form-control m-input--air" placeholder="Email Address"'); ?>
                </div>
            </div>

            <div class="form-group m-form__group row pt-0 m--padding-10">
                <div class="col-sm-6 m-form__group-sub m-input--air">
                    <label><?php echo translate('Date of Birth');?></label>
                    <?php echo form_input('date_of_birth',$this->input->post('date_of_birth')?timestamp_to_datepicker(strtotime($this->input->post('date_of_birth'))):timestamp_to_datepicker(time()),'class="form-control m-input--air date-picker" readonly');?>
                </div>

                <div class="col-sm-6 m-form__group-sub m-input--air">
                    <label><?php echo translate('ID Number');?></label>
                    <?php echo form_input('id_number',$this->input->post('id_number')?$this->input->post('id_number'):$post->id_number,'class="form-control id_number m-input--air" placeholder="ID Number"'); ?>
                    <?php echo form_hidden('user_id',$post->user_id); ?>
                </div>
            </div>
        </fieldset>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <fieldset class="m--margin-top-10 ">
            <legend class="m-3 p-2"><?php echo translate('Additional Applicant Details
');?></legend>
            <div class="form-group m-form__group row pt-0 m--padding-10">

                <div class="col-sm-6 m-form__group-sub m-input--air">
                    <label><?php echo translate('Postal Address');?></label>
                    <?php 
                        $textarea = array(
                            'name'=>'postal_address',
                            'id'=>'',
                            'value'=> $this->input->post('postal_address')?$this->input->post('postal_address'):$post->postal_address,
                            'cols'=>40,
                            'rows'=>5,
                            'maxlength'=>200,
                            'class'=>'form-control',
                            'placeholder'=>'Postal Address'
                        ); 
                        echo form_textarea($textarea); 
                    ?>
                     
                </div>

                <div class="col-sm-6 m-form__group-sub m-input--air">
                    <label><?php echo translate('Physical Address');?></label>
                    <?php
                        $textarea = array(
                            'name'=>'physical_address',
                            'id'=>'',
                            'value'=> $this->input->post('physical_address')?$this->input->post('physical_address'):$post->physical_address,
                            'cols'=>40,
                            'rows'=>5,
                            'maxlength'=>200,
                            'class'=>'form-control',
                            'placeholder'=>'Physical Address'
                        ); 
                        echo form_textarea($textarea); 
                    ?>                                         
                </div>
            </div>
        </fieldset>
        <div class="m-form__seperator m-form__seperator--dashed m-form__seperator--space-2x"></div>
        <fieldset class="m--margin-top-10 ">
            <legend class="m-3 p-2"><?php echo translate('Next of Kin Details');?></legend>
            <div class="form-group m-form__group row pt-0 m--padding-10">
                <div class="col-sm-12 m-form__group-sub m-input--air">
                    <div style="background:#fff!important;border: none;display: none;" class="no_records">
                        <div class="alert m-alert--outline alert-metal text-center">
                            <h4 class="block">Information! No records to display</h4>
                            <p>
                                You do not have any next of kin registered
                            </p>
                        </div>
                    </div>
                    <table  class="table table-checkable member-table table-condensed next_of_kin_table" id="multiple-entries" style="display: none;">
                        <thead>
                            <tr>
                                <th width='2%'>#</th>
                                <th width='18%'><?php echo translate('Full Names')?> <span class='required'>*</span></th>
                                <th width='16%'><?php echo translate('ID Number')?> <span class='required'>*</span></th>
                                <th width='16%'><?php echo translate('Phone') ?><span class='required'>*</span></th>
                                <th width='15%'><?php echo translate('Email')?></th>
                                <th width='16%'><?php echo translate('Relationship')?><span class='required'>*</span></th>
                                <th class='text-right' width='14%'><?php echo translate('Allocation')?> <span class='required'>*</span></th>                                            </tr>
                        </thead>
                        <tbody id="append-place-holder">
                            <?php
                                $i = 0;
                                foreach ($next_of_kin_entries as $next_of_kin): ?>
                                    <tr>
                                        <td class="number" style="padding: 1.0rem;"> <?php echo $i+1;?> </td>
                                        <td>
                                            <?php echo form_input('full_names['.$i.']',$next_of_kin->full_name,'class="form-control form-control-sm m-input full_names m-input--air capitalize " placeholder=""');?>
                                        </td>
                                         <td>
                                            <?php echo form_input('id_numbers['.$i.']',$next_of_kin->id_number,'class="form-control form-control-sm m-input--air  id_numbers"');?>
                                        </td>
                                        <td>
                                            <?php echo form_input('next_of_kin_phones['.$i.']',$next_of_kin->phone,'class="form-control form-control-sm m-input--air cust_login_phone next_of_kin_phones"');?>
                                        </td>
                                        <td>
                                            <?php echo form_input('next_of_kin_emails['.$i.']',$next_of_kin->email,'class="form-control form-control-sm m-input next_of_kin_emails m-input--air lowercase" placeholder=""');?>
                                        </td>
                                        <td>
                                            <?php echo form_input('relationships['.$i.']',$next_of_kin->relationship,'class="form-control form-control-sm m-input relationships m-input--air lowercase" placeholder=""');?>
                                        </td>
                                        <td>
                                            <?php echo form_input('allocations['.$i.']',$next_of_kin->allocation,'class="form-control form-control-sm m-input allocations m-input--air lowercase" placeholder=""');?>
                                        </td>
                                        <td style="padding: 1.0rem;">
                                            <a href='javascript:;' class="remove-line">
                                                <i class="text-danger la la-trash" style="margin-top:15%;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php  $i++; endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="m-form__actions m-form__actions pt-0 pb-0">
                <a href="javascript:;" class="btn btn-default btn-sm margin-right-10 add-new-line" id="add-new-line">
                    <i class="fa fa-plus"></i>
                    <span class="hidden-380">
                        <?php echo translate('Add next of kin');?>
                    </span>
                </a>
            </div>
        </fieldset>
        <div class="m-form__actions m-form__actions">                            
            <div class="row">
                <div class="col-md-12">
                    <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="edit_members_form" type="button">
                            Save Changes                                
                        </button>
                        &nbsp;&nbsp;
                        <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_edit_members_form">
                            Cancel                              
                        </button>
                    </span>
                </div>
            </div>
        </div>

    </div>

    <?php echo form_close() ?>
</div>
<div id="add_new_next_of_kin_settings" style=" display: none;">
    <table>
        <tbody>
            <tr>
                <td class="number" style="padding: 1.0rem;"> 1. </td>
                <td>
                    <?php echo form_input('full_names','','class="form-control form-control-sm m-input full_names m-input--air capitalize " placeholder=""');?>
                </td>
                 <td>
                    <?php echo form_input("id_numbers[]",'','class="form-control form-control-sm m-input--air  id_numbers"');?>
                </td>
                <td>
                    <?php echo form_input("next_of_kin_phones[]",'','class="form-control form-control-sm m-input--air cust_login_phone next_of_kin_phones"');?> 
                </td>
                <td>
                    <?php echo form_input('next_of_kin_emails','','class="form-control form-control-sm m-input next_of_kin_emails m-input--air lowercase" placeholder=""');?>
                </td>
                <td>
                    <?php echo form_input('relationships','','class="form-control form-control-sm m-input relationships m-input--air lowercase" placeholder=""');?>
                </td>
                <td>
                    <?php echo form_input('allocations','','class="form-control form-control-sm m-input allocations m-input--air lowercase" placeholder=""');?>
                </td>
                <td style="padding: 1.0rem;">
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
        <?php if(!empty($posts)||!empty($next_of_kin_entries)){ ?>
            $('.next_of_kin_table').slideDown();
            $('.no_records').slideUp();
        <?php }else{ ?>
            $('.next_of_kin_table').slideUp();
            $('.no_records').slideDown();
        <?php  } ?>

        $('#add-new-line').on('click',function(){
            if($('#next_of_kin_table').is(':visible')){
                append_new_line();
            }else{
                $('.next_of_kin_table').slideDown();
                var number = 1;
                $('.count').each(function(){
                    number++;
                });
                if(number==3){

                }else{
                    append_new_line();
                }
                $('.no_records').hide();
            }
        });
        SnippetEditMemberLine.init();
        PhoneInputCountry.init();
        $(document).on('change click','#group_role',function(){
            var group_role_id = $(this).val();
            if(group_role_id == "0"){
                //add_new_role_position = parseInt($(this).parent().parent().parent().find('.number').html().replace('.','')) - 1;
                $('#add_new_member_role_form input[name=name]').val('');
                $('#add_new_member_role_form textarea[name=description]').val('');
                $('#add_new_role_hidden').trigger('click');
            }
        });

        $(document).on('click','.next_of_kin_table a.remove-line',function(){
            $(this).parent().parent().remove();
            var number = 1;
            $('.number').each(function(){
                $(this).text(number);
                $(this).parent().find('input.full_names').attr('name','full_names['+(number-1)+']');
                $(this).parent().find('input.id_numbers').attr('name','id_numbers['+(number-1)+']');
                $(this).parent().find('input.next_of_kin_phones').attr('name','next_of_kin_phones['+(number-1)+']');
                $(this).parent().find('input.next_of_kin_emails').attr('name','next_of_kin_emails['+(number-1)+']');
                $(this).parent().find('input.relationships').attr('name','relationships['+(number-1)+']');
                $(this).parent().find('input.allocations').attr('name','allocations['+(number-1)+']');
                number++;
            });
            $('#multiple-entries .m-select2-append').select2({
                placeholder:{
                    id: '-1',
                    text: "--Select option--",
                }, 
                allowClear: !0
            });
            if($('.number').length == 1){
                $('.next_of_kin_table').hide();
                $('.no_records').show();
            }
            ///PhoneInputCountry.init();
        });
    });

    function append_new_line(){
        var html = $('#add_new_next_of_kin_settings tbody').html();
        html = html.replace_all('checker','');
        $('#append-place-holder').append(html);
        var number = 1;
        $('.number').each(function(){
            $(this).text(number);
            $(this).parent().find('input.full_names').attr('name','full_names['+(number-1)+']');
            $(this).parent().find('input.id_numbers').attr('name','id_numbers['+(number-1)+']');
            $(this).parent().find('input.next_of_kin_phones').attr('name','next_of_kin_phones['+(number-1)+']');
            $(this).parent().find('input.next_of_kin_emails').attr('name','next_of_kin_emails['+(number-1)+']');
            $(this).parent().find('input.relationships').attr('name','relationships['+(number-1)+']');
            $(this).parent().find('input.allocations').attr('name','allocations['+(number-1)+']');
            ++number;
        });
        $('#multiple-entries .m-select2-append').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            allowClear: !0
        });
        PhoneInputCountry.init();       
        
    }
    
</script>
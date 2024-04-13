<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                   <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">
                <div class='margin-top-20'>
                    <?php echo form_open($this->uri->uri_string(),'class="form_submit" role="form"'); ?>
                        <table class="table table-striped table-condensed table-hover table-multiple-items table-layout-fixed" id="">
                            <thead>
                                <tr>
                                    <th width='2%'>#</th>
                                    <th width='12%'>Date <span class='required'>*</span></th>
                                    <th width='17%'>Group <span class='required'>*</span></th>
                                    <th width='21%'>Package<span class='required'>*</span></th>
                                    <th width='15%'>Payment method <span class='required'>*</span></th>
                                    <th width='15%'>Transaction Code<span class='required'>*</span></th>
                                    <th class='text-right' width='14%'>Amount (<?php echo $this->default_country->currency_code; ?>) <span class='required'>*</span></th>
                                    <th width='4%'></th>
                                </tr>
                            </thead>
                            <tbody id='append-place-holder'>
                                <?php 
                                    if(!empty($posts)){
                                        if(isset($posts['payment_dates'])):
                                            $count = count($posts['payment_dates']);
                                            for($i=0;$i<=$count;$i++):
                                                if(isset($posts['payment_dates'][$i])&&isset($posts['groups'][$i])&&isset($posts['billing_packages'][$i])&&isset($posts['payment_methods'][$i])&&isset($posts['amounts'][$i])):    
                                ?>
                                    <tr>
                                        <td class='count'><?php echo $i+1; ?></td>
                                        <td <?php if($errors['payment_dates'][$i]){ echo " class='has-error payment-dates' "; } ?> >
                                            <?php if($errors['payment_dates'][$i]){ ?> 
                                            <div class="input-icon tooltips right" data-original-title="<?php echo $error_messages['payment_dates'][$i]; ?>" data-container="body"><i class="fa fa-exclamation " ></i><?php } ?>
                                                <?php echo form_input('payment_dates['.$i.']',$posts['payment_dates'][$i],' class="form-control payment_dates date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" ');?>
                                            <?php if($errors['payment_dates'][$i]){ ?> </div> <?php } ?>
                                        </td>
                                        <td <?php if($errors['groups'][$i]){ echo " class='has-error groups' "; } ?>>
                                            <?php if($errors['groups'][$i]){ ?> 
                                            <div class="input-icon tooltips right" data-original-title="<?php echo $error_messages['groups'][$i]; ?>" data-container="body"><i class="fa fa-exclamation " ></i><?php } ?> 
                                                <?php echo form_dropdown('groups['.$i.']',array(''=>'--Select group--')+$groups,$posts['groups'][$i],' class="form-control select2  group" ');?>
                                            <?php if($errors['groups'][$i]){ ?> </div><?php } ?>
                                            <?php if($i==0){?>
                                                <?php echo form_hidden('billing_invoices_id',$this->input->post('billing_invoices_id'),'class="billing_invoice"');?>
                                            <?php }?>
                                        </td>
                                        <td <?php if($errors['billing_packages'][$i]){ echo " class='has-error billing_packages' "; } ?>>
                                            <?php if($errors['billing_packages'][$i]){ ?> 
                                            <div class="input-icon tooltips right" data-original-title="<?php echo $error_messages['billing_packages'][$i]; ?>" data-container="body"><i class="fa fa-exclamation " ></i><?php } ?> 
                                                <?php echo form_dropdown('billing_packages['.$i.']',array(''=>'--Select billing package--')+$billing_packages,$posts['billing_packages'][$i],' class="form-control select2  billing package" ');?>
                                            <?php if($errors['billing_packages'][$i]){ ?> </div> <?php } ?>
                                        </td>
                                        <td <?php if($errors['payment_methods'][$i]){ echo " class='has-error payment_methods' "; } ?>>
                                            <?php if($errors['payment_methods'][$i]){ ?> 
                                            <div class="input-icon tooltips right" data-original-title="<?php echo $error_messages['payment_methods'][$i]; ?>" data-container="body"><i class="fa fa-exclamation " ></i><?php } ?> 
                                                <?php echo form_dropdown('payment_methods['.$i.']',array(''=>'--Select payment method--')+$payment_methods,$posts['payment_methods'][$i],' class="form-control select2  payment_method" ');?>
                                            <?php if($errors['payment_methods'][$i]){ ?> </div> <?php } ?>
                                            <a href="javascript:;" class="btn btn-default btn-xs inline-table-button add_payment_description" id="">
                                                <i class="fa fa-plus"></i>
                                                <span class="hidden-380">
                                                    Add description
                                                </span>
                                            </a>
                                            <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                                                <?php 

                                                    $textarea = array(
                                                        'name'=>'payment_descriptions['.$i.']',
                                                        'id'=>'',
                                                        'value'=> $posts['payment_descriptions'][$i],
                                                        'cols'=>25,
                                                        'rows'=>5,
                                                        'maxlength'=>'',
                                                        'class'=>'form-control payment_description',
                                                        'placeholder'=>''
                                                    ); 
                                                    echo form_textarea($textarea);

                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-icon tooltips right" data-container="body">
                                                <?php echo form_input('transaction_codes['.$i.']',$posts['transaction_codes'][$i],' class="form-control transaction_code" placeholder="Transaction Code" ');?>
                                            </div>
                                        </td>
                                        <td <?php if($errors['amounts'][$i]){ echo " class='has-error amounts' "; } ?> >
                                            <?php if($errors['amounts'][$i]){ ?> 
                                            <div class="input-icon tooltips right" data-original-title="<?php echo $error_messages['amounts'][$i]; ?>" data-container="body"><i class="fa fa-exclamation " ></i><?php } ?>
                                                <?php echo form_input('amounts['.$i.']',$posts['amounts'][$i],' class="form-control currency amount" ');?>
                                            <?php if($errors['amounts'][$i]){ ?> </div> <?php } ?>
                                        </td>
                                        <td>
                                            <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php
                                                endif;
                                            endfor;
                                        endif;
                                    }else{
                                ?>
                                    <tr>
                                        <td class='count'>1</td>
                                        <td><div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i><?php echo form_input('payment_dates[0]','',' class="form-control payment_date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" ');?></div></td>
                                        <td>
                                            <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                <?php echo form_dropdown('groups[0]',array(''=>'--Select Group--')+$groups,$this->input->get('group_id'),' class="form-control select2 group" ');?>
                                                
                                            </div>
                                            <?php echo form_hidden('billing_invoices_id',$this->input->get('billing_invoice_id'),'class="billing_invoice"');?>
                                        </td>
                                        <td>
                                            <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                <?php echo form_dropdown('billing_packages[0]',array(''=>'--Select Package--')+$billing_packages,$this->input->get('billing_package_id'),' class="form-control select2 billing_package" ');?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                                                <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                    <?php echo form_dropdown('payment_methods[0]',array(''=>'--Select payment method--')+$payment_methods,'',' class="form-control select2 payment_method" ');?>
                                                </div>
                                                <a href="javascript:;" class="btn btn-default btn-xs inline-table-button add_payment_description" id="">
                                                    <i class="fa fa-plus"></i>
                                                    <span class="hidden-380">
                                                        Add description
                                                    </span>
                                                </a>
                                                <?php 

                                                    $textarea = array(
                                                        'name'=>'payment_descriptions[0]',
                                                        'id'=>'',
                                                        'value'=> '',
                                                        'cols'=>25,
                                                        'rows'=>5,
                                                        'maxlength'=>'',
                                                        'class'=>'form-control payment_description',
                                                        'placeholder'=>''
                                                    ); 
                                                    echo form_textarea($textarea);

                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                <?php echo form_input('transaction_codes[0]','',' class="form-control transaction_code" placeholder="Transaction code"');?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                <?php echo form_input('amounts[0]','',' class="form-control amount currency" ');?>
                                            </div>
                                        </td>
                                        <td>
                                            <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    } 
                                ?>
                            </tbody>
                        </table>

                         <div class="row">
                            <div class='col-md-12 margin-bottom-10 text-left'>
                                <a href="javascript:;" class="btn margin-right-10 btn-default btn-xs" id="add-new-line">
                                    <i class="fa fa-plus"></i>
                                    <span class="hidden-380">
                                        Add new payment line
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="submit" value="1" class="btn blue submit_form_button">Record Billing Payments</button>
                            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                            <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                        </div>
                    <?php echo form_close(); ?>
                </div>
                <div id='append-new-line'>
                    <table>
                        <tbody>
                            <tr>
                                <td class='count'>1</td>
                                <td><div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i><?php echo form_input('payment_dates[0]','',' class="form-control payment_date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" ');?></div></td>
                                <td>
                                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                        <?php echo form_dropdown('groups[0]',array(''=>'--Select group--')+$groups,'',' class="form-control append_select2 group" ');?>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                        <?php echo form_dropdown('billing_packages[0]',array(''=>'--Select package--')+$billing_packages,'',' class="form-control append_select2 billing_package" ');?>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                        <?php echo form_dropdown('payment_methods[0]',array(''=>'--Select payment method--')+$payment_methods,'',' class="form-control append_select2 payment_method" ');?>
                                    </div>

                                    <a href="javascript:;" class="btn btn-default btn-xs inline-table-button add_payment_description" id="">
                                        <i class="fa fa-plus"></i>
                                        <span class="hidden-380">
                                            Add description
                                        </span>
                                    </a>
                                    <div class="margin-top-5" data-original-title="" data-container="body"><i class="" ></i>
                                        <?php 

                                            $textarea = array(
                                                'name'=>'payment_descriptions[0]',
                                                'id'=>'',
                                                'value'=> '',
                                                'cols'=>25,
                                                'rows'=>5,
                                                'maxlength'=>'',
                                                'class'=>'form-control payment_description',
                                                'placeholder'=>''
                                            ); 
                                            echo form_textarea($textarea);

                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                        <?php echo form_input('transaction_codes[0]','',' class="form-control transaction_code" placeholder="Transaction code"');?>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                        <?php echo form_input('amounts[0]','',' class="form-control amount currency" ');?>
                                    </div>
                                </td>
                                <td>
                                    <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
   
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $(document).on('click','.add_payment_description',function(){
        $(this).parent().find('.payment_description').toggle();
    });
    $('#add-new-line').on('click',function(){
        var html = $('#append-new-line tbody').html();
        html = html.replace_all('checker','');
        $('#append-place-holder').append(html);
        $('input[type=checkbox]').uniform();
        $('.tooltips').tooltip();
        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
        var number = 1;
        $('.count').each(function(){
            $(this).text(number);
            $(this).parent().find('.payment_date').attr('name','payment_dates['+(number-1)+']');
            $(this).parent().find('.group').attr('name','groups['+(number-1)+']');
            $(this).parent().find('.billing_package').attr('name','billing_packages['+(number-1)+']');
            $(this).parent().find('.payment_description').attr('name','payment_descriptions['+(number-1)+']');
            $(this).parent().find('.payment_method').attr('name','payment_methods['+(number-1)+']');
            $(this).parent().find('.transaction_code').attr('name','transaction_codes['+(number-1)+']');
            $(this).parent().find('.amount').attr('name','amounts['+(number-1)+']');
            number++;
        });
        $('.table-multiple-items .append_select2').select2();
        FormInputMask.init();
    });
    $('.table-multiple-items').on('click','a.remove-line',function(event){
        $(this).parent().parent().remove();
        var number = 1;
        $('.count').each(function(){
            $(this).text(number);
            number++;
        });
    });
    $('.date-picker').datepicker({dateFormat: 'dd-mm-yy' ,autoclose: true}).on('changeDate', function(e) {
        // `e` here contains the extra attributes
        if($(this).val()==''){
            $(this).parent().parent().addClass('has-error');
            $(this).parent().prepend('<i class="fa fa-exclamation "></i>');
            $(this).parent().parent().find('.tooltips').attr('data-original-title','Please enter a payment date');
        }else{
            $(this).parent().parent().removeClass('has-error');
            $(this).parent().find('i').remove();
            $(this).parent().parent().find('.tooltips').attr('data-original-title','');
        }
    });
    $('.groups select.group').on('change',function(){
        if($(this).val()==''){
            $(this).parent().parent().addClass('has-error');
            $(this).parent().prepend('<i class="fa fa-exclamation "></i>');
            $(this).parent().parent().find('.tooltips').attr('data-original-title','Please select a group');
        }else{
            $(this).parent().parent().removeClass('has-error');
            $(this).parent().find('i').remove();
            $(this).parent().parent().find('.tooltips').attr('data-original-title','');
        }
    });
    $('.billing_packages select.billing_package').on('change',function(){
        if($(this).val()==''){
            $(this).parent().parent().addClass('has-error');
            $(this).parent().prepend('<i class="fa fa-exclamation "></i>');
            $(this).parent().parent().find('.tooltips').attr('data-original-title','Please select a billing package');
        }else{
            $(this).parent().parent().removeClass('has-error');
            $(this).parent().find('i').remove();
            $(this).parent().parent().find('.tooltips').attr('data-original-title','');
        }
    });
   
    $('.payment_methods select.payment_method').on('change',function(){
        if($(this).val()==''){
            $(this).parent().parent().addClass('has-error');
            $(this).parent().prepend('<i class="fa fa-exclamation "></i>');
            $(this).parent().parent().find('.tooltips').attr('data-original-title','Please select a payment method');
        }else{
            $(this).parent().parent().removeClass('has-error');
            $(this).parent().find('i').remove();
            $(this).parent().parent().find('.tooltips').attr('data-original-title','');
        }
    });
    $('.amounts input.amount').on('blur',function(){
        if($(this).val()==''){
            $(this).parent().parent().addClass('has-error');
            $(this).parent().prepend('<i class="fa fa-exclamation "></i>');
            $(this).parent().parent().find('.tooltips').attr('data-original-title','Please enter an amount');
        }else{
            var amount = $(this).val();
            regex = /^[0-9.,\b]+$/;;
            if(regex.test(amount)){
                $(this).parent().parent().removeClass('has-error');
                $(this).parent().find('i').remove();
                $(this).parent().parent().find('.tooltips').attr('data-original-title','');
            }else{ 
                $(this).parent().parent().addClass('has-error');
                $(this).parent().prepend('<i class="fa fa-exclamation "></i>');
                $(this).parent().parent().find('.tooltips').attr('data-original-title','Please enter a valid amount');
            }
        }
    });

});

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

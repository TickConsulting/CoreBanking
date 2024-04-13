<?php
    $commission_type = $partner_commission_type?$partner_commission_type->commission_type:"";
?>
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
                <?php echo form_open($this->uri->uri_string(), ' id="form"  class="form_submit"'); ?> 
                    <div class="form-group">
                        <label>Commission Type<span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon">
                            <i class="fa fa-users"></i>
                            </span>
                            <?php echo form_dropdown('commission_type',array(""=>"Select Commission Type")+$commission_type_options,$this->input->post('commission_type')?$this->input->post('commission_type'):$partner_commission_type?$partner_commission_type->commission_type:"",'class="form-control select2" id="commission_type" '); ?>
                        </div>
                    </div>
                    <div id="percentage_commission" class="form-group">
                        <label>Percentage Commission Matrix<span class="required">*</span></label>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-condensed table-hover table-multiple-items" id="">
                                    <thead>
                                        <tr>
                                            <th width='8px'>#</th>
                                            <th width='65%'>Group Number Range <span class='required'>*</span></th>
                                            <th width='30%'>Percentage Commission <span class='required'>*</span></th>
                                            <th width='8px'></th>
                                        </tr>
                                    </thead>
                                    <tbody id='append-percentage-place-holder'>
                                        <?php 
                                        if(isset($posts)&&$this->input->post('commission_type')==1){  
                                            $count = 1;
                                            $row_count = count($posts['percentage_maximum_group_numbers']);
                                                for($i = 0; $i < $row_count ; $i++):
                                            ?>
                                            <tr>
                                                <td class="count">
                                                    <?php echo $count; ?>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                            <?php echo form_input('percentage_minimum_group_numbers['.$i.']',$posts['percentage_minimum_group_numbers'][$i],' class="form-control percentage_minimum_group_number text-center numeric" placeholder="Enter Minimum Group Number"  readonly="readonly" '); ?>
                                                        </div>
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-arrows-h"></i>
                                                        </span>
                                                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                            <?php echo form_input('percentage_maximum_group_numbers['.$i.']',$posts['percentage_maximum_group_numbers'][$i],' class="form-control percentage_maximum_group_number text-center numeric last" placeholder="Enter Maximum Group Number" '); ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php echo form_input('percentages['.$i.']',$posts['percentages'][$i],' class="form-control percentage text-center " placeholder="Enter Percentage Commission" '); ?>                                    
                                                </td>
                                                <td>
                                                    <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php 
                                                $count++;
                                            endfor;
                                        }else if($partner_commission_matrices){
                                            $count = 1;
                                            $i = 0;
                                            foreach($partner_commission_matrices as $partner_commission_matrix):
                                        ?>
                                            <tr>
                                                <td class="count"><?php echo $count; ?></td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                            <?php echo form_input('percentage_minimum_group_numbers['.$i.']',$partner_commission_matrix->minimum_group_number,' class="form-control percentage_minimum_group_numbers text-center numeric" placeholder="Enter Minimum Group Number"  readonly="readonly" '); ?>
                                                        </div>
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-arrows-h"></i>
                                                        </span>
                                                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                            <?php echo form_input('percentage_maximum_group_numbers['.$i.']',$partner_commission_matrix->maximum_group_number,' class="form-control percentage_maximum_group_number text-center numeric" placeholder="Enter Maximum Group Number" '); ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php echo form_input('percentages['.$i.']',$partner_commission_matrix->percentage,' class="form-control percentage text-center " placeholder="Enter Percentage Commission" '); ?>                                    
                                                </td>
                                                <td>
                                                    <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php
                                                $i++;
                                                $count++;
                                            endforeach;
                                        }else{ 
                                        ?>
                                            <tr>
                                                <td class="count">1</td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                            <?php echo form_input('percentage_minimum_group_numbers[0]','1',' class="form-control percentage_minimum_group_numbers text-center numeric" placeholder="Enter Minimum Group Number"  readonly="readonly" '); ?>
                                                        </div>
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-arrows-h"></i>
                                                        </span>
                                                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                            <?php echo form_input('percentage_maximum_group_numbers[0]','',' class="form-control percentage_maximum_group_number text-center numeric" placeholder="Enter Maximum Group Number" '); ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php echo form_input('percentages[0]','',' class="form-control percentage text-center " placeholder="Enter Percentage Commission" '); ?>                                    
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
                            </div>
                        </div>
                        <div class="row margin-top-20">
                            <div class='col-md-12 margin-bottom-10 text-left'>
                                <a href="javascript:;" class="btn btn-default btn-xs" id="add-new-percentage-line">
                                    <i class="fa fa-plus"></i>
                                    <span class="hidden-380">
                                        Add new line
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="fixed_commission" class="form-group">
                        <label>Fixed Commission Matrix<span class="required">*</span></label>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered table-condensed table-hover table-multiple-items" id="">
                                    <thead>
                                        <tr>
                                            <th width='8px'>#</th>
                                            <th width='65%'>Group Number Range <span class='required'>*</span></th>
                                            <th width='30%'>Fixed Commission Amount <span class='required'>*</span></th>
                                            <th width='8px'></th>
                                        </tr>
                                    </thead>
                                    <tbody id='append-fixed-place-holder'>
                                        <?php 
                                        if(isset($posts)&&$this->input->post('commission_type')==2){  
                                            $count = 1;
                                            $row_count = count($posts['minimum_group_numbers']);
                                                for($i = 0; $i < $row_count ; $i++):
                                        ?>
                                        <tr>
                                            <td class="count"><?php echo $count; ?></td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                        <?php echo form_input('minimum_group_numbers['.$i.']',$posts['minimum_group_numbers'][$i],' class="form-control minimum_group_number text-center numeric" placeholder="Enter Minimum Group Number"  readonly="readonly" '); ?>
                                                    </div>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-arrows-h"></i>
                                                    </span>
                                                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                        <?php echo form_input('maximum_group_numbers['.$i.']',$posts['maximum_group_numbers'][$i],' class="form-control maximum_group_number text-center numeric last" placeholder="Enter Maximum Group Number" '); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo form_input('fixed_amounts['.$i.']',$posts['fixed_amounts'][$i],' class="form-control currency text-center " placeholder="Enter Fixed Commission Amount" '); ?>                                    
                                            </td>
                                            <td>
                                                <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                                    $count++;
                                                endfor;
                                            }else if($partner_commission_matrices){
                            
                                            $count = 1;
                                            $i = 0;
                                            foreach($partner_commission_matrices as $partner_commission_matrix):
                                        ?>
                                            <tr>
                                                <td class="count"><?php echo $count; ?></td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                            <?php echo form_input('percentage_minimum_group_numbers['.$i.']',$partner_commission_matrix->minimum_group_number,' class="form-control percentage_minimum_group_numbers text-center numeric" placeholder="Enter Minimum Group Number"  readonly="readonly" '); ?>
                                                        </div>
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-arrows-h"></i>
                                                        </span>
                                                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                            <?php echo form_input('percentage_maximum_group_numbers['.$i.']',$partner_commission_matrix->maximum_group_number,' class="form-control percentage_maximum_group_number text-center numeric" placeholder="Enter Maximum Group Number" '); ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                     <?php echo form_input('fixed_amounts['.$i.']',number_to_currency($partner_commission_matrix->fixed_amount),' class="form-control currency text-center " placeholder="Enter Fixed Commission Amount" '); ?>                                    
                                                </td>
                                                <td>
                                                    <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php
                                                $i++;
                                                $count++;
                                            endforeach;
                                            }else{
                                        ?>
                                        <tr>
                                            <td class="count">1</td>
                                            <td>
                                                <div class="input-group">
                                                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                        <?php echo form_input('minimum_group_numbers[0]','1',' class="form-control minimum_group_number text-center numeric" placeholder="Enter Minimum Group Number"  readonly="readonly" '); ?>
                                                    </div>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-arrows-h"></i>
                                                    </span>
                                                    <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>
                                                        <?php echo form_input('maximum_group_numbers[0]','',' class="form-control maximum_group_number text-center numeric last" placeholder="Enter Maximum Group Number" '); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo form_input('fixed_amounts[0]','',' class="form-control currency text-center " placeholder="Enter Fixed Commission Amount" '); ?>                                    
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
                            </div>
                        </div>
                        <div class="row margin-top-20">
                            <div class='col-md-12 margin-bottom-10 text-left'>
                                <a href="javascript:;" class="btn btn-default btn-xs" id="add-new-fixed-line">
                                    <i class="fa fa-plus"></i>
                                    <span class="hidden-380">
                                        Add new line
                                    </span>
                                </a>
                            </div>
                        </div>                   
                    </div>

                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<div id='append-new-percentage-line'>
    <table>
        <tbody>
            <tr>
                <td class="count">1</td>
                <td>
                    <div class="input-group">
                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>                         
                            <?php echo form_input('percentage_minimum_group_numbers[]','',' class="form-control percentage_minimum_group_number text-center numeric" placeholder="Enter Minimum Group Number" readonly="readonly" '); ?>
                        </div>
                        <span class="input-group-addon">
                            <i class="fa fa-arrows-h"></i>
                        </span>
                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>                         
                            <?php echo form_input('percentage_maximum_group_numbers[]','',' class="form-control percentage_maximum_group_number text-center numeric last" placeholder="Enter Maximum Group Number" '); ?>
                        </div>
                    </div>
                </td>
                <td>
                    <?php echo form_input('percentages[]','',' class="form-control percentage text-center " placeholder="Enter Percentage Commission" '); ?>                                    
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
<div id='append-new-fixed-line'>
    <table>
        <tbody>
            <tr>
                <td class="count">1</td>
                <td>
                    <div class="input-group">
                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>                         
                            <?php echo form_input('minimum_group_numbers[]','',' class="form-control minimum_group_number text-center numeric" placeholder="Enter Minimum Group Number" readonly="readonly" '); ?>
                        </div>
                        <span class="input-group-addon">
                            <i class="fa fa-arrows-h"></i>
                        </span>
                        <div class="input-icon tooltips right" data-original-title="" data-container="body"><i class="" ></i>                         
                            <?php echo form_input('maximum_group_numbers[]','',' class="form-control maximum_group_number text-center numeric last" placeholder="Enter Maximum Group Number" '); ?>
                        </div>
                    </div>
                </td>
                <td>
                    <?php echo form_input('fixed_amounts[]','',' class="form-control fixed_amount currency text-center " placeholder="Enter Fixed Commission Amount" '); ?>                                    
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
<script>
    $(document).ready(function(){
        $('#commission_type').change(function(){
            if($(this).val()==1){
                $('#percentage_commission').slideDown();
                $('#fixed_commission').slideUp();
            }else if($(this).val()==2){
                $('#percentage_commission').slideUp();
                $('#fixed_commission').slideDown();
            }else{
                $('#percentage_commission,#fixed_commission').slideUp();
            }
        });
        
        <?php if($this->input->post('commission_type')==1||$commission_type==1){ ?>
            $('#percentage_commission').slideDown();
            $('#fixed_commission').slideUp();
        <?php }else if($this->input->post('commission_type')==2||$commission_type==2){ ?>
            $('#percentage_commission').slideUp();
            $('#fixed_commission').slideDown();
        <?php }else{ ?>
            $('#percentage_commission,#fixed_commission').slideUp();
        <?php } ?>

        $('#add-new-percentage-line').on('click',function(){
            var html = $('#append-new-percentage-line tbody').html();
            $('#append-percentage-place-holder').append(html);
            $('.tooltips').tooltip();
            update_percentage_count();
            update_minimum_percentage_entries();
        });

        $('#add-new-fixed-line').on('click',function(){
            var html = $('#append-new-fixed-line tbody').html();
            $('#append-fixed-place-holder').append(html);
            $('.tooltips').tooltip();
            update_fixed_count();
            update_minimum_fixed_entries();
        });

        $(document).on('keyup','.percentage_maximum_group_number',function(){
            update_minimum_percentage_entries();
        });

        $(document).on('keyup','.maximum_group_number',function(){
            update_minimum_fixed_entries();
        });

        $('.submit_form_button').click(function(){
            if($('#commission_type').val()==1){
                if(check_percentage_maximum_group_numbers()){
                    $('.form_submit').submit();
                }else{
                    $('.submit_form_button').show();
                    $('.processing_form_button').hide();
                    return false;
                }
            }else if($('#commission_type').val()==2){
                if(check_maximum_group_numbers()){
                    $('.form_submit').submit();
                }else{
                    $('.submit_form_button').show();
                    $('.processing_form_button').hide();
                    return false;
                }
            }else{
                $('.submit_form_button').show();
                $('.processing_form_button').hide();
                return false;
            }
        });
       
    });

    function update_percentage_count(){
        var number = 1;
        $('#percentage_commission .table-multiple-items .count').each(function(){
            $(this).text(number);
            $(this).parent().find('input.percentage_minimum_group_number').attr('name','percentage_minimum_group_numbers['+(number-1)+']');
            $(this).parent().find('input.percentage_maximum_group_number').attr('name','percentage_maximum_group_numbers['+(number-1)+']');
            $(this).parent().find('input.percentage').attr('name','percentages['+(number-1)+']');
            FormInputMask.init();
            number++;
        });
    }

    function update_fixed_count(){
        var number = 1;
        $('#fixed_commission .table-multiple-items .count').each(function(){
            $(this).text(number);
            $(this).parent().find('input.minimum_group_number').attr('name','minimum_group_numbers['+(number-1)+']');
            $(this).parent().find('input.maximum_group_number').attr('name','maximum_group_numbers['+(number-1)+']');
            $(this).parent().find('input.fixed_amount').attr('name','fixed_amounts['+(number-1)+']');
            FormInputMask.init();
            number++;
        });
    }

    function update_minimum_percentage_entries(){
        var number = 1;
        var maximum_group_number = 0;
        var next_minimum_group_number = 0;
        $('#percentage_commission .table-multiple-items .count').each(function(){
            if(maximum_group_number>0){
                next_minimum_group_number = parseInt(maximum_group_number)+1;
            }else{
                next_minimum_group_number = "";
            }
            if(number>1){
                $(this).parent().find('input.percentage_minimum_group_number').val(next_minimum_group_number);
            }
            maximum_group_number = $(this).parent().find('input.percentage_maximum_group_number').val();
            //check_maximum_group_number($(this).parent().find('input.maximum_group_number'));
            number++;
        });
    }

    function update_minimum_fixed_entries(){
        var number = 1;
        var maximum_group_number = 0;
        var next_minimum_group_number = 0;
        $('#fixed_commission .table-multiple-items .count').each(function(){
            if(maximum_group_number>0){
                next_minimum_group_number = parseInt(maximum_group_number)+1;
            }else{
                next_minimum_group_number = "";
            }
            if(number>1){
                $(this).parent().find('input.minimum_group_number').val(next_minimum_group_number);
            }
            maximum_group_number = $(this).parent().find('input.maximum_group_number').val();
            //check_maximum_group_number($(this).parent().find('input.maximum_group_number'));
            number++;
        });
    }

    function check_maximum_group_numbers(){
        var result = true;
        $('.table-multiple-items .count:visible').each(function(){
            if(check_maximum_group_number($(this).parent().find('input.maximum_group_number'))){

            }else{
                result = false;
            }
        });
        return result;
    }

    function check_percentage_maximum_group_numbers(){
        var result = true;
        $('.table-multiple-items .count:visible').each(function(){
            if(check_percentage_maximum_group_number($(this).parent().find('input.percentage_maximum_group_number'))){

            }else{
                result = false;
            }
        });
        return result;
    }

    function check_maximum_group_number(maximum_group_number_input){
        var minimum_group_number_input = maximum_group_number_input.parent().parent().find('.minimum_group_number');
        if(maximum_group_number_input.val()>0){
            //alert(minimum_group_number_input.val());
            //alert(maximum_group_number_input.val());
            if(parseInt(minimum_group_number_input.val())>parseInt(maximum_group_number_input.val())){
                maximum_group_number_input.parent().parent().addClass('has-error');
                maximum_group_number_input.parent().prepend('<i class="fa fa-exclamation "></i>');
                maximum_group_number_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter a maximum group number greater than its respective minimum group number'); 
                return false;
            }else{
                //alert("am in");
                maximum_group_number_input.parent().parent().removeClass('has-error');
                maximum_group_number_input.parent().find('i').remove();
                maximum_group_number_input.parent().parent().find('.tooltips').attr('data-original-title','');
                return true;
            }
        }else{
            //alert("am in");
            maximum_group_number_input.parent().parent().addClass('has-error');
            maximum_group_number_input.parent().prepend('<i class="fa fa-exclamation "></i>');
            maximum_group_number_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter a maximum group number'); 
            return false;
        }
    }

    function check_percentage_maximum_group_number(maximum_group_number_input){
        var minimum_group_number_input = maximum_group_number_input.parent().parent().find('.percentage_minimum_group_number');
        if(maximum_group_number_input.val()>0){
            //alert(minimum_group_number_input.val());
            //alert(maximum_group_number_input.val());
            if(parseInt(minimum_group_number_input.val())>parseInt(maximum_group_number_input.val())){
                maximum_group_number_input.parent().parent().addClass('has-error');
                maximum_group_number_input.parent().prepend('<i class="fa fa-exclamation "></i>');
                maximum_group_number_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter a maximum group number greater than its respective minimum group number'); 
                return false;
            }else{
                //alert("am in");
                maximum_group_number_input.parent().parent().removeClass('has-error');
                maximum_group_number_input.parent().find('i').remove();
                maximum_group_number_input.parent().parent().find('.tooltips').attr('data-original-title','');
                return true;
            }
        }else{
            //alert("am in");
            maximum_group_number_input.parent().parent().addClass('has-error');
            maximum_group_number_input.parent().prepend('<i class="fa fa-exclamation "></i>');
            maximum_group_number_input.parent().parent().find('.tooltips').attr('data-original-title','Please enter a maximum group number'); 
            return false;
        }
    }

</script>
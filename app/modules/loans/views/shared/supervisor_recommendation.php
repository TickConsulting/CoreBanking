<?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
    <div class="form-body">
        <div class="loan_application_form_holder" id="loan_application_form_holder">
            
        </div>
        <div class="supervisory_form_holder" id="supervisory_form_holder" style="display: none;">
            <fieldset>
                <div class="form-group comment">
                    <label>Comment <span class="required">*</span> </label>
                    <div class="input-group">
                        <span class="input-group-addon">
                        <i class="fa fa-comments"></i>
                        </span>
                        <?php
                            $textarea = array(
                                'name'=>'comment',
                                'id'=>'',
                                'value'=> $this->input->post('comment')?:'',
                                'cols'=>30,
                                'rows'=>8,
                                'maxlength'=>300,
                                'class'=>'form-control maxlength',
                                'placeholder'=>'Supervisor comment '
                            ); 
                            echo form_textarea($textarea); 
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label> Undergoing poor performance management process ? </label>
                    <div class="margin-top-10">
                        <label class="radio-inline radio-padding-0">
                            <div class="radio" id="">
                                <span class=""> 
                                     <?php echo form_radio('performance_id',1,$this->input->post('performance_id')?$post->performance_id:''," id=''"); ?>
                                </span>
                            </div> Yes
                        </label>
                       
                        <label class="radio-inline radio-padding-0">
                            <div class="radio" id="">
                                <span class="">
                                     <?php echo form_radio('performance_id',2,$this->input->post('performance_id')?$post->performance_id:''," id=''"); ?>
                                </span>
                            </div> No
                        </label>
                        <label class="radio-inline radio-padding-0">
                            <div class="radio" id="">
                                <span class="">
                                     <?php echo form_radio('performance_id',3,$this->input->post('performance_id')?$post->performance_id:''," id=''"); ?>                                 
                                </span>
                            </div> Any
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label> Ongoing /Pending disciplinary cases ?</label>
                    <div class="margin-top-10">
                        <label class="radio-inline radio-padding-0">
                            <div class="radio" id="">
                                <span class="">
                                <?php echo form_radio('disciplinary_id',1,$this->input->post('disciplinary_id')?$post->disciplinary_id:''," id=''"); ?>
                                </span>
                            </div> Yes
                        </label>
                       
                        <label class="radio-inline radio-padding-0">
                            <div class="radio" id="">
                                <span class="">
                                    <?php echo form_radio('disciplinary_id',2,$this->input->post('disciplinary_id')?$post->disciplinary_id:''," id=''"); ?>
                                </span>
                            </div> No
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Date<span class="required">*</span></label>
                            <div class="input-group  date <?php
                                echo " date-picker ";
                             ?> " data-date="<?php echo $this->input->post('stamp_date')?timestamp_to_datepicker(strtotime($this->input->post('stamp_date'))):timestamp_to_datepicker(time());?>" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                <?php echo form_input('stamp_date',$this->input->post('stamp_date')?timestamp_to_datepicker(strtotime($this->input->post('stamp_date'))):timestamp_to_datepicker($post->stamp_date)?:timestamp_to_datepicker(time()),'class="form-control" readonly');?> 
                                <span class="input-group-btn">
                                    <button class="btn default" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>  
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="form-actions" id="form_action_holder" style="display: none;">
            <input type="submit" class="btn blue submit_form_button" name="approve" value=" <?php
                    $default_message='Approve  Request';
                    $this->languages_m->translate('Approve  Request',$default_message);
                ?>">                   
            </input>
            <input type="submit" class="btn red submit_form_button" name="decline" value="<?php
                    $default_message='Decline  Request';
                    $this->languages_m->translate('Decline  Request',$default_message);
                ?>">                 
            </input>
            <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> 
                <?php
                    $default_message='Processing';
                    $this->languages_m->translate('processing',$default_message);
                ?>
            </button> 
            <button type="button" class="btn default">
                <?php
                    $default_message='Cancel';
                    $this->languages_m->translate('cancel',$default_message);
                ?>
            </button>
        </div>
    </div>    
<?php echo form_close(); ?>

<script>
    $(window).on('load',function(){
        loan_application_details();
    });

    function loan_application_details(){
        var loan_application_id = '<?php echo $this->uri->segment(4) ?>';
        App.blockUI({
            target: '#loan_application_form_holder',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            data:{loan_application_id: loan_application_id},
            url: '<?php echo base_url("ajax/loans/ajax_loan_details"); ?>',
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);                       
                        if(result.status == '200'){
                            $('#loan_application_form_holder').html(result.html); 
                            $('#supervisory_form_holder').slideDown();
                            $('#form_action_holder').slideDown();
                        }else if(result.status == '0'){
                           // $('#loan_type_details').html(result.message);  
                        }
                    }else{
                        alert(response);
                    }                          
                    App.unblockUI('#invoice_body');
                }
            }
        );
    }

    function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

</script>
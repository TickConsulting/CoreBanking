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
                <?php echo form_open_multipart(current_url(),'class="form_submit" role="form"');?>
                    <div class="form-body">
                        <div class="form-group">
                            <label>Referrer Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-map"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Referrer Name"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Referrer Information Required<span class="required">*</span></label>
                            <div class="margin-left-30">
                                <?php 
                                    if($this->input->post('referrer_information_required')==1){
                                        $referrer_information_required_yes_enabled = TRUE;
                                        $referrer_information_required_no_enabled = FALSE;
                                    }else if($post->referrer_information_required==1){
                                        $referrer_information_required_yes_enabled = TRUE;
                                        $referrer_information_required_no_enabled = FALSE;
                                    }else if($this->input->post('referrer_information_required')==0){
                                        $referrer_information_required_yes_enabled = FALSE;
                                        $referrer_information_required_no_enabled = TRUE;
                                    }else if($post->referrer_information_required==0){
                                        $referrer_information_required_yes_enabled = FALSE;
                                        $referrer_information_required_no_enabled = TRUE;                          
                                    }else{
                                        $referrer_information_required_yes_enabled = FALSE;
                                        $referrer_information_required_no_enabled = TRUE; 
                                    }
                                ?>
                                <label class="radio-inline">
                                    <div class="radio" id="">
                                        <span class="">
                                            <?php echo form_radio('referrer_information_required',1,$referrer_information_required_yes_enabled,""); ?>
                                        </span>
                                    </div> Yes </label>
                                <label class="radio-inline">
                                    <div class="radio" id="">
                                        <span class="">
                                            <?php echo form_radio('referrer_information_required',0,$referrer_information_required_no_enabled,""); ?>
                                        </span>
                                    </div> No </label>
                            </div>
                        </div>
                        
                        <div id="referrer_information_label_holder" class='row'>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Required information label </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-pencil"></i>
                                        </span> 
                                        <?php echo form_input('referrer_information_label',$this->input->post('referrer_information_label')?:$post->referrer_information_label,'class="form-control " autocomplete="off" style="" placeholder="" ');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php echo form_hidden('id',$id); ?>
                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Referrer"/>
						<button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <button type="button" class="btn default">Cancel</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('input[type=radio][name=referrer_information_required]').change(function() {
            if($(this).val()==1){
                $('#referrer_information_label_holder').slideDown();
            }else{
                $('#referrer_information_label_holder').slideUp();
            }
        });
        <?php if(($post->referrer_information_required==1||$this->input->post('referrer_information_required')==1)&&$referrer_information_required_yes_enabled){ ?>
            $('#referrer_information_label_holder').slideDown();
        <?php }else{ ?>
            $('#referrer_information_label_holder').slideUp();
        <?php } ?>
    });
</script>


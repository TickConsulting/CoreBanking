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
                            <label>Bank Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-bank"></i>
                                </span>
                                <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control" placeholder="Bank Name"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Slug<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-link"></i>
                                </span>
                                <?php echo form_input('slug',$this->input->post('slug')?$this->input->post('slug'):$post->slug,' readonly="readonly" class="form-control" placeholder="Slug"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Bank Wallet<span class="required">*</span></label>
                            <div class="input-group col-md-12">
                                <?php echo form_dropdown('wallet',array(''=>'Not a wallet','1'=>'Is a wallet'),$this->input->post('wallet')?:$post->wallet,'class="form-control select2"')?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Select Country 
                                <span class="required">*</span></label>
                            <div class="input-group col-md-12">

                                <?php echo form_dropdown('country_id',$this->country_options,$this->input->post('country_id')?:$post->country_id,' class="form-control m-input select2 member" ');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Primary Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('primary_color',$this->input->post('primary_color')?$this->input->post('primary_color'):$post->primary_color?$post->primary_color:'#000000','class="form-control colorpicker" id="hue-demo" ');?>
                            </div>
                        </div>

                         <div class="form-group">
                            <label>Secondary Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('secondary_color',$this->input->post('secondary_color')?$this->input->post('secondary_color'):$post->secondary_color?$post->secondary_color:'#000000','class="form-control colorpicker" placeholder="Secondary Color"');?>
                            </div>
                        </div> 

                        <div class="form-group">
                            <label>Tertiary Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('tertiary_color',$this->input->post('tertiary_color')?$this->input->post('tertiary_color'):$post->tertiary_color?$post->tertiary_color:'#000000','class="form-control colorpicker" placeholder="Tertiary Color"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Text Color</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paint-brush"></i>
                                </span>
                                <?php echo form_input('text_color',$this->input->post('text_color')?$this->input->post('text_color'):$post->text_color?$post->text_color:'#000000','class="form-control colorpicker" placeholder="Text Color"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="logo" class="">Bank Logo</label>
                            <div class="input-group">
                                <input type="file" name="logo">
                                <p class="help-block"> Upload Bank Logo Here </p>
                            </div>
                            <?php if(is_file(FCPATH.'uploads/files/'.$post->logo)){ ?>
                                <img src='<?php echo base_url('uploads/files/'.$post->logo); ?>' height="100px" />
                            <?php } ?>
                        </div>

                        <div class="form-group">
                            <label>Chamasoft Partner</label>
                            <div class="input-group">
                                <label class="checkbox-inline"><?php echo form_checkbox('partner',1,$this->input->post('partner')?$this->input->post('partner'):$post->partner,'class="form-control" id="partner_check_box" placeholder="Chamasoft Partner"');?> Bank is a Chamasoft Partner</label>
                            </div>
                        </div>

                        <div id="otp_urls_holder">
                            <div class="form-group">
                                <label>Country Create OTP URL<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-link"></i>
                                    </span>
                                    <?php echo form_input('create_otp_url',$this->input->post('create_otp_url')?$this->input->post('create_otp_url'):$post->create_otp_url,'class="form-control" placeholder=""');?>
                                </div>
                                <span class="help-block">eg https://wsuat.equitybankgroup.com/chamasoft/createOTP</span>
                            </div>
                            <div class="form-group">
                                <label>Country Verify OTP URL<span class="required">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-link"></i>
                                    </span>
                                    <?php echo form_input('verify_otp_url',$this->input->post('verify_otp_url')?$this->input->post('verify_otp_url'):$post->verify_otp_url,'class="form-control" placeholder=""');?>
                                </div>
                                <span class="help-block">eg https://wsuat.equitybankgroup.com/chamasoft/verifyOTP</span>
                            </div>
                        </div>

                        <?php echo form_hidden('id',$id); ?>

                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>
                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New Bank"/>
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
        $('#partner_check_box').change(function(){
            if($(this).val()==1){
                $('#otp_urls_holder').slideDown();
            }else{
                $('#otp_urls_holder').slideUp();
            }
        });

        <?php if($this->input->post('partner')==1||$post->partner==1){ ?>
            $('#otp_urls_holder').slideDown();
        <?php }else{ ?>
            $('#otp_urls_holder').slideUp();
        <?php } ?>
    });
</script>


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
                <?php echo form_open(current_url(),'class="form_submit" role="form"');?>
                    <div class="form-body">
                        <div class="form-group">
                            <label>First Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa  fa-user"></i>
                                </span>
                                <?php echo form_input('first_name',$post->first_name,'class="form-control" placeholder="User First Name"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <?php echo form_input('middle_name',$post->middle_name,'class="form-control" placeholder="User Middle Name"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Last Name<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <?php echo form_input('last_name',$post->last_name,'class="form-control" placeholder="User Last Name"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Phone Number<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-phone"></i>
                                </span>
                                <?php echo form_input('phone',$post->phone,'class="form-control" placeholder="User Valid Phone Number"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <?php echo form_input('email',$post->email,'class="form-control" placeholder="User Email Address"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Password<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user-secret"></i>
                                </span>
                                <?php echo form_password('password','','class="form-control" placeholder="Password"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Confirm Password<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user-secret"></i>
                                </span>
                                <?php echo form_password('conf_password','','class="form-control" placeholder="Confirm Password"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>USSD PIN<span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-pinterest-p"></i>
                                </span>
                                <?php echo form_input('ussd_pin',$post->ussd_pin,'class="form-control" placeholder="USSD PIN"');?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="multi-prepend" class="control-label">User Groups<span class="required">*</span></label> 
                            <div class="input-group select2-bootstrap-prepend">
                                
                                <?php echo form_dropdown('group_id[]',$groups,$this->input->post('group_id')?$this->input->post('group_id'):$sel_groups,'id="user_group_select" class="form-control select2" multiple placeholder="Select Tile Color"');?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="multi-prepend" class="control-label">Investment Group<span class="required">*</span></label> 
                            <div class="input-group select2-bootstrap-prepend">
                                
                                <?php echo form_dropdown('investment_group[]',array(' '=>'--Select Group--')+$investment_groups,$this->input->post('investment_group')?$this->input->post('investment_group'):'','id="" multiple class="form-control select2" placeholder="Select Tile Color"');?>
                            </div>
                        </div>
                        
                        <div id="partner_bank_options" class="form-group">
                            <label for="multi-prepend" class="control-label">Partner Banks<span class="required">*</span></label> 
                            <div class="input-group select2-bootstrap-prepend">
                                <?php echo form_dropdown('partner_bank_options[]',$partner_bank_options,$this->input->post('partner_bank_options')?$this->input->post('partner_bank_options'):$selected_partner_bank_options,'id="multi-append" class="form-control select2" multiple placeholder="Select Partner Bank Options"');?>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit"  class="btn blue submit_form_button">Save Changes</button>

                        <input type="submit" class="btn btn-primary submit_form_button" name="new_item" value="Save Changes and Create New User"/>

                        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
                        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#user_group_select').change(function(){
            if ($("#user_group_select option[value=4]:selected").length > 0){
                $('#partner_bank_options').slideDown();
            }else{
                $('#partner_bank_options').slideUp();
                //DO something if not selected
            }
        });
        <?php 
        if(is_array($this->input->post('group_id'))){
            $posted_select_values = $this->input->post('group_id');
        }else{
            $posted_select_values = array();
        }
        if(in_array(4,$sel_groups)||in_array(4,$posted_select_values)): ?>
            $('#partner_bank_options').slideDown();
        <?php endif; ?>
    });
</script>

 <?php echo form_open_multipart($this->uri->uri_string(), ' role="form" class="form_submit" '); ?>
    <div class="form-body">
        <div class="form-group">
            <label for="bank-branches"><?php
                    if($this->lang->line('asset_name')){
                    echo $this->lang->line('asset_name');
                    }else{
                    echo "Asset Name";
                    }
                ?><span class="required">*</span></label>
                <div class="input-group col-md-12 col-sm-12 col-xs-12 bank_branches_space">
                    <?php echo form_input('name',$this->input->post('name')?:$post->name,'class="form-control name" id = "name"  ') ?>
                </div>
        </div>


        <div class="form-group">
            <label><?php
                    if($this->lang->line('asset_category')){
                    echo $this->lang->line('asset_category');
                    }else{
                    echo "Asset Category";
                    }
                ?><span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                <i class="fa fa-bars"></i>
                </span>
                <?php echo form_dropdown('asset_category_id',array(''=>'--Select Category--')+$asset_categories,$this->input->post('asset_category_id')?$this->input->post('asset_category_id'):$post->asset_category_id,'class="form-control select2" placeholder="Account Number"'); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="bank-branches">Asset Cost<span class="required">*</span></label>
                <div class="input-group col-md-12 col-sm-12 col-xs-12 bank_branches_space">
                    <?php echo form_input('cost',$this->input->post('cost')?:$post->cost,'class="form-control currency" placeholder="Group Asset Cost"') ?>
                </div>
        </div>


        <div class="form-group">
            <label>Asset Description<span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-book"></i>
                </span>
                <?php echo form_textarea('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control" placeholder="Group Asset Description"');?>
            </div>
        </div>

        <?php echo form_hidden('id',$id);?>           
    </div>
    <div class="form-actions">
        <button type="submit"  class="btn blue submit_form_button"><?php
                    if($this->lang->line('save_changes')){
                    echo $this->lang->line('save_changes');
                    }else{
                    echo "Save Changes";
                    }
                ?></button>
        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i><?php
                    if($this->lang->line('processing')){
                    echo $this->lang->line('processing');
                    }else{
                    echo "Processing";
                    }
                ?></button> 
        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default"><?php
                    if($this->lang->line('cancel')){
                    echo $this->lang->line('cancel');
                    }else{
                    echo "Cancel";
                    }
                ?></button></a>
    </div>
<?php echo form_close(); ?>
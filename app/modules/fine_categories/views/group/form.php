<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_fine_category"'); ?> 
    <div class="form-group m-form__group row pt-0 m--padding-10 m-1">
        <div class="col-sm-12 m-form__group-sub pt-0 m--padding-10">
            <label><?php echo translate('Group Fine Category Name');?><span class="required">*</span></label>
            <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'  class="form-control  m-input--air" id="name" autocomplete="off"  placeholder="Group Fine Category Name"'); ?>
        </div>
        <?php echo form_hidden('id',isset($post->id)?$post->id:'')?>
        <div class="col-md-12 m-form__group-sub pt-0 m--padding-10">
            <span class="float-lg-right float-md-right float-sm-right float-xl-right">
                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_fine_category_button" type="button">
                    <?php echo translate('Save Changes & Submit');?>
                </button>
                &nbsp;&nbsp;
                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_invoice_button">
                    <?php echo translate('Cancel');?>
                </button> 
            </span>
        </div> 
    </div>
<?php echo form_close() ?>
    
<script type="text/javascript">
    $(document).ready(function(){
        var id =  $('input[name="id"]').val()
        if(id==''){
            SnippetCreateFIneCategory.init();
        }else{
            SnippetEditFIneCategory.init();
        }

    });

</script>
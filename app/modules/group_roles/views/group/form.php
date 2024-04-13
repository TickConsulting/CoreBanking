<div  id="">
    <div class="m-form__section m-form__section--first">                
        <div class="">
            <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="add_new_member_role_form"'); ?>
                <div id="group_roles_settings">
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-12 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Group Role Name');?>?: <span class="required">*</span></label>
                            <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control m-input m-input--air" placeholder="eg Chairperson"');?>
                        </div>
                    </div>
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-12 col-sm-12 m-form__group-sub">
                            <label class="form-control-label"><?php echo translate('Group Role Description');?> </label>
                            <?php echo form_textarea('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control m-input m-input--air" placeholder="Group Role Description"');?>
                        </div>
                    </div>
                    <?php echo form_hidden('id',isset($post->id)?$post->id:'')?>
                    <div class="form-group m-form__group row pt-0 m--padding-10">
                        <div class="col-lg-12 col-md-12">
                            <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="add_new_role_btn" type="button">
                                    <?php echo translate('Save Changes');?>
                                </button>
                                &nbsp;&nbsp;
                                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_create_group_roles">
                                    <?php echo translate('Cancel');?>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>              

<script type="text/javascript">

    $(document).ready(function(){
       var id =  $('input[name="id"]').val()
       if(id==''){
            SnippetCreateGroupRole.init(true,false);
       }else{
            SnippetEditGroupRoles.init();
       }

       

    });
    

</script>
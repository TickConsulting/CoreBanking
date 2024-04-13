<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_income_categories"'); ?>

    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-sm-12 m-form__group-sub">
            <label><?php echo translate('Name');?><span class="required">*</span></label>
            <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control name m-input--air" placeholder="Income Category Name"'); ?>
        </div>
    </div>
    <div class="form-group m-form__group row pt-0 m--padding-0">
        <div class="col-sm-12 m-form__group-sub">
            <?php echo form_hidden('slug',$this->input->post('slug')?:$post->slug);?>
            <?php echo form_hidden('id',$id);?>
        </div>
    </div>
    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-sm-12 m-form__group-sub">
            <label><?php echo translate('Description');?> </label>
            <?php echo form_textarea('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control m-input--air" placeholder="Group Income Category Description"');?>
        </div>
    </div>
    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-lg-12 col-md-12">
            <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_income_categories_button" type="button">
                    <?php echo translate('Save Changes & Submit');?>
                </button>
                &nbsp;&nbsp;
                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_income_categories_button">
                    <?php echo translate('Cancel');?>
                </button> 
            </span>
        </div>
    </div>

<?php echo form_close() ?>

<script type="text/javascript">
    $(document).ready(function(){
        
        var id =  $('input[name="id"]').val();
        if(id==''){
            SnippetCreateIncomeCategory.init(true,false);
        }else{
            SnippetEditIncomeCategory.init(true,false);
        }

        $('input[name=title]').keyup(function(){
            txt = $(this).val();
            var re = /\W/gi; 
            var rew = /\s/gi; 
            txt2=txt.replace(rew,'-');
            txt2=txt2.replace(re,'-');
            $('input[name=slug]').val(txt2.toLowerCase());
        });
        
        $('input[name=title]').blur(function(){
            txt = $(this).val();
            var re = /\W/gi; 
            var rew = /\s/gi; 
            txt2=txt.replace(rew,'-');
            txt2=txt2.replace(re,'-');
            $('input[name=slug]').val(txt2.toLowerCase());
        });
        $(document).on('keyup','input[name=name]',function(){ 
            txt = $(this).val();
            var re = /\W/gi; 
            var rew = /\s/gi; 
            txt2=txt.replace(rew,'-');
            txt2=txt2.replace(re,'-');
            $('input[name=slug]').each(function(){
                $(this).val(txt2.toLowerCase());
            });
        });

        $(document).on('blur','input[name=name]',function(){ 
            txt = $(this).val();
            var re = /\W/gi; 
            var rew = /\s/gi; 

            txt2=txt.replace(rew,'-');
            txt2=txt2.replace(re,'-');
            $('input[name=slug]').each(function(){
                $(this).val(txt2.toLowerCase());
            });
        });

        $(document).on('keyup','.slug_parent',function(){ 
            txt = $(this).val();
            var re = /\W/gi; 
            var rew = /\s/gi; 
            txt2=txt.replace(rew,'-');
            txt2=txt2.replace(re,'-');
            $('input[name=slug]').each(function(){
                $(this).val(txt2.toLowerCase());
            });
        });

        $(document).on('blur','.slug_parent',function(){ 
            txt = $(this).val();
            var re = /\W/gi; 
            var rew = /\s/gi; 
            txt2=txt.replace(rew,'-');
            txt2=txt2.replace(re,'-');
            $('input[name=slug]').each(function(){
                $(this).val(txt2.toLowerCase());
            });
        });

    });
</script>
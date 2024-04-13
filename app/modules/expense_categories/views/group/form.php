
<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_expense_category"'); ?>

<div class="form-group m-form__group row pt-0 m--padding-10">
    <div class="col-sm-12 m-form__group-sub">
        <label><?php echo translate('Group Expense Category Name');?><span class="required">*</span></label>
        <?php echo form_input('name',$this->input->post('name')?$this->input->post('name'):$post->name,'class="form-control name m-input--air" placeholder="Group Expense Category Name"'); ?>

        <span class="m-form__help"><?php echo translate('eg. End Year Party');?></span>
    </div>
    <?php echo form_hidden('id',$id);?>
    <?php echo form_hidden('slug',$this->input->post('slug')?:$post->slug);?>

    <div class="col-sm-12 m-form__group-sub m-input--air pt-0 m--padding-10">
        <label><?php echo translate('Group Expense Category Description');?> </label>
         <?php echo form_textarea('description',$this->input->post('description')?$this->input->post('description'):$post->description,'class="form-control" placeholder="Group Expense Category Description"');?>
    </div>

    <div class="col-sm-12 m-form__group form-group pt-0 m--padding-top-10" id="">
        <div class="m-checkbox-inline"> 
            <?php 
                if($this->input->post('is_an_administrative_expense_category')?$this->post->input('is_an_administrative_expense_category'):$post->is_an_administrative_expense_category==1){
                    $is_an_administrative_expense_category = TRUE;
                }else if($this->input->post('is_an_administrative_expense_category')?$this->post->input('is_an_administrative_expense_category'):$post->is_an_administrative_expense_category==0){
                    $is_an_administrative_expense_category = FALSE;
                }else{
                    $is_an_administrative_expense_category = FALSE;
                }
            ?>           
            <label class="m-checkbox m-checkbox--solid m-checkbox--brand">
                <?php echo form_checkbox('is_an_administrative_expense_category',1,$is_an_administrative_expense_category,""); ?><?php echo translate('Is an Administrative Expense Category');?>
                <span></span>
            </label>
        </div>
    </div>
</div>

<div class="form-group m-form__group row pt-0 m--padding-10">
    <div class="col-lg-12 col-md-12">
        <span class="float-lg-right float-md-left float-sm-left float-xl-right">
            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_expense_category_button" type="button">
                <?php echo translate('Save Changes & Submit');?>
            </button>
            &nbsp;&nbsp;
            <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_create_group_roles">
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
            SnippetCreateExpenseCategory.init(true,false);
        }else{
            SnippetEditExpenseCategory.init(true,false);
        }

    });
</script>

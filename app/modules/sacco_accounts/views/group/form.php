<?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="create_sacco_accounts"'); ?> 

    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-lg-12 col-sm-12 m-form__group-sub pt-0 m--padding-10">                            
            <label><?php echo translate('Account Name');?><span class="required">*</span></label>
            <?php echo form_input('account_name',$this->input->post('account_name')?$this->input->post('account_name'):$post->account_name,'  class="form-control  m-input--air" id="amount_name" autocomplete="off"  placeholder="Account Name"'); ?>
        </div>        
    </div>
    <?php echo form_hidden('id',$id);?>

    <div class="form-group m-form__group row pt-0 m--padding-10"> 
         <div class="col-sm-6 m-form__group-sub pt-0 m--padding-10" id=''>
            <label><?php echo translate('Group Name');?><span class="required">*</span></label>
            <?php echo form_dropdown('sacco_id',array(''=>'--Select Sacco--')+translate($saccos),$this->input->post('sacco_id')?:$post->sacco_id,' class="form-control m-input m-select2 " ');?>
        </div>        
        <div class="col-sm-6 m-form__group-sub pt-0 m--padding-10" id=''>
            <label><?php echo translate('Branch Name');?><span class="required">*</span></label>
            <div class="sacco_branches_space">
                <?php echo form_dropdown('sacco_branch_id',array(''=>'--Select Sacco Name First--'),$this->input->post('sacco_branch_id')?:$post->sacco_branch_id,' class="form-control m-input m-select2 " id = "sacco_branch_id" ');?>
            </div>
        </div> 
    </div>
    <div class="form-group m-form__group row pt-0 m--padding-10">
        <div class="col-lg-12 col-sm-12 m-form__group-sub pt-0 m--padding-10">                            
            <label><?php echo translate('Account Number');?><span class="required">*</span></label>
            <?php echo form_input('account_number',$this->input->post('account_number')?$this->input->post('account_number'):$post->account_number,'  class="form-control  m-input--air" id="amount_name" autocomplete="off"  placeholder="Account Number"'); ?>
        </div> 
    </div>

    <div class="col-md-12 m-form__group-sub pt-0 m--padding-10">
        <span class="float-lg-right float-md-right float-sm-right float-xl-right">
            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_sacco_account_button" type="button">
                <?php echo translate('Save Changes');?>
            </button>
            &nbsp;&nbsp;
            <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_sacco_account_button">
                <?php echo translate('Cancel');?>
            </button> 
        </span>
    </div>

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function(){
        var empty_branch_list =$('.sacco_branches_space').find('select').html();
        var branch_id = '<?php echo $post->sacco_branch_id;?>';
        var id =  $('input[name="id"]').val();
        if(id==''){
            SnippetCreateSaccoAccount.init(true,false);
        }else{
            SnippetEditSaccoAccount.init(true,false);
        }

        Select2.init('.m-select2')
        $('select[name="sacco_id"]').on('change',function(){

            var sacco_id = $(this).val();
            if(sacco_id)
            {
                $.post('<?php echo site_url('group/sacco_accounts/ajax_get_sacco_branches');?>',{'sacco_id':sacco_id,'branch_id':''},
                function(data)
                {
                    $('.sacco_branches_space').html(data);
                    $('#sacco_branch_id').select2();

                });
            }else{
                $('.sacco_branches_space').html('<select name="sacco_id" class="form-control m-input m-select2" id="sacco_branch_id">'+empty_branch_list+'</select>');
                 $('#sacco_branch_id').select2();
            }
        });

        var sacco_id = $('select[name="sacco_id"]').val();
        if(sacco_id){
            $.post('<?php echo site_url('group/sacco_accounts/ajax_get_sacco_branches');?>',{'sacco_id':sacco_id,'branch_id':branch_id},
            function(data)
            {
                $('.sacco_branches_space').html(data);
                $('#sacco_branch_id').select2();

            });
        }else{
            $('.sacco_branches_space').html('<select name="sacco_id" class="form-control m-input m-select2" id="sacco_branch_id">'+empty_branch_list+'</select>');
            $('#sacco_branch_id').select2();
        }

    });
</script>


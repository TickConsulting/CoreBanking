<?php echo form_open($this->uri->uri_string(),'class="m-form form_submit m-form--state record_contribution_transfer" id="record_contribution_transfer" role="form"'); ?>
   <div class="form-group m-form__group p-0">
        <div class="col-sm-12 m-form__group-sub">
            <label><?php echo translate('Contribution transfer date');?><span class="required">*</span></label>
            <div class="input-group ">
                <?php echo form_input('transfer_date',$this->input->post('transfer_date')?timestamp_to_datepicker(strtotime($this->input->post('transfer_date'))):timestamp_to_datepicker(time()),'class="form-control m-input datepicker" data-date-end-date="0d" data-date-start-date="-20y" readonly');?>
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="la la-calendar-check-o"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="pt-0 m--padding-top-10 col-sm-12">
            <div class="m-form__group-sub">
                <label><?php echo translate('Transfer for');?><span class="required">*</span></label>
                <?php echo form_dropdown('transfer_for',array(''=>translate('Select Option'))+array(1=>'Specific Members',2=>'All Members'),$this->input->post('transfer_for'),' class="form-control m-input m-select2 " id="transfer_for"');?>
            </div>
        </div>

        <div class="pt-0 m--padding-top-10 col-sm-12 specific_member_options" style="display: none;">
            <div class="m-form__group-sub">
                <label><?php echo translate('Select Member(s)');?><span class="required">*</span></label>
                <?php echo form_dropdown('member_ids[]',translate($this->active_group_member_options)+array('0'=>"Add Member"),$this->input->post('member_id')?:$post->member_id?:'',' class="form-control m-input m-select2 " multiple="multiple" id="member_id"');?>
            </div>
        </div>

        <div class="pt-0 m--padding-top-10 col-sm-12  additional_options" style="display: none;">
            <div class="row">
                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10">
                    <label><?php echo translate('Select Contribution /Loan From');?><span class="required">*</span></label>
                    <?php echo form_dropdown('contribution_from_id',array(''=>'-- Select Contribution From --')+$contribution_options+array('loan'=>'Loan Payments')+array('0'=>"Add Contribution"),$this->input->post('contribution_from_id')?:$post->contribution_from_id?:'',' class="form-control m-input m-select2 " id="contribution_from_id"');?>
                </div>
                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10 member-loans loan-from-dropdown-setting ">
                    <label><?php echo translate('Select Loan From');?><span class="required">*</span></label>

                    <?php echo form_dropdown('loan_from_id',array(''=>'--Select Loan From--')+$contribution_options+array('loan'=>'Loan Payments')+array('0'=>"Add Contribution"),$this->input->post('loan_from_id')?$this->input->post('loan_from_id'):$post->loan_from_id,' class="form-control m-input m-select2" id="loan_from_id"');?>

                </div>
                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10">
                    <label><?php echo translate('Select Transfer To');?><span class="required">*</span></label>
                    <?php echo form_dropdown('transfer_to',array(''=>'--Select Transfer To--')+$transfer_to_options,$this->input->post('transfer_to')?$this->input->post('transfer_to'):$post->transfer_to,' class="form-control m-input m-select2" id="transfer_to"');?>
                </div>

                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10" id="contribution_to_id">
                    <label><?php echo translate('Select Contribution To');?><span class="required">*</span></label>
                    <?php echo form_dropdown('contribution_to_id',array(''=>'--Select Contribution To--')+$contribution_options+array('0'=>"Add Contribution"),$this->input->post('contribution_to_id')?$this->input->post('contribution_to_id'):$post->contribution_to_id,' class="form-control m-input m-select2" id="contribution_to_id"');?>
                </div>
                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10" id="fine_category_to_id">
                    <label><?php echo translate('Select Fine Category To');?><span class="required">*</span></label>
                    <?php echo form_dropdown('fine_category_to_id',array(''=>'--Select Fine Category To--')+$fine_category_options+array('0'=>"Add Fine Category"),$this->input->post('fine_category_to_id')?$this->input->post('fine_category_to_id'):$post->fine_category_to_id,' class="form-control m-input m-select2" id="fine_category_to_id"');?>
                </div>

                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10 member-loans-transfer-to loan-to-dropdown-setting">
                    <label><?php echo translate('Select Loan To');?><span class="required">*</span></label>
                    <?php echo form_dropdown('loan_to_id',array(''=>'--Select Loan To--'),$this->input->post('loan_to_id')?:$post->loan_to_id,' class="form-control m-input m-select2" id="loan_to_id"');?>
                </div>

                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10" id="member_to_id">
                    <label><?php echo translate('Select Member to Receive Transfer');?><span class="required">*</span></label>
                    <?php echo form_dropdown('member_to_id',array(''=>'--Select Member--')+$this->active_group_member_options+array('0'=>"Add Member"),$this->input->post('member_to_id')?$this->input->post('member_to_id'):$post->member_to_id,' class="form-control m-input m-select2" id="member_to_id"');?>
                </div>

                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10" id="member_transfer_to">
                    <label><?php echo translate('Select Transfer To');?><span class="required">*</span></label>
                    <?php echo form_dropdown('member_transfer_to',array(''=>'--Select Transfer To--')+$member_transfer_to_options,$this->input->post('member_transfer_to')?$this->input->post('member_transfer_to'):$post->member_transfer_to,' class="form-control m-input m-select2" id="member_transfer_to"');?>
                </div>

                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10" id="member_contribution_to_id">
                    <label><?php echo translate('Select Contribution To');?><span class="required">*</span></label>
                    <?php echo form_dropdown('member_contribution_to_id',array(''=>'--Select Contribution To--')+$contribution_options+array('0'=>"Add Contribution"),$this->input->post('member_contribution_to_id')?$this->input->post('member_contribution_to_id'):$post->member_contribution_to_id,' class="form-control m-input m-select2" id="member_contribution_to_id"');?>
                </div>

                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10" id="member_fine_category_to_id">
                    <label><?php echo translate('Select Fine Category To');?><span class="required">*</span></label>
                    <?php echo form_dropdown('member_fine_category_to_id',array(''=>'--Select Fine Category To--')+$fine_category_options+array('0'=>"Add Fine Category"),$this->input->post('member_fine_category_to_id')?$this->input->post('member_fine_category_to_id'):$post->member_fine_category_to_id,' class="form-control m-input m-select2" id="member_fine_category_to_id"');?>
                </div>

                <div class="col-sm-12 m-form__group-sub pt-0 m--padding-top-10" id="member_loan_to_id">
                    <label><?php echo translate('Select Loan To');?><span class="required">*</span></label>
                    <?php echo form_dropdown('member_loan_to_id',array(''=>'--Select Loan To--'),$this->input->post('member_loan_to_id')?:$post->member_loan_to_id,' class="form-control m-input m-select2" id="member_loan_to_id"');?>
                </div>

                <div class="col-md-12 m-form__group-sub pt-0 m--padding-top-10">
                    <label><?php echo translate('Amount');?><span class="required">*</span></label>
                    <?php echo form_input('amount',$this->input->post('amount')?$this->input->post('amount'):$post->amount,'class="form-control m-input currency" ');?> 
                </div>

                <div class="col-md-12 m-form__group-sub pt-0 m--padding-top-10">
                    <label><?php echo translate('Description');?></label>
                    <div class="input-group ">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="mdi mdi-format-indent-increase"></i>
                            </span>
                        </div>
                        <?php echo form_textarea('description',$this->input->post('description')?:$post->description,'class="form-control m-input " ');?>                            
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group m-form__group row p-0 m--padding-top-10">
        <div class="col-lg-12 col-md-12">
            <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="create_contribution_transfers_button" type="button">
                    <?php echo translate('Save Changes & Submit');?>
                </button>
                &nbsp;&nbsp;
                <!-- <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_member_loan_button">
                    <?php echo translate('Cancel');?>
                </button> -->
            </span>
        </div>
    </div>
<?php echo form_close() ?>
<script>
    $(document).ready(function(){
        $('.m-select2').select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            width: "100%",
        });
        SnippetCreateContributionTransfer.init();
        $(document).on('change','select[name="transfer_for"]',function(){
            var transfer_for = $(this).val();
            if(transfer_for==1){
                $('.specific_member_options').slideDown();
                $('.additional_options').slideDown();
            }else if(transfer_for==2){
                $('.specific_member_options').slideUp();
                $('.additional_options').slideDown();
            }else{
                $('.specific_member_options').slideUp();
                $('.additional_options').slideUp();
            }
        });

        <?php if($this->input->post('transfer_for')){ ?>
            $('select[name="transfer_for"]').trigger('change');
        <?php }?>

        $(document).on('change','#transfer_to',function(){
            $('#member_fine_category_to_id,#member_contribution_to_id,#member_loan_to_id').slideUp();
            $('#member_transfer_to,#member_fine_category_to_id_select,#member_contribution_to_id_select,#member_loan_to_id_select').val('').trigger('change');
            if($(this).val()==1){
                $('#contribution_to_id').slideDown();
                $('#fine_category_to_id').slideUp();
                $('.member-loans-transfer-to,#member_to_id,#member_transfer_to').slideUp();
            }else if($(this).val()==2){
                $('#fine_category_to_id').slideDown();
                $('#contribution_to_id').slideUp();
                $('.member-loans-transfer-to,#member_to_id,#member_transfer_to').slideUp();
            }else if($(this).val()==3){
                $('.member-loans-transfer-to').slideDown();
                $('#fine_category_to_id').slideUp();
                $('#contribution_to_id,#member_to_id,#member_transfer_to').slideUp();
                $('#loan_to_id').select2();
            }else if($(this).val()==4){
                $('#member_to_id,#member_transfer_to').slideDown();
                $('#contribution_to_id,#fine_category_to_id,.member-loans-transfer-to').slideUp();
            }else{
                $('#contribution_to_id,#fine_category_to_id,.member-loans-transfer-to,#member_to_id,#member_transfer_to').slideUp();
            }
        });

        <?php if($this->input->post('transfer_to')){ ?>
            $('#transfer_to').trigger('change');
        <?php }?>

        $(document).on('change','select[name="member_transfer_to"]',function(){
            if($(this).val()==1){
                $('#member_contribution_to_id').slideDown();
                $('#member_fine_category_to_id,#member_loan_to_id').slideUp();
            }else if($(this).val()==2){
                $('#member_fine_category_to_id').slideDown();
                $('#member_contribution_to_id,#member_loan_to_id').slideUp();
            }else if($(this).val()==3){
                $('#member_loan_to_id').slideDown();
                $('#member_fine_category_to_id,#member_contribution_to_id').slideUp();
            }else{
                $('#member_fine_category_to_id,#member_contribution_to_id,#member_loan_to_id').slideUp();
            }
        });

        <?php if($post->member_transfer_to==1||$this->input->post('member_transfer_to')==1){ ?>
            $('#member_contribution_to_id').slideDown();
            $('#member_fine_category_to_id,#member_loan_to_id').slideUp();
        <?php }else if($post->member_transfer_to==2||$this->input->post('member_transfer_to')==2) { ?>
            $('#member_fine_category_to_id').slideDown();
            $('#member_contribution_to_id,#member_loan_to_id').slideUp();
        <?php }else if($post->member_transfer_to==3||$this->input->post('member_transfer_to')==3) { ?> 
            $('#member_loan_to_id').slideDown();
            $('#member_fine_category_to_id,#member_contribution_to_id').slideUp();
        <?php }else{ ?>
            $('#member_fine_category_to_id,#member_contribution_to_id,#member_loan_to_id').slideUp();
        <?php } ?>

        <?php if($post->transfer_to==1||$this->input->post('transfer_to')==1){ ?>
            $('#contribution_to_id').slideDown();
            $('#fine_category_to_id').slideUp();
            $('.member-loans-transfer-to,#member_to_id,#member_transfer_to').slideUp();
        <?php }else if($post->transfer_to==3||$this->input->post('transfer_to')==3){?>
            $('.member-loans-transfer-to').slideDown();
            $('#fine_category_to_id').slideUp();
            $('#contribution_to_id,#member_to_id,#member_transfer_to').slideUp();
        <?php }else if($post->transfer_to==4||$this->input->post('transfer_to')==4){?>
            $('#member_to_id,#member_transfer_to').slideDown();
            $('#contribution_to_id,#fine_category_to_id,.member-loans-transfer-to').slideUp();
        <?php }else{?>
            $('#contribution_to_id,#fine_category_to_id,.member-loans-transfer-to,#member_to_id,#member_transfer_to').slideUp();
        <?php } ?>

        <?php if($post->transfer_to==2||$this->input->post('transfer_to')==2){ ?>
            $('#fine_category_to_id').slideDown();
        <?php }else{ ?>
            $('#fine_category_to_id,#contribution_to_id').slideUp();
        <?php } ?>

        $('select[name="member_ids[]"]').change(function(){
            var member_ids = $(this).val();
            var member_id = '';
            if(member_ids.length > 1){
                $('select[name=loan_from_id], select[name="loan_to_id"]').attr('disabled','disabled');
            }else{
                $('select[name=loan_from_id], select[name="loan_to_id"]').removeAttr('disabled');  
                $.each(member_ids,function(key,value){
                    member_id = value;
                });
            }
            if(member_id){
                mApp.block('.loan-to-dropdown-setting',{
                    overlayColor: 'grey',
                    animate: true
                });
                mApp.block('.loan-from-dropdown-setting',{
                    overlayColor: 'grey',
                    animate: true
                });
                var url = '<?php echo site_url('group/loans/ajax_get_active_member_loans_for_transfer')?>';
                var loan_from_id = '<?php echo $this->input->post('loan_from_id')?:$post->loan_from_id; ?>';
                var loan_to_id = '<?php echo $this->input->post('loan_to_id')?:$post->loan_to_id; ?>';
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "html",
                    data: {'member_id': member_id,'loan_from_id':loan_from_id,'loan_to_id':loan_to_id},
                    success: function(res) {
                        var obj = $.parseJSON(res);
                        $('.loan-from-dropdown-setting').html('<label><?php echo translate('Select Loan From');?><span class="required">*</span></label>'+obj.loan_from);
                        $('.loan-to-dropdown-setting').html('<label><?php echo translate('Select Loan To');?><span class="required">*</span></label>'+obj.loan_to);
                        $('input[name="loan_from_id"]').select2();
                        $('input[name="loan_to_id"]').select2();
                        //$('.select2-container').css('width','100%');
                        mApp.unblock('.loan-from-dropdown-setting');
                        mApp.unblock('.loan-to-dropdown-setting');
                    },
                    error: function(xhr, ajaxOptions, thrownError)
                    {
                        
                    }
                });
            }
        });

        $('select[name="member_to_id"]').change(function(){
            mApp.block('.member-loan-to-dropdown-setting',{
                overlayColor: 'grey',
                animate: true
            });
            var member_id = $(this).val();
            var url = '<?php echo site_url('group/loans/ajax_get_active_member_loans_to_transfer')?>';
            var loan_from_id = '';
            var loan_to_id = '';
            $.ajax({
                type: "POST",
                url: url,
                dataType: "html",
                data: {'member_id': member_id,'loan_from_id':loan_from_id,'loan_to_id':loan_to_id},
                success: function(res) 
                {
                    var obj = $.parseJSON(res);
                    //$('.loan-from-dropdown-setting').html(obj.loan_from);
                    $('.member-loan-to-dropdown-setting').html(obj.member_loan_to_id);
                    //$('.select2').select2();
                    //$('.select2-container').css('width','100%');
                    mApp.unblock('.member-loan-to-dropdown-setting');
                },
                error: function(xhr, ajaxOptions, thrownError)
                {
                    
                }
            });
        });
    
        <?php if($this->input->post('member_to_id') || $post->member_to_id){?>
            var member_id = "<?php echo $this->input->post('member_id')?:$post->member_id; ?>";
            var member_loan_to_id = "<?php echo $this->input->post('member_loan_to_id')?:$post->member_loan_to_id; ?>";
            if(member_id){
                mApp.block('.member-loan-to-dropdown-setting',{
                    overlayColor: 'grey',
                    animate: true
                });

                var url = '<?php echo site_url('group/loans/ajax_get_active_member_loans_to_transfer')?>';
                var loan_from_id = '';
                var loan_to_id = '';
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "html",
                    data: {'member_id': member_id,'loan_from_id':loan_from_id,'member_loan_to_id':member_loan_to_id},
                    success: function(res) 
                    {
                        var obj = $.parseJSON(res);
                        //$('.loan-from-dropdown-setting').html(obj.loan_from);
                        $('.member-loan-to-dropdown-setting').html(obj.member_loan_to_id);
                        //$('.select2').select2();
                        //$('.select2-container').css('width','100%');
                        mApp.unblock('.member-loan-to-dropdown-setting');
                    },
                    error: function(xhr, ajaxOptions, thrownError)
                    {
                        
                    }
                });
            }
        <?php } ?>

        <?php if($this->input->post('member_id') || !empty($post->member_id)){?>
            var member_id = "<?php echo $this->input->post('member_id')?:$post->member_id; ?>";
            if(member_id){
                mApp.block('.loan-to-dropdown-setting',{
                    overlayColor: 'grey',
                    animate: true
                });
                mApp.block('.loan-from-dropdown-setting',{
                    overlayColor: 'grey',
                    animate: true
                });
                var url = '<?php echo site_url('group/loans/ajax_get_active_member_loans_for_transfer')?>';
                var loan_from_id = '<?php echo $this->input->post('loan_from_id')?:$post->loan_from_id; ?>';
                var loan_to_id = '<?php echo $this->input->post('loan_to_id')?:$post->loan_to_id; ?>';
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "html",
                    data: {'member_id': member_id,'loan_from_id':loan_from_id,'loan_to_id':loan_to_id},
                    success: function(res) 
                    {
                        var obj = $.parseJSON(res);
                        $('.loan-from-dropdown-setting').html(obj.loan_from);
                        $('.loan-to-dropdown-setting').html(obj.loan_to);
                        $('input[name="loan_from_id"]').select2();
                        $('input[name="loan_to_id"]').select2();
                        mApp.unblock('.loan-from-dropdown-setting');
                        mApp.unblock('.loan-to-dropdown-setting');
                    },
                    error: function(xhr, ajaxOptions, thrownError)
                    {
                        
                    }
                });
            }
        <?php }?>

        $('select[name="contribution_from_id"]').change(function(){
            if($(this).val()==='loan'){
                $('.member-loans').slideDown();
                $('#loan_from_id').select2();
            }else{
                $('.member-loans').slideUp();
            }
        });

        var contribution_from_id = $('Select[name="contribution_from_id"]').val();
        if(contribution_from_id==='loan'){
            $('.member-loans').slideDown();
            $('#loan_from_id').select2();
        }else{
            $('.member-loans').slideUp();
        }

        $(document).on('click','#add_new_member',function(){
            $(".member").select2({
                width:'100%',
                language: 
                    {
                    noResults: function() {
                        return '<a class="inline" data-toggle="modal" data-content="#create_new_member_pop_up" data-title="Add Member" data-id="create_member" id="add_new_member"  >Add Member</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger("select2:close");
        });

        $(document).on('click','#add_contribution',function(){
            $(".contribution").select2({
                width:'100%',
                language: 
                    {
                     noResults: function() {
                        return '<a class="inline pop_up" data-row="" data-toggle="modal" data-content="#contributions_form" data-title="Add Contribution" data-id="add_contribution" id="add_contribution" href="#">Add Contribution</a>';
                    }
                },
                escapeMarkup: function (markup) {
                    return markup;
                }
            }).trigger("select2:close");
        });

        $(document).on('change','.member',function(){
            if($(this).val()=='0'){
                $('#add_new_member').trigger('click');
                $(this).val("").trigger('change');
            }
        });

        $(document).on('change','.contribution',function(){
            if($(this).val()=='0'){
                $('#add_contribution').trigger('click');
                $(this).val("").trigger('change');
            }
        });
    });

    $(window).on('load',function(){
        $('.member').select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="inline" data-toggle="modal" data-content="#create_new_member_pop_up" data-title="Add Member" data-id="create_member" id="add_new_member"  >Add Member</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $(".contribution").select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="inline pop_up" data-row="" data-toggle="modal" data-content="#contributions_form" data-title="Add Contribution" data-id="add_contribution" id="add_contribution" href="#">Add Contribution</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $('.fine_category').select2({
            width:'100%',
            language: 
                {
                 noResults: function() {
                    return '<a class="inline pop_up" data-row="" data-toggle="modal" data-content="#add_fine_category" data-title="Add Fine Category" data-id="add_fine_category" id="add_fine_category_link" href="#">Add Fine Category</a>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    });

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

</script>




<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">Money Market Investment Information</h3>
    </div>
    <div class="panel-body"> 
    	<table class="table table-condensed table-bordered table-striped">
    		<thead>
    			<tr>
    				<th width="2%">#</th>
    				<th width="30%">Item</th>
    				<th>Particulars</th>
    			</tr>
    		</thead>
    		<tbody>
    			<tr>
    				<td>1</td>
    				<td>
                        <?php
                            $default_message='Investment Date';
                            $this->languages_m->translate('investment_date',$default_message);
                        ?>

                    </td>
    				<td><?php echo timestamp_to_date($post->investment_date); ?></td>
    			</tr>
    			<tr>
    				<td>2</td>
    				<td>
                        <?php
                            $default_message='Investment Institution Name';
                            $this->languages_m->translate('investment_institution_name',$default_message);
                        ?>
                    </td>
    				<td><?php echo $post->investment_institution_name; ?></td>
    			</tr>
    			<tr>
    				<td>3</td>
    				<td>Withdrawal Account</td>
    				<td><?php echo $withdrawal_account_options[$post->withdrawal_account_id]; ?></td>
    			</tr>
    			<tr>
    				<td>4</td>
    				<td>
                        <?php
                            $default_message='Investment Amount';
                            $this->languages_m->translate('investment_amount',$default_message);
                        ?>
                     (<?php echo $this->group_currency; ?>)</td>
    				<td><?php echo $this->group_currency.' '.number_to_currency($post->investment_amount); ?></td>
    			</tr>
    			<?php if($post->description): ?>
	    			<tr>
	    				<td>5</td>
	    				<td>
                            <?php
                                $default_message='Description';
                                $this->languages_m->translate('description',$default_message);
                            ?>
                        </td>
	    				<td><?php echo $post->description; ?></td>
	    			</tr>
	    		<?php endif; ?>
    		</tbody>
    	</table>
    </div>
</div>
<div class="portlet-body form">
    <?php echo form_open($this->uri->uri_string(),'class="m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed form_submit m-form--state top_up_investment" id="top_up_investment" role="form"'); ?>
    <span class="error"></span>
        <div class="form-group m-form__group row pt-0 m--padding-10">
            <div class="col-sm-6 m-form__group-sub">
                <label><?php echo translate('Top Up Date');?><span class="required">*</span></label>
                <?php echo form_input('investment_date',$this->input->post('investment_date')?timestamp_to_datepicker(strtotime($this->input->post('investment_date'))):(timestamp_to_datepicker($post->investment_date)?timestamp_to_datepicker($post->investment_date):timestamp_to_datepicker(time())),'class="form-control datepicker" data-date-end-date="0d" readonly style="width:100%"' );?> 
            </div>
            <?php echo form_hidden('id',$post->id); ?>
            <div class="col-sm-6 m-form__group-sub m-input--air">
                <label><?php echo translate('Account');?> <span class="required">*</span></label>
                 <?php echo form_dropdown('withdrawal_account_id',array(''=>'--'.translate('Select account').'--')+translate($account_options),$this->input->post('withdrawal_account_id')?$this->input->post('withdrawal_account_id'):$post->withdrawal_account_id,'class="m-select2 form-control " id ="account" data-placeholder="Select..."  ');?>
            </div>

            <div class="col-sm-12 m-form__group-sub pt-0 m--padding-10">
                <label><?php echo translate('Top Up Amount');?>(<?php echo $this->group_currency; ?>)<span class="required">*</span></label>
                <?php echo form_input('top_up_amount',$this->input->post('top_up_amount'),'  class="form-control currency m-input--air" id="top_up_amount" autocomplete="off"  placeholder="Top Up Amount"'); ?> 
            </div>
        </div>
        <div class="col-md-12 m-form__group-sub pt-0 m--padding-10">
            <span class="float-lg-right float-md-right float-sm-right float-xl-right">
                <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="top_up_button" type="submit">
                    <?php echo translate('Save Changes');?>
                </button>
                &nbsp;&nbsp;
                <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_top_up_button">
                    <?php echo translate('Cancel');?>
                </button> 
            </span>
        </div>
    <?php echo form_close(); ?>
</div>
<script>
$(document).ready(function(){
    $('.m-select2').select2({
        placeholder:{
            id: '-1',
            text: "--Select option--",
        }, 
    });

    $('#top_up_investment').on('submit',function(e){
        e.preventDefault();
        mApp.block('#top_up_investment', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Processing...'
        });
        var form = $(this);
        RemoveDangerClass(form);
        $('#top_up_investment .error').html('').hide();
        $.ajax({
            type: "POST",
            url: '<?php echo base_url("ajax/money_market_investments/top_up"); ?>',
            data: form.serialize(),
            success: function(response) {
                if(isJson(response)){
                    var data = $.parseJSON(response);
                    if(data.status == 1){
                        toastr['success'](data.message,'Top Up Successfully Recorded');
                        window.location.href = data.refer;
                    }else{
                        $('#top_up_investment .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').show();
                        if(data.validation_errors){
                            $.each(data.validation_errors, function( key, value ) {
                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                $('#top_up_investment input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                $('#top_up_investment select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                            });
                        }
                    }
                    
                }else{
                    $('#top_up_investment .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There was a problem processing your request, please try again</div>').show();
                }
                mApp.unblock('#top_up_investment');
            }
        });
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
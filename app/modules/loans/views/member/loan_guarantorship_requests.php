<div class="loan_guarantorship_requests">
    <span class="error"></span>
    <ul class="nav nav-tabs  m-tabs-line m-tabs-line--success" role="tablist">
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link  active show" data-toggle="tab" href="#pending_requests" role="tab" aria-selected="false"><i class="la la-hourglass-o"></i>
                <?php echo translate('Pending'); ?>
            </a>
        </li>
        
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link declined_requests" data-toggle="tab" href="#declined_requests" role="tab" aria-selected="true"><i class="la la-ban"></i>
                <?php echo translate('Declined'); ?>
            </a>
        </li>
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link approved_requests" data-toggle="tab" href="#approved_requests" role="tab" aria-selected="true"><i class="la la-check-circle"></i>
                <?php echo translate('Approved'); ?>
            </a>
        </li>
    </ul>                        
    <div class="tab-content">
        <div class="tab-pane " id="my_application" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane active show" id="pending_requests" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="approved_requests" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="declined_requests" role="tabpanel" style="min-height: 180px;">
            
        </div>
    </div> 
</div>

<div class="modal fade" id="decline_guarantorship_request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <?php echo translate('Decline Guarantorship Request'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="decline_guarantorship_request"'); ?>
                    <div class="form-group">
                        <label for="message-text" class="form-control-label">
                            <?php echo translate('Reason'); ?>:
                        </label>
                        <textarea class="form-control" id="decline_reason"></textarea>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <?php echo translate('Cancel'); ?>:
                </button>
                <button type="button" class="btn btn-primary decline">
                    <?php echo translate('Decline'); ?>:
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.m-select2-append').select2({
        width: "100%",
        placeholder:{
            id: '-1',
            text: "--Select option--",
        }, 
    });

    $("a[href='#pending_requests']").on('shown.bs.tab', function(e) {
       if($.trim($('#pending_requests').html()) == ""){
            load_pending_member_guarantorship_requests();
        }
    });

    $("a[href='#approved_requests']").on('shown.bs.tab', function(e) {
       if($.trim($('#approved_requests').html()) == ""){
            load_approved_member_guarantorship_requests();
        }
    });

    $("a[href='#declined_requests']").on('shown.bs.tab', function(e) {
       if($.trim($('#declined_requests').html()) == ""){
            load_declined_member_guarantorship_requests();
        }
    });

    $(document).on('click','.approve',function(e){
        e.preventDefault();
        var row = $(this).parent().parent().parent();
        var id = $(this).attr('id');
        bootbox.prompt({
            title: "Enter your password to approve this request",
            inputType: 'password',
            required: true,
            callback: function(password){
                if(password){
                    mApp.block(row, {
                        overlayColor: 'grey',
                        animate: true,
                        type: 'loader',
                        state: 'primary',
                        message: 'Approving...'
                    });
                    $.post('<?php echo base_url("ajax/loans/approve_loan_guarantorship_request"); ?>',{'password':password,'id':id},function(data){
                        if(isJson(data)){
                            var response = $.parseJSON(data);
                            if(response.status == 1){
                                toastr['success']('You have successfully approved the guarantorship request.','Guarantorship request approved successfully');
                                load_pending_member_guarantorship_requests();
                                load_approved_member_guarantorship_requests();
                            }else{
                                $('#pending_requests .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                            }
                        }else{
                            $('#pending_requests .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error occured processing your request, please try again</div>');
                        }
                        mApp.unblock(row);
                    });
                }
            }
        });
    });

    $(document).on('click','.decline',function(e){
        e.preventDefault();
        if($('#decline_reason').val() == ''){
            $('#decline_reason').parent().addClass('has-danger').append('<div class="form-control-feedback">Please give a reason</div>');
        }else{
            var row = $('#decline').parent().parent().parent();
            var id = $('#decline').attr('data-id');
            var decline_reason = $('#decline_reason').val();
            bootbox.prompt({
                title: "Enter your password to decline this request",
                inputType: 'password',
                required: true,
                callback: function(password){
                    if(password){
                        $('#decline_guarantorship_request_modal .close').trigger('click');
                        mApp.block(row, {
                            overlayColor: 'grey',
                            animate: true,
                            type: 'loader',
                            state: 'primary',
                            message: 'Declining...'
                        });
                        $.post('<?php echo base_url("ajax/loans/decline_loan_guarantorship_request"); ?>',{'decline_reason':decline_reason,'password':password,'id':id},function(data){
                            if(isJson(data)){
                                var response = $.parseJSON(data);
                                if(response.status == 1){
                                    toastr['success']('You have successfully declined the guarantorship request.','Guarantorship request declined successfully');
                                    load_pending_member_guarantorship_requests();
                                    load_declined_member_guarantorship_requests();
                                }else{
                                    $('#pending_requests .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                                }
                            }else{
                                $('#pending_requests .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error occured processing your request, please try again</div>');
                            }
                            mApp.unblock(row);
                        });
                    }
                }
            });
        }
        
    });
});

$(window).on('load',function(){
    load_pending_member_guarantorship_requests();
});

// var controller = "<?php echo $this->uri->segment(1, 0); ?>";
// var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
// var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
// var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_pending_member_guarantorship_requests(){
    mApp.block('#pending_requests', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loans/get_pending_member_guarantorship_requests"); ?>',
        dataType : "html",
            success: function(response) {
                $('#pending_requests').html(response).prepend('<span class="error"></span>');
                mApp.unblock('#pending_requests');
        }
    });
}

function load_approved_member_guarantorship_requests(){
    mApp.block('#approved_requests', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loans/get_approved_member_guarantorship_requests"); ?>',
        dataType : "html",
            success: function(response) {
                $('#approved_requests').html(response);
                mApp.unblock('#approved_requests');
        }
    });
}

function load_declined_member_guarantorship_requests(){
    mApp.block('#declined_requests', {
        overlayColor: 'grey',
        animate: true,
        type: 'loader',
        state: 'primary',
        message: 'Getting...'
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/loans/get_declined_member_guarantorship_requests"); ?>',
        dataType : "html",
            success: function(response) {
                $('#declined_requests').html(response);
                mApp.unblock('#declined_requests');
        }
    });














   
}

</script>
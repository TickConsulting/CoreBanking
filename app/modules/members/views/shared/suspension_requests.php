<div class="suspension_requests">
    <span class="error"></span>
    <ul class="nav nav-tabs  m-tabs-line m-tabs-line--success" role="tablist">
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link active show" data-toggle="tab" href="#pending_requests" role="tab" aria-selected="false"><i class="la la-hourglass-o"></i>
                <?php echo translate('Pending'); ?>
            </a>
        </li>
        <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link approved_requests" data-toggle="tab" href="#approved_requests" role="tab" aria-selected="true"><i class="la la-check-circle"></i>
                <?php echo translate('Approved'); ?>
            </a>
        </li>
         <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link declined_requests" data-toggle="tab" href="#declined_requests" role="tab" aria-selected="true"><i class="la la-ban"></i>
                <?php echo translate('Declined'); ?>
            </a>
        </li>
    </ul>                        
    <div class="tab-content">
        <div class="tab-pane active show" id="pending_requests" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="approved_requests" role="tabpanel" style="min-height: 180px;">
        </div>
        <div class="tab-pane" id="declined_requests" role="tabpanel" style="min-height: 180px;">
            
        </div>
    </div> 
</div>

<div class="modal fade" id="suspension_request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header" style="display: block;">
            <h5 class="modal-title" id="exampleModalLabel">
                <?php echo translate('Suspension Request'); ?>
                <span class="badge_holder">
                </span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body" style="min-height: 150px;">
            
          
            <div class="table-responsive pending_approval_requests" id="pending_approvals" style="display: none;">

            </div>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript">     

    $(document).ready(function(){
       $(document).on('click','.suspension_request',function(){
            $.ajax({
                url:'<?php echo site_url('ajax/members/pending_suspend_member_approvals/') ?>'+$(this).attr('id'),
                type:'GET',
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response); 
                        console.log(result.data)
                        mApp.unblock('#suspension_request_modal .modal-body');
                        $('.request_details').slideDown();
                        $('#pending_approvals').html(result.data).slideDown();
                    }else{
                        /*$('#suspension_request_modal .badge_holder').html('<span class="m-badge m-badge--success m-badge--wide">Approved</span>');*/
                        $('#suspension_request_modal .badge_holder').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response.message+'</div>');
                    }
                }
            });
       });

        $('#suspension_request_modal').on('shown.bs.modal',function(){
            mApp.block('#suspension_request_modal .modal-body', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Suspension requests ...'
            });
        });

        $('#suspension_request_modal').on('hidden.bs.modal',function(){
            $('#pending_approvals').slideUp('fast');
        });



    }); 

    $(window).on('load',function(){
        loan_pending_member_suspesnions();
    });

    function loan_pending_member_suspesnions(){
        mApp.block('#pending_requests', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/members/pending_member_suspensions"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#pending_requests').html(response);
                    mApp.unblock('#pending_requests');
            }
        });
    }   

</script>

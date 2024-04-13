<div class="row">
    <div class="col-md-12">
        
        <div id="loan_listing_status_action"></div>
               
    </div>
</div>

<script>
    $(window).on('load',function(){
        loan_listing_status_action();
    });

    function loan_listing_status_action(){
        App.blockUI({
            target: '#loan_listing_status_action',
            overlayColor: 'grey',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/members/ajax_pending_loan_listing/".$this->data['loan_application_id'].""); ?>',
            dataType : "html",
                success: function(response) {
                    $('#loan_listing_status_action').html(response);
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    App.unblockUI('#loan_listing_status_action');
                }
            }
        );
    }

</script>
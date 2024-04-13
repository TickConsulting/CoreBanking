<div class="row">
    <div class="col-md-12">
        <div id="loan_requests_declined_listing">
           
        </div>               
    </div>
</div>

<script>

    $(window).on('load',function(){
        loan_requests_declined_listing();
    });
    function loan_requests_declined_listing(){
        App.blockUI({
            target: '#loan_requests_declined_listing',
            overlayColor: 'grey',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/loan_applications/pending_bank_approval_loans"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#loan_requests_declined_listing').html(response);
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    App.unblockUI('#loan_requests_declined_listing');
                }
            }
        );
    }

</script>
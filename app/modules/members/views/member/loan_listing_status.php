<div class="row">
    <div class="col-md-12">
        <div id="loan_requests_listing"></div>
               
    </div>
</div>

<script>

    $(document).ready(function(){

    });

    $(window).on('load',function(){

        loan_requests_listing();

    });


    function loan_requests_listing(){
        App.blockUI({
            target: '#loan_requests_listing',
            overlayColor: 'grey',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/members/ajax_loan_guarantor_listing"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#loan_requests_listing').html(response);
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    App.unblockUI('#loan_requests_listing');
                }
            }
        );
    }

</script>
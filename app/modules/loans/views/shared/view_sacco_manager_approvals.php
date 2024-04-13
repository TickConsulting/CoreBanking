<div class="row">
    <div class="col-md-12">
        <div id="loan_requests_listing"></div>
               
    </div>
</div>

<script>
    $(window).on('load',function(){
        loan_requests_listing();
    });
    function loan_requests_listing(){
        App.blockUI({
            target: '#loan_requests_listing',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            url: '<?php echo base_url("ajax/loans/ajax_loan_requests_for_sacco_manager"); ?>',
                success: function(response) {
                    if(isJson(response)){
                        var result = $.parseJSON(response);                       
                        if(result.status == '200'){
                            $('#loan_requests_listing').html(result.html); 
                        }else if(result.status == '0'){
                           // $('#loan_type_details').html(result.message);  
                        }
                    }else{
                        alert(response);
                    }                          
                    App.unblockUI('#loan_requests_listing');
                }
            }
        );
    }

    function loan_application_details(){
        var loan_application_id = '<?php echo $this->uri->segment(4) ?>';
        App.blockUI({
            target: '#loan_application_form_holder',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "POST",
            data:{loan_application_id: loan_application_id},
            url: '<?php echo base_url("ajax/loans/ajax_loan_details"); ?>',
                success: function(response){
                    if(isJson(response)){
                        var result = $.parseJSON(response);                       
                        if(result.status == '200'){
                            $('#loan_application_form_holder').html(result.html); 
                            $('#supervisory_form_holder').slideDown();
                            $('#form_action_holder').slideDown();
                        }else if(result.status == '0'){
                           // $('#loan_type_details').html(result.message);  
                        }
                    }else{
                        alert(response);
                    }                          
                    App.unblockUI('#invoice_body');
                }
            }
        );
    }

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

</script>
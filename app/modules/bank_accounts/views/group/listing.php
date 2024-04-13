
<div id="bank_accounts_listing">
</div>
<script>
   

    $(window).on('load',function(){
        load_bank_accounts_listing();
    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_bank_accounts_listing(){
        mApp.block('#bank_accounts_listing',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/bank_accounts/ajax_get_bank_accounts_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "json",
                success: function(response) {
                    if(response.status == 1){
                        $('#bank_accounts_listing').html(response.html);
                        $('.select2').select2({width:"100%"});
                        $('.date-picker').datepicker({autoclose:true});
                    }else if(response.status == '202'){
                        Toastr.show("Session Expired",response.message,'error');
                        window.location.href = response.refer;
                    }else{
                        Toastr.show("Error occurred",response.message,'error');
                    }
                    mApp.unblock('#bank_accounts_listing');
                },
                error: function(){
                    Toastr.show("Error occurred","Could not complete the process at the moment",'error');
                },
                always: function(){
                    Toastr.show("Error occurred","Error detected. Engineers on site",'error');
                }
            }
        );
    }

</script>    
       

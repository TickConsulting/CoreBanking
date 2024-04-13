 
<div id="petty_cash_accounts_listing">
</div>   

<script>

     $(document).ready(function(){

        $(document).on('click','.confirmation_link',function(){
            var element = $(this);
            bootbox.confirm({
                message: "Are you sure you want to this?",
                // title: "Before you proceed",
                callback: function(result) {
                    if(result==true){
                        if (result === null) {
                            return true;
                        }else{
                            var href = element.attr('href');
                            window.location = href;
                        }
                    }else{
                        return true;
                    }
                }
            });
            return false;
        });

        $(document).on('click','.prompt_confirmation_message_link',function(e){
            e.preventDefault();
            var id = $(this).attr('id');
            swal({
                title: "Are you sure?", text: "You won't be able to revert this!", type: "warning", showCancelButton: !0, confirmButtonText: "Yes, delete it!", cancelButtonText: "No, cancel!", reverseButtons: !0
            }).then(function(e) {
                if(e.value == true){
                    bootbox.prompt({
                        title: "Input Your password to delete!",
                        inputType: 'password',
                        callback: function (result) {
                            mApp.block('.'+id+'_active_row', {
                                overlayColor: 'grey',
                                animate: true,
                                type: 'loader',
                                state: 'primary',
                                message: 'processing..'
                            });
                            $.ajax({
                                type:'POST',
                                url:'<?php echo site_url('group/petty_cash_accounts/delete') ?>',
                                data:{'id':id, 'password':result},
                                success: function(response){
                                    if(isJson(response)){
                                        var data = $.parseJSON(response)
                                        if(data.status == '1'){
                                            mApp.unblock('.'+id+'_active_row');
                                            $('.'+id+'_active_row').hide();
                                            swal("success",data.message, "success")
                                        }else{
                                            mApp.unblock('.'+id+'_active_row');
                                            swal("Cancelled",data.message, "error")
                                        }
                                    }else{
                                        mApp.unblock('.'+id+'_active_row');
                                        swal("Cancelled", "Could not delete your petty cash account :)", "error")   
                                    }
                                },
                                error: function(){
                                    mApp.unblock('.'+id+'_active_row');
                                    swal("Cancelled", "Could not delete your petty cash account :)", "error")
                                },
                            });
                        }
                    });
                }else{
                    swal("Cancelled", "Your petty Cash account is safe :)", "error")
                }
            })
        });


    });

    $(window).on('load',function(){
        load_petty_cash_accounts_listing();
    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_petty_cash_accounts_listing(){
        mApp.block('#petty_cash_accounts_listing', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Processing...'
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("group/petty_cash_accounts/ajax_get_petty_cash_accounts_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#petty_cash_accounts_listing').html(response);
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#petty_cash_accounts_listing');
                }
            }
        );
    }
    
</script>

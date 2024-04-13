<div id="group_account_managers_listing">
</div>
<script>

    $(document).ready(function(){
        $(document).on('click','.confirmation_link',function(){
            var element = $(this);
            bootbox.confirm({
                message: "Are you sure you want to this ?",
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
    });

    $(window).on('load',function(){
        load_group_managers_listing();
    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_group_managers_listing(){
        mApp.block('#group_account_managers_listing', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Processing...'
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("group/group_account_managers/ajax_get_group_account_managers_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#group_account_managers_listing').html(response);
                    //$('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    //$('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#group_account_managers_listing');
                }
            }
        );
    }



</script>
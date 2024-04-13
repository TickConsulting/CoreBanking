<div class="row">
    <div class="col-md-12"> 
        <?php
          $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1'; ?>
            <div class="btn-group margin-bottom-20 search-button">
                <a href="<?php echo site_url('group/assets/listing').$query; ?>" class="btn btn-sm btn-primary m-btn m-btn--icon">
                    <span>
                        <i class="la la-file-excel-o"></i>
                        <span>
                            <?php echo translate('Export To Excel'); ?>
                        </span>
                    </span>
                </a>
            </div>
    </div>
</div>

<div id="assets_listing" class="table-responsive">

</div>

<script>

$(document).ready(function(){
    $(document).on('click','.confirmation_link',function(){
        var element = $(this);
        bootbox.confirm({
            message: "Are you sure you want to proceed ?",
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

    load_assets_listing();

});

var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_assets_listing(){
    mApp.block('#assets_listing',{
        overlayColor: 'white',
        animate: true
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/assets/get_assets_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
        dataType : "html",
            success: function(response) {
                $('#assets_listing').html(response);
                $('.select2').select2({width:"100%"});
                $('.date-picker').datepicker({autoclose:true});
                mApp.unblock('#assets_listing');
            }
        }
    );
}

</script>
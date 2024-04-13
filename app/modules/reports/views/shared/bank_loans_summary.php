<div class="row margin-bottom-20">
    <div class="col-xs-12">
        <?php
            $search_string = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
        ?>
        <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().$search_string;?>"><i class='fa fa-file'></i> Generate Excel</a>
    </div>
</div>
<div class="clearfix"></div>
<hr>
<div id="statement_paper">
    <div id="bank_loans_summary" class="pt-3">

    </div>
    <hr>
    <div id="statement_footer" style="display: none;" >
        <p style="text-align:center;">© <?php echo date('Y')?> . This statement was issued with no alteration </p>
        <p style="text-align:center;">
            <strong>Powered by:</strong>
            <br>
            <img width="150px" src="<?php echo site_url('uploads/logos/'.$this->application_settings->paper_header_logo);?>" alt="<?php echo $this->application_settings->application_name;?> Logo" ?="">
        </p>
    </div>
</div>

<script>

    $(document).ready(function(){

    });

    $(window).on('load',function(){
        load_bank_loans_summary();
    });


var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_bank_loans_summary(){
        mApp.block('#bank_loans_summary',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_bank_loans_summary/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#bank_loans_summary').html(response);
                    $('#statement_footer').slideDown();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#bank_loans_summary');
                }
            }
        );
    }

</script>

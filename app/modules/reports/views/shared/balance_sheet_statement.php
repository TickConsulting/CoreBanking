<?php 
    $query = $_SERVER['QUERY_STRING']?'?generate_excel=1':'?generate_excel=1';
    echo '
    <div class="btn-group margin-bottom-20 search-button">
        <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Balance Sheet Statement">
            Export To Excel <i class="fa fa-file-excel-o"></i>
        </a>
    </div>';
?>
<div id="balance_sheet_statement">

</div>

<script>

    $(document).ready(function(){

    });

    $(window).on('load',function(){
        load_balance_sheet_statement();
    });


    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";


    function load_balance_sheet_statement(){
        mApp.block('#balance_sheet_statement',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_balance_sheet_statement/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#balance_sheet_statement').html(response);
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#balance_sheet_statement');
                }
            }
        );
    }
</script>


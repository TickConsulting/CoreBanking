<div class="row">
    <div class="col-md-12"> 
        <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
            
            echo '
            <div class="btn-group margin-bottom-20 search-button">
                <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Loan Guarantors Summary">
                    Export To Excel <i class="fa fa-file-excel-o"></i>
                </a>
            </div>';
            
        ?>
    </div>
</div>
<div id="loan_guarantor_summary">
</div>

<script>

    $(document).ready(function(){

    });

    $(window).on('load',function(){
        load_contributions_summary();
    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_contributions_summary(){
        App.blockUI({
            target: '#loan_guarantor_summary',
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_loan_guarantor_summary/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#loan_guarantor_summary').html(response);
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    App.unblockUI('#loan_guarantor_summary');
                }
            }
        );
    }

</script>
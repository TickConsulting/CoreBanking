<div class="row">
    <div class="col-md-12">
        <?php
            echo '
                <div class="btn-group margin-bottom-20 search-button">
                    <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> Search
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">';
                        echo form_open(current_url(),'method="GET" class="filter"');
                        echo '
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label ">Debtors</label>
                                    <div class="">';
                                        echo form_dropdown('debtor_id[]',array()+$this->group_debtor_options,$this->input->get('debtor_id'),'class="form-control select2" multiple="multiple"');
                                    echo '
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-filter"></i> Filter</button>
                                <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                            </div>';

                        echo form_close();
                        echo '
                    </div>
                </div>';
        ?>

        <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
            echo '
            <div class="btn-group margin-bottom-20 search-button">
                <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Deposits">
                    Export To Excel <i class="fa fa-file-excel-o"></i>
                </a>
            </div>';
        ?>
    </div>
</div>

<div id="debtors_listing">

</div>

<script>

$(window).on('load',function(){
    load_debtor_loan_listing();
});

var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

function load_debtor_loan_listing(){
    App.blockUI({
        target: '#debtors_listing',
        overlayColor: 'white',
        animate: true
    });
    $.ajax({
        type: "GET",
        url: '<?php echo base_url("ajax/debtors/list_debtors/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
        dataType : "html",
            success: function(response) {
                $('#debtors_listing').html(response);
                $('input[type=checkbox]').uniform();
                $('.select2').select2({width:"100%"});
                $('.date-picker').datepicker({autoclose:true});
                App.unblockUI('#debtors_listing');
            }
        }
    );
}
</script>
<div class="row">
    <div class="col-md-12"> 
      <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
        ?> 
        <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.$query;?>" target="_blank"><i class='fa fa-file'></i>&nbsp;
            <?php echo translate('Generate Excel'); ?>
        </a>
    </div>
</div>
<div class="clearfix"></div>
<hr>
<div id="statement_paper" style="padding: 3% !important;">
    <div id="account_balances">
    </div>
</div>

<script>

    $(document).ready(function(){

    });

    $(window).on('load',function(){
        load_account_balances();
    });


var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";


    function load_account_balances(){
        mApp.block('#account_balances',{
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_account_balances/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#account_balances').html(response);
                    $('#statement_footer').slideDown();
                    $('input[type=checkbox]').uniform();
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#account_balances');
                }
            }
        );
    }
</script>
       
       

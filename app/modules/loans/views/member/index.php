<style type="text/css">
    .dash_cont .fin_summ .fin_summ_entity .fin_summ_title {
        font-size: 13px;
    }
    .dash_cont .fin_summ .fin_summ_entity .fin_summ_amount {
        font-size: 16px;
    }
</style>
<div class="dash_cont wallet_summary">
    <h4><?php echo translate('Loans Summary')?></h4>
    <p><?php echo translate('Your loan account summary')?></p>
    <div class="row fin_summ">
        <div class="col-md-12 mt-2 mb-2">
            <div class="row">
                <div class="col-md-3">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Loan Limit');?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?> </span><?php echo number_to_currency($loan_limit);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-information-outline"></i></span><?php echo translate('Maximum loan to borrow');?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Outstanding Balances')?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?></span> <?php echo number_to_currency($loan_balances);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-cash-multiple"></i></span> <?php echo translate('Current loan balances');?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Total Borrowed')?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?></span> <?php echo number_to_currency($tota_loan_amount);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-cash-refund"></i></span> <?php echo translate('Total loan borrowed');?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fin_summ_entity">
                        <div class="fin_summ_title"><?php echo translate('Total Repaid')?></div>
                        <div class="fin_summ_amount"><span><?php echo $this->group_currency; ?></span> <?php echo number_to_currency($total_loan_repaid);?></div>
                        <div class="fin_summ_descr text-success">
                            <span><i class="mdi mdi-cash-refund"></i></span> <?php echo translate('Total loan repayments');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row fin_chart">
        <div class="col-md-12 pt-5" id="fin_summ_chart_series_canvas" style="min-height: 250px;">
            <canvas id="fin_summ_chart_series"></canvas>
        </div>
    </div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>
<script>
    $(document).ready(function(){
        mApp.block('#fin_summ_chart_series_canvas',{
            overlayColor: 'white',
            animate: true
        });
    });
    $(window).on('load',function() {
        load_deposits_dashboard_bank_chart();
    });
    
    var isMobile = false;
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
        isMobile = true;
    }
    function load_deposits_dashboard_bank_chart(){
        // $('#fin_summ_chart_series').remove(); 
        // $('#fin_summ_chart_series_canvas').append('<canvas id="fin_summ_chart_series" height="180"></canvas>');
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_member_monthly_loan_repayments"); ?>',
            dataType : "html",
            success: function(response) {
                if(isJson(response)){
                    res = $.parseJSON(response);
                    var financial_summary_chart_ctx = document.getElementById("fin_summ_chart_series").getContext('2d');
                    var cs_gradient = financial_summary_chart_ctx.createLinearGradient(0, 0, 0, 600), border_color='', point_border_color='', point_background_color='';
                    if(isMobile){
                        document.getElementById("fin_summ_chart_series").height = 300;
                    }else{
                        document.getElementById("fin_summ_chart_series").height = 100;
                    }
                    
                    <?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ ?>
                        cs_gradient.addColorStop(0, '#d8511f');
                        border_color = '#d8511f';
                        point_border_color = 'rgba(255,255,255,1)';
                        point_background_color = '#d8511f';
                    <?php } else if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                        cs_gradient.addColorStop(0, '#33911a');
                        border_color = '#33911a';
                        point_border_color = 'rgba(255,255,255,1)';
                        point_background_color = '#33911a';
                    <?php } else{ ?>
                        cs_gradient.addColorStop(0, '#00abf2');
                        border_color = '#00abf2';
                        point_border_color = 'rgba(255,255,255,1)';
                        point_background_color = '#00abf2';
                    <?php } ?>
                    cs_gradient.addColorStop(1, 'rgba(255,255,255,0)');

                    var contribution_chart_series = new Chart(financial_summary_chart_ctx, {
                        type: 'bar',
                        data: {
                            labels: res.months,
                            datasets: [
                                {
                                    label: "<?php echo translate('Total Deposits')?>",
                                    data: res.amounts,
                                    fill: true,
                                    borderColor: border_color,
                                    backgroundColor: cs_gradient,
                                    pointBorderColor: point_border_color,
                                    pointBackgroundColor: point_background_color,
                                    hoverBackgroundColor: cs_gradient,
                                    pointRadius: 5,
                                    pointHoverRadius: 8,
                                    pointBorderWidth: 3,
                                    pointStyle: 'rectRounded'
                                }
                            ]
                        },
                        options: {
                            legend: {
                                display: false,
                                position: 'bottom',
                            },
                            title: {
                                display: true,
                                position: 'bottom',
                                text: "<?php echo translate('Total Monthly Loan Repayments')?>"
                            },
                            elements: {
                                line: {
                                    tension: 0
                                }
                            },
                            tooltips: {
                                enabled: true,
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return "<?php echo $this->group_currency; ?> " + Number(tooltipItem.yLabel).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    }
                                }
                            },
                            scales: {
                                yAxes: [{
                                    display: true,
                                    gridLines: {
                                        display: true,
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        beginAtZero:false,
                                        display: true
                                    }
                                }],
                                xAxes: [{
                                    display: true,
                                    gridLines: {
                                        display: true,
                                        drawBorder: false,
                                    }
                                }]
                            }
                        }
                    });
                }
                mApp.unblock('#fin_summ_chart_series_canvas');
            }
        });
        //end: Balances chart
    }
</script>

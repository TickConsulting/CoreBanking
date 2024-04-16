<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">
                <?php if(!empty($transactions)){ ?>
                    <?php echo form_open('admin/billing/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> STK Requests</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
                         <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed table-searchable">
                            <thead>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <?php
                                        foreach ($columns as $key => $column) {
                                            if(!in_array($column, $ignore_columns)){
                                                echo '<th>'.$column.'</th>'; 
                                            } 
                                        }
                                    ?>
                                    <th>
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  $i=0; foreach ($transactions as $key => $value): 
                                ?>
                                    <tr>
                                        <td><?php echo ++$i;?></td>
                                        <?php foreach ($columns as $key => $column) {
                                            if(!in_array($column, $ignore_columns)){
                                                if($column=='paid-in'){
                                                    echo '<td class="text-right">'.number_to_currency($value[$column]).'</td>';
                                                }else if($column=='withdrawn'){
                                                    echo '<td class="text-right">'.number_to_currency($value[$column]).'</td>';
                                                }else{
                                                   echo '<td>'.$value[$column].'</td>'; 
                                               }
                                            }
                                        }?>
                                        <td id="<?php echo $value['receipt-no-']?>"><i class="fa fa-spinner fa-spin"></i></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>

                        <div class="clearfix"></div>
                        <div class="row col-md-12">
                        <?php 
                            if( ! empty($pagination['links'])): 
                            echo $pagination['links']; 
                            endif; 
                        ?>  
                        </div>
                        <div class="clearfix"></div>
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No B2B Transactions to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    $(document).ready(function(){
        var transactions = '<?php echo json_encode($transactions);?>';
        //console.log($.parseJSON(transactions));
        $.each($.parseJSON(transactions), function( index, value ) {
            $.ajax({
                type: "POST",
                url: '<?php echo base_url("admin/safaricom/reconcile_disbursements"); ?>',
                data: {'transaction' : value},
                dataType: 'json',
                success: function(response){
                    if(response.hasOwnProperty('status')){
                        if(response.status=='1'){
                            console.log('success');
                            $('#'+response.id).html('<i class="fa fa-check" style="color:green"></i>');
                        }else{
                            $('#'+response.id).html('<i class="fa fa-close" style="color:red"></i>');
                        }
                    }
                },
                error:function(){
                },
                always:function(){

                }
                //dataType: dataType
            });
        });
    });
</script>
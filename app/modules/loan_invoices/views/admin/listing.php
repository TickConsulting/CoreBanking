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
                <div class="btn-group search-button hold-on-click">
                    <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> Search
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">
                        <?php echo form_open(current_url(),'method="GET" class="filter"');?>
                            <div class="form-body">
                                <div class="form-group">
                                    <label>Investment Group Name</label>
                                    <div class="input-group">
                                        <?php echo form_dropdown('group_id[]',array(''=>'All')+$group_options,$this->input->get('group_id'),'class="form-control select2 input-sm" placeholder="Investment Group Name" multiple="multiple"'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label ">Send Date Range</label>
                                    <div class="">
                                        <div class="input-group date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                            <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control" '); ?>
                                            <span class="input-group-addon"> to </span>
                                            <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control" '); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-search"></i></button>
                                <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                            </div>

                        <?php echo form_close();?>

                                    
                       
                    </div>
                </div>
                <?php if(!empty($posts)){ ?>
                    <?php echo form_open('group/loans/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Contributions</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
                         <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed table-searchable">
                            <thead>
                                <tr>
                                    <th width='2%'>
                                        #
                                    </th>
                                    <th>
                                       Group
                                    </th>
                                    <th>
                                       Member Name
                                    </th>
                                    <th>
                                        Send Date
                                    </th>
                                    <th class='text-right'>
                                        Amount
                                    </th>  
                                    <th>
                                        Invoice Type
                                    </th>
                                    <th>
                                        Notifications
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><?php echo $i+1;?></td>
                                        <td>
                                            <?php echo $group_options[$post->group_id];?>
                                        </td>
                                        <td>
                                            <?php echo $post->first_name.' '.$post->last_name; ?>
                                        </td>
                                        <td>
                                          <?php echo timestamp_to_date_and_time($post->created_on);?>  
                                        </td>
                                        <td class='text-right'><?php echo number_to_currency($post->amount_payable); ?></td>
                                        <td>
                                            <?php 
                                                if($post->invoice_type==1){
                                                    echo "<span class='label label-default'>Invoice</span>";
                                                }else if($post->invoice_type==2){
                                                    echo "<span class='label label-success'>Fine Invoice</span>";
                                                }else if($post->invoice_type==3){
                                                     echo "<span class='label label-primary'>Outstanding balance fine invoice</span>";
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->notification_created){
                                                    echo "<span class='label label-success'>Not.</span>";
                                                }if($post->email_sent){
                                                    echo "<span class='label label-success'>Email</span>";
                                                }if($post->sms_sent){
                                                    echo "<span class='label label-success'>SMS</span>";
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php $i++;
                                    endforeach; ?>
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

                        <div class="clearfix"></div>
                    <?php echo form_close(); ?>
                    <?php }else{ ?>
                        <div class="alert alert-info">
                            <h4 class="block">Information! No records to display</h4>
                            <p>
                                There are no invoices or fines sent.
                            </p>
                        </div>
                    <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12"> 
        <div class="btn-group margin-bottom-20 search-button">
            <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> Search
                <i class="fa fa-angle-down"></i>
            </button>
            <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">
                <?php echo form_open(current_url(),'method="GET" class="filter"');?>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label ">Loan  Date Range</label>
                            <div class="">
                                <div class="input-group date-picker input-daterange" data-date="" data-date-format="dd-mm-yyyy">
                                    <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control" '); ?>
                                    <span class="input-group-addon"> to </span>
                                    <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control" '); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label ">Accounts</label>
                            <div class="">
                                <?php
                                    echo form_dropdown('accounts[]',array()+$account_options,$this->input->get('accounts'),'class="form-control select2" multiple="multiple"');
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label ">
                                <?php
                                    $default_message='Member';
                                    $this->languages_m->translate('member',$default_message);
                                ?>

                            </label>
                            <div class="">
                            <?php
                                echo form_dropdown('member_id[]',array()+$this->group_member_options,$this->input->get('member_id'),'class="form-control select2" multiple="multiple"');
                            ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-filter"></i> 
                                <?php
                                    $default_message='Filter';
                                    $this->languages_m->translate('filter',$default_message);
                                ?>
                        </button>
                        <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                        <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                    </div>

                <?php echo form_close();?>
            </div>
        </div>
        <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
            echo '
            <div class="btn-group margin-bottom-20 search-button">
                <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.site_url('group/loans/listing').$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Deposits">
                    Export To Excel <i class="fa fa-file-excel-o"></i>
                </a>
            </div>';
        ?>
    </div>
</div>

<div id="pending_loans_listing">
    <table class="table table-condensed table-striped table-hover table-header-fixed ">
        <thead>
            <tr>
                <th width="2%">
                    #
                </th>
                <th>
                    Loan Details
                </th>
                <th>
                    Guarantor Details
                </th>
                <th>
                    Signatory Details
                </th>                 
            </tr>
        </thead>
        <tbody> <?php 
           $i = 0;         
           foreach($loan_applications as $loan_application):
            ?>
                <tr>
                    <td><?php echo ++$i;?></td>
                    <td style="display: none;"><?php echo  $loan_application->id ?></td>
                    <td>
                        <strong>  Loan Name : </strong><?php echo $loan_types[$loan_application->loan_type_id]?><br>
                        <strong>  Member Name :  </strong> <?php echo $members[$loan_application->member_id]?> <br>
                        <strong> Loan Amount :  </strong> <span><?php echo $group_currency .' '. $loan_application->loan_amount?></span> &nbsp;<br>
                    </td> 
                    <td>
                        <?php
                        if(empty($guarantor_requests)){?>
                            <strong> <span class="label label-danger">No quarantor Records</span></strong>
                            <?php
                        }else{ ?>
                         <?php
                            foreach ($guarantor_requests as $key => $guarantor_request) {
                                if($loan_application->id ==  $guarantor_request->loan_application_id){
                                    ?>                          
                                    <strong> Guarantor Name : </strong></strong> <?php echo isset($members[$guarantor_request->guarantor_member_id])?$members[$guarantor_request->guarantor_member_id]:''?><br>
                                    <strong> Amount :  </strong> <span><?php echo $group_currency .' '. $guarantor_request->amount?></span> &nbsp;<br>
                                    <strong> Status: </strong><span> <?php 
                                      if($guarantor_request->loan_request_progress_status == 1){
                                        ?> <span class="label label-warning">Pending</span><br><?php
                                      }else if($guarantor_request->loan_request_progress_status == 3){
                                        ?><span class="label label-success">Approved</span><br><?php
                                      }else if($guarantor_request->loan_request_progress_status == 4){
                                         ?><span class="label label-danger">Declined</span><br><?php
                                      }?></span>
                                    <?php
                                }
                            } ?> 
                        <?php }?> 
                    </td>
                    <td><?php
                        if(empty($signatory_requests)){?>
                           <strong> <span class="label label-danger">No signatory Records</span></strong><?php
                        }else{ ?>
                        <?php
                            foreach ($signatory_requests as $key => $signatory_requests) {
                                if($loan_application->id ==  $signatory_requests->loan_application_id){
                                    ?>                          
                                    <strong> Signatory Name : </strong></strong> <?php echo $roles_holders[$signatory_requests->signatory_member_id]?><br>
                                    <strong> Status: </strong><span> <?php 
                                      if($signatory_requests->is_approved == ''  && $signatory_requests->is_declined == ''){
                                        ?> <span class="label label-warning">Pending</span><br><?php
                                      }else if($signatory_requests->is_approved == 1 && $signatory_requests->is_declined == ''){
                                        ?><span class="label label-success">Approved</span><br><?php
                                      }else if($signatory_requests->is_declined == 1 && $signatory_requests->is_approved == '' ){
                                         ?><span class="label label-danger">Declined</span><br><?php
                                      }?></span>
                                    <?php
                                }
                            } ?> 
                        <?php }?>                     
                    </td>                  
                </tr>
            <?php
                endforeach; ?>            
        </tbody>
    </table>

</div>
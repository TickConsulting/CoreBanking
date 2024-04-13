<?php if(isset($pdf_true) && $pdf_true){?>
    <link href="<?php echo site_url();?>assets/styling/style.css" rel="stylesheet" type="text/css" /> 
    <link href="<?php echo site_url();?>templates/admin_themes/groups/css/custom.css" rel="stylesheet" type="text/css" /> 
    <style type="text/css">
        .filter_header,.print_layout{
            display: none;
        }
        #statement_paper .table td {
            font-size: 9px;
            padding: .25rem;
        }
        .statement-header-content {
            font-size: 9px;
        }
        #statement_header,.header_paper,.contribution_name,#statement_paper .table th,#statement_footer{
            font-size: 10px;
        }
        #statement_paper {
            padding: none;
            box-shadow: none;
        }
        .pdf_layout{
            display: none;  
        }
    </style>
<?php } ?>

<div class="">
    <div class="row mt-3 filter_header">
        <div class="col-lg-12">
            <div class="m-dropdown m-dropdown--inline m-dropdown--large m-dropdown--arrow" m-dropdown-toggle="click" m-dropdown-persistent="1">
                <a href="#" class="m-dropdown__toggle btn btn-sm btn-primary dropdown-toggle">
                    <?php echo translate('Filter Records'); ?>
                </a>
                <div class="m-dropdown__wrapper">
                    <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                    <div class="m-dropdown__inner">
                        <div class="m-dropdown__body">              
                            <div class="m-dropdown__content">
                                <?php echo form_open(current_url(),'method="GET" class="filter m-form m-form--label-align-right"');?>
                                    <div class="form-group m-form__group row">
                                        <div class="col-lg-12">
                                            <label>
                                                <?php echo translate('Statement Date Range'); ?>
                                            </label>
                                            <div class="input-daterange input-group date-picker" id="m_datepicker_5" data-date-format="dd-mm-yyyy">
                                                    <?php echo form_input('from',timestamp_to_datepicker($from),' class="form-control" '); ?>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                                </div>
                                                <?php echo form_input('to',timestamp_to_datepicker($to),' class="form-control" '); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row pt-0">
                                        <div class="col-lg-12">
                                            <label>
                                                <?php echo translate('Select Contribution Accounts'); ?>
                                            </label>
                                            <?php echo form_dropdown('contributions[]',array(''=>'All')+$contribution_fine_options,$this->input->get('contributions')?$this->input->get('contributions'):'','class="form-control m-select2-search" multiple="multiple" id = ""  ') ?>
                                        </div>
                                    </div>   
                                    <div class="form-group m-form__group row pt-0">
                                        <div class="col-lg-12">
                                            <label>
                                                <?php echo translate('Select Fine Categories'); ?>
                                            </label>
                                            <?php echo form_dropdown('fine_categories[]',array(''=>'All')+$fine_category_options,$this->input->get('fine_categories')?$this->input->get('fine_categories'):'','class="form-control m-select2-search" multiple="multiple" id = ""  ') ?>
                                        </div>
                                    </div>   
                                    <div class="m-form__actions m--align-right p-0">
                                        <button name="filter" value="filter" type="submit"  class="btn btn-primary btn-sm">
                                            <i class="fa fa-filter"></i>
                                            <?php echo translate('Filter'); ?>
                                        </button>
                                        <button class="btn btn-sm btn-danger close-filter d-none" type="reset">
                                            <i class="fa fa-close"></i>
                                            <?php echo translate('Reset'); ?>
                                        </button>
                                    </div>
                                <?php echo form_close();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>                    
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12 pt-4">
                <div id="statement_paper">
                    <div class="row" id="statement_header">
                        <div class="col-xs-6 col-sm-6">
                            <div class="invoice-logo">
                                <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$this->group->avatar)?site_url('uploads/groups/'.$this->group->avatar):site_url('uploads/logos/'.$this->application_settings->paper_header_logo); ?>" alt="" class='group-logo image-responsive' /> 
                            </div>
                        </div> 
                        <div class="col-sm-6 text-right" >
                            <div class="company-address">
                                <span class="bold uppercase"><?php echo $this->group->name; ?></span>
                                <?php echo nl2br($this->group->address); ?><br/>
                                <span class="bold"><?php echo translate('Telephone')?>: </span> <?php echo $this->group->phone; ?>
                                <br/>
                                <span class="bold"><?php echo translate('Email Address')?>: </span> <?php echo $this->group->email; ?>
                                <br/>
                            </div>
                        </div>
                    </div>
                    <div class="row header_paper">
                        <div class="col-xs-6 col-sm-6">
                            <div>
                                <strong><?php echo translate('Name')?>: </strong> <?php echo $member->first_name.' '.$member->last_name?>
                                <br>
                                <strong><?php echo translate('Phone')?>: </strong> <?php echo $member->phone;?>
                                <br>
                                <strong><?php echo translate('Email Address')?>: </strong> <?php echo $member->email;?>
                                <br>
                                <strong><?php echo translate('Member Number')?>: </strong> <?php echo $member->membership_number;?>
                                <br>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div>
                                <strong><?php echo translate('Statement as at')?>: </strong> <?php echo timestamp_to_date(time()); ?>
                                <br>
                                <strong><?php echo translate('Statement Period')?>: </strong> <?php echo timestamp_to_date($from); ?> <span class="bold">to</span> <?php echo timestamp_to_date($to); ?>
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>

                    <hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if(empty($posts)){ ?>
                                <div class="col-md-12 margin-bottom-10 ">
                                    <div class="m-alert m-alert--outline alert alert-info fade show mr-3" role="alert">
                                        <strong><?php echo translate('No Records');?>!</strong>
                                        <br/><br/>
                                        <?php echo translate('No transaction records to display')?>
                                    </div>
                                </div>
                            <?php }else{ ?>
                                <div class="col-xs-12 table-responsive p-3">
                                    <table class="table table-hover table-striped table-condensed table-statement">
                                        <thead>
                                            <tr>
                                                <th class="invoice-title ">
                                                    <?php echo translate('Type'); ?>
                                                </th>
                                                <th class="invoice-title ">
                                                    <?php echo translate('Date'); ?>
                                                </th>
                                                <th class="invoice-title ">
                                                    <?php echo translate('Description'); ?>
                                                </th>
                                                <th class="invoice-title  text-right">
                                                    <?php echo translate('Payable'); ?>
                                                    (<?php echo $this->group_currency; ?>)</th>
                                                <th class="invoice-title  text-right">
                                                    <?php
                                                        $default_message='Paid';
                                                        $this->languages_m->translate('paid',$default_message);
                                                    ?>
                                                    (<?php echo $this->group_currency; ?>)</th>
                                                <th class="invoice-title  text-right">
                                                    <?php echo translate('Balance'); ?>
                                                    (<?php echo $this->group_currency; ?>)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo translate('Balance B/F'); ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                            </tr>
                                            <?php
                                                $total_amount_payable = 0; 
                                                $total_amount_paid = 0;  
                                                foreach($posts as $post): 
                                                    if($post->transaction_type==2){ 
                                                        $balance+=$post->amount;
                                                        $total_amount_payable+=$post->amount;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td><?php echo $contribution_fine_options[$post->contribution_id]; ?></td>
                                                        <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                                                        <td></td>
                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                <?php 
                                                    }else if($post->transaction_type==3){
                                                        $balance+=$post->amount;
                                                        $total_amount_payable+=$post->amount;
                                                ?>
                                                
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td><?php echo translate('Fine for').' '.$fine_category_options[$post->fine_category_id]; ?></td>
                                                        <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                                                        <td></td>
                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                <?php
                                                    }else if($post->transaction_type==12||$post->transaction_type==13||$post->transaction_type==14||$post->transaction_type==16){
                                                        $balance-=$post->amount;
                                                        $total_amount_paid+=$post->amount;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td>
                                                            <?php 
                                                                if($post->contribution_id){
                                                                    echo translate('Contribution fine payment for').' '.$contribution_fine_options[$post->contribution_id]; 
                                                                }else if($post->fine_category_id){
                                                                    echo translate('Fine payment for').' '.$fine_category_options[$post->fine_category_id]; 
                                                                }
                                                            ?>
                                                        </td>
                                                        <td></td>
                                                        <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                <?php
                                                    }else if($post->transaction_type==28){
                                                        $balance-=$post->amount;
                                                        $total_amount_paid+=$post->amount; 
                                                ?>
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td>
                                                            <?php
                                                                if($post->contribution_from_id=="loan"){
                                                                    echo translate("Loan repayment transfer to").' ';
                                                                }else{
                                                                    echo "'".$contribution_fine_options[$post->contribution_from_id]."'".translate('contribution transfer from');
                                                                }
                                                                if($post->contribution_to_id){
                                                                    echo $contribution_fine_options[$post->contribution_to_id]." ".translate('late payment fine');
                                                                }else if($post->fine_category_to_id){
                                                                    echo $fine_category_options[$post->fine_category_to_id];
                                                                }
                                                            ?>
                                                        </td>
                                                        <td></td>
                                                        <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>

                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                <?php
                                                    }else{ 
                                                ?>

                                                <?php 
                                                    } 
                                                ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><?php echo translate('Totals'); ?></td>
                                                <td></td>
                                                <td></td>
                                                <td class='text-right'><?php echo number_to_currency($total_amount_payable); ?></td>
                                                <td class='text-right'><?php echo number_to_currency($total_amount_paid); ?></td>
                                                <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div> 
                            <?php } ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div id="statement_footer" >
                        <p style="text-align:center;">© <?php echo date('Y')?> . <?php echo translate('This statement was issued with no alteration')?> </p>
                        <p style="text-align:center;">
                             <strong> <?php echo translate('Powered by')?>:</strong>
                            <br>
                            <img width="150px" src="<?php echo site_url('uploads/logos/'.$this->application_settings->paper_header_logo);?>" alt="<?php echo $this->application_settings->application_name;?> Logo" ?="">
                        </p>
                    </div>

                </div>
            </div>
            <div class="clearfix"></div>
            <hr>
        </div>
    </div>    
    <div class="row print_layout mt-3">
        <div class="col-xs-12">
            <button class="btn btn-sm btn-info hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> 
                <?php echo translate('Print'); ?>
            </button>
            &nbsp;&nbsp;&nbsp;
            <?php $search_string = substr(basename($_SERVER['REQUEST_URI']),strpos(basename($_SERVER['REQUEST_URI']), "?"));
            ?>
            <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.$this->member->id.'/'.TRUE;?>" target="_blank"><i class='fa fa-file'></i>
                <?php echo translate('Generate PDF'); ?>
            </a>
        </div>
    </div>
</div>

<div class="row pdf_layout">
    
</div>
<script type="text/javascript">
    $(document).ready( function(){
        $(".m-select2-search").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
            width: "100%"
        });
        $('.date-picker').datepicker({ dateFormat: 'dd-mm-yy' ,autoclose: true});
    });
</script>
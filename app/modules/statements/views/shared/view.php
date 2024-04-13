<?php if(isset($pdf_true) && $pdf_true){?>
    <link href="<?php echo site_url();?>assets/styling/style.css" rel="stylesheet" type="text/css" /> 
    <link href="<?php echo site_url();?>templates/admin_themes/groups/css/custom.css" rel="stylesheet" type="text/css" /> 
    <style type="text/css">
        .statement_types,.filter_header,.print_layout{
            display: none;
        }
        #statement_paper .table td {
            font-size: 6px;
            padding: .25rem;
        }
        .statement-header-content {
            font-size: 9px;
        }
        #statement_header,.header_paper,.contribution_name,#statement_paper .table th,#statement_footer{
            font-size: 11px;
        }
        #statement_paper {
            padding: none;
            box-shadow: none;
        }
        .pdf_layout{
            display: none;  
        }
        .no-records-display{
            font-size: 10px;
        }
        #statement_paper .table.table-statement td{
            font-size: 10px;
            line-height: 12px;
        }
        #statement_paper .table.table-statement tfoot>tr>td{
            font-weight: 800;
        }
        #statement_paper .table.table-statement thead,#statement_paper .table.table-statement tfoot{
            background-color: #B53A32;
            color: #fff;
        }

        tbody {
            background-color: #e4f0f5;
        }

        caption {
            padding: 10px;
            caption-side: bottom;
        }

        table {
            border-collapse: collapse;
            /*border: 2px solid rgb(200, 200, 200);*/
            letter-spacing: 1px;
            /*font-family: sans-serif;*/
            font-size: .8rem;
        }

        td,
        th {
            /*border: 1px solid rgb(190, 190, 190);*/
            padding: 5px 10px;
        }

        td {
            /*text-align: center;*/
        }
        
    </style>
<?php } ?>


<div class="">
    <div class="row statement_types hidden-print">
        <div class="col-md-6 col-xs-6">
            <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-left" m-dropdown-toggle="hover" aria-expanded="true">
                <a href="#" class="m-dropdown__toggle btn btn-primary dropdown-toggle btn-sm">
                    <i class="fa fa-angle-down"></i>
                </a>
                <div class="m-dropdown__wrapper" style="z-index: 101;">
                    <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                    <div class="m-dropdown__inner">
                        <div class="m-dropdown__body">              
                            <div class="m-dropdown__content">
                                <ul class="m-nav">
                                    <li class="m-nav__section m-nav__section--first">
                                        <span class="m-nav__section-text"><?php echo translate('Select Option') ?></span>
                                    </li>
                                    <li class="m-nav__item"> 
                                        <a class="m-nav__link" href="<?php echo site_url($this->uri->segment(1).'/statements/deposit_statement/'.$member->id); ?>"> <i class="m-nav__link-icon mdi mdi-file-document-box-outline"></i>
                                            <span class="m-nav__link-text"><?php echo translate('Contribution Statement') ?></span> </a>
                                    </li>
                                    <li class="m-nav__item"> 
                                        <a class="m-nav__link" href="<?php echo site_url($this->uri->segment(1).'/statements/fine_statement/'.$member->id); ?>"> <i class="m-nav__link-icon mdi mdi-thumb-down"></i>
                                            <span class="m-nav__link-text"><?php echo translate('Fine Statement') ?></span> </a>
                                    </li>
                                    <li class="m-nav__item"> 
                                        <a class="m-nav__link" href="<?php echo site_url($this->uri->segment(1).'/statements/miscellaneous_statement/'.$member->id); ?>"> <i class="m-nav__link-icon mdi mdi-gamepad-round-outline"></i>
                                            <span class="m-nav__link-text"><?php echo translate('Miscellaneous Statement') ?></span></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a  href="<?php echo site_url($this->uri->segment(1).'/statements/view/'.$next_group_member_id); ?>" class="btn btn-sm btn-primary"><?php echo translate('Current Statement')?>: <?php echo $this->group_member_options[$member->id]; ?></a>
        </div>
        <?php if($this->group->enable_member_information_privacy && $show_next_member==FALSE){}else{ ?>
            <div class="col-md-6 col-xs-6 text-right <?php echo $display;?>">
                <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right" m-dropdown-toggle="hover" aria-expanded="true">
                    <a href="<?php echo site_url($this->uri->segment(1).'/statements/view/'.$next_group_member_id); ?>" class="m-dropdown__toggle btn btn-primary dropdown-toggle btn-sm">
                        <?php echo translate('Next') ?>: <?php echo $this->group_member_options[$next_group_member_id]; ?>
                    </a>
                    <div class="m-dropdown__wrapper" style="z-index: 101;">
                        <span class="m-dropdown__arrow m-dropdown__arrow--right"></span>
                        <div class="m-dropdown__inner">
                            <div class="m-dropdown__body">              
                                <div class="m-dropdown__content">
                                    <ul class="m-nav">
                                        <?php 
                                            echo '
                                            <li class="m-nav__section m-nav__section--first">
                                                <span class="m-nav__section-text">Select Member</span>
                                            </li>';
                                            foreach($this->active_group_member_options as $member_id => $member_name):
                                                echo '
                                                <li class="m-nav__item"> 
                                                    <a class="m-nav__link" href="'.site_url($this->uri->segment(1).'/statements/view/'.$member_id).'"> <i class="m-nav__link-icon "></i>
                                                        <span class="m-nav__link-text">'.$member_name.'</span> </a>
                                                </li>';
                                            endforeach;
                                        ?>                                    
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>

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
                                    <div class="form-group m-form__group pt-0">
                                        <label>
                                            <?php echo translate('Select Contribution Accounts');?>
                                        </label>
                                        <?php echo form_dropdown('contributions[]',array()+translate($open_contribution_options),$this->input->get('contributions')?$this->input->get('contributions'):'','class="form-control select2" multiple="multiple"'); ?>
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
                                <br/>
                                <span class="bold"><?php echo translate('Address') ?>: </span><?php echo nl2br($this->group->address); ?><br/>
                                <span class="bold"><?php echo translate('Telephone') ?>: </span> <?php echo $this->group->phone; ?>
                                <br/>
                                <span class="bold"><?php echo translate('Email Address') ?>: </span> <?php echo $this->group->email; ?>
                                <br/>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row header_paper">
                        <div class="col-xs-6 col-sm-6">
                            <div>
                                <strong><?php echo translate('Name') ?>: </strong> <?php echo $member->first_name.' '.$member->last_name?>
                                <br>
                                <strong><?php echo translate('Phone') ?>: </strong> <?php echo $member->phone;?>
                                <br>
                                <strong><?php echo translate('Email Address') ?>: </strong> <?php echo $member->email;?>
                                <br>
                                <strong><?php echo translate('Member Number') ?>: </strong> <?php echo $member->membership_number;?>
                                <br>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div>
                                <strong><?php echo translate('Statement as at') ?>: </strong> <?php echo timestamp_to_date(time()); ?>
                                <br>
                                <strong><?php echo translate('Statement period') ?>: </strong> <?php echo timestamp_to_date($from); ?> <span class="bold"><?php echo translate('to') ?></span> <?php echo timestamp_to_date($to); ?>
                                <br>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-12 col-xs-12 statement-header-content">
                            <?php if(!empty($posts)){ ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-condensed table-statement">
                                        <thead>
                                            <tr>
                                                 <th nowrap width="15%" class="text-left"><strong><?php echo translate('Type') ?></strong></th>
                                                 <th nowrap width="10%" class="text-left"><strong><?php echo translate('Posting Date') ?></strong></th>
                                                 <th nowrap width="45%" class="text-left"><strong><?php echo translate('Description') ?></strong></th>
                                                 <th nowrap width="10%" class="text-right"><strong><?php echo translate('Payable') ?></strong></th>
                                                 <th nowrap width="10%" class="text-right"><strong><?php echo translate('Paid') ?></strong></th>
                                                 <th nowrap width="10%" class="text-right"><strong><?php echo translate('Balance') ?></strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Balance B/F</td>
                                                <td></td>
                                                <td></td>
                                                <td class='text-right'><?php echo number_to_currency($amount_payable); ?></td>
                                                <td class='text-right'><?php echo number_to_currency($amount_paid); ?></td>
                                                <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                            </tr>
                                            <?php
                                                $total_amount_payable = $amount_payable; 
                                                $total_amount_paid = $amount_paid;
                                                foreach($posts as $post):
                                                    if($post->transaction_type==1){ 
                                                        $balance+=$post->amount;
                                                        $total_amount_payable+=$post->amount;
                                                ?>
                                                    <tr>
                                                        <td nowrap><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <!-- <td><?php echo $post->id.'-'.$post->deposit_id.'-'.$statement_transaction_names[$post->transaction_type]; ?></td> -->
                                                        <td nowrap><span class="tooltips" data-original-title="Contribution Invoice Due Date: <?php echo timestamp_to_date($post->contribution_invoice_due_date,TRUE); ?>" ><?php echo timestamp_to_date($post->transaction_date,TRUE); ?></span></td>
                                                        <td nowrap><?php echo $contribution_options[$post->contribution_id]; ?></td>
                                                        <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                                                        <td></td>
                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                <?php 
                                                    }else if($post->transaction_type==21 ||$post->transaction_type==22 || $post->transaction_type==23 || $post->transaction_type==24){ 
                                                        $balance+=$post->amount;
                                                        $total_amount_paid-=$post->amount;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td><?php echo $contribution_options[$post->contribution_id]; ?> Refund</td>
                                                        <td></td>
                                                        <td class='text-right'>(<?php echo number_to_currency($post->amount); ?>)</td>
                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                <?php 
                                                    }else if($post->transaction_type==30){
                                                        $balance+=$post->amount;
                                                        $total_amount_paid-=$post->amount;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td>Transfer from <?php echo $contribution_options[$post->contribution_from_id]; ?> to <?php if($post->loan_to_id){ echo ' loan';}?> </td>
                                                        <td></td>
                                                        <td class='text-right'>(<?php echo number_to_currency($post->amount); ?>)</td>
                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                    <?php 

                                                    }else if($post->transaction_type==9||$post->transaction_type==10||$post->transaction_type==11||$post->transaction_type==15){ 
                                                        $balance-=$post->amount;
                                                        $total_amount_paid+=$post->amount;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td>
                                                            <?php
                                                                echo "'".$contribution_options[$post->contribution_id]."' payment made to ".$account_options[$post->account_id];
                                                            ?>
                                                        </td>
                                                        <td></td>
                                                        <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                <?php 
                                                    }else if($post->transaction_type==25){  
                                                        $balance+=floatval($post->amount);
                                                        $total_amount_paid-=floatval($post->amount); 
                                                        //
                                                ?>
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td>
                                                            <?php
                                                                echo "'".$contribution_options[$post->contribution_id]."' contribution transfer to ";
                                                                if($post->member_to_id){
                                                                    echo isset($this->group_member_options[$post->member_to_id])?($this->group_member_options[$post->member_to_id].'`s '):"Another member - ";
                                                                }
                                                                echo "'".$contribution_options[$post->contribution_to_id]."'";
                                                            ?>
                                                        </td>
                                                        <td></td>
                                                        <td class='text-right'>(<?php echo number_to_currency($post->amount); ?>)</td>
                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                <?php
                                                    }else if($post->transaction_type==26){
                                                        $balance-=floatval($post->amount);
                                                        $total_amount_paid+=floatval($post->amount);  
                                                ?>
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td>
                                                            <?php
                                                            if($post->contribution_from_id=="loan"):
                                                                echo "'".$contribution_options[$post->contribution_id]."' contribution transfer from Loan Share";
                                                            else:
                                                                echo "'".$contribution_options[$post->contribution_id]."' contribution transfer from ";
                                                                if($post->member_from_id){
                                                                    echo isset($this->group_member_options[$post->member_from_id])?($this->group_member_options[$post->member_from_id].'`s '):"Another member - ";
                                                                }
                                                                echo "'".$contribution_options[$post->contribution_from_id]."'";
                                                            endif;
                                                            ?>
                                                        </td>
                                                        <td></td>
                                                        <td class='text-right'><?php echo number_to_currency($post->amount); ?></td>
                                                        <td class='text-right'><?php echo number_to_currency($balance);
                                                         ?></td>
                                                    </tr>
                                                <?php
                                                    }else if($post->transaction_type==29){                                    
                                                        $total_amount_paid+=$post->amount;
                                                    }else if($post->transaction_type==27){
                                                        $balance+=$post->amount;
                                                        $total_amount_paid-=$post->amount; 
                                                ?>
                                                    <tr>
                                                        <td><?php echo $statement_transaction_names[$post->transaction_type]; ?></td>
                                                        <td><?php echo timestamp_to_date($post->transaction_date); ?></td>
                                                        <td>
                                                            <?php
                                                                echo "'".$contribution_options[$post->contribution_from_id]."' contribution transfer to ";
                                                                if($post->contribution_to_id){
                                                                    echo $contribution_options[$post->contribution_to_id]." late payment fine";
                                                                }else if($post->fine_category_to_id){
                                                                    echo $fine_category_options[$post->fine_category_to_id];
                                                                }
                                                            ?>
                                                        </td>
                                                        <td></td>
                                                        <td class='text-right'>(<?php echo number_to_currency($post->amount); ?>)</td>

                                                        <td class='text-right'><?php echo number_to_currency($balance); ?></td>
                                                    </tr>
                                                <?php
                                                    }
                                                ?>
                                            <?php endforeach; ?>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Totals</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td class='text-right'><strong><?php echo number_to_currency($total_amount_payable); ?></strong></td>
                                                <td class='text-right'><strong><?php echo number_to_currency($total_amount_paid); ?></strong></td>
                                                <td class='text-right'><strong><?php echo number_to_currency($balance); ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div> 
                            <?php }else{ ?>
                                <div class="col-xs-12 margin-bottom-10 ">
                                    <div class="alert alert-info">
                                        <h4 class="block">
                                            <?php
                                                $default_message='Information! No records to display';
                                                $this->languages_m->translate('no_records_to_display',$default_message);
                                            ?>
                                            
                                        </h4>
                                        <p>
                                            No transactions to display.
                                        </p>
                                    </div>
                                </div>
                            <?php } ?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <hr>
    <div class="row print_layout mt-3">
        <div class="col-xs-12">
            <button class="btn btn-sm btn-info hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i> 
                <?php echo translate('Print'); ?>
            </button>
            &nbsp;&nbsp;&nbsp;
            <?php $search_string = substr(basename($_SERVER['REQUEST_URI']),strpos(basename($_SERVER['REQUEST_URI']), "?"));
            ?>
            <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.TRUE.$search_string;?>" target="_blank"><i class='fa fa-file'></i>
                <?php echo translate('Generate PDF'); ?>
            </a>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.select2').select2({width:"100%"});
        $('.contribution_options').on('change',function(e){
            $('.contribution_options').val();
            $.ajax({
                type : 'Post',
                url : '<?php echo site_url("/ajax/statements/view/".$member->id); ?>',
                success : function(response){
                    var response = $.parseJSON(response);
                    if(response.status==1){
                        $('.statement').html(response.html);
                    }else{
                        //error
                    }
                },
                error : function(e){

                }
            });

        });
    });
    function isJson(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
</script>

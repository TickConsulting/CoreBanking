<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

 	protected $validation_rules=array(
    );

	public function __construct(){
        parent::__construct();
        $this->load->model('stocks_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('withdrawals/withdrawals_m');
        $this->load->library('transactions');
    }

    public function index(){
        $data = array();
        $this->template->title('Group Stocks')->build('group/index',$data);
    }

    public function record_stock_purchases(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['purchase_dates'])){
                    $count = count($posts['purchase_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['purchase_dates'][$i])&&isset($posts['names'][$i])&&isset($posts['number_of_shares'][$i])&&isset($posts['accounts'][$i])&&isset($posts['price_per_shares'][$i])): 
                            //Deposit dates
                            if($posts['purchase_dates'][$i]==''){
                                $successes['purchase_dates'][$i] = 0;
                                $errors['purchase_dates'][$i] = 1;
                                $error_messages['purchase_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['purchase_dates'][$i] = 1;
                                $errors['purchase_dates'][$i] = 0;
                            }
                            //Members
                            if($posts['names'][$i]==''){
                                $successes['names'][$i] = 0;
                                $errors['names'][$i] = 1;
                                $error_messages['names'][$i] = 'Please enter a stock name';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['names'][$i] = 1;
                                $errors['names'][$i] = 0;
                            }
                             //Accounts
                            if($posts['number_of_shares'][$i]==''){
                                $successes['number_of_shares'][$i] = 0;
                                $errors['number_of_shares'][$i] = 1;
                                $error_messages['number_of_shares'][$i] = 'Please enter the number of shares purchased';
                                $entries_are_valid = FALSE;
                            }else{
                                if(is_numeric($posts['number_of_shares'][$i])){
                                    $successes['number_of_shares'][$i] = 1;
                                    $errors['number_of_shares'][$i] = 0;
                                }else{
                                    $successes['number_of_shares'][$i] = 0;
                                    $errors['number_of_shares'][$i] = 1;
                                    $error_messages['number_of_shares'][$i] = 'Please enter a numbers only';
                                    $entries_are_valid = FALSE;
                                }
                            }
                            //Deposit Method
                            if($posts['accounts'][$i]==''){
                                $successes['accounts'][$i] = 0;
                                $errors['accounts'][$i] = 1;
                                $error_messages['accounts'][$i] = 'Please select an account';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['accounts'][$i] = 1;
                                $errors['accounts'][$i] = 0;
                            }
                            //amounts
                            if($posts['price_per_shares'][$i]==''){
                                $successes['price_per_shares'][$i] = 0;
                                $errors['price_per_shares'][$i] = 1;
                                $error_messages['price_per_shares'][$i] = 'Please enter the price per share';
                                $entries_are_valid = FALSE;
                            }else{
                                if(valid_currency($posts['price_per_shares'][$i])){
                                    $successes['price_per_shares'][$i] = 1;
                                    $errors['price_per_shares'][$i] = 0;
                                }else{
                                    $successes['price_per_shares'][$i] = 0;
                                    $errors['price_per_shares'][$i] = 1;
                                    $error_messages['price_per_shares'][$i] = 'Please enter a valid price per share';
                                    $entries_are_valid = FALSE; 
                                }
                            }
                        endif;
                    endfor;
                }
            }
            if($entries_are_valid){
                $this->session->set_flashdata('error','');
                if(isset($posts['purchase_dates'])){
                    $count = count($posts['purchase_dates']);
                    $successful_stock_purchase_entry_count = 0;
                    $unsuccessful_stock_purchase_entry_count = 0;
                    $count = count($posts['purchase_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['purchase_dates'][$i])&&isset($posts['names'][$i])&&isset($posts['number_of_shares'][$i])&&isset($posts['accounts'][$i])&&isset($posts['price_per_shares'][$i])): 
                            $price_per_share = valid_currency($posts['price_per_shares'][$i]);
                            $purchase_date = strtotime($posts['purchase_dates'][$i]); 
                            if($this->transactions->record_stock_purchase($this->group->id,$purchase_date,$posts['names'][$i],$posts['number_of_shares'][$i],$posts['accounts'][$i],$price_per_share)){
                                $successful_stock_purchase_entry_count++;
                            }else{
                                $unsuccessful_stock_purchase_entry_count++;
                            }
                        endif;
                    endfor;
                }
                if($successful_stock_purchase_entry_count){
                    if($successful_stock_purchase_entry_count==1){
                        $this->session->set_flashdata('success',$successful_stock_purchase_entry_count.' stock purchase successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$unsuccessful_stock_purchase_entry_count.' stock purchases successfully recorded. ');
                    }
                }
                redirect('group/stocks/listing');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            } 
        }

        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['posts'] = $posts;
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        // $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $this->template->title(translate('Record Stock Purchases'))->build('group/record_stock_purchases',$data);
    }

    public function listing(){
        $data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['to'] = $to;
        $data['from'] = $from;
        if($this->input->get('generate_excel') == 1){
            $filter_parameters = array(
                'to' => $to,
                'from' => $from,
                'stocks' => $this->input->get('stocks'),
            );
            $data['posts'] = $this->stocks_m->get_group_stocks($filter_parameters);
            $data['group'] = $this->group;
            $data['group_currency'] = $this->group_currency;
            $json_file = json_encode($data);
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/stocks/listing',$this->group->name.' List of Stocks'));
            die;
        }
        $this->template->title(translate('List Stocks'))->build('group/listing',$data);
    }


    public function ajax_update_stock_current_price($id = 0){
        if($id){
            $input = array(
                'current_price'=>valid_currency($this->input->post('value')),
                'modified_on'=>time(),
            );
            if($result = $this->stocks_m->update($id,$input)){
                echo number_to_currency(valid_currency($this->input->post('value')));
            }else{
                echo 'Could not update stock current price';
            }
        }else{
            echo 'Stock id missing.';
        }
    }

    public function void($id = 0,$redirect = TRUE){
        $id OR redirect('group/stocks/listing');
        $post = $this->stocks_m->get_group_stock($id);
        $post OR redirect('group/stocks/listing');
        $withdrawal = $this->withdrawals_m->get_withdrawal_by_stock_id($post->id,$this->group->id);
        $withdrawal OR redirect('group/stocks/listing');
        if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$this->group->id)){
            $this->session->set_flashdata('success','Stock purchase voided successfully');
        }else{
            $this->session->set_flashdata('error','Stock purchase could not be voided successfully');
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/stocks/listing');
            }
        }
    }

    function sell($id = 0){
        $id OR redirect('group/stocks/listing');
        $post = $this->stocks_m->get_group_stock($id);
        $post OR redirect('group/stocks/listing');
        $validation_rules=array(
            array(
                'field' =>  'account_id',
                'label' =>  'Account',
                'rules' =>  'trim|required',
            ),array(
                'field' =>  'sale_date',
                'label' =>  'Stocks Sale Date',
                'rules' =>  'trim|required',
            ),
            array(
                'field' =>  'number_of_shares_to_be_sold',
                'label' =>  'Number of Shares Sold',
                'rules' =>  'trim|required|numeric|is_natural_no_zero|callback__is_less_than_or_equal_to_shares_available',
            ),
            array(
                'field' =>  'number_of_shares_available',
                'label' =>  'Shares Available',
                'rules' =>  'trim|required|numeric',
            ),
            array(
                'field' =>  'sale_price_per_share',
                'label' =>  'Sale Price per Share',
                'rules' =>  'trim|required|currency',
            ),array(
                'field' =>  'number_of_previously_sold_shares',
                'label' =>  'Number of Previously Sold Shares',
                'rules' =>  'trim|numeric',
            ),
        );
        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $sale_date = strtotime($this->input->post('sale_date'));
            $number_of_shares_sold = $this->input->post('number_of_shares_to_be_sold');
            $sale_price_per_share = valid_currency($this->input->post('sale_price_per_share'));
            $number_of_previously_sold_shares = $this->input->post('number_of_previously_sold_shares');
            $account_id = $this->input->post('account_id');
            if($this->transactions->record_stock_sale($this->group->id,$id,$sale_date,$account_id,$number_of_shares_sold,$sale_price_per_share,$number_of_previously_sold_shares)){
                $this->session->set_flashdata('success','Stock sale recorded successfully');
            }else{
                $this->session->set_flashdata('error','Something went wrong during the recording of the stock sale');
            }
            redirect('group/stocks/stock_sales');
        }else{
            foreach($validation_rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        // $data['withdrawal_account_options'] = $this->accounts_m->get_active_group_account_options(FALSE,'','','',TRUE);
        $data['withdrawal_account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        // $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        
        $data['post'] = $post;
        $this->template->title('Sell '.$post->name .' stock')->build('group/sell',$data);
    }

    function stock_sales(){
        $data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['stock_options'] = $this->stocks_m->get_group_stock_options();
        $data['to'] = $to;
        $data['from'] = $from;
        if($this->input->get('generate_excel') == 1){
            $filter_parameters = array(
                'from' => $from,
                'to' => $to,
                'accounts' => $this->input->get('accounts'),
                'stocks' => $this->input->get('stocks'),
            );
            $data['posts'] = $this->deposits_m->get_group_stock_sale_deposits($this->group->id,$filter_parameters); 
            $data['group'] = $this->group;
            $data['group_currency'] = $this->group_currency;
            $json_file = json_encode($data);
            //print_r($json_file);
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/stocks/stock_sales',$this->group->name.' List of Stock Sales'));
            die;
        }
        $this->template->title(translate('Stock Sales'))->build('group/stock_sales',$data);
    }

    function _is_less_than_or_equal_to_shares_available(){
        if($this->input->post('number_of_shares_available')<$this->input->post('number_of_shares_to_be_sold')){
            $this->form_validation->set_message('_is_less_than_or_equal_to_shares_available', 'Number of sold shares cannot exceed available shares');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_void'){
            if(empty($action_to)){
                $this->session->set_flashdata('error','Select atleast one stock purchase to void');
                redirect('group/stocks/listing');
            }else{
                for($i=0;$i<count($action_to);$i++){
                    $this->void($action_to[$i],FALSE);
                }
            }
            redirect($this->agent->referrer());
        }else if($action == 'bulk_stock_sale_void'){
            if(empty($action_to)){
                $this->session->set_flashdata('error','Select atleast one stock sale to void');
                redirect('group/stock_sales/listing');
            }else{
                for($i=0;$i<count($action_to);$i++){
                    $this->void_stock_sale($action_to[$i],FALSE);
                }
            }
            redirect($this->agent->referrer());
        }
    }

    function ajax_stocks_listing(){
        $stock_purchases = $this->withdrawals_m->get_group_back_dating_stock_purchases();
        $stock_objects_array = $this->stocks_m->get_group_back_dating_stock_objects_array();
        $stock_sales_objects_array = $this->deposits_m->get_group_back_dating_stock_sales_objects_array();
        echo '<h4>Back-dated Group Stocks Purchases & Sales</h4>';
        if($stock_purchases){
            $stock_options = $this->stocks_m->get_group_stock_options();
            echo '
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Stock Name
                        </th>
                        <th>
                            No. of Stocks Purchased
                        </th>
                        <th class="text-right">
                            Price per Share ('.$this->group_currency.')
                        </th>
                        <th>
                            No. of Stocks Sold
                        </th>
                        <th class="text-right">
                            Sale Price per Share ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    $count = 1;
                        foreach($stock_purchases as $stock_purchase):
                            if(isset($stock_objects_array[$stock_purchase->stock_id])){
                                $stock = $stock_objects_array[$stock_purchase->stock_id];
                                echo '
                                <tr>
                                    <td>'.$count++.'</td>
                                    <td>'.$stock_options[$stock_purchase->stock_id].'</td>
                                    <td>'.$stock->number_of_shares.'</td>
                                    <td class="text-right"> 
                                        '.number_to_currency($stock->purchase_price).'
                                    </td>
                                    <td>';
                                    if(isset($stock_sales_objects_array[$stock_purchase->stock_id])){
                                        echo $stock_sales_objects_array[$stock_purchase->stock_id]->number_of_shares_sold;
                                    }else{
                                        echo "0";
                                    }
                                echo 
                                    '
                                    </td>
                                    <td class="text-right">';
                                    if(isset($stock_sales_objects_array[$stock_purchase->stock_id])){
                                        echo number_to_currency($stock_sales_objects_array[$stock_purchase->stock_id]->sale_price_per_share);
                                    }else{
                                        echo number_to_currency(0);
                                    }
                                echo'
                                    </td>
                                </tr>'; 
                            }
                        endforeach;
                echo '
                </tbody>
            </table>';
        }else{
            echo '
            <div class="alert alert-info">
                <strong>Information!</strong> No stocks to display
            </div>';
        }
    }

    function ajax_stocks_form(){
        $stock_purchases = $this->withdrawals_m->get_group_back_dating_stock_purchases();
        $stock_objects_array = $this->stocks_m->get_group_back_dating_stock_objects_array();
        $stock_sales_objects_array = $this->deposits_m->get_group_back_dating_stock_sales_objects_array();
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        echo '<h4>Back-dated Group Stocks Purchases & Sales</h4>';
        echo '
            <div class="alert alert-info">
                <strong>Information!</strong> How many stocks <strong>had</strong> the group bought and at how much for each share as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
            </div>';
            echo '
            <table id="back-date-stocks-table" class="back-date-stocks-table table table-striped table-bordered table-condensed table-hover table-multiple-items">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Stock Name
                        </th>
                        <th>
                            No. of Stocks Purchased
                        </th>
                        <th class="text-right">
                            Price per Share ('.$this->group_currency.')
                        </th>
                        <th>
                            No. of Stocks Sold
                        </th>
                        <th class="text-right">
                            Sale Price per Share ('.$this->group_currency.')
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id=\'append-place-holder\'>';
                        if($stock_purchases){
                            $stock_options = $this->stocks_m->get_group_stock_options();
                            $count = 1;
                            $i = 0;
                            foreach($stock_purchases as $stock_purchase):
                                if(isset($stock_objects_array[$stock_purchase->stock_id])){
                                    $stock = $stock_objects_array[$stock_purchase->stock_id];
                                    echo '
                                    <tr>
                                        <td>'.$count++.'</td>
                                        <td>
                                            <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                                <i class="" ></i>
                                                    '.form_input('stock_names['.$i.']',$stock_options[$stock_purchase->stock_id]," class='form-control stock_name'").'
                                            </div>
                                        </td>
                                        <td>
                                            <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                                <i class="" ></i>
                                                    '.form_input('number_of_stocks_purchased['.$i.']',$stock->number_of_shares," class='form-control number number_of_stocks_purchased'").'
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                                <i class="" ></i> 
                                                    '.form_input('price_per_share['.$i.']',number_to_currency($stock->purchase_price)," class='form-control currency price_per_share'").'
                                            </div>
                                        </td>
                                        <td>';
                                        if(isset($stock_sales_objects_array[$stock_purchase->stock_id])){
                                            $number_of_shares_sold =  $stock_sales_objects_array[$stock_purchase->stock_id]->number_of_shares_sold;
                                        }else{
                                            $number_of_shares_sold =  "";
                                        }

                                        echo '
                                            <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                                <i class="" ></i> 
                                                    '.form_input('number_of_stocks_sold['.$i.']',$number_of_shares_sold," class='form-control number number_of_stocks_sold'").'
                                            </div>
                                        ';

                                    echo 
                                        '
                                        </td>
                                        <td class="text-right">';
                                        if(isset($stock_sales_objects_array[$stock_purchase->stock_id])){
                                            $sale_price_per_share =  number_to_currency($stock_sales_objects_array[$stock_purchase->stock_id]->sale_price_per_share);
                                        }else{
                                            $sale_price_per_share = "";
                                        }
                                        echo
                                        '<div class=" input-icon tooltips right" data-original-title="" data-container="">
                                            <i class="" ></i> 
                                                '.form_input('sale_price_per_share['.$i.']',$sale_price_per_share," class='form-control currency sale_price_per_share'").'
                                        </div>';
                                    echo'
                                        </td>
                                    </tr>'; 
                                    $i++;
                                }
                            endforeach;
                        }else{
                            echo '
                            <tr>
                                <td class="count">1</td>
                                <td>
                                    <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                        <i class="" ></i>
                                            '.form_input('stock_names[]',""," class='form-control stock_name'").'
                                    </div>
                                </td>
                                <td>
                                    <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                        <i class="" ></i>
                                            '.form_input('number_of_stocks_purchased[]',""," class='form-control number number_of_stocks_purchased'").'
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                        <i class="" ></i> 
                                            '.form_input('price_per_share[]',""," class='form-control currency price_per_share'").'
                                    </div>
                                </td>
                                <td>
                                    <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                        <i class="" ></i> 
                                            '.form_input('number_of_stocks_sold[]',""," class='form-control number number_of_stocks_sold'").'
                                    </div>
                                </td>
                                <td class="text-right"> 
                                    <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                        <i class="" ></i> 
                                            '.form_input('sale_price_per_share[]',""," class='form-control currency sale_price_per_share'").'
                                    </div>
                                </td>
                                <td>
                                    <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                            </tr>';
                        } 
                echo '
                </tbody>
            </table>';
            echo '
            <div class="row">
                <div class="col-md-12 margin-bottom-10 text-left">
                    <a href="javascript:;" class="btn btn-default btn-xs" id="add-new-line">
                        <i class="fa fa-plus"></i>
                        <span class="hidden-380">
                            Add new line
                        </span>
                    </a>
                </div>
            </div>';
    }

}
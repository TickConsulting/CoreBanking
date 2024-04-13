<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->model('stocks_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('accounts/accounts_m');        
    }

    function record_stock_purchase(){
        $response = array();
        $data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
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
                            if(is_numeric(currency($posts['number_of_shares'][$i]))){
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
            if($entries_are_valid){
                // print_r($posts); die;
                $this->session->set_flashdata('error','');
                if(isset($posts['purchase_dates'])){
                    $successful_stock_purchase_entry_count = 0;
                    $unsuccessful_stock_purchase_entry_count = 0;
                    $count = count($posts['purchase_dates']);         
                    for($i=0;$i<$count;$i++):
                        if(isset($posts['purchase_dates'][$i])&&isset($posts['names'][$i])&&isset($posts['number_of_shares'][$i])&&isset($posts['accounts'][$i])&&isset($posts['price_per_shares'][$i])): 
                            $price_per_share = valid_currency($posts['price_per_shares'][$i]);
                            $purchase_date = strtotime($posts['purchase_dates'][$i]); 
                            if($this->transactions->record_stock_purchase($this->group->id,$purchase_date,$posts['names'][$i],currency($posts['number_of_shares'][$i]),$posts['accounts'][$i],$price_per_share)){
                                $successful_stock_purchase_entry_count++;
                            }else{
                                $unsuccessful_stock_purchase_entry_count++;
                            }
                        endif;
                    endfor;
                }
                if($successful_stock_purchase_entry_count){                
                    if($successful_stock_purchase_entry_count==1){
                        $response = array(
                            'status'=>1,
                            'message'=>$successful_stock_purchase_entry_count.' stock purchase successfully recorded. ',
                            'refer'=>site_url('group/stocks/listing')
                        );
                    }else{
                        $response = array(
                            'status'=>1,
                            'message'=>$successful_stock_purchase_entry_count.' stock purchases successfully recorded. ',
                            'refer'=>site_url('group/stocks/listing')
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => $unsuccessful_stock_purchase_entry_count.' unsuccessful stock record entries',
                    );
                }
            }else{
                $response = array(
                    'status'=>0,
                    'message'=>'There are some errors on the form. Please review and try again',
                );
            } 
        }else{
            $response = array(
                'status'=>0,
                'message'=>'No stock purchases submitted.',
            );
        }
        
        echo json_encode($response);
    }

    function _is_less_than_or_equal_to_shares_available(){
        if($this->input->post('number_of_shares_available')<$this->input->post('number_of_shares_to_be_sold')){
            $this->form_validation->set_message('_is_less_than_or_equal_to_shares_available', 'Number of sold shares cannot exceed available shares');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function sell(){
        $response = array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->stocks_m->get_group_stock($id);
            if($post){
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
                        $response = array(
                            'status' => 1,
                            'message' => 'Stock sale recorded successfully.',
                            'refer'=>site_url('group/stocks/stock_sales')
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Something went wrong during the recording of the stock sale.',
                        );
                    }
                }else{
                    $post = array();
                    $form_errors = $this->form_validation->error_array();
                    foreach ($form_errors as $key => $value) {
                        $post[$key] = $value;
                    }
                    $response = array(
                        'status' => 0,
                        'message' => 'There are some errors on the form. Please review and try again.',
                        'validation_errors' => $post,
                    );
                }
            }else{
                $response = array(
                    'status'=>0,
                    'message'=>'Stock details missing'
                );
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'Stock id variable required'
            );
        }
        echo json_encode($response);      

    }

    function get_stocks_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $withdrawal_account_options = $this->accounts_m->get_active_group_account_options(FALSE,'','','',TRUE);
        $filter_parameters = array(
            'to' => $to,
            'from' => $from,
            'stocks' => $this->input->get('stocks'),
        );
        $total_rows = $this->stocks_m->count_group_stocks($filter_parameters);
        $pagination = create_pagination('group/stocks/listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->stocks_m->limit($pagination['limit'])->get_group_stocks($filter_parameters);
        if(!empty($posts)){ 
            echo form_open('group/stocks/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Stocks</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
                echo '
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th width=\'2%\' nowrap>
                                <label class="m-checkbox">
                                    <input type="checkbox" name="check" value="all" class="check_all">
                                    <span></span>
                                </label>
                            </th>
                            <th width=\'2%\' nowrap>
                                #
                            </th>
                            <th nowrap>
                                '.translate('Stock').'
                            </th>
                            <th nowrap>
                                '.translate('Purchase Date').'
                            </th>
                             <th nowrap>
                                '.translate('Current Shares').'
                            </th>
                            <th nowrap>
                                '.translate('Shares Sold').'
                            </th>
                            <th class=\'text-right\' nowrap>
                                '.translate('Purchase Price').' ('.$this->group_currency.')
                            </th>
                          
                           
                            <th class=\'text-right\' nowrap>
                                '.translate('Current Price').' ('.$this->group_currency.')
                            </th>
                           
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); foreach($posts as $post):
                        echo '
                            <tr>
                                <td>
                                    <label class="m-checkbox">
                                        <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>'.($i+1).'</td>
                                <td>
                                    '.$post->name.'
                                </td>
                                <td>
                                    '.timestamp_to_date($post->purchase_date).'
                                </td>
                                 <td class=\'text-right\'>
                                    '.$post->number_of_shares.'
                                </td>
                                <td class=\'text-right\'>
                                    '.($post->number_of_shares_sold?:0).'
                                </td>
                                <td class=\'text-right\'>
                                    '.number_to_currency($post->purchase_price).'
                                </td>
                                
                                <td class=\'text-right\'>
                                    <a href="javascript:;" class="update_current_price"  data-type="text" data-pk="1" data-url="'.site_url('group/stocks/ajax_update_stock_current_price/'.$post->id).'" data-title="Enter Current Price" data-placement="top">'.number_to_currency($post->current_price).'</a>
                                </td>
                               
                                <td nowrap>
                                    <a href="'.site_url('group/stocks/sell/'.$post->id).'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button">
                                        <span>
                                            <i class="la la-money"></i>
                                            <span>
                                                '.translate('Sell').' &nbsp;&nbsp; 
                                            </span>
                                        </span>
                                    </a>

                                    <a href="'.site_url('group/stocks/void/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to void stock purchase?">
                                        <span>
                                            <i class="la la-trash"></i>
                                            <span>
                                                '.translate('Void').' &nbsp;&nbsp;
                                            </span>
                                        </span>
                                    </a>
                                </td>
                            </tr>';
                            $i++;
                        endforeach;
                        echo'
                    </tbody>
                </table>
                <div class="clearfix"></div>
                <div class="row col-md-12">';
                if( ! empty($pagination['links'])): 
                echo $pagination['links']; 
                endif; 
                echo ' 
                </div>
                <div class="clearfix"></div>';
                if($posts):
                    echo '
                    <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> '.translate('Bulk Void').'</button>';
                endif;
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show mt-3" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate('No stocks to display.').'.
                </div>
            ';
        }
    }



    function get_stock_sales_listing(){ 
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'from' => $from,
            'to' => $to,
            'accounts' => $this->input->get('accounts'),
            'stocks' => $this->input->get('stocks'),
        );
        $total_rows = $this->deposits_m->count_group_stock_sale_deposits($this->group->id,$filter_parameters);
        $pagination = create_pagination('group/stocks/stock_sales/pages', $total_rows,50,5,TRUE);
        $account_options = $this->accounts_m->get_group_account_options(FALSE);
        $stock_options = $this->stocks_m->get_group_stock_options();
        $posts = $this->deposits_m->limit($pagination['limit'])->get_group_stock_sale_deposits($this->group->id,$filter_parameters);
        if(!empty($posts)){
            echo form_open('group/deposits/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Stock Sales</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
            echo ' 
            <table class="table m-table m-table--head-separator-primary">
                <thead>
                    <tr>
                        <th width=\'2%\' nowrap>
                            <label class="m-checkbox">
                                <input type="checkbox" name="check" value="all" class="check_all">
                                <span></span>
                            </label>
                        </th>
                        <th width=\'2%\' nowrap>
                            #
                        </th>
                        <th nowrap>
                            Stock
                        </th>
                        <th nowrap>
                            Sale Date
                        </th>
                        <th nowrap>
                            No. of Shares Sold
                        </th>
                        <th class=\'text-right\' nowrap>
                            Sale Price per Share  ('.$this->group_currency.')
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    $i = $this->uri->segment(5, 0); 
                    foreach($posts as $post):
                    echo '
                        <tr>
                            <td>
                                <label class="m-checkbox">
                                    <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                    <span></span>
                                </label>
                            </td>
                            <td>'.($i+1).'</td>
                            <td>
                                '.$stock_options[$post->stock_id].'
                            </td>
                            <td>
                                '.timestamp_to_date_and_time($post->deposit_date).'<br/>
                            </td>
                            <td>
                                '.$post->number_of_shares_sold.'
                            </td>
                            <td class=\'text-right\'>
                                '.number_to_currency($post->sale_price_per_share).'
                            </td>
                            <td>
                                <a href="'.site_url('group/deposits/void/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to void stock sale?">
                                    <span>
                                        <i class="la la-trash"></i>
                                        <span>
                                            Void &nbsp;&nbsp;
                                        </span>
                                    </span>
                                </a>
                            </td>
                        </tr>';
                        $i++;
                    endforeach;
                    echo '
                </tbody>
            </table>
            <div class="clearfix"></div>
            <div class="row col-md-12">'; 
            if( ! empty($pagination['links'])): 
            echo $pagination['links']; 
            endif; 
                echo '
                </div>
                <div class="clearfix"></div>';
                if($posts):
                    echo '<button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
                endif;
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate(' No stock sales to display.').'.
                </div>';
        }
    }
}

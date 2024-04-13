<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

    protected $validation_rules=array(
        array(
                'field' =>   'name',
                'label' =>   'Asset Name',
                'rules' =>   'xss_clean|trim|required',
            ),
        array(
                'field' =>   'cost',
                'label' =>   'Asset Cost',
                'rules' =>   'trim|required|currency',
            ),
        array(
                'field' =>   'asset_category_id',
                'label' =>   'Asset Category',
                'rules' =>   'xss_clean|trim|required|numeric',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Asset Description',
                'rules' =>   'xss_clean|trim',
            ),
    );
	
	protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->model('assets_m');
        $this->load->model('deposits/deposits_m');
        $this->load->model('accounts/accounts_m');
	}

    function get_assets_listing(){
        $total_rows = $this->assets_m->count_group_assets();
        $pagination = create_pagination('group/assets/listing/pages',$total_rows,50,5,TRUE);
        $asset_category_options = $this->asset_categories_m->get_group_asset_category_options();
        $posts = $this->assets_m->limit($pagination['limit'])->get_group_assets();
        if(!empty($posts)){
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="btn btn-info">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Assets</p>';
    
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
                echo '
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th width=\'2%\'>
                                #
                            </th>
                            <th>
                                Asset Name
                            </th>
                            <th>
                                Asset Category
                            </th>  
                            <th class="text-right">
                                Cost ('.$this->group_currency.')
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); $i++; 
                        foreach($posts as $post):
                            echo '
                            <tr>
                                <td>'.($i++).'</td>
                                <td>'.$post->name.'</td>
                                <td>'.$asset_category_options[$post->asset_category_id].'';
                                    if($post->description){
                                        echo 
                                        '<br/><strong>Description</strong><hr/>
                                        '.$post->description.'';
                                    }
                                    echo '
                                </td> 
                                <td class="text-right">
                                    '.number_to_currency($post->cost).'
                                </td>  
                                <td>';
                                    if($post->active){
                                        echo '
                                            <a href="'.site_url('group/assets/sell/'.$post->id).'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button">
                                                <span>
                                                    <i class="la la-money"></i>
                                                    <span>
                                                        Sell &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>

                                            <a href="'.site_url('group/assets/void/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to void asset purchase?">
                                                <span>
                                                    <i class="la la-trash"></i>
                                                    <span>
                                                        Void &nbsp;&nbsp;
                                                    </span>
                                                </span>
                                            </a>
                                        ';
                                    }
                                    echo '
                                </td>
                            </tr>';
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
        
        }else{
            echo '
            <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                <strong>info !&nbsp;</strong>
                No assets to display.                       
            </div>';
        }
    }

    function get_asset_sales_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'to' => $to,
            'from' => $from,
            'accounts' => $this->input->get('accounts'),
            'assets' => $this->input->get('assets'),
        );
        $total_rows = $this->deposits_m->count_group_asset_sale_deposits($this->group->id,$filter_parameters);
        $pagination = create_pagination('group/assets/asset_sales/pages', $total_rows,50,5,TRUE);
        $account_options = $this->accounts_m->get_group_account_options(FALSE);
        $asset_options = $this->assets_m->get_group_asset_options();
        $posts = $this->deposits_m->limit($pagination['limit'])->get_group_asset_sale_deposits($this->group->id,$filter_parameters);
        if(!empty($posts)){ 
            echo form_open('group/deposits/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Asset Sales</p>';
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
                                Sale Date
                            </th>
                            <th nowrap>
                                Asset Name
                            </th>
                            <th nowrap>
                                Asset Category
                            </th>
                            <th nowrap>
                                Selling Account
                            </th>
                            <th class=\'text-right\' nowrap>
                                Amount  ('.$this->group_currency.')
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
                                <td>'.timestamp_to_date($post->deposit_date).'</td>
                                <td>'.$asset_options[$post->asset_id].'</td>
                                <td>'.$asset_options[$post->asset_id].'</td>
                                <td>'.$account_options[$post->account_id].'</td>
                                <td class=\'text-right\'>
                                    '.number_to_currency($post->amount).'
                                </td>
                                <td>
                                    <a href="'.site_url('group/deposits/void/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to void deposit?">
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
                echo '
                <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
            endif;
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show mt-4" role="alert">
                    <strong>'.translate('Opps').'!</strong> '.translate(' Seems you have not sold any asset yet.').'.
                </div>';            
        }
    }

    function create(){
        $data = array();
        $response = array();
        $errors = array();
        $error_messages = array();
        $successes = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $asset = array(
                'name'  =>  $this->input->post('name'),
                'cost'  =>  $this->input->post('cost'),
                'asset_category_id'  =>  $this->input->post('asset_category_id'),
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );
            $id = $this->assets_m->insert($asset);
            if($id){
                $asset['id'] = $id;
                $response = array(
                    'status' => 1,
                    'asset'=>$asset,
                    'id'=>$id,
                    'name'=>$asset['name'],
                    'message' => 'Successfully created.',
                );
               
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add asset.',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
        }

        echo json_encode($response);
    }

    function record_asset_purchase_payments(){
        $data = array();
        $posts = $_POST;
        $errors = array();
        $response = array();
        $error_messages = array();
        $successes = array();
        $entries_are_valid = TRUE;
        if(!empty($posts)){
            if(isset($posts['payment_dates'])){
                $count = count($posts['payment_dates']);
                for ($i=0; $i <$count; $i++):
                    if(isset($posts['payment_dates'][$i])&&isset($posts['assets'][$i])&&isset($posts['accounts'][$i])&&isset($posts['payment_methods'][$i])&&isset($posts['amounts'][$i])):

                        //Payment dates
                        if($posts['payment_dates'][$i]==''){
                            $successes['payment_dates'][$i] = 0;
                            $errors['payment_dates'][$i] = 1;
                            $error_messages['payment_dates'][$i] = 'Please enter a date';
                            $entries_are_valid = FALSE;
                        }else{
                            $successes['payment_dates'][$i] = 1;
                            $errors['payment_dates'][$i] = 0;
                        }

                        //assets
                        if($posts['assets'][$i]==''){
                            $successes['assets'][$i] = 0;
                            $errors['assets'][$i] = 1;
                            $error_messages['assets'][$i] = 'Please select a asset';
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($posts['assets'][$i])){
                                $successes['assets'][$i] = 1;
                                $errors['assets'][$i] = 0;
                            }else{
                                $successes['assets'][$i] = 0;
                                $errors['assets'][$i] = 1;
                                $error_messages['assets'][$i] = 'Please enter a valid asset value';
                                $entries_are_valid = FALSE;
                            }
                        }

                        //Accounts
                        if($posts['accounts'][$i]==''){
                            $successes['accounts'][$i] = 0;
                            $errors['accounts'][$i] = 1;
                            $error_messages['accounts'][$i] = 'Please select an account';
                            $entries_are_valid = FALSE;
                        }else{
                            $successes['accounts'][$i] = 1;
                            $errors['accounts'][$i] = 0;
                        }

                        //Deposit Method
                        if($posts['payment_methods'][$i]==''){
                            $successes['payment_methods'][$i] = 0;
                            $errors['payment_methods'][$i] = 1;
                            $error_messages['payment_methods'][$i] = 'Please select a payment method';
                            $entries_are_valid = FALSE;
                        }else{
                            if(is_numeric($posts['payment_methods'][$i])){
                                $successes['payment_methods'][$i] = 1;
                                $errors['payment_methods'][$i] = 0;
                            }else{
                                $successes['payment_methods'][$i] = 0;
                                $errors['payment_methods'][$i] = 1;
                                $error_messages['payment_methods'][$i] = 'Please enter a valid payment method';
                                $entries_are_valid = FALSE;
                            }
                        }
                        
                        //amounts
                        if($posts['amounts'][$i]==''){
                            $successes['amounts'][$i] = 0;
                            $errors['amounts'][$i] = 1;
                            $error_messages['amounts'][$i] = 'Please enter a payment amount';
                            $entries_are_valid = FALSE;
                        }else{
                            if(valid_currency($posts['amounts'][$i])){
                                $successes['amounts'][$i] = 1;
                                $errors['amounts'][$i] = 0;
                            }else{
                                $successes['amounts'][$i] = 0;
                                $errors['amounts'][$i] = 1;
                                $error_messages['amounts'][$i] = 'Please enter a valid payment amount';
                                $entries_are_valid = FALSE; 
                            }
                        }
                    endif;
                endfor;
            }

        }

        if($entries_are_valid){
            $this->session->set_flashdata('error','');
            if(isset($posts['payment_dates'])){
                $count = count($posts['payment_dates']);
                $successful_asset_payment_entry_count = 0;
                $unsuccessful_asset_payment_entry_count = 0;
                $count = count($posts['payment_dates']);
                for($i=0;$i<=$count;$i++):
                    if(isset($posts['payment_dates'][$i])&&isset($posts['assets'][$i])&&isset($posts['accounts'][$i])&&isset($posts['payment_methods'][$i])&&isset($posts['amounts'][$i])):    
                        $amount = valid_currency($posts['amounts'][$i]);
                        $payment_date = strtotime($posts['payment_dates'][$i]); 
                        $description = isset($posts['payment_descriptions'][$i])?$posts['payment_descriptions'][$i]:'';
                        if($this->transactions->record_asset_purchase_payment($this->group->id,$payment_date,$posts['assets'][$i],$posts['accounts'][$i],$posts['payment_methods'][$i],$description,$amount)){
                            $successful_asset_payment_entry_count++;
                        }else{
                            $unsuccessful_asset_payment_entry_count++;
                        }
                    endif;
                endfor;
            }
            if($successful_asset_payment_entry_count){
                if($successful_asset_payment_entry_count==1){
                    $response = array(
                        'status' => 1,
                        'message' => 'success',$successful_asset_payment_entry_count.' asset purchase payment successfully recorded. ',
                        'refer'=>site_url('group/assets/asset_purchase_payments')
                    );
                }else{
                    $response = array(
                        'status' => 1,
                        'message' => 'success',$successful_asset_payment_entry_count.' asset purchase payments unsuccessfully recorded. ',
                        'refer'=>site_url('group/assets/asset_purchase_payments')
                    );
                }
            }

        }else{
            $response = array(
                'status' => 0,
                'validation_errors'=>$error_messages,
                'message' => 'There are some errors on the form. Please review and try again.',
            );
        }
        echo json_encode($response);
    }

    function sell(){
        $response = array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->assets_m->get_group_asset($id);
            if($post){
                $validation_rules=array(
                    array(
                            'field' =>  'amount',
                            'label' =>  'Sale Amount',
                            'rules' =>  'xss_clean|trim|required|currency',
                        ),
                    array(
                            'field' =>   'sale_date',
                            'label' =>   'Sale Date',
                            'rules' =>   'xss_clean|trim|required',
                        ),
                    array(
                            'field' =>   'account_id',
                            'label' =>   'Account',
                            'rules' =>   'xss_clean|trim|required',
                        ),
                );

                $this->form_validation->set_rules($validation_rules);
                if($this->form_validation->run()){
                    $sale_date = strtotime($this->input->post('sale_date'));
                    $account_id = $this->input->post('account_id');
                    $amount = $this->input->post('amount');
                    if($this->transactions->record_asset_sale_deposit($this->group->id,$id,$sale_date,$account_id,$amount)){
                        $response = array(
                            'status' => 1,
                            'message' => 'Asset sale recorded successfully.',
                            'refer'=>site_url('group/assets/asset_sales')
                        );
                        //$this->session->set_flashdata('success','Asset sale recorded successfully');
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Asset sale not recorded successfully.',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'There are some errors on the form. Please review and try again.',
                        'validation_errors' => $this->form_validation->error_array(),
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Asset details is missing.',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Asset id is required.',
            );
        }
        echo json_encode($response);        
    }

    function ajax_void(){
        $response =  array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->assets_m->get_group_asset($id);
            if($post){
                if($this->transactions->void_asset($id,$post)){
                    $response = array(
                        'status'=>1,
                        'message'=>'Asset voided successfully'
                    );
                }else{
                    $response = array(
                        'status'=>0,
                        'message'=>'Asset could not be voided successfully'
                    );
                }
            }else{
                $response = array(
                    'status'=>0,
                    'message'=>'Asset details could not be found'
                );
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'Asset id is missing'
            );
        }
        echo json_encode($response);

    }

}
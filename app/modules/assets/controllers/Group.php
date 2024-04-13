<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Group extends Group_Controller{

    protected $validation_rules=array(
        array(
                'field' =>   'name',
                'label' =>   'Asset Name',
                'rules' =>   'trim|required',
            ),
        array(
                'field' =>   'cost',
                'label' =>   'Asset Cost',
                'rules' =>   'trim|required|currency',
            ),
        array(
                'field' =>   'asset_category_id',
                'label' =>   'Asset Category',
                'rules' =>   'trim|required|numeric',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Asset Description',
                'rules' =>   'trim',
            ),
    );

	public function __construct(){
        parent::__construct();
        $this->load->model('assets_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('asset_categories/asset_categories_m');
        $this->load->library('transactions');
    }

    function index(){
        $this->template->title('Assets')->build('group/index');
    }

    public function listing(){
        $data = array();
        $total_rows = $this->assets_m->count_group_assets();
        $pagination = create_pagination('group/assets/listing/pages',$total_rows,50,5,TRUE);
        $data['pagination'] = $pagination;
        $data['asset_category_options'] = $this->asset_categories_m->get_group_asset_category_options();
        $data['posts'] = $this->assets_m->limit($pagination['limit'])->get_group_assets();
        if($this->input->get('generate_excel')==1){
            $data['group_currency'] = $this->group_currency;
            $data['group'] = $this->group;
            $json_file = json_encode($data);
            //print_r($json_file);die;
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/assets/listing',$this->group->name.' Asset Listing'));
            die;
        }
        $this->template->title(translate('List Assets'))->build('group/listing',$data);
    }


    public function record_asset_purchase_payments(){
    	$data = array();
        $posts = $_POST;
        $errors = array();
        $error_messages = array();
        $successes = array();
        if($this->input->post('submit')){
            $entries_are_valid = TRUE;
            if(!empty($posts)){ 
                if(isset($posts['payment_dates'])){
                    $count = count($posts['payment_dates']);
                    for($i=0;$i<=$count;$i++):
                        if(isset($posts['payment_dates'][$i])&&isset($posts['assets'][$i])&&isset($posts['accounts'][$i])&&isset($posts['payment_methods'][$i])&&isset($posts['amounts'][$i])):    
                            //Deposit dates
                            if($posts['payment_dates'][$i]==''){
                                $successes['payment_dates'][$i] = 0;
                                $errors['payment_dates'][$i] = 1;
                                $error_messages['payment_dates'][$i] = 'Please enter a date';
                                $entries_are_valid = FALSE;
                            }else{
                                $successes['payment_dates'][$i] = 1;
                                $errors['payment_dates'][$i] = 0;
                            }

                            //Members
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
                        $this->session->set_flashdata('success',$successful_asset_payment_entry_count.' asset purchase payment successfully recorded. ');
                    }else{
                        $this->session->set_flashdata('success',$successful_asset_payment_entry_count.' asset purchase payments successfully recorded. ');
                    }
                }
                redirect('group/assets/asset_purchase_payments');
            }else{
                $this->session->set_flashdata('error','There are some errors on the form. Please review and try again.');
            }
        }
        $data['posts'] = $posts;
        $data['errors'] = $errors;
        $data['error_messages'] = $error_messages;
        $data['successes'] = $successes;
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        // $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $data['asset_category_options'] = $this->asset_categories_m->get_group_asset_category_options();
        $data['withdrawal_method_options'] = $this->transactions->withdrawal_method_options;
        $data['asset_options'] = $this->assets_m->get_group_asset_options();
        $this->template->title(translate('Record Asset Purchase Payments'))->build('group/record_asset_purchase_payments',$data);
    }

    public function asset_purchase_payments(){
        $data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['from'] = $from;
        $data['to'] = $to;
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['asset_options'] = $this->assets_m->get_group_asset_options();
        if($this->input->get('generate_excel')==1){
            $filter_parameters = array(
                'from' => $from,
                'to' => $to,
                'accounts' => $this->input->get('accounts'),
                'assets' => $this->input->get('assets'),
            );
            $data['withdrawal_transaction_names'] = $this->transactions->withdrawal_transaction_names;
            $data['posts'] = $this->withdrawals_m->get_group_asset_purchase_withdrawals($filter_parameters);
            $data['group_currency'] = $this->group_currency;
            $data['group'] = $this->group;
            $json_file = json_encode($data);
            //print_r($json_file);die;
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/assets/asset_purchase_payments',$this->group->name.' Asset Purchase Payments'));
            die;
        }
        $this->template->title(translate('Asset Purchase Payments'))->build('group/asset_purchase_payments',$data);
    }

    public function ajax_get_asset_purchase_payments_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'from' => $from,
            'to' => $to,
            'accounts' => $this->input->get('accounts'),
            'assets' => $this->input->get('assets'),
        );
        $total_rows = $this->withdrawals_m->count_group_asset_purchase_withdrawals($filter_parameters);
        $pagination = create_pagination('group/asset_purchase_payments/listing/pages',$total_rows,50,5,TRUE);
        $withdrawal_transaction_names = $this->transactions->withdrawal_transaction_names;
        $asset_options = $this->assets_m->get_group_asset_options();
        $posts = $this->withdrawals_m->limit($pagination['limit'])->get_group_asset_purchase_withdrawals($filter_parameters);
        $account_options = $this->accounts_m->get_group_account_options(FALSE);
        if(!empty($posts)){
            echo form_open('group/withdrawals/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Asset Purchase Payments</p>';
    
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
                            Purchase date
                        </th>
                        <th nowrap>
                            Purchase Details
                        </th>
                        <th nowrap>
                            Purchasing Account
                        </th>
                        <th class=\'text-right\' nowrap>
                            Amount ('.$this->group_currency.')
                        </th>  
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    $i = $this->uri->segment(5, 0); $i++; foreach($posts as $post):
                    echo '
                        <tr id='.$post->id.'_active_row>
                            <td>
                                <label class="m-checkbox">
                                    <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                    <span></span>
                                </label>
                            </td>
                            <td>'.($i++).'</td>
                            <td>'.timestamp_to_date($post->withdrawal_date).'</td>
                            <td>';
                                if($post->type==1||$post->type==2||$post->type==3||$post->type==4){
                                    echo $withdrawal_transaction_names[$post->type].' for '.$expense_category_options[$post->expense_category_id];
                                }else if($post->type==5||$post->type==6||$post->type==7||$post->type==8){
                                    echo $withdrawal_transaction_names[$post->type].' for '.$asset_options[$post->asset_id];
                                }
                                if($post->description){
                                    echo ' : '.$post->description;
                                }
                        echo '
                            </td>
                            <td>'.$account_options[$post->account_id].'</td>
                            <td  class=\'text-right\'>
                                '.number_to_currency($post->amount).'
                            </td>  
                            <td>
                                <a href="'.site_url('group/assets/void/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to void asset purchase?">
                                    <span>
                                        <i class="la la-trash"></i>
                                        <span>
                                            Void &nbsp;&nbsp;
                                        </span>
                                    </span>
                                </a>
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
            if($posts):
                echo '<button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
            endif;
            echo form_close();
        }else{
            echo '
            <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                </button>
                <strong>Heads up!</strong> No asset purchase payments to display.
            </div>';
        } 
    }

    function edit($id=0){
        $id OR redirect('group/assets/listing');
        $post = $this->assets_m->get($id);
        if(!$post){
            redirect('group/assets/listing');
        }
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $array = array(
                    'name'  =>  $this->input->post('name'),
                    'cost'  =>  $this->input->post('cost'),
                    'asset_category_id'  =>  $this->input->post('asset_category_id'),
                    'group_id'  =>  $this->group->id,
                    'modified_by'    =>  $this->user->id,
                    'modified_on'    =>  time(),
                );
            if($this->assets_m->update($id,$array)){
                $this->session->set_flashdata('success',"Successfully updated");
            }else{
                $this->session->set_flashdata('error',"Unable to update asset");
            }
            redirect('group/assets/listing');
        }else{
            foreach (array_keys($this->validation_rules) as $field)
            {
                 if (isset($_POST[$field]))
                {
                    $post->$field = $this->form_validation->$field;
                }
            }
        }
        $data['post'] = $post;
        $data['asset_categories'] = $this->asset_categories_m->get_group_asset_category_options();
        $data['id'] = $post->id;

        $this->template->title('Edit '.$post->name)->build('group/edit',$data);
    }

    public function ajax_create(){
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->assets_m->insert(array(
                'name'  =>  $this->input->post('name'),
                'cost'  =>  $this->input->post('cost'),
                'asset_category_id'  =>  $this->input->post('asset_category_id'),
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
                )
            );
            if($id){
                if($asset = $this->assets_m->get_group_asset($id)){
                    echo json_encode($asset);
                }else{
                    echo 'Could not add find any asset ';
                }
            }else{
                echo 'Could not add asset ';
            }
        }else{
            echo validation_errors();
        }
    }

    public function sell($id = 0){
        $id OR redirect('group/assets/listing');
        $post = $this->assets_m->get_group_asset($id);
        $post OR redirect('group/assets/listing');
        $validation_rules=array(
            array(
                    'field' =>  'amount',
                    'label' =>  'Sale Amount',
                    'rules' =>  'trim|required|currency',
                ),
            array(
                    'field' =>   'sale_date',
                    'label' =>   'Sale Date',
                    'rules' =>   'trim|required',
                ),
            array(
                    'field' =>   'account_id',
                    'label' =>   'Account',
                    'rules' =>   'trim|required',
                ),
        );

        $this->form_validation->set_rules($validation_rules);
        if($this->form_validation->run()){
            $sale_date = strtotime($this->input->post('sale_date'));
            $account_id = $this->input->post('account_id');
            $amount = $this->input->post('amount');
            if($this->transactions->record_asset_sale_deposit($this->group->id,$id,$sale_date,$account_id,$amount)){
                $this->session->set_flashdata('success','Asset sale recorded successfully');
            }else{
                $this->session->set_flashdata('error','Asset sale not recorded successfully');
            }
            redirect('group/assets/asset_sales');
        }else{
            foreach($validation_rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }

        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        // $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $asset_category_options = $this->asset_categories_m->get_group_asset_category_options();
        $data['post'] = $post;
        $this->template->title('Sell Asset '.$post->name.' '.$asset_category_options[$post->asset_category_id])->build('group/sell',$data);
    }

    public function asset_sales(){
        $data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['from'] = $from;
        $data['to'] = $to;
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['asset_options'] = $this->assets_m->get_group_asset_options();
        if($this->input->get('generate_excel')==1){
            $filter_parameters = array(
                'to' => $to,
                'from' => $from,
                'accounts' => $this->input->get('accounts'),
                'assets' => $this->input->get('assets'),
            );
            $data['posts'] = $this->deposits_m->get_group_asset_sale_deposits($this->group->id,$filter_parameters);
            $data['group_currency'] = $this->group_currency;
            $data['group'] = $this->group;
            $json_file = json_encode($data);
            //print_r($json_file);die;
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/assets/asset_sales',$this->group->name.' Asset Purchase Sells'));
            die;
        }
        $this->template->title(translate('Asset Sales'))->build('group/asset_sales',$data);
    }

    public function void($id = 0,$redirect = TRUE){
        $id OR redirect('group/assets/listing');
        $post = $this->assets_m->get_group_asset($id);
        $post OR redirect('group/assets/listing');
        if($this->transactions->void_asset($id,$post)){
            $this->session->set_flashdata('success','Asset voided successfully');
        }else{
            $this->session->set_flashdata('error','Asset could not be voided successfully');
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/assets/listing');
            }
        }
    }



    function ajax_assets_listing(){
        $asset_purchases = $this->withdrawals_m->get_group_back_dating_asset_purchase_payments();
        $asset_objects_array = $this->assets_m->get_group_back_dating_asset_objects_array();
        $asset_purchase_objects_array = $this->withdrawals_m->get_group_back_dating_asset_purchase_objects_array();
        $asset_sale_objects_array = $this->deposits_m->get_group_back_dating_asset_sale_objects_array();
        $asset_category_options = $this->asset_categories_m->get_group_asset_category_options();
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();

        if($asset_purchases){
        echo '<h4>Back-dated Group Assets</h4>';
        echo '<table id="back-date-assets-table" class="table table-striped table-bordered table-hover table-header-fixed table-condensed">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Asset Name
                        </th>
                        <th width="15%">
                            Asset Category
                        </th>
                        <th class="text-right">
                            Cost ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Payments Made in Total ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Sales Made in Total ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody id=\'append-place-holder\'>';
                    $count = 1;
                    $i = 0;
                    foreach($asset_purchases as $asset_purchase):
                        if(isset($asset_objects_array[$asset_purchase->asset_id])){
                            $asset = $asset_objects_array[$asset_purchase->asset_id];
                            echo '
                            <tr>
                                <td>'.$count++.'</td>
                                <td>
                                    '.$asset->name.'
                                </td>
                                <td>
                                    '.$asset_category_options[$asset->asset_category_id].'
                                </td>
                                <td class="text-right">
                                    '.number_to_currency($asset->cost).'
                                </td>
                                <td class="text-right">';
                                        if(isset($asset_purchase_objects_array[$asset->id])){
                                            $payment_made = $asset_purchase_objects_array[$asset->id]->amount;
                                        }else{
                                            $payment_made = "";
                                        }
                                        echo number_to_currency($payment_made).'
                                </td>
                                <td class="text-right"> 
                                    ';
                                        if(isset($asset_sale_objects_array[$asset->id])){
                                            $sale_made = $asset_sale_objects_array[$asset->id]->amount;
                                        }else{
                                            $sale_made = "";
                                        }
                                        echo number_to_currency($sale_made).'
                                </td>
                            </tr>'; 
                            $i++;
                        }
                    endforeach; 
                echo '
                </tbody>
            </table>';
         }else{
            echo '
            <div class="alert alert-info">
                <strong>Information!</strong> No assets to display
            </div>
            ';
        }
    }

    function ajax_assets_form(){
        $asset_purchases = $this->withdrawals_m->get_group_back_dating_asset_purchase_payments();
        $asset_objects_array = $this->assets_m->get_group_back_dating_asset_objects_array();
        $asset_purchase_objects_array = $this->withdrawals_m->get_group_back_dating_asset_purchase_objects_array();
        $asset_sale_objects_array = $this->deposits_m->get_group_back_dating_asset_sale_objects_array();
        $asset_category_options = $this->asset_categories_m->get_group_asset_category_options();
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        echo '<h4>Back-dated Group Assets</h4>';
        echo '
            <div class="alert alert-info">
                <strong>Information!</strong> How many assets <strong>had</strong> the group bought and sold as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
            </div>';
            echo '
            <table id="back-date-assets-table" class="back-date-assets-table table table-striped table-bordered table-condensed table-hover table-multiple-items">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Asset Name
                        </th>
                        <th width="15%">
                            Asset Category
                        </th>
                        <th class="text-right">
                            Cost ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Payments Made in Total ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Sales Made in Total ('.$this->group_currency.')
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id=\'append-place-holder\'>';
                        if($asset_purchases){
                            $count = 1;
                            $i = 0;
                            foreach($asset_purchases as $asset_purchase):
                                if(isset($asset_objects_array[$asset_purchase->asset_id])){
                                    $asset = $asset_objects_array[$asset_purchase->asset_id];
                                    echo '
                                    <tr>
                                        <td class="count">'.$count++.'</td>
                                        <td>
                                            <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                                <i class="" ></i>
                                                    '.form_input('asset_names['.$i.']',$asset->name," class='form-control asset_name'").'
                                            </div>
                                        </td>
                                        <td>
                                            <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                                <i class="" ></i>
                                                    '.form_dropdown('asset_categories['.$i.']',array(''=>"Select Asset Category")+$asset_category_options,$asset->asset_category_id," class='form-control asset_category modal_select2'").'
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                                <i class="" ></i> 
                                                    '.form_input('costs['.$i.']',$asset->cost," class='form-control currency cost'").'
                                            </div>
                                        </td>
                                        <td>
                                            <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                                <i class="" ></i>';
                                                if(isset($asset_purchase_objects_array[$asset->id])){
                                                    $payment_made = $asset_purchase_objects_array[$asset->id]->amount;
                                                }else{
                                                    $payment_made = "";
                                                }
                                                echo form_input('payments_made['.$i.']',$payment_made," class='form-control currency payment_made  '").'
                                            </div>
                                        </td>
                                        <td class="text-right"> 
                                            <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                                <i class="" ></i>';
                                                if(isset($asset_sale_objects_array[$asset->id])){
                                                    $sale_made = $asset_sale_objects_array[$asset->id]->amount;
                                                }else{
                                                    $sale_made = "";
                                                }
                                                echo form_input('sales_made['.$i.']',$sale_made," class='form-control currency sale_made '").'
                                            </div>
                                        </td>
                                        <td>
                                            <a data-original-title="Remove line"  href="javascript:;" class="tooltips remove-line btn btn-icon-only red">
                                                <i class="fa fa-times"></i>
                                            </a>
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
                                            '.form_input('asset_names[]',""," class='form-control asset_name'").'
                                    </div>
                                </td>
                                <td>
                                    <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                        <i class="" ></i>
                                            '.form_dropdown('asset_categories[]',array(''=>"Select Asset Category")+$asset_category_options,""," class='form-control asset_category modal_select2'").'
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                        <i class="" ></i> 
                                            '.form_input('costs[]',""," class='form-control currency cost'").'
                                    </div>
                                </td>
                                <td>
                                    <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                        <i class="" ></i> 
                                            '.form_input('payments_made[]',""," class='form-control currency payment_made  '").'
                                    </div>
                                </td>
                                <td class="text-right"> 
                                    <div class=" input-icon tooltips right" data-original-title="" data-container="">
                                        <i class="" ></i> 
                                            '.form_input('sales_made[]',""," class='form-control currency sale_made '").'
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
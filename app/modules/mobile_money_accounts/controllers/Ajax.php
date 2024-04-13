<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->model('mobile_money_accounts_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->model('transaction_statements/transaction_statements_m');
    }

    protected $validation_rules = array(
        array(
                'field' =>  'account_name',
                'label' =>  'Account Name',
                'rules' =>  'xss_clean|trim|required'
            ),
        array(
                'field' =>  'mobile_money_provider_id',
                'label' =>  'Mobile Money Provider',
                'rules' =>  'xss_clean|required|trim|numeric'
            ),
        array(
                'field' =>  'account_number',
                'label' =>  'Account Number/ Till Number/ Phone Number',
                'rules' =>  'xss_clean|required|trim|numeric|callback__is_unique_account'
            ),
        array(
                'field' =>  'initial_balance',
                'label' =>  'Initial Petty Cash Account balances',
                'rules' =>  'xss_clean|trim|currency'
            ),
    );

    function _is_unique_account(){
        $account_number = $this->input->post('account_number');
        $mobile_money_provider_id = $this->input->post('mobile_money_provider_id');
        $id = $this->input->post('id');
        if($this->mobile_money_accounts_m->check_if_account_exists($id,$mobile_money_provider_id,$account_number)){
            $this->form_validation->set_message('_is_unique_account','The account number '.$account_number.' already exists and cannot allow duplicate');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function create(){
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->mobile_money_accounts_m->insert(array(
                'account_name'              =>  $this->input->post('account_name'),
                'mobile_money_provider_id'  =>  $this->input->post('mobile_money_provider_id'),
                'account_number'            =>  $this->input->post('account_number'),
                'initial_balance'           =>  currency($this->input->post('initial_balance')),
                'created_by'                =>  $this->user->id,
                'created_on'                =>  time(),
                'group_id'                  =>  $this->group->id,
                'active'                    =>  1,
            ));
            if($id){
                $mobile_money_account['id'] = $id;
                $response = array(
                    'status' => 1,
                    'refer' => site_url('bank/mobile_money_accounts/listing'),
                    'message' => 'Mobile Money account successfully added',
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add mobile money account',
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

    function edit(){
        $response = array();
        $id = $this->input->post('id');
        $post = $this->mobile_money_accounts_m->get($id);
        if($post){
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){
                
                $result = $this->mobile_money_accounts_m->update($id,array(
                    'account_name'              =>  $this->input->post('account_name'),
                    'mobile_money_provider_id'  =>  $this->input->post('mobile_money_provider_id'),
                    'account_number'            =>  $this->input->post('account_number'),
                    'initial_balance'           =>  currency($this->input->post('initial_balance')),
                    'modified_by'                =>  $this->user->id,
                    'modified_on'                =>  time(),
                    'group_id'                  =>  $this->group->id
                ));
                if($result){
                    if($mobile_money_account = $this->mobile_money_accounts_m->get_group_mobile_money_account($id)){
                        $mobile_money_providers = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
                        $mobile_money_account->mobile_money_provider_details = $mobile_money_providers[$mobile_money_account->mobile_money_provider_id];
                        $mobile_money_account->mobile_money_provider_name = $mobile_money_providers[$mobile_money_account->mobile_money_provider_id];
                        $mobile_money_account->mobile_money_account_id = $mobile_money_account->id;
                        $response = array(
                            'status' => 1,
                            'mobile_money_account'=>$mobile_money_account,
                            'message' => 'successfully edited',
                            'refer'=>site_url('bank/mobile_money_accounts/listing'),
                        );
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not add find any mobile money account',
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not add mobile money account',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add mobile money account',
                    'validation_errors' => $this->form_validation->error_array(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not not find money market account Details',
            ); 
        }
        echo json_encode($response);
    }

    function delete(){
        $response = array();
        $id = abs((int) filter_var($this->input->post('id'), FILTER_SANITIZE_NUMBER_INT));
        if($id){
            $post = $this->mobile_money_accounts_m->get_group_mobile_money_account($id);
            if($post){
                $password = $this->input->post('password');
                $identity = valid_phone($this->user->phone)?:$this->user->email;
                if($this->ion_auth->login($identity,$password)){
                    if($this->transaction_statements_m->check_if_group_account_has_transactions('mobile-'.$post->id,$post->group_id)){
                        $response = array(
                            'status'=>0,
                            'message'=>'The mobile money account has transactions associated to it, void all transactions associated to this account before deleting it'
                        );
                    }else{
                        if($this->mobile_money_accounts_m->delete($post->id,$post->group_id)){
                            $response = array(
                                'status'=>1,
                                'message'=>'Mobile money account deleted successfully'
                            );
                        }else{
                            $response = array(
                                'status'=>0,
                                'message'=>'Mobile money not be deleted'
                            );
                        }
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message'=>'You entered the wrong password'
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find the requested mobile money account',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find the requested mobile money account',
            );
        }
        echo json_encode($response);
    }

    function get(){
        $response = array();
        $id = abs((int) filter_var($this->input->post('id'), FILTER_SANITIZE_NUMBER_INT));
        if($id){
            $post = $this->mobile_money_accounts_m->get_group_mobile_money_account($id);
            if($post){
                echo json_encode($post);die;
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find the requested mobile money account',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find the requested mobile money account',
            );
        }
        echo json_encode($response);
    }
    function ajax_get_mobile_money_accounts_listing(){
        $total_rows = $this->mobile_money_accounts_m->count_all();
        $pagination = create_pagination('group/mobile_money_accounts/listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->mobile_money_accounts_m->limit($pagination['limit'])->get_group_mobile_money_accounts();
        if(!empty($posts)){ 
            echo form_open('admin/saccos/action', ' id="form"  class="form-horizontal"');  
            if ( ! empty($pagination['links'])):
                echo '
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Mobile money accounts</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                    endif; 
                echo ' 
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>                    
                            <th>
                                #
                            </th>
                            <th nowrap>
                                Account Name
                            </th>
                            <th nowrap>
                                Provider
                            </th>
                            <th nowrap>
                                Account Number
                            </th>
                            <th class="text-right" nowrap>
                                Balances ('.($this->group_currency).')
                            </th>
                            <th nowrap>
                                Status
                            </th>
                            <th width="30%" nowrap>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); foreach($posts as $post):
                        $current_balance = $post->current_balance?$post->current_balance:0;
                        $initial_balance = $post->initial_balance?$post->initial_balance:0;

                        echo '
                            <tr class="'.$post->id.'_active_row">
                                <td>'.($i+1).'</td>
                                <td>'.$post->account_name.'</td>
                                <td>'.$post->mobile_money_provider_name.'</td>
                                <td>'.$post->account_number.'</td>
                                <td class="text-right">
                                    '.number_to_currency($initial_balance + $current_balance).'
                                </td>
                                <td>';
                                        if($post->is_closed)
                                        {
                                            echo "<span class='m-badge m-badge--warning m-badge--wide'>Closed</span>";
                                        }
                                        else
                                        {
                                            if($post->active){
                                                echo "<span class='m-badge m-badge--success m-badge--wide'>Active</span>";
                                            }else{
                                                echo "<span class='m-badge m-badge--warning m-badge--wide'>Hidden</span>";
                                            }
                                        }
                                echo '
                                </td>
                                <td class="actions">';
                                    echo '
                                        <div class="btn-group">
                                            <a href="'.site_url('group/mobile_money_accounts/edit/'.$post->id).'" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i>
                                                Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu">
                                    ';
                                        if($post->is_closed){ 
                                            echo '
                                                <a href="'.site_url('group/mobile_money_accounts/reopen/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-play"></i>Reopen</a>
                                            ';
                                        }else{
                                            if($post->active){
                                                echo '
                                                    <a href="'.site_url('group/mobile_money_accounts/hide/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-eye-slash"></i>Hide</a>
                                                ';
                                            }else{
                                                echo '
                                                    <a data-original-title="Activate Mobile Money Account" href="'.site_url('group/mobile_money_accounts/activate/'.$post->id).'" class="dropdown-item confirmation_link"><i class="la la-check-square-o"></i>Unhide</a>
                                                ';
                                            }
                                            echo '
                                                <a data-original-title="Close Mobile Money Account" href="'.site_url('group/mobile_money_accounts/close/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-pause"></i>Close</a>
                                            ';
                                        }
                                    echo '
                                        <a data-original-title="Delete Mobile Money Account" href="'.site_url('group/mobile_money_accounts/delete/'.$post->id).'" class="dropdown-item prompt_confirmation_message_link" id="'.$post->id.'"><i class="fa fa-trash"></i>Delete</a>

                                        </div>
                                    </div>
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
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    </button>
                    <strong>Ooops!</strong> Looks like you do not have any Mobile Money accounts configured.
                </div>
            ';
        }
    }
}
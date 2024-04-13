<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->library('bank');
        $this->load->model('sacco_accounts_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('sacco_branches/sacco_branches_m');
    }

    protected $validation_rules = array(
        array(
            'field' =>  'account_name',
            'label' =>  'Account Name',
            'rules' =>  'xss_clean|required|trim'
        ),array(
            'field' =>  'sacco_id',
            'label' =>  'Sacco Name',
            'rules' =>  'xss_clean|required|trim|numeric'
        ),array(
            'field' =>  'sacco_branch_id',
            'label' =>  'Sacco Branch Name',
            'rules' =>  'xss_clean|required|trim|numeric'
        ),array(
            'field' =>  'account_number',
            'label' =>  'Account Number',
            'rules' =>  'xss_clean|required|trim|numeric|callback__is_unique_account|min_length[5]|max_length[20]'
        ),array(
            'field' =>  'initial_balance',
            'label' =>  'Bank Branch Name',
            'rules' =>  'xss_clean|trim|currency'
        ),
    );

    function _is_unique_account(){
        $account_number = $this->input->post('account_number');
        $sacco_id = $this->input->post('sacco_id');
        $id = $this->input->post('id');
        $account_exists = $this->sacco_accounts_m->check_if_account_exists($id,$account_number,$sacco_id);
        if($account_exists){
            $this->form_validation->set_message('_is_unique_account','Sorry the account number '.'`'.$account_number.'`'.' is already registered and cannot allow duplicate');
            return FALSE;
        }else{
            return TRUE;
        }
    }


    function ajax_get_sacco_branches(){
        $sacco_id = $this->input->post('sacco_id');
        $post = $this->saccos_m->get($sacco_id);
        $branch_id = $this->input->post('branch_id');
        if($sacco_id){
            $branches = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id($sacco_id);
            echo form_dropdown('sacco_branch_id',array(''=>'--Select '.$post->name.' branch--')+$branches,$branch_id?:'','class="form-control select2" id="sacco_branch_id"');
        }
    }

    function create(){
        $data = array();
        $response = array();
        $post = new stdClass();
        $posts = $_POST;
        $message = '';
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $sacco_account = array(
                'group_id'          =>  $this->group->id,
                'account_number'    =>  $this->input->post('account_number'),
                'account_name'      =>  $this->input->post('account_name'),
                'initial_balance'   =>  $this->input->post('initial_balance'),
                'sacco_branch_id'    =>  $this->input->post('sacco_branch_id'),
                'sacco_id'           =>  $this->input->post('sacco_id'),
                'created_by'        =>  $this->user->id,
                'created_on'        =>  time(),
                'active'            =>  1,
            );
            $id = $this->sacco_accounts_m->insert($sacco_account);
            if($id){
                if($sacco_account = $this->sacco_accounts_m->get_group_sacco_account($id)){
                    $saccos = $this->saccos_m->get_group_sacco_options();
                    $sacco_branches = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id($sacco_account->sacco_id);
                    $sacco_account->sacco_details = $saccos[$sacco_account->sacco_id].' ('.$sacco_branches[$sacco_account->sacco_branch_id].')';
                    $sacco_account->sacco_name = $saccos[$sacco_account->sacco_id];
                    $sacco_account->sacco_branch = $sacco_branches[$sacco_account->sacco_branch_id];
                    $sacco_account->sacco_account_id = $sacco_account->id;
                    $response = array(
                        'status' => 1,
                        'sacco_account'=>$sacco_account,
                        'message' => 'Sacco Account created successfully',
                        'refer'=>site_url('group/sacco_accounts/listing')
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not add find any sacco account',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add sacco account',
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
        echo json_encode($response);
    }

    function delete(){
        $response = array();
        $id = abs((int) filter_var($this->input->post('id'), FILTER_SANITIZE_NUMBER_INT));
        if($id){
            $post = $this->sacco_accounts_m->get($id);
            if($post){
                if($this->user->id==$this->group->owner||$this->ion_auth->is_admin()){
                    $password = $this->input->post('password');
                    $identity = valid_phone($this->user->phone)?:$this->user->email;
                    if($this->ion_auth->login($identity,$password)){
                        if($this->transaction_statements_m->check_if_group_account_has_transactions('sacco-'.$post->id,$post->group_id)){
                            $response = array(
                                'status'=>0,
                                'message'=>'The sacco account has transactions associated to it, void all transactions associated to this account before deleting it'
                            );
                        }else{
                            if($this->sacco_accounts_m->delete($post->id,$post->group_id)){
                                $response = array(
                                    'status'=>1,
                                    'message'=>'Sacco account deleted successfully'
                                );
                            }else{
                                $response = array(
                                    'status'=>0,
                                    'message'=>'Sacco account could not be deleted'
                                );
                            }
                        }
                    }else{
                        $response = array(
                            'status'=>0,
                            'message'=>'You entered the wrong password'
                        );
                    }
                }else{
                    $response = array(
                        'status'=>0,
                        'message'=>'You do not have sufficient permissions to delete Sacco account'
                    );
                }
            }else{
                $response = array(
                    'status'=>0,
                    'message'=>'Could not find the selected Group account',
                );
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'Sacco Account id is required'
            );

        }
        echo json_encode($response);
    }

    function get(){
        $response = array();
        $id = abs((int) filter_var($this->input->post('id'), FILTER_SANITIZE_NUMBER_INT));
        if($id){
            $post = $this->sacco_accounts_m->get_group_sacco_account($id);
            if($post){
                echo json_encode($post);die;
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not find the requested sacco account',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not find the requested sacco account',
            );
        }
        echo json_encode($response);
    }

    public function edit(){
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->input->post('id');
            $result = $this->sacco_accounts_m->update($id,array(
                'group_id'          =>  $this->group->id,
                'account_number'    =>  $this->input->post('account_number'),
                'account_name'      =>  $this->input->post('account_name'),
                'sacco_branch_id'    =>  $this->input->post('sacco_branch_id'),
                'sacco_id'           =>  $this->input->post('sacco_id'),
                'initial_balance'    =>  currency($this->input->post('initial_balance')),
                'modified_by'        =>  $this->user->id,
                'modified_on'        =>  time()
            ));
            if($result){
                if($sacco_account = $this->sacco_accounts_m->get_group_sacco_account($id)){
                    $saccos = $this->saccos_m->get_group_sacco_options();
                    $sacco_branches = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id($sacco_account->sacco_id);
                    $sacco_account->sacco_details = $saccos[$sacco_account->sacco_id].' ('.$sacco_branches[$sacco_account->sacco_branch_id].')';
                    $sacco_account->sacco_name = $saccos[$sacco_account->sacco_id];
                    $sacco_account->sacco_branch = $sacco_branches[$sacco_account->sacco_branch_id];
                    $sacco_account->sacco_account_id = $sacco_account->id;
                    $response = array(
                        'status' => 1,
                        'sacco_account'=>$sacco_account,
                        'refer'=>site_url('group/sacco_accounts/listing'),
                        'message' => 'Edited successfully.',
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not add find any bank account.',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add bank account.',
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
        echo json_encode($response);
    }

    function get_sacco_accounts_listing(){
        $total_rows = $this->sacco_accounts_m->count_all();
        $pagination = create_pagination('group/sacco_accounts/listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->sacco_accounts_m->limit($pagination['limit'])->get_group_sacco_accounts();
        if(!empty($posts)){ 
            echo form_open('admin/saccos/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])): 
                echo '
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Sacco Accounts</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                    endif; 
                echo '
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th nowrap>
                                #
                            </th>
                            <th nowrap>
                                Account Name
                            </th>
                            <th nowrap>
                                Group
                            </th>
                            <th nowrap>
                                Branch
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
                            <th nowrap>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); foreach($posts as $post):
                        echo '
                            <tr class="'.$post->id.'_active_row">
                                <td>'.($i+1).'</td>
                                <td>'.$post->account_name.'</td>
                                <td>'.$post->sacco_name.'</td>
                                <td>'.$post->sacco_branch.'</td>
                                <td>'.$post->account_number.'</td>
                                <td class="text-right">
                                    '.number_to_currency((float)$post->initial_balance + (float)$post->current_balance).'
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
                                                echo "<span class='m-badge m-badge--default m-badge--wide'>Hidden</span>";
                                            }
                                        }
                                    echo '
                                </td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <a href="'.site_url('group/sacco_accounts/edit/'.$post->id).'" class="btn btn-sm btn-primary">
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
                                            <a href="'.site_url('group/sacco_accounts/reopen/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-play"></i>Reopen</a>
                                        ';
                                    }else{
                                        if($post->active){
                                            echo '
                                                <a href="'.site_url('group/sacco_accounts/hide/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-eye-slash"></i>Hide</a>
                                            ';
                                        }else{
                                            echo '
                                                <a data-original-title="Activate Sacco Account" href="'.site_url('group/sacco_accounts/activate/'.$post->id).'" class="dropdown-item confirmation_link"><i class="la la-check-square-o"></i>Unhide</a>
                                            ';
                                        }
                                        echo '
                                            <a data-original-title="Close Sacco Account" href="'.site_url('group/sacco_accounts/close/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-pause"></i>Close</a>
                                        ';
                                    }
                                    echo '
                                        <a data-original-title="Delete Sacco Account" href="'.site_url('group/sacco_accounts/delete/'.$post->id).'" class="dropdown-item prompt_confirmation_message_link" id="'.$post->id.'"><i class="fa fa-trash"></i>Delete</a>
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
            <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                <strong>'.translate('Sorry').'!</strong> '.translate('No Sacco accounts to display.').'.
            </div>';
        }    
    }

}
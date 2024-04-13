<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $validation_rules = array(
        array(
                'field' =>  'account_name',
                'label' =>  'Account Name',
                'rules' =>  'trim|required'
            ),
        array(
                'field' =>  'account_slug',
                'label' =>  'Account Name',
                'rules' =>  'required|trim|callback__is_unique_account_name'
            ),
        array(
                'field' =>  'initial_balance',
                'label' =>  'Initial Petty Cash Account balances',
                'rules' =>  'trim|currency'
            ),

        );

    protected $data = array();
	
	function __construct(){
        parent::__construct();
        $this->load->model('petty_cash_accounts_m');
        $this->load->model('transaction_statements/transaction_statements_m');
    }


    function index(){
        $this->template->title('Group Petty Cash Accounts List')->build('group/listing',$this->data);
    }

    function _is_unique_account_name()
    {
        $account_slug = $this->input->post('slug');
        $id = $this->input->post('id');
        if($this->petty_cash_accounts_m->check_if_account_exists($id,$account_slug))
        {
            $this->form_validation->set_message('_is_unique_account_name','Another Petty Cash Account by the name <strong>`'.$this->input->post('account_name').'`</strong> already exists');
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }


    public function create(){
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run())
        {
           $id = $this->petty_cash_accounts_m->insert(array(
                'account_name'      =>  $this->input->post('account_name'),
                'account_slug'      =>  $this->input->post('slug'),
                'initial_balance'   =>  currency($this->input->post('initial_balance')?:0),
                'created_by'        =>  $this->user->id,
                'created_on'        =>  time(),
                'group_id'          =>  $this->group->id,
                'active'            =>  1,
            ));
           if($id)
           {
                $this->session->set_flashdata('success','Petty cash account successfully added');
                if($this->input->post('new_item'))
                {
                    redirect('group/petty_cash_accounts/create','refresh');
                }else
                {
                    redirect('group/petty_cash_accounts/listing','refresh');
                }
           }
           else
           {
                $this->session->set_flashdata('error','Unable to add Petty cash account');
                redirect('group/petty_cash_accounts/listing','refresh');
           }
            
        }
        else
        {
            foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }

        $this->data['post'] = $post;
        $this->data['id'] = '';
        $this->template->title('Create Group Petty Cash Account')->build('group/form',$this->data);
    }

    public function ajax_create(){
        $response = array();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $petty_cash_account = array(
                'account_name'      =>  $this->input->post('account_name'),
                'account_slug'      =>  $this->input->post('slug'),
                'initial_balance'   =>  currency($this->input->post('initial_balance')?:0),
                'created_by'        =>  $this->user->id,
                'created_on'        =>  time(),
                'group_id'          =>  $this->group->id,
                'active'            =>  1,
            );
            $id = $this->petty_cash_accounts_m->insert($petty_cash_account);
            if($id){
                $petty_cash_account['id'] = $id;
                $response = array(
                    'petty_cash_account' => $petty_cash_account,
                    'status' => 1,
                    'refer' => site_url('group/petty_cash_accounts/listing'),
                    'message' => 'Petty cash account successfully added',
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add petty cash account',
                    'validation_errors' => '',
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

    public function ajax_edit(){
        $response = array();
        $id = $this->input->post('id');
        $post = $this->petty_cash_accounts_m->get($id);
        if($post){
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){                
                $update = $this->petty_cash_accounts_m->update($post->id,array(
                    'account_name'      =>  $this->input->post('account_name'),
                    'account_slug'      =>  $this->input->post('slug'),
                    'initial_balance'   =>  currency($this->input->post('initial_balance')?:0),
                    'modified_by'       =>  $this->user->id,
                    'modified_on'       =>  time(),
                ));
                if($update){
                    $response = array(
                        'status' => 1,
                        'message' => 'Success petty cash account edited',
                        'refer'=>site_url('group/petty_cash_accounts/listing'),
                        'validation_errors' => '',
                    );
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not update petty cash account',
                        'validation_errors' => '',
                    );
                }
            }else{
                $response = array(
                    'status' => 0,
                    'message' => '',
                    'validation_errors' => $this->form_validation->error_array(),
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Could not not find petty cash account Details',
            ); 
        }
        echo json_encode($response);
    }

   
    function edit($id=0)
    {
        $id OR redirect('group/petty_cash_accounts/listing');

        $post = new StdClass();

        $post = $this->petty_cash_accounts_m->get($id);
        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Petty Cash account does not exist');
            redirect('group/petty_cash_accounts/listing');
            return FALSE;
        }

        if($post->is_system){
            redirect("group/petty_cash_accounts/listing");
        }

        $this->form_validation->set_rules($this->validation_rules);

        if($this->form_validation->run())
        {
           $update = $this->petty_cash_accounts_m->update($post->id,array(
                'account_name'      =>  $this->input->post('account_name'),
                'account_slug'      =>  $this->input->post('slug'),
                'initial_balance'   =>  currency($this->input->post('initial_balance')?:0),
                'modified_by'       =>  $this->user->id,
                'modified_on'       =>  time(),
            ));
           if($update)
           {
                $this->session->set_flashdata('success','Petty cash account successfully added');
                if($this->input->post('new_item'))
                {
                    redirect('group/petty_cash_accounts/create','refresh');
                }else
                {
                    redirect('group/petty_cash_accounts/listing','refresh');
                }
           }
           else
           {
                $this->session->set_flashdata('error','Unable to add Petty cash account');
                redirect('group/petty_cash_accounts/listing','refresh');
           }
            
        }
        else
        {
            // Go through all the known fields and get the post values
            foreach (array_keys($this->validation_rules) as $field){
                 if (isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            }
        }
        $this->data['post'] = $post;
        $this->data['id'] = $id;

        $this->template->title('Edit Group Petty Cash Account')->build('group/form',$this->data);
    }


    function listing(){
        $this->template->title('Group Petty Cash Accounts List')->build('group/listing',$this->data);
    }

    function ajax_get_petty_cash_accounts_listing(){
        $total_rows = $this->petty_cash_accounts_m->count_all();
        $pagination = create_pagination('group/petty_cash_accounts/listing/pages', $total_rows,50,5,TRUE);
        $posts = $this->petty_cash_accounts_m->limit($pagination['limit'])->get_all();
        if(!empty($posts)){
            echo form_open('admin/saccos/action', ' id="form"  class="form-horizontal"'); 
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Petty cash accounts </p>';
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
                                <th>
                                   Petty Cash Account Name
                                </th>
                                <th class="text-right">
                                    Balances ('.($this->currency_code_options[$this->group->currency_id]?:$this->default_country->currency_code).')
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
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
                                    <td class="text-right">
                                        '.number_to_currency($post->initial_balance + $post->current_balance).'
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
                                    <td class="actions">';
                                        if($post->is_system){

                                        }else{
                                            echo '
                                                <div class="btn-group">
                                                    <a href="'.site_url('group/petty_cash_accounts/edit/'.$post->id).'" class="btn btn-sm btn-primary">
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
                                                        <a href="'.site_url('group/petty_cash_accounts/reopen/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-play"></i>Reopen</a>
                                                    ';
                                                }else{
                                                    if($post->active){
                                                        echo '
                                                            <a href="'.site_url('group/petty_cash_accounts/hide/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-eye-slash"></i>Hide</a>
                                                        ';
                                                    }else{
                                                        echo '
                                                            <a data-original-title="Activate Petty Cash Account" href="'.site_url('group/petty_cash_accounts/activate/'.$post->id).'" class="dropdown-item confirmation_link"><i class="la la-check-square-o"></i>Unhide</a>
                                                        ';
                                                    }
                                                    echo '
                                                        <a data-original-title="Close Petty Cash Account" href="'.site_url('group/petty_cash_accounts/close/'.$post->id).'" class="dropdown-item confirmation_link"><i class="fa fa-pause"></i>Close</a>
                                                    ';
                                                }
                                            echo '
                                                <a data-original-title="Delete Petty Cash Account" href="'.site_url('group/petty_cash_accounts/delete/'.$post->id).'" class="dropdown-item prompt_confirmation_message_link" id="'.$post->id.'"><i class="fa fa-trash"></i>Delete</a>

                                                </div>
                                            </div>
                                        ';
                                        }
                                        echo '
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
                    <strong>Ooops!</strong> Looks like you do not have any petty cash accounts configured.
                </div>
            ';
        }  
    }

    function hide($id=0,$redirect = TRUE)
    {
        $id OR redirect('group/petty_cash_accounts/listing');

        $post = $this->petty_cash_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Petty Cash account does not exist');
            redirect('group/petty_cash_accounts/listing');
            return FALSE; 
        }

        if($post->is_system){
            redirect("group/petty_cash_accounts/listing");
        }

        if(!$post->active)
        {
            $this->session->set_flashdata('Error','Sorry, the Petty Cash Account is already hidden');
            redirect('group/petty_cash_accounts/listing');
            return FALSE; 
        }
        if($post->is_system){
            $res = FALSE;
        }else{
            $res = $this->petty_cash_accounts_m->update($post->id,array('active'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));
        }
        if($res)
        {
            $this->session->set_flashdata('success','Petty Cash Account successfully hidden');
            if($redirect)
            {
                redirect('group/petty_cash_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide the Petty Cash account');
            if($redirect)
            {
                redirect('group/petty_cash_accounts/listing');
            }
        }
    }

    function activate($id=0,$redirect = TRUE)
    {
        $id OR redirect('group/petty_cash_accounts/listing');

        $post = $this->petty_cash_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Petty Cash account does not exist');
            redirect('group/petty_cash_accounts/listing');
            return FALSE; 
        }

        if($post->active)
        {
            $this->session->set_flashdata('Error','Sorry, the Petty Cash account is already active');
            redirect('group/petty_cash_accounts/listing');
            return FALSE; 
        }
        if($post->is_system){
            $res = FALSE;
        }else{
            $res = $this->petty_cash_accounts_m->update($post->id,array('active'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));
        }
        if($res)
        {
            $this->session->set_flashdata('success','Petty Cash Account successfully activated');
            if($redirect)
            {
                redirect('group/petty_cash_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to actvate the Petty Cash account');
            if($redirect)
            {
                redirect('group/petty_cash_accounts/listing');
            }
        }
    }


    function close($id=0 , $redirect = TRUE)
    {
        $id OR redirect('group/petty_cash_accounts/listing');

        $post = $this->petty_cash_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Petty Cash account does not exist');
            redirect('group/petty_cash_accounts/listing');
            return FALSE; 
        }

        if($post->is_closed)
        {
            $this->session->set_flashdata('Error','Sorry, the Petty Cash account is already closed');
            redirect('group/petty_cash_accounts/listing');
            return FALSE; 
        }
        if($post->is_system){
            $res = FALSE;
        }else{
            $res = $this->petty_cash_accounts_m->update($post->id,array('is_closed'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));
        }
        if($res)
        {
            $this->session->set_flashdata('success','Petty Cash Account successfully closed');
            if($redirect)
            {
                redirect('group/petty_cash_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to close the Petty Cash account');
            if($redirect)
            {
                redirect('group/petty_cash_accounts/listing');
            }
        }
    }

    function reopen($id=0 , $redirect = TRUE)
    {
        $id OR redirect('group/petty_cash_accounts/listing');

        $post = $this->petty_cash_accounts_m->get($id);

        if(empty($post))
        {
            $this->session->set_flashdata('Error','Sorry, the Petty Cash account does not exist');
            redirect('group/petty_cash_accounts/listing');
            return FALSE; 
        }

        if(!$post->is_closed)
        {
            $this->session->set_flashdata('Error','Sorry, the Petty Cash account is already open');
            redirect('group/petty_cash_accounts/listing');
            return FALSE; 
        }

        if($post->is_system){
            $res = FALSE;
        }else{
            $res = $this->petty_cash_accounts_m->update($post->id,array('is_closed'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));
        }
        if($res)
        {
            $this->session->set_flashdata('success','Petty Cash Account successfully re-opened');
            if($redirect)
            {
                redirect('group/petty_cash_accounts/listing');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Unable to re-open the Petty Cash account');
            if($redirect)
            {
                redirect('group/petty_cash_accounts/listing');
            }
        }
    }

    function delete(){
        $response = array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->petty_cash_accounts_m->get($id);
            if($post){
                if($this->user->id==$this->group->owner||$this->ion_auth->is_admin()){
                    $password = $this->input->post('password');
                    $identity = valid_phone($this->user->phone)?:$this->user->email;
                    if($this->ion_auth->login($identity,$password)){
                        if($this->transaction_statements_m->check_if_group_account_has_transactions('petty-'.$post->id,$post->group_id)){
                            $response = array(
                                'status'=>0,
                                'message'=>'The petty cash account has transactions associated to it, void all transactions associated to this account before deleting it'
                            );
                        }else{
                            if($this->petty_cash_accounts_m->delete($post->id,$post->group_id)){
                                $response = array(
                                    'status'=>1,
                                    'message'=>'Petty cash account deleted successfullyy'
                                );
                            }else{
                                $response = array(
                                    'status'=>0,
                                    'message'=>'Petty cash not deleted'
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
                        'message'=>'You do not have sufficient permissions to delete this mobile money account'
                    );
                }
            }else{
               $response = array(
                    'status'=>0,
                    'message'=>'Could not find petty cash  account '
                ); 
            }
        }else{
            $response = array(
                'status'=>0,
                'message'=>'petty cash account id is required'
            );

        }
        echo json_encode($response);
    }

    /*function delete($id = 0){
        $id OR redirect('group/petty_cash_accounts/listing');
        $post = new stdClass();
        $post = $this->petty_cash_accounts_m->get($id);
        if($post->is_system){
            redirect("group/petty_cash_accounts/listing");
        }
        if($this->user->id==$this->group->owner){
            $password = $this->input->get('confirmation_string');
            $identity = valid_phone($this->user->phone)?:$this->user->email;
            if($this->ion_auth->login($identity,$password)){
                if($this->transaction_statements_m->check_if_group_account_has_transactions('petty-'.$post->id,$post->group_id)){
                    $this->session->set_flashdata('warning','The petty cash account has transactions associated to it, void all transactions associated to this account before deleting it');
                }else{
                    if($this->petty_cash_accounts_m->delete($post->id,$post->group_id)){
                        $this->session->set_flashdata('success','Petty cash account deleted successfully');
                    }else{
                        $this->session->set_flashdata('error','Petty cash account could not be deleted');
                    }
                }
            }else{
                $this->session->set_flashdata('warning','You entered the wrong password.');
            }
        }else{
            $this->session->set_flashdata('warning','You do not have sufficient permissions to delete a petty cash account.');
        }
        redirect('group/petty_cash_accounts/listing');
    }*/

}
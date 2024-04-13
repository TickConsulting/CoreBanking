<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

	protected $validation_rules=array(
        array(
                'field' =>  'name',
                'label' =>  'Bank Name',
                'rules' =>  'trim|required',
            ),
        array(
                'field' =>  'slug',
                'label' =>  'Bank Slug',
                'rules' =>  'trim|required|callback_bank_is_unique',
            ),
        array(
                'field' =>  'partner',
                'label' =>  'Is a Chamasoft Partner',
                'rules' =>  'trim|numeric',
            ),
        array(
                'field' =>  'logo',
                'label' =>  'Bank Logo',
                'rules' =>  '',
            ),
        array(
                'field' =>  'primary_color',
                'label' =>  'Primary Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'secondary_color',
                'label' =>  'Secondary Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'tertiary_color',
                'label' =>  'Tertiary Color',
                'rules' =>  '',
            ),
        array(
                'field' =>  'text_color',
                'label' =>  'Text Color',
                'rules' =>  '',
            ),

        array(
                'field' =>  'create_otp_url',
                'label' =>  'Country Create One Time Password URL',
                'rules' =>  'trim',
            ),
        array(
                'field' =>  'verify_otp_url',
                'label' =>  'Country Verify One Time Password URL',
                'rules' =>  'trim',
            ),
        array(
                'field' =>  'is_a_wallet',
                'label' =>  'Bank is a wallet',
                'rules' =>  'trim',
            ),
        array(
                'field' =>  'country_id',
                'label' =>  'Country',
                'rules' =>  'trim|required|numeric',
            ),
        array(
                'field' =>  'wallet',
                'label' =>  'wallet',
                'rules' =>  'trim',
            ),
        

    );

	function __construct(){
        parent::__construct();
        $this->load->library('files_uploader');
        $this->load->model('banks_m');
        $this->load->model('bank_branches_m');
        $this->country_options = $this->countries_m->get_country_options();
    }

    public function create(){
    	$data = array();
        $post = new stdClass();      
        $this->form_validation->set_rules($this->validation_rules);
        if($this->input->post('partner')==1){
            $this->validation_rules[] = array(
                'field' =>  'create_otp_url',
                'label' =>  'Country Create One Time Password URL',
                'rules' =>  'trim|required',
            );
            $this->validation_rules[] = array(
                'field' =>  'verify_otp_url',
                'label' =>  'Country Verify One Time Password URL',
                'rules' =>  'trim|required',
            );
        }
        if($this->form_validation->run()){
            if(!empty($_FILES['logo']['name'])){
                if($upload_data = $this->files_uploader->upload('logo')){
                    $logo = $upload_data['file_name'];
                    $this->session->set_flashdata('info','Bank logo uploaded successfully');
                }else{
                    $logo = '';
                }
            }else{
                $logo = '';
            }
            $data = array(
                'name'=>$this->input->post('name'),
                'slug'=>$this->input->post('slug'),
                'partner'=>$this->input->post('partner')?1:0,
                'wallet'=>$this->input->post('wallet')?1:0,
                'primary_color'=>$this->input->post('primary_color'),
                'secondary_color'=>$this->input->post('secondary_color'),
                'tertiary_color'=>$this->input->post('tertiary_color'),
                'create_otp_url' => $this->input->post('create_otp_url'),
                'verify_otp_url' => $this->input->post('verify_otp_url'),
                'text_color'=>$this->input->post('text_color'),
                'active'=>1,
                'country_id'=>$this->input->post('country_id'),
                'logo'=>$logo,
                'created_on'=>time(),
                'created_by'=>$this->ion_auth->get_user()->id
            );
            $id = $this->banks_m->insert($data);
            if($id){
                $this->session->set_flashdata('success','Bank created successfully');
            }else{
                $this->session->set_flashdata('error','Bank could not be created');
            }
            if($this->input->post('new_item')){
                redirect('admin/banks/create','refresh');
            }else{
                redirect('admin/banks/listing');
            }
        }else{
        	foreach ($this->validation_rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $data['id'] = '';
        $data['post'] = $post;
        $data['country_options'] = $this->country_options;
        $this->template->title('Create Bank')->build('admin/form',$data);
    }

    public function listing(){
        $data = array();
        $total_rows = $this->banks_m->count_all();
        $pagination = create_pagination('admin/banks/listing/pages', $total_rows,50,5,TRUE);
        $data['posts'] = $this->banks_m->limit($pagination['limit'])->get_all();
        $data['pagination'] = $pagination;
        $data['country_options'] = $this->country_options;
        $this->template->title('List Banks')->build('admin/listing',$data);
    }

    public function edit($id = 0){
        $id OR redirect('admin/banks/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->banks_m->get($id);
        //print_r($post);die;
        if(!$post){
            $this->session->set_flashdata('error','Sorry the bank does not exist');
            redirect('admin/banks/listing');
        } 
        if($this->input->post('partner')==1){
            $this->validation_rules[] = array(
                'field' =>  'create_otp_url',
                'label' =>  'Country Create One Time Password URL',
                'rules' =>  'trim|required',
            );
            $this->validation_rules[] = array(
                'field' =>  'verify_otp_url',
                'label' =>  'Country Verify One Time Password URL',
                'rules' =>  'trim|required',
            );
        }
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            if(!empty($_FILES['logo']['name'])){
                if($upload_data = $this->files_uploader->upload('logo')){
                    $logo = $upload_data['file_name'];
                    //delete old logo
                    if(is_file(FCPATH.'uploads/files/'.$post->logo)){
                        if(unlink(FCPATH.'uploads/files/'.$post->logo)){
                            $this->session->set_flashdata('info','Bank logo successfully replaced');
                        }
                    }
                }else{
                    $logo = '';
                }
            }else{
                $logo = '';
            }
            $data = array(
                'name'=>$this->input->post('name'),
                'slug'=>$this->input->post('slug'),
                'partner'=>$this->input->post('partner')?1:0,
                'wallet'=>$this->input->post('wallet')?1:0,
                'primary_color'=>$this->input->post('primary_color'),
                'secondary_color'=>$this->input->post('secondary_color'),
                'tertiary_color'=>$this->input->post('tertiary_color'),
                'create_otp_url' => $this->input->post('create_otp_url'),
                'verify_otp_url' => $this->input->post('verify_otp_url'),
                'text_color'=>$this->input->post('text_color'),
                'country_id'=>$this->input->post('country_id'),
                'active'=>1,
                'logo'=>$logo,
                'modified_on'=>time(),
                'modified_by'=>$this->ion_auth->get_user()->id
            );
            $result = $this->banks_m->update($id,$data);
            if($result){
                $bank_branches = $this->bank_branches_m->get_bank_branch_options_by_bank_id($id);
                if($bank_branches){
                    $bank_branch_updated = 0;
                    $failed_to_update = 0;
                    foreach ($bank_branches as $key => $bank_branch):
                        $branch_input = array(
                            'country_id'=>$this->input->post('country_id'),
                            'modified_on'=>time(),
                            'modified_by'=>$this->ion_auth->get_user()->id
                        );
                        if($update = $this->bank_branches_m->update($key,$branch_input)){
                            $bank_branch_updated++;
                        }else{
                            $failed_to_update++;
                        }
                    endforeach;
                    if($bank_branch_updated){
                        $this->session->set_flashdata('success','Bank Changes Saved Successfully ,'.$bank_branch_updated .' Banck branches updated successfully');
                    }else{
                        $this->session->set_flashdata('success','Bank Changes Saved Successfully ,'.$failed_to_update .' Banck branches failed updated successfully');  
                    }
                }
            }else{
                $this->session->set_flashdata('error','Changes could not be saved');
            }
            if($this->input->post('new_item')){
                redirect('admin/banks/create','refresh');
            }else{
                redirect('admin/banks/edit/'.$id);
            }
        }else{

        }
        $data['id'] = $id;
        $data['post'] = $post;
        $data['country_options'] = $this->country_options;
        $this->template->title('Edit Bank')->build('admin/form',$data);
    }

     function change_bank_country_ids(){
        $bank_ids =  array(3,4,5,7,8,9,10,11,12,13,14,15,16,18,19,20,22,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,52,55,57,59,62,63);
        
        foreach ($bank_ids as $key => $bank_id):
            $update_bank = array(
                'country_id'=>1,
                'active'=>1,
                'modified_on'=>time(),
                'modified_by'=>$this->ion_auth->get_user()->id
            );
            $result = $this->banks_m->update($bank_id,$update_bank);
            if($result){
                $bank_branches = $this->bank_branches_m->get_bank_branch_options_by_bank_id($bank_id);
                if($bank_branches){
                    $bank_branch_updated = 0;
                    $failed_to_update = 0;
                    foreach ($bank_branches as $bank_branch_id => $bank_branch):
                        $branch_input = array(
                            'country_id'=>1,
                            'modified_on'=>time(),
                            'modified_by'=>$this->ion_auth->get_user()->id
                        );
                        if($update = $this->bank_branches_m->update($bank_branch_id,$branch_input)){
                            $bank_branch_updated++;
                        }else{
                            $failed_to_update++;
                        }
                    endforeach;
                    if($bank_branch_updated){
                        $this->session->set_flashdata('success','Bank Changes Saved Successfully ,'.$bank_branch_updated .' Banck branches updated successfully');
                    }else{
                        $this->session->set_flashdata('success','Bank Changes Saved Successfully ,'.$failed_to_update .' Banck branches failed updated successfully');  
                    }
                }
            }
        endforeach;
        print_r($bank_ids); die();
    }

    public function bank_is_unique(){
        if($bank = $this->banks_m->get_by_slug($this->input->post('slug'))){
            if($this->input->post('id')==$bank->id){
                return TRUE;
            }else{
                $this->form_validation->set_message('bank_is_unique', 'The bank already exists.');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_hide'){
            for($i=0;$i<count($action_to);$i++){
                $this->hide($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_activate'){
            for($i=0;$i<count($action_to);$i++){
                $this->activate($action_to[$i],FALSE);
            }
        }
        /*
        else if($action == 'bulk_delete'){
            for($i=0;$i<count($action_to);$i++){
                $this->delete($action_to[$i],FALSE);
            }
        }
        */
        redirect('admin/banks/listing');
    }

   

    function hide($id = 0,$redirect= TRUE){
        $id OR redirect('admin/banks/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Bank does not exist');
            redirect('admin/banks/listing');
        }
        $result = $this->banks_m->update($post->id,array('active'=>0,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Banks was successfully hidden');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Bank');
        }

        if($redirect){
            redirect('admin/banks/listing');
        }
        return TRUE;
    }

    function activate($id = 0,$redirect= TRUE){
        $id OR redirect('admin/banks/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Bank does not exist');
            redirect('admin/banks/listing');
        }

        $result = $this->banks_m->update($post->id,array('active'=>1,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Banks were successfully activated');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Bank');
        }

        if($redirect){
            redirect('admin/banks/listing');
        }
        return TRUE;
    }

    function set_as_default($id = 0){
        $id OR redirect('admin/banks/listing');
        $bank = $this->banks_m->get_default_bank();
        if($bank){
            $input = array(
                'default_bank' => 0,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            if($result = $this->banks_m->update($bank->id,$input)){
                $this->session->set_flashdata('info',$bank->name.' successfully removed as default bank.');
            }else{
                $this->session->set_flashdata('error',$bank->name.' could not be removed as default bank.');
            }
        }else{
            $this->session->set_flashdata('error','Could not find default bank.');
        }
        $input = array(
            'default_bank' => 1,
            'modified_by' => $this->user->id,
            'modified_on' => time(),
        );

        $bank = $this->banks_m->get($id);
        if($bank){
            if($result = $this->banks_m->update($bank->id,$input)){
                $this->session->set_flashdata('success',$bank->name.' successfully set as default bank.');
            }else{
                $this->session->set_flashdata('error',$bank->name.' could not be set as default bank.');
            }
        }else{
            $this->session->set_flashdata('error','Could not find bank to set as default.');
        }
        redirect('admin/banks/listing');
    }

    function remove_as_default($id = 0){
        $id OR redirect('admin/banks/listing');
        $bank = $this->banks_m->get_default_bank();
        if($bank->id == $id){
            $input = array(
                'default_bank' => 0,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );

            $bank = $this->banks_m->get($id);
            if($bank){
                if($result = $this->banks_m->update($bank->id,$input)){
                    $this->session->set_flashdata('success',$bank->name.' successfully set as default bank.');
                }else{
                    $this->session->set_flashdata('error',$bank->name.' could not be set as default bank.');
                }
            }else{
                $this->session->set_flashdata('error','Could not find bank to set as default.');
            }
        }else{
            $this->session->set_flashdata('error','Could not find default bank.');
        }
       
        redirect('admin/banks/listing');
    }

    function get_all_banks_to_json(){
        $posts = $this->banks_m->get_all();
        $data = array();
        foreach ($posts as $key => $post) {
            $data[] = array(
                'id'=>$post->id,
                'name'=>$post->name,
                'logo'=>$post->logo,
                'primary_color'=>$post->primary_color,
                'secondary_color'=>$post->secondary_color,
                'tertiary_color'=>$post->tertiary_color,
                'text_color'=>$post->text_color,
                'country_id'=>$post->country_id,
            );
            
        }

        file_put_contents("logs/banks.json",json_encode($data)."\n",FILE_APPEND);
    }

    /*
    function delete($id = 0,$redirect= TRUE){
        $id OR redirect('admin/banks/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Bank does not exist');
            redirect('admin/banks/listing');
        }
        $result = $this->banks_m->delete($post->id);
        if($result){
            if($this->bank_branches_m->delete_branch_branches_by_bank_id($post->id)){

            }else{
                $this->session->set_flashdata('error','Could not delete bank branches');
            }
            $this->session->set_flashdata('success','Banks was successfully deleted');
        }else{
            $this->session->set_flashdata('error','Unable to delete '.$post->name.' Bank');
        }

        if($redirect){
            redirect('admin/banks/listing');
        }
        return TRUE;
    }
    */



}
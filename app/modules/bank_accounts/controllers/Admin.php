<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

    protected $data = array();

	function __construct(){
        parent::__construct();
        $this->load->model('bank_accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('bank_branches/bank_branches_m');
    }

    function fix(){

            $bank_accounts=$this->bank_accounts_m->get_all();
            for($i=0;$i<count($bank_accounts);$i++){
                $input=array('verified_on' => $bank_accounts[$i]->modified_on);
                update($bank_accounts[$i]->id,$input,$SKIP_VALIDATION=FALSE);
            }
            print_r("Done");
        }

    function connected(){
        
        $total_rows = $this->bank_accounts_m->count_verified_partner_bank_accounts();
        $pagination = create_pagination('admin/bank_accounts/connected/pages', $total_rows,50,5,TRUE);
    	$this->data['bank_options'] = $this->banks_m->get_admin_bank_options();
    	$this->data['bank_branch_options'] = $this->bank_branches_m->get_bank_branch_options_by_bank_id();
        $group_options = $this->groups_m->get_options();
        $this->data['group_options'] = $group_options;
        $this->data['contact_person'] = $this->groups_m->get_group_contact_person_array($group_options);
        $this->data['pagination'] = $pagination;
        if($this->input->get('generate_excel') == 1){
            $this->data['posts'] = $this->bank_accounts_m->get_verified_partner_bank_accounts();
            $this->data['settings'] = $this->application_settings;
            $json_file = json_encode($this->data);
            //print_r($json_file);die;
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/bank_accounts/connected',$this->application_settings->application_name.' Connected Bank Accounts'));
            die;
        }
        $this->data['posts'] = $this->bank_accounts_m->limit($pagination['limit'])->get_verified_partner_bank_accounts();
        $this->template->title('Connected Bank Accounts List')->build('admin/connected',$this->data);
    }

    function verify($id = 0){
        $id OR redirect('admin/bank_accounts/listing');
        $post = $this->bank_accounts_m->get_bank_account($id);
        $post OR redirect('group/bank_accounts/listing');
        $input = array(
            'is_verified' => 1,
            'modified_on' => time(),
        );
        if($this->bank_accounts_m->update($id,$input)){
            $this->session->set_flashdata('success',"Bank account linked, the group will now receive transaction alerts");
        }else{
            $this->session->set_flashdata('warning',"Bank account could not be linked.");
        }
        redirect('admin/groups/search/'.$post->group_id);
    }

    function disconnect($id = 0){
        $id OR redirect('admin/bank_accounts/listing');
        $post = $this->bank_accounts_m->get_bank_account($id);
        $post OR redirect('admin/bank_accounts/listing');
         $input = array(
            'is_verified' => 0,
            'modified_on' => time(),
        );
        if($this->bank_accounts_m->update($id,$input)){
            $this->session->set_flashdata('success',"Bank account unlinked, the group will not receive transaction alerts");
        }else{
            $this->session->set_flashdata('warning',"Bank account could not be unlinked.");
        }
        redirect('admin/groups/search/'.$post->group_id);
    }

}
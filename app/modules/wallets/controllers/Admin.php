<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

    protected $data=array();
    protected $path = 'uploads/logos/wallet_logos';
    protected $validation_rules = array(
    	array(
    		'field' => 'name',
    		'label' => 'Channel Name',
    		'rules' => 'trim|required',
    	),
    	array(
    		'field' => 'channel',
    		'label' => 'Channel Number',
    		'rules' => 'trim|required|numeric|callback__is_unique_channel',
    	),
    	array(
    		'field' => 'logo',
    		'label' => 'Channel Logo',
    		'rules' => '',
    	),
    	array(
    		'field' => 'country_ids',
    		'label' => 'Operating Countries',
    		'rules' => '',
    	),
    );

	function __construct(){
        parent::__construct();
        $this->load->model('wallets_m');
        $this->load->library('files_uploader');
    }

    function index(){   
    }

    function _is_unique_channel(){
    	$id = $this->input->post('id');
    	$channel = $this->input->post('channel');
    	if($this->wallets_m->is_unique_channel($channel,$id)){
    		return TRUE;
    	}else{
    		$this->form_validation->set_message('_is_unique_channel','Another channel already exists');
    		return FALSE;
    	}
    }

    function create(){
    	$post = new StdClass();
    	$this->form_validation->set_rules($this->validation_rules);
    	if($this->form_validation->run()){
    		$logo_directory = './uploads/logos/wallet_logos';
           	if(!is_dir($logo_directory)){
                mkdir($logo_directory,0777,TRUE);
           	}
			$logo = $this->files_uploader->upload('logo',$this->path);
    		$input = array(
    			'name' =>strtoupper($this->input->post('name')),
    			'channel' => $this->input->post('channel'),
    			'logo' => $logo['file_name']?:'',
    			'created_by' => $this->input->post('created_by'),
    			'created_on' => $this->input->post('created_on'),
    			'active' => 1,
    		);
    		if($id = $this->wallets_m->insert($input)){
    			$country_lists = $this->input->post('country_ids');
    			$country_ids = array();
				$wallet_id= array();
				$created_on= array();
				$created_by= array();
				$active= array();
    			foreach ($country_lists as $key => $country_id) {
    				$country_ids[] = $country_id;
    				$wallet_id[] = $id;
    				$created_on[] = time();
    				$created_by[] = $this->user->id;
    				$active[] = 1;
    			}
    			$batch_input = array(
    				'wallet_id' => $wallet_id,
    				'country_id' => $country_ids,
    				'created_on' => $created_on,
    				'created_by' => $created_by,
    				'active' => $active,
    			);
    			$this->wallets_m->batch_insert_country_wallet_pairing($batch_input);
    			$this->session->set_flashdata('success','Wallet successfully created');
    		}else{
    			$this->session->set_flashdata('error','Error occured creating wallet channel. Try again later');
    		}
    		redirect('admin/wallets/listing');
    	}else{
    		foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
    	}
    	$this->data['post'] = $post;
    	$this->data['id'] = '';
    	$this->data['countries'] = $this->countries_m->get_country_options();
    	$this->template->title('Create Wallet Channels')->build('admin/form',$this->data);
    }

    function edit($id=0){
    	$id OR redirect('admin/wallets/listing');
    	$post = $this->wallets_m->get($id);
    	$this->form_validation->set_rules($this->validation_rules);
    	if($this->form_validation->run()){
    		$logo_directory = './uploads/logos/wallet_logos';
           	if(!is_dir($logo_directory)){
                mkdir($logo_directory,0777,TRUE);
           	}
           	if($_FILES['logo']['name']){
				$logo = $this->files_uploader->upload('logo',$this->path);
			}else{
				$logo['file_name'] = $post->logo;
			}
    		$update = array(
    			'name' =>strtoupper($this->input->post('name')),
    			'channel' => $this->input->post('channel'),
    			'logo' => $logo['file_name']?:'',
    			'created_by' => $this->input->post('created_by'),
    			'created_on' => $this->input->post('created_on'),
    			'active' => 1,
    		);
    		if($this->wallets_m->update($id,$update)){
    			$country_lists = $this->input->post('country_ids');
    			$country_ids = array();
				$wallet_id= array();
				$created_on= array();
				$created_by= array();
				$active= array();
    			foreach ($country_lists as $key => $country_id) {
    				$country_ids[] = $country_id;
    				$wallet_id[] = $id;
    				$created_on[] = time();
    				$created_by[] = $this->user->id;
    				$active[] = 1;
    			}
    			$batch_input = array(
    				'wallet_id' => $wallet_id,
    				'country_id' => $country_ids,
    				'created_on' => $created_on,
    				'created_by' => $created_by,
    				'active' => $active,
    			);
    			if($this->wallets_m->delete_wallet_country_pairings($id)){
    				$this->wallets_m->batch_insert_country_wallet_pairing($batch_input);
    			}
    			$this->session->set_flashdata('success','Wallet successfully updated');
    		}else{
    			$this->session->set_flashdata('error','Error occured updating wallet channel. Try again later');
    		}
    		redirect('admin/wallets/listing');
    	}else{
    		$post->country_ids = $this->wallets_m->get_wallet_countrys($id);
    		foreach (array_keys($this->validation_rules) as $field){
                 if (isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            }
    	}
    	$this->data['post'] = $post;
    	$this->data['id'] = $id;
    	$this->data['countries'] = $this->countries_m->get_country_options();
    	$this->data['path'] = $this->path;
    	$this->template->title('Create Wallet Channels')->build('admin/form',$this->data);
    }

    function listing(){
    	$posts = $this->wallets_m->get_all();
    	$this->data['posts'] = $posts;
    	$this->data['country_wallet_pairings'] = $this->wallets_m->get_wallet_country_pairings();
    	$this->data['countries'] = $this->countries_m->get_country_options();
    	$this->data['path'] = $this->path;
    	$this->template->title('Wallet Channels')->build('admin/listing',$this->data);
    }

    function delete($id=0){
    	$id OR redirect('admin/wallets/listing');
    	$post = $this->wallets_m->get($id);
    	if($post){
    		$this->wallets_m->delete($id);
    		$this->wallets_m->delete_wallet_country_pairings($id);
    		$this->session->set_flashdata('success','Wallet successfully removed');
    	}else{
    		$this->session->set_flashdata('error','Could not find wallet');
    	}
    	redirect('admin/wallets/listing');
    }

    function accounts(){
        $account = $this->bank_accounts_m->get_admin_wallet_account();
        if(empty($account)){
            $account = $this->_open_online_account();
        }
        $this->data['post'] = $account;
        $this->template->title('Admin Wallet Account')->build('admin/account',$this->data);
    }

    function _open_online_account(){
        $bank_branch = $this->bank_branches_m->get_online_banking_headoffice();
        if($bank_branch){
            $reference_number = time()+rand(100,2500);
            $post_data = json_encode(array(
                "request_id" => time(),
                "data" => array(
                    "account_name" => strtoupper($this->application_settings->application_name).' C.E.W',
                    'notification_url' => site_url('transaction_alerts/reconcile_direct_online_banking_payment'),
                    "reference_number" => $reference_number,
                    'member' => array(
                        "full_name" => $this->user->first_name.' '.$this->user->last_name,
                        "id_number" => rand(100,2500),
                        "phone_number" => $this->user->phone,
                    ),
                    "currency" => "KES",
                    "disable_charges" => 1,
                ),
            ));
            $url = "https://api.chamasoft.com:443/api/accounts/open_account";
            if($response = $this->curl->post_json_payment($post_data,$url)){
                if($res = json_decode($response)){
                    $code = $res->code;
                    $description = $res->description;
                    if($code == 200){
                        $data = $res->data;
                        $account = $data->account;
                        $account_name = $account->account_name;
                        $account_number = $account->account_number;
                        $security_pass = $account->security_pass;
                        $balance = $account->balance;
                        $currency = $account->currency;
                        $input = array(
                            'is_admin'          =>  1,
                            'account_number'    =>  $account_number,
                            'account_name'      =>  $account_name,
                            'initial_balance'   =>  currency($balance),
                            'bank_branch_id'    =>  $bank_branch->id,
                            'bank_id'           =>  $bank_branch->bank_id,
                            'created_by'        =>  $this->user->id,
                            'account_password'  =>  $security_pass,
                            'is_default'        =>  1,
                            'active'            =>  1,
                            'is_closed'         =>  0,
                            'created_on'        =>  time(),
                        );
                        $id = $this->bank_accounts_m->insert($input);
                        if($id){
                            $input['id'] = $id;
                            return $input;                                   
                        }else{
                            $this->session->set_flashdata('error','Could not complete group account setup');
                            return FALSE;
                        }
                    }else{
                        $this->session->set_flashdata('error',$description);
                        return FALSE;  
                    }
                }else{
                    $this->session->set_flashdata('error','Error occured while processing request. Try again later');
                    return FALSE;  
                }
            }else{
                $this->session->set_flashdata('error','Could not make payment. '.$this->session->flashdata('error'));
                return FALSE;
            }
        }else{
            $this->session->set_flashdata('error','Contact support to enable E-Wallet');
            return FALSE;
        }
    }
}
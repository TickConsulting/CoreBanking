<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class Equity_bank_m extends MY_Model{

	protected $_table = 'equity_bank_configurations';

	function __construct(){
		parent::__construct();
		$this->install();
	}

	function install(){
		$this->db->query("
		create table if not exists equity_bank_configurations(
			id int not null auto_increment primary key,
			`client_id` blob,
			`client_secret` blob,
			`grant_type` blob,
			`access_token` blob,
			`access_token_expires_at` blob,
			`access_token_type` blob,
			`is_default` blob,
			`active` blob,
			created_on blob,
			modified_on blob
		)");
	}
	function is_unique_username($username = '',$id = 0){
		if($id){
			$this->db->where('id !=',$id);
		}
		$this->db->where($this->dx('username').' = "'.$username.'"',NULL,FALSE);
		return $this->db->count_all_results('equity_bank_configurations')?0:1;
	}
	function insert_configuration($input = array(),$key=FALSE){
        return $this->insert_secure_data('equity_bank_configurations', $input);
	}


	function update_configuration($id,$input=array(),$SKIP_VALIDATION=FALSE){
		return $this->update_secure_data($id,'equity_bank_configurations',$input);
	}

	function get_default_configuration(){
		$this->select_all_secure('equity_bank_configurations');
		$this->db->where($this->dx('is_default').' = "1"',NULL,FALSE);
		return $this->db->get('equity_bank_configurations')->row();
	}

	function delete_configuration($id=0){
		$this->db->where('id',$id);
		return $this->db->delete('equity_bank_configurations');
	}


	function get_token(){
		$configuration = $this->get_default_configuration();
		if($configuration){
			if($configuration->access_token){
				if($configuration->access_token_expires_at > time()){					
					return $configuration->access_token_type.' '.$configuration->access_token;
				}else{					
					if($this->curl->equityBankRequests->client_account_token($configuration)){
						$token = $this->get_token();
						if($token){
							return $token;
						}else{
							$this->get_token();
						}
					}else{
						return FALSE;
						//die('1Failed to generate token with configuration access token');
					}
				}
			}else{
				if($this->curl->equityBankRequests->client_account_token($configuration)){
					$token = $this->get_token();
					if($token){
						return $token;
					}else{
						$this->get_token();
					}
				}else{
					return FALSE;
					//die('2failed to generate token with configuration empty access token two');
				}
			}
		}else{
			if($this->curl->equityBankRequests->client_account_token()){
				$token = $this->get_token();
				if($token){
					return $token;
				}else{
					$this->get_token();
				}
			}else{
				return FALSE;
				//die('3failed to generate token with configuration empty access token one');
			}
		}
	}




}?>
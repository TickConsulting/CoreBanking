<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class Ipn_m extends MY_Model{

	protected $_table = 'ipns';

	function __construct()
	{
		parent::__construct();
		$this->install();
	}
	/***
	status
	1. allocated to a chama
	'' Not tried to allocate
	2. Payment not allocated
	****/

	function install()
	{
		$this->db->query("
			create table if not exists ipns(
				id int not null auto_increment primary key,
				`ipn_depositor` blob,
				`transaction_id` blob,
				`reference_number` blob,
				`transaction_date` blob,
				`amount` blob,
				`active` blob,
				`currency` blob,
				`transaction_type` blob,
				`particulars` blob,
				`phone` blob,
				`account` blob,
				`customer_name` blob,
				`status` blob,
				`created_on` blob
			)"
		);

		$this->db->query("
			create table if not exists safaricom_stk_push_requests(
				`id` int not null auto_increment primary key, 
				`shortcode` blob, 
				`phone` blob, 
				`request_id` blob, 
				`group_id` blob, 
				`user_id` blob, 
				`amount` blob, 
				`request_callback_url` blob, 
				`response_code` blob, 
				`response_description` blob, 
				`checkout_request_id` blob, 
				`customer_message` blob, 
				`result_code` blob, 
				`result_description` blob,  
				`created_on` blob, 
				`modified_on` blob, 
				`modified_by` blob, 
				`transaction_id` blob, 
				`organization_balance` blob, 
				`transaction_date` blob, 
				`is_reversed` blob, 
				`reversed_on` blob, 
				`reference_number` blob, 
				`merchant_request_id` blob, 
				`request_reconcilled` blob, 
				`callback_sent` blob, 
				`reversal_transaction_id` blob, 
				`reversal_charge` blob
			)"
		);

		$this->db->query("
			create table if not exists safaricom_configurations(
				`id` int not null auto_increment primary key, 
				`username` blob,  
				`shortcode` blob,
				`password` blob, 
				`api_key` blob, 
				`access_token` blob, 
				`access_token_expires_at` blob, 
				`access_token_type` blob, 
				`is_default` blob, 
				`active` blob, 
				`created_by` blob, 
				`created_on` blob, 
				`modified_on` blob, 
				`modified_by` blob
			)"
		);
		
	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE)
	{
		return $this->insert_secure_data('ipns',$input);
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'ipns',$input);
	}

	function get($id=0){
		$this->select_all_secure('ipns');
		$this->db->where('id',$id);
		return $this->db->get('ipns')->row();
	}

	function is_transaction_dublicate($transaction_id=0,$ipn_depositor=0,$status_ignore=0){
		$this->select_all_secure('ipns');
		$this->db->where($this->dx('transaction_id').'="'.$transaction_id.'"',NULL,FALSE);
		$this->db->where($this->dx('ipn_depositor').'="'.$ipn_depositor.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		if($status_ignore){
			$this->db->where($this->dx('status').' IS NULL ',NULL,FALSE);
		}
		return $this->db->count_all_results('ipns')?:0;
	}

	function get_ipn_id($transaction_id=0,$ipn_depositor=0){
		$this->db->select('id');
		$this->db->where($this->dx('transaction_id').'="'.$transaction_id.'"',NULL,FALSE);
		$this->db->where($this->dx('ipn_depositor').'="'.$ipn_depositor.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->get('ipns')->row()->id;
	}

	function get_ipn_transaction($transaction_id=0,$ipn_depositor=0){
		$this->select_all_secure('ipns');
		$this->db->where($this->dx('transaction_id').'="'.$transaction_id.'"',NULL,FALSE);
		$this->db->where($this->dx('ipn_depositor').'="'.$ipn_depositor.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		return $this->db->get('ipns')->row();
	}

	function count_all(){
		return $this->db->count_all_results('ipns')?:0;
	}

	function get_all(){
		$this->select_all_secure('ipns');
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		return $this->db->get('ipns')->result();
	}

	function get_unallocated_notications(){
		$this->select_all_secure('ipns');
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$this->db->where('('.$this->dx('status').'="" OR '.$this->dx('status').' =" " OR '.$this->dx('status').' is NULL OR '.$this->dx('status').' ="0" )',NULL,FALSE);
		return $this->db->get('ipns')->result();
	}

	function delete_ipn_entry($id='')
	{
		$this->db->where('id',$id);
		return $this->db->delete('ipns');
	}

	function delete_transaction($transaction_id=0,$ipn_depositor=0){
		$this->db->select('id');
		$this->db->where($this->dx('transaction_id').'="'.$transaction_id.'"',NULL,FALSE);
		$this->db->where($this->dx('ipn_depositor').'="'.$ipn_depositor.'"',NULL,FALSE);
		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
		$result =  $this->db->get('ipns')->row();
		if($result){
			$this->db->where('id',$result->id);
			return $this->db->delete('ipns');
		}
	}

	function get_all_unforwarded_ips($limit=0){
		$this->select_all_secure('ipns');
		$this->db->where($this->dx('is_forwarded').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('status').' > "1"',NULL,FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		return $this->db->get('ipns')->result();
	}
	

	function reset_forwarding(){
       return $this -> db -> query("update ipns set 
                is_forwarded=".$this->exa('NULL'));  
    }

    function count_forwading(){
    	$this->db->where($this->dx('is_forwarded').' IS NULL',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('status').' > "1"',NULL,FALSE);
		return $this->db->count_all_results('ipns')?:0;
    }



    /*******customer stk push payments*****/

    function generate_stkpush_request_id(){
		$this->db->select(array(
            $this->dx('request_id').' as request_id',
        ));
        $this->db->order_by($this->dx('created_on'),'DESC',FALSE);
        $this->db->limit(1);
        $res = $this->db->get('safaricom_stk_push_requests')->row();
        if($res){
            return substr(chunk_split((str_replace('-','',$res->request_id)+1), 5, '-'), 0, -1);
        }else{
            return $this->starting_response_request_id;
        }
	}

	function insert_stk_push_request($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('safaricom_stk_push_requests',$input);
	}

	function get_stk_request_by_request_id($request_id = 0,$get_result_code = TRUE){
		$this->select_all_secure('safaricom_stk_push_requests');
		$this->db->where($this->dx('request_id').' = "'.$request_id.'"',NULL,FALSE);
		if($get_result_code){
			$this->db->where($this->dx('result_code').' iS NULL',NULL,FALSE);	
		}
		return $this->db->get('safaricom_stk_push_requests')->row();
	}

	function get_stk_request_by_merchant_request_id_and_checkout_request_id($checkout_request_id=0,$merchant_request_id=0){
		$this->select_all_secure('safaricom_stk_push_requests');
		$this->db->where($this->dx('checkout_request_id').' = "'.$checkout_request_id.'"',NULL,FALSE);
		$this->db->where($this->dx('merchant_request_id').' = "'.$merchant_request_id.'"',NULL,FALSE);
		$this->db->where($this->dx('request_reconcilled').' IS NULL ',NULL,FALSE);
		return $this->db->get('safaricom_stk_push_requests')->row();
	}

	function get_stk_request_by_checkout_request_id($checkout_request_id=0){
		$this->select_all_secure('safaricom_stk_push_requests');
		$this->db->where($this->dx('checkout_request_id').' = "'.$checkout_request_id.'"',NULL,FALSE);
		return $this->db->get('safaricom_stk_push_requests')->row();
	}

	function get_stk_request($id = 0){
		$this->select_all_secure('safaricom_stk_push_requests');
		$this->db->where('id',$id);
		return $this->db->get('safaricom_stk_push_requests')->row();
	}

	function update_stkpushrequest($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'safaricom_stk_push_requests',$input);
    }

    function count_all_stk_push_requests(){
    	return $this->db->count_all_results('safaricom_stk_push_requests')?:0;
    }

    function get_all_stk_push_requests(){
    	$this->select_all_secure('safaricom_stk_push_requests');
    	$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    	return $this->db->get('safaricom_stk_push_requests')->result();
    }

    function get_request_where_reference_number($reference_number=0){
    	$this->select_all_secure('safaricom_stk_push_requests');
		$this->db->where($this->dx('reference_number').' = "'.$reference_number.'"',NULL,FALSE);
		return $this->db->get('safaricom_stk_push_requests')->row();
    }

    function get_stk_payment_by_transaaction_id($transaction_id=0){
    	$this->select_all_secure('safaricom_stk_push_requests');
		$this->db->where($this->dx('transaction_id').' = "'.$transaction_id.'"',NULL,FALSE);
		return $this->db->get('safaricom_stk_push_requests')->row();
    }


    /****************Configurations*************************/
     function is_unique_username($username = '',$id = 0,$shortcode = 0){
		if($id){
			$this->db->where('id !=',$id);
		}
		$this->db->where($this->dx('username').' = "'.$username.'"',NULL,FALSE);
		$this->db->where($this->dx('shortcode').' = "'.$shortcode.'"',NULL,FALSE);
		return $this->db->count_all_results('safaricom_configurations')?0:1;
	}

	function insert_configuration($input = array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('safaricom_configurations',$input);
	}

	function update_configuration($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'safaricom_configurations',$input);
    }

	function get_configurations(){
		$this->select_all_secure('safaricom_configurations');
		$this->db->order_by($this->dx('created_on'),FALSE);
		return $this->db->get('safaricom_configurations')->result();
	}

	function get_configuation($id=0){
		$this->select_all_secure('safaricom_configurations');
		$this->db->where('id',$id);
		return $this->db->get('safaricom_configurations')->row();
	}

	function delete_configuration($id=0){
		$this->db->where('id',$id);
		return $this->db->delete('safaricom_configurations');
	}

	function get_default_configuration(){
		$this->select_all_secure('safaricom_configurations');
		$this->db->where($this->dx('is_default').' = "1"',NULL,FALSE);
		return $this->db->get('safaricom_configurations')->row();
	}


	function get_paybill_configuration($shortcode = 0){
		$this->select_all_secure('safaricom_configurations');
		$this->db->where($this->dx('shortcode').' = "'.$shortcode.'"',NULL,FALSE);
		return $this->db->get('safaricom_configurations')->row();
	}

	function get_token($shortcode = 0){
		$configuration = $this->get_paybill_configuration($shortcode);
		if($configuration){
			if($configuration->access_token){
				if($configuration->access_token_expires_at > time()){
					return $configuration->access_token_type.' '.$configuration->access_token;
				}else{
					if($this->curl->darajaRequests->generate_token($configuration)){
						$token = $this->get_token($shortcode);
						if($token){
							return $token;
						}else{
							$this->get_token($shortcode);
						}
					}else{
						die('Failed to generate token with configuration access token');
					}
				}
			}else{
				if($this->curl->darajaRequests->generate_token($configuration)){
					$token = $this->get_token($shortcode);
					if($token){
						return $token;
					}else{
						$this->get_token($shortcode);
					}
				}else{
					die('failed to generate token with configuration empty access token');
				}
			}
		}else{
			die('No default configuration');
		}
	}
}
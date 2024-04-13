<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
Class Groups_m extends MY_Model{

	protected $_table = 'investment_groups';

	protected $group_status = array(
			''	=>	'Group On Trial',
			1	=>	'Group Subscribed',
			2	=>	'Group Suspended'
		);

	function __construct()
	{
		parent::__construct();
		$this->load->model('bank_branches/bank_branches_m');
		// $this->load->model('billing/billing_m');
		//$this->install();
	}

	function install()
	{
		$this->db->query("
			create table if not exists investment_groups(
				id int not null auto_increment primary key,
				`name` blob,
				`slug` blob,
				`size` blob,
				`phone` blob,
				`email` blob,
				`address` blob,
				`sms_balance` blob,
				`account_number` blob,
				`country_id` blob,
				`currency_id` blob,
				`referrer_id` blob,
				`activation_code` blob,
				`lock_access` blob,
				`owner` blob,
				`billing_package_id` blob,
				`logo` blob,
				`status` blob,
				`active` blob,
				`created_by` blob,
				`created_on` blob,
				`modified_by` blob,
				`modified_on` blob,
			)"
		);

		$this->db->query("
			create table if not exists group_deletions(
				id int not null auto_increment primary key,
				`group_name` blob,
				`group_id` blob,
				`deleted_by` blob,
				`modified_by` blob,
				`modified_on` blob,
				`deleted_on` blob,
				`backup_file` blob,
				`restore_status` blob,
				`group_size` blob,
				`group_phone` blob,
				`group_email` blob,
				`group_created_by` blob,
				`group_created_on` blob
			)"
		);

	}

	function insert($input=array(),$SKIP_VALIDATION = FALSE){
		return $this->insert_secure_data('investment_groups',$input);
	}

	function insert_group_deletion_data($input=array(),$SKIP_VALIDATION = FALSE)
	{
		return $this->insert_secure_data('group_deletions',$input);
	}

	function get_all_group_deletions($name=''){
		$this->select_all_secure('group_deletions');
		if($name){
			$this->db->where($this->dx('group_name').' LIKE "%'.$name.'%"');
		}
		$this->db->order_by($this->dx('deleted_on'),'DESC',FALSE);
		return $this->db->get('group_deletions')->result();
	}

	function count_all_group_deletions($name=''){
		if($name){
			// $this->db->where($this->dx('group_name'),$name);
			$this->db->where($this->dx('group_name').' LIKE "%'.$name.'%"');
		}
		$this->db->order_by($this->dx('deleted_on'),'DESC',FALSE);
		return $this->db->count_all_results('group_deletions');
	}

	function get_group_deletion($id = 0){
		$this->select_all_secure('group_deletions');
		$this->db->where('id',$id);
		return $this->db->get('group_deletions')->row();
	}

	function update($id,$input=array(),$SKIP_VALIDATION=FALSE)
	{
		return $this->update_secure_data($id,'investment_groups',$input);
	}

	function update_group_sms($group_id=0,$sms_purchased=0){
		return $this->db->query('update investment_groups set 
				sms_balance='.$this->exa($this->dx("sms_balance").'+'.$sms_purchased).' 
				where id ="'.$group_id.'"');    
	}

	function get($id=0)
	{
		$this->select_all_secure('investment_groups');
		// $this->db->select(
		// 	array(
		// 		$this->dx('users.first_name')." as owner_first_name ",
		// 		$this->dx('users.last_name')." as owner_last_name ",
		// 		$this->dx('users.phone')." as owner_phone ",
		// 		$this->dx('users.email')." as owner_email ",
		// 	)
		// );
		$this->db->where('investment_groups.id',$id);
		// $this->db->join('users',$this->dx('owner').' = users.id ');
		return $this->db->get('investment_groups')->row();
	}

	function get_group_by_old_id($id=0){
		$this->select_all_secure('investment_groups');
		$this->db->where($this->dx('old_id').' = "'.$id.'"',NULL,FALSE);
		return $this->db->get('investment_groups')->row();
	}

	function get_groups_with_monthly_email_statements_due_today($date = ''){
		if($date){
			//nothing
		}else{
			$date = time();
		}
		$this->select_all_secure('investment_groups');
		$this->db->where($this->dx('enable_send_monthly_email_statements').' = 1');
		if(date('d',$date) == 28){
			$this->db->where($this->dx('next_monthly_contribution_statement_send_date').'IS NULL', NULL, FALSE);
		}
		$this->db->where('(DATE_FORMAT(FROM_UNIXTIME('.$this->dx('next_monthly_contribution_statement_send_date')."),'%m%d%Y') = '".date('mdY', $date)."')", NULL, FALSE);
		$this->db->where($this->dx('active').' =1');
		return $this->db->get('investment_groups')->result();
	}

	function get_all($name='',$owner='',$phone='',$email='',$status = '',$group_ids = array(),$query=''){
		$this->select_all_secure('investment_groups');
		// $this->db->select(
		// 	array(
		// 		$this->dx('users.first_name')." as owner_first_name ",
		// 		$this->dx('users.last_name')." as owner_last_name ",
		// 		$this->dx('users.phone')." as owner_phone ",
		// 		$this->dx('users.email')." as owner_email ",
		// 	)
		// );

		
		if($owner||$phone||$email)
		{
			$this->db->join('users',$this->dx('investment_groups.owner').'=users.id','INNER');
		}
		if($name)
		{
			$name = trim($this->db->escape_like_str($name));
            $this->db->where(' CONVERT(' . $this->dx('investment_groups.name') . " USING 'latin1')  like '%" . $name . "%'", NULL, FALSE);
		}
		if($owner)
		{
			$owner = trim($this->db->escape_like_str($owner));

			$this->db->where('CONVERT(CONCAT('.$this->dx('users.first_name').', " ", '.$this->dx('users.last_name').')'.' USING "latin1") LIKE "%'.$owner.'%" OR '.'CONVERT(CONCAT('.$this->dx('users.last_name').', " ", '.$this->dx('users.first_name').')'.' USING "latin1") LIKE "%'.$owner.'%"' ,NULL,FALSE);
		}
		if($phone)
		{
			//echo $phone;die;
			$phone = valid_phone($this->db->escape_like_str($phone));
			$this->db->like('('.$this->dx('users.phone').'="'.$phone.'" OR'.$this->dx('investment_groups.phone').'="'.$phone.'")',NULL,FALSE);
		}
		if($email)
		{
			$email = strtolower($this->db->escape_like_str($email));
			$this->db->where("( CONVERT(".$this->dx('users.email')." USING 'latin1') LIKE '%".$email."%' OR CONVERT(".$this->dx('investment_groups.email')." USING 'latin1') LIKE '%".$email."%')",NULL,FALSE);
		}
		if($status==1){
			$this->db->where($this->dx('trial_days').' > 0',NULL,FALSE);
   			$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);	
		}else if($status==2){
			$this->db->where($this->dx('status').' = 1 ',NULL,FALSE);
		}else if($status==3){
			$this->db->where($this->dx('trial_days').' <= 0',NULL,FALSE);
   			$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL OR '.$this->dx('status').' = "2" )',NULL,FALSE);	
		}

		// if($from && $to){
		// 	$this->db->where($this->dx('investment_groups.created_on').' >= "'.$from.'"',NULL,FALSE);
		// 	$this->db->where($this->dx('investment_groups.created_on').' <= "'.$to.'"',NULL,FALSE);
		// }

		if($query){
			$this->db->where("( 
				CONVERT(" . $this->dx('investment_groups.name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR 
				CONVERT(" . $this->dx('investment_groups.account_number') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR
				CONVERT(" . $this->dx('users.first_name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR
				CONVERT(" . $this->dx('users.last_name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%'
			)", NULL, FALSE);
		}

		if($group_ids){
				$this->db->where_in('id',$group_ids);
		}
		//$this->db->join('users',$this->dx('owner').' = users.id ','INNER');
		$this->db->order_by($this->dx('investment_groups.created_on'),'DESC',FALSE);
		return $this->db->get('investment_groups')->result();
	}

	function get_group_ids_by_bank_ids($bank_ids=array()){

		$this->select_secure('group_id');

		if(empty($bank_ids)){

		}else{

			$this->db->where_in($this->dx('bank_id'),$bank_ids);
			return $this->db->get('bank_accounts')->result_array();
		}

	}

	function count_all($name='',$owner_id='',$phone='',$email='',$status = '',$group_ids = array(),$query='')
	{	
		
		if($name){
			$this->db->where($this->dx('name')." LIKE '%".$this->db->escape_like_str($name)."%'",NULL,FALSE);
		}
		if($owner_id){
			$this->db->where($this->dx('owner')." LIKE '%".$this->db->escape_like_str($owner_id)."%'",NULL,FALSE);
		}
		if($phone){
			$this->db->where($this->dx('phone')." LIKE '%".$this->db->escape_like_str($phone)."%'",NULL,FALSE);
		}
		if($email){
			$this->db->where($this->dx('email')." LIKE '%".$this->db->escape_like_str($email)."%'",NULL,FALSE);
		}
		if($status==1){
			$this->db->where($this->dx('trial_days').' > 0',NULL,FALSE);
   			$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);	
		}else if($status==2){
			$this->db->where($this->dx('status').' = 1 ',NULL,FALSE);
		}else if($status==3){
			$this->db->where($this->dx('trial_days').' <= 0',NULL,FALSE);
   			$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL OR '.$this->dx('status').' = "2" )',NULL,FALSE);	
		}
		// if($from){
		// 	$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		// }
		// if($to){
		// 	$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		// }

		if($query){
			$this->db->where(" ( 
				CONVERT(" . $this->dx('investment_groups.name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR 
				CONVERT(" . $this->dx('investment_groups.account_number') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR
				CONVERT(" . $this->dx('users.first_name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR
				CONVERT(" . $this->dx('users.last_name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%'
			)", NULL, FALSE);
		}
		//$this->db->join('users',$this->dx('owner').' = users.id ');
		if($group_ids){
			
			$this->db->where_in('id',$group_ids);
		}
		return $this->db->count_all_results('investment_groups');
	}

	


	function get_groups_on_trial(){
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name ',
				$this->dx('owner').' as owner',
				$this->dx('status').' as status ',
				$this->dx('slug').' as slug ',
				$this->dx('created_on').' as created_on ',
				$this->dx('trial_days_end_date').' as trial_days_end_date ',
				$this->dx('trial_days').' as trial_days ',
			)
		);
		$this->db->where($this->dx('trial_days').' > 0',NULL,FALSE);
		$this->db->where($this->dx('lock_access').' = 0 ',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
        $this->db->order_by('created_on','DESC', FALSE);
		return $this->db->get('investment_groups')->result();
	}

	function get_group_ids_on_trial(){
		$this->db->select('id');
		$this->db->where($this->dx('trial_days').' > 0',NULL,FALSE);
		$this->db->where($this->dx('lock_access').' = 0 ',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
        $this->db->order_by('created_on','DESC', FALSE);
		$result =  $this->db->get('investment_groups')->result();
		$arr = array();
		if($result){
			foreach ($result as $key => $value) {
				$arr[] = $value->id;
			}
		}
		return $arr;
	}

	function get_groups_with_expired_trial(){
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name ',
				$this->dx('owner').' as owner',
				$this->dx('status').' as status ',
			)
		);
		$this->db->where($this->dx('trial_days').' < 1',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);	
   		return $this->db->get('investment_groups')->result();
	}

	function get_group_ids_with_expired_trial(){
		$this->db->select('id');
		$this->db->where($this->dx('trial_days').' <= 0',NULL,FALSE);
		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL OR '.$this->dx('status').' = "2" )',NULL,FALSE);
		$result =  $this->db->get('investment_groups')->result();
		$arr = array();
		if($result){
			foreach ($result as $key => $value) {
				$arr[] = $value->id;
			}
		}
		return $arr;
   	}

	function count_groups_with_expired_trial(){
		$this->db->where($this->dx('trial_days').' < 1',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);	
   		return $this->db->count_all_results('investment_groups');
	}


	function get_groups_with_connected_accounts(){
		$this->db->select(
			array(
				'investment_groups.id',
				$this->dx('name').' as name ',
				$this->dx('owner').' as owner',
				$this->dx('status').' as status ',
			)
		);
		$this->db->where($this->dx('is_verified').' = "1" ',NULL,FALSE);
		$this->db->join('bank_accounts',"investment_groups.id = ".$this->dx('bank_accounts.group_id'));
		$this->db->order_by('name','ASC', FALSE);
		return $this->db->get('investment_groups')->result();
	}

	function groups_registered_after_certain_days($date = 0){
		$arr =  array();
		$this->select_all_secure('investment_groups');
		if($date){

		}else{
			$date = date("d-m-Y",strtotime("-3 days"));
		}
		$this->db->order_by('name','ASC', FALSE);
		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F',strtotime($date)) . "'", NULL, FALSE);
		$results = $this->db->get('investment_groups')->result();
		foreach ($results as $key => $result):
			$arr[$result->owner] = $result;
		endforeach;
		return $arr;
	}

	function get_groups_with_accounts(){
		$this->db->select(
			array(
				'investment_groups.id',
				$this->dx('name').' as name ',
				$this->dx('owner').' as owner',
				$this->dx('status').' as status ',
			)
		);
		//$this->db->where($this->dx('is_verified').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('investment_groups.created_on')." > '".strtotime("24th November 2016")."'",NULL,FALSE);
		$this->db->join('bank_accounts',"investment_groups.id = ".$this->dx('bank_accounts.group_id'));
		$this->db->order_by('name','ASC', FALSE);
		return $this->db->get('investment_groups')->result();
	}

	function count_groups_on_trial(){
		$this->db->where($this->dx('trial_days').' > 0',NULL,FALSE);
		$this->db->where($this->dx('lock_access').' = 0 ',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		return $this->db->count_all_results('investment_groups');
	}

	function get_groups_trial_expired(){
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name ',
				$this->dx('owner').' as owner',
				$this->dx('status').' as status ',
				$this->dx('trial_days').' as trial_days ',
				$this->dx('trial_days_end_date').' as trial_days_end_date ',
				$this->dx('created_on').' as created_on ',
				$this->dx('slug').' as slug ',
			)
		);
		$this->db->where($this->dx('trial_days').' < 1',NULL,FALSE);
		$this->db->where($this->dx('lock_access').' = 0 ',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		$this->db->order_by('created_on','DESC', FALSE);
		return $this->db->get('investment_groups')->result();
	}

	function count_locked_groups(){
		$this->db->where($this->dx('lock_access').' = 1 ',NULL,FALSE);
   		return $this->db->count_all_results('investment_groups');
	}

	function get_locked_groups(){
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name ',
				$this->dx('owner').' as owner',
				$this->dx('status').' as status ',
				$this->dx('trial_days').' as trial_days ',
				$this->dx('trial_days_end_date').' as trial_days_end_date ',
				$this->dx('created_on').' as created_on ',
				$this->dx('slug').' as slug ',
			)
		);
		$this->db->where($this->dx('lock_access').' = 1 ',NULL,FALSE);
   		$this->db->order_by('created_on','DESC', FALSE);
		return $this->db->get('investment_groups')->result();
	}

	function get_locked_group_ids(){
		$this->db->select(
			array(
				'id',
			)
		);
		$this->db->where($this->dx('lock_access').' = 1 ',NULL,FALSE);
   		$this->db->order_by('created_on','DESC', FALSE);
		$result =  $this->db->get('investment_groups')->result();
		$arr = array();
		if($result){
			foreach ($result as $key => $value) {
				$arr[] = $value->id;
			}
		}
		return $arr;
	}

	function get_groups_in_package($billing_package_id=0){
		$this->db->select(
				'id'
		);
		$this->db->where($this->dx('billing_package_id').' IN( '.$billing_package_id.') ',NULL,FALSE);
   		$this->db->order_by('created_on','DESC', FALSE);
		$result = $this->db->get('investment_groups')->result();

		$arr = array();
		if($result){
			foreach ($result as $value) {
				$arr[] = $value->id;
			}
		}
		return $arr;
	}

	function count_groups_less_than_one_year($age=0,$ids=array()){
		$id_list ='';
		if(is_array($ids)){
			foreach ($ids as $key => $value) {
				if($id_list){
					$id_list.=','.$value;
				}else{
					$id_list = $value;
				}
			}
		}
		if($id_list){
			$this->db->where('id'.' NOT IN('.$id_list.')',NULL,FALSE);
		}
		if($age){
			$this->db->where($this->dx('created_on').' >= "'.$age.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('lock_access').' = 0 ',NULL,FALSE);
		$this->db->where($this->dx('active_size').' >= 5 ',NULL,FALSE);
		return $this->db->count_all_results('investment_groups')?:0;
	}

	function count_groups_more_than_one_year($age=0,$ids=array()){
		$id_list ='';
		if(is_array($ids)){
			foreach ($ids as $key => $value) {
				if($id_list){
					$id_list.=','.$value;
				}else{
					$id_list = $value;
				}
			}
		}
		if($id_list){
			$this->db->where('id'.' NOT IN('.$id_list.')',NULL,FALSE);
		}
		if($age){
			$this->db->where($this->dx('created_on').' < "'.$age.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('lock_access').' = 0 ',NULL,FALSE);
		$this->db->where($this->dx('active_size').' >= 5 ',NULL,FALSE);
		return $this->db->count_all_results('investment_groups')?:0;
	}

	function count_groups_trial_expired(){
		$this->db->where($this->dx('trial_days').' < 1',NULL,FALSE);
		$this->db->where($this->dx('lock_access').' = 0 ',NULL,FALSE);
   		$this->db->where("(".$this->dx('status').' = "" OR '.$this->dx('status').' = "0" OR '.$this->dx('status').' IS NULL )',NULL,FALSE);
		return $this->db->count_all_results('investment_groups');
	}

	function get_subscribed_groups(){
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name',
				$this->dx('owner').' as owner',
				$this->dx('status').' as status',
			)
		);
		$this->db->where($this->dx('status').' = 1 ',NULL,FALSE);
        $this->db->order_by('name','ASC', FALSE);
   		return $this->db->get('investment_groups')->result();
	}

	function count_subscribed_groups($from=0,$to=0){
		$this->db->where($this->dx('status').' = 1 ',NULL,FALSE);
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
   		return $this->db->count_all_results('investment_groups');
	}

	function get_paying_groups($group_ids = array()){
		$this->db->select(
			array(
				'investment_groups.id as id',
				$this->dx('investment_groups.name').' as name',
				$this->dx('investment_groups.owner').' as owner',
				$this->dx('investment_groups.status').' as status',
				$this->dx('investment_groups.created_on').' as created_on',
				$this->dx('investment_groups.billing_cycle').' as billing_cycle',
				$this->dx('investment_groups.slug').' as slug',
				$this->dx('investment_groups.referrer_id').' as referrer_id',
				$this->dx('investment_groups.referrer_information').' as referrer_information',
			)
		);
		if(empty($group_ids)){
			$this->db->where_in('investment_groups.id','0');
		}else{
			$this->db->where_in('investment_groups.id',$group_ids);
		}
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
        $this->db->order_by('created_on','DESC', FALSE);
   		return $this->db->get('investment_groups')->result();
	}

	function count_paying_groups($group_ids = array()){
   		return count($this->get_paying_groups($group_ids));
	}


	function get_all_groups(){
		$this->db->select(
			array(
				'id',
				$this->dx('name').' as name',
				$this->dx('owner').' as owner',
				$this->dx('status').' as status',
			)
		);
   		return $this->db->get('investment_groups')->result();
	}

	function get_in_arrears_paying_groups(){
		$arr = array();
		$this->db->select(
			array(
				'investment_groups.id as id',
				$this->dx('investment_groups.name').' as name',
				'sum('.$this->dx('billing_payments.amount').') - sum('.$this->dx('billing_invoices.amount').') as arrears'
			)
		);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		$this->db->where($this->dx('billing_invoices.due_date').' < '.strtotime('today'),NULL,FALSE);
		$this->db->join('billing_payments','investment_groups.id = '.$this->dx('billing_payments.group_id'));
		$this->db->join('billing_invoices','investment_groups.id = '.$this->dx('billing_invoices.group_id'));
   		$groups = $this->db->get('investment_groups')->result();
   		foreach ($groups as $group) {
   			# code...
   			if($group->arrears>0):
   				$arr[] = $group;
   			endif;
   		}
   		return $arr;
	}


	function get_group_options_by_group_id_list($group_id_list = '0'){
		$arr = array();
		$this->db->select(
			array(
				'investment_groups.id as id',
				$this->dx('investment_groups.name').' as name',
			)
		);
		$this->db->where('id IN ('.$group_id_list.')',NULL,FALSE);
		$groups = $this->db->get('investment_groups')->result();
   		return $groups;
	}

	function count_in_arrears_paying_groups(){
		$this->db->select(
			array(
				'investment_groups.id as id',
				$this->dx('investment_groups.name').' as name',
				'sum('.$this->dx('billing_payments.amount').') - sum('.$this->dx('billing_invoices.amount').') as arrears'
			)
		);
		$this->db->where($this->dx('investment_groups.status').' = 1 ',NULL,FALSE);
		$this->db->join('billing_payments','investment_groups.id = '.$this->dx('billing_payments.group_id'));
		$this->db->join('billing_invoices','investment_groups.id = '.$this->dx('billing_invoices.group_id'));
   		return $this->db->count_all_results('investment_groups');
	}

	function get_groups_signed_up_today_by_bank_branch(){
		$this->db->select(
			array(
				$this->dx('bank_accounts.bank_branch_id')." as bank_branch_id ",
				"COUNT(".$this->dx('members.group_id').") as member_count",
			)
		);
		$this->db->join("bank_accounts","investment_groups.id = ".$this->dx('bank_accounts.group_id'));
		$this->db->join("members","investment_groups.id = ".$this->dx('members.group_id'));
        $this->db->group_by(array($this->dx("bank_accounts.bank_branch_id")));
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('investment_groups.created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
   		
        $this->db->order_by('member_count', 'ASC', FALSE);
   		return $this->db->get('investment_groups')->result();
	}

	function get_groups_signed_up_today_count_by_bank_branch_array($bank_branches = array()){
		$arr = array();
		foreach($bank_branches as $key => $value):
			$arr[$key] = 0;
		endforeach;
		$this->db->select(
			array(
				$this->dx('bank_accounts.bank_branch_id')." as bank_branch_id ",
			)
		);
		$this->db->join("bank_accounts","investment_groups.id = ".$this->dx('bank_accounts.group_id'));
        $this->db->group_by(array($this->dx("bank_accounts.bank_branch_id")));
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('investment_groups.created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
   		$result = $this->db->get('investment_groups')->result();
   		foreach($result as $row):
   			$arr[$row->bank_branch_id] += 1;
   		endforeach;
   		return $arr;
	}


	function count_groups_signed_up_today(){
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
   		return $this->db->count_all_results('investment_groups');
	}

	function get_options($params=array()){
		$arr = array();
		$this->db->select(array('id',$this->dx('name').' as name'));
		$this->db->order_by($this->dx('investment_groups.name').'ASC',FALSE);
		if($params){
    		foreach ($params as $k => $v) {
    			if($v=='id'){
    				$this->db->where('id',$v);
    			}else if($v=='lock_access'){
    				$this->db->where('('.$this->dx('lock_access').'="0" OR'.$this->dx('lock_access').' is NULL OR '.$this->dx('lock_access').'="" )',NULL,FALSE);
    			}else{
    				$this->db->where($this->dx($k).'="'.$v.'"',NULL,FALSE);
    			}
    		}
    	}
		$groups = $this->db->get('investment_groups')->result();
		foreach($groups as $group){
			$arr[$group->id] = $group->name;
		}
		return $arr;
	}

	function get_group_account_number_options($params=array()){
		$arr = array();
		$this->db->select(array('id',$this->dx('account_number').' as account_number'));
		if($params){
    		foreach ($params as $k => $v) {
    			if($v=='id'){
    				$this->db->where('id',$v);
    			}else if($v=='lock_access'){
    				$this->db->where('('.$this->dx('lock_access').'="0" OR'.$this->dx('lock_access').' is NULL OR '.$this->dx('lock_access').'="" )',NULL,FALSE);
    			}else{
    				$this->db->where($this->dx($k).'="'.$v.'"',NULL,FALSE);
    			}
    		}
    	}
		$groups = $this->db->get('investment_groups')->result();
		foreach($groups as $group){
			$arr[$group->id] = $group->account_number;
		}
		return $arr;
	}

	function get_group_options_account_number_as_key($paying_group_ids = array()){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('investment_groups.name').' as group_name',
				$this->dx('bank_accounts.account_number').' as account_number',
			)
		);
		if(empty($paying_group_ids)){
			$this->db->where_in('investment_groups.id','0');
		}else{
			$this->db->where_in('investment_groups.id',$paying_group_ids);
		}
		$this->db->join('investment_groups',$this->dx('bank_accounts.group_id').' = investment_groups.id');
		$bank_accounts = $this->db->get('bank_accounts')->result();
		foreach($bank_accounts as $bank_account){
			$arr[$bank_account->account_number] = $bank_account->group_name;
		}
		return $arr;
	}

	function get_group_options_account_number_as_key_group_id_as_value($paying_group_ids = array()){
		$arr = array();
		$this->db->select(
			array(
				'investment_groups.id as group_id',
				$this->dx('bank_accounts.account_number').' as account_number',
			)
		);
		if(empty($paying_group_ids)){
			$this->db->where_in('investment_groups.id','0');
		}else{
			$this->db->where_in('investment_groups.id',$paying_group_ids);
		}
		$this->db->join('investment_groups',$this->dx('bank_accounts.group_id').' = investment_groups.id');
		$bank_accounts = $this->db->get('bank_accounts')->result();
		foreach($bank_accounts as $bank_account){
			$arr[$bank_account->account_number] = $bank_account->group_id;
		}
		return $arr;
	}

	function delete($id=0){
		$this->db->where('id',$id);
		return $this->db->delete('investment_groups');
	}

	function generate_slug($slug){
        $this->db->where($this->dx("slug")." like '".$slug."%'",NULL,FALSE);
        $count = $this->db->get('investment_groups')->num_rows();
        if($count>0){
                return $slug.=$count+1;
        }else{
                return $slug;                
        }           
   	}

   	function get_by_slug($slug = '',$id=''){
		$this->select_all_secure('investment_groups');
		$this->db->where($this->dx('slug').' = "'.$slug.'"',NULL,FALSE);
		if($id)
		{
			$this->db->where('id !=',$id);
		}
		$this->db->limit(1);
		return $this->db->get('investment_groups')->row();
   	}

	function get_by_join_code($join_code=''){
		$this->select_all_secure('investment_groups');
		$this->db->where($this->dx('join_code').' = "'.$join_code.'"',NULL,FALSE);		
		$this->db->limit(1);
		return $this->db->get('investment_groups')->row();
   	}

   	function get_groups_by_account($account_number='')
   	{
   		$this->select_all_secure('investment_groups');
   		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
   		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
   		return $this->db->get('investment_groups')->result();
   	}

   	function current_user_groups($user_id = 0){
   		$this->db->select(array(
   			'investment_groups.id',
   			$this->dx('investment_groups.name').' as name',
   			$this->dx('investment_groups.slug').' as slug',
   			$this->dx('investment_groups.size').' as size',
   			$this->dx('investment_groups.sms_balance').' as sms_balance',
   			$this->dx('investment_groups.account_number').' as account_number',
   			$this->dx('investment_groups.is_validated').' as is_validated',
   			$this->dx('investment_groups.theme').' as theme',
   			$this->dx('investment_groups.online_banking_enabled').' as online_banking_enabled',
   			$this->dx('investment_groups.enable_member_information_privacy').' as enable_member_information_privacy',
   			$this->dx('investment_groups.member_listing_order_by').' as member_listing_order_by',
   			$this->dx('investment_groups.order_members_by').' as order_members_by',
   			$this->dx('investment_groups.enable_send_monthly_email_statements').' as enable_send_monthly_email_statements',
   			$this->dx('investment_groups.enable_bulk_transaction_alerts_reconciliation').' as enable_bulk_transaction_alerts_reconciliation',
   			$this->dx('investment_groups.disable_arrears').' as disable_arrears',
   			$this->dx('investment_groups.disable_ignore_contribution_transfers').' as disable_ignore_contribution_transfers',
   			$this->dx('investment_groups.disable_member_directory').' as disable_member_directory',
   			$this->dx('investment_groups.disable_member_edit_profile').' as disable_member_edit_profile',
   			$this->dx('investment_groups.enable_absolute_loan_recalculation').' as enable_absolute_loan_recalculation',
   			$this->dx('investment_groups.avatar').' as avatar',
   			$this->dx('investment_groups.country_id').' as country_id',
   			$this->dx('countries.name').' as country_name',
   			$this->dx('investment_groups.phone').' as phone',
   			$this->dx('investment_groups.email').' as email',
   			$this->dx('investment_groups.subscription_status').' as subscription_status',
   			$this->dx('investment_groups.trial_days').' as trial_days',
   			$this->dx('investment_groups.group_offer_loans').' as group_offer_loans',
   			$this->dx('members.is_admin').' as is_admin',
   			$this->dx('members.group_role_id').' as group_role_id',
   		));
   		$this->db->where($this->dx('members.user_id').' = "'.$user_id.'"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').' = "1" ',NULL,FALSE);
   		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->join('members',$this->dx('members.group_id').' = investment_groups.id');
   		$this->db->join('countries',$this->dx('investment_groups.country_id').' = countries.id');
   		$this->db->order_by($this->dx('investment_groups.name'),'DESC',FALSE);
   		return $this->db->get('investment_groups')->result();
   	}

   	function current_user_groups_managed($user_id = 0){
   		$this->select_all_secure('investment_groups');
   		$this->db->where($this->dx('group_account_managers.user_id').' = "'.$user_id.'"',NULL,FALSE);
   		$this->db->join('group_account_managers',$this->dx('group_account_managers.group_id').' = investment_groups.id');
   		$this->db->order_by($this->dx('investment_groups.name'),'DESC',FALSE);
   		return $this->db->get('investment_groups')->result();
   	}

   	function count_current_user_groups($user_id = 0){
   		$this->db->where($this->dx('members.user_id').' = "'.$user_id.'"',NULL,FALSE);
   		$this->db->where($this->dx('members.active').' = "1" ',NULL,FALSE);
   		$this->db->where($this->dx('members.is_deleted').' IS NULL ',NULL,FALSE);
   		$this->db->join('members',$this->dx('members.group_id').' = investment_groups.id');
   		return $this->db->count_all_results('investment_groups');
   	}

   	function count_current_bank_staff_groups($user_id = 0){
   		$this->db->where($this->dx('created_by').' = "'.$user_id.'"',NULL,FALSE);
   		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
   		return $this->db->count_all_results('investment_groups');
   	}

   	function get_current_bank_staff_groups_ids($user_id = 0){
   		$this->select_all_secure('investment_groups');
   		$this->db->where($this->dx('created_by').' = "'.$user_id.'"',NULL,FALSE);
   		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
   		$results = $this->db->get('investment_groups')->result();
   		$arr = [];
   		foreach ($results as $key => $result) {
   			$arr[] = $result->id;
   		}
   		return $arr;
   	}

   	function get_group_status()
   	{
   		return $this->group_status;
   	}

   	function get_group_options_for_billing(){
   		$this->select_all_secure('investment_groups');
   		//$this->db->where($this->dx('status').'="1"');
   		$result = $this->db->get('investment_groups')->result();
   		$arr = array();
   		if($result){
   			foreach ($result as $value) {
   				$arr[$value->id] = $value->name.' - '.$value->account_number;
   			}
   		}

   		return $arr;
   	}

   	function get_group_owner($group_id=0){
   		$this->db->select(array(
   				$this->dx('investment_groups.name').' as name',
   				$this->dx('investment_groups.slug').' as slug',
   				$this->dx('investment_groups.sms_balance').' as sms_balance',
   				$this->dx('investment_groups.account_number').' as account_number',
   				$this->dx('investment_groups.billing_package_id').' as billing_package_id',
   				$this->dx('investment_groups.billing_cycle').' as billing_cycle',
   				$this->dx('investment_groups.billing_date').' as billing_date',
   				$this->dx('users.first_name').' as first_name',
   				$this->dx('users.last_name').' as last_name',
   				$this->dx('users.phone').' as phone',
   				$this->dx('users.email').' as email',
   				'users.id as user_id',
   				$this->dx('investment_groups.owner').' as owner',
   				$this->dx('investment_groups.owner').' as user_id',
   				$this->dx('investment_groups.email').' as group_email',
   				$this->dx('investment_groups.status').' as status',
   				$this->dx('investment_groups.id').' as id',
   				'members.id as member_id',
   			));
   		$this->db->where('investment_groups.id',$group_id);
   		$this->db->join('users',$this->dx('investment_groups.owner').'= users.id');
   		$this->db->join('members',$this->dx('members.user_id').'= users.id');
   		return $this->db->get('investment_groups')->row();
   	}

   	function get_group_by_account($account_number=0,$id=0){
   		$this->db->select('id');
   		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
   		$id = $this->db->get('investment_groups')->row();
   		if($id){
   			return $id->id;
   		}else{
   			return FALSE;
   		}
   	}

   	function count_groups_by_account($account_number=0,$id=0){
   		$this->db->where($this->dx('account_number').'="'.$account_number.'"',NULL,FALSE);
   		if($id)
		{
			$this->db->where('id !=',$id);
		}
		return $this->db->count_all_results('investment_groups')?:0;
   	}

   	function groups_with_trial_days(){
   		$this->select_all_secure('investment_groups');
   		$this->db->where($this->dx('trial_days').'>"0"',NULL,FALSE);
   		$this->db->where('('.$this->dx('status').'="" OR '.$this->dx('status').'= "0" OR'.$this->dx('status').' =" " OR '.$this->dx('status').' is NULL OR'.$this->dx('status').'="1" )',NULL,FALSE);
   		$this->db->where('('.$this->dx('lock_access').'="" OR '.$this->dx('lock_access').'= "0" OR'.$this->dx('lock_access').' =" " OR '.$this->dx('lock_access').' is NULL )',NULL,FALSE);
   		return $this->db->get('investment_groups')->result();
   	}

   	function get_groups_for_user($user_id=0){
   		$this->db->select(array(
   				$this->dx('investment_groups.name').' as name',
   				$this->dx('investment_groups.status').' as status',
   				$this->dx('investment_groups.lock_access').' as lock_access',
   				'investment_groups.id'.' as group_id',
   			));
   		$this->db->where($this->dx('members.user_id').'="'.$user_id.'"',NULL,FALSE);
   		$this->db->join('members',$this->dx('members.group_id').'= investment_groups.id',NULL,FALSE);
   		return $this->db->get('investment_groups')->result();
   	}

   	function get_groups_for_user_pairing($post=array()){
   		$user_id_list = '';
   		if($post){
   			foreach ($post as $key => $value) {
   				if($user_id_list){
   					$user_id_list.=','.$value->id;
   				}else{
   					$user_id_list=$value->id;
   				}
   			}
   		}else{
   			$user_id_list = "0";
   		}
   		$this->db->select(array(
   				$this->dx('investment_groups.name').' as name',
   				$this->dx('members.user_id').' as user_id',
   				'investment_groups.id'.' as group_id',
   			));
   		$this->db->where($this->dx('members.user_id').' IN ('.$user_id_list.')',NULL,FALSE);
   		$this->db->group_by(array(
   			$this->dx('members.user_id'),
   			$this->dx('investment_groups.name')
   		));	
   		$this->db->join('members',$this->dx('members.group_id').'= investment_groups.id',NULL,FALSE);
   		$results = $this->db->get('investment_groups')->result();
   		$arr = array();
   		foreach ($results as $key => $value) {
   			if($value->name !="Karateng&quot; Widows"){
   				if(array_key_exists($value->user_id, $arr)){
   					$arr[$value->user_id] = $arr[$value->user_id].', '.$value->name;
   				}else{
   					$arr[$value->user_id] = $value->name;
   				}
   			}	
   		}
   		return $arr;
   	}


   	function get_groups_to_be_billed_today($date=0,$limit=0){
   		if($date){

   		}else{
   			$date = time();
   		}if($limit && is_numeric($limit)){

   		}else{
   			$limit=20;
   		}
   		$this->select_all_secure('investment_groups');
   		$this->db->select(array(
                    "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('investment_groups.billing_date')." ),'%Y %D %M') as billing_date2 ",
                ));
   		$this->db->where($this->dx('active').'="1"',NULL,FALSE);
   		$this->db->where($this->dx('status').'="1"',Null,FALSE);
   		$this->db->where($this->dx('lock_access').'!="1"',Null,FALSE);
   		$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('billing_date')."),'%Y %d %m') = '" . date('Y d m',$date) . "'", NULL, FALSE);
   		$this->db->limit($limit);
   		return $this->db->get('investment_groups')->result();
   	}

   	function get_this_group_currency($group_id=0){
       $this->db->select(array(
                $this->dx('countries.currency_code').' as currency_code'
            ));
        $this->db->where('investment_groups.id',$group_id?:$this->group_id);
        $this->db->join('countries','countries.id = '.$this->dx('currency_id'));
        return $this->db->get('investment_groups')->row()->currency_code;
    }

    function get_group_active_size($group_id=0){
    	$this->db->select($this->dx('active_size').' as active_size');
    	$this->db->where('id',$group_id);
    	$group = $this->db->get('investment_groups')->row();
    	if($group){
    		return $group->active_size;
    	}else{
    		return 0;
    	}
    }

    function get_group_by_bank_account_number($bank_account_number = 0){
    	$this->select_all_secure('investment_groups');
    	$this->db->where($this->dx('bank_accounts.account_number').' = "'.$bank_account_number.'"',NULL,FALSE);
    	$this->db->where($this->dx('bank_accounts.active')." = '1' ",NULL,FALSE);
    	$this->db->join('bank_accounts',$this->dx('bank_accounts.group_id')." = investment_groups.id");
    	return $this->db->get('investment_groups')->row();
    }


    function count_all_paying_groups($params=array()){
    	if($params){
    		foreach ($params as $k => $v) {
    			if($v=='id'){
    				$this->db->where('id',$v);
    			}else{
    				$this->db->where($this->dx($k).'="'.$v.'"',NULL,FALSE);
    			}
    		}
    	}
    	$this->db->where($this->dx('status').'="1"',NULL,FALSE);
    	$this->db->where('('.$this->dx('lock_access').'="0" OR'.$this->dx('lock_access').' is NULL OR '.$this->dx('lock_access').'="" )',NULL,FALSE);
    	return $this->db->count_all_results('investment_groups')?:0;
    }


    function get_all_paying_groups($params=array()){
    	$this->select_all_secure('investment_groups');
    	if($params){
    		foreach ($params as $k => $v) {
    			if($v=='id'){
    				$this->db->where('id',$v);
    			}else{
    				$this->db->where($this->dx($k).'="'.$v.'"',NULL,FALSE);
    			}
    		}
    	}
    	$this->db->where($this->dx('status').'="1"',NULL,FALSE);
    	$this->db->where('('.$this->dx('lock_access').'="0" OR'.$this->dx('lock_access').' is NULL OR '.$this->dx('lock_access').'="" )',NULL,FALSE);
    	$this->db->order_by($this->dx('investment_groups.name').'ASC',FALSE);
    	return $this->db->get('investment_groups')->result();
    }

    function get_group_table_data($table_name = "",$group_id = 0){
    	$this->db->select(
    		array(
    			'id',
    			$this->dx('contribution_id').' as contribution_id ',
    		)
    	);
    	$this->db->where($this->dx('group_id')." = ".$group_id,NULL,FALSE);
    	return $this->db->get($table_name)->result();
    }

    function update_group_table_data($id = 0,$table_name = "",$input = array()){
    	return $this->update_secure_data($id,$table_name,$input);
    }

    function count_groups_with_members_between($lower=0,$upper=20,$paying=FALSE){
    	$this->db->where($this->dx('active_size').' >= '.$lower,NULL,FALSE);
    	$this->db->where($this->dx('active_size').' < '.$upper,NULL,FALSE);
    	if($paying){
    		$this->db->where($this->dx('status').' = "1"',NULL,FALSE);
    	}
    	return $this->db->count_all_results('investment_groups')?:0;
    }

    function get_group_total_transactions($group_ids=array()){
    	$group_id_list ='';
    	if($group_ids){
    		foreach ($group_ids as $key => $value) {
    			if($group_id_list){
    				$group_id_list.=','.$value;
    			}else{
    				$group_id_list=$value;
    			}
    		}
    	}
    	$this->db->select(array(
    			$this->dx('group_id').' as group_id',
    			//$this->dx('member_id').' as member_id',
    			'sum('.$this->dx('amount').') as sum',
    			'count('.$this->dx('group_id').') as count',
    		));
    	$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('contribution_id').' >"0" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_id_list.')',NULL,FALSE);
    	$this->db->group_by(array($this->dx("group_id")));
    	$return = $this->db->get('deposits')->result();
    	$arr = array();
    	if($return){
    		foreach ($return as $key => $value) {
    			$days_group_operate = round($this->group_age_on_chamasoft($value->group_id));
    			$members = $this->members_in_group($value->group_id);
    			$count = $value->count;
    			$months = round($days_group_operate/30);
    			if($months){
    				$arr[$key] = array(
    				//'deposits_per_month'=>round($count/$months),
    				//'member_id' => $value->member_id,
    				'group_id' => $value->group_id,
    				'totalAmount' => round(($value->sum/$months)/$members),
    				);
    			}
    		}
    	}

    	return $arr;
    }

    function group_age_on_chamasoft($group_id=0){
    	$this->db->select(array(
    			$this->dx('created_on').' as created_on'
    		));
    	$this->db->where('id',$group_id);
    	$created_on = $this->db->get('investment_groups')->row();
    	if($created_on){
    		return days_ago($created_on->created_on);
    	}
    }

    function get_oldest_club(){
    	$this->select_all_secure('investment_groups');
    	$this->db->select(array(
                    "DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y %D %M') as created_on2 ",
                ));
    	$this->db->order_by($this->dx('created_on'),'ASC',FALSE);
    	$this->db->limit(1);
    	return $this->db->get('investment_groups')->row();
    }

    function get_groups_with_members($lower=0,$upper=0){
    	echo $lower.'<br/>';
    	echo $upper.'<br/>';
    	$this->db->select('id as id');
    	$this->db->where('('.$this->dx('active_size').' >= '.trim($lower).' )',NULL,FALSE);
    	$this->db->where('('.$this->dx('active_size').' < '.trim($upper).' )',NULL,FALSE);
    	$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	return $this->db->get('investment_groups')->result();
    }

    function get_inactive_members($group_ids=array()){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}
    	$this->db->where('('.$this->dx('active').' = "0" OR '.$this->dx('active').' IS NULL OR '.$this->dx('active').' ="" OR '.$this->dx('active').' =" " )',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	return $this->db->get('members')->result();
    }

    function get_active_members($group_ids=array()){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}
    	$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	return $this->db->get('members')->result();
    }

    function members_in_group($id=0){
    	$this->db->select($this->dx('active_size').' as active_size');
    	$this->db->where('id',$id);
    	$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	return $this->db->get('investment_groups')->row()->active_size;
    }


    function member_fines_per_group($group_ids=array()){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}

		$this->db->select(array(
				$this->dx('group_id').' as group_id',
    			//$this->dx('member_id').' as member_id',
    			'sum('.$this->dx('amount').') as sum',
    			'count('.$this->dx('group_id').') as count',
			));
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	$this->db->group_by(array($this->dx("member_id")));
    	$return = $this->db->get('deposits')->result();

    	$arr = array();
    	if($return){
    		foreach ($return as $key => $value) {
    			$days_group_operate = round($this->group_age_on_chamasoft($value->group_id));
    			$members = $this->members_in_group($value->group_id);
    			$count = $value->count;
    			$months = round($days_group_operate/30);
    			if($months){
    				$arr[$key] = array(
    				//'deposits_per_month'=>round($count/$months),
    				//'member_id' => $value->member_id,
    				'group_id' => $value->group_id,
    				'totalAmount' => $value->sum,
    				);
    			}
    		}
    	}

    	return $arr;

    	print_r($return);
	}

	function average_loans_by_group($group_ids=array(),$year_ago=0){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}

		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	if($year_ago){
    		echo 'time'.date('d-m-Y ',strtotime(-$year_ago.' years')).'<br/>';
    		$this->db->where($this->dx('disbursement_date').' > "'.strtotime(-$year_ago.' years').'"',NULL,FALSE);
    	}
    	return $loans = $this->db->count_all_results('loans');
	}

	function average_loan_amount_by_group($group_ids=array(),$year_ago=0){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}

		$this->db->select(array(
				$this->dx('loan_amount').' as amount',
				$this->dx('member_id').' as member_id',
				$this->dx('group_id').' as group_id',
			));
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	$this->db->group_by($this->dx("group_id"));
    	if($year_ago){
    		$this->db->where($this->dx('disbursement_date').' > "'.strtotime(-$year_ago.' years').'"',NULL,FALSE);
    	}
    	$return = $this->db->get('loans')->result();

    	$sum = 0;
    	foreach ($return as $key => $value) {
    		$sum+=$value->amount;
    	}
    	return $sum/count($return);

	}

	function get_groups_doing_stocks($group_ids=array()){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}

		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	return $this->db->count_all_results('stocks')?:0;
	}

	function get_total_value_for_groups_doing_stocks($group_ids=array()){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}
		$this->db->select(array(
				'sum('.$this->dx('amount').') as amount'
			));
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	$this->db->where($this->dx('type').' IN (13,14,15,16)',NULL,FALSE);
    	$return= $this->db->get('withdrawals')->row();
    	if($return){
    		return $return->amount;
    	}else{
    		return 0;
    	}
	}	

	function get_groups_doing_assets($group_ids=array()){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}

		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	return $this->db->count_all_results('assets')?:0;
	}

	function get_total_value_for_groups_doing_assets($group_ids=array()){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}
		$this->db->select(array(
				'sum('.$this->dx('amount').') as amount'
			));
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	$this->db->where($this->dx('type').' IN (5,6,7,8)',NULL,FALSE);
    	$return= $this->db->get('withdrawals')->row();
    	if($return){
    		return $return->amount;
    	}else{
    		return 0;
    	}
	}	



	function get_groups_doing_money_market($group_ids=array()){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}

		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	return $this->db->count_all_results('money_market_investments')?:0;
	}

	function get_total_value_for_groups_doing_money_market($group_ids=array()){
		$group_list = '';
		if($group_ids){
			foreach ($group_ids as $id) {
				if($group_list){
					$group_list.=','.$id;
				}else{
					$group_list=$id;
				}
			}
		}
		$this->db->select(array(
				'sum('.$this->dx('amount').') as amount'
			)
		);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
    	$this->db->where($this->dx('group_id').' IN ('.$group_list.')',NULL,FALSE);
    	$this->db->where($this->dx('type').' IN (17,18,19,20)',NULL,FALSE);
    	$return= $this->db->get('withdrawals')->row();
    	if($return){
    		return $return->amount;
    	}else{
    		return 0;
    	}
	}	

	function get_group_active_sizes_array(){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('active_size').' as active_size'
			)
		);
		$groups = $this->db->get('investment_groups')->result();
		foreach($groups as $group):
			if($group->active_size){
				$arr[$group->id] = $group->active_size;
			}else{
				$arr[$group->id] = 1;
			}
		endforeach;
		return $arr;
	}

	function get_group_sizes_array(){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('size').' as size'
			)
		);
		$groups = $this->db->get('investment_groups')->result();
		foreach($groups as $group):
			if($group->size){
				$arr[$group->id] = $group->size;
			}else{
				$arr[$group->id] = 1;
			}
		endforeach;
		return $arr;
	}

	function get_groups_by_partner_id($partner_id = 0){
		$this->select_all_secure('investment_groups');
		$this->db->where($this->dx('partner_id')." = '".$partner_id."' ",NULL,FALSE);
		return $this->db->get('investment_groups')->result();
	}

	function count_groups_by_partner_id($partner_id = 0){
		$this->db->where($this->dx('partner_id')." = '".$partner_id."' ",NULL,FALSE);
		return $this->db->count_all_results('investment_groups');
	}

	function get_search_options(){
		$result = new stdClass();
		$query = trim($this->input->get("q"));

		$this->db->select(
			array(
				$this->dx('group_id')." as group_id "
			)
		);
		$this->db->where($this->dx('active')." = 1 ",NULL,FALSE);
		$this->db->where("  CONVERT(" . $this->dx('account_number') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' ",NULL,FALSE);
		$bank_accounts = $this->db->get('bank_accounts')->result();
		$arr = array();
		foreach($bank_accounts as $bank_account):
			if(in_array($bank_account->group_id,$arr)){

			}else{
				$arr[] = $bank_account->group_id;
			}
		endforeach;
		if(empty($arr)){
			$arr = array(0);
		}


		$this->select_all_secure('investment_groups');
		$this->db->select(
			array(
				'CONCAT("'.$this->application_settings->protocol.$this->application_settings->url.'/uploads/groups/'.'",'.$this->dx('investment_groups.avatar').') as avatar_url ',
				'CONCAT("'.$this->application_settings->protocol.$this->application_settings->url.'/templates/admin_themes/groups/img/default_group_avatar.png'.'") as default_avatar_url ',
				$this->dx('users.first_name')." as first_name ",
				$this->dx('users.last_name')." as last_name ",
				$this->dx('users.phone')." as phone ",
				$this->dx('users.email')." as email ",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('investment_groups.created_on')."),'%d-%m-%Y') as formatted_signup_date ",
				" DATE_FORMAT(FROM_UNIXTIME(".$this->dx('users.last_login')."),'%d-%m-%Y') as formatted_last_login_date ",
			)
		);
		$this->db->where(" ( 
			CONVERT(" . $this->dx('investment_groups.name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR 
			CONVERT(" . $this->dx('investment_groups.account_number') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR
			CONVERT(" . $this->dx('users.first_name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR
			CONVERT(" . $this->dx('users.last_name') . " USING 'latin1')  like '%" . $this->escape_str($query) . "%' OR investment_groups.id IN(".implode(',',$arr).")
		)", NULL, FALSE);
	
		//$this->db->limit(10);
		$this->db->join('users',$this->dx('owner').' = users.id ','LEFT');
		//$this->db->join('bank_accounts',$this->dx('group_id').' = investment_groups.id ','left');
		$this->db->order_by($this->dx('investment_groups.created_on'),'DESC',FALSE);
		$this->db->order_by($this->dx('investment_groups.name'),'DESC',FALSE);
		$groups = $this->db->get('investment_groups')->result();
		$result->total_count = count($groups);
		$result->incomplete_results = false;
		$result->items = $groups;

		echo json_encode($result);
	}

	function get_user_groups_array($user_ids_array = array()){
		$this->db->select(
			array(
				"investment_groups.id as id",
				$this->dx('investment_groups.name')." as name ",
				$this->dx('members.user_id')." as user_id ",
			)
		);
		if(empty($user_ids_array)){
			$this->db->where($this->dx('members.user_id')." IN (0) ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('members.user_id')." IN (".implode(',',$user_ids_array).") ",NULL,FALSE);
		}
		$this->db->join('members',$this->dx('members.group_id')." = investment_groups.id ");
		$groups = $this->db->get('investment_groups')->result();
		$arr = array();
		foreach($groups as $group):
			$arr[$group->user_id][] = $group->name;
		endforeach;
		return $arr;
	}

	function get_group_contact_person_array($groups=array()){
		$arr = array();
		if($groups){
			$list = '0';
			foreach ($groups as $id=>$group) {
				if($list){
					$list.=','.$id;
				}else{
					$list =$id;
				}
			}
			$this->db->select(
				array(
					$this->dx('users.first_name')." as first_name ",
					$this->dx('users.last_name')." as last_name ",
					$this->dx('users.phone')." as phone ",
					$this->dx('users.email')." as email ",
					'investment_groups.id'." as group_id ",
				)
			);
			$this->db->where('investment_groups.id IN('.$list.')',NULL,FALSE);
			$this->db->join('users',$this->dx('owner').' = users.id ');
			$users = $this->db->get('investment_groups')->result();
			foreach ($users as $key => $user) {
				$arr[$user->group_id] = array(
					'name' => $user->first_name.' '.$user->last_name,
					'phone' => $user->phone,
					'email' => $user->email,
				);
			}
		}

		return $arr;
	}

	function get_groups_billing_cycle($lower=0,$upper=0,$paying=0){
		$this->db->select(array(
			'count(*) as count',
			$this->dxa('billing_cycle'),
		));
		$this->db->where($this->dx('active_size').' >= '.$lower,NULL,FALSE);
    	$this->db->where($this->dx('active_size').' < '.$upper,NULL,FALSE);
    	if($paying){
    		$this->db->where($this->dx('status').' = "1"',NULL,FALSE);
    	}
    	$this->db->group_by(array($this->dx('billing_cycle')));
    	return $this->db->get('investment_groups')->result();
	}

	function get_group_countries_no(){
		$this->db->select(
			array(
				'id as id',
				$this->dx('currency_id')." as currency_id  ",
			)
		);
		$groups = $this->db->get('investment_groups')->result();
		$arr = array();
		foreach($groups as $group):
			$arr[$group->currency_id] = $group->id;
		endforeach;
		return $arr;

	}
	function get_group_countries_no1(){
		$this->db->select(
			array(
				'COUNT(DISTINCT'.$this->dx('country_id').') as country_id ',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('investment_groups')->row();	

	}

	function get_group_objects_array($group_ids = array()){
		$arr = array();
		$this->select_all_secure('investment_groups');
		if(empty($group_ids)){
			$this->db->where_in('id','0');
		}else{
			$this->db->where_in('id',$group_ids);
		}
		$groups = $this->db->get('investment_groups')->result();
		foreach($groups as $group):
			$arr[$group->id] = $group;
		endforeach;
		return $arr;
	}

	function get_group_name($id = 0){
		if($id){
			$this->db->select(array(
				$this->dxa('name')
			));
			$this->db->where('id',$id);
			$group = $this->db->get('investment_groups')->row();
			if($group){
				return $group->name;
			}
		}
	}
	function mark_groups_as_unreconciled(){
		$where = "  id IS NOT NULL ;";
		$input = array(
			'statements_reconciled' => 1,
			'fine_statements_reconciled' => 1,
			'active' => 1,
			'modified_on' => time(),
		);
		return $this->update_secure_where($where,'investment_groups',$input);
	}

	function unencrypt_group_id($table){
		return $this->update_unencrypted_group_id($table);
	}

	function get_groups_bank_staff_onboard_count_per_month_from_date_array($user_id = 0,$from = 0,$frequency =0){
    	$arr = array();
    	$this->select_all_secure('investment_groups');
    	$this->db->select(
			array(
				"id as id",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year ",
				"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%b') as month ",
			)
		);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('created_on'),'ASC',FALSE);
		if($from){
			$this->db->where($this->dx('created_on').'>="'.$from.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('created_on').'>="'.strtotime("-12 months").'"',NULL,FALSE);
		}
		$this->db->where($this->dx('created_by').' = "'.$user_id.'"',NULL,FALSE);
		$groups = $this->db->get('investment_groups')->result();
		$arr = array();

		foreach ($groups as $key => $group) {
			if($frequency == 1){
				$arr[date('dMY',$group->created_on)][] = $group;
			}else if($frequency == 2){
				$arr[date('dDMY',$group->created_on)][] = $group;	
			}else if($frequency == 3){
				$arr[date('M Y',$group->created_on)][] = $group;
			}else if($frequency == 4){
				$arr[date('M Y',$group->created_on)][] = $group;
			}else if($frequency == 5){
				$arr[date('M Y',$group->created_on)][] = $group;
			}else{
				$arr[date('M Y',$group->created_on)][] = $group;		
			}
		}
		$count_arr = array();
		foreach ($arr as $key => $ar) {
			$count_arr[$key] = count($ar);
		}
		return $count_arr;
    }

    function get_latest_bank_staff_group_signups($user_id = 0,$from_date = 0){
		$this->select_all_secure('investment_groups');
		if($from_date){
			$this->db->where($this->dx('created_on').' >="'.$from_date.'"',NULL,FALSE);
		}
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		$this->db->where($this->dx('created_by').' = "'.$user_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' ="1"',NULL,FALSE);
		$this->db->limit(20);		
		return $this->db->get('investment_groups')->result();
	}

	function get_group_max_id(){
		$this->db->select(
			array(
				" MAX(id) as id "
			)
		);
		return $this->db->get('investment_groups')->row()->id;
	}

	function get_group_to_remove_duplicates(){
		$this->select_all_secure('investment_groups');
		$this->db->where("(".$this->dx('remove_duplicates')." IS NULL OR ".$this->dx('remove_duplicates')."  = 0 )",NULL,FALSE);
		return $this->db->get('investment_groups')->row();
	}

	function count_potential_archive_groups(){
		$this->db->where($this->dx('last_group_activity').' < '.strtotime('-6 months',time()),NULL,FALSE);
		$this->db->where($this->dx('subscription_status').' IN(2,5,7,8)',NULL,FALSE);
		//$this->db->where($this->dx('active_size').' <= "5"',NULL,FALSE);
		$this->db->order_by($this->dx('active_size'),'ASC',FALSE);
		return $this->db->count_all_results('investment_groups');

		// $this->db->where($this->dx('created_on').' < '.strtotime('-10 days',time()),NULL,FALSE);
		// $this->db->where($this->dx('subscription_status').' = "8"',NULL,FALSE);
		// $count2 =  $this->db->count_all_results('investment_groups');
		// return $count2;
	}

	function get_potential_archive_groups(){
		$this->select_all_secure('investment_groups');
		$this->db->where($this->dx('last_group_activity').' < '.strtotime('-6 months',time()),NULL,FALSE);
		$this->db->where($this->dx('subscription_status').' IN(2,5,7)',NULL,FALSE);
		//$this->db->where($this->dx('active_size').' <= "5"',NULL,FALSE);
		$this->db->order_by($this->dx('active_size'),'ASC',FALSE);
		return $this->db->get('investment_groups')->result();

		// $this->select_all_secure('investment_groups');
		// $this->db->where($this->dx('created_on').' < '.strtotime('-10 days',time()),NULL,FALSE);
		// $this->db->where($this->dx('subscription_status').' = "8"',NULL,FALSE);
		// $result2 =  $this->db->get('investment_groups')->result();

		//return $result2;
	}


	function get_inactive_group_disable_notifications(){
		$this->select_all_secure('investment_groups');
		$this->db->where($this->dx('last_group_activity').' < '.strtotime('-45 days',time()),NULL,FALSE);
		$this->db->where('('.$this->dx('disable_notifications').' ="0"  OR '.$this->dx('disable_notifications').' IS NULL )',NULL,FALSE);
		$this->db->order_by($this->dx('last_group_activity'),'DESC',FALSE);
		return $this->db->get('investment_groups')->result();
	}

	function group_registration_by_year(){
    	$this->db->select(array(
    		'COUNT(*) as total_count',
    		"DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')." ),'%Y') as year",
    		// $this->dx('subscription_status').' subscription_status',
    	));
    	$this->db->group_by(array(
    		'year',
    		// 'subscription_status',
    	));
    	$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
    	return $this->db->get('investment_groups')->result();
    }
}
?>

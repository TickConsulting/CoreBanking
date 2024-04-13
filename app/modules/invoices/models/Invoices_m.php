<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Invoices_m extends MY_Model {

	protected $_table = 'invoices';

	function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install(){
		$this->db->query("
		create table if not exists invoices(
			id int not null auto_increment primary key,
			`type` blob,
			`invoice_number` blob,
			`parent_id` blob,
			`user_id` blob,
			`member_id` blob,
			`contribution_id` blob,
			`fine_id` blob,
			`group_id` blob,
			`invoice_date` blob,
			`due_date` blob,
			`amount_payable` blob,
			`amount_paid` blob,
			`description` blob,
			`active` blob,
			`created_by` blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");

		$this->db->query("
		create table if not exists contribution_invoicing_queue(
			id int not null auto_increment primary key,
			`contribution_id` blob,
			`member_id` blob,
			`group_id` blob,
			`invoice_date` blob,
			`due_date` blob,
			`amount_payable` blob,
			`description` blob,
			created_on blob
		)");

		$this->db->query("
		create table if not exists contribution_fine_invoicing_queue(
			id int not null auto_increment primary key,
			`contribution_id` blob,
			`member_id` blob,
			`group_id` blob,
			`fine_date` blob,
			`due_date` blob,
			created_on blob
		)");
	}

	function insert($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('invoices',$input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'invoices',$input);
    }

    function update_where($where = "",$input = array()){
    	return $this->update_secure_where($where,'invoices',$input);
    }

	function insert_contribution_invoicing_queue($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('contribution_invoicing_queue',$input);
	}

	function insert_contribution_fine_invoicing_queue($input = array(),$skip_value = FALSE){
		return $this->insert_secure_data('contribution_fine_invoicing_queue',$input);
	}

	function get_queued_contribution_invoices($limit = 5){
		$this->select_all_secure('contribution_invoicing_queue');
		//$this->db->where($this->dx('group_id').' = 4 ',NULL,FALSE);

		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		return $this->db->get('contribution_invoicing_queue')->result();
	}

	function count_queued_contribution_invoices(){
		return $this->db->count_all_results('contribution_invoicing_queue');
	}

	function count_queued_contribution_fine_invoices(){
		return $this->db->count_all_results('contribution_fine_invoicing_queue');
	}

	function get_queued_contribution_fine_invoices($limit = 5,$group_id = 0){
		$this->select_all_secure('contribution_fine_invoicing_queue');
		if($group_id){
			$this->db->where($this->dx('group_id')." = '".$group_id."' ",NULL,FALSE);
		}
		$this->db->order_by($this->dx('created_on'),'DESC',FALSE);
		if($limit){
			$this->db->limit($limit);
		}
		return $this->db->get('contribution_fine_invoicing_queue')->result();
	}
	
	function delete_contribution_invoice_queue($id = 0){
		$this->db->where('id',$id);
		return $this->db->delete('contribution_invoicing_queue');
	}

	function delete_contribution_fine_invoice_queue($id = 0){
		$this->db->where('id',$id);
		return $this->db->delete('contribution_fine_invoicing_queue');
	}

	function get_invoices_sent_today_array($invoice_date = 0){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('user_id').' as user_id',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				$this->dx('group_id').' as group_id',
				$this->dx('invoice_date').' as invoice_date',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($invoice_date){
			//die;
        	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') = '" . date('Y jS F',$invoice_date) . "'", NULL, FALSE);
		}else{
        	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
		}
        $invoices = $this->db->get('invoices')->result();
        foreach ($invoices as $invoice){
        	# code...
        	$arr[$invoice->user_id][$invoice->member_id][$invoice->contribution_id][$invoice->group_id][$invoice->invoice_date] = $invoice->id;
        }
        return $arr;
	}

	function get_contribution_invoices_sent_array($invoice_dates_array = array()){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('user_id').' as user_id',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				$this->dx('group_id').' as group_id',
				$this->dx('invoice_date').' as invoice_date',
			)
		);
		$this->db->where($this->dx('type').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if(empty($invoice_dates_array)){
			//die;
        	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);

		}else{
        	$count = 1;
			$invoice_dates_list = "";
			foreach($invoice_dates_array as $key => $invoice_date):
				if($count==1){
					$invoice_dates_list .=  date('Y jS F',$invoice_date);
				}else{
					$invoice_dates_list .=  ",".date('Y jS F',$invoice_date);
				}
				$count++;
			endforeach;
        	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') IN('" . $invoice_dates_list . "')", NULL, FALSE);
		}
        $invoices = $this->db->get('invoices')->result();
        foreach ($invoices as $invoice){
        	# code...
        	$arr[$invoice->user_id][$invoice->member_id][$invoice->contribution_id][$invoice->group_id][$invoice->invoice_date] = $invoice->id;
        }
        return $arr;
	}

	function get_contribution_fine_invoices_sent_array($invoice_dates_array = array()){
		$arr = array();
		$this->db->select(
			array(
				'id',
				$this->dx('user_id').' as user_id',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				$this->dx('group_id').' as group_id',
				$this->dx('invoice_date').' as invoice_date',
			)
		);
		$this->db->where($this->dx('type').' = "2" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if(empty($invoice_dates_array)){
			//die;
        	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);

		}else{
        	$count = 1;
			$invoice_dates_list = "";
			foreach($invoice_dates_array as $key => $invoice_date):
				if($count==1){
					$invoice_dates_list .=  date('Y jS F',$invoice_date);
				}else{
					$invoice_dates_list .=  ",".date('Y jS F',$invoice_date);
				}
				$count++;
			endforeach;
        	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') IN('" . $invoice_dates_list . "')", NULL, FALSE);
		}
        $invoices = $this->db->get('invoices')->result();
        foreach ($invoices as $invoice){
        	# code...
        	$arr[$invoice->user_id][$invoice->member_id][$invoice->contribution_id][$invoice->group_id][$invoice->invoice_date] = $invoice->id;
        }
        return $arr;
	}

	function get_fine_invoices_sent_today_array($invoice_date = 0){
		$arr = array();
		//$this->select_all_secure('invoices')
		$this->db->select(
			array(
				'id',
				$this->dx('user_id').' as user_id',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				$this->dx('group_id').' as group_id',
				$this->dx('invoice_date').' as invoice_date',
				$this->dx('parent_id').' as parent_id',
			)
		);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($invoice_date){
			//die;
        	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') = '" . date('Y jS F',$invoice_date) . "'", NULL, FALSE);
		}else{
        	$this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('invoice_date')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
		}
        $invoices = $this->db->get('invoices')->result();
        foreach ($invoices as $invoice){
        	# code...
        	$arr[$invoice->user_id][$invoice->member_id][$invoice->contribution_id][$invoice->group_id][$invoice->invoice_date][$invoice->parent_id] = $invoice->id;
        }
        return $arr;
	}

	function get_invoices_by_ids($group_id=0,$ids =  array()){
		$this->select_all_secure('invoices');
		if(empty($ids)){
			$this->db->where('id'." IN (0) ",NULL,FALSE);
		}else{
			$this->db->where('id'." IN (".implode(",",$ids).") ",NULL,FALSE);
		}
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
	    $this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('invoice_date'),'DESC',FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_group_invoices($filter_parameters = array(),$group_id = 0){
		$this->select_all_secure('invoices');

		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('invoice_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('invoice_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['contributions']) && $filter_parameters['contributions']){
			$contribution_id_list = '0';
			$contributions = $filter_parameters['contributions'];
			$count = 1;
			foreach($contributions as $contribution_id){
				if($contribution_id){
					if($count==1){
						$contribution_id_list = $contribution_id;
					}else{
						$contribution_id_list .= ','.$contribution_id;
					}
					$count++;
				}
			}
			if($contribution_id_list){
        		$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['fine_categories']) && $filter_parameters['fine_categories']){
			$fine_category_list = '0';
			$fine_categories = $filter_parameters['fine_categories'];
			$count = 1;
			foreach($fine_categories as $fine_category_id){
				if($fine_category_id){
					if($count==1){
						$fine_category_list = $fine_category_id;
					}else{
						$fine_category_list .= ','.$fine_category_id;
					}
					$count++;
				}
			}
			if($fine_category_list){
        		$this->db->where($this->dx('fine_category_id').' IN ('.$fine_category_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['member_ids']) && $filter_parameters['member_ids']){
			if(isset($filter_parameters['member_ids']) && $filter_parameters['member_ids']){
				$member_list = '0';
				$members = $filter_parameters['member_ids'];
				$count = 1;
				foreach($members as $member_id){
					if($member_id){
						if($count==1){
							$member_list = $member_id;
						}else{
							$member_list .= ','.$member_id;
						}
						$count++;
					}
				}

				if($member_list){
	        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}

		if(isset($filter_parameters['type']) && $filter_parameters['type']){
			if($filter_parameters['type']==1){
				$this->db->where($this->dx('type')." = '1' ",NULL,FALSE);
			}else if($filter_parameters['type']==2){
				$this->db->where($this->dx('type')." = '2' ",NULL,FALSE);
			}else if($filter_parameters['type']==3){
				$this->db->where($this->dx('type')." = '3' ",NULL,FALSE);
			}else if($filter_parameters['type']==4){
				$this->db->where($this->dx('type')." = '4' ",NULL,FALSE);

			}
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('invoice_date'),'DESC',FALSE);
		return $this->db->get('invoices')->result();
	}
	
	function get_group_fine_invoices($filter_parameters = array(),$group_id = 0){
		$this->select_all_secure('invoices');
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('invoice_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('invoice_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
				$member_list = '0';
				$members = $filter_parameters['member_id'];
				$count = 1;
				foreach($members as $member_id){
					if($member_id){
						if($count==1){
							$member_list = $member_id;
						}else{
							$member_list .= ','.$member_id;
						}
						$count++;
					}
				}

				if($member_list){
	        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}

		if(isset($filter_parameters['contributions']) && $filter_parameters['contributions']){
			$contribution_id_list = '0';
			$contributions = $filter_parameters['contributions'];
			$count = 1;
			foreach($contributions as $contribution_id){
				if($contribution_id){
					if($count==1){
						$contribution_id_list = $contribution_id;
					}else{
						$contribution_id_list .= ','.$contribution_id;
					}
					$count++;
				}
			}
			if($contribution_id_list){
        		$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['fine_categories']) && $filter_parameters['fine_categories']){
			$fine_category_list = '0';
			$fine_categories = $filter_parameters['fine_categories'];
			$count = 1;
			foreach($fine_categories as $fine_category_id){
				if($fine_category_id){
					if($count==1){
						$fine_category_list = $fine_category_id;
					}else{
						$fine_category_list .= ','.$fine_category_id;
					}
					$count++;
				}
			}
			if($fine_category_list){
        		$this->db->where($this->dx('fine_category_id').' IN ('.$fine_category_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['type']) && $filter_parameters['type']){
			if($filter_parameters['type']=='2'){
				$this->db->where($this->dx('type')." = '2' ",NULL,FALSE);
			}else if($filter_parameters['type']=='3'){
				$this->db->where($this->dx('type')." = '3' ",NULL,FALSE);
			}else{
				$this->db->where($this->dx('type')." = 0 ",NULL,FALSE);
			}
		}else{
			$this->db->where($this->dx('type').' IN (2,3) ',NULL,FALSE);
		}
		
		//$this->db->where($this->dx('type').' IN (2,3) ',NULL,FALSE);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}		
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('invoice_date'),'DESC',FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_all_contribution_fine_invoices(){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('type').' IN (2) ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('invoice_date'),'DESC',FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_group_member_invoices($group_id=0,$member_id=0){
		$this->select_all_secure('invoices');
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('invoice_date'),'DESC',FALSE);
		return $this->db->get('invoices')->result();
	}
	
	function count_group_invoices($filter_parameters = array(),$group_id=0){
		
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('invoice_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('invoice_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['contributions']) && $filter_parameters['contributions']){
			$contribution_id_list = '0';
			$contributions = $filter_parameters['contributions'];
			$count = 1;
			foreach($contributions as $contribution_id){
				if($contribution_id){
					if($count==1){
						$contribution_id_list = $contribution_id;
					}else{
						$contribution_id_list .= ','.$contribution_id;
					}
					$count++;
				}
			}
			if($contribution_id_list){
        		$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['fine_categories']) && $filter_parameters['fine_categories']){
			$fine_category_list = '0';
			$fine_categories = $filter_parameters['fine_categories'];
			$count = 1;
			foreach($fine_categories as $fine_category_id){
				if($fine_category_id){
					if($count==1){
						$fine_category_list = $fine_category_id;
					}else{
						$fine_category_list .= ','.$fine_category_id;
					}
					$count++;
				}
			}
			if($fine_category_list){
        		$this->db->where($this->dx('fine_category_id').' IN ('.$fine_category_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['member_ids']) && $filter_parameters['member_ids']){
			if(isset($filter_parameters['member_ids']) && $filter_parameters['member_ids']){
				$member_list = '0';
				$members = $filter_parameters['member_ids'];
				$count = 1;
				foreach($members as $member_id){
					if($member_id){
						if($count==1){
							$member_list = $member_id;
						}else{
							$member_list .= ','.$member_id;
						}
						$count++;
					}
				}

				if($member_list){
	        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}

		if(isset($filter_parameters['type']) && $filter_parameters['type']){
			if($filter_parameters['type']==1){
				$this->db->where($this->dx('type')." = '1' ",NULL,FALSE);
			}else if($filter_parameters['type']==2){
				$this->db->where($this->dx('type')." = '2' ",NULL,FALSE);
			}else if($filter_parameters['type']==3){
				$this->db->where($this->dx('type')." = '3' ",NULL,FALSE);
			}else if($filter_parameters['type']==4){
				$this->db->where($this->dx('type')." = '4' ",NULL,FALSE);
			}
		}
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('invoices');
	}

	function count_group_fine_invoices($filter_parameters = array()){
		if(isset($filter_parameters['from']) && isset($filter_parameters['to'])){
			if($filter_parameters['from'] && $filter_parameters['to']){
				$this->db->where($this->dx('invoice_date').' >= "'.$filter_parameters['from'].'"',NULL,FALSE);
				$this->db->where($this->dx('invoice_date').' <= "'.$filter_parameters['to'].'"',NULL,FALSE);
			}
		}
		if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
			if(isset($filter_parameters['member_id']) && $filter_parameters['member_id']){
				$member_list = '0';
				$members = $filter_parameters['member_id'];
				$count = 1;
				foreach($members as $member_id){
					if($member_id){
						if($count==1){
							$member_list = $member_id;
						}else{
							$member_list .= ','.$member_id;
						}
						$count++;
					}
				}

				if($member_list){
	        		$this->db->where($this->dx('member_id').' IN ('.$member_list.')',NULL,FALSE);
				}
			}
		}

		if(isset($filter_parameters['contributions']) && $filter_parameters['contributions']){
			$contribution_id_list = '0';
			$contributions = $filter_parameters['contributions'];
			$count = 1;
			foreach($contributions as $contribution_id){
				if($contribution_id){
					if($count==1){
						$contribution_id_list = $contribution_id;
					}else{
						$contribution_id_list .= ','.$contribution_id;
					}
					$count++;
				}
			}
			if($contribution_id_list){
        		$this->db->where($this->dx('contribution_id').' IN ('.$contribution_id_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['fine_categories']) && $filter_parameters['fine_categories']){
			$fine_category_list = '0';
			$fine_categories = $filter_parameters['fine_categories'];
			$count = 1;
			foreach($fine_categories as $fine_category_id){
				if($fine_category_id){
					if($count==1){
						$fine_category_list = $fine_category_id;
					}else{
						$fine_category_list .= ','.$fine_category_id;
					}
					$count++;
				}
			}
			if($fine_category_list){
        		$this->db->where($this->dx('fine_category_id').' IN ('.$fine_category_list.')',NULL,FALSE);
			}
		}

		if(isset($filter_parameters['type']) && $filter_parameters['type']){
			if($filter_parameters['type']==2){
				$this->db->where($this->dx('type')." = 2 ",NULL,FALSE);
			}else if($filter_parameters['type']==2){
				$this->db->where($this->dx('type')." = 3 ",NULL,FALSE);
			}else{
				$this->db->where($this->dx('type')." = 0 ",NULL,FALSE);
			}
		}else{
			$this->db->where($this->dx('type').' IN (2,3) ',NULL,FALSE);
		}
		
		$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('invoices');
	}

	function count_group_member_invoices($group_id=0,$member_id=0){
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('member_id').' = "'.$this->member->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('invoices');
	}

	function get_invoices($group_id = 0,$from = 0,$to = 0){
		$this->select_all_secure('invoices');
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->order_by($this->dx('invoice_date'),'DESC',FALSE);
		return $this->db->get('invoices')->result();
	}
	
	function count_invoices($group_id = 0,$from = 0,$to = 0){
		if($from){
			$this->db->where($this->dx('created_on').' >= "'.$from.'"',NULL,FALSE);
		}
		if($to){
			$this->db->where($this->dx('created_on').' <= "'.$to.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('invoices');
	}

	function count_invoices_sent_today(){
        $this->db->where("DATE_FORMAT(FROM_UNIXTIME(".$this->dx('created_on')."),'%Y %D %M') = '" . date('Y jS F') . "'", NULL, FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->count_all_results('invoices');
	}

	function get_group_invoice($id = 0,$member_id=0,$group_id=0){
		$this->select_all_secure('invoices');
		$this->db->where('id',$id);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		if($member_id){
			$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		}
		return $this->db->get('invoices')->row();
	}

	function get($id = 0){
		$this->select_all_secure('invoices');
		$this->db->where('id',$id);
		return $this->db->get('invoices')->row();
	}

	function get_member_contribution_invoices_to_reconcile($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
			$date -=24*60*60;
			$this->db->where($this->dx('invoice_date').' > "'.$date.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('invoice_date'), 'ASC', FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_member_contribution_fine_invoices_to_revise($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
			$date -=24*60*60;
			$this->db->where($this->dx('invoice_date').' > "'.$date.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('invoice_date'), 'ASC', FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_member_contribution_fine_invoices_to_reconcile($group_id = 0,$member_id = 0,$contribution_id = 0,$date = 0){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' = "2"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
			$date -=24*60*60;
			$this->db->where($this->dx('invoice_date').' > "'.$date.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('invoice_date'), 'ASC', FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_member_fine_invoices_to_reconcile($group_id = 0,$member_id = 0,$fine_category_id = 0,$date = 0){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('fine_category_id').' = "'.$fine_category_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' = "3"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
			$date -=24*60*60;
			$this->db->where($this->dx('invoice_date').' > "'.$date.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('invoice_date'), 'ASC', FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_member_miscellaneous_invoices_to_reconcile($group_id = 0,$member_id = 0,$date = 0){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' = "4"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		if($date){
			$date -=24*60*60;
			$this->db->where($this->dx('invoice_date').' > "'.$date.'"',NULL,FALSE);
		}
        $this->db->order_by($this->dx('invoice_date'), 'ASC', FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_member_contribution_unpaid_invoices($group_id = 0,$member_id = 0,$contribution_id = 0,$fine_limit = 0,$date = 0,$percentage_fine_chargeable_on = 0,$fixed_fine_chargeable_on = 0){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('contribution_id').' = "'.$contribution_id.'"',NULL,FALSE);
		$this->db->where($this->dx('type').' = "1"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$this->db->where("(".$this->dx('amount_payable').' != '.$this->dx('amount_paid')." OR ".$this->dx('amount_paid')." IS NULL )",NULL,FALSE);
		if($date){
			if($percentage_fine_chargeable_on){
				$date -= (24*60*60*($percentage_fine_chargeable_on-1));
				$this->db->where($this->dx('due_date').' < "'.$date.'"',NULL,FALSE);
			}else if($fixed_fine_chargeable_on){
				$date -= (24*60*60*($fixed_fine_chargeable_on-1));
				$this->db->where($this->dx('due_date').' < "'.$date.'"',NULL,FALSE);
			}else{
				$this->db->where($this->dx('due_date').' < "'.$date.'"',NULL,FALSE);
			}
		}
		if($fine_limit){
			$this->db->limit($fine_limit);
		}
        $this->db->order_by($this->dx('invoice_date'), 'DESC', FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_group_member_invoices_to_void($member_id = 0,$group_id = 0){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->db->where($this->dx('member_id').' = "'.$member_id.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_group_member_total_contribution_back_dated_arrears_per_contribution_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
		$contribution_options = $this->contributions_m->get_group_contribution_options();
		foreach ($this->group_member_options as $member_id => $name){
			foreach($contribution_options as $contribution_id => $contribution_name){
				$arr[$member_id][$contribution_id] = 0;
			}
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount_payable').') as arrears',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (1) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id"),$this->dx("contribution_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$invoices = $this->db->get('invoices')->result();
		foreach($invoices as $invoice){
			$arr[$invoice->member_id][$invoice->contribution_id] = $invoice->arrears;
		}
		return $arr;
	}

	function get_group_member_total_fines_issued_back_dated_arrears_per_fine_category_array($group_id = 0,$to = 0,$from = 0){
		$arr = array();
		$fine_category = $this->fine_categories_m->get_group_back_dating_fine_category();
		foreach ($this->group_member_options as $member_id => $name){
			$arr[$member_id][$fine_category->id] = 0;
		}
		$this->db->select(
			array(
				'sum('.$this->dx('amount_payable').') as arrears',
				$this->dx('member_id').' as member_id',
				$this->dx('contribution_id').' as contribution_id',
				$this->dx('fine_category_id').' as fine_category_id',
			)
		);
		if($group_id){
			$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id').' = "'.$this->group->id.'"',NULL,FALSE);
		}
		$this->db->where($this->dx('type').' IN (2,3) ',NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		//$this->db->where('('.$this->dx('type').' = "1" OR '.$this->dx('type').' = "2" OR '.$this->dx('type').' = "3" OR '.$this->dx('type').' = "7" )',NULL,FALSE);
		$this->db->group_by(array($this->dx("member_id"),$this->dx("fine_category_id")));
		$this->db->where($this->dx('active').' = "1"',NULL,FALSE);
		$invoices = $this->db->get('invoices')->result();
		foreach($invoices as $invoice){
			$arr[$invoice->member_id][$invoice->fine_category_id] = $invoice->arrears;
		}
		return $arr;
	}

	function get_group_back_dating_contribution_invoices(){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (1) ',NULL,FALSE);
		$this->db->where($this->dx('group_id')." = ".$this->group->id." ",NULL,FALSE);
		return $this->db->get('invoices')->result();
	}

	function get_group_back_dating_fine_invoices(){
		$this->select_all_secure('invoices');
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('is_a_back_dating_record').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('type').' IN (2,3) ',NULL,FALSE);
		$this->db->where($this->dx('group_id')." = ".$this->group->id." ",NULL,FALSE);
		return $this->db->get('invoices')->result();
	}

	function update_group_back_dating_invoices_cut_off_date($group_id = 0,$input = array()){
		$where = " ".$this->dx('is_a_back_dating_record')." = '1' AND ".$this->dx('group_id')." = '".$group_id."' AND ".$this->dx('active')." = '1' ;";
        if($this->update_where($where,$input)){
        	return TRUE;
        }else{
        	return FALSE;
        }
	}

	function get_group_total_contribution_invoices_amount_payable(){
		$this->db->select(
			array(
				"SUM(".$this->dx('amount_payable').") as amount_payable "
			)
		);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('type').' IN (1) ',NULL,FALSE);
		$this->db->where($this->dx('group_id')." = ".$this->group->id." ",NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('invoices')->row()->amount_payable;
	}


	function get_group_total_fine_invoices_amount_payable(){
		$this->db->select(
			array(
				"SUM(".$this->dx('amount_payable').") as amount_payable "
			)
		);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
		$this->db->where($this->dx('type').' IN (2,3) ',NULL,FALSE);
		$this->db->where($this->dx('group_id')." = ".$this->group->id." ",NULL,FALSE);
		$this->db->limit(1);
		return $this->db->get('invoices')->row()->amount_payable;
	}

	function delete_group_queued_contribution_fine_invoices($group_ids = array()){
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." IN (0) ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN (".implode(",",$group_ids).") ",NULL,FALSE);

		}
		return $this->db->delete('contribution_fine_invoicing_queue');
	}

	function get_group_contribution_fine_queue_counts(){
		$this->db->select(
			array(
				'investment_groups.id as group_id ',
				$this->dx('investment_groups.name')." as group_name ",
				"COUNT(".$this->dx('group_id').") as count "
			)
		);
        $this->db->group_by(
        	array(
        		$this->dx("group_id")
        	)
        );
		$this->db->join('investment_groups',' investment_groups.id = '.$this->dx('contribution_fine_invoicing_queue.group_id'));
        $this->db->order_by('count', 'DESC', FALSE);
		return $this->db->get('contribution_fine_invoicing_queue')->result();
	}

	function delete_contribution_fine_invoicing_queue(){
		$this->db->where($this->dx('group_id')." !='4' ",NULL,FALSE);
		$this->db->delete('contribution_fine_invoicing_queue');
	}

	function void_group_invoices($group_id= 0 ,$invoices_id_array = array()){
    	if(empty($invoices_id_array)){
	    	$where = " id = 0 ;";
    	}else{
	    	$where = " id IN (".implode(',',array_filter($invoices_id_array)).") AND ".$this->dx('active')." = 1 ;";
    	}
		$input = array(
			'active' => 0,
			'modified_on' => time(),
		);
		$this->db->where($this->dx('group_id').' = "'.$group_id.'"',NULL,FALSE);
		$this->update_secure_where($where,'invoices',$input);
		return $this->db->affected_rows();
    }

	function void_group_contribution_fine_invoices($group_id = 0,$invoice_ids = array()){
		$input = array(
			'active' => 0,
			'modified_on' => time(),
		);
		$invoice_id_list = 0;
		if(empty($invoice_ids)){

		}else{
			$invoice_id_list = implode(',', $invoice_ids);
		}

        $where = " ".$this->dx('group_id')." = (".$group_id.") AND id IN (".$invoice_id_list.") ";
        return $this->update_secure_where($where,'invoices',$input); 
	}

	function get_group_member_contribution_fine_invoices_to_revise($group_ids = array(),$invoice_ids = array()){
		$this->select_all_secure('invoices');
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." IN (0) ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN (".implode(",",$group_ids).") ",NULL,FALSE);
		}
		if(empty($group_ids)){
			$this->db->where($this->dx('group_id')." IN (0) ",NULL,FALSE);
		}else{
			$this->db->where($this->dx('group_id')." IN (".implode(",",$group_ids).") ",NULL,FALSE);
		}
	}

}
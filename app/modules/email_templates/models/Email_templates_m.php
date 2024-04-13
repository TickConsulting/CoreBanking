<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Email_templates_m extends MY_Model {

	protected $_table = 'email_templates';

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	function install()
	{
		$this->db->query("
		create table if not exists email_templates(
					id int not null auto_increment primary key,
					`title` BLOB,
					`slug` BLOB,
					`description` BLOB,
					`content` BLOB,
					`active` BLOB,
					created_by BLOB,
					created_on BLOB,
					modified_on BLOB,
					modified_by BLOB
				)");
	}

	function insert($input,$skip_validation=FALSE)
	{
		return $this->insert_secure_data('email_templates',$input);
	}

	function get_all()
	{
		$this->select_all_secure('email_templates');
		$this->db->order_by($this->dx('modified_on'), 'DESC',FALSE);
		return $this->db->get('email_templates')->result();
	}
	
	function get_all_array()
	{
		$arr = array();
		$this->db->order_by('created_on', 'DESC');
		$email_templates = $this->db->get('email_templates')->result();
		foreach($email_templates as $email_template){
			$arr[$email_template->slug] = $email_template->title;
		}
		return $arr;
	}


	function get($id)
	{
		$this->select_all_secure('email_templates');
		$this->db->where(array('id' => $id));
		return $this->db->get('email_templates')->row();
	}

	function get_by_slug($slug,$id='')
	{
		$this->select_all_secure('email_templates');
		$this->db->where($this->dx('slug').'="'.$slug.'"',NULL,FALSE);
		if($id)
		{
			$this->db->where('id !=',$id);
		}
		return $this->db->get('email_templates')->row();
	}

	function get_many_by($params = array())
	{	
		$this->select_all_secure('email_templates');
		if (!empty($params['status']))
		{
			// If it's all, then show whatever the status
			if ($params['status'] != 'all')
			{
				// Otherwise, show only the specific status
				$this->db->where($this->dx('status').'="'.$params['status'].'"',NULL,FALSE);
			}
		}
		// Nothing mentioned, show live only (general frontend stuff)
		else
		{
			$this->db->where($this->dx('status').'="live"',NULL,FALSE);
		}
		// By default, dont show future email_templates

		if (!isset($params['show_future']) || (isset($params['show_future']) && $params['show_future'] == FALSE))
		{
			$this->db->where($this->dx('created_on').' <='.time(),NULL,FALSE);
		}

		// Limit the results based on 1 number or 2 (2nd is offset)

		//echo print_r($params); die;
		if (isset($params['limit']) && is_array($params['limit']))
			$this->db->limit($params['limit'][0], $params['limit'][1]);
		elseif (isset($params['limit']))

			$this->db->limit($params['limit']);
		return $this->get_all();

	}



	function count_all($params = array())
	{
		return $this->db->count_all_results('email_templates');
	}



	function update($id, $input,$skip_validation = false)
	{
		return $this->update_secure_data($id,'email_templates',$input);
	}

}

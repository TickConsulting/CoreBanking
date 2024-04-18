<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_menus_m extends MY_Model {

	protected $_table = 'bank_menus';

	protected $special_url_segments = array("/verify_ownership/","/edit/","/view/","/listing/",'/view_installments/',"/miscellaneous_statement/","/fine_statement/","/top_up/","/sell/","/connect/","/statement/");

	protected $notification_counts = array();

	protected $parent_id_as_key_link_as_value_multidimensional_array = array();

	protected $child_id_as_key_parent_id_as_value_array = array();

	protected $link_as_key_parent_id_as_value_array = array();

	protected $parent_id_as_key_menu_as_value_array = array();


	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->load->model('languages/languages_m');
		$this->install();
		$this->notification_counts = array(
			'UNRECONCILED_DEPOSITS_COUNT' => isset($this->unreconciled_deposits_count)?$this->unreconciled_deposits_count:'',
			'UNRECONCILED_WITHDRAWALS_COUNT' => isset($this->unreconciled_withdrawals_count)?$this->unreconciled_withdrawals_count:'',
			'UNRECONCILED_DEPOSITS_WITHDRAWALS' => isset($this->total_unreconciled_deposits_and_withdrawals_count)?$this->total_unreconciled_deposits_and_withdrawals_count:'',
			'WITHDRAWAL_TASKS_COUNT' => isset($this->withdrawal_tasks_count)?$this->withdrawal_tasks_count:'',
			'ACTIVE_LOAN_APPLICATIONS' => isset($this->active_loan_applications)?$this->active_loan_applications:'',
			'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT' => isset($this->pending_withdrawal_approval_requests_count)?$this->pending_withdrawal_approval_requests_count:'',
		);
		$this->load->library('billing_settings');

		$this->parent_id_as_key_link_as_value_multidimensional_array = $this->get_parent_id_as_key_url_as_value_multidimensional_array();
		$this->active_parent_id_as_key_link_as_value_multidimensional_array = $this->get_active_parent_id_as_key_url_as_value_multidimensional_array();
		$this->child_id_as_key_parent_id_as_value_array = $this->get_child_id_as_key_parent_id_as_value_array();
		$this->active_child_id_as_key_parent_id_as_value_array = $this->get_active_child_id_as_key_parent_id_as_value_array();
		$this->link_as_key_parent_id_as_value_array = $this->get_link_as_key_parent_id_as_value_array();
		$this->active_link_as_key_parent_id_as_value_array = $this->get_active_link_as_key_parent_id_as_value_array();
		$this->parent_id_as_key_menu_as_array = $this->get_parent_id_as_key_menu_as_value_array();
		$this->active_parent_id_as_key_menu_as_array = $this->get_active_parent_id_as_key_menu_as_value_array();
		
	}

	public function set_language($id){
		$language=$this->languages_m->get($this->user->language_id);
		$this->lang->load('application',$language->name);
	}

	public function get_active_parent_id_as_key_menu_as_value_array(){
		$arr = array();
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		$bank_menus = $this->db->get('bank_menus')->result();
		foreach($bank_menus as $menu):
			$arr[$menu->parent_id][] = $menu;
		endforeach;
		return $arr;
	}


	public function get_parent_id_as_key_menu_as_value_array(){
		$arr = array();
		$this->select_all_secure('bank_menus');
		//$this->db->where($this->db->where('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		$bank_menus = $this->db->get('bank_menus')->result();
		foreach($bank_menus as $menu):
			$arr[$menu->parent_id][] = $menu;
		endforeach;
		return $arr;
	}



	public function get_active_link_as_key_parent_id_as_value_array(){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('url')." as url ",
				$this->dx('parent_id')." as parent_id ",
			)
		);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);

		$bank_menus = $this->db->get('bank_menus')->result();
		foreach($bank_menus as $menu):
			$arr[$menu->url] = $menu->parent_id;
		endforeach;
		return $arr;
	}


	public function get_link_as_key_parent_id_as_value_array(){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('url')." as url ",
				$this->dx('parent_id')." as parent_id ",
			)
		);
		//$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		$bank_menus = $this->db->get('bank_menus')->result();
		foreach($bank_menus as $menu):
			$arr[$menu->url] = $menu->parent_id;
		endforeach;
		return $arr;
	}



	public function get_active_child_id_as_key_parent_id_as_value_array(){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('parent_id')." as parent_id ",
				" id ",
			)
		);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		$bank_menus = $this->db->get('bank_menus')->result();
		foreach($bank_menus as $menu):
			$arr[$menu->id] = $menu->parent_id;
		endforeach;
		return $arr;
	}



	public function get_parent_id_as_key_url_as_value_multidimensional_array(){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('parent_id')." as parent_id ",
				$this->dx('url')." as url ",
			)
		);

		//$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		$bank_menus = $this->db->get('bank_menus')->result();
		foreach($bank_menus as $menu):
			$arr[$menu->parent_id][] = $menu->url;
		endforeach;
		return $arr;
	}


	public function get_active_parent_id_as_key_url_as_value_multidimensional_array(){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('parent_id')." as parent_id ",
				$this->dx('url')." as url ",
			)
		);
		$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		$bank_menus = $this->db->get('bank_menus')->result();
		foreach($bank_menus as $menu):
			$arr[$menu->parent_id][] = $menu->url;
		endforeach;
		return $arr;
	}


	public function get_child_id_as_key_parent_id_as_value_array(){
		$arr = array();
		$this->db->select(
			array(
				$this->dx('parent_id')." as parent_id ",
				" id ",
			)
		);
		//$this->db->where($this->dx('active')." = '1' ",NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		$bank_menus = $this->db->get('bank_menus')->result();
		foreach($bank_menus as $menu):
			$arr[$menu->id] = $menu->parent_id;
		endforeach;
		return $arr;
	}


	

	public function install()
	{
		$this->db->query("
		create table if not exists bank_menus(
			id int not null auto_increment primary key,
			`parent_id` blob,
			`name` blob,
			`slug` blob,
			`url` blob,
			`language_key` blob,
			`description` blob,
			`help_url` blob,
			`icon` blob,
			`color` blob,
			`size` blob,
			`position` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	/**
		
		1. active
		2. inactive - hidden

	**/

	public function get($id = 0)
	{
		$this->select_all_secure('bank_menus');
		$this->db->where('id',$id);
		return $this->db->get('bank_menus')->row();
	}

	function insert($input = array(),$skip_value = FALSE)
	{
		return $this->insert_secure_data('bank_menus',$input);
	}

	function generate_page_title($url=''){ 
		
		if(empty($url))
		{
			$url = $this->uri->uri_string();
		}
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('url').'="'.$this->db->escape_str($url).'"',NULL,FALSE);

		$menu = $this->db->get('bank_menus')->row();

		if($menu){

			echo '<i class="'.$menu->icon.' font-dark"></i>';
			
			foreach ($this->notification_counts as $k => $v){
	            $name = preg_replace('/\['.$k.'\]/', '', $menu->name);
	        	if($name!==$menu->name){
	        		break;
	        	}
	        }
	        $slug = generate_menu_slug(trim($name));
	        $name = $this->lang->line($slug)?:$name;
			echo '<span class="caption-subject font-dark">'.ucwords($name).'</span>';

		}
		else
		{	
			$slug = generate_menu_slug('{group:template:title}');
	        $name = $this->lang->line($slug)?:'{group:template:title}';
			echo '<i class="fa fa-list-ul font-dark"></i>
			<span class="caption-subject font-dark">'.ucwords($name).'</span>';
		}
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'bank_menus',$input);
    }

	function get_all()
	{
		$this->select_all_secure('bank_menus');
		return $this->db->get('bank_menus')->result();
	}

	function count_all_active()
	{
		return $this->db->count_all_results('bank_menus');
	}

	function get_options(){
		$arr = array();
		$this->select_all_secure('bank_menus');
		$bank_menus = $this->db->get('bank_menus')->result();
		foreach($bank_menus as $menu){
			$arr[$menu->id] = $menu->name;
		}
		return $arr;
	}


	function get_parent_links(){
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('bank_menus')->result();	
	}

	function get_parent_link_by_url($url = ''){
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('url').' = "'.$url.'"',NULL,FALSE);
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
        $this->db->limit(1);
		return $this->db->get('bank_menus')->row();	
	}

	function get_active_parent_links(){
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('bank_menus')->result();	
	}

	function get_active_links_in_array($bank_menus_array=array()){
		$menu_id_list = '0';
		$count = 1;
		foreach($bank_menus_array as $menu_id){
			if($menu_id){
				if($count==1){
					$menu_id_list = $menu_id;
				}else{
					$menu_id_list .= ','.$menu_id;
				}
				$count++;
			}
		}
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
		if($menu_id_list){
    		$this->db->where('id IN ('.$menu_id_list.')',NULL,FALSE);
		}
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('bank_menus')->result();
	}


	function get_active_parent_link($parent_id){
		$this->select_all_secure('bank_menus');
		$this->db->where('id',$parent_id);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);

		return $this->db->get('bank_menus')->row();	
	}

	function get_children_links($parent_id = 0){
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('bank_menus')->result();	
	}

	function get_active_children_links($parent_id = 0){
		/*
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('bank_menus')->result();
		*/
		if(isset($this->active_parent_id_as_key_menu_as_array[$parent_id])){
			return $this->active_parent_id_as_key_menu_as_array[$parent_id];
		}else{
			return FALSE;
		}	
	}

	function has_children($parent_id = 0){
		/*
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		return $this->db->count_all_results('bank_menus')>0?TRUE:FALSE;
		*/
		if(in_array($parent_id,$this->child_id_as_key_parent_id_as_value_array)){
			return TRUE;
		}else{
			return FALSE;
		}	
	}

	function has_active_children($parent_id = 0){
		/*
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('bank_menus')>0?TRUE:FALSE;	
		**/
		if(in_array($parent_id,$this->active_child_id_as_key_parent_id_as_value_array)){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function has_active_grand_children($parent_id=0){
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('bank_menus')>0?TRUE:FALSE;
	}

	function remove_special_url_segments($url = '',$url_segments = array()){
		if(!empty($url_segments)&&$url){
			foreach ($url_segments as $url_segment) {
				$p = strpos($url,$url_segment);
				if ( $p!== false) {
					$url = substr($url,0,$p);
					//return once the first occurrence is found
					return $url.'/listing';
				}
			}
			//return current url if all fails
			return $url;
		}else{
			return '';
		}
	}

	function display_children($parent_id = 0){
		$links = $this->get_children_links($parent_id);	
		if(!empty($links)){
			echo '<ol class="dd-list">';
			foreach($links as $link):
				echo '<li class="dd-item" data-id="'.$link->id.'">
	                        <div class="dd-handle">
	                             '.$link->name.' ';
                                  echo $link->active==1?' - Active':' - Hidden'; 
	                        echo '</div>';
	                        $this->bank_menus_m->display_children($link->id); 
	            echo '</li>';
	        endforeach;
			echo '</ol>';
		}	
	}

	function generate_dashboard_menu(){
		$parent_links = $this->get_active_parent_links();
		if($parent_links){
			echo '<div class="tiles">';
			foreach($parent_links as $parent_link){
				if($this->has_active_children($parent_link->id)){
					$children_links = $this->get_active_children_links($parent_link->id);
					echo '
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<i class="'.$parent_link->icon.'"></i>'.$parent_link->name.'
							</div>
							<div class="tools">
								<a href="javascript:;" class="expand">
								</a>
							</div>
						</div>
						<div class="portlet-body">';
						foreach($children_links as $child_link){
							echo    '<a href="'.site_url($child_link->url).'">
										<div class="tile bg-'.$child_link->color.' '.$child_link->size.'">
											<div class="tile-body ">
												<i class="'.$child_link->icon.'"></i>
											</div>
											<div class="tile-object">
												<div class="name">
													'.$child_link->name.'
												</div>
												<div class="number">
												</div>
											</div>
										</div>
									</a>';
						}					
					echo '<div class="clearfix"></div></div> 
					</div>';
				}
			}
		}
		echo '</div>';
	}

	function get_active_link(){
		$active_url = $this->remove_special_url_segments(uri_string(),$this->special_url_segments);
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('url').' = "'.$active_url.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->get('bank_menus')->row();
	}

	function get_active_children_links_by_url(){
		$parent_menu = $this->get_active_link();
		if($parent_menu){
			return $this->get_active_children_links($parent_menu->id);
		}else{
			return false;
		}
	}

	function generate_dashboard_sub_menu(){
		$parent_link = $this->get_active_link();
		$children_links = $this->get_active_children_links_by_url();
		if($parent_link&&!empty($children_links)){
			$parents = array();
			if($this->all_active_children_have_active_grand_children($parent_link->id)){
				echo '<div class="tiles">';
					foreach($children_links as $child_link):
						if($this->has_active_children($child_link->id)){
							$grand_children_links = $this->get_active_children_links($child_link->id);
							echo '
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption">
										<i class="'.$child_link->icon.'"></i> '.$parent_link->name.' &raquo; '.$child_link->name.'
									</div>
									<div class="tools">
										<a href="javascript:;" class="expand">
										</a>
									</div>
								</div>
								<div class="portlet-body">';
								foreach($grand_children_links as $grand_child_link):
									echo '<a href="'.site_url($grand_child_link->url).'">
										<div class="tile bg-'.$grand_child_link->color.' '.$grand_child_link->size.'">
											<div class="tile-body ">
												<i class="'.$grand_child_link->icon.'"></i>
											</div>
											<div class="tile-object">
												<div class="name">
													'.$grand_child_link->name.'
												</div>
												<div class="number">
												</div>
											</div>
										</div>
									</a>';
								endforeach;
							echo '<div class="clearfix"></div></div> 
							</div>';
						}
					endforeach;
				echo '</div>';
			}else{
				echo '<div class="tiles">';
				echo '
							<div class="portlet light">
								<div class="portlet-title">
									<div class="caption">
										<i class="'.$parent_link->icon.'"></i>'.$parent_link->name.'
									</div>
									<div class="tools">
										<a href="javascript:;" class="expand">
										</a>
									</div>
								</div>
								<div class="portlet-body">';
								foreach($children_links as $child_link):
									if($this->has_active_children($child_link->id)){
										//keep an index of the children
									}else{
									echo '<a href="'.site_url($child_link->url).'">
											<div class="tile bg-'.$child_link->color.' '.$child_link->size.'">
												<div class="tile-body ">
													<i class="'.$child_link->icon.'"></i>
												</div>
												<div class="tile-object">
													<div class="name">
														'.$child_link->name.'
													</div>
													<div class="number">
													</div>
												</div>
											</div>
										</a>';
									}
								endforeach;
								echo '<div class="clearfix"></div></div> 
							</div>';

							foreach($children_links as $child_link):
								if($this->has_active_children($child_link->id)){
									$grand_children_links = $this->get_active_children_links($child_link->id);
									echo '
									<div class="portlet light">
										<div class="portlet-title">
											<div class="caption">
												<i class="'.$child_link->icon.'"></i> '.$parent_link->name.' '.$child_link->name.'
											</div>
											<div class="tools">
												<a href="javascript:;" class="expand">
												</a>
											</div>
										</div>
										<div class="portlet-body">';
										foreach($grand_children_links as $grand_child_link):
											echo '<a href="'.site_url($grand_child_link->url).'">
												<div class="tile bg-'.$grand_child_link->color.' '.$grand_child_link->size.'">
													<div class="tile-body ">
														<i class="'.$grand_child_link->icon.'"></i>
													</div>
													<div class="tile-object">
														<div class="name">
															'.$grand_child_link->name.'
														</div>
														<div class="number">
														</div>
													</div>
												</div>
											</a>';
										endforeach;
									echo '<div class="clearfix"></div></div> 
									</div>';
								}
							endforeach;
				echo '</div>';
			}
		}
	}

	function all_active_children_have_active_grand_children($parent_id = 0){
		$children_links = $this->get_active_children_links($parent_id);
		if($children_links){
			foreach ($children_links as $child_link) {
				if(!$this->has_active_children($child_link->id)){
					return false;
				}
			}
			return true;
		}else{
			false;
		}
	}

	function get_fellow_active_children_links($parent_id){
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('bank_menus')->result();
	}

	function generate_header_bank_menus(){
		$current_menu = $this->get_active_link();
		//find fellow children
		if($current_menu){
			$children_links  = $this->get_fellow_active_children_links($current_menu->parent_id);
			if($children_links){
				echo '<div class="actions">';
					foreach($children_links as $child_link){
						if($current_menu->id!==$child_link->id){
						echo '
							<a href="'.site_url($child_link->url).'" class="btn '.$child_link->color.' btn-sm">
								<i class="'.$child_link->icon.'"></i> '.$child_link->name.'
							</a>';
						}else{
							echo '
							<a href="'.site_url($child_link->url).'" class="btn green-meadow btn-sm">
								<i class="'.$child_link->icon.'"></i> '.$child_link->name.'
							</a>';
						}
					}
				echo '</div>';
			}
		}
	}

	function generate_module_dashboard_link(){
		$current_menu = $this->get_active_link();
		if($current_menu){
			$parent_link = $this->get_active_parent_link($current_menu->parent_id);
			if($parent_link){
				echo '
                <a href="'.site_url($parent_link->url).'" class="btn btn-sm blue">
                <i class="'.$parent_link->icon.'"></i> '.$parent_link->name.' Dashboard </a>';
			}else{
				echo '
                <a href="'.site_url('admin').'" class="btn btn-sm blue">
                <i class="icon-home"></i> Dashboard </a>';
			}
		}
	}

	function child_is_active($parent_id,$child_link_url){
		/*
		$child_link_url = $this->remove_special_url_segments($child_link_url,$this->special_url_segments);
		if($child_link_url){
			$this->db->select(array($this->dx('parent_id').' as parent_id '));
			$this->db->where($this->dx('url').' = "'.$child_link_url.'"',NULL,FALSE);
			$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
			$menu = $this->db->get('bank_menus')->row();
			if($menu){
				if($parent_id == $menu->parent_id){
					return TRUE;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
		*/
		$child_link_url = $this->remove_special_url_segments($child_link_url,$this->special_url_segments);
		if($child_link_url){
			if(isset($this->active_parent_id_as_key_link_as_value_multidimensional_array[$parent_id])){
				$arr = $this->active_parent_id_as_key_link_as_value_multidimensional_array[$parent_id];
				if(in_array($child_link_url,$arr)){
					return TRUE;
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function grand_child_is_active($parent_id,$grand_child_link_url){
		/*
		$grand_child_link_url = $this->remove_special_url_segments($grand_child_link_url,$this->special_url_segments);
		if($grand_child_link_url){
			$this->db->select(array($this->dx('parent_id').' as parent_id '));
			$this->db->where($this->dx('url').' = "'.$grand_child_link_url.'"',NULL,FALSE);
			$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
			$parent_menu = $this->db->get('bank_menus')->row();	
			if($parent_menu){
				$this->db->select(array($this->dx('parent_id').' as parent_id '));
				$this->db->where('id',$parent_menu->parent_id);
				$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
				$menu = $this->db->get('bank_menus')->row();
				if($menu){
					if($parent_id == $menu->parent_id){
						return TRUE;
					}else{
						return FALSE;
					}
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
		**/
		$grand_child_link_url = $this->remove_special_url_segments($grand_child_link_url,$this->special_url_segments);
		if($grand_child_link_url){
			if(isset($this->active_link_as_key_parent_id_as_value_array[$grand_child_link_url])){
				$menu_child_id = $this->active_link_as_key_parent_id_as_value_array[$grand_child_link_url];
				if(isset($this->active_child_id_as_key_parent_id_as_value_array[$menu_child_id])){
					$menu_id = $this->active_child_id_as_key_parent_id_as_value_array[$menu_child_id];
					if($menu_id == $parent_id){
						return TRUE;
					}else{
						return FALSE;
					}
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function great_grand_child_is_active($parent_id,$great_grand_child_link_url){
		/*
		$great_grand_child_link_url = $this->remove_special_url_segments($great_grand_child_link_url,$this->special_url_segments);
		if($great_grand_child_link_url)
		{
			$this->db->select(array($this->dx('parent_id').' as parent_id '));
			$this->db->where($this->dx('url').' = "'.$great_grand_child_link_url.'"',NULL,FALSE);
			$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
			$grand_child_menu = $this->db->get('bank_menus')->row();
			if($grand_child_menu)
			{
				$this->db->select(array($this->dx('parent_id').' as parent_id '));
				$this->db->where('id',$grand_child_menu->parent_id);
				$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
				$parent_menu = $this->db->get('bank_menus')->row();
				if($parent_menu)
				{
					$this->db->select(array($this->dx('parent_id').' as parent_id '));
					$this->db->where('id',$parent_menu->parent_id);
					$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
					$menu = $this->db->get('bank_menus')->row();
					if($menu)
					{
						if($parent_id == $menu->parent_id)
						{
							return TRUE;
						}
						else
						{
							return FALSE;
						}
					}
					else
					{
						return FALSE;
					}
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}*/
		$great_grand_child_link_url = $this->remove_special_url_segments($great_grand_child_link_url,$this->special_url_segments);
		if($great_grand_child_link_url){
			if(isset($this->active_link_as_key_parent_id_as_value_array[$great_grand_child_link_url])){
				$menu_grand_child_id = $this->active_link_as_key_parent_id_as_value_array[$great_grand_child_link_url];
				if(isset($this->active_child_id_as_key_parent_id_as_value_array[$menu_grand_child_id])){
					$menu_child_id = $this->active_child_id_as_key_parent_id_as_value_array[$menu_grand_child_id];
					if(isset($this->active_child_id_as_key_parent_id_as_value_array[$menu_child_id])){
						$menu_id = $this->active_child_id_as_key_parent_id_as_value_array[$menu_child_id];
						if($menu_id == $parent_id){
							return TRUE;
						}else{
							return FALSE;
						}
					}else{
						return FALSE;
					}
				}else{
					return FALSE;
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function has_active_parent($menu_id=0){
		$menu = $this->get($menu_id);
		if($menu){
			if($menu->parent_id){
				return $menu->parent_id;
			}else{
				return FALSE;
			}
		}
	}


	function generate_side_bar_menu(){
		///$this->set_language($this->user->language_id);
		$this->notification_counts = array(
			'UNRECONCILED_DEPOSITS_COUNT' => $this->unreconciled_deposits_count,
			'UNRECONCILED_WITHDRAWALS_COUNT' => $this->unreconciled_withdrawals_count,
			'UNRECONCILED_DEPOSITS_WITHDRAWALS' => $this->total_unreconciled_deposits_and_withdrawals_count,
			'ACTIVE_LOAN_APPLICATIONS' => $this->active_loan_applications,
			'WITHDRAWAL_TASKS_COUNT' => $this->withdrawal_tasks_count,
			'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT' => $this->pending_withdrawal_approval_requests_count,
		);
		if($this->member_role_permissions){
			$bank_menus_array=array();
			foreach ($this->member_role_permissions as $permission) {

				$bank_menus_array[$permission->menu_id] = $permission->menu_id;
				$parent_menu_id = isset($this->active_child_id_as_key_parent_id_as_value_array[$permission->menu_id])?$this->active_child_id_as_key_parent_id_as_value_array[$permission->menu_id]:'';
				if(in_array($parent_menu_id,$bank_menus_array)){

				}else{
					$bank_menus_array[$parent_menu_id] = $parent_menu_id;
				}
			}
			$string = 'bank';
			$dashboard_id = $this->get_dashboard_id();
			$bank_menus_array=array($dashboard_id=>$dashboard_id)+$bank_menus_array;
			if($bank_menus_array){
				$parent_new = array();
				foreach ($bank_menus_array as $menu_id) {
					$link = $this->get($menu_id);
					if($link && $link->parent_id==0){
						$parent_new[] = $link;
					}else{
						//highest parent;
						/*$parent = $this->get($link->parent_id);
						if($parent->parent_id!=0){
							$add_menu = $this->get($parent->id);
							$bank_menus_array = $bank_menus_array + array($add_menu->id=>$add_menu->id);
							if($parent->parent_id==0){
								$parent=$add_menu;
							}else{
								$add_menu2 = $this->get($add_menu->parent_id);
								if($add_menu2->parent_id==0){
									$parent = $add_menu2;
								}else{
									$add_menu3 = $this->get($add_menu2->parent_id);
									if($add_menu3->parent_id==0){
										$parent = $add_menu3;
									}
								}
							}
						}
						$parent_new[] = $parent;*/
					}
				}
				$value = array();
				foreach ($parent_new as $parent_new_value) {
					$value[$parent_new_value->id] = $parent_new_value;
				}
				if($value){
					echo '<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
					';
					foreach ($value as $parent) {
						$href = $parent->url?site_url($parent->url):'javascript:;';
						if(preg_match('/javascript/', $href)){
							$href = 'javascript:;';
						}
						$show = 1;
						$specific = 0;
					
						if($specific){
							if($show){
								if($this->billing_settings->menu_acceptable_for_package($this->group->billing_package_id,$parent->id)){
									echo '<li class="';
									if(uri_string()==$parent->url&&$parent->url!==''){
										echo 'active';
									}
									if($this->child_is_active($parent->id,uri_string())||$this->grand_child_is_active($parent->id,uri_string())||$this->great_grand_child_is_active($parent->id,uri_string())){
										echo 'active start open';
									}
									echo '">
										<a href="'.$href.'">
										<i class="'.$parent->icon.'"></i>';
										foreach ($this->notification_counts as $k => $v){
											$count_class = "";
											if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
												$count_class = " deposits_count ";
											}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
												$count_class = " withdrawals_count ";
											}else if($k == 'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT'){
												$count_class = " withdrawal_approval_count ";
											}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
												$count_class = " withdrawals_count ";
											}

											if($v){
									            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $parent->name);
									        	if($name!==$parent->name){
									        		break;
									        	}
								        	}else{
									        	$name = preg_replace('/\['.$k.'\]/', '<span style="display:none;" class="badge badge-info '.$count_class.' ">'.$v.'</span>', $parent->name);
									        	if($name!==$parent->name){
									        		break;
									        	}
									        }
								        }
										echo '<span class="title">&nbsp;'.$name.'</span>';
										if($this->has_children($parent->id)){
										    echo '<span class="arrow "></span>';	
										}
										echo '</a>';
										$this->generate_side_bar_sub_menu($parent->id,$bank_menus_array,TRUE);
									echo'</li>';
								}
							}
						}else{
							if($this->billing_settings->menu_acceptable_for_package($this->group->billing_package_id,$parent->id)){
								echo '<li class="';
								if(uri_string()==$parent->url&&$parent->url!==''){
									echo 'active';
								}
								if($this->child_is_active($parent->id,uri_string())||$this->grand_child_is_active($parent->id,uri_string())||$this->great_grand_child_is_active($parent->id,uri_string())){
									echo 'active start open';
								}
								echo '">
									<a href="'.$href.'">
									<i class="'.$parent->icon.'"></i>';
									foreach ($this->notification_counts as $k => $v){
										$count_class = "";
										if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
											$count_class = " deposits_count ";
										}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
											$count_class = " withdrawals_count ";
										}else if($k == 'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT'){
											$count_class = " withdrawal_approval_count ";
										}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
											$count_class = " withdrawals_count ";
										}

										if($v){
								            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $parent->name);
								        	if($name!==$parent->name){
								        		break;
								        	}
							        	}else{
								        	$name = preg_replace('/\['.$k.'\]/', '<span style="display:none;" class="badge badge-info '.$count_class.' ">'.$v.'</span>', $parent->name);
								        	if($name!==$parent->name){
								        		break;
								        	}
								        }
							        }
									echo '<span class="title">&nbsp;'.$name.'</span>';
									if($this->has_children($parent->id)){
									    echo '<span class="arrow "></span>';	
									}
									echo '</a>';
									$this->generate_side_bar_sub_menu($parent->id,$bank_menus_array,TRUE);
								echo'</li>';
							}
						}
					}
					echo '</ul>';
				}
			}
		}else{
			$parent_links = $this->get_active_parent_links();
			if($parent_links){
				echo '<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="false" data-slide-speed="200">
					';
				foreach($parent_links as $link){
					$href = $link->url?site_url($link->url):'javascript:;';
					if(preg_match('/javascript/', $href)){
						$href = 'javascript:;';
					}
					//if($this->billing_settings->menu_acceptable_for_package($this->group->billing_package_id,$link->id)){
					if(TRUE){
						$show = 1;
						$specific = 0;
						if($specific){
							if($show){
								echo'	
									<li class="';
									if(uri_string()==$link->url&&$link->url!==''){
										echo 'active';
									}
									if($this->child_is_active($link->id,uri_string())||$this->grand_child_is_active($link->id,uri_string())||$this->great_grand_child_is_active($link->id,uri_string())){
										echo 'active start open';
									}
									echo '">
										<a href="'.$href.'">
										<i class="'.$link->icon.'"></i>';

										$menu_name = $link->name;
										$menu_name_array = explode('[',$menu_name);
										if(isset($menu_name_array[0])&&isset($menu_name_array[1])){
											if($link->language_key){
												if($new_menu_name = $this->lang->line($link->language_key)){
													$menu_name = $new_menu_name.' ['.$menu_name_array[1];
												}
											}
										}else{
											if($link->language_key){
												if($new_menu_name = $this->lang->line($link->language_key)){
													$menu_name = $new_menu_name;
												}
											}
										}
										foreach ($this->notification_counts as $k => $v){
											$count_class = "";
											if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
												$count_class = " deposits_count ";
											}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
												$count_class = " withdrawals_count ";
											}else if($k == 'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT'){
												$count_class = " withdrawal_approval_count ";
											}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
												$count_class = " withdrawals_count ";
											}
											if($v){
									            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $menu_name);
									        	if($name!==$menu_name){
									        		break;
									        	}
								        	}else{
									        	$name = preg_replace('/\['.$k.'\]/', '<span style="display:none;" class="badge badge-info '.$count_class.' ">'.$v.'</span>', $menu_name);
									        	if($name!==$menu_name){
									        		break;
									        	}
									        }
								        }

										echo '<span class="title">&nbsp;'.$name.'</span>';
										if($this->has_children($link->id)){
										    echo '<span class="arrow "></span>';	
										}
										echo '</a>';
										$this->generate_side_bar_sub_menu($link->id);
								echo'</li>
								';
							}else{
							}
						}else{
							echo'	
								<li class="';
								if(uri_string()==$link->url&&$link->url!==''){
									echo 'active';
								}
								if($this->child_is_active($link->id,uri_string())||$this->grand_child_is_active($link->id,uri_string())||$this->great_grand_child_is_active($link->id,uri_string())){
									echo 'active start open';
								}
								echo '">
									<a href="'.$href.'">
									<i class="'.$link->icon.'"></i>';

									$menu_name = $link->name;
									$menu_name_array = explode('[',$menu_name);
									if(isset($menu_name_array[0])&&isset($menu_name_array[1])){
										if($link->language_key){
											if($new_menu_name = $this->lang->line($link->language_key)){
												$menu_name = $new_menu_name.' ['.$menu_name_array[1];
											}
										}
									}else{
										if($link->language_key){
											if($new_menu_name = $this->lang->line($link->language_key)){
												$menu_name = $new_menu_name;
											}
										}
									}
									foreach ($this->notification_counts as $k => $v){
										$count_class = "";
										if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
											$count_class = " deposits_count ";
										}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
											$count_class = " withdrawals_count ";
										}else if($k == 'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT'){
											$count_class = " withdrawal_approval_count ";
										}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
											$count_class = " withdrawals_count ";
										}
										if($v){
								            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $menu_name);
								        	if($name!==$menu_name){
								        		break;
								        	}
							        	}else{
								        	$name = preg_replace('/\['.$k.'\]/', '<span style="display:none;" class="badge badge-info '.$count_class.' ">'.$v.'</span>', $menu_name);
								        	if($name!==$menu_name){
								        		break;
								        	}
								        }
							        }

									echo '<span class="title">&nbsp;'.$name.'</span>';
									if($this->has_children($link->id)){
									    echo '<span class="arrow "></span>';	
									}
									echo '</a>';
									$this->generate_side_bar_sub_menu($link->id);
							echo'</li>
							';
						}
					}
				}
				echo '</ul>';
			}
		}
	}

	function generate_side_bar_sub_menu($parent_id = 0,$acceptable_children=array(),$check_children=FALSE){
		$children_links = $this->get_active_children_links($parent_id);
		if($children_links){
			echo '<ul class="sub-menu">';
			foreach($children_links as $child_link){

				if($this->group->subscription_status == '5'){
					if(!preg_match('/list/', strtolower($child_link->name))){
						continue;
					}
				}

				if($check_children):
					$show = 1;
					$specific = 0;
					

					if($specific){
						if($show){
							if(in_array($child_link->id, $acceptable_children)):
								$href = $child_link->url?site_url($child_link->url):'javascript:;';
								if(preg_match('/javascript/', $href)){
									$href = 'javascript:;';
								}
								if($this->billing_settings->menu_acceptable_for_package($this->group->billing_package_id,$child_link->id)):
									echo '
									<li class="';
									if(uri_string()==$child_link->url&&$child_link->url!==''){
										echo 'active';
									}
									if($this->child_is_active($child_link->id,uri_string())||$this->grand_child_is_active($child_link->id,uri_string())){
										echo 'active open';
									}
									echo '">
										<a href="'.$href.'">';
										if($this->has_children($child_link->id)){
										    echo '<span class="arrow "></span>';	
										}
										
										foreach ($this->notification_counts as $k => $v){
											$count_class = "";
											if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
												$count_class = " deposits_count ";
											}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
												$count_class = " withdrawals_count ";
											}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
												$count_class = " withdrawals_count ";
											}
											if($v){
									            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $child_link->name);
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }else{
									            $name = preg_replace('/\['.$k.'\]/', '<span style="display:none;"  class="badge badge-info '.$count_class.' ">'.$v.'</span>', $child_link->name);
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }
								        }
								        echo $name;
																
										echo '</a>';
										$this->generate_side_bar_sub_menu($child_link->id,$acceptable_children,$check_children);
									echo '</li>';
								else:
									$this->generate_side_bar_sub_menu($child_link->id,$acceptable_children,$check_children);
								endif;
							endif;
						}
					}else{
						if(in_array($child_link->id, $acceptable_children)):
							$href = $child_link->url?site_url($child_link->url):'javascript:;';
							if(preg_match('/javascript/', $href)){
								$href = 'javascript:;';
							}
							if($this->billing_settings->menu_acceptable_for_package($this->group->billing_package_id,$child_link->id)):
								echo '
								<li class="';
								if(uri_string()==$child_link->url&&$child_link->url!==''){
									echo 'active';
								}
								if($this->child_is_active($child_link->id,uri_string())||$this->grand_child_is_active($child_link->id,uri_string())){
									echo 'active open';
								}
								echo '">
									<a href="'.$href.'">';
									if($this->has_children($child_link->id)){
									    echo '<span class="arrow "></span>';	
									}

									foreach ($this->notification_counts as $k => $v){
										$count_class = "";
										if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
											$count_class = " deposits_count ";
										}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
											$count_class = " withdrawals_count ";
										}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
											$count_class = " withdrawals_count ";
										}
										if($v){
								            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $child_link->name);
								        	if($name!==$child_link->name){
								        		break;
								        	}
								        }else{
								            $name = preg_replace('/\['.$k.'\]/', '<span style="display:none;"  class="badge badge-info '.$count_class.' ">'.$v.'</span>', $child_link->name);
								        	if($name!==$child_link->name){
								        		break;
								        	}
								        }
							        }
							        echo $name;
															
									echo '</a>';
									$this->generate_side_bar_sub_menu($child_link->id,$acceptable_children,$check_children);
								echo '</li>';
							else:
								$this->generate_side_bar_sub_menu($child_link->id,$acceptable_children,$check_children);
							endif;
						endif;
					}
					
				else:
					$href = $child_link->url?site_url($child_link->url):'javascript:;';
					if(preg_match('/javascript/', $href)){
						$href = 'javascript:;';
					}
					//if($this->billing_settings->menu_acceptable_for_package($this->group->billing_package_id,$child_link->id)):
					if(TRUE):
						$show = 1;
						$specific = 0;
						
						if($specific){
							if($show){
								echo '
									<li class="';
									if(uri_string()==$child_link->url&&$child_link->url!==''){
										echo 'active';
									}
									if($this->child_is_active($child_link->id,uri_string())||$this->grand_child_is_active($child_link->id,uri_string())){
										echo 'active open';
									}
									echo '">
										<a href="'.$href.'">';
										if($this->has_children($child_link->id)){
										    echo '<span class="arrow "></span>';	
										}
										$sub_menu_name = $child_link->name;
											$sub_menu_name_array = explode('[',$sub_menu_name);
											if(isset($sub_menu_name_array[0])&&isset($sub_menu_name_array[1])){

												if($child_link->language_key){
													if($sub_menu_name1 = $this->lang->line($child_link->language_key)){
														// we have the name
														$child_link->name = $sub_menu_name1.'['.$sub_menu_name_array[1];
													}
												}
											}else{
												if($child_link->language_key){
													if($sub_menu_name1 = $this->lang->line($child_link->language_key)){
														// we have the name
														$child_link->name = $sub_menu_name1;
													}
												}
											}
										foreach ($this->notification_counts as $k => $v){
											$count_class = "";
											if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
												$count_class = " deposits_count ";
											}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
												$count_class = " withdrawals_count ";
											}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
												$count_class = " withdrawals_count ";
											}
											if($v){
									            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $child_link->name);
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }else{
										        $name = preg_replace('/\['.$k.'\]/', '<span style="display:none;"  class="badge badge-info '.$count_class.' ">'.$v.'</span>', $child_link->name);
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }
								        }
								        echo $name;
																
										echo '</a>';
										$this->generate_side_bar_sub_menu($child_link->id);
									echo '</li>';
							}else{

							}
						}else{
							echo '
								<li class="';
								if(uri_string()==$child_link->url&&$child_link->url!==''){
									echo 'active';
								}
								if($this->child_is_active($child_link->id,uri_string())||$this->grand_child_is_active($child_link->id,uri_string())){
									echo 'active open';
								}
								echo '">
									<a href="'.$href.'">';
									if($this->has_children($child_link->id)){
									    echo '<span class="arrow "></span>';	
									}
									$sub_menu_name = $child_link->name;
										$sub_menu_name_array = explode('[',$sub_menu_name);
										if(isset($sub_menu_name_array[0])&&isset($sub_menu_name_array[1])){

											if($child_link->language_key){
												if($sub_menu_name1 = $this->lang->line($child_link->language_key)){
													// we have the name
													$child_link->name = $sub_menu_name1.'['.$sub_menu_name_array[1];
												}
											}
										}else{
											if($child_link->language_key){
												if($sub_menu_name1 = $this->lang->line($child_link->language_key)){
													// we have the name
													$child_link->name = $sub_menu_name1;
												}
											}
										}
									foreach ($this->notification_counts as $k => $v){
										$count_class = "";
										if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
											$count_class = " deposits_count ";
										}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
											$count_class = " withdrawals_count ";
										}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
											$count_class = " withdrawals_count ";
										}
										if($v){
								            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $child_link->name);
								        	if($name!==$child_link->name){
								        		break;
								        	}
								        }else{
									        $name = preg_replace('/\['.$k.'\]/', '<span style="display:none;"  class="badge badge-info '.$count_class.' ">'.$v.'</span>', $child_link->name);
								        	if($name!==$child_link->name){
								        		break;
								        	}
								        }
							        }
							        echo $name;
															
									echo '</a>';
									$this->generate_side_bar_sub_menu($child_link->id);
								echo '</li>';
						}
					else:
						$this->generate_side_bar_sub_menu($child_link->id);
					endif;
				endif;
			}
			echo '</ul>';
		}
	}

	function get_menu_by_link_url($link_url='')
	{
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('url').' = "'.$link_url.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $menu = $this->db->get('bank_menus')->row();
	}

	function get_current_url_id($link_url=''){
		$this->db->select(array('id as id'));
		$this->db->where($this->dx('url').' = "'.$link_url.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$menu = $this->db->get('bank_menus')->row();
		if($menu){
			return $menu->id;
		}else{
			return FALSE;
		}
	}

	function get_dashboard_id(){
		$this->db->select(array('id as id'));
		$this->db->where($this->dx('name').' = "Dashboard"',NULL,FALSE);
		$this->db->where($this->dx('parent_id').'="0"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$menu = $this->db->get('bank_menus')->row();
		if($menu){
			return $menu->id;
		}else{
			return FALSE;
		}
	}

	function generate_page_quick_action_bank_menus(){
		$link_url = uri_string();
		$menu = $this->get_menu_by_link_url($link_url);
		echo '<div class="actions">
			<a href="'.site_url('group/settings').'" class="btn blue btn-sm" > 
	            <i class="fa fa-cogs"></i>'.
	            ($this->lang->line("settings")?:"Settings").
	        '.</a>
		';
		if($menu){
			echo '
            <div class="btn-group">
                
                <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> '.
                	($this->lang->line('quick_actions')?:"Quick Links").'
                    <i class="fa fa-angle-down"></i>
                </button>

               	<ul class="dropdown-menu dropdown-content pull-right hold-on-click" role="menu">';
			
			$parent_id = $menu->parent_id;

			$parent_menu = $this->get($parent_id);
			$childrens = $this->get_children_links($parent_id);
			if($childrens)
			{
					foreach ($childrens as $child) {
						if(preg_match('/]/',$child->name)){
							$name = '';
							foreach ($this->notification_counts as $k => $v){
					            $name = preg_replace('/\['.$k.'\]/', '', $child->name);
					            $child->name =$name; 
					        	if($name!==$child->name){
					        		break;
					        	}

					        }
						}

						$show = 1;
						$specific = 0;
					

						if($specific){
							if($show){
								$href = $child->url?site_url($child->url):'javascript:;';
								if(preg_match('/javascript/', $href)){
									$href = 'javascript:;';
								}
								$menu_name = $child->name;
								if($child->language_key){
									if($new_menu_name = $this->lang->line($child->language_key)){
										$menu_name = $new_menu_name;
									}
								}
								echo '<li>
		                                <a href="'.$href.'">
		                                    <i class="'.$child->icon.'"></i> '.$menu_name.'</a>';

		                                    $children_menu = $this->get_active_children_links($child->id);
		                                    if($children_menu)
		                                    {
		                                    	echo '<li class="divider"> </li>';
		                                    	foreach ($children_menu as $child_menu) {
		                                    		$child_href = $child_menu->url?site_url($child_menu->url):'javascript:;';
		                                    		if(preg_match('/javascript/', $child_href)){
														$child_href = 'javascript:;';
													}
													$child_menu_name = $child_menu->name;
													if($child_menu_name->language_key){
														if($child_new_menu_name = $this->lang->line($child_menu_name->language_key)){
														}
													}
		                                    		echo '<li class="child-menu">
							                                <a href="'.$child_href.'">
							                                    <i class="'.$child_menu->icon.'"></i> '.$child_new_menu_name.'</a>
							                             </li>';
		                                    		
		                                    	}
		                                    }

		                        echo '</li>';
							}else{

							}
						}else{
							$href = $child->url?site_url($child->url):'javascript:;';
							if(preg_match('/javascript/', $href)){
								$href = 'javascript:;';
							}
							$menu_name = $child->name;
							if($child->language_key){
								if($new_menu_name = $this->lang->line($child->language_key)){
									$menu_name = $new_menu_name;
								}
							}
							echo '<li>
	                                <a href="'.$href.'">
	                                    <i class="'.$child->icon.'"></i> '.$menu_name.'</a>';

	                                    $children_menu = $this->get_active_children_links($child->id);
	                                    if($children_menu)
	                                    {
	                                    	echo '<li class="divider"> </li>';
	                                    	foreach ($children_menu as $child_menu) {
	                                    		$child_href = $child_menu->url?site_url($child_menu->url):'javascript:;';
	                                    		if(preg_match('/javascript/', $child_href)){
													$child_href = 'javascript:;';
												}
												$child_menu_name = $this->lang->line($child_menu_name->language_key)?:$child_menu->name;
	                                    		echo '<li class="child-menu">
						                                <a href="'.$child_href.'">
						                                    <i class="'.$child_menu->icon.'"></i> '.$child_menu_name.'</a>
						                             </li>';
	                                    		
	                                    	}
	                                    }

	                        echo '</li>';
						}
					} 
					echo '<li class="divider"> </li>';

			}
			$help_url = $menu->help_url?'https://help.chamasoft.com/'.$menu->help_url:'javascript:;';
			if(preg_match('/javascript/', $help_url)){
				$help_url = 'javascript:;';
			}
            echo '<li>
                    <a href="'.$help_url.'" target="_blank">
                        <i class="fa fa-ambulance"></i> '.($this->lang->line('help')?:'Help').'</a>
                </li>';
			echo '</ul>
            	</div>
               ';
		}
		else
		{
			$link_url = uri_string();
			foreach ($this->special_url_segments as $key => $value){
	             $segment = explode('/', $value);
	             if(preg_match('/'.$segment[0].'\/'.$segment[1].'/', $link_url))
	             {
	               $link_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
	               $menu = $this->get_menu_by_link_url($link_url)?:$this->get_menu_by_link_url($link_url.'listing');
	               if($menu){
	               		echo '
				            <div class="btn-group">
				                <a class="btn btn-sm green dropdown-toggle" href="javascript:;" data-toggle="dropdown"> Actions
				                    <i class="fa fa-angle-down"></i>
				                </a>

				               	<ul class="dropdown-menu pull-right">';

							$childrens = $this->get_children_links($menu->parent_id);
							if($childrens)
							{
									foreach ($childrens as $child) {
										$href = $child->url?site_url($child->url):'javascript:;';
										if(preg_match('/javascript/', $href)){
											$href = 'javascript:;';
										}
										$menu_name = $child->name;
										if($child->language_key){
											if($new_menu_name = $this->lang->line($child->language_key)){
												$menu_name = $new_menu_name;
											}
										}
										echo '<li>
				                                <a href="'.$href.'">
				                                    <i class="'.$child->icon.'"></i> '.$menu_name.'</a>';
				                                    $children_menu = $this->get_active_children_links($child->id);
				                                    if($children_menu)
				                                    {
				                                    	echo '<li class="divider"> </li>';
				                                    	foreach ($children_menu as $child_menu) {
				                                    		$child_href = $child_menu->url?site_url($child_menu->url):'javascript:;';
				                                    		if(preg_match('/javascript/', $child_href)){
																$child_href = 'javascript:;';
															}
															$child_menu_name = $child_menu->name;
															if($child_menu_name->language_key){
																if($child_new_menu_name = $this->lang->line($child_menu_name->language_key)){
																}
															}
				                                    		echo '<li class="child-menu">
									                                <a href="'.$child_href.'">
									                                    <i class="'.$child_menu->icon.'"></i> '.$child_new_menu_name.'</a>
									                             </li>';
				                                    		
				                                    	}
				                                    }
				                        echo '</li>';
									} 
									echo '<li class="divider"> </li>';

							}
							$help_url = 'javascript:;';
							if($menu)
							{
								$help_url = $menu->help_url?'https://help.chamasoft.com/'.$menu->help_url:'javascript:;';
								if(preg_match('/javascript/', $help_url)){
									$help_url = 'javascript:;';
								}
							}
				            echo '<li>
				                    <a href="'.$help_url.'" target="_blank">
				                        <i class="fa fa-ambulance"></i> '.($this->lang->line('help')?:'Help').'</a>
				                </li>';
							echo '</ul>
				            	</div>
				               ';
				           break;
	               }
	             }
	          }
		}
		echo "</div>";
	}


	function get_menu_options()
	{
		$parent_links = $this->get_active_parent_links();
		$child_menu = array();
		$parent_name = array();
		if($parent_links){
			foreach ($parent_links as $parent){
				$parent_name[$parent->id] = $parent->name;
			}
		}

		foreach ($parent_name as $key=>$parent) {
			if($this->has_active_children($key)){
				$children_links = $this->get_active_children_links($key);
				$i=0;
				foreach ($children_links as $child) {
					if($this->has_active_children($child->id))
					{
						$grand_children_links = $this->get_active_children_links($child->id);
						foreach ($grand_children_links as $grand_child) {
							$child_menu[$parent][$child->id] = $child->name;
							$child_menu[$child->name][$grand_child->id] = $grand_child->name;
						}
					}else{
						$child_menu[$key] = $parent;
						$child_menu[$parent][$child->id] = $child->name;
					}			
				}
			}else{
				$child_menu[$key] = $parent;
			}
		}
		return $child_menu;
	}

	function get_parent_menu_options(){
		$parent_links = $this->get_active_parent_links();
		$parent_name = array();
		if($parent_links){
			foreach ($parent_links as $parent){
				$parent_name[$parent->id] = $parent->name;
			}
		}

		return $parent_name;
	}





	function generate_menu(){
		if($this->member_role_permissions){
			$bank_menus_array=array();
			foreach ($this->member_role_permissions as $permission) {
				$bank_menus_array[$permission->menu_id] = $permission->menu_id;
				$parent_menu_id = isset($this->active_child_id_as_key_parent_id_as_value_array[$permission->menu_id])?$this->active_child_id_as_key_parent_id_as_value_array[$permission->menu_id]:'';
				if(in_array($parent_menu_id,$bank_menus_array)){

				}else{
					$bank_menus_array[$parent_menu_id] = $parent_menu_id;
				}
			}
			$string = 'group';
			$dashboard_id = $this->get_dashboard_id();
			$bank_menus_array=array($dashboard_id=>$dashboard_id)+$bank_menus_array;
			if($bank_menus_array){
				$parent_new = array();
				foreach ($bank_menus_array as $menu_id) {
					$link = $this->get($menu_id);
					if($link && $link->parent_id==0){
						$parent_new[] = $link;
					}else{
					}
				}
				$value = array();
				foreach ($parent_new as $parent_new_value) {
					$value[$parent_new_value->id] = $parent_new_value;
				}
				if($value){
					echo '
						<ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
					        <div class="toggle_menu_search">
					            <li class="m-menu__section m-menu__section--first mt-0">
					               <h4 class="m-menu__section-text">'.translate('SEARCH MENU').'</h4>
					               <span class="search_history">
					                  <i class="mdi mdi-notification-clear-all clear_search" title="Clear search"></i>
					               </span>
					            </li>
					            <li class="m-menu__item " aria-haspopup="true"  m-menu-link-redirect="1">
					               <div class="m-input-icon m-input-icon--left sidebar_search">
					                  <input type="text" class="form-control form-control-sm m-input search_input" placeholder="'.translate('Type here').'...">
					                  <span class="m-input-icon__icon m-input-icon__icon--left">
					                     <span>
					                        <i class="mdi mdi-magnify"></i>
					                     </span>
					                  </span>
					               </div>
					            </li>
				        	</div>
				        	<li class="m-menu__item" aria-haspopup="true"  m-menu-link-redirect="1"><a  href="'.site_url('group').'" class="m-menu__link "><i class="m-menu__link-icon mdi mdi-view-dashboard"></i><span class="m-menu__link-text">'.translate('Dashboard').'</span></a></li>
					';
					foreach ($value as $parent) {
						$href = $parent->url?site_url($parent->url):'javascript:;';
						if(preg_match('/javascript/', $href)){
							$href = 'javascript:;';
						}
						$show = 1;
						$specific = 0;
						

						if($specific){
							if($show){
								if($this->billing_settings->menu_acceptable_for_package($this->group->billing_package_id,$parent->id)){
									echo '<li class="m-menu__section li_searchable_hidden">';
									echo '<h4 class="m-menu__section-text">'.translate($name).'</h4>';
									echo '<i class="m-menu__section-icon '.$parent->icon.'"></i>';
									echo'</li>';
									$this->generate_side_bar_sub_menu($parent->id,$bank_menus_array,TRUE);

								}
							}
						}else{
							if($this->billing_settings->menu_acceptable_for_package($this->group->billing_package_id,$parent->id)){
								echo '<li class="m-menu__section li_searchable_hidden">';
									echo '<h4 class="m-menu__section-text">'.translate($name).'</h4>';
									echo '<i class="m-menu__section-icon '.$parent->icon.'"></i>';
									echo'</li>';
									$this->generate_sub_menu($parent->id,$bank_menus_array,TRUE);
							}
						}
					}
					echo '
							<div class="no_search">
					            <i class="mdi mdi-table-search"></i>
					            <p>Oops!<br><small>No menu items found<br><span>[click to close]</span></small></p>
					        </div>
					    </ul>
					';
				}else{

				}
			}
		}else{
			$parent_links = $this->get_active_parent_links();
			if($this->uri->uri_string() =='bank' || $this->uri->uri_string()==''){
				$active = 'm-menu__item--active';
			}else{
				$active = '';
			}
			if($parent_links){
				echo '
					<ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
				        <div class="toggle_menu_search">
				            <li class="m-menu__section m-menu__section--first mt-0">
				               <h4 class="m-menu__section-text">'.translate('SEARCH MENU').'</h4>
				               <span class="search_history">
				                  <i class="mdi mdi-notification-clear-all clear_search" title="Clear search"></i>
				               </span>
				            </li>
				            <li class="m-menu__item " aria-haspopup="true"  m-menu-link-redirect="1">
				               <div class="m-input-icon m-input-icon--left sidebar_search">
				                  <input type="text" class="form-control form-control-sm m-input search_input" placeholder="'.translate('Type here').'...">
				                  <span class="m-input-icon__icon m-input-icon__icon--left">
				                     <span>
				                        <i class="mdi mdi-magnify"></i>
				                     </span>
				                  </span>
				               </div>
				            </li>
			        	</div>
			       		<li class="m-menu__item '.$active.'" aria-haspopup="true"  m-menu-link-redirect="1"><a  href="'.site_url('dashboard').'" class="m-menu__link "><i class="m-menu__link-icon mdi mdi-view-dashboard"></i><span class="m-menu__link-text">'.translate('Dashboard').'</span></a></li>
				';
				foreach($parent_links as $link){
					$href = $link->url?site_url($link->url):'javascript:;';
					if(preg_match('/javascript/', $href)){
						$href = 'javascript:;';
					}
					if(TRUE){
						$show = 1;
						$specific = 0;
					
						if($specific){
							if($show){
								echo'<li class="m-menu__section li_searchable_hidden">';
										$menu_name = $link->name;
										$menu_name_array = explode('[',$menu_name);
										if(isset($menu_name_array[0])&&isset($menu_name_array[1])){
											if($link->language_key){
												if($new_menu_name = $this->lang->line($link->language_key)){
													$menu_name = $new_menu_name.' ['.$menu_name_array[1];
												}
											}
										}else{
											if($link->language_key){
												if($new_menu_name = $this->lang->line($link->language_key)){
													$menu_name = $new_menu_name;
												}
											}
										}
                  					echo '<h4 class="m-menu__section-text">'.translate($menu_name).'</h4><i class="m-menu__section-icon '.$link->icon.'"></i>';
									echo'</li>';
									$this->generate_sub_menu($link->id);

							}else{
							}
						}else{
							echo'<li class="m-menu__section li_searchable_hidden">';
									$menu_name = $link->name;
									$menu_name_array = explode('[',$menu_name);
									if(isset($menu_name_array[0])&&isset($menu_name_array[1])){
										if($link->language_key){
											if($new_menu_name = $this->lang->line($link->language_key)){
												$menu_name = $new_menu_name.' ['.$menu_name_array[1];
											}
										}
									}else{
										// if($link->language_key){
										// 	if($new_menu_name = $this->lang->line($link->language_key)){
										// 		$menu_name = $new_menu_name;
										// 	}
										// }
									}
									echo '<h4 class="m-menu__section-text">'.translate($menu_name).'</h4><i class="m-menu__section-icon '.$link->icon.'"></i>';
							echo'</li>
							';
							$this->generate_sub_menu($link->id);

						}
					}
				}
				echo '
						<div class="no_search">
				            <i class="mdi mdi-table-search"></i>
				            <p>Oops!<br><small>No menu items found<br><span>[click to close]</span></small></p>
				        </div>
				    </ul>
				';
			}
		}
	}

	function generate_page_title_bank_menus($url=''){
		$url = $url?:$this->uri->uri_string();
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('url').'="'.$this->db->escape_str($url).'"',NULL,FALSE);
		$menu = $this->db->get('bank_menus')->row();
		if($menu){
			if($this->has_active_children($menu->id)){
				if($new_parent = $this->check_menu_position($menu->id)){
					$this->generate_children_title_bank_menus($new_parent,$url);
				}else{
					$this->generate_children_title_bank_menus($menu->id,$url);
				}
			}else{
				$parent_id = $this->has_active_parent($menu->id);
				if($parent_id){
					if($new_parent = $this->check_menu_position($menu->id)){
						$this->generate_children_title_bank_menus($new_parent,$url,$parent_id);
					}else{
						$this->generate_children_title_bank_menus($parent_id,$url);
					}
				}else{
				}
			}
		}else{
			$num = 2;
			$strings = explode('/', $url);
			$count = count($strings);
			if(in_array('listing', $strings)){
				$num = 1;
			}
			if($count>2){
				$max = $count-$num;
				$new_url = '';
				foreach ($strings as $key => $string) {
					if($key == $max){
						break;
					}
					if($new_url){
						$new_url.='/'.$string;
					}else{
						$new_url=$string;
					}
					
				}
				if(preg_match('/group\/statements/', $new_url)){
					$new_url = 'group/statements/listing';
				}
				$this->generate_page_title_bank_menus($new_url);
			}
		}
	}

	function generate_children_title_bank_menus($id=0,$url='',$set_active=''){
		$this->notification_counts = array(
			'UNRECONCILED_DEPOSITS_COUNT' => $this->unreconciled_deposits_count,
			'UNRECONCILED_WITHDRAWALS_COUNT' => $this->unreconciled_withdrawals_count,
			'UNRECONCILED_DEPOSITS_WITHDRAWALS' => $this->total_unreconciled_deposits_and_withdrawals_count,
			'ACTIVE_LOAN_APPLICATIONS' => $this->active_loan_applications,
			'WITHDRAWAL_TASKS_COUNT' => $this->withdrawal_tasks_count,
			'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT' => $this->pending_withdrawal_approval_requests_count,
		);
		$childrens = $this->get_active_children_links($id);
		$enable_split=FALSE;
		if(count($childrens)>4){
			$enable_split = TRUE;
		}
		echo '
			<ul class="page_title_cust_navs nav nav-tabs m-tabs-line m-tabs-line--primary" role="tablist">';
			$i = 0;
			$has_active = false;
			foreach ($childrens as $child) {
			
				$specific = 1;
				$show = 1;
				if($specific){
					if($show):
						if($set_active){
							if($set_active == $child->id){
								$active = 'active';
								$has_active = TRUE;
							}else{
								$active = '';
							}
						}else{
							if($url == $child->url){
								$active = 'active';
								$has_active = TRUE;
							}else{
								$active = '';
							}
						}
						$set_break = false;
						if($i==4){
							if($enable_split){
								if($url == $child->url){
									$set_break = TRUE;
								}else{
									continue;
								}
							}
						}
						if($i==3 && $has_active==false){
							if($enable_split){
				        		continue;
				        	}
				        }
						$href = $child->url?site_url($child->url):'javascript:;';
						if(preg_match('/javascript/', $href)){
							$href = 'javascript:;';
						}
						$sub_menu_name = $child->name;
						$sub_menu_name_array = explode('[',$sub_menu_name);
						if(isset($sub_menu_name_array[0])&&isset($sub_menu_name_array[1])){
							$child->name = translate($sub_menu_name_array[0]).' ['.$sub_menu_name_array[1];
						}else{
							$child->name = translate($sub_menu_name);
						}

				        foreach ($this->notification_counts as $k => $v){
							$count_class = "";
							if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
								$count_class = "deposits_count ";
							}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
								$count_class = " withdrawals_count ";
							}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
								$count_class = " withdrawals_count ";
							}
							if($v){
					            $name = preg_replace('/\['.$k.'\]/', '<span class="m-nav__link-badge m-badge m-badge--info cust_badge_counter_child '.$count_class.' ">  '.$v.'</span>', $child->name);
					        	if($name!==$child->name){
					        		break;
					        	}
					        }else{
						        $name = preg_replace('/\['.$k.'\]/', '<span style="display:none;"  class="m-nav__link-badge m-badge m-badge--info cust_badge_counter_child '.$count_class.' "> '.$v.'</span>', $child->name);
					        	if($name!==$child->name){
					        		break;
					        	}
					        }
				        }
				        if($this->has_active_children($child->id)){
				        	echo '
					        	<li class="nav-item dropdown m-tabs__item">
			                        <a class="nav-link m-tabs__link dropdown-toggle '.$active.'" data-toggle="dropdown" href="'.$href.'" role="button" aria-haspopup="true" aria-expanded="false">
			                            <i class="'.$child->icon.'"></i> '.($name).'
			                        </a>'.$this->generate_page_title_bank_menus_children($child->id,$url).'
			                    </li>
			                ';
				        }else{
				        	echo '
								<li class="nav-item m-tabs__item">
			                        <a class="nav-link m-tabs__link '.$active.'" href="'.$href.'" >
			                            <i class="'.$child->icon.'"></i> '.($name).'
			                        </a>
			                    </li>
							';
				        }
				        if($set_break){
				        	break;
				        }
				        $i++;
				    endif;
			    }
			}
		echo '
			</ul>
		';
	}

	function generate_page_title_bank_menus_children($id=0,$url=''){
		$childrens = $this->get_active_children_links($id);
		$html = '
			<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 48px, 0px);">';
		if($childrens){
			$divider_show_num = 0;
			foreach ($childrens as $key=>$child) {
				$show = 1;
				$specific = 0;
				

					$specific = 1;
					$show = 1;
				
				if($specific){
					if($show){
						++$divider_show_num;
						$href = $child->url?site_url($child->url):'javascript:;';
						if(preg_match('/javascript/', $href)){
							$href = 'javascript:;';
						}
						if($url == $child->url){
							$active = 'active';
						}else{
							$active = '';
						}
						foreach ($this->notification_counts as $k => $v){
				        	$count_class = "";
							if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
								$count_class = "deposits_count ";
							}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
								$count_class = " withdrawals_count ";
							}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
								$count_class = " withdrawals_count ";
							}
							if($v){
					            $name = preg_replace('/\['.$k.'\]/', '<span class="m-nav__link-badge m-badge m-badge--info cust_badge_counter_child '.$count_class.' ">  '.$v.'</span>', $child->name);
					        	if($name!==$child->name){
					        		break;
					        	}
					        }else{
						        $name = preg_replace('/\['.$k.'\]/', '<span style="display:none;"  class="m-nav__link-badge m-badge m-badge--info cust_badge_counter_child '.$count_class.' "> '.$v.'</span>', $child->name);
					        	if($name!==$child->name){
					        		break;
					        	}
					        }
				        }
						$html.='
							<a class="dropdown-item '.$active.'"  href="'.$href.'">
			                    '.translate($name).'
			                </a>
						';
						if($divider_show_num>1 && $key ==(count($childrens)-2)){
							$html.='
								<div class="dropdown-divider"></div>
							';
						}
					}
				}
			}
		}
		$html.='
			</div>
		';
		return $html;
	}

	function check_menu_position($id=0){
		if($parent_id = $this->has_active_parent($id)){
			if($new_parent_id = $this->has_active_parent($parent_id)){
				if($parent_id = $this->has_active_parent($new_parent_id)){
					return $new_parent_id;
				}else{
					if($this->has_active_children($id)){
						return $parent_id = $this->has_active_parent($id);
					}else{
						return FALSE;
					}
				}
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function check_if_can_set_active($id=''){
		if($id){
			$url = $this->uri->uri_string();
			$menu = $this->get_link_by_url($url);
			if($menu){
				if($id == $menu->parent_id){
					return TRUE;
				}else{
					if($id == $this->has_active_parent($menu->parent_id)){
						return TRUE;
					}
				}
			}
		}else{
			return TRUE;
		}
	}


	function generate_sub_menu($parent_id = 0,$acceptable_children=array(),$check_children=FALSE){
		$this->notification_counts = array(
			'UNRECONCILED_DEPOSITS_COUNT' => $this->unreconciled_deposits_count,
			'UNRECONCILED_WITHDRAWALS_COUNT' => $this->unreconciled_withdrawals_count,
			'UNRECONCILED_DEPOSITS_WITHDRAWALS' => $this->total_unreconciled_deposits_and_withdrawals_count,
			'ACTIVE_LOAN_APPLICATIONS' => $this->active_loan_applications,
			'WITHDRAWAL_TASKS_COUNT' => $this->withdrawal_tasks_count,
			'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT' => $this->pending_withdrawal_approval_requests_count,
		);
		
		$children_links = $this->get_active_children_links($parent_id);
		if($children_links){
			foreach($children_links as $child_link){
				if($check_children):
					$show = 1;
					$specific = 0;
					

					if($specific){
						if($show){
							if(in_array($child_link->id, $acceptable_children)):
								$href = $child_link->url?site_url($child_link->url):'javascript:;';
								if(preg_match('/javascript/', $href)){
									$href = 'javascript:;';
								}
								if($this->billing_settings->menu_acceptable_for_package($this->group->billing_package_id,$child_link->id)):
									echo '<li class="m-menu__item li_searchable" aria-haspopup="true" m-menu-link-redirect="1">';
									echo '<a href="'.$href.'" class="m-menu__link">';
										if($this->has_children($child_link->id)){
										}
										$sub_menu_name = $child_link->name;
										$sub_menu_name_array = explode('[',$sub_menu_name);
										if(isset($sub_menu_name_array[0])&&isset($sub_menu_name_array[1])){
											$child_link->name = translate($sub_menu_name_array[0]).' ['.$sub_menu_name_array[1];
										}else{
											$child_link->name = translate($sub_menu_name);
										}
										foreach ($this->notification_counts as $k => $v){
											$count_class = "";
											if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
												$count_class = " deposits_count ";
											}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
												$count_class = " withdrawals_count ";
											}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
												$count_class = " withdrawals_count ";
											}
											if($v){
									            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', translate($child_link->name));
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }else{
									            $name = preg_replace('/\['.$k.'\]/', '<span style="display:none;"  class="badge badge-info '.$count_class.' ">'.$v.'</span>', translate($child_link->name));
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }
								        }
										echo '<i class="m-menu__link-icon '.$child_link->icon.'"></i><span class="m-menu__link-text">';
								        echo $name;
										echo '</span></a></li>';
								else:
								endif;
							endif;
						}
					}else{
						if(in_array($child_link->id, $acceptable_children)):
							$href = $child_link->url?site_url($child_link->url):'javascript:;';
							if(preg_match('/javascript/', $href)){
								$href = 'javascript:;';
							}
						endif;
					}
				else:
					$href = $child_link->url?site_url($child_link->url):'javascript:;';
					if(preg_match('/javascript/', $href)){
						$href = 'javascript:;';
					}
					if(TRUE):
						$show = 1;
						$specific = 0;
						
						if($specific){
							if($show){
								echo '<li class="m-menu__item li_searchable" aria-haspopup="true" m-menu-link-redirect="1">';
								echo '<a href="'.$href.'" class="m-menu__link ">';
										$sub_menu_name = $child_link->name;
										$sub_menu_name_array = explode('[',$sub_menu_name);
										if(isset($sub_menu_name_array[0])&&isset($sub_menu_name_array[1])){
											$child_link->name = translate($sub_menu_name_array[0]).' ['.$sub_menu_name_array[1];
										}else{
											$child_link->name = translate($sub_menu_name);
										}
										foreach ($this->notification_counts as $k => $v){
											$count_class = "";
											if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
												$count_class = " deposits_count ";
											}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
												$count_class = " withdrawals_count ";
											}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
												$count_class = " withdrawals_count ";
											}
											if($v){
									            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', ($child_link->name));
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }else{
										        $name = preg_replace('/\['.$k.'\]/', '<span style="display:none;"  class="badge badge-info '.$count_class.' ">'.$v.'</span>', $child_link->name);
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }
								        }
										echo '<i class="m-menu__link-icon '.$child_link->icon.'"></i><span class="m-menu__link-text">';
								    	// echo $child_link->name;
								        echo $name;
																
										echo '</span></a></li>';
										// $this->generate_sub_menu($child_link->id);
									// echo '</li>';
							}else{

							}
						}else{
								if($this->uri->uri_string() == $child_link->url){
									$active = 'm-menu__item--active';
								}else{
									if($this->check_if_can_set_active($child_link->id)){
										$active = 'm-menu__item--active';
									}else{
										$active = '';
									}
								}

								echo '<li class="m-menu__item li_searchable '.$active.'" aria-haspopup="true" m-menu-link-redirect="1">';
								echo '<a href="'.$href.'" class="m-menu__link">';
										$sub_menu_name = $child_link->name;
										$sub_menu_name_array = explode('[',$sub_menu_name);
										if(isset($sub_menu_name_array[0])&&isset($sub_menu_name_array[1])){
											$child_link->name = translate($sub_menu_name_array[0]).' ['.$sub_menu_name_array[1];
										}else{
											$child_link->name = translate($sub_menu_name);
										}
										foreach ($this->notification_counts as $k => $v){
											$count_class = "";
											if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
												$count_class = "deposits_count ";
											}else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
												$count_class = " withdrawals_count ";
											}else if ($k == 'UNRECONCILED_DEPOSITS_WITHDRAWALS') {
												$count_class = " withdrawals_count ";
											}
											if($v){
									            $name = preg_replace('/\['.$k.'\]/', '<span class="m-nav__link-badge m-badge m-badge--info cust_badge_counter '.$count_class.' ">'.$v.'</span>', ($child_link->name));
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }else{
										        $name = preg_replace('/\['.$k.'\]/', '<span style="display:none;"  class="m-nav__link-badge m-badge m-badge--info cust_badge_counter '.$count_class.' ">'.$v.'</span>', translate($child_link->name));
									        	if($name!==$child_link->name){
									        		break;
									        	}
									        }
								        }
										echo '<i class="m-menu__link-icon '.$child_link->icon.'"></i><span class="m-menu__link-text">';
								        echo $name;
										echo '</span></a></li>';

						}
					else:
					endif;
				endif;
			}
			// echo '</li>';
		}else{
		}
	}


	function get_link_by_url($url = ''){
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('url').' = "'.$url.'"',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
        $this->db->limit(1);
		return $this->db->get('bank_menus')->row();	
	}

	function get_link_by_language_key($language_key = ''){
		$this->select_all_secure('bank_menus');
		$this->db->where($this->dx('language_key').' = "'.$language_key.'"',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
        $this->db->limit(1);
		return $this->db->get('bank_menus')->row();	
	}


}?>
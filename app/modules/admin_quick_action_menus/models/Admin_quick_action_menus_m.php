<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_quick_action_menus_m extends MY_Model {

	protected $_table = 'admin_quick_action_menus';

	protected $special_url_segments = array('/edit');

	function __construct(){
		$this->load->dbforge();
		//$this->install();
	}

	function install(){
		$this->db->query("
		create table if not exists admin_quick_action_menus(
			id int not null auto_increment primary key,
			`parent_id` blob,
			`name` blob,
			`url` blob,
			`icon` blob,
			`position` blob,
			`active` blob,
			created_by blob,
			created_on blob,
			modified_on blob,
			modified_by blob
		)");
	}

	function get($id = 0){
		$this->select_all_secure('admin_quick_action_menus');
		$this->db->where('id',$id);
		return $this->db->get('admin_quick_action_menus')->row();
	}

	function insert($input,$key=FALSE){
        return $this->insert_secure_data('admin_quick_action_menus', $input);
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'admin_quick_action_menus',$input);
    }

	function get_options(){
		$arr = array();
		$this->select_all_secure('admin_quick_action_menus');
		$admin_quick_action_menus = $this->db->get('admin_quick_action_menus')->result();
		foreach($admin_quick_action_menus as $menu){
			$arr[$menu->id] = $menu->name;
		}
		return $arr;
	}

	function get_all(){
		$this->select_all_secure('admin_quick_action_menus');
		return $this->db->get('admin_quick_action_menus')->result();
	}

	function get_parent_links(){
		$this->select_all_secure('admin_quick_action_menus');
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('admin_quick_action_menus')->result();	
	}

	function get_active_parent_links(){
		$this->select_all_secure('admin_quick_action_menus');
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('admin_quick_action_menus')->result();	
	}

	function get_children_links($parent_id = 0){
		$this->select_all_secure('admin_quick_action_menus');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('admin_quick_action_menus')->result();	
	}

	function get_active_children_links($parent_id = 0){
		$this->select_all_secure('admin_quick_action_menus');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('admin_quick_action_menus')->result();	
	}

	function has_children($parent_id = 0){
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		return $this->db->count_all_results('admin_quick_action_menus')>0?TRUE:FALSE;	
	}

	function has_active_children($parent_id = 0){
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('admin_quick_action_menus')>0?TRUE:FALSE;	
	}

	function remove_special_url_segments($url = '',$url_segments = array()){
		if(!empty($url_segments)&&$url){
			foreach ($url_segments as $url_segment) {
				$p = strpos($url,$url_segment);
				if ( $p!== false) {
					$url = substr($url,0,$p);
					//return once the first occurrence is found
					return $url;
				}
			}
			//return current url if all fails
			return $url;
		}else{
			return '';
		}
		$p = strpos($grand_child_link_url,'/edit');
		if ( $p!== false) {
			$grand_child_link_url = substr($grand_child_link_url,0,$p);
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
	                        $this->admin_quick_action_menus_m->display_children($link->id); 
	            echo '</li>';
	        endforeach;
			echo '</ol>';
		}	
	}

	function generate_quick_action_menu(){
		$parent_links = $this->get_active_parent_links();
		if($parent_links){
			echo '<ul class="dropdown-menu" role="menu">';
				
				foreach($parent_links as $parent_link){
					echo '<li>
                        <a href="'.site_url($parent_link->url).'">
                            <i class="'.$parent_link->icon.'"></i> '.$parent_link->name.' </a>
                    </li>';
					if($this->has_active_children($parent_link->id)){
						echo '<li class="divider">
						</li>';
						$children_links = $this->get_active_children_links($parent_link->id);
						foreach($children_links as $child_link){
							echo '	<li>
										<a href="'.site_url($child_link->url).'">
										<i class="'.$child_link->icon.'"></i> '.$child_link->name.' </a>
									</li>';
						}
					}
				}
			echo '</ul>';
		}
	}

	



}
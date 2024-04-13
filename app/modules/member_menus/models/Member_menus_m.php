<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_menus_m extends MY_Model {

	protected $_table = 'member_menus';

	protected $special_url_segments = array("/edit/","/fine_statement/","/miscellaneous_statement/","/view/","/listing/",'/view_installments/',"/create/","/miscellaneous_statement/","/fine_statement/","/top_up/","/sell/");

	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
		$this->notification_counts = array(
			'PENDING_LOAN_APPLICATIONS' => isset($this->pending_loan_applications)?$this->pending_loan_applications:'',
			'PENDING_APPROVAL_REQUESTS' => isset($this->pending_withdrawal_approval_requests_count)?$this->pending_withdrawal_approval_requests_count:'',
		);
	}

	public function install(){
		$this->db->query("
		create table if not exists member_menus(
			id int not null auto_increment primary key,
			`parent_id` blob,
			`name` blob,
			`url` blob,
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
		$this->select_all_secure('member_menus');
		$this->db->where('id',$id);
		return $this->db->get('member_menus')->row();
	}

	function insert($input = array(),$skip_value = FALSE)
	{
		return $this->insert_secure_data('member_menus',$input);
	}

	function generate_page_title($url='')
	{ 
		if(empty($url))
		{
			$url = $this->uri->uri_string();
		}
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('url').'="'.$this->db->escape_str($url).'"',NULL,FALSE);

		$res = $this->db->get('member_menus')->row();

		if($res)
		{

			echo '<i class="'.$res->icon.' font-dark"></i>
			<span class="caption-subject font-dark">'.ucwords($res->name).'</span>';
		}
		else
		{
			echo '<i class="fa fa-list-ul font-dark"></i>
			<span class="caption-subject font-dark">{group:template:title}</span>';
		}
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'member_menus',$input);
    }

	function get_all()
	{
		$this->select_all_secure('member_menus');
		return $this->db->get('member_menus')->result();
	}

	function count_all_active()
	{
		return $this->db->count_all_results('member_menus');
	}

	function get_options(){
		$arr = array();
		$this->select_all_secure('member_menus');
		$member_menus = $this->db->get('member_menus')->result();
		foreach($member_menus as $menu){
			$arr[$menu->id] = $menu->name;
		}
		return $arr;
	}


	function get_parent_links(){
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('member_menus')->result();	
	}

	function get_active_parent_links(){
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('member_menus')->result();	
	}

	function get_active_parent_link($parent_id){
		$this->select_all_secure('member_menus');
		$this->db->where('id',$parent_id);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);

		return $this->db->get('member_menus')->row();	
	}

	function get_children_links($parent_id = 0){
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('member_menus')->result();	
	}

	function get_active_children_links($parent_id = 0){
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('member_menus')->result();	
	}

	function has_children($parent_id = 0){
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		return $this->db->count_all_results('member_menus')>0?TRUE:FALSE;	
	}

	function has_active_children($parent_id = 0){
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('member_menus')>0?TRUE:FALSE;	
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
	                        $this->member_menus_m->display_children($link->id); 
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
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('url').' = "'.$active_url.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->get('member_menus')->row();
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
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('member_menus')->result();
	}

	function generate_header_member_menus(){
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
		$child_link_url = $this->remove_special_url_segments($child_link_url,$this->special_url_segments);
		if($child_link_url){
			$this->db->select(array($this->dx('parent_id').' as parent_id '));
			$this->db->where($this->dx('url').' = "'.$child_link_url.'"',NULL,FALSE);
			$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
			$menu = $this->db->get('member_menus')->row();
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
	}

	function grand_child_is_active($parent_id,$grand_child_link_url){
		$grand_child_link_url = $this->remove_special_url_segments($grand_child_link_url,$this->special_url_segments);
		if($grand_child_link_url){
			$this->db->select(array($this->dx('parent_id').' as parent_id '));
			$this->db->where($this->dx('url').' = "'.$grand_child_link_url.'"',NULL,FALSE);
			$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
			$parent_menu = $this->db->get('member_menus')->row();	
			if($parent_menu){
				$this->db->select(array($this->dx('parent_id').' as parent_id '));
				$this->db->where('id',$parent_menu->parent_id);
				$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
				$menu = $this->db->get('member_menus')->row();
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
	}

	function great_grand_child_is_active($parent_id,$great_grand_child_link_url){
		$great_grand_child_link_url = $this->remove_special_url_segments($great_grand_child_link_url,$this->special_url_segments);
		if($great_grand_child_link_url)
		{
			$this->db->select(array($this->dx('parent_id').' as parent_id '));
			$this->db->where($this->dx('url').' = "'.$great_grand_child_link_url.'"',NULL,FALSE);
			$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
			$grand_child_menu = $this->db->get('member_menus')->row();
			if($grand_child_menu)
			{
				$this->db->select(array($this->dx('parent_id').' as parent_id '));
				$this->db->where('id',$grand_child_menu->parent_id);
				$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
				$parent_menu = $this->db->get('member_menus')->row();
				if($parent_menu)
				{
					$this->db->select(array($this->dx('parent_id').' as parent_id '));
					$this->db->where('id',$parent_menu->parent_id);
					$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
					$menu = $this->db->get('member_menus')->row();
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
		}
	}

	function generate_side_bar_menu(){
		// die('im0');
		$parent_links = $this->get_active_parent_links();
		if($parent_links){
			echo '<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				';
			foreach($parent_links as $link){
				$show = 1;
				$specific = 0;
				if($link->enable_menu_for){
					$specific = 1;
					$feature = $link->enabled_disabled_feature;
					if(isset($this->group->$feature) && $this->group->$feature){
						if($link->enabled_or_disabled == 1){//show menu
							$show = 1;
						}else if($link->enabled_or_disabled == 2){//do not show menu
							$show = 0;
						}
					}else{
						if($link->enabled_or_disabled == 1){//show menu
							$show = 0;
						}else if($link->enabled_or_disabled == 2){//do not show menu
							$show = 1;
						}
					}
				}

				if($specific){
					if($show){
						$href = $link->url?site_url($link->url):'javascript:;';
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
								<i class="'.$link->icon.'"></i>
								<span class="title">'.$link->name.'</span>';
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
					$href = $link->url?site_url($link->url):'javascript:;';
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
							echo '<span class="title">';
							echo ($this->lang->line($link->language_key)?:$link->name).'</span>';
							if($this->has_children($link->id)){
							    echo '<span class="arrow "></span>';	
							}
							echo '</a>';
							$this->generate_side_bar_sub_menu($link->id);
					echo'</li>
					';
				}
			}
			echo '</ul>';
		}
	}

	function generate_side_bar_sub_menu($parent_id = 0){
		$children_links = $this->get_active_children_links($parent_id);
		if($children_links){
			echo '<ul class="sub-menu">';
			foreach($children_links as $child_link){
				$show = 1;
				$specific = 0;
				if($child_link->enable_menu_for){
					$specific = 1;
					$feature = $child_link->enabled_disabled_feature;
					if(isset($this->group->$feature) && $this->group->$feature){
						if($child_link->enabled_or_disabled == 1){//show menu
							$show = 1;
						}else if($child_link->enabled_or_disabled == 2){//do not show menu
							$show = 0;
						}
					}else{
						if($child_link->enabled_or_disabled == 1){//show menu
							$show = 0;
						}else if($child_link->enabled_or_disabled == 2){//do not show menu
							$show = 1;
						}
					}
				}

				if($specific){
					if($show){
						$href = $child_link->url?site_url($child_link->url):'javascript:;';
						echo '
							<li class="';
							if(uri_string()==$child_link->url&&$child_link->url!==''){
								echo 'active';
							}
							if($this->child_is_active($child_link->id,uri_string())||$this->grand_child_is_active($child_link->id,uri_string())){
								echo 'active open';
							}
							echo '">
								<a href="'.$href.'">
								<i class="'.$child_link->icon.'"></i>';
								if($this->has_children($child_link->id)){
								    echo '<span class="arrow "></span>';	
								}
								echo $child_link->name.'</a>';
								$this->generate_side_bar_sub_menu($child_link->id);
						echo '</li>';
					}else{

					}
				}else{
					$href = $child_link->url?site_url($child_link->url):'javascript:;';
					echo '
						<li class="';
						if(uri_string()==$child_link->url&&$child_link->url!==''){
							echo 'active';
						}
						if($this->child_is_active($child_link->id,uri_string())||$this->grand_child_is_active($child_link->id,uri_string())){
							echo 'active open';
						}
						echo '">
							<a href="'.$href.'">
							<i></i>';
							if($this->has_children($child_link->id)){
							    echo '<span class="arrow "></span>';	
							}
							// echo $this->lang->line($child_link->language_key)?:$child_link->name.'</span>';

							echo ($this->lang->line($child_link->language_key)?:$child_link->name).'</a>';
							$this->generate_side_bar_sub_menu($child_link->id);
					echo '</li>';
				}
				/*if($this->group->disable_member_directory&&preg_match('/(directory)/',$child_link->url)){

				}else{
					
				}*/
			}
			echo '</ul>';
		}
	}

	function get_menu_by_link_url($link_url='')
	{
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('url').' = "'.$link_url.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $menu = $this->db->get('member_menus')->row();
	}

	function generate_page_quick_action_member_menus(){
		$link_url = uri_string();
		$menu = $this->get_menu_by_link_url($link_url);
		if($menu)
		{
			echo '<div class="actions">
            <div class="btn-group">
                <a class="btn btn-sm green dropdown-toggle" href="javascript:;" data-toggle="dropdown"> Actions
                    <i class="fa fa-angle-down"></i>
                </a>

               	<ul class="dropdown-menu pull-right">';
			
			$parent_id = $menu->parent_id;

			$parent_menu = $this->get($parent_id);
			$childrens = $this->get_children_links($parent_id);
			if($childrens){
				foreach ($childrens as $child) {
					$show = 1;
					$specific = 0;
					if($child->enable_menu_for){
						$specific = 1;
						$feature = $child->enabled_disabled_feature;
						if(isset($this->group->$feature) && $this->group->$feature){
							if($child->enabled_or_disabled == 1){//show menu
								$show = 1;
							}else if($child->enabled_or_disabled == 2){//do not show menu
								$show = 0;
							}
						}else{
							if($child->enabled_or_disabled == 1){//show menu
								$show = 0;
							}else if($child->enabled_or_disabled == 2){//do not show menu
								$show = 1;
							}
						}
					}

					if($specific){
						if($show){
							$href = $child->url?site_url($child->url):'javascript:;';
							echo '<li>
	                            <a href="'.$href.'">
	                                <i class="'.$child->icon.'"></i> '.$child->name.'</a>';

	                                $children_menu = $this->get_active_children_links($child->id);
	                                if($children_menu)
	                                {
	                                	echo '<li class="divider"> </li>';
	                                	foreach ($children_menu as $child_menu) {
	                                		$child_href = $child_menu->url?site_url($child_menu->url):'javascript:;';

	                                		echo '<li class="child-menu">
					                                <a href="'.$child_href.'">
					                                    <i class="'.$child_menu->icon.'"></i> '.$child_menu->name.'</a>
					                             </li>';
	                                		
	                                	}
	                                }
		                    echo '</li>';
						}else{

						}
					}else{
						$href = $child->url?site_url($child->url):'javascript:;';
						echo '<li>
                            <a href="'.$href.'">
                                <i class="'.$child->icon.'"></i> '.$child->name.'</a>';

                                $children_menu = $this->get_active_children_links($child->id);
                                if($children_menu)
                                {
                                	echo '<li class="divider"> </li>';
                                	foreach ($children_menu as $child_menu) {
                                		$child_href = $child_menu->url?site_url($child_menu->url):'javascript:;';

                                		echo '<li class="child-menu">
				                                <a href="'.$child_href.'">
				                                    <i class="'.$child_menu->icon.'"></i> '.$child_menu->name.'</a>
				                             </li>';
                                		
                                	}
                                }
	                    echo '</li>';
					}
				} 
				echo '<li class="divider"> </li>';

			}
			$help_url = 'javascript:;';
			if($parent_menu)
			{
				$help_url = $parent_menu->help_url?site_url($parent_menu->help_url):'javascript:;';

			}
            echo '<li>
                    <a href="'.$help_url.'" target="_blank">
                        <i class="fa fa-ambulance"></i> Help</a>
                </li>';
			echo '</ul>
            	</div>
               </div>';
		}
		else
		{
			$link_url = uri_string();
			foreach ($this->special_url_segments as $key => $value) 
	         {
	             $segment = explode('/', $value);
	             if(preg_match('/'.$segment[0].'\/'.$segment[1].'/', $link_url))
	             {
	               $link_url = $this->uri->segment(1).'/'.$this->uri->segment(2).'/';
	               $menu = $this->get_menu_by_link_url($link_url)?:$this->get_menu_by_link_url($link_url.'listing');
	               if($menu)
	               {
	               		echo '<div class="actions">
				            <div class="btn-group">
				                <a class="btn btn-sm green dropdown-toggle" href="javascript:;" data-toggle="dropdown"> Actions
				                    <i class="fa fa-angle-down"></i>
				                </a>

				               	<ul class="dropdown-menu pull-right">';

							$childrens = $this->get_children_links($menu->id);
							if($childrens)
							{
									foreach ($childrens as $child) {
										$href = $child->url?site_url($child->url):'javascript:;';
										
										echo '<li>
				                                <a href="'.$href.'">
				                                    <i class="'.$child->icon.'"></i> '.$child->name.'</a>';
				                                    $children_menu = $this->get_active_children_links($child->id);
				                                    if($children_menu)
				                                    {
				                                    	echo '<li class="divider"> </li>';
				                                    	foreach ($children_menu as $child_menu) {
				                                    		$child_href = $child_menu->url?site_url($child_menu->url):'javascript:;';

				                                    		echo '<li class="child-menu">
									                                <a href="'.$child_href.'">
									                                    <i class="'.$child_menu->icon.'"></i> '.$child_menu->name.'</a>
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
								$help_url = $menu->help_url?site_url($menu->help_url):'javascript:;';
							}
				            echo '<li>
				                    <a href="'.$help_url.'" target="_blank">
				                        <i class="fa fa-ambulance"></i> Help</a>
				                </li>';
							echo '</ul>
				            	</div>
				               </div>';
	               }
	             }
	          }
		}
	}

	function generate_menu(){
		$parent_links = $this->get_active_parent_links();
		if($this->uri->uri_string() =='group' || $this->uri->uri_string()==''){
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
		       		<li class="m-menu__item '.$active.'" aria-haspopup="true"  m-menu-link-redirect="1"><a  href="'.site_url('member').'" class="m-menu__link "><i class="m-menu__link-icon mdi mdi-view-dashboard"></i><span class="m-menu__link-text">'.translate('Dashboard').'</span></a></li>
			';
			foreach($parent_links as $link){
				$href = $link->url?site_url($link->url):'javascript:;';
				if(preg_match('/javascript/', $href)){
					$href = 'javascript:;';
				}
				if(TRUE){
					$show = 1;
					$specific = 0;
					if($link->enable_menu_for){
						$specific = 1;
						$feature = $link->enabled_disabled_feature;
						if(isset($this->group->$feature) && $this->group->$feature){
							if($link->enabled_or_disabled == 1){//show menu
								$show = 1;
							}else if($link->enabled_or_disabled == 2){//do not show menu
								$show = 0;
							}
						}else{
							if($link->enabled_or_disabled == 1){//show menu
								$show = 0;
							}else if($link->enabled_or_disabled == 2){//do not show menu
								$show = 1;
							}
						}
						if(preg_match('/subscription_status/', $feature)){
							$status = (int) filter_var($feature, FILTER_SANITIZE_NUMBER_INT);
							if($status && ($this->group->subscription_status == $status)){
								$show = 0;
							}
						}
					}
					if($specific){
						if($show){
							echo'<li class="m-menu__section li_searchable_hidden">';
									$menu_name = $link->name;
									$menu_name_array = explode('[',$menu_name);
									if(isset($menu_name_array[0])&&isset($menu_name_array[1])){
										$menu_name = translate($menu_name_array[0]).' ['.$menu_name_array[1];
									}else{
										$menu_name = translate($menu_name);
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
										$menu_name = translate($menu_name_array[0]).' ['.$menu_name_array[1];
									}else{
										$menu_name = translate($menu_name);
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

	function generate_sub_menu($parent_id = 0,$acceptable_children=array(),$check_children=FALSE){
		$this->notification_counts = array(
			'PENDING_LOAN_APPLICATIONS' => $this->pending_loan_applications,
			'PENDING_APPROVAL_REQUESTS' => $this->pending_withdrawal_approval_requests_count,
		);
		$children_links = $this->get_active_children_links($parent_id);
		if($children_links){
			foreach($children_links as $child_link){
				if($this->group->subscription_status == '5'){
					if(!preg_match('/list/', strtolower($child_link->name))){
						continue;
					}
				}
				if($check_children):
					$show = 1;
					$specific = 0;
					if($child_link->enable_menu_for){
						$specific = 1;
						$feature = $child_link->enabled_disabled_feature;
						if(isset($this->group->$feature) && $this->group->$feature){
							if($child_link->enabled_or_disabled == 1){//show menu
								$show = 1;
							}else if($child_link->enabled_or_disabled == 2){//do not show menu
								$show = 0;
							}
						}else{
							if($child_link->enabled_or_disabled == 1){//show menu
								$show = 0;
							}else if($child_link->enabled_or_disabled == 2){//do not show menu
								$show = 1;
							}
						}
					}

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
											if($k == 'PENDING_LOAN_APPLICATIONS'){
												$count_class = " deposits_count ";
											}else if($k == 'PENDING_APPROVAL_REQUESTS'){
												$count_class = " deposits_count ";
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
						if($child_link->enable_menu_for){
							$specific = 1;
							$feature = $child_link->enabled_disabled_feature;
							if(isset($this->group->$feature) && $this->group->$feature){
								if($child_link->enabled_or_disabled == 1){//show menu
									$show = 1;
								}else if($child_link->enabled_or_disabled == 2){//do not show menu
									$show = 0;
								}
							}else{
								if($child_link->enabled_or_disabled == 1){//show menu
									$show = 0;
								}else if($child_link->enabled_or_disabled == 2){//do not show menu
									$show = 1;
								}
							}
						}
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
											if($k == 'PENDING_LOAN_APPLICATIONS'){
												$count_class = " deposits_count ";
											}else if($k == 'PENDING_APPROVAL_REQUESTS'){
												$count_class = " deposits_count ";
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
									if($k == 'PENDING_LOAN_APPLICATIONS'){
										$count_class = " deposits_count ";
									}else if($k == 'PENDING_APPROVAL_REQUESTS'){
										$count_class = " deposits_count ";
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

	function get_link_by_url($url = ''){
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('url').' = "'.$url.'"',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
        $this->db->limit(1);
		return $this->db->get('member_menus')->row();	
	}


	function generate_page_title_menus($url=''){
		$url = $url?:$this->uri->uri_string();
		$this->select_all_secure('member_menus');
		$this->db->where($this->dx('url').'="'.$this->db->escape_str($url).'"',NULL,FALSE);
		$menu = $this->db->get('member_menus')->row();
		if($menu){
			if($this->has_active_children($menu->id)){
				if($new_parent = $this->check_menu_position($menu->id)){
					$this->generate_children_title_menus($new_parent,$url);
				}else{
					$this->generate_children_title_menus($menu->id,$url);
				}
			}else{
				$parent_id = $this->has_active_parent($menu->id);
				if($parent_id){
					if($new_parent = $this->check_menu_position($menu->id)){
						$this->generate_children_title_menus($new_parent,$url,$parent_id);
					}else{
						$this->generate_children_title_menus($parent_id,$url);
					}
				}else{
				}
			}
		}else{
			$strings = explode('/', $url);
			$count = count($strings);
			if($count>2){
				$max = $count-2;
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
				$this->generate_page_title_menus($new_url);
			}
		}
	}

	// function generate_children_title_menus($id=0,$url='',$set_active=''){
	// 	$childrens = $this->get_active_children_links($id);
	// 	$enable_split=FALSE;
	// 	if(count($childrens)>4){
	// 		$enable_split = TRUE;
	// 	}
	// 	echo '
	// 		<ul class="page_title_cust_navs nav nav-tabs m-tabs-line m-tabs-line--primary" role="tablist">';
	// 		$i = 0;
	// 		$has_active = false;
	// 		foreach ($childrens as $child) {
	// 			if($set_active){
	// 				if($set_active == $child->id){
	// 					$active = 'active';
	// 					$has_active = TRUE;
	// 				}else{
	// 					$active = '';
	// 				}
	// 			}else{
	// 				if($url == $child->url){
	// 					$active = 'active';
	// 					$has_active = TRUE;
	// 				}else{
	// 					$active = '';
	// 				}
	// 			}
	// 			$set_break = false;
	// 			if($i==4){
	// 				if($enable_split){
	// 					if($url == $child->url){
	// 						$set_break = TRUE;
	// 					}else{
	// 						continue;
	// 					}
	// 				}
	// 			}
	// 			if($i==3 && $has_active==false){
	// 				if($enable_split){
	// 	        		continue;
	// 	        	}
	// 	        }
	// 			$href = $child->url?site_url($child->url):'javascript:;';
	// 			if(preg_match('/javascript/', $href)){
	// 				$href = 'javascript:;';
	// 			}
	// 			$sub_menu_name = $child->name;
	// 			$sub_menu_name_array = explode('[',$sub_menu_name);
	// 			if(isset($sub_menu_name_array[0])&&isset($sub_menu_name_array[1])){
	// 				$child->name = translate($sub_menu_name_array[0]).' ['.$sub_menu_name_array[1];
	// 			}else{
	// 				$child->name = translate($sub_menu_name);
	// 			}
	// 			$name = $child->name;
	// 	        if($this->has_active_children($child->id)){
	// 	        	echo '
	// 		        	<li class="nav-item dropdown m-tabs__item">
	//                         <a class="nav-link m-tabs__link dropdown-toggle '.$active.'" data-toggle="dropdown" href="'.$href.'" role="button" aria-haspopup="true" aria-expanded="false">
	//                             <i class="'.$child->icon.'"></i> '.($name).'
	//                         </a>'.$this->generate_page_title_menus_children($child->id,$url).'
	//                     </li>
	//                 ';
	// 	        }else{
	// 	        	echo '
	// 					<li class="nav-item m-tabs__item">
	//                         <a class="nav-link m-tabs__link '.$active.'" href="'.$href.'" >
	//                             <i class="'.$child->icon.'"></i> '.($name).'
	//                         </a>
	//                     </li>
	// 				';
	// 	        }
	// 	        if($set_break){
	// 	        	break;
	// 	        }
	// 	        $i++;
	// 		}
	// 	echo '
	// 		</ul>
	// 	';
	// }

	// function generate_page_title_menus_children($id=0,$url=''){
	// 	$childrens = $this->get_active_children_links($id);
	// 	$html = '
	// 		<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 48px, 0px);">';
	// 	if($childrens){
	// 		foreach ($childrens as $key=>$child) {
	// 			$href = $child->url?site_url($child->url):'javascript:;';
	// 			if(preg_match('/javascript/', $href)){
	// 				$href = 'javascript:;';
	// 			}
	// 			if($url == $child->url){
	// 				$active = 'active';
	// 			}else{
	// 				$active = '';
	// 			}
	// 			$name = $child->name;
	// 			$html.='
	// 				<a class="dropdown-item '.$active.'"  href="'.$href.'">
	//                     '.translate($name).'
	//                 </a>
	// 			';
	// 			if($key ==(count($childrens)-2)){
	// 				$html.='
	// 					<div class="dropdown-divider"></div>
	// 				';
	// 			}
	// 		}
	// 	}
	// 	$html.='
	// 		</div>
	// 	';
	// 	return $html;
	// }

	function generate_children_title_menus($id=0,$url='',$set_active=''){
		$this->notification_counts = array(
			'PENDING_LOAN_APPLICATIONS' => $this->pending_loan_applications,
			'PENDING_APPROVAL_REQUESTS' => $this->pending_withdrawal_approval_requests_count,
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
				$show = 1;
				$specific = 0;
				if($child->enable_menu_for == 1){
					$specific = 1;
					$feature = $child->enabled_disabled_feature;
					if(isset($this->group->$feature) && $this->group->$feature){
						if($child->enabled_or_disabled == 1){//show menu
							$show = 1;
						}else if($child->enabled_or_disabled == 2){//do not show menu
							$show = 0;
						}
					}else{
						if($child->enabled_or_disabled == 1){//show menu
							$show = 0;
						}else if($child->enabled_or_disabled == 2){//do not show menu
							$show = 1;
						}
					}
				}else{
					$specific = 1;
					$show = 1;
				}
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
							if($k == 'PENDING_LOAN_APPLICATIONS'){
								$count_class = "deposits_count ";
							}else if($k == 'PENDING_APPROVAL_REQUESTS'){
								$count_class = " deposits_count ";
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
			                        </a>'.$this->generate_page_title_menus_children($child->id,$url).'
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

	function generate_page_title_menus_children($id=0,$url=''){
		$childrens = $this->get_active_children_links($id);
		$html = '
			<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 48px, 0px);">';
		if($childrens){
			$divider_show_num = 0;
			foreach ($childrens as $key=>$child) {
				$show = 1;
				$specific = 0;
				if($child->enable_menu_for == 1){
					$specific = 1;
					$feature = $child->enabled_disabled_feature;
					if(isset($this->group->$feature) && $this->group->$feature){
						if($child->enabled_or_disabled == 1){//show menu
							$show = 1;
						}else if($child->enabled_or_disabled == 2){//do not show menu
							$show = 0;
						}
					}else{
						if($child->enabled_or_disabled == 1){//show menu
							$show = 0;
						}else if($child->enabled_or_disabled == 2){//do not show menu
							$show = 1;
						}
					}
				}else{
					$specific = 1;
					$show = 1;
				}
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
							if($k == 'PENDING_LOAN_APPLICATIONS'){
								$count_class = "deposits_count ";
							}else if($k == 'PENDING_APPROVAL_REQUESTS'){
								$count_class = " deposits_count ";
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


}?>
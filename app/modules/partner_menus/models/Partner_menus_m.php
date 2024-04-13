<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Partner_menus_m extends MY_Model {

	protected $_table = 'partner_menus';

	protected $special_url_segments = array(
		"/edit/",
		"/view/",
		'/statement/',
		"/listing/page/",
		"/groups_on_trial/pages/",
		"/groups_trial_elapsed/pages/",
		"/locked_groups/pages/",
	);

	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		//$this->install();
	}

	public function install()
	{
		$this->db->query("
		create table if not exists partner_menus(
			id int not null auto_increment primary key,
			`parent_id` blob,
			`name` blob,
			`url` blob,
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
		$this->select_all_secure('partner_menus');
		$this->db->where('id',$id);
		return $this->db->get('partner_menus')->row();
	}

	function insert($input = array(),$skip_value = FALSE)
	{
		return $this->insert_secure_data('partner_menus',$input);
	}

	function generate_page_title($url='')
	{ 
		if(empty($url))
		{
			$url = $this->uri->uri_string();
		}
		$this->select_all_secure('partner_menus');
		$this->db->where($this->dx('url').'="'.$this->db->escape_str($url).'"',NULL,FALSE);

		$res = $this->db->get('partner_menus')->row();

		if($res)
		{

			echo '<i class="'.$res->icon.' font-dark"></i>
			<span class="caption-subject font-dark">'.$res->name.'</span>';
		}
		else
		{
			echo '<i class="fa fa-list-ul font-dark"></i>
			<span class="caption-subject font-dark">{group:template:title}</span>';
		}
	}

	function update($id,$input,$val=FALSE){
    	return $this->update_secure_data($id,'partner_menus',$input);
    }

	function get_all()
	{
		$this->select_all_secure('partner_menus');
		return $this->db->get('partner_menus')->result();
	}

	function count_all_active()
	{
		return $this->db->count_all_results('partner_menus');
	}

	function get_options(){
		$arr = array();
		$this->select_all_secure('partner_menus');
		$menus = $this->db->get('partner_menus')->result();
		foreach($menus as $menu){
			$arr[$menu->id] = $menu->name;
		}
		return $arr;
	}


	function get_parent_links(){
		$this->select_all_secure('partner_menus');
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('partner_menus')->result();	
	}

	function get_active_parent_links(){
		$this->select_all_secure('partner_menus');
		$this->db->where($this->dx('parent_id').' = "0" ',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('partner_menus')->result();	
	}

	function get_active_parent_link($parent_id){
		$this->select_all_secure('partner_menus');
		$this->db->where('id',$parent_id);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('partner_menus')->row();	
	}

	function get_children_links($parent_id = 0){
		$this->select_all_secure('partner_menus');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('partner_menus')->result();	
	}

	function get_active_children_links($parent_id = 0){
		$this->select_all_secure('partner_menus');
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('partner_menus')->result();	
	}

	function has_children($parent_id = 0){
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		return $this->db->count_all_results('partner_menus')>0?TRUE:FALSE;	
	}

	function has_active_children($parent_id = 0){
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->count_all_results('partner_menus')>0?TRUE:FALSE;	
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
	                        $this->partner_menus_m->display_children($link->id); 
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
		$this->select_all_secure('partner_menus');
		$this->db->where($this->dx('url').' = "'.$active_url.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $this->db->get('partner_menus')->row();
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
		$this->select_all_secure('partner_menus');
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		$this->db->where($this->dx('parent_id').' = '.$parent_id,NULL,FALSE);
        $this->db->order_by($this->dx('position').'+0','ASC', FALSE);
		return $this->db->get('partner_menus')->result();
	}

	function generate_header_menus(){
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
                <a href="'.site_url('partner').'" class="btn btn-sm blue">
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
			$menu = $this->db->get('partner_menus')->row();
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
			$parent_menu = $this->db->get('partner_menus')->row();	
			if($parent_menu){
				$this->db->select(array($this->dx('parent_id').' as parent_id '));
				$this->db->where('id',$parent_menu->parent_id);
				$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
				$menu = $this->db->get('partner_menus')->row();
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
			$grand_child_menu = $this->db->get('partner_menus')->row();
			if($grand_child_menu)
			{
				$this->db->select(array($this->dx('parent_id').' as parent_id '));
				$this->db->where('id',$grand_child_menu->parent_id);
				$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
				$parent_menu = $this->db->get('partner_menus')->row();
				if($parent_menu)
				{
					$this->db->select(array($this->dx('parent_id').' as parent_id '));
					$this->db->where('id',$parent_menu->parent_id);
					$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
					$menu = $this->db->get('partner_menus')->row();
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
		$parent_links = $this->get_active_parent_links();
		if($parent_links){
			echo '<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				';
			foreach($parent_links as $link){
				$href = $link->url?site_url($link->url):'javascript:;';
				if(preg_match('/javascript/', $href)){
					$href = 'javascript:;';
				}
				echo'	
						<li class=" nav-item ';
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
			}
			echo '</ul>';
		}
	}

	function generate_side_bar_sub_menu($parent_id = 0){
		$children_links = $this->get_active_children_links($parent_id);
		if($children_links){
			echo '<ul class="sub-menu">';
			foreach($children_links as $child_link){
				$href = $child_link->url?site_url($child_link->url):'javascript:;';
				if(preg_match('/javascript/', $href)){
					$href = 'javascript:;';
				}
							echo '
							<li class=" nav-item ';
							if(uri_string()==$child_link->url&&$child_link->url!==''){
								echo 'active';
							}
							if($this->child_is_active($child_link->id,uri_string())||$this->grand_child_is_active($child_link->id,uri_string())||$this->great_grand_child_is_active($child_link->id,uri_string())){
								echo 'active open';
							}
							echo '">
								<a href="'.$href.'">';
								if($this->has_children($child_link->id)){
								    echo '<span class="arrow "></span>';	
								}
								echo $child_link->name.'</a>';
								$this->generate_side_bar_sub_menu($child_link->id);
							echo '</li>';
			}
			echo '</ul>';
		}
	}

	function get_menu_by_link_url($link_url='')
	{
		$this->select_all_secure('partner_menus');
		$this->db->where($this->dx('url').' = "'.$link_url.'"',NULL,FALSE);
		$this->db->where($this->dx('active').' = "1" ',NULL,FALSE);
		return $menu = $this->db->get('partner_menus')->row();
	}

	function generate_page_quick_action_menus()
	{
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
			if($childrens)
			{
					foreach ($childrens as $child) {
						$href = $child->url?site_url($child->url):'javascript:;';
						if(preg_match('/javascript/', $href)){
							$href = 'javascript:;';
						}
						
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
	               $link_url = $this->uri->segment(1).'/'.$this->uri->segment(2);
	               $menu = $this->get_menu_by_link_url($link_url);
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
										if(preg_match('/javascript/', $href)){
											$href = 'javascript:;';
										}
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
				                                    	echo '<li class="divider"> </li>';
				                                    }
				                        echo '</li>';
									} 

							}
							
							echo '</ul>
				            	</div>
				               </div>';
	               }
	             }
	          }
		}

	}


}?>
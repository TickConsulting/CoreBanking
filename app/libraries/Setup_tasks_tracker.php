<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Setup_tasks_tracker{

	protected $ci;
	public $application_settings;

	public function __construct(){
		$this->ci= & get_instance();
		$this->ci->load->model('setup_tasks/setup_tasks_m');
		$this->ci->load->model('settings/settings_m');
		$this->ci->load->model('members/members_m');
		$this->ci->load->model('groups/groups_m');
		$this->application_settings = $this->ci->settings_m->get_settings()?:'';
	}

	public function set_completion_status($setup_task_slug = '',$group_id = 0,$current_user_id = 0){
		if($setup_task_slug&&$group_id&&$current_user_id){
			if($this->ci->setup_tasks_m->check_if_setup_tracker_tasks_exists($setup_task_slug,$group_id)){
				//do nothing for now
			}else{
				if($setup_task_slug=='add-group-members'){
					if($group = $this->ci->groups_m->get($group_id)){
						$group_members_count = $this->ci->members_m->count_group_members($group_id);
						$size = $group->size;
						$average_size = round($size/2);
						if($group_members_count>=$average_size){
							$input = array(
								'setup_task_slug'=>$setup_task_slug,
								'group_id'=>$group_id,
								'created_on'=>time(),
								'created_by'=>$current_user_id,
							);
							if($result = $this->ci->setup_tasks_m->insert_setup_task_tracker($input)){
								return TRUE;
							}else{
								$this->ci->session->set_flashdata('error','Could not insert setup task');
								return FALSE;
							}
						}else{
							//do nothing for now
							return FALSE;
						}
					}else{
						$this->ci->session->set_flashdata('error','Group not found');
						return FALSE;
					}
				}else if($setup_task_slug=='complete-group-profile'){
					if($this->ci->setup_tasks_m->check_if_setup_tracker_tasks_exists($setup_task_slug,$group_id)){
						//do nothing for now
					}else{
						$input = array(
							'setup_task_slug'=>$setup_task_slug,
							'group_id'=>$group_id,
							'created_on'=>time(),
							'created_by'=>$current_user_id,
						);
						if($result = $this->ci->setup_tasks_m->insert_setup_task_tracker($input)){
							return TRUE;
						}else{
							$this->ci->session->set_flashdata('error','Could not insert setup task');
							return FALSE;
						}
					}
				}else if($setup_task_slug=='create-group-bank-account'){
					if($this->ci->setup_tasks_m->check_if_setup_tracker_tasks_exists($setup_task_slug,$group_id)){
						//do nothing for now
					}else{
						$input = array(
							'setup_task_slug'=>$setup_task_slug,
							'group_id'=>$group_id,
							'created_on'=>time(),
							'created_by'=>$current_user_id,
						);
						if($result = $this->ci->setup_tasks_m->insert_setup_task_tracker($input)){
							return TRUE;
						}else{
							$this->ci->session->set_flashdata('error','Could not insert setup task');
							return FALSE;
						}
					}
				}else if($setup_task_slug=='create-contribution'){
					if($this->ci->setup_tasks_m->check_if_setup_tracker_tasks_exists($setup_task_slug,$group_id)){
						//do nothing for now
					}else{
						$input = array(
							'setup_task_slug'=>$setup_task_slug,
							'group_id'=>$group_id,
							'created_on'=>time(),
							'created_by'=>$current_user_id,
						);
						if($result = $this->ci->setup_tasks_m->insert_setup_task_tracker($input)){
							return TRUE;
						}else{
							$this->ci->session->set_flashdata('error','Could not insert setup task');
							return FALSE;
						}
					}
				}else if($setup_task_slug=='loan-type-setting'){
					if($this->ci->setup_tasks_m->check_if_setup_tracker_tasks_exists($setup_task_slug,$group_id)){
						//do nothing for now
					}else{
						$input = array(
							'setup_task_slug'=>$setup_task_slug,
							'group_id'=>$group_id,
							'created_on'=>time(),
							'created_by'=>$current_user_id,
						);
						if($result = $this->ci->setup_tasks_m->insert_setup_task_tracker($input)){
							return TRUE;
						}else{
							$this->ci->session->set_flashdata('error','Could not insert setup task');
							return FALSE;
						}
					}
				}else{
					$this->ci->session->set_flashdata('warning','Completion status not found');
					return FALSE;
				}
			}
		}else{
			$this->ci->session->set_flashdata('error','Could not set completion status.');
			return FALSE;
		}
	}

	public function unset_completion_status($setup_task_slug = '',$group_id = 0,$current_user_id = 0){
		if($setup_task_slug&&$group_id&&$current_user_id){
			if($this->ci->setup_tasks_m->check_if_setup_tracker_tasks_exists($setup_task_slug,$group_id)){
				//do nothing for now
				if($setup_task_slug=='add-group-members'){
					if($group = $this->ci->groups_m->get($group_id)){
						$group_members_count = $this->ci->members_m->count_group_members($group_id);
						$size = $group->size;
						if($group_members_count<$size){
							if($this->ci->setup_tasks_m->delete_setup_task_tracker_by_setup_task_slug($setup_task_slug,$group_id)){
								return TRUE;
							}else{
								return FALSE;
							}
						}
					}
				}
			}
		}
	}
}
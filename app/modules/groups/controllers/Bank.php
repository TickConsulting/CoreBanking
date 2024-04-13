<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Bank extends Bank_Controller{

  protected $data = array();

    function __construct(){
        parent::__construct();
        $this->load->model('groups/groups_m');
        $this->load->model('bank_accounts/bank_accounts_m');
        $this->load->model('bank_branches/bank_branches_m');
        $this->load->model('banks/banks_m');
        $this->load->model('transaction_alerts/transaction_alerts_m');
        $this->load->model('migrate_m');
   }

    function create(){
        $this->group->id = '';
        $this->group->group_setup_position = '';
        $this->group->group_setup_status = '';
        $this->group->group_offer_loans = '';
        $this->group->avatar = '';
        $this->group->name = '';
        $this->data['system_group_roles'] = $this->investment_groups->system_group_roles;
        $this->data['type_of_groups'] = $this->investment_groups->type_of_groups;
        $this->data['currencies'] = $this->countries_m->get_currency_options();
        $this->data['countries'] = $this->countries_m->get_country_options(1);
        $this->template->set_layout('setup_tasks.html')->title('Onboard New Group')->build('bank/group_setup',$this->data);
    }

    function ajax_search_options(){
        $this->groups_m->get_search_options();

    }

    function search($group_id=''){
        if($group_id){
            $group = $this->groups_m->get($group_id);
            if(!$group){
                redirect('admin/groups/search');
            }
        }
        $this->data['group_id'] = $group_id;
        $this->template->title('Search for a Group')->build('bank/search',$this->data);
    }

    function listing(){
        $this->template->title('Groups Listing')->build('bank/listing',$this->data);
    }

    function delete($group_id = 0,$confirmation_code = '',$redirect = TRUE){
        $confirmation_code = $this->input->get('confirmation_code')?$this->input->get('confirmation_code'):$confirmation_code;
        $referrer  = $this->agent->referrer();
        if($confirmation_code=="thecheese"){
          set_time_limit(0);
          $this->_backup_group($group_id,1);
          if($this->groups_m->delete($group_id)){
            $result = TRUE;
            $members = $this->members_m->get_group_members($group_id);
            foreach ($members as $member) {
              # code...
              if($this->members_m->delete($member->id)){
                $member_group_count = $this->groups_m->count_current_user_groups($member->user_id) + 1;
                if($member_group_count==1){
                  if($this->ion_auth->is_admin($member->user_id)){

                  }else{
                    if($this->users_m->delete($member->user_id)){
                    }else{
                      $result = FALSE;
                    }
                  }
                  
                }
              }else{
                $result = FALSE;
              }
            }
            $database = $this->db->database;
            $tables=$this->db->query("SELECT t.TABLE_NAME AS table_name FROM INFORMATION_SCHEMA.TABLES AS t WHERE t.TABLE_SCHEMA = '".$database."' ")->result_array();    
              $count = 1;
              $ignore_tables = array('equity_bank_transaction_alerts','transaction_alerts','users_groups');
              foreach($tables as $key => $val) {
                $table_name = $val['table_name'];
                  if($this->db->field_exists('group_id',$table_name)){
                    if(in_array($table_name,$ignore_tables)){
                      if($table_name=='transaction_alerts'){
                        $this->migrate_m->unset_group_transaction_alerts($group_id);
                      }
                    }else{
                      if($this->migrate_m->delete_group_data($group_id,$table_name)){
                        //do nothing for now
                      }else{
                        $result = FALSE;
                      }
                    }
                  }
                  if($result){
                    $this->session->set_flashdata('success','All went well during the deletion of the group and group data');
                  }else{
                    $this->session->set_flashdata('warning','Something went wrong during the deletion of the group and group data');
                  }
              }
          }
        }else{
          $this->session->set_flashdata('warning','You entered the wrong confirmation code');
        } 

    
        if($redirect){
          redirect('bank/groups/listing');       
        }
    }

    function _backup_group($group_id = 0,$delete = 0,$reset=0){
        return $this->migrate_m->backup_group($group_id,$delete,$reset);
    }

    function onboarded_group_trends1212($period = 0){
        $from = strtotime(" 1st ".date('M Y',strtotime("-11 months",time())));
        $format = 'M Y';
        $month_format = "M";
        $result_format = 'M';
        $add_one = TRUE;
        if($period){
            if($period == 'last_7'){
                $format = 'Ymd';
                $month_format = "Ymd";
                $from = strtotime("-7 days",time());
                $add_one = FALSE;
                $result_format = 'D';
            }else if ($period == 'last_1') {
                $format = 'Ymd';
                $month_format = "Ymd";
                $add_one = FALSE;
                $result_format = 'd M';
                $from = strtotime(" 1st ".date('M Y',strtotime("-1 month",time())));
            }else if($period == 'last_3'){
                $from = strtotime(" 1st ".date('M Y',strtotime("-2 months",time())));
            }else if($period == 'last_6'){
                $from = strtotime(" 1st ".date('M Y',strtotime("-5 months",time())));
            }else if($period == 'last_10'){
                $format = 'Y';
                $month_format = "Y";
                $result_format = 'Y';
                $from = strtotime(" 1st day of ".date('Y',strtotime("-10 years",time())));
            }
        }
    }

    function onboarded_group_trends($period = 0){
        $from = strtotime(" 1st ".date('M Y',strtotime("-11 months",time())));
        $format = 'M Y';
        $month_format = "M";
        $result_format = 'M';
        $add_one = TRUE;
        $months_array = array();
        $month_arr = array();
        $dayList = array(           
            1 => "Last 7 days",
            2 =>  "Last 1 Month",
            3 =>  "Last 3 Months",
            4 =>  "Last 6 Months",
            5 =>  "Last One Year"
        );
        if($period){
            if($period == 1){
                $format = 'Ymd';
                $month_format = "Ymd";
                $from = strtotime("-7 days",time());
                $add_one = FALSE;
                $result_format = 'D';
                for ($i=0; $i < 7 ; $i++) {
                    $month_arr[] =  date('D',strtotime("-".$i." days"));
                    $months_array[date('dMY',strtotime("-".$i." days"))] = date('dMY',strtotime("-".$i." days"));
                } 
            }else if ($period == 2) {
                $format = 'Ymd';
                $month_format = "Ymd";
                $add_one = FALSE;
                $result_format = 'd M';
                $from = strtotime(" 1st ".date('M Y',strtotime("-1 month",time())));
                $days = (strtotime(date('d-M-Y',time())) - $from)/(24*60*60)+1;
                /*for ($i = 0; $i < 30; $i++){
                    $timestamp = time();
                    $tm = 86400 * $i; // 60 * 60 * 24 = 86400 = 1 day in seconds
                    $tm = $timestamp - $tm;
                    $the_date = date("m/d/Y", $tm);
                    $month_arr[] =  date('d D',$tm);
                    $months_array[date('d D Y',$tm)] = date('d D',$tm);
                }*/
                for ($i=0; $i < 30 ; $i++) {
                    $month_arr[] =  date('d M',strtotime("-".$i." days"));
                    $months_array[date('dDMY',strtotime("-".$i." days"))] = date('dDMY',strtotime("-".$i." days"));
                } 
            }else if($period == 3){
                $from = strtotime(" 1st ".date('M Y',strtotime("-3 months",time())));
                $days = (strtotime(date('d-M-Y',time())) - $from)/(24*60*60)+1;
                for ($i=0; $i < 3 ; $i++) {
                    $month_arr[] =  date('M Y',strtotime("-".$i." month"));
                    $months_array[date('M Y',strtotime("-".$i." month"))] = date('M Y',strtotime("-".$i." month"));
                } 
            }else if($period == 4){
                $from = strtotime(" 1st ".date('M Y',strtotime("-6 months",time())));
                for ($i=0; $i < 6 ; $i++) {
                    $month_arr[] =  date('M Y',strtotime("-".$i." month"));
                    $months_array[date('M Y',strtotime("-".$i." month"))] = date('M Y',strtotime("-".$i." month"));
                } 
            }else if($period == 5){
                $format = 'Y';
                $month_format = "Y";
                $result_format = 'Y';
                $from = strtotime(" 1st day of ".date('Y',strtotime("-1 years",time())));
                for ($i=0; $i < 12 ; $i++) {
                    $month_arr[] =  date('M Y',strtotime("-".$i." month"));
                    $months_array[date('M Y',strtotime("-".$i." month"))] = date('M Y',strtotime("-".$i." month"));
                }
            }
        }
        $user_id = $this->user->id;
        $groups_per_frequency = $this->groups_m->get_groups_bank_staff_onboard_count_per_month_from_date_array($user_id,$from,$period);
        //print_r($groups_per_frequency); die();
        $group_count = array();        
        foreach ($months_array as $key => $month) { 
            $group_count[] = isset($groups_per_frequency[$month])?$groups_per_frequency[$month]:0;
        }
        $_response = array(
            'period'=>isset($dayList[$period])?$dayList[$period]:"Last 7 days",
            'group_sign_ups'=>array_reverse($group_count),
            'months'=>array_reverse($month_arr),              
        );   
        echo json_encode($_response);        
    }

    function get_latest_signups(){
        $date = strtotime("-20 day");
        $user_id = $this->user->id;
        $groups = $this->groups_m->get_latest_bank_staff_group_signups($user_id);
        $groups_array = array();
        $html ='';
        if($groups){           
            foreach ($groups as $key => $group) {
                $html.='
                    <div class="m-widget4 m-widget4--progress">
                        <div class="m-widget4__item py-3">                  
                            <div class="m-widget4__info">
                                <span class="m-widget4__title">
                                    '.ucwords($group->name).'
                                </span>
                            </div>
                            <div class="m-widget4__progress">
                                <div class="m-widget4__progress-wrapper">
                                    <span class="m-widget17__progress-number" id="amount_loaned_this_month">
                                        '.ucwords($group->active_size).'<br>
                                        <span class="m-link m-link--metal m-timeline-3__item-link">'.translate('Members').'</span>
                                    </span>
                                </div>
                            </div>
                            <div class="m-widget4__ext pr-3">
                                <a href="'.site_url('bank/groups/search/'.$group->id).'" class="m-btn m-btn--hover-brand m-btn--pill btn btn-sm btn-secondary">
                                   '.translate('View').'
                                </a>
                            </div>
                        </div>
                    </div>';
                /*$groups_array[] = array(
                    'id'=>$group->id,
                    'name'=>$group->name,
                    'active_size'=>$group->active_size,
                    'size'=>$group->size,
                );*/
            }
        }else{
            $html.='
            <div class="tab-content">
                <div class="tab-pane active show member_contributions_summary" id="m_portlet_tab_contributions">
                    <div class="m-scrollable member_contributions_summary_position" data-scrollable="true" data-max-height="200" style="max-height:200px;">
                       <div id="search-placeholder">
                            <div class="alert m-alert--outline alert-metal">
                                <h4 class="block">'.translate('Applicant List').'</h4>
                                <p>
                                    '.translate('You have no Applicants').'.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
        $response = array(
            'result_code' => 200,
            'message' => "success",
            'html' => $html
        );
        echo json_encode($response);
    }

    function login_as_admin($id=0){
        $id OR redirect('bank/groups/listing');
        $this->session->set_userdata('group_id',$id);
        redirect($this->application_settings->protocol.$this->application_settings->url.'/group');
    }
}

?>
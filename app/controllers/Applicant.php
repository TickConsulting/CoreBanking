<?php if(!defined('BASEPATH')) exit('You are not allowed to view this script');
class Applicant extends Member_Controller{
	protected $data = array();
    function __construct(){
        parent::__construct();
    }


    function uri_not_found(){
        $this->template->title('Page not found')->build('group/uri_not_found');
    }

    function index(){
        $contributions = $this->contributions_m->get_group_contributions_display_reports();
        $contribution_list = '';
        foreach ($contributions as $contribution) {
            if($contribution_list){
                $contribution_list.=','.$contribution->id;
            }else{
                $contribution_list=$contribution->id;
            }
        }
        //$group_member_total_contribution_paid = $this->statements_m->get_group_member_total_paid_by_contribution_array($this->member->id,$this->group->id,$contribution_list);

        $group_member_total_contribution_paid = $this->statements_m->get_group_member_total_paid_by_contribution_array($this->member->id,$this->group->id,$contribution_list);

    	$share_contributions = '0';
    	$savings_contributions = '0';
    	$projects_contribution ='0';
    	$total_payments = 0;
    	if($group_member_total_contribution_paid){
    		foreach ($contributions as $contribution) {
	    		if($contribution->category == 1){
	    			$share_contributions+=isset($group_member_total_contribution_paid[$contribution->id])?$group_member_total_contribution_paid[$contribution->id]:0;
	    		}else if($contribution->category == 2){
	    			$savings_contributions+=isset($group_member_total_contribution_paid[$contribution->id])?$group_member_total_contribution_paid[$contribution->id]:0;
	    		}else if($contribution->category == 5){
	    			$projects_contribution+=isset($group_member_total_contribution_paid[$contribution->id])?$group_member_total_contribution_paid[$contribution->id]:0;
                    $amount = isset($group_member_total_contribution_paid[$contribution->id])?$group_member_total_contribution_paid[$contribution->id]:0;
                    echo $amount." ";
	    		}
	    	}
    	}
    	$this->data['total_member_deposits'] = array_sum($group_member_total_contribution_paid);
    	$this->data['share_contributions'] = $share_contributions;
    	$this->data['savings_contributions'] = $savings_contributions;
    	$this->data['projects_contribution'] = $projects_contribution;
    	$this->data['bank_account'] = $this->bank_accounts_m->get_group_verified_partner_bank_account();
    	$this->data['total_member_loans'] = $this->loans_m->get_total_loan_balances($this->group->id,$this->member->id);
        $this->template->set_layout('member_dashboard.html')->title(translate('Member Dashboard'))->build('member/index',$this->data);
    }

}
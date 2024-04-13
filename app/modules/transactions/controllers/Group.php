<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{
	function __construct(){
        parent::__construct();
        $this->load->model('deposits/deposits_m');
        $this->load->model('withdrawals/withdrawals_m');
    }

    function index(){
        $from = strtotime('first day of this month');
        $to = strtotime('last day of this month');
        $data = array();
        $data['total_group_deposits'] = $this->deposits_m->get_group_total_deposits('',$from,$to);
        $data['total_group_withdrawals'] = $this->withdrawals_m->get_group_total_expenses('',$from,$to);
        $this->template->title('Transactions')->build('group/index',$data);
    }
}
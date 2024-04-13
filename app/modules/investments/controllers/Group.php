<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $data=array();

    public function __construct(){
        parent::__construct();
        $this->load->model('investments_m');
        $this->load->model('assets/assets_m');
        $this->load->model('money_market_investments/money_market_investments_m');

    }

    function index(){
        $this->data = array();
        $stock_value = $this->stocks_m->get_group_current_stocks_value();
        $asset_value = $this->assets_m->get_group_asset_value();
        $this->data['stock_portfolio'] = isset($stock_value['total_value'])?$stock_value['total_value']:0;
        $this->data['asset_portfolio'] = isset($asset_value['asset_total_cost'])?$asset_value['asset_total_cost']:0;
        $this->data['money_market_portfolio'] = $this->money_market_investments_m->get_total_group_money_market_investment_value()?:0;
        $this->data['money_market_interest'] = $this->money_market_investments_m->get_group_total_money_market_interest();
        $this->template->title(translate('Investments'))->build('group/index',$this->data);
    }

}


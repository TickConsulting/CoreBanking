<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends Group_Controller{
 	protected $validation_rules=array(
        array(
                'field' =>  'investment_institution_name',
                'label' =>  'Investment Institution Name',
                'rules' =>  'trim|required',
            ),
        array(
                'field' =>   'investment_date',
                'label' =>   'Investment Date',
                'rules' =>   'trim|required',
            ),
        array(
                'field' =>   'investment_amount',
                'label' =>   'Investment Amount',
                'rules' =>   'trim|required|currency',
            ),
        array(
                'field' =>   'withdrawal_account_id',
                'label' =>   'Account',
                'rules' =>   'trim|required',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Description',
                'rules' =>   'trim',
            ),
    );

	public function __construct(){
        parent::__construct();
        $this->load->model('money_market_investments_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('banks/banks_m');
        $this->load->model('saccos/saccos_m');
        $this->load->model('mobile_money_providers/mobile_money_providers_m');
        $this->load->library('transactions');
    }

    public function index(){
    	$data = array();
        $this->template->title('Money Market Investments')->build('group/index',$data);
    }

    public function create(){
    	$data = array();
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $investment_institution_name = $this->input->post('investment_institution_name');
            $investment_date = strtotime($this->input->post('investment_date'));
            $investment_amount = valid_currency($this->input->post('investment_amount'));
            $withdrawal_account_id = $this->input->post('withdrawal_account_id');
            $description = $this->input->post('description');
            if($this->transactions->create_money_market_investment($this->group->id,$investment_institution_name,$investment_date,$investment_amount,$withdrawal_account_id,$description)){
                $this->session->set_flashdata('success','Money market investment created successfully'); 
            }else{
                $this->session->set_flashdata('error','Something went wrong when creating the money market investment');
            }
            redirect('group/money_market_investments/listing');
        }else{
        	foreach($this->validation_rules as $key => $field){
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['post'] = $post;
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $this->template->title('Create Money Market Investment')->build('group/form',$data);
    }

    public function listing(){
        $data = array(); 
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['to'] = $to;
        $data['from'] = $from;
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        if($this->input->get('generate_excel')==1){
            $filter_parameters = array(
                'to' => $to,
                'from' => $from,
                'money_market_investments' => $this->input->get('money_market_investments'),
                'accounts' => $this->input->get('accounts'),
            );
            $data['posts'] = $this->money_market_investments_m->get_group_money_market_investments($filter_parameters);
            $data['group_currency'] = $this->group_currency;
            $data['group'] = $this->group;
            $json_file = json_encode($data);
            //print_r($json_file);die;
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/money_market_investments/listing',$this->group->name.' List of Money Market Investments'));
            die;
        }
        $this->template->title(translate('List Money Market Investments'))->build('group/listing',$data); 
    }

    public function void($id = 0,$redirect = TRUE){
        $id OR redirect('group/money_market_investments/listing');
        $post = $this->money_market_investments_m->get_group_money_market_investment($id);
        $post OR redirect('group/money_market_investments/listing');
        $withdrawal = $this->withdrawals_m->get_money_market_investment_withdrawal_by_money_market_investment_id($id,$this->group->id);
        if($withdrawal){
            if($this->transactions->void_group_withdrawal($withdrawal->id,$withdrawal,TRUE,$this->group->id)){
                $this->session->set_flashdata('success','Money market investment successfully voided.');
            }else{
                $this->session->set_flashdata('error','Money market investment could not be voided.');
            }
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/money_market_investments/listing');
            }
        }
    }

    public function open_money_market_investment($id=0,$redirect = TRUE){
        $id OR redirect('group/money_market_investments/listing');
        $post = $this->money_market_investments_m->get_group_money_market_investment($id);
        $post OR redirect('group/money_market_investments/listing');

        $input = array(
            'is_closed'=>0,
            'modified_on'=>time(),
        );

        if($this->money_market_investments_m->update($id,$input)){
            $this->session->set_flashdata('success','Money market investment successfully opened.');
        }else{
            $this->session->set_flashdata('error','Money market investment could not be opened.');
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/money_market_investments/listing');
            }
        }
    }

    public function close_money_market_investment($id=0,$redirect = TRUE){
        $id OR redirect('group/money_market_investments/listing');
        $post = $this->money_market_investments_m->get_group_money_market_investment($id);
        $post OR redirect('group/money_market_investments/listing');

        $input = array(
            'is_closed'=>1,
            'modified_on'=>time(),
        );

        if($this->money_market_investments_m->update($id,$input)){
            $this->session->set_flashdata('success','Money market investment successfully closed.');
        }else{
            $this->session->set_flashdata('error','Money market investment could not be closed.');
        }
        if($redirect){
            if($this->agent->referrer()){
                redirect($this->agent->referrer());
            }else{
                redirect('group/money_market_investments/listing');
            }
        }
    }

    public function get_money_market_investment_details(){
        $money_market_investment = $this->money_market_investments_m->get(66);
        $withdrawals = $this->withdrawals_m->get_money_market_investment_withdrawals_by_money_market_investment_id(66,$this->group->id);
        print_r($money_market_investment);
        print_r($withdrawals);
        $input = array(
            'active'=>1
        );
        $this->money_market_investments_m->update(66,$input);
        
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_void'){
            for($i=0;$i<count($action_to);$i++){
                $this->void($action_to[$i],FALSE);
            }
        }
        redirect('group/money_market_investments/listing');
    }



    function top_up($id = 0){
        $id OR redirect('group/money_market_investments/listing');
        $post = $this->money_market_investments_m->get_group_money_market_investment($id);
        $post OR redirect('group/money_market_investments/listing');
        $post->is_closed != 1 OR redirect('group/money_market_investments/listing');
        $validation_rules=array(
            array(
                    'field' =>  'top_up_description',
                    'label' =>  'Top Up Description',
                    'rules' =>  'trim',
                ),
            array(
                    'field' =>   'top_up_date',
                    'label' =>   'Top Up Date',
                    'rules' =>   'trim|required',
                ),
            array(
                    'field' =>   'top_up_amount',
                    'label' =>   'Top Up Amount',
                    'rules' =>   'trim|required|currency',
                ),
            array(
                    'field' =>   'top_up_withdrawal_account_id',
                    'label' =>   'Account',
                    'rules' =>   'trim|required',
                ),
        );
        // print_r($post); die;
        // $this->form_validation->set_rules($validation_rules);
        // if($this->form_validation->run()){
        //     $top_up_date = strtotime($this->input->post('top_up_date'));
        //     $top_up_amount = valid_currency($this->input->post('top_up_amount'));
        //     $top_up_withdrawal_account_id = $this->input->post('top_up_withdrawal_account_id');
        //     $top_up_description = $this->input->post('top_up_description');
        //     if($this->transactions->top_up_money_market_investment($this->group->id,$id,$top_up_date,$top_up_amount,$top_up_withdrawal_account_id,$top_up_description)){
        //         $this->session->set_flashdata('success','Money market investment topped up successfully.');
        //     }else{
        //         $this->session->set_flashdata('error','Something went wrong while topping up the investment.');
        //     }
        //     redirect('group/money_market_investments/listing');
        // }else{
            // foreach($validation_rules as $key => $field){
            //     $post->$field['field'] = set_value($field['field']);
            // }
        // }
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        $data['withdrawal_account_options'] = $this->accounts_m->get_active_group_account_options(FALSE,'','','',TRUE);
        // $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['post'] = $post;
        $this->template->title('Top Up Money Market Investments')->build('group/top_up',$data); 
    }

    function cash_in($id = 0){
        $data = array();
        $id OR redirect('group/money_market_investments/listing');
        $post = $this->money_market_investments_m->get_group_money_market_investment($id);
        $post OR redirect('group/money_market_investments/listing');
        $post->is_closed != 1 OR redirect('group/money_market_investments/listing');
        $data['post'] = $post;     
        $data['banks'] = $this->banks_m->get_group_bank_options();
        $data['saccos'] = $this->saccos_m->get_group_sacco_options();
        $data['mobile_money_providers'] = $this->mobile_money_providers_m->get_group_mobile_money_provider_options();
        // $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',TRUE);
        $data['account_options'] = $this->accounts_m->get_active_group_account_options('','','','',FALSE);
        $data['withdrawal_account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $this->template->title('Cash in '.$post->investment_institution_name.' Money Market Investment')->build('group/cash_in',$data); 
    }

    function cash_ins(){
        $data = array();
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $data['to'] = $to;
        $data['from'] = $from;
        $data['account_options'] = $this->accounts_m->get_group_account_options(FALSE);
        $data['money_market_investment_options'] = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        if($this->input->get('generate_excel')==1){
            $filter_parameters = array(
                'to' => $to,
                'from' => $from,
                'money_market_investments' => $this->input->get('money_market_investments'),
                'accounts' => $this->input->get('accounts'),
            );
            $data['posts'] = $this->deposits_m->get_group_money_market_investment_cash_in_deposits($this->group->id,$filter_parameters);
            $data['group_currency'] = $this->group_currency;
            $data['group'] = $this->group;
            $json_file = json_encode($data);
            //print_r($json_file);
            print_r($this->curl_post_data->curl_post_json_excel($json_file,'https://excel.chamasoft.com/money_market_investments/cash_ins',$this->group->name.' List of Money Market Investments cash Ins'));
            die;
        }
        $this->template->title(translate('Money Market Investment Cash Ins'))->build('group/cash_ins',$data);
    }


    function ajax_money_market_market_investments_listing(){
        $past_money_market_investment = $this->money_market_investments_m->get_group_back_dated_past_money_market_investment();
        if($past_money_market_investment){
            $past_money_market_investment_amount = $past_money_market_investment->investment_amount;
            $cashed_in_money_market_investment_amount = $past_money_market_investment->cash_in_amount;
        }else{
            $past_money_market_investment_amount = 0;
            $cashed_in_money_market_investment_amount = 0;
        }
        $ongoing_money_market_investment = $this->money_market_investments_m->get_group_back_dated_ongoing_money_market_investment();
        if($ongoing_money_market_investment){
            $ongoing_money_market_investment = $ongoing_money_market_investment->investment_amount;
        }else{
            $ongoing_money_market_investment = 0;
        }
        echo '<h4>Back-dated Money Market Investments</h4>';
            echo '
            <hr/>
            <h5>Recalled/Cashed In Money Market Investments</h5>
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Decription
                        </th>
                        <th class="text-right">
                            Total Amount Invested ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Total Amount Recalled/Cashed in  ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        echo '
                        <tr>
                            <td>1</td>
                            <td>Recalled/Cashed In Money Market Investments Totals</td>
                            <td class="text-right"> 
                                '.number_to_currency($past_money_market_investment_amount).'
                            </td>
                            <td class="text-right"> 
                                '.number_to_currency($cashed_in_money_market_investment_amount).'
                            </td>
                        </tr>'; 
                echo '
                </tbody>
            </table>';
            echo '
            <h5>Ongoing Active Money Market Investments</h5>
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Decription
                        </th>
                        <th class="text-right">
                            Total Amount Invested ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        echo '
                        <tr>
                            <td>1</td>
                            <td>Ongoing Money Market Investments Totals</td>
                            <td class="text-right"> 
                                '.number_to_currency($ongoing_money_market_investment).'
                            </td>
                        </tr>'; 
                echo '
                </tbody>
            </table>';
    }

    function ajax_money_market_market_investments_form(){
        $group_cut_off_date = $this->transaction_statements_m->get_group_cut_off_date();
        $past_money_market_investment = $this->money_market_investments_m->get_group_back_dated_past_money_market_investment();
        if($past_money_market_investment){
            $past_money_market_investment_amount = $past_money_market_investment->investment_amount;
            $cashed_in_money_market_investment_amount = $past_money_market_investment->cash_in_amount;
        }else{
            $past_money_market_investment_amount = 0;
            $cashed_in_money_market_investment_amount = 0;
        }
        $ongoing_money_market_investment = $this->money_market_investments_m->get_group_back_dated_ongoing_money_market_investment();
        if($ongoing_money_market_investment){
            $ongoing_money_market_investment = $ongoing_money_market_investment->investment_amount;
        }else{
            $ongoing_money_market_investment = 0;
        }
        echo '
        <div class="alert alert-info">
            <strong>Information!</strong> Enter the amount the group <strong>had</strong> invested and recalled/cashed in total and active money market investments in total as at '.timestamp_to_date($group_cut_off_date->cut_off_date).'
        </div>';
        echo '<h4>Back-dated Money Market Investments</h4>';
            echo '
            <hr/>
            <h5>Recalled/Cashed In Money Market Investments</h5>
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Decription
                        </th>
                        <th class="text-right">
                            Total Amount Invested ('.$this->group_currency.')
                        </th>
                        <th class="text-right">
                            Total Amount Recalled/Cashed in  ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        echo '
                        <tr>
                            <td>1</td>
                            <td>Recalled/Cashed In Money Market Investments Totals</td>
                            <td class="text-right"> 
                                '.form_input('past_money_market_investment',$past_money_market_investment_amount," class='form-control currency'").'
                            </td>
                            <td class="text-right"> 
                                '.form_input('cashed_in_money_market_investment',$cashed_in_money_market_investment_amount," class='form-control currency'").'
                            </td>
                        </tr>'; 
                echo '
                </tbody>
            </table>';
            echo '
            <h5>Ongoing Active Money Market Investments</h5>
            <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed ">
                <thead>
                    <tr>
                        <th width="8px">
                            #
                        </th>
                        <th>
                            Decription
                        </th>
                        <th class="text-right">
                            Total Amount Invested ('.$this->group_currency.')
                        </th>
                    </tr>
                </thead>
                <tbody>';
                        echo '
                        <tr>
                            <td>1</td>
                            <td>Ongoing Money Market Investments Totals</td>
                            <td class="text-right"> 
                                '.form_input('ongoing_money_market_investment',$ongoing_money_market_investment," class='form-control currency'").'
                            </td>
                        </tr>'; 
                echo '
                </tbody>
            </table>';
    }

    function fix_money_market_investments(){

        $posts = $this->money_market_investments_m->get_group_money_market_investments();

        foreach($posts as $post):
            if($post->withdrawal_account_id == 'bank-bank-1970'){
                $input = array(
                    'withdrawal_account_id' => 'bank-1970',
                    'modified_on' => time()
                );
                $this->money_market_investments_m->update($post->id,$input);

            }
        endforeach;

        //print_r($posts);
    }

    function open_closed_money_market_investments($id = 0){
        if($id){
            $post = $this->money_market_investments_m->get($id);
            //print_r($post); die();
            if($post){
                $input = array(
                    'is_closed'=>0,
                    'modified_on'=>time(),
                );
                if($result = $this->money_market_investments_m->update($post->id,$input)){
                    echo "TRUE";
                }else{
                    echo " FALSE";
                }
        }
        }else{
            echo "Money market id is required";
        }

        //print_r($posts);
    }
}
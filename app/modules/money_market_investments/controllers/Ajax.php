<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();

    protected $validation_rules=array(
        array(
                'field' =>  'investment_institution_name',
                'label' =>  'Investment Institution Name',
                'rules' =>  'xss_clean|trim|required',
            ),
        array(
                'field' =>   'investment_date',
                'label' =>   'Investment Date',
                'rules' =>   'xss_clean|trim|required',
            ),
        array(
                'field' =>   'investment_amount',
                'label' =>   'Investment Amount',
                'rules' =>   'xss_clean|trim|required|currency',
            ),
        array(
                'field' =>   'withdrawal_account_id',
                'label' =>   'Account',
                'rules' =>   'xss_clean|trim|required',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Description',
                'rules' =>   'xss_clean|trim',
            ),
    );

    function __construct(){
        parent::__construct();
        $this->load->model('money_market_investments_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('deposits/deposits_m');
    }

    function cash_in(){
        if($_POST){
            $post = $this->money_market_investments_m->get_group_money_market_investment($_POST['id']);
            if($post){
                if($post->is_closed != 1){
                    $validation_rules=array(
                        array(
                                'field' =>   'cash_in_date',
                                'label' =>   'Cash in Date',
                                'rules' =>   'xss_clean|trim|required',
                            ),
                        array(
                                'field' =>   'cash_in_amount',
                                'label' =>   'Cash In Amount',
                                'rules' =>   'xss_clean|trim|required|currency',
                            ),
                        array(
                                'field' =>   'deposit_account_id',
                                'label' =>   'Account',
                                'rules' =>   'xss_clean|trim|required',
                            ),
                    );
                    $this->form_validation->set_rules($validation_rules);
                    if($this->form_validation->run()){
                        $cash_in_date = strtotime($this->input->post('cash_in_date'));
                        $cash_in_amount = valid_currency($this->input->post('cash_in_amount'));
                        $deposit_account_id = $this->input->post('deposit_account_id');
                        if($this->transactions->record_money_market_investment_cash_in_deposit($this->group->id,$_POST['id'],$cash_in_date,$deposit_account_id,$cash_in_amount)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Money market investment cash in recorded successfully',
                                'refer' => site_url('group/money_market_investments/listing'),
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Money market investment cash in was not recorded successfully.',
                                'validation_errors' => '',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'There are some errors on the form. Please review and try again.',
                            'validation_errors' => $this->form_validation->error_array(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Money market investment is closed, cannot record a cash in.',
                        'validation_errors' => '',
                    );
                }

            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Money market investment records not found.',
                    'validation_errors' => '',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'No data submitted for processing',
                'validation_errors' => '',
            );
        }
        echo json_encode($response);
    }

    function top_up(){
        if($_POST){
            $post = $this->money_market_investments_m->get_group_money_market_investment($_POST['id']);
            if($post){
                if($post->is_closed != 1){
                    $validation_rules=array(
                        array(
                            'field' =>  'top_up_description',
                            'label' =>  'Top Up Description',
                            'rules' =>  'trim',
                        ),
                        array(
                            'field' =>   'investment_date',
                            'label' =>   'Top Up Date',
                            'rules' =>   'trim|required',
                        ),
                        array(
                            'field' =>   'top_up_amount',
                            'label' =>   'Top Up Amount',
                            'rules' =>   'trim|required|currency',
                        ),
                        array(
                            'field' =>   'withdrawal_account_id',
                            'label' =>   'Account',
                            'rules' =>   'trim|required',
                        ),
                    );
                    $this->form_validation->set_rules($validation_rules);
                    if($this->form_validation->run()){
                        $investment_date = strtotime($this->input->post('investment_date'));
                        $top_up_amount = valid_currency($this->input->post('top_up_amount'));
                        $withdrawal_account_id = $this->input->post('withdrawal_account_id');
                        $top_up_description = $this->input->post('top_up_description');
                        if($this->transactions->top_up_money_market_investment($this->group->id,$_POST['id'],$investment_date,$top_up_amount,$withdrawal_account_id,$top_up_description)){
                            $response = array(
                                'status' => 1,
                                'message' => 'Money market investment top up recorded successfully',
                                'refer' => site_url('group/money_market_investments/listing'),
                            );
                        }else{
                            $response = array(
                                'status' => 0,
                                'message' => 'Money market investment top up was not recorded successfully.',
                                'validation_errors' => '',
                            );
                        }
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'There are some errors on the form. Please review and try again.',
                            'validation_errors' => $this->form_validation->error_array(),
                        );
                    }
                }else{
                    $response = array(
                        'status' => 0,
                        'message' => 'Money market investment is closed, cannot record a cash in.',
                        'validation_errors' => '',
                    );
                }

            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Money market investment records not found.',
                    'validation_errors' => '',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'No data submitted for processing',
                'validation_errors' => '',
            );
        }
        echo json_encode($response);
    }


    function get_money_market_investments_listing(){
        
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'to' => $to,
            'from' => $from,
            'money_market_investments' => $this->input->get('money_market_investments'),
            'accounts' => $this->input->get('accounts'),
        );
        $total_rows = $this->money_market_investments_m->count_group_money_market_investments($filter_parameters);
        $pagination = create_pagination('group/money_market_investments/listing/pages', $total_rows,50,5,TRUE);
        $account_options = $this->accounts_m->get_group_account_options(FALSE);
        $posts = $this->money_market_investments_m->limit($pagination['limit'])->get_group_money_market_investments($filter_parameters);
        if(!empty($posts)){
            echo form_open('group/money_market_investments/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Money Market Investments</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
                echo '
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th width=\'2%\' nowrap>
                                <label class="m-checkbox">
                                    <input type="checkbox" name="check" value="all" class="check_all">
                                    <span></span>
                                </label>
                            </th>
                            <th width=\'2%\' nowrap>
                                #
                            </th>
                            <th nowrap>
                                Investment Date
                            </th>
                            <th nowrap>
                                Investment Details
                            </th>
                            <th nowrap>
                                Investing Account
                            </th>
                            <th class=\'text-right\' nowrap>
                                Amount ('.$this->group_currency.')
                            </th>
                            <th nowrap>
                                Status
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); foreach($posts as $post):
                        echo '
                            <tr>
                                <td>
                                    <label class="m-checkbox">
                                        <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>'.($i+1).'</td>
                                <td>'.timestamp_to_date($post->investment_date).'</td>
                                <td>'.$post->investment_institution_name;

                                    if($post->description){
                                        echo $post->description?'<br/>'.$post->description:'';
                                    }
                                echo '</td>
                                <td>'.$account_options[$post->withdrawal_account_id].'</td>
                                <td class=\'text-right\'>
                                    '.number_to_currency($post->investment_amount).'</td>
                                <td nowrap>';
                                    if($post->active){
                                        if($post->is_closed){
                                            $final_amount = isset($post->total_cash_in_amount)?number_to_currency($post->total_cash_in_amount):number_to_currency($post->cash_in_amount);
                                            echo "<span class='m-badge m-badge--default m-badge--wide'>Closed</span><br/>";
                                            echo '<small><strong> Cash In Date : </strong>'.timestamp_to_date($post->cash_in_date)."</small><br/>";
                                            echo '<small><strong>Last Cash In Amount : </strong>'.number_to_currency($post->cash_in_amount).'</small><br/>';
                                            echo '<small><strong> Total Cash In Amount : </strong>'.number_to_currency($final_amount).'</small>';
                                        }else{
                                            echo "<span class='m-badge m-badge--info m-badge--wide'>Active</span>";
                                        }
                                    }
                                
                            echo '
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="'.site_url('group/money_market_investments/void/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to void money market investment?">
                                            <span>
                                                <i class="la la-trash"></i>
                                                <span>
                                                    Void &nbsp;&nbsp;
                                                </span>
                                            </span>
                                        </a>
                            ';
                            if($post->active){
                                if($post->is_closed){
                                    echo '
                                        <button type="button" class="btn btn-sm btn-danger dropdown-toggle dropdown-toggle-split more_actions_toggle action_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                            <span class="sr-only">More actions..</span>
                                        </button>
                                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(120px, 31px, 0px);">
                                            <a href="'.site_url('group/money_market_investments/open_money_market_investment/'.$post->id).'" class="dropdown-item">
                                                Open Investment
                                            </a>
                                        </div>
                                    </div>';
                                }else{
                                    echo '
                                        <button type="button" class="btn btn-sm btn-danger dropdown-toggle dropdown-toggle-split more_actions_toggle action_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                            <span class="sr-only">More actions..</span>
                                        </button>
                                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(120px, 31px, 0px);">
                                            <a href="'.site_url('group/money_market_investments/top_up/'.$post->id).'" class="dropdown-item">
                                                Top Up Investment
                                            </a>

                                            <a href="'.site_url('group/money_market_investments/cash_in/'.$post->id).'" class="dropdown-item">
                                                Cash In Investment
                                            </a>

                                            <a href="'.site_url('group/money_market_investments/close_money_market_investment/'.$post->id).'" class="dropdown-item">
                                                Close Investment
                                            </a>
                                        </div>
                                    </div>';
                                }
                            }
                                    echo '
                                </td>
                            </tr>';
                            $i++;
                            endforeach;
                        echo ' 
                    </tbody>
                </table>
                <div class="clearfix"></div>
                <div class="row col-md-12">';
                 
                if( ! empty($pagination['links'])): 
                echo $pagination['links']; 
                endif; 

                echo '
                </div>
                <div class="clearfix"></div>';
                if($posts):
                    echo '
                    <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
                endif;
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show mt-2" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate('No money market investments to display').'.
                </div>
            ';
        }

    }


    function get_money_market_investment_cash_ins_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'to' => $to,
            'from' => $from,
            'money_market_investments' => $this->input->get('money_market_investments'),
            'accounts' => $this->input->get('accounts'),
        );
        $total_rows = $this->deposits_m->count_group_money_market_investment_cash_in_deposits($this->group->id,$filter_parameters);
        $pagination = create_pagination('group/money_market_investments/cash_ins/pages', $total_rows,50,5,TRUE);
        $account_options = $this->accounts_m->get_group_account_options(FALSE);
        $money_market_investment_options = $this->money_market_investments_m->get_group_money_market_investment_options($this->group->id,$this->group_currency);
        $posts = $this->deposits_m->limit($pagination['limit'])->get_group_money_market_investment_cash_in_deposits($this->group->id,$filter_parameters);
        if(!empty($posts)){
            echo form_open('group/deposits/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Money Market Investment Cash Ins</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
                echo '
                    <table class="table m-table m-table--head-separator-primary">
                        <thead>
                            <tr>
                                <th width=\'2%\' nowrap>
                                    <label class="m-checkbox">
                                        <input type="checkbox" name="check" value="all" class="check_all">
                                        <span></span>
                                    </label>
                                </th>
                                <th width=\'2%\' nowrap>
                                    #
                                </th>
                                <th nowrap>
                                    Cash In Date
                                </th>
                                <th nowrap>
                                    Name
                                </th>
                                <th nowrap>
                                    Account
                                </th>
                                <th class=\'text-right\' nowrap>
                                    Cash In Amount  ('.$this->group_currency.')
                                </th>
                                <th nowrap>
                                    &nbsp;
                                </th>
                            </tr>
                        </thead>
                        <tbody>';
                            $i = $this->uri->segment(5, 0); foreach($posts as $post):
                            echo '
                                <tr>
                                    <td>
                                        <label class="m-checkbox">
                                            <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                            <span></span>
                                        </label>
                                    </td>
                                    <td>'.($i+1).'</td>
                                    <td>'.timestamp_to_date($post->deposit_date).'</td>
                                    <td>'.$money_market_investment_options[$post->money_market_investment_id].'</td>
                                    <td>'.$account_options[$post->account_id].'</td>
                                    <td class=\'text-right\'>
                                        '.number_to_currency($post->amount).'
                                    </td>
                                    <td>
                                        <a href="'.site_url('group/deposits/void/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="">
                                            <span>
                                                <i class="la la-trash"></i>
                                                <span>
                                                    Void &nbsp;&nbsp;
                                                </span>
                                            </span>
                                        </a>
                                    </td>
                                </tr>';
                                $i++;
                                endforeach;
                            echo '
                        </tbody>
                    </table>
                    <div class="clearfix"></div>
                    <div class="row col-md-12">
                ';
                if( ! empty($pagination['links'])): 
                echo $pagination['links']; 
                endif; 
                echo '
                </div>
                <div class="clearfix"></div>';
                if($posts):
                    echo '
                    <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_void\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash-o\'></i> Bulk Void</button>';
                endif;
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info fade show mt-2" role="alert">
                    <strong>'.translate('Sorry').'!</strong> '.translate('No money market cash ins to display').'.
                </div>
            ';
        } 
    }

    function create(){
        $response = array();
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $investment_institution_name = $this->input->post('investment_institution_name');
            $investment_date = strtotime($this->input->post('investment_date'));
            $investment_amount = valid_currency($this->input->post('investment_amount'));
            $withdrawal_account_id = $this->input->post('withdrawal_account_id');
            $description = $this->input->post('description');
            if($this->transactions->create_money_market_investment($this->group->id,$investment_institution_name,$investment_date,$investment_amount,$withdrawal_account_id,$description)){
                $response = array(
                    'status' => 1,
                    'message' => 'Money market investment created successfully',
                    'refer'=>site_url('group/money_market_investments/listing')
                ); 
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Something went wrong when creating the money market investment',
                ); 
            }
        }else{
            $post = array();
            $form_errors = $this->form_validation->error_array();
            foreach ($form_errors as $key => $value) {
                $post[$key] = $value;
            }
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $post,
            );
        }
        echo json_encode($response);
    }
}
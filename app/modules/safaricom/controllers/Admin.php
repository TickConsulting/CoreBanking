<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends Admin_Controller
{
    protected $data = array();
    protected $rules = array();
    public $client;

    public $c2b_request_sample = array(
        'customer' => array(
            'phone' => '',
            'user_id' => '',
        ),
        'transaction' => array(
            'amount' => '',
            'description' => '',
            'shortcode' => '',
            'command_id' => '',
        ),
        'result' => array(
            'callback_url' => '',
        ),
        'credentials' => array(
            'username' => '',
            "password" => '',
        ),
        'group' => array(
            'group_id' => '',
            'group_name' => ''
        )
    );

    public $b2b_transaction_request = array(
        'sender' => array(
            'sender_shortcode' => '',
            'sender_name' => '',
        ),
        'receiver' => array(
            'receiver_shortcode' => '',
            'receiver_name' => '',
        ),
        'transaction' => array(
            'amount' => '',
            'command_id' => '',
            'description' => '',
        ),
        'credentials' => array(
            'username' => '',
            'password' => '',
        ),
        'result' => array(
            'callback_url' => '',
        ),
    );

    protected $configuration_rules = array(
        array(
            'label' => 'Username',
            'field' => 'username',
            'rules' => 'required|trim|callback__is_unique_username',
        ),
        array(
            'label' => 'Password',
            'field' => 'password',
            'rules' => 'required',
        ),
        array(
            'label' => 'Shortcode',
            'field' => 'shortcode',
            'rules' => 'required|numeric',
        ),
        array(
            'label' => 'Access Token',
            'field' => 'access_token',
            'rules' => '',
        )
    );

    function __construct()
    {
        parent::__construct();
        $this->load->library('mailer');
        $this->load->library('transactions');
        $this->load->model('safaricom_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('loans/loans_m');
        // $this->load->model('tariffs/tariffs_m');
    }
    function index()
    {
    }

    function _is_unique_username()
    {
        $username = $this->input->post('username');
        $id = $this->input->post('id');
        $shortcode = $this->input->post('shortcode');
        if ($this->equity_bank_m->is_unique_username($username, $id, $shortcode)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('_is_unique_username', 'Kindly use a different username');
            return FALSE;
        }
    }

    function b2c_requests()
    {
        $total_rows = $this->safaricom_m->count_all_b2c_requests();
        $pagination = create_pagination('admin/safaricom/b2c_requests/pages', $total_rows, 50, 5, TRUE);
        $this->data['posts'] = $this->safaricom_m->limit($pagination['limit'])->get_all_b2c_requests();
        $this->data['pagination'] = $pagination;
        $this->template->title('B2C Requests')->build('admin/b2c_requests', $this->data);
    }

    function view_b2c_request($id = 0)
    {
        $id or redirect('admin/safaricom/b2c_requests');
        $post = $this->safaricom_m->get_b2c_request($id);
        if (!$post) {
            $this->session->set_flashdata('error', 'Sorry the request could not be found');
            redirect('admin/safaricom/b2c_requests');
            return FALSE;
        }
        print_r('<pre>');
        print_r($post);
        print_r('</pre>');
        die;
        $this->data['post'] = $post;
        $this->template->title('Safaricom B2C Request')->build('admin/view_request', $this->data);
    }

    function forward_b2c_request($id = 0)
    {
        $id or redirect('admin/safaricom/b2c_requests');
        $post = $this->safaricom_m->get_b2c_request($id);
        if (!$post) {
            $this->session->set_flashdata('error', 'Sorry the request could not be found');
            redirect('admin/safaricom/b2c_requests');
            return FALSE;
        }
        $this->transactions->send_customer_disbursement_callback($post);
    }

    function create_b2c_request()
    {
        $response = '';
        $post = '';
        if (isset($_POST['submit'])) {
            $b2c_request = $this->input->post('request');
            if (json_decode($b2c_request) == false) {
                $response = array(
                    'response' =>
                    array(
                        'status' => 1,
                        'description' => 'Json format is wrong',
                    ),
                    'request' => array(),
                );
                $this->data['response'] = json_encode($response);
            } else {
                $response = $this->curl->post_json($b2c_request, site_url('safaricom/b2c_withdrawal_request'));
                if ($response) {
                    $response = json_decode($response);
                    if ($response) {
                        if (isset($response->response->request_id)) {
                            $id = array(
                                'request_id' => $response->response->request_id,
                            );
                            $callback = $this->curl->post_json(json_encode($id), site_url('safaricom/b2c_get_call_back_status'));
                            if ($callback) {
                                $callback = array('callback' => (array)json_decode($callback));
                                $response = (array)$response;
                                $response = (json_encode($response + $callback, JSON_PRETTY_PRINT));
                            } else {
                                $response = (json_encode($response, JSON_PRETTY_PRINT));
                            }
                        } else {
                            $response = (json_encode($response, JSON_PRETTY_PRINT));
                        }
                    } else {
                        die("invalid response");
                    }
                } else {
                    die("no response");
                }
            }
            $post = $b2c_request;
        }
        $this->data['response'] = $response;
        $this->data['request_sample'] = $post ?: json_encode($this->c2b_request_sample, JSON_PRETTY_PRINT);
        $this->template->title('Safaricom B2C Request')->build('admin/create_request', $this->data);
    }

    function reverse($id = 0)
    {
        $id or redirect('admin/safaricom/c2b_requests');
        $confirmation_code = $this->input->get_post('confirmation_code');
        if ($this->ion_auth->login($this->user->phone, $confirmation_code)) {
            $post = $this->safaricom_m->get_c2b_request($id);
            if ($post) {
                $reference_number = $post->reference_number;
                $amount = $post->amount;
                $charge_amount = 0;
                $account = $this->accounts_m->get_account_by_account_number($post->account);
                if (!$account) {
                    $receipt_number = $post->transaction_id;
                    $request = $this->safaricom_m->get_stk_payment_by_transaaction_id($receipt_number);
                    $reference_number = $request->reference_number;
                    $charge_amount = $request->charge;
                    if ($request) {
                        $account = $this->accounts_m->get($request->account_id);
                    }
                }
                $amount = $amount - $charge_amount;
                if ($payment_request_response = $this->transactions->reverse_transaction($reference_number, $amount, $account->id,$post)) {
                    //$response = $payment_request_response;
                    print_r($payment_request_response);
                    die;
                    redirect($this->agent->referrer());
                } else {
                    $this->session->set_flashdata('error', $this->session->flashdata('error'));
                    redirect('admin/safaricom/c2b_requests');
                    return FALSE;
                }
            } else {
                $this->session->set_flashdata('error', 'Could not find transaction');
                redirect('admin/safaricom/c2b_requests');
                return FALSE;
            }
        } else {
            $this->session->set_flashdata('error', 'User login failed');
            redirect('admin/safaricom/c2b_requests');
        }
    }

    function ajax_get_request_status()
    {
        $request_id = $this->input->post('request_id');
        $response = $this->input->post('response');
        $request_type = $this->input->post('request_type');
        if ($request_id && $request_type) {
            $id = array(
                'request_id' => $request_id,
            );
            if ($request_type == 1) {
                $callback = $this->curl->post_json(json_encode($id), site_url('safaricom/b2c_get_call_back_status'));
            } else {
                $callback = $this->curl->post_json(json_encode($id), site_url('safaricom/b2b_get_call_back_status'));
            }
            if ($callback) {
                $callback = (array)json_decode($callback);
                $response = json_decode($response);
                if ($response) {
                    $request = (array)$response->request;
                    $response_1 = (array)$response->response;

                    $result = array('request' => $request, 'response' => $response_1, 'callback' => $callback);
                    echo json_encode($result, JSON_PRETTY_PRINT);
                } else {
                    echo 'error';
                }
            } else {
                echo 'error';
            }
        }
    }


    function upload_transactions()
    {
        $post = new StdClass();
        $transactions = array();
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        if ($this->input->post('import')) {
            $file_name = $_FILES['postal_code_list']['name'];
            if ($file_name) {
                $directory = './uploads/files/csvs/postal_codes';
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, TRUE);
                }
                $config['upload_path'] = FCPATH . 'uploads/files/csvs/postal_codes';
                $config['allowed_types'] = 'xls|xlsx|csv';
                $config['max_size'] = '1024';
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('postal_code_list')) {
                    $upload_data = $this->upload->data();
                    $file_path = $upload_data['full_path'];
                    $this->load->library('Excel');
                    $excel_sheet = new PHPExcel();
                    if (file_exists($file_path)) {
                        $file_type = PHPExcel_IOFactory::identify($file_path);
                        $excel_reader = PHPExcel_IOFactory::createReader($file_type);
                        $excel_book = $excel_reader->load($file_path);
                        $sheet = $excel_book->getSheet(0);
                        $columns = array();
                        for ($i = 0; $i < 20; $i++) {
                            $value = $sheet->getCellByColumnAndRow($i, 7)->getValue();
                            if (trim($value)) {
                                $columns[$i] = generate_slug($value);
                            }
                        }
                        if ($columns) {
                            $highestRow = $sheet->getHighestRow();
                            $transactions = array();
                            $num_re = 0;
                            for ($row = 8; $row <= $highestRow; $row++) {
                                foreach ($columns as $key => $value) {
                                    $text = str_replace('\\', ' ', $sheet->getCellByColumnAndRow($key, $row)->getValue());
                                    //echo $text.'<br>';
                                    $title = str_replace('\\', ' ', $sheet->getCellByColumnAndRow($key, 6)->getValue());
                                    $withdrawn_amount = str_replace('\\', ' ', $sheet->getCellByColumnAndRow(6, $row)->getValue()) ?: 100;

                                    if (abs($withdrawn_amount) < 30) {
                                        continue;
                                    }

                                    if ($title == 'Transaction Status') {
                                        if ($text == 'Completed') {
                                            $transactions[$row][$value] = $text;
                                        }
                                    } else {
                                        $transactions[$row][$value] = $text;
                                    }
                                }
                                //unset($transactions[$num_re]);
                            }
                            $this->data['shortcode'] = str_replace('\\', ' ', $sheet->getCellByColumnAndRow(1, 2)->getValue());
                            // print_r('<pre>');
                            // print_r($transactions);
                            // print_r('</pre>');die;
                            if ($transactions) {
                                $this->data['columns'] = $columns;
                                $this->data['ignore_columns'] = array(
                                    'initiation-time',
                                    'details',
                                    'transaction-status',
                                    'balance',
                                    'balance-confirmed',
                                    'reason-type',
                                    'linked-transaction-id'
                                );
                            } else {
                                $this->session->set_flashdata('info', $file_name . ' file does not have any transactions to import');
                            }
                        } else {
                            $this->session->set_flashdata('error', $file_name . ' file does not have the correct format');
                        }
                    } else {
                        $this->session->set_flashdata('error', $file_name . ' file was not found');
                    }
                } else {
                    $this->session->set_flashdata('error', $file_name . ' file type is not allowed');
                }
            } else {
                $this->session->set_flashdata('error', 'You did not upload any file. Kindly upload and try again.');
            }
            if (!$transactions) {
                redirect('admin/safaricom/upload_transactions');
            }
        }
        $this->data['transactions'] = $transactions;
        $this->data['post'] = $post;
        if ($transactions) {
            if ($this->data['shortcode'] == 546448) {
                $this->template->title('Upload Transactions')->build('admin/reconcile_transactions', $this->data);
            } elseif ($this->data['shortcode'] = 546866) {
                $this->template->title('Upload Transactions')->build('admin/reconcile_disbursements', $this->data);
            }
        } else {
            $this->template->title('Upload Transactions')->build('admin/upload_transactions', $this->data);
        }
    }


    function reconcile_transaction()
    {
        $response = array();
        $transaction = $this->input->post('transaction');
        if ($transaction) {
            $account_number = $transaction['a/c-no-'];
            $transaction_id = $transaction['receipt-no-'];
            $paid_in = currency($transaction['paid-in']);
            $organization_balance = isset($transaction['balance']) ? currency($transaction['balance']) : 0;
            $transaction_type = $transaction['reason-type'];
            $transaction_date = strtotime($transaction['completion-time']);
            if ($paid_in > 0) {
                if ($account_number) {
                    if (preg_match('/-/', $account_number)) {
                        $stk_push = $this->safaricom_m->get_stk_request_by_request_id($account_number,'',FALSE);
                        // print_r($stk_push);die;
                        if ($stk_push) {
                            $request = $this->safaricom_m->get_stk_request($stk_push->id);
                            if ($stk_push->request_reconcilled == 1) {
                                $response = array(
                                    'status' => 1,
                                    'id' => $transaction_id,
                                );
                            } else {
                                $update = array(
                                    'response_code' => 0,
                                    'result_code' => 0,
                                    'transaction_id' => $transaction_id,
                                    'organization_balance' => $organization_balance,
                                    'transaction_date' => $transaction_date,
                                    'modified_on' => time(),
                                );
                                if ($this->safaricom_m->update_stkpushrequest($request->id, $update)) {
                                    $request = $this->safaricom_m->get_stk_request($stk_push->id);
                                    if ($request->result_code == '0') {
                                        if ($this->transactions->record_transaction($request)) {
                                            $response = array(
                                                'status' => 1,
                                                'id' => $transaction_id,
                                            );
                                        } else {
                                            $response = array(
                                                'status' => 0,
                                                'id' => $transaction_id,
                                            );
                                        }
                                    }
                                } else {
                                    $response = array(
                                        'status' => 0,
                                        'id' => $transaction_id,
                                    );
                                }
                            }
                            $this->transactions->send_customer_callback($request);
                        } else {
                            $response = array(
                                'status' => 0,
                                'id' => $transaction_id,
                            );
                        }
                    } else {
                        if ($this->safaricom_m->is_transaction_dublicate($transaction_id)) {
                            $transaction = $this->safaricom_m->get_c2b_payment_by_transaaction_id($transaction_id);
                            if ($transaction->status == 1) {
                                $response = array(
                                    'status' => 1,
                                    'id' => $transaction_id,
                                );
                            } else {
                                $update_id = $this->safaricom_m->update_c2b_by_transaction_id($transaction_id, $organization_balance, $transaction_type);
                                if ($update_id) {
                                    if ($this->transactions->record_direct_payment($update_id)) {
                                        $response = array(
                                            'status' => 1,
                                            'id' => $transaction_id,
                                        );
                                    } else {
                                        $response = array(
                                            'status' => 0,
                                            'id' => $transaction_id,
                                        );
                                    }
                                } else {
                                    $this->mailer->send_via_sendgrid('geoffrey.githaiga@digitalvision.co.ke', 'E-Wallet Deposit - Failed Didnt check account', serialize($transaction), 'E-Wallet', 'notifications@chamasoft.com', 'info@chamasoft.com');
                                    $response = array(
                                        'status' => 0,
                                        'id' => $transaction_id,
                                    );
                                }
                            }
                        } else {
                            if ($this->transactions->check_if_valid_account($account_number)) {
                                $details = $transaction['other-party-info'];
                                $more_details = explode(' - ', $details);
                                $phone = valid_phone($more_details[0]);
                                $customer = $more_details[1];
                                $input_data = array(
                                    'transaction_id' => $transaction_id,
                                    'reference_number' => $account_number,
                                    'transaction_date' => strtotime($transaction['completion-time']),
                                    'amount' => $paid_in,
                                    'active' => 1,
                                    'currency' => 'KES',
                                    'transaction_type' => $transaction['reason-type'],
                                    'transaction_particulars' => $transaction['details'],
                                    'phone' => $phone,
                                    'account' => $account_number,
                                    'shortcode' => '546448',
                                    'customer_name' => $customer,
                                    'created_on' => time(),
                                );
                                if ($id = $this->safaricom_m->insert_c2b($input_data)) {
                                    $update_id = $this->safaricom_m->update_c2b_by_transaction_id($transaction_id, $organization_balance, $transaction['reason-type']);
                                    if ($update_id) {
                                        if ($this->transactions->record_direct_payment($update_id)) {
                                            $response = array(
                                                'status' => 1,
                                                'id' => $transaction_id,
                                            );
                                        }
                                    }
                                } else {
                                    $response = array(
                                        'status' => 0,
                                        'id' => $transaction_id,
                                    );
                                }
                            } else {
                                $response = array(
                                    'status' => 0,
                                    'id' => $transaction_id,
                                );
                            }
                        }
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'id' => $transaction_id,
                    );
                }
            } else {
                $response = array(
                    'status' => 0,
                    'id' => $transaction_id,
                );
            }
        }
        echo json_encode($response);
    }

    function reconcile_disbursements()
    {
        $response = array();
        $transaction = $this->input->post('transaction');
        if ($transaction) {
            $details = generate_slug($transaction['details']);
            $transaction_id = $transaction['receipt-no-'];
            $paid_in = currency($transaction['paid-in']);
            $withdrawn_amount = currency(abs($transaction['withdrawn']));
            $organization_balance = isset($transaction['balance']) ? currency($transaction['balance']) : 0;
            $transaction_type = $transaction['reason-type'];
            $transaction_status = $transaction['transaction-status'];
            $transaction_completed_time = strtotime($transaction['completion-time']);
            $transaction_party_name = implode(" ", array_slice(explode("-", $transaction['other-party-info']), 1));
            $linked_transaction_id = $transaction['linked-transaction-id'];
            // Check if the transaction status is completed
            if ($transaction_status == 'Completed') {
                if ($details) {
                    $string  = substr($details, strpos($details, "id-is-") + 1);
                    $data_ep = explode('-', $string);
                    if (isset($data_ep[2]) && isset($data_ep[3]) && isset($data_ep[4])) {
                        // Get the originator conversatio ID
                        $originator_conversation_id = $data_ep[2] . '-' . $data_ep[3] . '-' . $data_ep[4];
                        // get the b2c request by originator conversation id
                        $request = $this->safaricom_m->get_b2c_request_by_originator_conversation_id($originator_conversation_id);
                        if ($request) {
                            if ($withdrawn_amount > 0) {
                                // structure the update
                                $update = array(
                                    'transaction_receipt' => $transaction_id,
                                    'transaction_amount' => $withdrawn_amount,
                                    'receiver_party_public_name' => $transaction_party_name,
                                    'transaction_completed_time' => $transaction_completed_time,
                                    'transaction_id' => $transaction_id,
                                    'callback_result_description' => "The service request is processed successfully",
                                    'callback_result_code' => "0"
                                );
                                // update the b2c request
                                if ($update_id = $this->safaricom_m->update_b2c_request($request->id, $update)) {
                                    $request = $this->safaricom_m->get_b2c_request($request->id);
                                    // reconcile the transaction
                                    if ($this->transactions->reconcile_account_disbursement($request)) {
                                        $this->transactions->send_customer_disbursement_callback($request);
                                        $response = array(
                                            "status" => 1,
                                            "id" => $transaction_id
                                        );
                                    } else {
                                        $response = array(
                                            "status" => 1,
                                            "id" => $transaction_id
                                        );
                                    }
                                } else {
                                    $response = array(
                                        "status" => 0,
                                        "id" => $transaction_id
                                    );
                                }
                            }
                        }
                        else if($paid_in > 0){ // b2c reversal
                            // get the b2c request by linked transaction id
                            $request  = $this->safaricom_m->get_b2c_request_by_transaction_id($linked_transaction_id);
                            if($request){
                                $payment_transaction = array(
                                    "id" => $request->id,
                                    "transaction_id" => $transaction_id,
                                    "account_id" => $request->account_id,
                                    "amount" => $paid_in,
                                    "transaction_date" => time(),
                                    "description" => "Payment reversal for transaction " . $linked_transaction_id." for ".$transaction_party_name
                                );
                                if($this->transactions->record_transaction(((object)$payment_transaction),"","",-1)){
                                    $update = array(
                                        'is_reversed' => 1,
                                        'modified_on' => time(),
                                    );
                                    $this->safaricom_m->update_b2c_request($request->id,$update);
                                    $payment = array(
                                        "transaction_send_status" => 1,
                                        "reference_number" => $transaction_id,
                                        "transaction_id" => $transaction_id,
                                        "phone" => "",
                                        "transaction_date" => time(),
                                        "customer_name" => $transaction_party_name,
                                        "transaction_type" => 4,
                                        "transaction_particulars" => "Payment reversal for transaction " . $linked_transaction_id." for ".$transaction_party_name,
                                        "currency" => "KES"
                                    );
                                    $account = $this->accounts_m->get($request->account_id);
                                    $this->transactions->send_notification(((object)$payment),$account,$paid_in);
                                    // send callback to channel
                                    $response = array(
                                        "status" => 1,
                                        "id" => $transaction_id
                                    );
                                }else{
                                    $response = array(
                                        "status" => 0,
                                        "id" => $transaction_id
                                    );
                                }
                            }else{
                                $response = array(
                                    "status" => 0,
                                    "id" => $transaction_id
                                );
                            }
                        }else {
                            $response = array(
                                "status" => 0,
                                "id" => $transaction_id
                            );
                        }
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'id' => $transaction_id,
                    );
                }
            } else {
                $response = array(
                    'status' => 0,
                    'id' => $transaction_id,
                );
            }
        }
        echo json_encode($response);
    }


    function c2b_requests()
    {
        $total_rows = $this->safaricom_m->count_all_c2b_requests();
        $pagination = create_pagination('admin/safaricom/c2b_requests/pages', $total_rows, 50, 5, TRUE);
        $this->data['posts'] = $this->safaricom_m->limit($pagination['limit'])->get_all_c2b_requests();
        $this->data['pagination'] = $pagination;

        //print_r($this->data);die;
        $this->template->title('List C2B Transactions')->build('admin/c2b_requests', $this->data);
    }

    function delete_c2b($id = 0)
    {
        $id or redirect('admin/safaricom/c2b_requests');
        $post = $this->safaricom_m->get_c2b_request($id);
        if (!$post) {
            $this->session->set_flashdata('error', 'Sorry the payment does not exist');
            return FALSE;
        }
        $delete_id = $this->safaricom_m->delete_c2b($id);
        if ($delete_id) {
        } else {
        }
        redirect('admin/safaricom/c2b_requests');
    }


    function b2b_transactions()
    {
        $total_rows = $this->safaricom_m->count_all_b2b_transactions();
        $pagination = create_pagination('admin/safaricom/b2b_transactions/pages', $total_rows, 50, 5, TRUE);
        $this->data['posts'] = $this->safaricom_m->limit($pagination['limit'])->get_all_b2b_transactions();
        $this->data['pagination'] = $pagination;
        $this->template->title('List B2B Transactions')->build('admin/b2b_requests', $this->data);
    }

    function view_b2b_request($id = 0)
    {
        $id or redirect('admin/safaricom/b2b_transactions');
        $post = $this->safaricom_m->get_b2b_transaction($id);
        if (!$post) {
            $this->session->set_flashdata('error', 'Sorry the request could not be found');
            redirect('admin/safaricom/b2b_transactions');
            return FALSE;
        }
        print_r('<pre>');
        print_r($post);
        print_r('</pre>');
        die;
        $this->data['post'] = $post;
        $this->template->title('Safaricom B2B Request')->build('admin/view_request', $this->data);
    }

    function create_b2b_request()
    {
        $response = '';
        $post = '';
        if (isset($_POST['submit'])) {
            $b2b_request = $this->input->post('request');
            if (json_decode($b2b_request) == false) {
                $response = array(
                    'response' =>
                    array(
                        'status' => 1,
                        'description' => 'Json format is wrong',
                    ),
                    'request' => array(),
                );
                $this->data['response'] = json_encode($response);
            } else {
                $response = $this->curl->post_json($b2b_request, site_url('safaricom/b2b_transaction_request'));
                if ($response) {
                    $response = json_decode($response);
                    if ($response) {
                        if (isset($response->response->request_id)) {
                            $id = array(
                                'request_id' => $response->response->request_id,
                            );
                            /*$callback = $this->curl->post_json(json_encode($id),site_url('safaricom/b2b_get_call_back_status'));
                            if($callback){
                                $callback = array('callback'=>(array)json_decode($callback));
                                $response = (array)$response;
                                $response = (json_encode($response+$callback,JSON_PRETTY_PRINT));
                            }*/
                            $response = (json_encode($response, JSON_PRETTY_PRINT));
                        } else {
                            $response = (json_encode($response, JSON_PRETTY_PRINT));
                        }
                    }
                }
            }
            $post = $b2b_request;
        }
        $this->data['response'] = $response;
        $this->data['request_sample'] = $post ?: json_encode($this->b2b_transaction_request, JSON_PRETTY_PRINT);
        $this->template->title('Safaricom B2B Request')->build('admin/create_request', $this->data);
    }


    function stk_push_requests()
    {
        $total_rows = $this->safaricom_m->count_all_stk_push_requests();
        $pagination = create_pagination('admin/safaricom/stk_push_requests/pages', $total_rows, 50, 5, TRUE);
        $this->data['posts'] = $this->safaricom_m->limit($pagination['limit'])->get_all_stk_push_requests();
        $this->data['pagination'] = $pagination;
        $this->template->title('STK Push Request List')->build('admin/stk_push_requests', $this->data);
    }

    function checkidentity_requests()
    {
        $total_rows = $this->safaricom_m->count_all_checkidentity_requests();
        $pagination = create_pagination('admin/safaricom/checkidentity_requests/pages', $total_rows, 50, 5, TRUE);
        $this->data['posts'] = $this->safaricom_m->limit($pagination['limit'])->get_all_checkidentity_requests();
        $this->data['pagination'] = $pagination;
        $this->template->title('CheckIdentity Request List')->build('admin/checkidentity_requests', $this->data);
    }

    function query_kyc_requests()
    {
        $total_rows = $this->safaricom_m->count_all_query_kyc_requests();
        $pagination = create_pagination('admin/safaricom/query_kyc_requests/pages', $total_rows, 50, 5, TRUE);
        $this->data['posts'] = $this->safaricom_m->limit($pagination['limit'])->get_all_query_kyc_requests();
        $this->data['pagination'] = $pagination;
        $this->template->title('Query User KYC Request List')->build('admin/query_kyc_requests', $this->data);
    }

    function create_configurations()
    {
        $post = new StdClass();
        $this->form_validation->set_rules($this->configuration_rules);
        if ($this->form_validation->run()) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $api_key = $this->input->post('api_key');
            $shortcode = $this->input->post('shortcode');
            $endpoint = $this->input->post('endpoint');
            $consumer_secret = $this->input->post('consumer_secret');
            $consumer_key = $this->input->post('consumer_key');
            $input = array(
                'username' => $username,
                'password' => $password,
                'encrypted_user_password' =>openssl_key_encrypt($password,FALSE,TRUE),
                'shortcode' => $shortcode,
                'consumer_secret' => $consumer_secret,
                'consumer_key' => $consumer_key,
                'endpoint' => $endpoint,
                'api_key' => "Basic " . base64_encode($consumer_key . ':' . $consumer_secret),
                'active' => 1,
                'created_by' => $this->user->id,
                'created_on' => time(),
            );
            if ($this->safaricom_m->insert_configuration($input)) {
                $this->session->set_flashdata('success', 'Successfully created configuration');
            } else {
                $this->session->set_flashdata('error', 'Error occured creating configuration');
            }
            redirect('admin/safaricom/configuration_listing');
        } else {
            foreach ($this->configuration_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value  = set_value($field['field']);
            }
        }
        $this->data['id'] = '';
        $this->data['post'] = $post;
        $this->template->title('Create Safaricom Configurations')->build('admin/configurations_form', $this->data);
    }

    function edit_configuration($id = 0)
    {
        $id or redirect('admin/safaricom/configuration_listing');
        $post = $this->safaricom_m->get_configuation($id);
        if (!$post) {
            $this->session->set_flashdata('error', 'Configuration not found');
            redirect('admin/safaricom/configuration_listing');
        }
        $this->form_validation->set_rules($this->configuration_rules);
        if ($this->form_validation->run()) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $api_key = $this->input->post('api_key');
            $consumer_secret = $this->input->post('consumer_secret');
            $consumer_key = $this->input->post('consumer_key');
            $shortcode = $this->input->post('shortcode');
            $endpoint = $this->input->post('endpoint');
            $access_token = $this->input->post('access_token');
            $update = array(
                'username' => $username,
                'password' => $password,
                'encrypted_user_password'=>openssl_key_encrypt($password,FALSE,TRUE),
                'shortcode' => $shortcode,
                'consumer_secret' => $consumer_secret,
                'endpoint' => $endpoint,
                'consumer_key' => $consumer_key,
                'api_key' => "Basic " . base64_encode($consumer_key . ':' . $consumer_secret),
                'modified_by' => $this->user->id,
                'modified_on' => time(),
                'access_token' => $access_token,
                'access_token_expires_at' => strtotime("+30 minutes", time()),
                'access_token_type' => 'Bearer',
            );
            if ($this->safaricom_m->update_configuration($post->id, $update)) {
                $this->session->set_flashdata('success', 'Successfully updated configuration');
            } else {
                $this->session->set_flashdata('error', 'Error occured updating configuration');
            }
            redirect('admin/safaricom/configuration_listing');
        } else {
            foreach ($this->configuration_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = $post->$field_value ?: set_value($field_value);
            }
        }
        $this->data['id'] = $id;
        $this->data['post'] = $post;
        $this->template->title('Edit Safaricom Configurations')->build('admin/configurations_form', $this->data);
    }

    function configuration_listing()
    {
        $posts = $this->safaricom_m->get_configurations();
        $this->data['posts'] = $posts;
        $this->template->title('List Safaricom Configurations')->build('admin/list_configurations', $this->data);
    }

    function delete_configuration($id = 0)
    {
        $id or redirect('admin/safaricom/configuration_listing');
        $post = $this->safaricom_m->get_configuation($id);
        if (!$post) {
            $this->session->set_flashdata('error', 'Configuration not found');
            redirect('admin/safaricom/configuration_listing');
        }
        if ($this->safaricom_m->delete_configuration($id)) {
            $this->session->set_flashdata('success', 'Configuration successfully removed');
        } else {
            $this->session->set_flashdata('error', 'Error occured removing configuation');
        }
        redirect('admin/safaricom/configuration_listing');
    }

    function action()
    {
        $action = $this->input->post('btnAction');
        $ids = $this->input->post('action_to');
        if ($action == 'setDefault') {
            $result = $this->set_default($ids);
        }
        redirect('admin/safaricom/configuration_listing');
    }

    function set_default($ids = array())
    {
        if ($ids) {
            $post = $this->safaricom_m->get_default_configuration();
            if ($post) {
                $update = array(
                    'is_default' => '',
                    'modified_by' => $this->user->id,
                    'modified_on' => time(),
                );
                $this->safaricom_m->update_configuration($post->id, $update);
            }
            $update = array(
                'is_default' => 1,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            $this->safaricom_m->update_configuration($ids[0], $update);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function view($id = 0)
    {
        $id or redirect('admin/safaricom/c2b_requests');
        // $post = 
        $post = $this->safaricom_m->get_c2b_request($id);
        print_r($post);
    }

    function forward($id = 0)
    {
        $post = $this->safaricom_m->get_c2b_request($id);
        if ($post) {
            $account = $this->accounts_m->get_account_by_account_number($post->account);
            $amount = 0;
            if ($account) {
                $charge_amount = $this->tariffs_m->calculate_deposit_charge($account->tariff_id, $post->amount) ?: 0;
                $amount = ($post->amount - $charge_amount);
            }
            $report = $this->transactions->send_notification($post, $account, $amount);
            if ($report) {
                $this->session->set_flashdata('success', 'Transaction forwaded ->' . $report);
            } else {
                $this->session->set_flashdata('error', $this->session->flashdata('error'));
            }
        } else {
            $this->session->set_flashdata('error', 'Could not find the transaction to forward');
        }
        $referrer = $this->agent->referrer();
        redirect($referrer);
    }

    function forward_request($id = 0)
    {
        $request = $this->safaricom_m->get_stk_request($id);
         
        if ($request) {
            $loan=$this->loans_m->get($request->loan_id);
            $amount=$request->amount;
            if($loan){
            $deposit_date =$request->modified_on ; 
            $send_sms_notification =0;
            $deposit_method =1;
            $send_email_notification =0;
            $description='Payment via STK Push';
            $member = $this->members_m->get_group_member($loan->member_id,$loan->group_id);
            $created_by = $this->members_m->get_group_member_by_user_id($loan->group_id,$member->user_id);
            if($amount && $deposit_date && $member && $created_by){
                if($this->loan->record_loan_repayment($loan->group_id,$deposit_date,$member,$loan->id,"mobile-",$deposit_method,$description,$amount,$send_sms_notification,$send_email_notification,$created_by)){
                $this->session->set_flashdata('success', 'Request forwaded : ');
                    
                }else{
                $this->session->set_flashdata('error', 'Repayment failed ');
                    
                }
                
            } else {
                $this->session->set_flashdata('error', 'Missing parameters');
            }
            }
            else{
                $this->session->set_flashdata('error', 'loan not found : ');

            }
            
        } else {
            $this->session->set_flashdata('error', 'Could not find request to forward');
        }
        $referrer = $this->agent->referrer();
        redirect($referrer);
    }
}

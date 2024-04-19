<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Ajax extends Ajax_Controller
{

    protected $data = array();
    public $access_levels = array(1 => 'Group Administrator', 0 => 'Member');
    public $error_feedback = array();
    public $organization_roles = array(
        '1' => 'Payroll Accountant',
        '2' => 'Sacco Officer ',
        '3' => 'Credit committee',
        '4' => 'Sacco Manager'
    );

    protected $validation_rules = array(
        array(
            'field' =>  'first_name',
            'label' =>  'First Name',
            'rules' =>  'xss_clean|trim|required',
        ), array(
            'field' =>  'last_name',
            'label' =>  'Last Name',
            'rules' =>  'xss_clean|trim|required',
        ), array(
            'field' =>  'middle_name',
            'label' =>  'Middle Name',
            'rules' =>  'xss_clean|trim',
        ), array(
            'field' =>  'phone',
            'label' =>  'Phone Number',
            'rules' =>  'xss_clean|trim|required|callback_phone_is_unique',
        ),
        array(
            'field' =>  'loan_limit',
            'label' =>  'Loan Limit',
            'rules' =>  'currency|trim|required',
        ), array(
            'field' =>  'email',
            'label' =>  'Email address',
            'rules' =>  'xss_clean|trim|valid_email|callback_email_is_unique',
        )
        , array(
            'field' =>  'id_number',
            'label' =>  'ID Number',
            'rules' =>  'xss_clean|trim|callback_id_number_is_unique',
        ), array(
            'field' =>  'membership_number',
            'label' =>  'Membership Number',
            'rules' =>  'xss_clean|trim|callback_membership_number_is_unique',
        ), array(
            'field' =>  'group_role_id',
            'label' =>  'Group Role',
            'rules' =>  'xss_clean|trim|numeric|callback_group_role_assignment_is_unique',
        ),
        array(
            'field' =>  'organization_role_id',
            'label' =>  'Organization Role',
            'rules' =>  'xss_clean|trim|numeric|callback_organization_role_assignment_is_unique',
        ), array(
            'field' =>  'is_admin',
            'label' =>  'Access Level',
            'rules' =>  'xss_clean|trim|numeric',
        ), array(
            'field' =>  'date_of_birth',
            'label' =>  'Date of Birth',
            'rules' =>  'xss_clean|trim',
        ),
    );

    protected $suspension_validation_rules = array(
        array(
            'field' =>  'member_id',
            'label' =>  'Member id',
            'rules' =>  'xss_clean|trim|required|callback__is_group_member',
        ), array(
            'field' =>  'comment',
            'label' =>  'Suspendion Comment',
            'rules' =>  'xss_clean|trim|required',
        ),
        array(
            'field' =>  'password',
            'label' =>  'Password',
            'rules' =>  'xss_clean|trim|required',
        )
    );

    function __construct()
    {
        parent::__construct();
        $this->load->model('members_m');
        $this->load->model('loans_m');
        $this->load->model('loan_types_m');
        $this->load->model('loan_applications_m');
        $this->load->library('group_members');
        $this->load->library('excel_library');
    }

    public function phone_is_unique()
    {
        $phone = valid_phone($this->input->post('phone'));
        if ($user = $this->ion_auth->identity_check($phone)) {
            if ($this->input->post('user_id') == $user->id) {
                return TRUE;
            } else if ($this->ion_auth->is_in_group($this->user->id, 3) || $this->ion_auth->is_admin()) {
                return TRUE;
            } else {
                $this->form_validation->set_message('phone_is_unique', 'The phone number is already registered to another member.');
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

    public function email_is_unique()
    {
        $email = $this->input->post('email');
        if ($email == '') {
            return TRUE;
        } else {
            if ($user = $this->ion_auth->identity_check($email)) {
                if ($this->input->post('user_id') == $user->id) {
                    return TRUE;
                } else {
                    $this->form_validation->set_message('email_is_unique', 'The email address is already registered to another member.');
                    return FALSE;
                }
            } else {
                return TRUE;
            }
        }
    }

    public function id_number_is_unique()
    {
        $id_number = $this->input->post('id_number');
        if ($id_number == '') {
            return TRUE;
        } else {
            if ($user = $this->ion_auth->id_number_check($id_number)) {
                if ($this->input->post('user_id') == $user->id) {
                    return TRUE;
                } else {
                    $this->form_validation->set_message('id_number_is_unique', 'The id number is already registered to another member.');
                    return FALSE;
                }
            } else {
                return TRUE;
            }
        }
    }

    public function membership_number_is_unique()
    {
        $membership_number = $this->input->post('membership_number');
        if ($membership_number == '') {
            return TRUE;
        } else {
            //$member = $this->members_m->get_member_by_membership_number($membership_number);
            //print_r($member);
            //die;
            if ($member = $this->members_m->get_member_by_membership_number($membership_number)) {
                if ($this->input->post('user_id') == $member->user_id) {
                    return TRUE;
                } else {
                    $this->form_validation->set_message('membership_number_is_unique', 'The membership number is already registered to another member.');
                    return FALSE;
                }
            } else {
                return TRUE;
            }
        }
    }

    public function group_role_assignment_is_unique()
    {
        $group_role_id = $this->input->post('group_role_id');
        if ($group_role_id == '') {
            return TRUE;
        } else {
            if ($member = $this->members_m->get_member_by_group_role_id($group_role_id)) {
                if ($this->input->post('user_id') == $member->user_id) {
                    return TRUE;
                } else {
                    $this->form_validation->set_message('group_role_assignment_is_unique', 'The group role is already assigned to another member.');
                    return FALSE;
                }
            } else {
                return TRUE;
            }
        }
    }

    public function organization_role_assignment_is_unique()
    {
        $organization_role_id = $this->input->post('organization_role_id');
        if ($organization_role_id == '') {
            return TRUE;
        } else {
            if ($member = $this->members_m->get_member_by_organization_role_id($organization_role_id)) {
                if ($this->input->post('user_id') == $member->user_id) {
                    return TRUE;
                } else {
                    return TRUE;
                    /*$this->form_validation->set_message('organization_role_assignment_is_unique', 'The organization role is already assigned to another member.');
                    return FALSE;*/
                }
            } else {
                return TRUE;
            }
        }
    }

    public function _is_group_member()
    {
        $member_id = $this->input->post('member_id');
        if ($member_id) {
            $member_details = $this->members_m->get_group_member($member_id);
            if ($member_details) {
                return TRUE;
            } else {
                $this->form_validation->set_message('_is_group_member', 'The Member details is missing.');
                return FALSE;
            }
        } else {
            $this->form_validation->set_message('_is_group_member', 'The Member id is required.');
            return FALSE;
        }
    }


    function get_members_listing()
    {
        // if(array_key_exists($this->member->id, $this->member_role_holder_options) || $this->ion_auth->is_in_group($this->user->id,1)){
        $filter_parameters = array(
            'member_id' => $this->input->get('member_id'),
        );
        $total_rows = $this->members_m->count_group_members($this->group->id, $filter_parameters);
        $data['organization_role_options'] = $this->group_members->organization_roles;
        $pagination = create_pagination('bank/members/listing/pages', $total_rows, 50, 5, TRUE);
        $group_role_options = $this->group_roles_m->get_group_role_options();
        $organization_roles = $this->group_members->organization_roles;
        $posts = $this->members_m->limit($pagination['limit'])->get_group_members($this->group->id, $filter_parameters);


        if ($posts) {
            echo form_open('bank/members/action', ' id="form"  class="form-horizontal"');
            if ($pagination) {
                echo '
                    <div class="search-pagination">';
                if (!empty($pagination['links'])) :
                    echo '
                            <div class="paging">Showing from <span class="greyishBtn">' . $pagination['from'] . '</span> to <span class="greyishBtn">' . $pagination['to'] . '</span> of <span class="greyishBtn">' . $pagination['total'] . '</span> Members
                            </div>
                            <div class ="pagination">' . $pagination['links'] . '</div></div>';
                endif;
                echo '
                    </div>';
            }
            echo '
                    <div class="table-responsive">
                        <table class="table m-table m-table--head-separator-primary">
                            <thead>
                                <tr >
                                    <th width=\'2%\'>
                                        <label class="m-checkbox">
                                            <input type="checkbox" name="check" value="all" class="check_all">
                                            <span></span>
                                        </label>
                                    </th>
                                    <th>
                                        #
                                    </th>
                                    <th>' . translate('Name') . '</th>
                                    <th>' . translate('Contacts') . '</th>
                                    <th>' . translate('Limit') . '</th>
                                   
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>';
            $i = $this->uri->segment(5, 0);
            foreach ($posts as $post) :
                echo '
                                    <tr id="' . $post->id . '_active_row">
                                        <td scope="row">
                                            <label class="m-checkbox">
                                                <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="' . $post->id . '" />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td>' . ($i + 1) . '</td>
                                        <td>';
                echo $post->first_name . ' ' . $post->last_name;
                if ($this->show_membership_number) {
                    echo '-' . $this->membership_numbers[$post->id];
                }
                if ($post->user_id == $this->group->owner) {
                    echo '<br><span class="m-badge m-badge--brand m-badge--wide m-badge--rounded">' . translate('Admin') . '</span>';
                }
                if ($post->active == '0') {
                    echo '<br><span class="m-badge m-badge--brand m-badge--wide m-badge--rounded m-badge--danger">' . translate('Suspended') . '</span>';
                }
                echo '
                                        </td><td>Phone:' . $post->phone;
                if ($post->email) {
                    echo '<br> Email:' . $post->email;
                }
                if ($post->id_number) {
                    echo '<br> ID Number ' . $post->id_number;
                }
                echo '
                                        </td><td>';
                if ($post->loan_limit) {
                    echo number_to_currency($post->loan_limit);
                } else {
                    echo "0.00";
                }
                echo '
                                        </td><td>
                                            <div class="btn-group">
                                                <a href="' . site_url('bank/members/view/' . $post->id) . '" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon">
                                                    <span>
                                                        <i class="fa fa-eye"></i>
                                                        <span>' . translate('View User') . '</span>
                                                    </span>
                                                </a>
                                                
                                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="' . site_url('bank/members/edit/' . $post->id) . '">
                                                        <i class="la la-pencil"></i>
                                                        &nbsp;
                                                        ' . translate('Update Profile') . '
                                                    </a>';
              
                echo '
                                                </div>
                                            </div>
                                        </td>
                                    ';
                $i++;
            endforeach;
            echo '
                            </tbody>
                        </table>
                    </div>

                    <div class="row col-md-12">';
            if ($pagination) {
                echo '
                            <div class="search-pagination">';
                if (!empty($pagination['links'])) :
                    echo '
                                    <div class="paging">Showing from <span class="greyishBtn">' . $pagination['from'] . '</span> to <span class="greyishBtn">' . $pagination['to'] . '</span> of <span class="greyishBtn">' . $pagination['total'] . '</span> Members
                                    </div>
                                    <div class ="pagination">' . $pagination['links'] . '</div></div>';
                endif;
                echo '
                            </div>';
            }
            echo '
                    </div>
                    <div class="clearfix"></div>';
            if ($posts) :
                echo '
                        ';
            endif;
            echo form_close();
        } else {
            echo '
                <div class="alert alert-info">
                    <h4 class="block">Information! No records to display</h4>
                    <p>
                        No Members to display.
                    </p>
                </div>';
        }
        // }else{
        //      echo '
        //     <div class="container-fluid">
        //         <div class="row">
        //             <div class="col-md-12">
        //                 <div class="m-alert m-alert--outline alert alert-info fade show" role="alert"">
        //                     <strong>Information!</strong> You dont have rights to access this panel
        //                 </div>
        //             </div>
        //         </div>
        //     </div>';
        // }
    }

    function get_membership_requests(){
        $filter_parameters = array(
            'member_id' => $this->input->get('member_id'),
        );
        $total_rows = $this->members_m->count_group_membership_requests($this->group->id, $filter_parameters);
        $pagination = create_pagination('bank/members/membership_requests/pages', $total_rows, 50, 5, TRUE);
        $posts = $this->members_m->limit($pagination['limit'])->get_group_membership_requests($this->group->id, $filter_parameters);
        if($posts){
            $html = '<ul class="members_list">';
            foreach ($posts as $post) :
                $html .= '
                    <li class="member_">
                        <div class="row">
                            <div class="col-md-1">
                                <a href="' . site_url('bank/members/view/' . $post->id) . '">
                                    <img src="' . (is_file(FCPATH . 'uploads/groups/' . $post->avatar) ? site_url('uploads/groups/' . $post->avatar) : site_url('templates/admin_themes/groups/img/default_avatar.png')) . '" />
                                </a>
                            </div>
                            <div class="col-md-9">
                                <div class="member_content">
                                    <h2 class="">
                                        <a class="member_name" href="' . site_url('bank/members/view/' . $post->id) . '">' . $post->first_name . ' ' . $post->last_name . '</a>
                                    </h2>
                                    <p class="search-desc">';
                                        $html .= '
                                        <span class="bold">' . translate('Phone') . '</span>: ' . $post->phone;
                                        if ($post->email) :
                                            $html .= '
                                            <br/><span class="bold">' . translate('Mail') . '</span>:' . $post->email;
                                        endif;

                                        if ($post->id_number) :
                                            $html .= '
                                            <br/><span class="bold">' . translate('ID Number') . '</span>:' . $post->id_number;
                                        endif;

                                        if ($post->location) :
                                            $html .= '
                                            <br/><span class="bold">' . translate('Location') . '</span>:' . $post->location;
                                        endif;

                                        if ($post->next_of_kin_full_name) :
                                            $html .= '
                                            <br/><span class="bold">' . translate('Next Of Kin Full Name') . '</span>:' . $post->next_of_kin_full_name;
                                        endif;

                                        if ($post->next_of_kin_id_number) :
                                            $html .= '
                                            <br/><span class="bold">' . translate('Next Of Kin ID Number') . '</span>:' . $post->next_of_kin_id_number;
                                        endif;

                                        if ($post->next_of_kin_phone) :
                                            $html .= '
                                            <br/><span class="bold">' . translate('Next Of Kin Phone') . '</span>:' . $post->next_of_kin_phone;
                                        endif;

                                        if ($post->next_of_kin_relationship) :
                                            $html .= '
                                            <br/><span class="bold">' . translate('Next Of Kin Relationship') . '</span>:' . $post->next_of_kin_relationship;
                                        endif;
                
                                    $html .= '
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="directory-actions member_action mt-2">
                                    <div class="btn-group">
                                        <a href="' . site_url('bank/members/admit_member/' . $post->id) . '" class="btn btn-success btn-sm mr-3">Approve</a>
                                        <a href="' . site_url('bank/members/reject_member/' . $post->id) . '" class="btn btn-danger btn-sm">Reject</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>';
            endforeach;
        } else {           
            $html =  '
                <div class="alert alert-info">
                    <h4 class="block">Information! No records to display</h4>
                    <p>
                        No Members to display.
                    </p>
                </div>';
        }

        if ($pagination) {
            $html .= '
            <div class="search-pagination">';
            if (!empty($pagination['links'])) :
                $html .= '
                    <div class="paging">Showing from <span class="greyishBtn">' . $pagination['from'] . '</span> to <span class="greyishBtn">' . $pagination['to'] . '</span> of <span class="greyishBtn">' . $pagination['total'] . '</span> Members
                    </div>
                    <div class ="pagination">' . $pagination['links'] . '</div></div>';
            endif;
            $html .= '
            </div>';
        }

        echo $html;
    }

    function get_members_directory()
    {
        $total_rows = $this->members_m->count_active_group_members();
        $pagination = create_pagination('bank/members/pages', $total_rows, 25, 4, TRUE);
        $group_role_options = $this->group_roles_m->get_group_role_options();
        $group_member_total_cumulative_contribution_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_paid_per_member_array($this->group->id);
        $group_member_total_cumulative_contribution_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_contribution_arrears_per_member_array($this->group->id);

        if ($group_member_total_cumulative_contribution_paid_per_member_array && $group_member_total_cumulative_contribution_arrears_per_member_array) {
            $total_contributions_paid_per_member_array = $group_member_total_cumulative_contribution_paid_per_member_array;
            $total_contribution_balances_per_member_array = $group_member_total_cumulative_contribution_arrears_per_member_array;
        } else {
            $total_contributions_paid_per_member_array = $this->reports_m->get_group_total_contributions_paid_per_member_array();
            $total_contribution_balances_per_member_array = $this->reports_m->get_group_total_contribution_balances_per_member_array();
        }
        $group_member_total_cumulative_fine_paid_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_paid_per_member_array($this->group->id);
        $group_member_total_cumulative_fine_arrears_per_member_array = $this->statements_m->get_group_member_total_cumulative_fine_arrears_per_member_array($this->group->id);
        if ($group_member_total_cumulative_fine_paid_per_member_array && $group_member_total_cumulative_fine_arrears_per_member_array) {
            $group_total_fines_paid_per_member_array = $group_member_total_cumulative_fine_paid_per_member_array;
            $group_total_fines_balances_per_member_array = $group_member_total_cumulative_fine_arrears_per_member_array;
        } else {
            $group_total_fines_paid_per_member_array = $this->reports_m->get_group_total_fines_paid_per_member_array();
            $group_total_fines_balances_per_member_array = $this->reports_m->get_group_total_fines_balances_per_member_array();
        }
        $posts = $this->members_m->limit($pagination['limit'])->get_active_group_members();
        $html = '
        <ul class="members_list">';
        if ($posts) {
            foreach ($posts as $post) :
                $html .= '
                    <li class="member_">
                        <div class="row">
                            <div class="col-md-1">
                                <a href="' . site_url('bank/members/view/' . $post->id) . '">
                                    <img src="' . (is_file(FCPATH . 'uploads/groups/' . $post->avatar) ? site_url('uploads/groups/' . $post->avatar) : site_url('templates/admin_themes/groups/img/default_avatar.png')) . '" />
                                </a>
                            </div>
                            <div class="col-md-9">
                                <div class="member_content">
                                    <h2 class="">
                                        <a class="member_name" href="' . site_url('bank/members/view/' . $post->id) . '">' . $post->first_name . ' ' . $post->last_name . '</a>
                                        <p class="member_role" >' . ($group_role_options[$post->group_role_id] ?? 'Member') . '</p>
                                    </h2>
                                    <p class="search-desc">';
                if ($this->show_membership_number) {
                    $html .= '
                                            <span class="bold">Membership No.</span>: ' . ($this->membership_numbers[$post->id] ?? '') . '<br/>';
                }
                $html .= '
                                        <span class="bold">' . translate('Phone') . '</span>: ' . $post->phone;
                if ($post->email) :
                    $html .= '
                                            <br/><span class="bold">' . translate('Mail') . '</span>:' . $post->email;
                endif;
                $html .= '
                                        <br/><span class="bold">' . translate('Total Contributions') . '
                                        </span> : ' . $this->group_currency . ' ' . number_to_currency($total_contributions_paid_per_member_array[$post->id]) . '
                                        <br/><span class="bold">' . translate('Total Fines') . '
                                        </span> : ' . $this->group_currency . ' ' . number_to_currency($group_total_fines_paid_per_member_array[$post->id]);

                if (isset($total_contribution_balances_per_member_array[$post->id]) && $total_contribution_balances_per_member_array[$post->id] > 0) :
                    $html .= '
                                            <br/><span class="bold">' . translate('Total Contribution Arrears') . '
                                            </span> : ' . $this->group_currency . ' ' . number_to_currency($total_contribution_balances_per_member_array[$post->id]);
                endif;
                if ($group_total_fines_balances_per_member_array[$post->id] > 0) :
                    $html .= '
                                            <br/><span class="bold">' . translate('Total Fines Arrears') . '
                                            </span> : ' . $this->group_currency . ' ' . number_to_currency($group_total_fines_balances_per_member_array[$post->id]);
                endif;
                $html .= '
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="directory-actions member_action mt-2">
                                    <div class="btn-group">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">' . translate('Actions') . ' 
                                            <!-- <i class="fa fa-angle-down"></i> -->
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li>
                                                <a href="' . site_url('bank/members/view/' . $post->id) . '"><i class="fa fa-eye"></i>' . translate('View Profile') . '
                                                </a>
                                            </li>
                                            <li>
                                                <a href="' . site_url('bank/members/edit/' . $post->id) . '"><i class="fa fa-edit"></i>' .
                    translate("Edit Profile") . '
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>';
            endforeach;
        } else {
            $html .= '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>Sorry!</strong> There are no members to display.
                </div>
                ';
        }
        $html .= '  
        </ul>';
        if ($pagination) {
            $html .= '
            <div class="search-pagination">';
            if (!empty($pagination['links'])) :
                $html .= '
                    <div class="paging">Showing from <span class="greyishBtn">' . $pagination['from'] . '</span> to <span class="greyishBtn">' . $pagination['to'] . '</span> of <span class="greyishBtn">' . $pagination['total'] . '</span> Members
                    </div>
                    <div class ="pagination">' . $pagination['links'] . '</div></div>';
            endif;
            $html .= '
            </div>';
        }
        echo $html;
    }

    function get_members_setup_listing()
    {
        $filter_parameters = array(
            'member_id' => $this->input->get('member_id'),
        );
        $per_page = ($this->input->post('length')) > 1 ? $this->input->post('length') : 0;
        $start_number = $this->input->post('start');
        $order = $this->input->post('order');
        $order = $this->input->post('order');
        if ($order) {
            $dir = strtoupper($order[0]['dir']);
        } else {
            $dir = 'ASC';
        }
        $search = $this->input->post('search');
        if ($search) {
            $_GET['name'] = $search['value'];
        }
        $total_rows = $this->members_m->count_group_members($this->group->id, $filter_parameters);
        $data['organization_role_options'] = $this->group_members->organization_roles;
        $pagination = create_custom_pagination('bank/members/listing/pages', $total_rows, $per_page, $start_number, TRUE);
        $group_role_options = $this->group_roles_m->get_group_role_options();
        $organization_roles = $this->group_members->organization_roles;
        $posts = $this->members_m->limit($pagination['limit'])->get_group_members($this->group->id, $filter_parameters, $dir);
        $data = array();
        $ids = array();
        if ($posts) {
            foreach ($posts as $key => $post) {
                $ids[] = $post->id;
                $data[] = array(
                    $start_number + $key + 1,
                    $post->first_name . ' ' . $post->last_name,
                    $post->phone,
                    $post->email,
                    $group_role = isset($group_role_options[$post->group_role_id]) ? $group_role_options[$post->group_role_id] : 'Member',
                    $post->id,
                );
            }
        }
        $response = array(
            "data" => $data,
            "iTotalDisplayRecords" => $total_rows,
            "iTotalRecords" => $this->members_m->count_group_members($this->group->id),
            'ids' => $ids,
        );
        echo json_encode($response);
        die;
    }


    function ajax_loan_guarantor_listing()
    {
        $group_id = $this->group->id;
        $member_id = $this->member->id;
        $user_id = $this->user->id;
        $get_loan_guarantor_request = $this->loans_m->get_loan_application_guarantorship_requests_by_member_id($member_id, $group_id);
        if (!empty($get_loan_guarantor_request)) {  ?>
            <table class="table table-condensed table-striped table-hover table-header-fixed ">
                <thead>
                    <tr>
                        <th width="2%">
                            <span><input name="check" value="all" class="check_all" type="checkbox"></span>
                        </th>
                        <th width="2%">
                            #
                        </th>
                        <th>
                            Loan Details
                        </th>
                        <th class="text-right">
                            Loan Amount (<?php echo $this->group_currency ?>)
                        </th>
                    </tr>
                </thead>
                <tbody> <?php
                        $count = 0;
                        foreach ($get_loan_guarantor_request as $key => $get_loan_guarantor_request_details) {
                            $count++;
                            $guarantor_id = $get_loan_guarantor_request_details->id;
                            $loan_type_id = $get_loan_guarantor_request_details->loan_type_id;
                            $guarantor_member_id = $get_loan_guarantor_request_details->guarantor_member_id;
                            $loan_application_id = $get_loan_guarantor_request_details->loan_application_id;
                            $loan_request_applicant_user_id = $get_loan_guarantor_request_details->loan_request_applicant_user_id;
                            $loan_request_progress_status = $get_loan_guarantor_request_details->loan_request_progress_status;
                            $decline_comment = $get_loan_guarantor_request_details->decline_comment;
                            $guarantor_loan_amount = $get_loan_guarantor_request_details->amount;
                            $get_loan_type = $this->loan_types_m->get($loan_type_id);
                            $get_loan_application_details = $this->loan_applications_m->get($loan_application_id);
                            $loan_amount = $get_loan_application_details->loan_amount;
                            $get_loan_applicant_details = $this->users_m->get($loan_request_applicant_user_id);
                            $get_loan_guarantor_member_details = $this->members_m->get($guarantor_member_id);
                            $guarantor_user_id = $get_loan_guarantor_member_details->user_id;
                            $get_loan_guarantor_user_details = $this->users_m->get($guarantor_user_id);
                            $loan_progress_status = $get_loan_guarantor_request_details->loan_request_progress_status;
                            //print_r($get_loan_type);
                        ?>
                        <tr>
                            <td><span>
                                    <input type="checkbox" name="check" value="all" class="check_all"></span></td>
                            <td><?php echo $count; ?></td>

                            <td>
                                <strong> Loan Name : </strong> <?php echo $get_loan_application_details->name ?> <br>
                                <strong> Loan Applicant Name : </strong> <?php echo $get_loan_applicant_details->first_name . ' ' . $get_loan_applicant_details->last_name ?> <br>
                                <strong> Guarantor Name : </strong> </strong> <?php echo $get_loan_guarantor_user_details->first_name . ' ' . $get_loan_guarantor_user_details->last_name ?> <br>
                                <strong> Guarantor Amount : </strong> <?php echo number_to_currency($guarantor_loan_amount) ?> <br>
                                <strong> Loan Duration : </strong> <span>
                                    <?php if ($get_loan_type->loan_repayment_period_type == 1) {
                                        echo $get_loan_type->fixed_repayment_period . ' Months';
                                    } else if ($get_loan_type->loan_repayment_period_type == 2) {
                                        echo $get_loan_type->minimum_repayment_period . ' - ' . $get_loan_type->maximum_repayment_period . ' Months';
                                    } ?><br> <strong> Loan Request Status : </strong>
                                    <?php if ($loan_progress_status == 1) { ?>
                                        <span class="label label-success">In Progress</span><?php
                                                                                        } else if ($loan_progress_status == 2) { ?>
                                        <span class="label label-danger"> loan Declined</span>
                                    <?php } else if ($loan_progress_status == 3) { ?>
                                        <span class="label label-success"> loan Approved</span>
                                    <?php } else { ?>
                                        <span class="label label-danger"> loan Declined</span>
                                    <?php } ?>

                            </td>
                            <td class="text-right"><?php echo number_to_currency($loan_amount) ?></td>
                            <td> <?php if ($loan_progress_status == 2) {
                                    ?><?php
                                    } else if ($loan_progress_status == 1) {
                                        if ($this->group->id == 3912) { ?>
                                <a href="<?php echo base_url('member/members/view_eazzy_club_loan_requests/' . $loan_application_id . '') ?>" class="btn btn-xs blue ">
                                    <i class="icon-pencil"></i> Edit &nbsp;&nbsp;
                                </a><?php

                                        } else {
                                    ?>
                                <a href="<?php echo base_url('member/members/view_loan_requests/' . $loan_application_id . '') ?>" class="btn btn-xs blue ">
                                    <i class="icon-pencil"></i> Edit &nbsp;&nbsp;
                                </a>
                        <?php }
                                    } ?>
                            </td>

                        </tr><?php

                            } ?>
                </tbody>
            </table><?php
                } else { ?>
            <div class="alert alert-info">
                <h4 class="block">
                    <?php
                    $default_message = 'Information! No records to display';
                    $this->languages_m->translate('no_records_to_display', $default_message);
                    ?>

                </h4>
                <p>
                    Could not find loan guarantor requests
                </p>
            </div>
        <?php
                }
            }

            function ajax_pending_loan_listing($loan_application_id)
            {
                $group_id = $this->group->id;
                $member_id = $this->member->id;
                $user_id = $this->user->id;
                $loan_applications = $this->loan_applications_m->get($loan_application_id);
                if (!empty($loan_applications)) {
                    $loan_type = $this->loan_types_m->get($loan_applications->loan_type_id);
                    $user_details = $this->members_m->get_group_member($loan_applications->member_id, $group_id);
                    $loan_guarantor_request_details = $this->loans_m->get_loan_application_guarantorship_requests_by_loan_application_id($loan_application_id, $group_id);
                    $guarantor_details = $this->members_m->get_group_member($loan_guarantor_request_details->guarantor_member_id, $group_id);
                    echo '
            <table class="table table-condensed table-striped table-hover table-header-fixed ">
                <thead>
                    <tr>
                        <th >
                             <span><input name="check" value="all" class="check_all" type="checkbox"></span>
                        </th>
                        <th>
                            Loan Details
                        </th>
                        <th class="text-right">
                            Loan Amount (KES)
                        </th>  
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody> ';
                    $count = 1;
                    echo '<tr>';
                    echo '<td>'  . $count . '</td>';
                    echo '<td><strong> Loan Name : </strong> ' . $loan_type->name . '<br>';
                    echo '   <strong> Loan Applicant Name : </strong>' . $user_details->first_name . ' ' . $user_details->last_name . '<br>';
                    echo '<strong> Guarantor Name : </strong>' . $guarantor_details->first_name . '' . $guarantor_details->last_name . ' <br>';
                    echo '<strong> Guarantor Amount : </strong> ' . number_to_currency($loan_guarantor_request_details->amount) . ' <br>';
                    echo '<strong> Loan Duration : </strong> <span>' .  isset($loan_type->minimum_repayment_period) ? $loan_type->minimum_repayment_period : '' . '-' . $loan_type->maximum_repayment_period . ' months</span> &nbsp;<br> ';
                    if ($loan_guarantor_request_details->loan_request_progress_status == 1) {
                        echo '<strong> Loan Request  Status: </strong> <span class="label label-success">In Progress</span>';
                    } else if ($loan_guarantor_request_details->loan_request_progress_status == 2) {
                        echo '<strong> Loan Guarantor  Status: </strong><span class="label label-danger"> loan Declined</span>';
                    } else if ($loan_guarantor_request_details->loan_request_progress_status == 3) {
                        echo '<strong> Loan Guarantor  Status: </strong> <span class="label label-success"> loan Approved</span>';
                    } else {
                        echo '<strong> Loan Guarantor  Status: </strong> <span class="label label-danger"> loan Declined</span>';
                    }
                    echo '</td>';
                    echo '<td class="text-right">' . number_to_currency($loan_applications->loan_amount) . '</td>';
                    echo '<td><a href=' . base_url("member/loans/view_loan_application/" . $loan_application_id) . ' class="  btn btn-xs btn-primary">
                              <i class="icon-thrash"></i> view &nbsp;&nbsp; 
                            </a>
                            </td>';
                    echo '</tr>';
                } else {
                    echo '
            <div class="alert alert-danger">
               <button class="close" data-dismiss="alert"></button>
               <strong>Error!</strong> Could not find loans  application details
            </div';
                }
            }

            function ajax_loan_listing_status($loan_request_id)
            {
                $group_id = $this->group->id;
                $member_id = $this->member->id;
                $user_id = $this->user->id;
                $get_loan_guarantor_request_details = $this->loans_m->get_loan_application_guarantorship_requests_by_id($loan_request_id, $group_id);
                if (!empty($get_loan_guarantor_request_details)) {
                    //print_r($get_loan_guarantor_request_details);
                    //die(); 
        ?>
            <table class="table table-condensed table-striped table-hover table-header-fixed ">
                <thead>
                    <tr>
                        <th width="2%">
                            <span><input name="check" value="all" class="check_all" type="checkbox"></span>
                        </th>
                        <th width="2%">
                            #
                        </th>
                        <th>
                            Loan Details
                        </th>
                        <th class="text-right">
                            Loan Amount (KES)
                        </th>
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody> <?php
                        $count = 0;
                        //foreach ($get_loan_guarantor_request as $key => $get_loan_guarantor_request_details) {
                        //print_r($get_loan_guarantor_request_details); die();
                        $count++;
                        $loan_type_id = $get_loan_guarantor_request_details->loan_type_id;
                        $guarantor_member_id = $get_loan_guarantor_request_details->guarantor_member_id;
                        $loan_application_id = $get_loan_guarantor_request_details->loan_application_id;
                        $loan_request_applicant_user_id = $get_loan_guarantor_request_details->loan_request_applicant_user_id;
                        $loan_request_progress_status = $get_loan_guarantor_request_details->loan_request_progress_status;
                        $decline_comment = $get_loan_guarantor_request_details->decline_comment;
                        $guarantor_loan_amount = $get_loan_guarantor_request_details->amount;
                        $get_loan_type = $this->loan_types_m->get($loan_type_id);
                        $get_loan_application_details = $this->loan_applications_m->get($loan_application_id);
                        $loan_amount = isset($get_loan_application_details->loan_amount) ? $get_loan_application_details->loan_amount : '';
                        $get_loan_applicant_details = $this->users_m->get($loan_request_applicant_user_id);
                        $get_loan_guarantor_member_details = $this->members_m->get($guarantor_member_id);
                        $guarantor_user_id = $get_loan_guarantor_member_details->user_id;
                        $get_loan_guarantor_user_details = $this->users_m->get($guarantor_user_id);
                        $loan_progress_status = $get_loan_guarantor_request_details->loan_request_progress_status;
                        ?>
                    <tr>
                        <td><span>
                                <input type="checkbox" name="check" value="all" class="check_all"></span></td>
                        <td><?php echo $count; ?></td>

                        <td>
                            <strong> Loan Name : </strong> <?php echo isset($get_loan_application_details->name) ? $get_loan_application_details->name : '' ?> <br>
                            <strong> Loan Applicant Name : </strong> <?php echo $get_loan_applicant_details->first_name . ' ' . $get_loan_applicant_details->last_name ?> <br>
                            <strong> Guarantor Name : </strong> </strong> <?php echo $get_loan_guarantor_user_details->first_name . ' ' . $get_loan_guarantor_user_details->last_name ?> <br>
                            <strong> Guarantor Amount : </strong> <?php echo number_to_currency($guarantor_loan_amount) ?> <br>
                            <strong> Loan Duration : </strong> <span><?php echo isset($get_loan_type->minimum_repayment_period) ? $get_loan_type->minimum_repayment_period : '' ?> -<?php echo isset($get_loan_type->maximum_repayment_period) ? $get_loan_type->maximum_repayment_period : ''  ?> months</span> &nbsp;<strong> Loan Request Status: </strong>
                            <?php if ($loan_progress_status == 1) { ?>
                                <span class="label label-success">In Progress</span><?php
                                                                                } else if ($loan_progress_status == 2) { ?>
                                <span class="label label-danger"> loan Declined</span>
                            <?php } else if ($loan_progress_status == 3) { ?>
                                <span class="label label-success"> loan Approved</span>
                            <?php } else { ?>
                                <span class="label label-danger"> loan Declined</span>
                            <?php } ?>

                        </td>
                        <td class="text-right"><?php echo number_to_currency($loan_amount) ?></td>
                        <td> <?php if ($loan_progress_status == 2) {
                                ?><a href="<?php echo base_url('bank/loans/delete_loans_declined/' . $loan_request_id . '') ?>" class="btn btn-xs btn-danger">
                                    <i class="icon-thrash"></i> Delete &nbsp;&nbsp;
                                </a><?php
                                } else if ($loan_progress_status == 2) { ?>
                                <a href="<?php echo base_url('bank/loans/edit1/' . $loan_request_id . '') ?>" class="btn btn-xs btn-primary">
                                    <i class="icon-thrash"></i> Edit &nbsp;&nbsp;
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo base_url('bank/loans/delete_loans_declined/' . $loan_request_id . '') ?>" class="btn btn-xs btn-danger">
                                    <i class="icon-thrash"></i> Delete &nbsp;&nbsp;
                                </a><?php

                                } ?>
                        </td>

                    </tr><?php

                            ?>
                </tbody>
            </table><?php
                } else { ?>
            <div class="alert alert-danger">
                <button class="close" data-dismiss="alert"></button>
                <strong>Error!</strong> Could not find loans application details
            </div>
<?php
                }
            }

            public function import_members()
            {
                set_time_limit(0);
                ini_set('memory_limit', '4096M');
                $response = array();
                // if($_POST){
                $directory = './uploads/files/csvs';
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, TRUE);
                }
                $config['upload_path'] = FCPATH . 'uploads/files/csvs/';
                $config['allowed_types'] = 'xls|xlsx|csv';
                $config['max_size'] = '1024';
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('member_imports')) {
                    $successful_invitations_count = 0;
                    $unsuccessful_invitations_count = 0;
                    $upload_data = $this->upload->data();
                    $file_path = $upload_data['full_path'];
                    $this->load->library('Excel_Library');
                    //$excel_sheet = new PHPExcel();
                    $spreadsheet = new Spreadsheet();
                    if (file_exists($file_path)) {
                        //$file_type = PHPExcel_IOFactory::identify($file_path);
                        $file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file_path);
                        $excel_reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);
                        $excel_book = $excel_reader->load($file_path);
                        $sheet = $excel_book->getActiveSheet(0);
                        //$worksheet = $spreadsheet->getActiveSheet();  
                        $allowed_column_headers = array('First Name', 'Last Name', 'Phone', 'Email', 'ID Number', 'Membership Number', 'Date of Birth(DD-MM-YYYY)', 'Location', 'Next of Kin Full Name', 'Next of Kin ID Number', 'Next of Kin Phone', 'Next of Kin Relationship');
                        $count = count($allowed_column_headers) - 1;
                        // Get the highest row and column numbers referenced in the worksheet
                        $highestRow = $sheet->getHighestRow(); // e.g. 10
                        $highestColumn = $sheet->getHighestColumn(); // e.g 'F'
                        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
                        $value_array = array();
                        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                            $value = $sheet->getCellByColumnAndRow($col, 1)->getValue();
                            $value_array[] = $value;
                            if (in_array(trim($value), $allowed_column_headers)) {
                                $column_validation = true;
                            } else {
                                $column_validation = false;
                                break;
                            }
                        }

                        if ($column_validation) {
                            //$highestRow = $sheet->getHighestRow();
                            $members = array();
                            for ($row = 2; $row <= $highestRow; ++$row) {
                                $first_name = '';
                                $last_name = '';
                                $phone = '';
                                $email = '';
                                for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                                    if ($col == 1) {
                                        $first_name = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    } else if ($col == 2) {
                                        $last_name = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    } else if ($col == 3) {
                                        $phone = valid_phone($sheet->getCellByColumnAndRow($col, $row)->getValue());
                                    } else if ($col == 4) {
                                        $email = filter_var($sheet->getCellByColumnAndRow($col, $row)->getValue(), FILTER_SANITIZE_EMAIL);
                                    } else if ($col == 5) {
                                        $id_number = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    } else if ($col == 6) {
                                        $membership_number = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    } else if ($col == 7) {
                                        $date_of_birth = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    } else if ($col == 8) {
                                        $physical_address = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    } else if ($col == 9) {
                                        $next_of_kin_full_name = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    } else if ($col == 10) {
                                        $next_of_kin_id_number = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    } else if ($col == 11) {
                                        $next_of_kin_phone = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    } else if ($col == 12) {
                                        $next_of_kin_relationship = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                                    }
                                }
                                $members[] = array(
                                    'first_name' => $first_name,
                                    'last_name' => $last_name,
                                    'phone' => $phone,
                                    'email' => $email,
                                    'id_number' => $id_number,
                                    'membership_number' => $membership_number,
                                    'date_of_birth' => $date_of_birth,
                                    'physical_address' => $physical_address,
                                    'next_of_kin_full_name' => $next_of_kin_full_name,
                                    'next_of_kin_id_number' => $next_of_kin_id_number,
                                    'next_of_kin_phone' => $next_of_kin_phone,
                                    'next_of_kin_relationship' => $next_of_kin_relationship,
                                );
                            }

                            if (empty($members)) {
                                $response = array(
                                    'status' => 0,
                                    'message' => 'The Member list is empty. Kindly fill and upload a filled file',
                                );
                            } else {
                                if (!$members[0]['first_name']) {
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'The Member list is empty. Kindly fill and upload a filled file',
                                    );
                                } else {
                                    $members = (object)$members;
                                    $successes = 0;
                                    $duplicates = 0;
                                    $ignores = 0;
                                    $errors = 0;
                                    $phones = array();
                                    $emails = array();
                                    $row = 2;
                                    foreach ($members as $member) {
                                        $member = (object)$member;
                                        if ($member->first_name || $member->last_name || $member->phone || $member->email) {
                                            if ($member->first_name && $member->last_name && valid_phone($member->phone)) {
                                                $email = valid_email($member->email) ? $member->email : '';
                                                $first_name = strip_tags($member->first_name);
                                                $last_name = strip_tags($member->last_name);
                                                $membership_number = strip_tags($member->membership_number);
                                                $date_of_birth = strip_tags($member->date_of_birth);
                                                $physical_address = strip_tags($member->physical_address);
                                                $id_number = strip_tags($member->id_number);
                                                $next_of_kin_full_name = strip_tags($member->next_of_kin_full_name);
                                                $next_of_kin_id_number = strip_tags($member->next_of_kin_id_number);
                                                $next_of_kin_phone = strip_tags($member->next_of_kin_phone);
                                                $next_of_kin_relationship = strip_tags($member->next_of_kin_relationship);
                                                if ($this->group_members->add_member_to_group(
                                                    $this->group,
                                                    $first_name,
                                                    $last_name,
                                                    $member->phone,
                                                    $member->email,
                                                    FALSE,
                                                    FALSE,
                                                    $this->user,
                                                    $this->member->id,
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    TRUE,
                                                    $id_number,
                                                    $membership_number,
                                                    $date_of_birth,
                                                    $physical_address,
                                                    $next_of_kin_full_name,
                                                    $next_of_kin_id_number,
                                                    $next_of_kin_phone,
                                                    $next_of_kin_relationship
                                                )) {
                                                    $successful_invitations_count++;
                                                } else {
                                                    $unsuccessful_invitations_count++;
                                                }
                                            } else {
                                                $error_message = ' Row #' . $row;
                                                if ($member->first_name == '') {
                                                    $error_message .= ' First name missing';
                                                }
                                                if ($member->last_name == '') {
                                                    if ($error_message == ' Row #' . $row) {
                                                        $error_message .= ' Last name missing';
                                                    } else {
                                                        $error_message .= ' ,last name missing';
                                                    }
                                                }
                                                if (valid_phone($member->phone) == FALSE) {
                                                    if ($error_message == ' Row #' . $row) {
                                                        $error_message .= ' Phone invalid or missing';
                                                    } else {
                                                        $error_message .= ' and phone invalid or missing';
                                                    }
                                                }
                                                $this->error_feedback[] = $error_message;
                                                $errors++;
                                            }
                                            $row++;
                                        }
                                    }
                                    if ($successful_invitations_count) {
                                        if ($successful_invitations_count == 1) {
                                            $response = array(
                                                'status' => 1,
                                                'message' => $successful_invitations_count . ' member successfully added to your group.',
                                                'refer' => site_url('bank/members/listing'),
                                            );
                                        } else {
                                            $response = array(
                                                'status' => 1,
                                                'message' => $successful_invitations_count . ' members successfully added to your group.',
                                                'refer' => site_url('bank/members/listing'),
                                            );
                                        }
                                    }
                                    if ($unsuccessful_invitations_count) {
                                        if ($unsuccessful_invitations_count == 1) {
                                            $response = array(
                                                'status' => 1,
                                                'message' => $unsuccessful_invitations_count . ' member details were updated.',
                                            );
                                        } else {
                                            $response = array(
                                                'status' => 1,
                                                'message' => $unsuccessful_invitations_count . ' members details were updated.',
                                            );
                                        }
                                    }
                                    if ($errors) {
                                        if ($errors == 1) {
                                            $response = array(
                                                'status' => 0,
                                                'message' => $errors . ' error encountered while importing, some details were missing.',
                                            );
                                        } else {
                                            $this->session->set_flashdata('error', $errors . ' errors encountered while importing, some details were missing.');
                                            $response = array(
                                                'status' => 0,
                                                'message' => $this->error_feedback,
                                            );
                                        }
                                    }
                                    if ($this->error_feedback) {
                                        $response = array(
                                            'status' => 0,
                                            'message' => $this->error_feedback,
                                        );
                                    }
                                    $this->group_members->set_active_group_size($this->group->id);
                                    //$this->setup_tasks_tracker->set_completion_status('add-group-members',$this->group->id,$this->user->id);
                                }
                            }
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => 'Member list file does not have the correct format',
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 0,
                            'message' => 'Member list file was not found',
                        );
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'File upload error: ' . $this->upload->display_errors(),
                    );
                }
                // }else{
                //     $response = array(
                //         'status' => 0,
                //         'message' => 'Kindly select file to upload',
                //     );
                // }
                echo json_encode($response);
            }

            function add_members()
            {
                $names = $this->input->post('names');
                $phones = $this->input->post('phones');
                $email_addresses = $this->input->post('email_addresses');
                $id_numbers = $this->input->post('id_numbers');
                $limits = $this->input->post('limits');
                $group_role_ids = $this->input->post('group_role_ids');
                $status = 0;
                $message = "";
                $calling_codes = $this->input->post('calling_codes');
                $members = array();
                foreach ($names as $key => $full_name) {
                    if ($full_name) {
                        $full_names = explode(' ', $full_name);
                        if (count($full_names) > 1) {
                            $count = count($full_names);
                            if ($count == 2) {
                                $first_name = $full_names[0];
                                $last_name = $full_names[1];
                            } else if ($count == 3) {
                                $first_name = $full_names[0];
                                $last_name = $full_names[1] . ' ' . $full_names[2];
                            } else if ($count == 4) {
                                $first_name = $full_names[0];
                                $last_name = $full_names[1] . ' ' . $full_names[2] . ' ' . $full_names[3];
                            }
                            if ($first_name && $last_name) {
                                $phone = $calling_codes[$key] . $phones[$key];
                                $id_number = $id_numbers[$key];
                                $email = $email_addresses[$key];
                                $loan_limit = $limits[$key];
                                $group_role_id = $group_role_ids[$key];
                                $calling_code = $calling_codes[$key];
                                $original_phone = $phones[$key];
                                
                                if (valid_phone($phone)) {
                                    if ($email) {
                                        if (valid_email($email)) {
                                        } else {
                                            $status = 0;
                                            $message = 'Enter valid email address ' . $email;
                                            break;
                                        }
                                        if(valid_currency($loan_limit)){
                                            // continue;
                                        }
                                        else{
                                            $status = 0;
                                            $message = 'Enter valid loan Limit';
                                            break;
                                        }
                                        if(empty($id_number)){
                                            $status = 0;
                                            $message = 'ID number is required';
                                            break;
                                        }
                                       
                                    }
                                    $members[] = array(
                                        'first_name' => ucwords($first_name),
                                        'last_name' => ucwords($last_name),
                                        'phone' => $phone,
                                        'id_number' => $id_number,
                                        'email' => $email,
                                        'loan_limit' => $loan_limit,
                                        'group_role_id' => 1,
                                        'calling_code' => $calling_code,
                                        'original_phone' => $original_phone,
                                    );
                                } else {
                                    $status = 0;
                                    $message = 'Invalid phone number ' . $phone;
                                    break;
                                }
                            } else {
                                $status = 0;
                                $message = 'Full name entered is not valid. Enter first name and last name';
                                break;
                            }
                        } else {
                            $status = 0;
                            $message = 'Full name entered is not valid. Enter first and last name';
                            break;
                        }
                    } else {
                        $status = 0;
                        $message = "Ensure all full name fields are not empty";
                        break;
                    }
                }
                $response = array(
                    'status' => $status,
                    'message' => $message,
                );
                if ($members) {
                    $members = (object)$members;
                    $successes = 0;
                    $duplicates = 0;
                    $ignores = 0;
                    $errors = 0;
                    $phones = array();
                    $emails = array();
                    $successful_invitations_count = 0;
                    $unsuccessful_invitations_count = 0;
                     $this->group=array(
                        "id"=>1
                     );
                     
                    foreach ($members as $member) {
                        $member = (object)$member;
                        if ($this->group_members->add_member_to_group(
                            $this->group,
                            $member->first_name,
                            $member->last_name,
                            $member->phone,
                            $member->email,
                            FALSE,
                            FALSE,
                            $this->user,
                            $this->member->id ?? '',
                            $member->group_role_id,
                            '',
                            $member->calling_code,
                            $member->original_phone,
                            FALSE,
                            $id_number,
                            $loan_limit,
                        )) {
                            $successful_invitations_count++;
                        } else {
                            $unsuccessful_invitations_count++;
                        }
                    }
                    if ($successful_invitations_count) {
                        if ($successful_invitations_count == 1) {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_invitations_count . ' user successfully added .',
                                'refer' => site_url('bank/members/listing'),
                            );
                        } else {
                            $response = array(
                                'status' => 1,
                                'message' => $successful_invitations_count . ' user successfully added.',
                                'refer' => site_url('bank/members/listing'),
                            );
                        }
                    }
                    if ($unsuccessful_invitations_count) {
                        if ($unsuccessful_invitations_count == 1) {
                            $response = array(
                                'status' => 1,
                                'message' => $unsuccessful_invitations_count . ' member was not added to your group.',
                                'refer' => site_url('bank/members/listing'),
                            );
                        } else {
                            $response = array(
                                'status' => 1,
                                'message' => $unsuccessful_invitations_count . ' members were not added to your group.',
                                'refer' => site_url('bank/members/listing'),
                            );
                        }
                    }
                    $this->group_members->set_active_group_size($this->group->id);
                    //$this->setup_tasks_tracker->set_completion_status('add-group-members',$this->group->id,$this->user->id);
                }
                echo json_encode($response);
            }

            function edit_members()
            {
                $status = 0;
                $message = "";
                $id = $this->input->post('id');
                $group_role_options = $this->group_roles_m->get_group_role_options();
                if ($id) {
                    if ($post = $this->members_m->get_group_member($id)) {
                        $entries_are_valid = TRUE;
                        $errors = array();
                        $error_messages = array();
                        $successes = array();
                        $validation_errors = array();
                        $allocations_total = 0;
                        $posts = $_POST;
                        if (isset($posts['full_names'])) {
                            $count = count($_POST['full_names']);
                            for ($i = 0; $i < $count; $i++) :
                                //Full names
                                if (isset($posts['full_names'][$i])) {
                                    if ($posts['full_names'][$i]) {
                                    } else {
                                        $entries_are_valid = FALSE;
                                        $message = 'Please enter next of kin full name. Enter first name and last name ';
                                        break;
                                    }
                                } else {
                                    $entries_are_valid = FALSE;
                                    $message = 'Please enter next of kin full name. Enter first name and last name ';
                                    break;
                                }

                                //ID Numbers
                                if (isset($posts['id_numbers'][$i])) {
                                    if (is_numeric($posts['id_numbers'][$i])) {
                                    } else {
                                        $entries_are_valid = FALSE;
                                        $message = 'Please enter a valid id number';
                                        break;
                                    }
                                } else {
                                    $entries_are_valid = FALSE;
                                    $message = 'Please enter an id number';
                                    break;
                                }

                                //Next of Kin Phones
                                if (isset($posts['next_of_kin_phones'][$i])) {
                                    if ($posts['next_of_kin_phones'][$i] == '') {
                                        $entries_are_valid = FALSE;
                                        $message = 'Please enter an id number';
                                        break;
                                    } else {
                                        if (valid_phone($posts['next_of_kin_phones'][$i])) {
                                        } else {
                                            $entries_are_valid = FALSE;
                                            $message = 'Please enter next of kin valid phone number';
                                            break;
                                        }
                                    }
                                } else {
                                    $entries_are_valid = FALSE;
                                    $message = 'Please enter next of kin valid phone number';
                                    break;
                                }
                                //Next of Kin Emails
                                if (isset($posts['next_of_kin_emails'][$i])) {
                                    if ($posts['next_of_kin_emails'][$i]) {
                                        if (valid_email($posts['next_of_kin_emails'][$i])) {
                                        } else {
                                            $entries_are_valid = FALSE;
                                            $message = 'Please enter next of kin valid email address';
                                            break;
                                        }
                                    }
                                }

                                //Relationships
                                if (isset($posts['relationships'][$i])) {
                                } else {
                                    $entries_are_valid = FALSE;
                                    $message = 'Please enter a relationship';
                                    break;
                                }

                                //Allocations
                                if (isset($posts['allocations'][$i])) {
                                    if ($posts['allocations'][$i] == '') {
                                        $entries_are_valid = FALSE;
                                        $message = 'Please enter an allocation';
                                        break;
                                    } else {
                                        if (is_numeric($posts['allocations'][$i])) {
                                            $successes['allocations'][$i] = 1;
                                            $errors['allocations'][$i] = 0;
                                            $allocations_total += $posts['allocations'][$i];
                                        } else {
                                            $entries_are_valid = FALSE;
                                            $message = 'Please enter a valid allocation value';
                                            break;
                                        }
                                    }
                                } else {
                                    $entries_are_valid = FALSE;
                                    $message = 'Please enter an allocation';
                                    break;
                                }

                            endfor;
                            if ($count > 1) {
                                if ($allocations_total !== 100) {
                                    $entries_are_valid = FALSE;
                                    $message = 'Allocation to Next of Kin needs to add up to 100%';
                                }
                            }
                        }

                        $this->form_validation->set_rules($this->validation_rules);
                        if ($this->form_validation->run()) {
                            $user_input = array(
                                'first_name' => $this->input->post('first_name'),
                                'last_name' => $this->input->post('last_name'),
                                'email' => $this->input->post('email'),
                                'phone' => valid_phone($this->input->post('phone')),
                                'modified_on' => time(),
                                'modified_by' => $this->user->id
                            );
                            // if($post->user_id == $this->user->id){
                            // $is_admin = 1;
                            // }else{
                            $is_admin = $this->input->post('is_admin') ? 1 : 0;
                            // }
                            if ($this->ion_auth->update($post->user_id, $user_input)) {
                                $member_input = array(
                                    'membership_number' => $this->input->post('membership_number'),
                                    'group_role_id' => $this->input->post('group_role_id'),
                                    'is_admin' => $is_admin,
                                    'modified_on' => time(),
                                    'modified_by' => $this->user->id
                                );
                                if ($this->members_m->update($post->id, $member_input)) {
                                    //continue
                                } else {
                                    $response = array(
                                        'status' => $status,
                                        'message' => 'Could not update member profile',
                                    );
                                }
                            } else {
                                $response = array(
                                    'status' => $status,
                                    'message' => 'Could not update user profile',
                                );
                            }

                            if ($entries_are_valid) {
                                $this->members_m->delete_next_of_kin($this->group->id, $id);
                                $successful_next_of_kin_entry = 0;
                                $unsuccessful_next_of_kin_entry = 0;
                                if (isset($posts['full_names'])) {
                                    $count = count($posts['full_names']);
                                    for ($i = 0; $i <= $count; $i++) :
                                        if (isset($posts['full_names'][$i]) && isset($posts['id_numbers'][$i]) && isset($posts['next_of_kin_phones'][$i]) && isset($posts['next_of_kin_emails'][$i]) && isset($posts['relationships'][$i]) && isset($posts['allocations'][$i])) :
                                            $input = array(
                                                'full_name' => $posts['full_names'][$i],
                                                'id_number' => $posts['id_numbers'][$i],
                                                'phone' => $posts['next_of_kin_phones'][$i],
                                                'email' => $posts['next_of_kin_emails'][$i],
                                                'relationship' => $posts['relationships'][$i],
                                                'allocation' => $posts['allocations'][$i],
                                                'member_id' => $id,
                                                'group_id' => $this->group->id,
                                                'created_by' => $this->user->id,
                                                'created_on' => time(),
                                            );
                                            if ($next_of_kin_id = $this->members_m->insert_next_of_kin($input)) {
                                                $successful_next_of_kin_entry++;
                                            } else {
                                                //do nothing for now
                                                $unsuccessful_next_of_kin_entry++;
                                            }
                                        endif;
                                    endfor;
                                }
                                $response = array(
                                    'status' => 1,
                                    'message' => 'successfully edited profile: ' . $successful_next_of_kin_entry . ' next of kin entry saved',
                                    'refer' => site_url('bank/members/listing'),
                                );
                            } else {
                                $response = array(
                                    'status' => $status,
                                    'message' => $message,
                                );
                            }
                        } else {
                            $response = array(
                                'status' => $status,
                                'message' => validation_errors(),
                            );
                        }
                    } else {
                        $response = array(
                            'status' => $status,
                            'message' => 'Could not find group member',
                        );
                    }
                } else {
                    $response = array(
                        'status' => $status,
                        'message' => 'Member id not supplied',
                    );
                }
                echo json_encode($response);
            }

            function get_member()
            {
                $response = array();
                $id = $this->input->post('id');
                if ($id) {
                    $post = $this->members_m->get_group_member($id);
                    if ($post) {
                        $response = array(
                            'status' => 1,
                            'data' => $post,
                            'message' => 'Success'
                        );
                    } else {
                        $response = array(
                            'status' => 0,
                            'message' => 'Member details is missing'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'Member id is missing'
                    );
                }
                echo json_encode($response);
            }

            function suspend_member()
            {
                $data = array();
                $response = array();
                $post = new stdClass();
                $posts = $_POST;
                $message = '';
                $password = $this->input->post('password');
                $comment = $this->input->post('comment');
                $member_id = $this->input->post('member_id');
                $this->form_validation->set_rules($this->suspension_validation_rules);
                if ($this->form_validation->run()) {
                    if ($this->ion_auth->login($this->user->phone, $this->input->post('password'), 1)) {
                        $post = $this->members_m->get_group_member($member_id);
                        if ($this->user->id == $post->user_id || $post->user_id == $this->group->owner) {
                            $response = array(
                                'status' => 0,
                                'message' => 'You cannot suspend this member.',
                            );
                        } else {
                            $input = array(
                                'active' => 0,
                                'modified_on' => time(),
                                'modified_by' => $this->user->id,
                                'suspension_reason' => $comment,
                            );
                            if ($this->members_m->update($member_id, $input)) {
                                $this->group_members->set_active_group_size($this->group->id);
                                $response = array(
                                    'status' => 1,
                                    'message' => $post->first_name . ' ' . $post->last_name . ' suspended successfully. ',
                                    'refer' => site_url('bank/members/listing'),
                                );
                            } else {
                                $response = array(
                                    'status' => 0,
                                    'message' => $post->first_name . ' ' . $post->last_name . ' could not be suspended. ',
                                );
                            }
                        }
                    } else {
                        $response = array(
                            'status' => 0,
                            'message' => 'Password provided is incorrect.',
                        );
                    }
                } else {
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

            function pending_member_suspensions()
            {
                $posts = $this->members_m->get_member_suspension_requests($this->group->id);
                if ($posts) {

                    echo ' 
                <div class="table-responsive datatable">
                    <table class="table table-bordered table-hover ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Suspended on</th>
                                <th>Memeber Suspended</th>
                                <th>Reason </th>
                            </tr>
                        </thead>
                        <tbody>';
                    $i = $this->uri->segment(5, 0);
                    $i++;
                    foreach ($posts as $post) {
                        $user = $this->ion_auth->get_user($post->user_id);
                        echo '
                                    <tr data-toggle="modal" class="suspension_request" id="' . $post->id . '" data-target="#suspension_request_modal" style="cursor:pointer;">
                                        <th scope="row">' . ($i++) . '</th>
                                        <td>' . timestamp_to_date($post->request_date) . '</td>
                                        <td>' . $user->first_name . ' ' . $user->last_name . '</td>
                                        <td>
                                            ' . $post->suspension_reason . '
                                            <span class="m-badge m-badge--info m-badge--wide float-right">View...</span>
                                        </td>
                                    </tr>
                                ';
                    }
                    echo '
                        </tbody>
                    </table>
                </div>
                <div class="row col-md-12">';
                    if (!empty($pagination['links'])) :
                        echo $pagination['links'];
                    endif;
                    echo '
                </div>
            ';
                } else {
                    echo '
                <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                    <strong>Sorry!</strong> There are no member suspension requests.
                </div>
            ';
                }
            }

            function pending_suspend_member_approvals($suspension_id = 0)
            {
                $response = array();
                $html = '';
                //$suspension_id = $this->input->post('suspension_id');
                if ($suspension_id) {
                    $suspension = $this->members_m->get_member_suspension_request($suspension_id);
                    if ($suspension) {
                        $posts = $this->members_m->get_all_member_approval_suspension_requests($suspension_id);
                        if ($posts) {
                            $html .= '<div class="text-center">
                                <h5>Group Official Approvals</h5>
                            </div>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Member</th>
                                        <th>Request Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>';
                            $i = 0;
                            foreach ($posts as $key => $post) :
                                $i++;
                                $html .= '<tr>
                                    <th scope="row">' . $i . '</th>
                                    <td>' . $this->active_group_member_options[$post->member_id] . '</td>
                                    <td>' . timestamp_to_date($post->created_on) . '</td>
                                    <td>';

                                if ($post->is_approved) {
                                    $html .= '<span class="m-badge m-badge--success m-badge--wide">Approved</span>';
                                } elseif ($post->is_declined) {
                                    $html .= '<span class="m-badge m-badge--danger m-badge--wide">Declined</span>';
                                } else {
                                    $html .= '<span class="m-badge m-badge--warning m-badge--wide">Pending</span>';
                                    if ($post->member_id == $this->member->id) {
                                        $html .= '
                                                <span class="float-right withdrawal_request_actions">
                                                    <a href="' . base_url("ajax/member/respond/") . $post->id . '" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon">
                                                        <span>
                                                            <span>Respond</span>
                                                            &nbsp;
                                                            &nbsp;
                                                            <i class="fa fa-magic"></i>
                                                        </span>
                                                    </a>
                                                </span>
                                            ';
                                    }
                                }
                                $html .= '
                                </td>';
                            endforeach;
                        } else {
                            $html .= '<div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                            <strong>Sorry!</strong> Signatory Approval details is missing.
                        </div>
                        ';
                        }
                    } else {
                        $html .= '<div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                        <strong>Sorry!</strong> Suspension details is missing.
                    </div>
                    ';
                    }
                } else {
                    $html .= '<div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
                        <strong>Sorry!</strong> Suspension request id is required.
                    </div>
                    ';
                }
                $response = array(
                    'status' => 1,
                    'data' => $html,
                );

                echo json_encode($response);
            }

            function send_invitation()
            {
                $response = array();
                $member_id = $this->input->post('member_id');
                if ($member_id) {
                    if (array_key_exists($member_id, $this->active_group_member_options)) {
                        $post = $this->members_m->get_group_member($member_id);
                        $user = $this->ion_auth->get_user($post->user_id);
                        if ($user) {
                            //if($this->messaging->send_group_invitation_to_user($this->group,$user,$post,$this->user,$this->member->id,TRUE,TRUE)){
                            if ($this->messaging->send_single_member_first_time_login_invitation_message($this->group, 1, $this->user, $post->user_id)) {
                                $response = array(
                                    'status' => 1,
                                    'message' => translate('Invitation sent successfully'),
                                );
                            } else {
                                $response = array(
                                    'status' => 0,
                                    'message' => translate('Invitation not sent'),
                                );
                            }
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => translate('User details missing'),
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 0,
                            'message' => translate('Member does not belong to the group'),
                        );
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => translate('Member id request missing'),
                    );
                }
                echo json_encode($response);
            }

            function delete()
            {
                $response  = array();
                $id = $this->input->post('id');
                if ($id) {
                    $post = $this->members_m->get_group_member($id);
                    if ($post) {
                        $password = $this->input->post('password');
                        $identity = valid_phone($this->user->phone) ?: $this->user->email;
                        if ($this->ion_auth->login($identity, $password)) {
                            if ($this->user->id == $this->group->owner || $this->ion_auth->is_admin()) {
                                if ($this->user->id == $post->user_id || $post->user_id == $this->group->owner) {
                                    $response = array(
                                        'status' => 0,
                                        'message' => 'You cannot delete this member from the group.',
                                    );
                                } else {
                                    if ($this->deposits_m->get_group_member_deposits($id, $this->group->id)) {
                                        $response = array(
                                            'status' => 0,
                                            'message' => 'You are not allowed to delete this member. Kindly complete setup and request permission to suspend member',
                                        );
                                    } else {
                                        if ($this->transactions->void_all_group_member_transactions($this->group->id, $id)) {
                                            $input = array(
                                                'active' => 0,
                                                'is_deleted' => 1,
                                                'modified_on' => time(),
                                                'modified_by' => $this->user->id
                                            );
                                            if ($this->members_m->update($id, $input)) {
                                                $this->group_members->set_active_group_size($this->group->id);
                                                $response = array(
                                                    'status' => 1,
                                                    'message' => $post->first_name . ' ' . $post->last_name . ' deleted successfully. ',
                                                );
                                            } else {
                                                $response = array(
                                                    'status' => 0,
                                                    'message' => $post->first_name . ' ' . $post->last_name . ' could not be deleted. '
                                                );
                                            }
                                        } else {
                                            $response = array(
                                                'status' => 0,
                                                'message' => 'Something went wrong while voiding all member records',
                                            );
                                        }
                                    }
                                }
                            } else {
                                $response = array(
                                    'status' => 0,
                                    'message' => 'You do not have sufficient permissions to delete a member.',
                                );
                            }
                        } else {
                            $response = array(
                                'status' => 0,
                                'message' => 'You can not proceed with the process. You entered a wrong password',
                            );
                        }
                    } else {
                        $response = array(
                            'status' => 0,
                            'message' => 'Could not find the member selected',
                        );
                    }
                } else {
                    $response = array(
                        'status' => 0,
                        'message' => 'Could not find the member selected',
                    );
                }
                echo json_encode($response);
            }

            function get_group_member_options()
            {
                $members = $this->members_m->get_group_member_options($this->group->id);
                echo json_encode($members);
            }
        }

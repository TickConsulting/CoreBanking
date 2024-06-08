<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

	protected $data = array();
    protected $send_to_list = array(
        ' ' => '--Select Applicants to message--',
        '1' => 'All Applicants',
        '2' => 'Individual Applicants',
    );
    protected $members = array();
    protected $validation_rules = array(
        array(
            'field' =>  'send_to',
            'label' =>  'Send Message To',
            'rules' =>  'xss_clean|required|trim|numeric',
        ),
        array(
            'field' =>  'message',
            'label' =>  'Message',
            'rules' =>  'xss_clean|required',
        ),
    );

    function __construct(){
        parent::__construct();
        $this->members = $this->members_m->get_group_member_options_for_messaging();
        $this->load->model('members/members_m');
        $this->load->model('sms_m');
    }

    function get_queued_sms_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $this->group_member_options=$this->members_m->get_group_member_options();
        $filter_parameters = array(
            'from' => $from,
            'to' => $to,
            'member_id' => $this->input->get('member_id')?:'',
        );
        $total_rows = $this ->sms_m->count_all_queued_group_sms($filter_parameters,TRUE);
        $pagination = create_pagination('group/sms/queued_sms/page/', $total_rows,200,5,TRUE);
        $posts = $this->sms_m->limit($pagination['limit'])->get_all_queued_group_sms($filter_parameters,TRUE);
        if(!empty($posts)){
            echo form_open('group/sms/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo'
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> SMSes</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links'];
                    echo '</div></div>';
                endif;
                echo '
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th width=\'2%\'>
                                 <label class="m-checkbox">
                                    <input type="checkbox" name="check" value="all" class="check_all">
                                    <span></span>
                                </label>
                            </th>
                            <th>
                                #
                            </th>
                            <th nowrap>
                                '.translate('Recipient').'
                            </th>
                            <th nowrap>
                                '.translate('Phone Number').'
                            </th>
                            <th nowrap>
                                '.translate('Message').'
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0);
                        foreach($posts as $post): $sent_by = $this->ion_auth->get_user($post->created_by);
                            echo '
                            <tr>
                                <td>
                                    <label class="m-checkbox">
                                        <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>'.($i+1).'</td>
                                <td nowrap>'.(isset($this->group_member_options[$post->member_id])?$this->group_member_options[$post->member_id]:"").'</td>
                                <td nowrap>'.$post->sms_to.'</td>
                                <td>'.$post->message.'<br/>
                                    <small><strong>Queued by : </strong>'.$sent_by->first_name.' '.$sent_by->last_name.',
                                    <strong>Queued on : </strong> '.timestamp_to_date_and_time($post->created_on).'</small>
                                </td>
                                <td>
                                    <a href="'.site_url('group/sms/delete/'.$post->id).'" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button" data-message="Are you sure you want to delete sms?">
                                        <span>
                                            <i class="la la-trash"></i>
                                            <span>
                                                Delete &nbsp;&nbsp;
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
                <div class="row col-md-12">';
                    if( ! empty($pagination['links'])):
                    echo $pagination['links'];
                    endif;
                echo '
                </div>
                <div class="clearfix"></div>';
                if($posts):
                    echo '
                    <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_delete\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-trash\'></i> Bulk Delete</button>';
                endif;
                echo '
                <div class="clearfix"></div>';
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                    '. translate('Ooops') .'! '. translate('Looks like you currently have no queued smses') .'.                       
                </div>
            ';
        }
    }


    function get_member_received_sms_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $total_rows = $this ->sms_m->count_all_group_member_received_smses();
        $pagination = create_pagination('member/sms/page/', $total_rows,200,5,TRUE);
        $posts = $this->sms_m->limit($pagination['limit'])->get_all_group_member_received_smses();
        if(!empty($posts)){
            echo form_open('member/sms/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo'
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> SMSes</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links'];
                    echo '</div></div>';
                endif;
                echo '
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th>
                                #
                            </th>
                            <th nowrap>
                                Sent By
                            </th>
                            <th nowrap>
                                Message
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0);
                        foreach($posts as $post): $sent_by = $this->ion_auth->get_user($post->created_by);
                            echo '
                            <tr>
                                <td>'.($i+1).'</td>
                                <td nowrap>'.$sent_by->first_name.' '.$sent_by->last_name.'</td>
                                <td>'.$post->message.'<br/>';
                                    if($post->sms_result_id){echo '<small><strong>Received On : </strong>'.timestamp_to_datetime($post->created_on);}
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
                echo '</div>';
            echo form_close();
        }else{
            echo '
                <div style="background:#fff!important;border: none;">
                    <div class="alert m-alert--outline alert-metal text-center">
                        <h4 class="block">'.translate('Information').'! '.translate('No records to display').'</h4>
                        <p>
                            '.translate('You have not received any smses yet').'
                        </p>
                    </div>
                </div>
            ';
        }
    }


    function get_sms_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'from' => $from,
            'to' => $to,
            'member_id' => $this->input->get('member_id')?:''
        );
        $total_rows = $this ->sms_m->count_all_group_sms($filter_parameters,TRUE);
        $pagination = create_pagination('group/sms/listing/page/', $total_rows,100,5,TRUE);
        $posts = $this->sms_m->limit($pagination['limit'])->get_all_group_sms($filter_parameters,TRUE);
        if(!empty($posts)){
            echo form_open('admin/sms/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                    echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> SMSes</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links'];
                    echo '</div></div>';
                endif;
            echo '
                <table class="table m-table m-table--head-separator-primary">
                    <thead>
                        <tr>
                            <th>
                                #
                            </th>
                            <th nowrap width="15%">
                               '.translate('Recipient').'
                            </th>
                            <th nowrap width="15%">
                               '.translate('Phone Number').'
                            </th>
                            <th nowrap width="60%">
                               '.translate('Message').'
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = $this->uri->segment(5, 0); foreach($posts as $post):
                        $sent_by = $this->ion_auth->get_user($post->created_by);
                            echo '
                            <tr class="'.$post->id.'_active_row">
                                <td>'.($i+1).'</td>
								<td nowrap>'.(isset($this->group_member_options[$post->member_id])?$this->group_member_options[$post->member_id]:"").'</td>
                                <td nowrap>'.$post->sms_to.'</td>
                                <td>'.$post->message.'<br/>
										<small><strong> Sent By : </strong>'.$sent_by->first_name.' '.$sent_by->last_name.', <strong> Sent On : </strong> '.timestamp_to_datetime($post->created_on).' </small>
								</td>
                                <td nowrap>';
                                    if($post->sms_result_id){
                                        echo '<span class="m-badge m-badge--success m-badge--wide"> '.translate('Sent').'</span>';
                                    }else{
                                         echo '<span class="m-badge m-badge--warning m-badge--wide">'.translate('Failed').'</span>';
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
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                    '. translate('Ooops') .'! '. translate('Looks like you have no sent smses yet').'.                       
                </div>
            ';
        }

    }

    function create(){
        $data = array();
        $response = array();
        $posts = $_POST;
         
        if($this->input->post('send_to')==2){
            $this->validation_rules[] = array(
                'field' =>  'member_id[]',
                'label' =>  'Member Name',
                'rules' =>  'required'
            );
        }
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            if($this->input->post('send_to')==1){
                //all members of the group
                foreach($this->members_m->get_group_member_options() as $key => $value) {
                    $member[] = $this->members_m->get_group_member($key);
                }
            }else{
                $member_id = $this->input->post('member_id');
                $member = array();
                foreach($member_id as $key => $value) {
                    $member[] = $this->members_m->get_group_member($value);
                }
            }
            $message = $this->input->post('message');
            $message_id = $this->messaging->create_and_queue_sms($member,$message,$this->user,$this->group->id,$this->application_settings->application_name);
            if($message_id){
                $response = array(
                    'status' => 1,
                    'id' => $message_id,
                    'message' => 'Sms Composed successfully ',
                    'refer'=>site_url('group/sms?a=queued')
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Failed to composed sms ',
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

    function compose(){
        $textarea = array(
            'name'=>'message',
            'id'=>'',
            'value'=> $this->input->post('message')?:'',
            'cols'=>40,
            'rows'=>8,
            'maxlength'=>160,
            'class'=>'form-control maxlength',
            'placeholder'=>'Compose SMS to send'
        );
        $html = form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="add_compose_message"');
        $html.=
            '<div>
                <div class="form-group m-form__group row pt-0 m--padding-10 ">
                    <div class="col-sm-12 m-form__group-sub m-input--air">
                        <label>'.translate('Send Message To ').'<span class="required">*</span></label>
                        '.form_dropdown('send_to',translate($this->send_to_list),$this->input->post('send_to')?:'','class="form-control send_to m-select2" placeholder="Send To"').'
                    </div>
                </div>

                <div class="form-group m-form__group row pt-0 m--padding-10 member_input">
                    <div class="col-sm-12 m-form__group-sub m-input--air">
                        <label>'.translate('Select Member').'<span class="required">*</span></label>
                        '.form_dropdown('member_id[]',$this->members,$this->input->post('member_id')?:'','class=" form-control m-select2" multiple="multiple" data-placeholder="Select..."').'
                    </div>
                </div>

                <div class="form-group m-form__group row pt-0 m--padding-10 ">
                    <div class="col-sm-12 m-form__group-sub m-input--air">
                        <label>'.translate('Message').'<span class="required">*</span></label>
                        '.form_textarea($textarea).'
                    </div>
                </div>
                <div class="form-group m-form__group row pt-0 m--padding-10">
                    <div class="col-lg-12 col-md-12">
                        <span class="float-lg-right float-md-left float-sm-left float-xl-right">
                            <button class="btn btn-primary m-btn m-btn--custom m-btn--icon btn-sm" id="compose_sms_button" type="button">
                                '.translate('Save Changes').'
                            </button>
                            &nbsp;&nbsp;
                            <button class="btn btn-metal m-btn m-btn--custom m-btn--icon btn-sm cancel_form" type="button" id="cancel_compose_sms_button">
                                '.translate('Cancel').'
                            </button>
                        </span>
                    </div>
                </div>
            </div>';
        $html.=form_close();
        echo $html;
    }


}

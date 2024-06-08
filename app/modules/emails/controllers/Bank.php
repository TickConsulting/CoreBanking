<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bank extends Bank_Controller
{

    protected $message_template;
    protected $members;
    protected $data = array();

    protected $validation_rules = array(
            array(
                'field' => 'member_id_to[]',
                'label' => 'Member Name',
                'rules' => 'required|trim',
            ),
            array(
                'field' => 'subject',
                'label' => 'Subject',
                'rules' => 'required|trim',
            ),
            array(
                'field' => 'message',
                'label' => 'Message',
                'rules' => 'required|trim',
            ),

        );


	function __construct()
    {
        parent::__construct();

        $this->load->model('emails/emails_m');
        $this->load->model('members/members_m');
        $this->load->library('messaging');

        if($this->group->disable_member_directory){
            $this->members = array('info@chamasoft.com' => $this->application_settings->application_name.' '.translate('Support Team'));
            if(valid_email($this->group->email)){
                $this->members += array($this->group->email => 'Group Email');
            }
        }else{
            $this->members = array('all'=>'All Applicants')+$this->members_m->get_group_member_options_for_emailing()+array('customer@tickconsulting.co.ke'=>$this->application_settings->application_name.' Team');

        }
        $this->message_template = '<br/>Dear [NAME],<br/><br/><br/><br/>Regards,<br/>'.$this->user->first_name.' '.$this->user->last_name.'.';

        $this->data['message_template'] = $this->message_template;
        $this->data['members'] = $this->members;
        
    }

    function index(){
        //print_r($posts = $this->emails_m->inbox_group_emails());die;
        $this->template->title(translate('Emails'))->build('shared/form',$this->data);
    }

    function search()
    {
        $params = $this->input->get('params');
        echo $params;
        
    }

    function create(){
        $post = new StdClass();

        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $cc = array();
            $bcc = array();
            $attachment = array();
            $member = array();

            $member_id_to = $this->input->post('member_id_to');
            $member_id_cc = $this->input->post('member_id_cc');
            $member_id_bcc = $this->input->post('member_id_bcc');
            if(in_array('all',$member_id_to)){
                foreach($this->members as $key => $value) 
                {
                    $result = $this->members_m->get_group_member($key);
                    if($result){
                        $member[] = $result;
                    }
                }  
            }else{
                foreach($member_id_to as $key => $value) 
                {
                    if(preg_match('/customer\@tickconsulting\.co\.ke/', $value)){
                        $member[] = 'chamasoft-team';
                    }elseif($value == $this->group->email){
                        $member[] = 'group-email';
                    }else{
                        $member[] = $this->members_m->get_group_member($value);
                    }
                }

                if(is_array($member_id_cc)){
                    foreach ($member_id_cc as $key => $value) {
                        if(!in_array($value, $member)){
                            if(preg_match('/customer\@tickconsulting\.co\.ke/', $value)){
                                $cc[] = 'Credit-Risk-Tick-team';
                            }elseif($value == $this->group->email){
                                $cc[] = 'tick-email';
                            }else{
                                $cc[] = $this->members_m->get_group_member($value);
                            }
                        }
                    }
                }
                if(is_array($member_id_bcc)){
                    foreach($member_id_bcc as $key => $value) {
                        if(!in_array($value, $member) && !in_array($value, $cc))
                        {
                            if(preg_match('/customer\@tickconsulting\.co\.ke/', $value)){
                                $bcc[] = 'Credit-Risk-Tick-team';
                            }elseif($value == $this->group->email){
                                $bcc[] = 'tick-email';
                            }else{
                                $bcc[] = $this->members_m->get_group_member($value);
                            }
                        }
                    }
                }
            }

            $message = $this->input->post('message');
            $subject = $this->input->post('subject');
            $attachment = $this->input->post('file_names');
            $send = $this->input->post('send');
            $attach = array();
            if($attachment){
                foreach ($attachment as $value) {
                    $attach[] = 'uploads/emails/'.$value;
                }
            }

            $email_id = $this->messaging->create_and_queue_email($member,$message,$this->user,$this->group->id,$subject,$attach,$cc,$bcc,'',$send);
        }else{
           foreach($this->validation_rules as $key => $field) {
                $post->$field['field'] = set_value($field['field']);
            } 
        }

        redirect('bank/emails/');
        
    }


    function outbox_emails(){
        $posts = $this->emails_m->get_all_queued_emails();
        $html='<table class="table table-striped table-advance table-hover">';
            if($posts):
                $html.='<thead>
                        <tr>
                            <th colspan="3">
                                <label class="m-checkbox m-checkbox--bold">
                                    <input type="checkbox" class="mail-checkbox mail-group-checkbox">
                                    <span></span>
                                </label>
                                <div class="btn-group input-actions" style="margin-bottom:-4px;">
                                    <a class="btn btn-sm btn-outline-success dropdown-toggle sbold" href="javascript:;" data-toggle="dropdown"> Actions</a>
                                    <ul class="dropdown-menu" style="min-width:160px;">
                                        <li>
                                            <a href="javascript:;" class="set_draft">
                                                <i class="fa fa-save"></i>Set as a Draft</a>
                                        </li>
                                        <li class="divider"> </li>
                                        <li>
                                            <a href="javascript:;" class="delete_outbox">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </th>
                            <th class="pagination-control" colspan="3">
                                <!--<span class="pagination-info"> 1-30 of 789 </span>
                                <a class="btn btn-sm blue btn-outline">
                                    <i class="fa fa-angle-left"></i>
                                </a>
                                <a class="btn btn-sm blue btn-outline">
                                    <i class="fa fa-angle-right"></i>
                                </a>-->
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach ($posts as $post) 
                    {
                        $member_email_to = $this->members_m->get_group_member_by_email($post->email_to);
                        $html.='<tr data-messageid="'.$post->id.'" style="cursor:pointer;">
                                    <td class="inbox-small-cells">
                                        <label class="m-checkbox m-checkbox--bold">
                                            <input type="checkbox" name="list-mails[]" value="'.$post->id.'" class="mail-checkbox">
                                            <span style="margin-top:-4px;"></span>
                                        </label>
                                    </td>
                                    <td class="inbox-small-cells">';
                                        if($this->user->email == $post->email_to){
                                            $html.='<i class="fa fa-star text-warning inbox-started"></i>';
                                        }else{
                                            $html.='<i class="fa fa-star" style="color:#ccc;"></i>';
                                        }
                                    $html.='</td>';
                                    if($member_email_to){
                                        $html.='<td class="view-queued-message hidden-xs">'.$member_email_to->first_name.' '.$member_email_to->last_name.'</td>';
                                    }else{
                                        $html.='<td class="view-queued-message hidden-xs">'.$this->application_settings->application_name.'</td>';
                                    }
                                    
                                    $html.='<td class="view-queued-message ">'.$post->subject.'</td>
                                    <td class="view-queued-message inbox-small-cells">';
                                        if(unserialize($post->attachments)){
                                            $html.='<i class="fa fa-paperclip"></i>';
                                        }
                            $html.='</td>
                                    <td class="view-queued-message text-right">'.timestamp_to_message_time($post->created_on).'</td>
                                </tr>';
                    }
                    else:
                            $html.='<tr><td colspan="6" style="background:#fff!important;border: none;"><div class="alert m-alert--outline alert-metal">
                                        <h4 class="block">Information! No records to display</h4>
                                        <p>
                                            There are no emails in outbox.
                                        </p>
                                    </div></td></tr>';

                        endif;

                    $html.='</tbody>
                </table>';

            echo $html;
    }

    function draft_emails(){
        $posts = $this->emails_m->get_all_draft_emails();
        $html='
            <table class="table table-striped table-advance table-hover">';
                if($posts):
                    $html.='<thead>
                            <tr>
                                <th colspan="3">
                                    <label class="m-checkbox m-checkbox--bold">
                                        <input type="checkbox" class="mail-checkbox mail-group-checkbox">
                                        <span></span>
                                    </label>
                                    <div class="btn-group input-actions" style="margin-bottom:-4px;">
                                        <a class="btn btn-sm btn-outline-success dropdown-toggle sbold" href="javascript:;" data-toggle="dropdown"> Actions</a>
                                        <ul class="dropdown-menu" style="min-width:160px;">
                                            <li>
                                                <a href="javascript:;" class="send_draft">
                                                    <i class="fa fa-check"></i>Send Draft</a>
                                            </li>
                                            <li class="divider"> </li>
                                            <li>
                                                <a href="javascript:;" class="delete_draft">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th class="pagination-control" colspan="3">
                                    <!--<span class="pagination-info"> 1-30 of 789 </span>
                                    <a class="btn btn-sm blue btn-outline">
                                        <i class="fa fa-angle-left"></i>
                                    </a>
                                    <a class="btn btn-sm blue btn-outline">
                                        <i class="fa fa-angle-right"></i>
                                    </a>-->
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                    foreach ($posts as $post) 
                    {
                        $member_email_to = $this->members_m->get_group_member_by_email($post->email_to);
                        $html.='<tr data-messageid="'.$post->id.'" style="cursor:pointer;">
                                    <td class="inbox-small-cells">
                                        <label class="m-checkbox m-checkbox--bold">
                                            <input type="checkbox" name="list-mails[]" value="'.$post->id.'" class="mail-checkbox">
                                            <span style="margin-top:-4px;"></span>
                                        </label>
                                    </td>
                                    <td class="inbox-small-cells">';
                                    if($this->user->email == $post->email_to){
                                        $html.='<i class="fa fa-star text-warning inbox-started"></i>';
                                    }else{
                                        $html.='<i class="fa fa-star" style="color:#ccc;"></i>';
                                    }
                                    $html.='</td>';
                                    if($member_email_to){
                                        $html.='<td class="view-draft-message hidden-xs">'.$member_email_to->first_name.' '.$member_email_to->last_name.'</td>';
                                    }
                                    else{
                                        $html.='<td class="view-draft-message hidden-xs">'.$this->application_settings->application_name.'</td>';
                                    }
                                    $html.='<td class="view-draft-message ">'.$post->subject.'</td>
                                    <td class="view-draft-message inbox-small-cells">';
                                        if(unserialize($post->attachments)){
                                            $html.='<i class="fa fa-paperclip"></i>';
                                        }
                            $html.='
                                    </td>
                                    <td class="view-draft-message text-right">'.timestamp_to_message_time($post->created_on).'</td>
                                </tr>';
                    }
                else:
                    $html.='
                        <tr>
                            <td colspan="6" style="background:#fff!important;border: none;">
                                <div class="alert m-alert--outline alert-metal">
                                    <h4 class="block">Information! No records to display</h4>
                                    <p>
                                        There are no draft emails.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    ';
                endif;
                $html.='</tbody>
            </table>';
        echo $html; 
    }

    function sent_emails(){
        $posts = $this->emails_m->get_all_sent_emails();
        $html='<table class="table table-striped table-advance table-hover">';
                if($posts):
                $html.='<thead>
                        <tr>
                            <th colspan="3">
                                <label class="m-checkbox m-checkbox--bold">
                                    <input type="checkbox" class="mail-checkbox mail-group-checkbox">
                                    <span></span>
                                </label>
                            </th>
                            <th class="pagination-control" colspan="3">
                                <!--<span class="pagination-info"> 1-30 of 789 </span>
                                <a class="btn btn-sm blue btn-outline">
                                    <i class="fa fa-angle-left"></i>
                                </a>
                                <a class="btn btn-sm blue btn-outline">
                                    <i class="fa fa-angle-right"></i>
                                </a>-->
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                    foreach ($posts as $post) 
                    {
                        $member_email_to = $this->members_m->get_group_member_by_email($post->email_to);
                        $html.='<tr data-messageid="'.$post->id.'" style="cursor:pointer;">
                                    <td class="inbox-small-cells">
                                        <label class="m-checkbox m-checkbox--bold">
                                            <input type="checkbox" name="list-mails[]" value="'.$post->id.'" class="mail-checkbox">
                                            <span style="margin-top:-4px;"></span>
                                        </label>
                                    </td>
                                    <td class="inbox-small-cells">';
                                        if($this->user->email == $post->email_to){
                                            $html.='<i class="fa fa-star text-warning inbox-started"></i>';
                                        }else{
                                            $html.='<i class="fa fa-star" style="color:#ccc;"></i>';
                                        }
                                    $html.='</td>';
                                    if($member_email_to){
                                        $html.='<td class="view-sent-message hidden-xs">'.$member_email_to->first_name.' '.$member_email_to->last_name.'</td>';
                                    }else{
                                        $html.='<td class="view-sent-message hidden-xs">'.$this->application_settings->application_name.'</td>';
                                    }
                                    
                                    $html.='<td class="view-sent-message ">'.$post->subject.'</td>
                                    <td class="view-sent-message inbox-small-cells">';
                                        if(unserialize($post->attachments)){
                                            $html.='<i class="fa fa-paperclip"></i>';
                                        }
                            $html.='</td>
                                    <td class="view-sent-message text-right">'.timestamp_to_message_time($post->created_on).'</td>
                                </tr>';
                    }
                    else:
                            $html.='<tr><td colspan="6" style="background:#fff!important;border: none;"><div class="alert m-alert--outline alert-metal">
                                        <h4 class="block">Information! No records to display</h4>
                                        <p>
                                            There are no sent emails.
                                        </p>
                                    </div></td></tr>';

                        endif;

                    $html.='</tbody>
                </table>';

            echo $html; 
    }

    function app_app_inbox(){
        $posts = $this->emails_m->inbox_group_emails();
        $html= '<table class="table table-striped table-advance table-hover">';
            if($posts):
            $html.= '<thead>
                        <tr>';
                            $html.=form_open(site_url('bank/emails/action'));
                            $html.='<th colspan="3">
                                <label class="m-checkbox m-checkbox--bold">
                                    <input type="checkbox" class="mail-checkbox mail-group-checkbox">
                                    <span></span>
                                </label>';
                                $html.='
                                <div class="btn-group input-actions">
                                    <a class="btn btn-sm blue btn-outline dropdown-toggle sbold" href="javascript:;" data-toggle="dropdown"> Actions</a>
                                    <ul class="dropdown-menu" style="min-width:160px;">
                                        <li>
                                            <a href="javascript:;" class="mark-as-read">
                                                <i class="fa fa-eye"></i> Mark as Read
                                            </a>
                                        </li>
                                        <li class="divider"> </li>
                                        <li>
                                            <a href="javascript:;" class="mark-as-unread">
                                                <i class="fa fa-eye-slash"></i> Mark as Unread
                                            </a>
                                        </li>
                                    </ul>
                                </div>';
                            $html.='</th>
                            <th class="pagination-control" colspan="3">
                                <!--<span class="pagination-info"> 1-30 of 789 </span>
                                <a class="btn btn-sm blue btn-outline">
                                    <i class="fa fa-angle-left"></i>
                                </a>
                                <a class="btn btn-sm blue btn-outline">
                                    <i class="fa fa-angle-right"></i>
                                </a>-->
                            </th>
                        </tr>';
                    $html.=form_close();
                    $html.='</thead>
                    <tbody>';
                            foreach ($posts as $post) 
                            {
                                $member_email_from = $this->members_m->get_group_member_by_email($post->email_from);
                               if($post->is_read){
                                    $html.='<tr data-messageid="'.$post->id.'">';
                               }
                               else{
                                    $html.='<tr class="unread" data-messageid="'.$post->id.'">';
                               }

                                $html.='<td class="inbox-small-cells">
                                            <label class="m-checkbox m-checkbox--bold">
                                                <input type="checkbox" name="list-mails[]" value="'.$post->id.'" class="mail-checkbox">
                                                <span></span>
                                            </label>
                                        </td>
                                        <td class="inbox-small-cells">';
                                            if($this->user->email == $post->email_to){
                                                $html.='<i class="fa fa-star text-warning inbox-started"></i>';
                                            }else{
                                                $html.='<i class="fa fa-star" style="color:#ccc;"></i>';
                                            }
                                        $html.='</td>';
                                        if($member_email_from){
                                            $html.='<td class="view-message hidden-xs">'.$member_email_from->first_name.' '.$member_email_from->last_name.'</td>';
                                        }else{
                                            $html.='<td class="view-message hidden-xs">'.$this->application_settings->application_name.'</td>';
                                        }
                                        
                                        $html.='<td class="view-message ">'.$post->subject.'</td>
                                        <td class="view-message inbox-small-cells">';

                                        if(unserialize($post->attachments)){
                                            $html.='<i class="fa fa-paperclip"></i>';
                                        }
                                    $html.='</td>
                                        <td class="view-message text-right"> '.timestamp_to_message_time($post->created_on).' </td>';


                               $html.='</tr>';
                            }

                        else:
                            $html.='<tr><td colspan="6" style="background:#fff!important;border:none;"><div class="alert m-alert--outline alert-metal">
                                        <h4 class="block">' . translate('Information! No records to display') .'</h4>
                                        <p>
                                            '. translate('There are no emails received') .'.
                                        </p>
                                    </div></td></tr>';

                        endif;
                    $html.='</tbody>
                </table>';

            echo $html;
    }

    function view($id=0){
        $this->template->title('Emails')->set_layout('default_full_width.html')->build('shared/form',$this->data);
    }


    function compose(){
        $html= form_open_multipart(site_url('bank/emails/create'),'class="inbox-compose form-horizontal form_submit" id="fileupload"');
        $html.='<div class="inbox-compose-btn mb-2">
                    <button style="" type="submit" name="send" value="send" class="btn btn-sm btn-primary submit_form_button mr-2">
                        <i class="fa fa-check" style="margin-top:-4px;"></i> Send
                    </button>
                    <button type="button" class="btn btn-md btn-primary btn-sm processing_form_button disabled mr-2" name="processing" value="Processing">
                        <i class="fa fa-spinner fa-spin" style="margin-top:-4px;"></i> Processing
                    </button> 
                    <button type="button" class="btn btn-sm btn-danger discard mr-2">
                        <i class="fa fa-trash-alt" style="margin-top:-4px;"></i> Discard
                    </button>
                    <button type="submit" class="btn btn-sm btn-success draft" name="draft">
                        <i class="fa fa-save" style="margin-top:-4px;"></i> Save Draft
                    </button>
                </div>
                <div class="inbox-form-group mt-4 mail-to">
                    <label class="control-label">To:</label>
                    <div class="controls controls-to">';
                    $html.=form_dropdown('member_id_to[]',$this->members,$this->input->post('member_id_to'),'class="form-control member_id_to m-select2" multiple');
                    $html.='
                        <!-- <span class="inbox-cc-bcc">
                            <span class="inbox-cc"> Cc </span>
                            <span class="inbox-bcc"> Bcc </span>
                        </span> -->
                    </div>
                </div>
                <div class="inbox-form-group mt-4 input-cc display-hide">
                    <a href="javascript:;" class="close"> </a>
                    <label class="control-label">Cc:</label>
                    <div class="controls controls-cc">';
                $html.=form_dropdown('member_id_cc[]',$this->members,$this->input->post('member_id_cc'),'class="form-control m-select2" multiple');
                    $html.='</div>
                </div>
                <div class="inbox-form-group mt-4 input-bcc display-hide">
                    <a href="javascript:;" class="close"> </a>
                    <label class="control-label">Bcc:</label>
                    <div class="controls controls-bcc">';
                $html.=form_dropdown('member_id_bcc[]',$this->members,$this->input->post('member_id_bcc'),'class="form-control m-select2" multiple'); 
                    $html.='</div>
                </div>
                <div class="inbox-form-group mt-4">
                    <label class="control-label">Subject:</label>
                    <div class="controls" style="margin-top:-14px;">';
                $html.='<div class="input-icon right">
                            <i class="fa fa-warning subject-warning" data-original-title="This field is required."></i>';
                $html.=form_input('subject',$this->input->post('subject'),' class="form-control subject" aria-required="true" aria-describedby="name-error" aria-invalid="true"');
                $html.='</div>';

                $html.='</div>
                </div>
                <div class="inbox-form-group mt-4">';
                $textarea = array(
                        'name'=>'message',
                        'id'=>'',
                        'value'=> $this->input->post('message')?:$this->message_template?:'',
                        'cols'=>40,
                        'rows'=>10,
                        'class'=>'inbox-editor html_editor form-control autosizeme mt-4',
                        'placeholder'=>'Compose Email to send'
                    ); 
                $html.=form_textarea($textarea); 

                $html.='</div>

                <div class="inbox-compose-attachment">
                    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                    <span class="btn green btn-outline fileinput-button">
                        <i class="fa fa-plus"></i>
                        <span> Add files... </span>
                        <input type="file" name="files[]" multiple> </span>
                    <!-- The table listing the files available for upload/download -->
                    <table role="presentation" class="table table-striped margin-top-10">
                        <tbody class="files"> </tbody>
                    </table>
                </div>

                <script id="template-upload" type="text/x-tmpl"> {% for (var i=0, file; file=o.files[i]; i++) { %}
                    <tr class="template-upload fade">
                        <td class="name" width="30%">
                            <span>{%=file.name%}</span>
                        </td>
                        <td class="size" width="40%">
                            <span>{%=o.formatFileSize(file.size)%}</span>
                        </td> {% if (file.error) { %}
                        <td class="error" width="20%" colspan="2">
                            <span class="label label-danger">Error</span> {%=file.error%}</td> {% } else if (o.files.valid && !i) { %}
                        <td>
                            <p class="size">{%=o.formatFileSize(file.size)%}</p>
                            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                            </div>
                        </td> {% } else { %}
                        <td colspan="2"></td> {% } %}
                        <td class="cancel" width="10%" align="right">{% if (!i) { %}
                            <button class="btn btn-sm red cancel">
                                <i class="fa fa-ban"></i>
                                <span>Cancel</span>
                            </button> {% } %}</td>
                    </tr> {% } %} </script>
                <!-- The template to display files available for download -->
                <script id="template-download" type="text/x-tmpl"> {% for (var i=0, file; file=o.files[i]; i++) { %}
                    <tr class="template-download fade"> {% if (file.error) { %}
                        <td class="name" width="40%">
                            <span>{%=file.name%}</span>
                        </td>
                        <td class="size" width="40%">
                            <span>{%=o.formatFileSize(file.size)%}</span>
                        </td>
                        <td class="error" width="30%" colspan="2">
                            <span class="label label-danger">Error</span> {%=file.error%}</td> {% } else { %}
                        <td class="name" width="30%">
                            <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&\'gallery\'%}" download="{%=file.name%}">{%=file.name%}</a>
                            <input type="hidden" name="file_names[]" value="{%=file.name%}"/>
                        </td>
                        <td class="size" width="40%">
                            <span>{%=o.formatFileSize(file.size)%}</span>
                        </td>
                        <td colspan="2"></td> {% } %}
                        <td class="delete" width="10%" align="right">
                            <button class="btn default btn-sm" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}" {% if (file.delete_with_credentials) { %} data-xhr-fields=\'{"withCredentials":true}\' {% } %}>
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr> {% } %} </script>
                
                <div class="inbox-compose-btn mb-2">
                    <button style="" type="submit" name="send" value="send" class="btn btn-sm btn-primary submit_form_button mr-2">
                        <i class="fa fa-check" style="margin-top:-4px;"></i> Send
                    </button>
                    <button type="button" class="btn btn-md btn-primary btn-sm processing_form_button disabled mr-2" name="processing" value="Processing">
                        <i class="fa fa-spinner fa-spin" style="margin-top:-4px;"></i> Processing
                    </button> 
                    <button type="button" class="btn btn-sm btn-danger discard mr-2">
                        <i class="fa fa-trash-alt" style="margin-top:-4px;"></i> Discard
                    </button>
                    <button type="submit" class="btn btn-sm btn-success draft" name="draft">
                        <i class="fa fa-save" style="margin-top:-4px;"></i> Save Draft
                    </button>
                </div>
            </form>';

            echo $html;
    }


    function app_inbox_view(){
        $id = $this->input->get('message_id');
        $post = $this->emails_m->get_mail($id);
        if($post){
            if($post->email_to == $this->user->email && !$post->is_read)
            {
                //update email set as read
                $this->emails_m->update($post->id,array('is_read'=>1));
            }
            $member_email_from = $this->members_m->get_group_member_by_email($post->email_from);

            $html='<div class="inbox-header inbox-view-header">
                <h1 class="pull-left">'.$post->subject.'
                    <a href="javascript:;"> Inbox </a>
                </h1>
                <div class="pull-right">
                    <a href="javascript:;" class="btn btn-icon-only dark btn-outline">
                        <i class="fa fa-print"></i>
                    </a>
                </div>
            </div>
            <div class="inbox-view-info">
                <div class="row">
                    <div class="col-md-10">';
                    if($member_email_from){
                        if($member_email_from->avatar && file_exists('uploads/groups/'.$member_email_from->avatar)){
                            $html.='<img src="'.base_url().'uploads/groups/'.$member_email_from->avatar.'" class="inbox-author">';
                        }else{
                            $html.='<img alt="" class="inbox-author" src="'.base_url().'/templates/admin_themes/groups/img/avatar.png">';
                        }
                        $html.='<span class="sbold">'.$member_email_from->first_name.' '.$member_email_from->last_name.'</span>';                    }
                    else{
                        $html.='<img alt="" class="inbox-author" src="'.base_url().'/uploads/logos/'.$this->application_settings->responsive_logo.'">';
                        $html.='<span class="sbold">'.$this->application_settings->application_name.'</span>';
                    }

                    $html.='<span>&#60;'.$post->email_from.'&#62; </span> to
                        <span class="sbold"> me </span> on '.timestamp_to_message_time($post->created_on).'<br/>';

                    if($post->cc){
                        $html.='<span class="sbold cc-bcc"> CC.  </span>'.$post->cc.'<br/>';
                    }

                    $html.='</div>
                    <div class="col-md-2 inbox-info-btn">
                        <div class="btn-group">
                            <button data-messageid="'.$post->id.'" class="btn green reply-btn">
                                <i class="fa fa-reply"></i> Reply
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="javascript:;" data-messageid="'.$post->id.'" class="reply-btn">
                                        <i class="fa fa-reply"></i> Reply </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <i class="fa fa-trash-o"></i> Delete </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="inbox-view"><div class="table-responsive">
                '.$post->message.'
           </div> </div>';
                $attachments = unserialize($post->attachments);
                if($attachments){
                    $html.='<hr>
                            <div class="inbox-attached">
                                <div class="margin-bottom-15">
                                    <!--<span>attachments — </span>-->
                                    <a href="javascript:;">Download all attachments </a>
                                </div>';
                    foreach ($attachments as $value) 
                    {
                        $name = explode('/', $value);
                        $html.='<div class="margin-bottom-25">';
                        $file_type = explode('.', $name[2]);
                        if(strtolower($file_type[1])=='jpg' || strtolower($file_type[1])=='png'){
                           $html.='<img src="'.base_url().$name[0].'/'.$name[1].'/thumbnail/'.$name[2].'">'; 
                        }else{
                            $html.='<img src="'.base_url().'uploads/file_images/'.$file_type[1].'.jpg">';
                        }
                        
                        $html.='<div>
                                    <strong>'.$name[2].'</strong>
                                    <a href="'.$this->application_settings->protocol.$this->application_settings->url.'/'.$value.'" download="'.$name[2].'" >Download </a>
                                </div>';
                        $html.'</div>';
                    }
                }
            $html.='</div>';

                    echo $html;

        }
        else{
            echo '<div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    Sorry, the mail was not found.
                </p>
            </div>';
        }
    }

    function app_inbox_view_queued(){
        $id = $this->input->get('message_id');

        $post = $this->emails_m->get_queued_mail($id);

        if($post){
            $member_email_to = $this->members_m->get_group_member_by_email($post->email_to);

            $html='<div class="inbox-header inbox-view-header">
                <h1 class="pull-left">'.$post->subject.'
                    <a href="javascript:;"> Outbox </a>
                </h1>
                <div class="pull-right">
                    <a href="javascript:;" class="btn btn-icon-only dark btn-outline">
                        <i class="fa fa-print"></i>
                    </a>
                </div>
            </div>
            <div class="inbox-view-info">
                <div class="row">
                    <div class="col-md-10">';
                    if($member_email_to){
                         if($member_email_to->avatar && file_exists('uploads/groups/'.$member_email_to->avatar)){
                                $html.='<img src="'.base_url().'uploads/groups/'.$member_email_to->avatar.'" class="inbox-author">';
                            }else{
                                $html.='<img alt="" class="inbox-author" src="'.base_url().'/templates/admin_themes/groups/img/avatar.png">';
                            }
                        $html.='<span class="sbold">'.$member_email_to->first_name.' '.$member_email_to->last_name.'</span>';
                    }else{
                        $html.='<img alt="" class="inbox-author" src="'.base_url().'/uploads/logos/'.$this->application_settings->responsive_logo.'">';
                        $html.='<span class="sbold"> '.$this->application_settings->application_name.' Support</span>';
                    }
                       
                    $html.='<span>&#60;'.$post->email_to.'&#62; </span> to
                        <span class="sbold"> me </span> on '.timestamp_to_message_time($post->created_on).'<br/>';

                        if($post->cc){
                            $html.='<span class="sbold cc-bcc"> CC.  </span>'.$post->cc.'<br/>';
                        }
                        if($post->bcc){
                            $html.='<span class="sbold cc-bcc"> BCC. </span>'.$post->bcc.'<br/>';
                        }

                    $html.='</div>
                    <div class="col-md-2 inbox-info-btn">
                        
                    </div>
                </div>
            </div>
            <div class="inbox-view"><div class="table-responsive">
                '.$post->message.'
            </div></div>';
                $attachments = unserialize($post->attachments);
                if($attachments){
                    $html.='<hr>
                            <div class="inbox-attached">
                                <div class="margin-bottom-15">
                                    <!--<span>attachments — </span>-->
                                    <a href="javascript:;">Download all attachments </a>
                                </div>';
                    foreach ($attachments as $value) 
                    {
                        $name = explode('/', $value);
                        $html.='<div class="margin-bottom-25">';
                        $file_type = explode('.', $name[2]);
                        if(strtolower($file_type[1])=='jpg' || strtolower($file_type[1])=='png'){
                           $html.='<img src="'.base_url().$name[0].'/'.$name[1].'/thumbnail/'.$name[2].'">'; 
                        }else{
                            $html.='<img src="'.base_url().'uploads/file_images/'.$file_type[1].'.jpg">';
                        }
                        
                        $html.='<div>
                                    <strong>'.$name[2].'</strong>
                                    <a href="'.$this->application_settings->protocol.$this->application_settings->url.'/'.$value.'" download="'.$name[2].'" >Download </a>
                                </div>';
                        $html.'</div>';
                    }
                }
            $html.='</div>';

                    echo $html;

        }
        else{
            echo true;
        }
    }

    function app_inbox_view_draft(){
        $id = $this->input->get('message_id');
        $post = $this->emails_m->get_draft_mail($id);
        if($post){
            $member_email_to = $this->members_m->get_group_member_by_email($post->email_to);
            $html='<div class="inbox-header inbox-view-header row col-md-12">
                <h1 class="pull-left">'.$post->subject.'
                    <span class="m-badge m-badge--metal m-badge--wide m-badge--rounded">
                        Draft
                    </span>
                </h1>
                <!-- <div class="pull-right">
                    <a href="javascript:;" class="btn btn-icon-only dark btn-outline">
                        <i class="fa fa-print"></i>
                    </a>
                </div> -->
            </div>
            <div class="inbox-view-info row">
                    <div class="col-md-10">';
                        if($member_email_to){
                            $html.=' <img src="https://ui-avatars.com/api/?name='.$member_email_to->first_name.' '.$member_email_to->last_name.'&background=00abf2&color=fff&size=30&" class="m--img-rounded m--marginless m--img-centered" alt=""/>';
                            $html.='<span class="sbold">'.$member_email_to->first_name.' '.$member_email_to->last_name.'</span>';
                        }else{
                            $html.=' <img src="https://ui-avatars.com/api/?name='.$this->application_settings->application_name.'&background=00abf2&color=fff&size=30&" class="m--img-rounded m--marginless m--img-centered" alt=""/>';
                            $html.='<span class="sbold">'.$this->application_settings->application_name.'</span>';
                        }

                        $html.='<span>&#60;'.$post->email_from.'&#62; </span> from
                            <span class="sbold"> me </span> on '.timestamp_to_message_time($post->created_on).'<br/>';
                            if($post->cc){
                                $html.='<span class="sbold cc-bcc"> CC:  </span>'.$post->cc.'<br/>';
                            }
                            if($post->bcc){
                                $html.='<span class="sbold cc-bcc"> BCC: </span>'.$post->bcc.'<br/>';
                            }
                        $html.='</div>';
                        $html.='<div class="col-md-2 inbox-info-btn">
                    </div>
            </div>
            <div class="inbox-view"><div class="table-responsive">
                '.$post->message.'
            </div></div>';
                $attachments = unserialize($post->attachments);
                if($attachments){
                    $html.='<hr>
                            <div class="inbox-attached">
                                <div class="margin-bottom-15">
                                    <!--<span>attachments — </span>-->
                                    <a href="javascript:;">Download all attachments </a>
                                </div>';
                    foreach ($attachments as $value) 
                    {
                        $name = explode('/', $value);
                        $html.='<div class="margin-bottom-25">';
                        $file_type = explode('.', $name[2]);
                        if(strtolower($file_type[1])=='jpg' || strtolower($file_type[1])=='png'){
                           $html.='<img src="'.base_url().$name[0].'/'.$name[1].'/thumbnail/'.$name[2].'">'; 
                        }else{
                            $html.='<img src="'.base_url().'uploads/file_images/'.$file_type[1].'.jpg">';
                        }
                        
                        $html.='<div>
                                    <strong>'.$name[2].'</strong>
                                    <a href="'.$this->application_settings->protocol.$this->application_settings->url.'/'.$value.'" download="'.$name[2].'" >Download </a>
                                </div>';
                        $html.'</div>';
                    }
                }
            $html.='</div>';
            echo $html;
        }else{
            echo true;
        }
    }

    function app_inbox_view_sent(){

        $id = $this->input->get('message_id');

        $post = $this->emails_m->get_sent_mail($id);

        if($post){
            $member_email_to = $this->members_m->get_group_member_by_email($post->email_to);

            $html='<div class="inbox-header inbox-view-header">
                <h1 class="pull-left">'.$post->subject.'
                    <a href="javascript:;"> Sent </a>
                </h1>
                <div class="pull-right">
                    <a href="javascript:;" class="btn btn-icon-only dark btn-outline">
                        <i class="fa fa-print"></i>
                    </a>
                </div>
            </div>
            <div class="inbox-view-info">
                <div class="row">
                    <div class="col-md-10">';
                    if($member_email_to){
                        if($member_email_to->avatar && file_exists('uploads/groups/'.$member_email_to->avatar)){
                            $html.='<img src="'.base_url().'uploads/groups/'.$member_email_to->avatar.'" class="inbox-author">';
                        }else{
                            $html.='<img alt="" class="inbox-author" src="'.base_url().'/templates/admin_themes/groups/img/avatar.png">';
                        }
                        $html.='<span class="sbold">'.$member_email_to->first_name.' '.$member_email_to->last_name.'</span>';
                    }else{
                        $html.='<img alt="" class="inbox-author" src="'.base_url().'/uploads/logos/'.$this->application_settings->responsive_logo.'">';
                        $html.='<span class="sbold">'.$this->application_settings->application_name.'</span>';
                    }
                    $html.='<span>&#60;'.$post->email_to.'&#62; </span> from
                        <span class="sbold"> me </span> on '.timestamp_to_message_time($post->created_on).'<br/>';
                        if($post->cc){
                            $html.='<span class="sbold cc-bcc"> CC.  </span>'.$post->cc.'<br/>';
                        }
                        if($post->bcc){
                            $html.='<span class="sbold cc-bcc"> BCC. </span>'.$post->bcc.'<br/>';
                        }
                    $html.='</div><div class="col-md-2 inbox-info-btn">
                        
                    </div>
                </div>
            </div>
            <div class="inbox-view"><div class="table-responsive">
                '.$post->message.'
            </div></div>';
                $attachments = unserialize($post->attachments);
                if($attachments){
                    $html.='<hr>
                            <div class="inbox-attached">
                                <div class="margin-bottom-15">
                                    <!--<span>attachments — </span>-->
                                    <a href="javascript:;">Download all attachments </a>
                                </div>';
                    foreach ($attachments as $value) 
                    {
                        $name = explode('/', $value);
                        $html.='<div class="margin-bottom-25">';
                        $file_type = explode('.', $name[2]);
                        if(strtolower($file_type[1])=='jpg' || strtolower($file_type[1])=='png'){
                           $html.='<img src="'.base_url().$name[0].'/'.$name[1].'/thumbnail/'.$name[2].'">'; 
                        }else{
                            $html.='<img src="'.base_url().'uploads/file_images/'.$file_type[1].'.jpg">';
                        }
                        
                        $html.='<div>
                                    <strong>'.$name[2].'</strong>
                                    <a href="'.$this->application_settings->protocol.$this->application_settings->url.'/'.$value.'" download="'.$name[2].'" >Download </a>
                                </div>';
                        $html.'</div>';
                    }
                }
            $html.='</div>';

                    echo $html;

        }
        echo true;
    }

    function app_inbox_reply($id=0){
        $id = $this->input->get('message_id');
        $post = $this->emails_m->get($id);
        $member_email_from = $this->members_m->get_group_member_by_email($post->email_from);
        if($post){
            $html= form_open_multipart(site_url('bank/emails/create'),'class="inbox-compose form-horizontal form_submit" id="fileupload"');
            $html.='<div class="inbox-compose-btn">
                    <button type="submit" name="send" value="send" class="btn green submit_form_button">
                        <i class="fa fa-check"></i>Send</button>
                    <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing">
                        <i class="fa fa-spinner fa-spin"></i> Processing
                        </button> 
                    <button type="button" class="btn default discard">Discard</button>
                    <button type="submit" class="btn default draft" name="draft"><i class="fa fa-save"></i> Draft</button>
                </div>
                <div class="inbox-form-group mail-to">
                    <label class="control-label">To:</label>
                    <div class="controls controls-to">';
                    if($member_email_from){
                        $html.=form_dropdown('member_id_to[]',array('all'=>'All Applicants')+$this->members,$member_email_from->id,'class="form-control member_id_to select2-multiple" multiple id="multiple"');
                    }
                    else{
                        $html.=form_dropdown('member_id_to[]',array('customer@tickconsulting.co.ke'=>$this->application_settings->application_name.' Team')+$this->members,'info@chamasoft.com','class="form-control member_id_to select2-multiple" multiple id="multiple"');
                    }
                    
                    $html.='<span class="inbox-cc-bcc">
                            <span class="inbox-cc " style="display:none"> Cc </span>
                            <span class="inbox-bcc"> Bcc </span>
                        </span>
                    </div>
                </div>
                <div class="inbox-form-group input-cc">
                    <a href="javascript:;" class="close"> </a>
                    <label class="control-label">Cc:</label>
                    <div class="controls controls-cc">';
                    $html.=form_dropdown('member_id_cc[]',$this->members,'','class="form-control select2-multiple" multiple id="multiple"');
                $html.='</div>
                <div class="inbox-form-group input-bcc display-hide">
                    <a href="javascript:;" class="close"> </a>
                    <label class="control-label">Bcc:</label>
                    <div class="controls controls-bcc">
                        <input type="text" name="bcc" class="form-control"> </div>
                </div>
                <div class="inbox-form-group">
                    <label class="control-label">Subject:</label>
                    <div class="controls">';
                    $html.='<div class="input-icon right">
                            <i class="fa fa-warning subject-warning" data-original-title="This field is required."></i>';
                $html.=form_input('subject','Re: '.$post->subject,' class="form-control subject" aria-required="true" aria-describedby="name-error" aria-invalid="true"');
                $html.='</div></div>
                <div class="inbox-form-group">
                    <div class="controls-row">';
                        $textarea = array(
                                'name'=>'message',
                                'id'=>'',
                                'value'=> $this->message_template,
                                'cols'=>40,
                                'rows'=>10,
                                'class'=>'inbox-editor inbox-wysihtml5 form-control autosizeme',
                                'placeholder'=>'Compose Email to reply'
                            ); 
                        $html.=form_textarea($textarea);

                        $html.=' <div id="reply_email_content_body" class="">
                                    <br>
                                    <br>
                                    <blockquote>'.$post->message.'</blockquote>
                                </div>'; 
                       
                    $html.='</div>
                </div>
                <div class="inbox-compose-attachment">
                    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                    <span class="btn green btn-outline  fileinput-button">
                        <i class="fa fa-plus"></i>
                        <span> Add files... </span>
                        <input type="file" name="files[]" multiple> </span>
                    <!-- The table listing the files available for upload/download -->
                    <table role="presentation" class="table table-striped margin-top-10">
                        <tbody class="files"> </tbody>
                    </table>
                </div>
                <script id="template-upload" type="text/x-tmpl"> {% for (var i=0, file; file=o.files[i]; i++) { %}
                    <tr class="template-upload fade">
                        <td class="name" width="30%">
                            <span>{%=file.name%}</span>
                        </td>
                        <td class="size" width="40%">
                            <span>{%=o.formatFileSize(file.size)%}</span>
                        </td> {% if (file.error) { %}
                        <td class="error" width="20%" colspan="2">
                            <span class="label label-danger">Error</span> {%=file.error%}</td> {% } else if (o.files.valid && !i) { %}
                        <td>
                            <p class="size">{%=o.formatFileSize(file.size)%}</p>
                            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                            </div>
                        </td> {% } else { %}
                        <td colspan="2"></td> {% } %}
                        <td class="cancel" width="10%" align="right">{% if (!i) { %}
                            <button class="btn btn-sm red cancel">
                                <i class="fa fa-ban"></i>
                                <span>Cancel</span>
                            </button> {% } %}</td>
                    </tr> {% } %} </script>
                <!-- The template to display files available for download -->
                <script id="template-download" type="text/x-tmpl"> {% for (var i=0, file; file=o.files[i]; i++) { %}
                    <tr class="template-download fade"> {% if (file.error) { %}
                        <td class="name" width="30%">
                            <span>{%=file.name%}</span>
                        </td>
                        <td class="size" width="40%">
                            <span>{%=o.formatFileSize(file.size)%}</span>
                        </td>
                        <td class="error" width="30%" colspan="2">
                            <span class="label label-danger">Error</span> {%=file.error%}</td> {% } else { %}
                        <td class="name" width="30%">
                            <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&"gallery"%}" download="{%=file.name%}">{%=file.name%}</a>
                            <input type="hidden" name="file_names[]" value="{%=file.name%}"/>
                        </td>
                        <td class="size" width="40%">
                            <span>{%=o.formatFileSize(file.size)%}</span>
                        </td>
                        <td colspan="2"></td> {% } %}
                        <td class="delete" width="10%" align="right">
                            <button class="btn default btn-sm" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}" {% if (file.delete_with_credentials) { %} data-xhr-fields="{withCredentials:true}"" {% } %}>
                                <i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr> {% } %} </script>
                <div class="inbox-compose-btn">
                    <button type="submit" name="send" value="send" class="btn green submit_form_button">
                        <i class="fa fa-check"></i>Send</button>
                    <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing">
                        <i class="fa fa-spinner fa-spin"></i> Processing
                        </button> 
                    <button type="button" class="btn default discard">Discard</button>
                    <button type="submit" class="btn default draft" name="draft"><i class="fa fa-save"></i> Draft</button>
                </div>
            </form>';

            echo $html;
        }else{
            echo true;;
        }
    }


    function attachments(){
       $this->load->library('UploadHandler');
    }

    function count_mails(){
        $draft_emails = $this->emails_m->draft_group_emails_count();
        $outbox_emails = $this->emails_m->queued_group_emails_count();
        $inbox_emails = $this->emails_m->inbox_unread_group_emails_count();

        $counts = (object)array('outbox_emails'=>$outbox_emails,'inbox_emails'=>$inbox_emails,'draft_emails'=>$draft_emails);

        echo json_encode($counts);
    }

    function action(){
        $values = $this->input->post('ids');

        $action = $this->input->post('action');
        if($values):

        if($action=='bulk_mark_as_read'){
            foreach ($values as $value) {
                $this->mark_as_read($value);
            }
            $this->session->set_flashdata('success',count($values).' Email(s) successfully marked as read');
            echo TRUE;
        }else if($action=='bulk_mark_as_unread'){
           foreach ($values as $value) {
                $this->mark_as_unread($value);
            } 
            $this->session->set_flashdata('success',count($values).' Email(s) successfully marked as unread');
            echo TRUE;
        }else if($action=='bulk_save_as_draft'){
            foreach ($values as $value) {
                $this->save_as_draft($value);
            } 
            $this->session->set_flashdata('success',count($values).' Email(s) successfully saved to draft');
            echo TRUE;
        }else if($action == 'bulk_delete_outbox'){
            foreach ($values as $value) {
                $this->delete_queue($value);
            } 
            $this->session->set_flashdata('success',count($values).' Email(s) successfully deleted and will not be sent');
            echo TRUE;
        }else if($action=='bulk_send_draft'){
             foreach ($values as $value) {
                $this->send_draft($value);
            } 
            $this->session->set_flashdata('success',count($values).' Email(s) will be sent shortly');
            echo TRUE;
        }else if($action == 'bulk_delete_draft'){
             foreach ($values as $value) {
                $this->delete_draft($value);
            } 
            $this->session->set_flashdata('success',count($values).' Email(s) successfully deleted from draft');
            echo TRUE;
        }
        endif;
        echo FALSE;
    }

    function get_all_queed()
    {
        print_r($this->emails_m->get_email_queue());
    }

    function mark_as_read($id=0){
        if(!$id){
            return FALSE;
        }
        else{
            $this->emails_m->update($id,array('is_read'=>1));
            return TRUE;
        }
    }

    function mark_as_unread($id=0){
        if(!$id){
            return FALSE;
        }
        else{
            $this->emails_m->update($id,array('is_read'=>NULL));
            return TRUE;
        }
    }

    function save_as_draft($id=0){
        if(!$id){
            return FALSE;
        }
        else{
            $this->emails_m->update_queue($id,array('is_draft'=>1));
            return TRUE;
        }
    }

    function delete_queue($id=0){
        if(!$id){
            return FALSE;
        }
        else{
            $this->emails_m->delete_email_queue($id);
            return TRUE;
        }
    }

    function send_draft($id=0){
        if(!$id){
            return FALSE;
        }
        else{
            $this->emails_m->update_queue($id,array('is_draft'=>0));
            return TRUE;
        }
    }

    function delete_draft($id=0){
        if(!$id){
            return FALSE;
        }
        else{
            $this->emails_m->delete_email_queue($id);
            return TRUE;
        }
    }

    function send_thank_you_email(){
        $this->messaging->send_thank_you_email($this->group->id);
    }
}
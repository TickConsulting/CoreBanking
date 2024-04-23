<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

	protected $validation_rules=array(
        array(
                'field'     =>  'primary_color',
                'label'     =>  'Primary Color',
                'rules'     =>  '',
            ),
        array(
                'field'     =>  'secondary_color',
                'label'     =>  'Secondary Color',
                'rules'     =>  '',
            ),
        array(
                'field'     =>  'tertiary_color',
                'label'     =>  'Tertiary Color',
                'rules'     =>  '',
            ),
        array(
                'field'     =>  'text_color',
                'label'     =>  'Text Color',
                'rules'     =>  '',
            ),
        array(
                'field'     =>  'link_color',
                'label'     =>  'Link Color',
                'rules'     =>  '',
            ),
        array(
                'field'     =>  'trial_days',
                'label'     =>  'Groups Trial Days',
                'rules'     =>  'trim|required|numeric',
            ),
        array(
                'field'     =>  'protocol',
                'label'     =>  '<?php echo $post->application_name; ?> Protocol',
                'rules'     =>  'trim|required',
            ),
        array(
                'field'     =>  'url',
                'label'     =>  '<?php echo $post->application_name; ?> URL',
                'rules'     =>  'trim|required',
            ),
        array(
                'field'     =>  'default_language_id',
                'label'     =>  'Default Language',
                'rules'     =>  'required|numeric',
            ),
        array(
                'field'     =>  'bill_number_start',
                'label'     =>  'Groups Billing Number Start',
                'rules'     =>  'trim|required|numeric',
            ),
        array(
                'field'     =>  'favicon',
                'label'     =>  '<?php echo $post->application_name; ?> Favicon',
                'rules'     =>  'trim',
            ),
        array(
                'field'     =>  'logo',
                'label'     =>  '<?php echo $post->application_name; ?> Logo',
                'rules'     =>  'trim',
            ),
        array(
                'field'     =>  'paper_header_logo',
                'label'     =>  '<?php echo $post->application_name; ?> Paper Header Logo',
                'rules'     =>  'trim',
            ),
        array(
                'field'     =>  'paper_footer_logo',
                'label'     =>  '<?php echo $post->application_name; ?> Paper Footer Logo',
                'rules'     =>  'trim',
            ),
        array(
                'field'     =>  'admin_login_logo',
                'label'     =>  'Admin Login Page Logo',
                'rules'     =>  'trim',
            ),
        array(
                'field'     =>  'group_login_logo',
                'label'     =>  'Group Login Page Logo',
                'rules'     =>  'trim',
            ),
        array(
                'field'     =>  'responsive_logo',
                'label'     =>  'Group Responsive Logo',
                'rules'     =>  'trim',
            ),
        array(
                'field'     =>  'application_name',
                'label'     =>  'Application Name',
                'rules'     =>  'trim|required',
            ),
        array(
                'field'     =>  'sender_id',
                'label'     =>  'Sender ID',
                'rules'     =>  'trim|required',
            ),
        array(
                'field'     =>  'home_page_controller',
                'label'     =>  'Home Page Controller',
                'rules'     =>  'trim',
            ),
        array(
                'field'     =>  'enable_two_factor_auth',
                'label'     =>  'Enable Two factor authentication',
                'rules'     =>  'trim|numeric',
        ),
        array(
                'field'     =>  'enable_refferers',
                'label'     =>  'Enable Refferers',
                'rules'     =>  'trim|numeric',
            ),
        array(
                'field'     =>  'enforce_default_country',
                'label'     =>  'Enforce Default Country',
                'rules'     =>  'trim|numeric',
            ),
        array(
                'field'     =>  'display_group_information',
                'label'     =>  'Display Group Information on Group Dashboard',
                'rules'     =>  'trim|numeric',
            ),
        array(
                'field'     =>  'enforce_group_setup_tasks',
                'label'     =>  'Enforce Group Setup Tasks',
                'rules'     =>  'trim|numeric',
            ),
        array(
                'field'     =>  'disable_smses',
                'label'     =>  'Disable SMSes',
                'rules'     =>  'trim|numeric',
            ),
        array(
                'field'     =>  'session_length',
                'label'     =>  'Session Length',
                'rules'     =>  'trim|required|numeric',
            ),
        array(
                'field'     =>  'enable_language_change',
                'label'     =>  'Language Change',
                'rules'     =>  'trim|numeric',
            ),
        array(
                'field'     =>  'session_timeout',
                'label'     =>  'Session Timeout',
                'rules'     =>  'trim|required|numeric',
            ),
        array(
                'field'     =>  'allow_self_onboarding',
                'label'     =>  'Allow Self Onboarding',
                'rules'     =>  'trim|numeric',
            ),
        array(
                'field'     =>  'show_system_account_balance',
                'label'     =>  'Show System Account Balance',
                'rules'     =>  'trim|numeric',
            ),
        array(
                'field'     =>  'enable_online_disbursement',
                'label'     =>  'Enable Online Disbursments',
                'rules'     =>  'trim|numeric',
            ),
        array(
            'field'     =>  'enable_google_recaptcha',
            'label'     =>  'Enable Google Recaptcha',
            'rules'     =>  'trim|numeric',
        ),
        array(
                'field'     =>  'activate_billing',
                'label'     =>  'Activate billing',
                'rules'     =>  'trim|numeric',
        ),
        array(
            'field'     =>  'activate_login_attempts',
            'label'     =>  'Enforce Login Attempts',
            'rules'     =>  'trim|numeric',
        ),
        array(
            'field'     =>  'entity_name',
            'label'     =>  'Entity Name',
            'rules'     =>  'trim|required',
        ),
    );

    protected $data = array();

    protected $path = 'uploads/logos';

	function __construct(){
        parent::__construct();
        $this->load->library('files_uploader');
        $this->load->model('settings_m');
        $this->load->model('languages/languages_m');
    }

    function index()
    {

        redirect('admin/settings/view');
    }

    public function create()
    {
        $post = new stdClass();      
        $this->form_validation->set_rules($this->validation_rules);

        if($this->settings_m->get_all())
        {
            redirect('admin/settings/edit/1');
        }

        //if(isset($_FILES['favicon'])){echo $_FILES['favicon']['tmp_name'];die;}
        if($this->form_validation->run())
        {
           $logo_directory = './uploads/logos';
           if(!is_dir($logo_directory))
           {
                mkdir($logo_directory,0777,TRUE);
           }

            $favicon = $this->files_uploader->upload('favicon',$this->path);
            $logo = $this->files_uploader->upload('logo',$this->path);
            $paper_header_logo = $this->files_uploader->upload('paper_header_logo',$this->path);
            $paper_footer_logo = $this->files_uploader->upload('paper_footer_logo',$this->path);
            $admin_login_logo = $this->files_uploader->upload('admin_login_logo',$this->path);
            $group_login_logo = $this->files_uploader->upload('group_login_logo',$this->path);
            $responsive_logo  = $this->files_uploader->upload('responsive_logo',$this->path);

            $input = array(
                        'protocol'  =>  $this->input->post('protocol'),
                        'url'  =>  $this->input->post('url'),
                        'default_language_id' =>  $this->input->post('default_language_id'),
                        'primary_color' =>  $this->input->post('primary_color'),
                        'secondary_color'=> $this->input->post('secondary_color'),
                        'tertiary_color'=> $this->input->post('tertiary_color'),
                        'text_color'    => $this->input->post('text_color'),
                        'link_color'    => $this->input->post('link_color'),
                        'trial_days'    =>  $this->input->post('trial_days'),
                        'session_length'    =>  $this->input->post('session_length'),
                        'bill_number_start'=>   $this->input->post('bill_number_start'),
                        'allow_self_onboarding'=>   $this->input->post('allow_self_onboarding'),
                        'show_system_account_balance'=>   $this->input->post('show_system_account_balance'),
                        'enable_online_disbursement'=>   $this->input->post('enable_online_disbursement'),
                        'enable_language_change'=>   $this->input->post('enable_language_change'),
                        'enable_two_factor_auth'=>   $this->input->post('enable_two_factor_auth'),
                        'enable_google_recaptcha'=>   $this->input->post('enable_google_recaptcha'),
                        'favicon'       =>  $favicon['file_name']?:'',
                        'logo'          =>  $logo['file_name']?:'',
                        'group_login_logo'=> $group_login_logo['file_name']?:'',
                        'admin_login_logo'=> $admin_login_logo['file_name']?:'',
                        'paper_header_logo'=>$paper_header_logo['file_name']?:'',
                        'paper_footer_logo'=>$paper_footer_logo['file_name']?:'',
                        'responsive_logo'  =>$responsive_logo['file_name']?:'',
                        'created_by'    =>  $this->ion_auth->get_user()->id,
                        'created_on'    =>  time(),
                        'active'        =>  1,
                        'activate_billing' => $this->input->post('activate_billing'),
                        'activate_login_attempts' => $this->input->post('activate_login_attempts'),
                        'entity_name' => $this->input->post('entity_name'),
                );

            $id = $this->settings_m->insert($input);
            if($id)
            {
                $this->session->set_flashdata('success','Settings successfully added');
                redirect('admin/settings/edit/'.$id);
            }
            else
            {
                $this->session->set_flashdata('error','Unable to add the setting. Please try again');
                redirect('admin/settings/create');
            }
           
        }
        else
        {
            foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $this->data['language_options'] = $this->languages_m->get_language_options();
        $this->data['id'] = '';
        $this->data['post'] = $post;
        $this->template->title('Create <?php echo $post->application_name; ?> Settings')->build('admin/form',$this->data);
    }

    function edit($id=0)
    {   
        $post = $this->settings_m->get_settings($id);
        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry the setting does not exist');
            redirect('admin/settings/create');
        }
        $this->form_validation->set_rules($this->validation_rules);
        $favicon['file_name'] = '';
        $logo['file_name'] = ''; 
        $paper_header_logo['file_name']='';
        $paper_footer_logo['file_name']='';
        $admin_login_logo['file_name']='';
        $group_login_logo['file_name']='';
        $responsive_logo['file_name']='';
        if($this->form_validation->run()){
            $logo_directory = './uploads/logos';
            if(!is_dir($logo_directory)){
                mkdir($logo_directory,0777,TRUE);
            }

            if($_FILES['favicon']['name'])
            {
                $favicon = $this->files_uploader->upload('favicon',$this->path);
                 if($favicon)
                 {
                    if(is_file(FCPATH.$this->path.'/'.$post->favicon)){
                        if(unlink(FCPATH.$this->path.'/'.$post->favicon)){
                            $this->session->set_flashdata('info','Favicon Icon successfully replaced');
                        }
                    }

                 }
            }

            if($_FILES['logo']['name'])
            {
                 $logo = $this->files_uploader->upload('logo',$this->path);
                 if($logo)
                 {
                    if(is_file(FCPATH.$this->path.'/'.$post->logo)){
                        if(unlink(FCPATH.$this->path.'/'.$post->logo)){
                            $this->session->set_flashdata('info','<?php echo $post->application_name; ?> Logo successfully replaced');
                        }
                    }

                 }
            }

            if($_FILES['paper_header_logo']['name'])
            {
                 $paper_header_logo = $this->files_uploader->upload('paper_header_logo',$this->path);
                 if($paper_header_logo)
                 {
                    if(is_file(FCPATH.$this->path.'/'.$post->paper_header_logo)){
                        if(unlink(FCPATH.$this->path.'/'.$post->paper_header_logo)){
                            $this->session->set_flashdata('info','<?php echo $post->application_name; ?> Paper Header Logo successfully replaced');
                        }
                    }

                 }
            }

            if($_FILES['paper_footer_logo']['name'])
            {
                 $paper_footer_logo = $this->files_uploader->upload('paper_footer_logo',$this->path);
                 if($paper_footer_logo)
                 {
                    if(is_file(FCPATH.$this->path.'/'.$post->paper_footer_logo)){
                        if(unlink(FCPATH.$this->path.'/'.$post->paper_footer_logo)){
                            $this->session->set_flashdata('info','<?php echo $post->application_name; ?> Paper Footer Logo successfully replaced');
                        }
                    }

                 }
            }

            if($_FILES['admin_login_logo']['name'])
            {
                 $admin_login_logo = $this->files_uploader->upload('admin_login_logo',$this->path);
                 if($admin_login_logo)
                 {
                    if(is_file(FCPATH.$this->path.'/'.$post->admin_login_logo)){
                        if(unlink(FCPATH.$this->path.'/'.$post->admin_login_logo)){
                            $this->session->set_flashdata('info','<?php echo $post->application_name; ?> Admin Login Page Logo successfully replaced');
                        }
                    }

                 }
            }

            if($_FILES['group_login_logo']['name'])
            {
                 $group_login_logo = $this->files_uploader->upload('group_login_logo',$this->path);
                 if($group_login_logo)
                 {
                    if(is_file(FCPATH.$this->path.'/'.$post->group_login_logo)){
                        if(unlink(FCPATH.$this->path.'/'.$post->group_login_logo)){
                            $this->session->set_flashdata('info','<?php echo $post->application_name; ?> Group Login Page Logo successfully replaced');
                        }
                    }

                 }
            }

            if($_FILES['responsive_logo']['name'])
            {
                 $responsive_logo = $this->files_uploader->upload('responsive_logo',$this->path);
                 if($responsive_logo)
                 {
                    if(is_file(FCPATH.$this->path.'/'.$post->responsive_logo)){
                        if(unlink(FCPATH.$this->path.'/'.$post->responsive_logo)){
                            $this->session->set_flashdata('info','<?php echo $post->application_name; ?> Group Responsive Logo successfully replaced');
                        }
                    }

                 }
            }


            $data = array(
                    'application_name'         =>  $this->input->post('application_name'),
                    'application_email'         =>  $this->input->post('application_email'),
                    'application_phone'         =>  $this->input->post('application_phone'),
                    'sender_id'         =>  $this->input->post('sender_id'),
                    'default_language_id'         =>  $this->input->post('default_language_id'),
                    'home_page_controller'         =>  $this->input->post('home_page_controller'),
                    'primary_color'         =>  $this->input->post('primary_color'),
                    'secondary_color'       =>  $this->input->post('secondary_color'),
                    'tertiary_color'        =>  $this->input->post('tertiary_color'),
                    'text_color'            =>  $this->input->post('text_color'),
                    'link_color'            =>  $this->input->post('link_color'),
                    'trial_days'            =>  $this->input->post('trial_days'),
                    'session_length'    =>  $this->input->post('session_length'),
                    'bill_number_start'     =>  $this->input->post('bill_number_start'),
                    'enable_language_change'=>   $this->input->post('enable_language_change'),
                    'url'                   =>  $this->input->post('url'),
                    'protocol'              =>  $this->input->post('protocol'),
                    'enable_two_factor_auth' =>  $this->input->post('enable_two_factor_auth')?1:0,
                    'enable_referrers'      =>  $this->input->post('enable_referrers')?1:0,
                    'enforce_default_country'      =>  $this->input->post('enforce_default_country')?1:0,
                    'display_group_information'      =>  $this->input->post('display_group_information')?1:0,
                    'enforce_group_setup_tasks'      =>  $this->input->post('enforce_group_setup_tasks')?1:0,
                    'disable_smses'      =>  $this->input->post('disable_smses')?1:0,
                    'session_timeout' => $this->input->post('session_timeout'),
                    'favicon'               =>  $favicon['file_name']?:$post->favicon,
                    'allow_self_onboarding'=>   $this->input->post('allow_self_onboarding'),
                    'show_system_account_balance'=>   $this->input->post('show_system_account_balance'),
                    'enable_online_disbursement'=>   $this->input->post('enable_online_disbursement'),
                    'enable_google_recaptcha'=>   $this->input->post('enable_google_recaptcha'),
                    'logo'                  =>  $logo['file_name']?:$post->logo,
                    'paper_footer_logo'     =>  $paper_footer_logo['file_name']?:$post->paper_footer_logo,
                    'paper_header_logo'     =>  $paper_header_logo['file_name']?:$post->paper_header_logo,
                    'admin_login_logo'      =>  $admin_login_logo['file_name']?:$post->admin_login_logo,
                    'group_login_logo'      =>  $group_login_logo['file_name']?:$post->group_login_logo,
                    'responsive_logo'       =>  $responsive_logo['file_name']?:$post->responsive_logo,
                    'modified_by'           =>  $this->ion_auth->get_user()->id,
                    'modified_on'           =>  time(),
                    'activate_billing' =>  $this->input->post('activate_billing')?1:0,
                    'activate_login_attempts' => $this->input->post('activate_login_attempts')?1:0,
                    'entity_name'           => $this->input->post('entity_name'),
                );
            $update = $this->settings_m->update($post->id,$data);
            if($update){ 
                $this->session->set_flashdata('success','<?php echo $post->application_name; ?> Settings successfully updated');
            }
            else
            {
                $this->session->unset('info');
                $this->session->set_flashdata('error','Unable to update the settings');
            }

            redirect('admin/settings/view');
        }else{

        }
        $this->data['language_options'] = $this->languages_m->get_language_options();
        $this->data['path'] = $this->path;
        $this->data['post'] = $post;
        $this->data['id'] = '';
        $this->data['post'] = $post;
        $this->template->title('Edit <?php echo $post->application_name; ?> Settings')->build('admin/form',$this->data);
    }

    function view(){
        $post = $this->settings_m->get_settings();
        if(empty($post))
        {
            redirect('admin/settings/create','create');
        }
        $this->data['post'] = $post;
        $this->data['path'] = $this->path;
        $this->template->title('Application Settings')->build('admin/view',$this->data);
    }

}
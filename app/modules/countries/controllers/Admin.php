<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

	protected $validation_rules=array(
        array(
                'field' =>  'name',
                'label' =>  'Country Name',
                'rules' =>  'trim|required|callback_name_is_unique',
            ),
        array(
                'field' =>  'code',
                'label' =>  'Country Code',
                'rules' =>  'trim|required|callback_code_is_unique',
            ),
        array(
                'field' =>  'currency',
                'label' =>  'Country Currency',
                'rules' =>  'trim|required',
            ),
        array(
                'field' =>  'currency_code',
                'label' =>  'Country Currency',
                'rules' =>  'trim|required',
            ),
        array(
                'field' =>  'calling_code',
                'label' =>  'Country calling_code Code',
                'rules' =>  'trim|required',
            )
    );

	function __construct(){
        parent::__construct();
        $this->load->model('countries_m');
    }

    public function create(){
    	$data = array();
        $post = new stdClass();      
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                'name'=>$this->input->post('name'),
                'code'=>$this->input->post('code'),
                'currency'=>$this->input->post('currency'),
                'currency_code'=>$this->input->post('currency_code'),
                'calling_code' => $this->input->post('calling_code'),
                'active'=>1,
                'created_on'=>time(),
                'created_by'=>$this->ion_auth->get_user()->id
            );
            $id = $this->countries_m->insert($data);
            if($id){
                $this->session->set_flashdata('success','Country created successfully');
            }else{
                $this->session->set_flashdata('error','Country could not be created');
            }
            if($this->input->post('new_item')){
                redirect('admin/countries/create','refresh');
            }else{
                redirect('admin/countries/listing');
            }
        }else{
        	foreach ($this->validation_rules as $key => $field){
                $post->$field['field'] = set_value($field['field']);
            }
        }
        $data['id'] = '';
        $data['post'] = $post;
        $this->template->title('Create Country')->build('admin/form',$data);
    }

    public function get_country_and_insert_to_currency(){
        $countries = $this->countries_m->get_all();
        foreach ($countries as $country) {
            $country_array = array(
                'name'=>$country->name,
                'code'=>$country->code,
                'calling_code'=>$country->calling_code,
                'active'=>1,
                'created_by'=>$this->user->id,
                'created_on'=>time(),
            );
            //print_r($country_array);
            //$id =1;
            $id = $this->country_m->insert_to1($country_array);
            if($id){
                $currency_array= array(
                    'country_id'=>$id,
                    'currency'=>$country->currency,
                    'currency_code'=>$country->currency_code,
                    'active'=>1,
                    'created_by'=>$this->user->id,
                    'created_on'=>time(),
                );
               $this->country_m->insert_to_currency($currency_array);
            }else{
                die('not insert 1');
            }
            
        }
        

    }

    public function listing(){
        $data = array();
        $search_query = array(
                'name' => $this->input->get('name'),
                'currency' => $this->input->post('currency'),
                'calling_code' => $this->input->post('calling_code'),
            );
        $total_rows = $this->countries_m->count_all($search_query);
        $pagination = create_pagination('admin/countries/listing/pages',$total_rows,50,5,TRUE);
        $data['posts'] = $this->countries_m->limit($pagination['limit'])->get_all($search_query);
        $data['pagination'] = $pagination;
        $this->template->title('List Countries')->build('admin/listing',$data);
    }

    public function edit($id = 0){
        $id OR redirect('admin/countries/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->countries_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the country does not exist');
            redirect('admin/countries/listing');
        } 
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                'name'=>$this->input->post('name'),
                'code'=>$this->input->post('code'),                
                'calling_code' => $this->input->post('calling_code'),
                'modified_on'=>time(),
                'modified_by'=>$this->ion_auth->get_user()->id
            );            
            $result = $this->countries_m->update($id,$data);
            if($result){
                // $currency_data = array(
                //     'currency'=>$this->input->post('currency'),
                //     'currency_code'=>$this->input->post('currency_code'),
                //     'modified_on'=>time(),
                //     'modified_by'=>$this->ion_auth->get_user()->id
                // );
                // $success_id = $this->currency_m->update($id,$data);
                if($success_id){
                    $this->session->set_flashdata('success','Country Changes Saved Successfully');
                }else{
                    $this->session->set_flashdata('error','Changes could not be saved');
                }                
            }else{
                $this->session->set_flashdata('error','Changes could not be saved');
            }
            if($this->input->post('new_item')){
                redirect('admin/countries/create','refresh');
            }else{
                redirect('admin/countries/listing');
            }
        }else{

        }
        $data['id'] = $id;
        $data['post'] = $post;
        $this->template->title('Edit Country')->build('admin/form',$data);
    }

    public function name_is_unique(){
        if($country = $this->countries_m->get_where(array('name'=>$this->input->post('name')),FALSE)){
            if($this->input->post('id')==$country->id){
                return TRUE;
            }else{
                $this->form_validation->set_message('name_is_unique', 'The country name already exists.');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    public function code_is_unique(){
        if($country = $this->countries_m->get_where(array('code'=>$this->input->post('code')),FALSE)){
            if($this->input->post('id')==$country->id){
                return TRUE;
            }else{
                $this->form_validation->set_message('code_is_unique', 'The country code already exists.');
                return FALSE;
            }
        }else{
            return TRUE;
        }
    }

    public function _calling_code_is_unique(){
        $calling_code = $this->input->post('calling_code');
        $id = $this->input->post('id');
        if($this->countries_m->unique_country_code($calling_code,$id)){
            return TRUE;
        }else{
            $this->form_validation->set_message('_calling_code_is_unique', 'The Calling Code must be Unique.');
            return FALSE;
        }
    }

    function action(){
        $action_to = $this->input->post('action_to');
        $action = $this->input->post('btnAction');
        if($action == 'bulk_hide'){
            for($i=0;$i<count($action_to);$i++){
                $this->hide($action_to[$i],FALSE);
            }
        }else if($action == 'bulk_activate'){
            for($i=0;$i<count($action_to);$i++){
                $this->activate($action_to[$i],FALSE);
            }
        }else if($action == 'set_as_default'){
            if(count($action_to)==1){
                $this->set_as_default($action_to[0]);
            }else if(count($action_to)==0){
                $this->session->set_flashdata('error','Please select one country.');
            }else{
                $this->session->set_flashdata('error','Please select one country. You cannot more than one country.');
            }
        }
        redirect('admin/countries/listing');
    }

    function set_as_default($id = 0){
        $id OR redirect('admin/countries/listing');
        $country = $this->countries_m->get_default_country();
        if($country){
            $input = array(
                'default_country' => 0,
                'modified_by' => $this->user->id,
                'modified_on' => time(),
            );
            if($result = $this->countries_m->update($country->id,$input)){
                $this->session->set_flashdata('info',$country->name.' successfully removed as default country.');
            }else{
                $this->session->set_flashdata('error',$country->name.' could not be removed as default country.');
            }
        }else{
            $this->session->set_flashdata('error','Could not find default country.');
        }
        $input = array(
            'default_country' => 1,
            'modified_by' => $this->user->id,
            'modified_on' => time(),
        );

        $country = $this->countries_m->get($id);
        if($country){
            if($result = $this->countries_m->update($country->id,$input)){
                $this->session->set_flashdata('success',$country->name.' successfully set as default country.');
            }else{
                $this->session->set_flashdata('error',$country->name.' could not be set as default country.');
            }
        }else{
            $this->session->set_flashdata('error','Could not find country to set as default.');
        }
        redirect('admin/countries/listing');
    }

    function hide($id = 0,$redirect= TRUE){
        $id OR redirect('admin/countries/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Country does not exist');
            redirect('admin/countries/listing');
        }
        $result = $this->countries_m->update($post->id,array('active'=>0,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Countries were successfully hidden');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Country');
        }

        if($redirect){
            redirect('admin/countries/listing');
        }
        return TRUE;
    }

    function activate($id = 0,$redirect= TRUE){
        $id OR redirect('admin/countries/listing');
        $post = $this->admin_menus_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Country does not exist');
            redirect('admin/countries/listing');
        }

        $result = $this->countries_m->update($post->id,array('active'=>1,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Countries were successfully activated');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Country');
        }

        if($redirect){
            redirect('admin/countries/listing');
        }
        return TRUE;
    }

    function rename_file(){
        $countries = $this->countries_m->get_all();
        $path = "D:/flags/png";
        $i = 0;
        foreach ($countries as $key=>$country) {
            $flag_slug = generate_slug(trim($country->name));
            $file = $path.'/'.$flag_slug.'.png';
            if(file_exists($file)){
                $new_file = $path.'/new_file/'.generate_slug(trim($country->code)).'.png';
                if(rename($file, $new_file)){

                }else{
                    echo ($i+1).' '.$country->name.' failed renaming<br/>';
                    $i++;
                }
            }else{
                echo ($i+1).' '.$country->name.' does not exist<br/>';
                $i++;
            }
        }
    }

    function get_all_countries_to_json(){
        $posts = $this->countries_m->get_all();
        $data = array();
        foreach ($posts as $key => $post) {
            $data[] = array(
                'id'=>$post->id,
                'name'=>$post->name,
                'code'=>$post->code,
                'currency'=>$post->currency,
                'currency_code'=>$post->currency_code,
                'default_country'=>$post->default_country,
                'calling_code'=>$post->calling_code,
            );
            
        }
        file_put_contents("logs/countries.json",json_encode($data)."\n",FILE_APPEND);
    }

}
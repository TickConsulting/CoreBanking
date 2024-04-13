<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Group extends Group_Controller{

    protected $validation_rules=array(
        array(
                'field' =>   'name',
                'label' =>   'Category Name',
                'rules' =>   'trim|required|xss_clean',
            ),
        array(
                'field' =>   'slug',
                'label' =>   'Category Name Slug',
                'rules' =>   'trim|required|xss_clean|callback__is_unique_category_name',
            ),
        array(
                'field' =>   'description',
                'label' =>   'Category Description',
                'rules' =>   'trim|xss_clean',
            ),
    );

    public function __construct(){
        parent::__construct();
        $this->load->model('asset_categories_m');
    }
    
    function _is_unique_category_name(){
        $id = $this->input->post('id');
        $group_id = $this->group->id;
        $slug = $this->input->post('slug');
        if($slug){
            if($this->asset_categories_m->get_by_slug($slug,$id,$group_id)){
                $this->form_validation->set_message('_is_unique_category_name','Another Fine Category by the name <strong>`'.$this->input->post('name').'`</strong> already exists');
                return FALSE;
            }else{
                return TRUE;
            }
        }  
    }
   
    // function index(){
    //     $data = array();
    //     $this->template->title('Asset Categories')->build('group/index',$data);
    // }

    public function index(){
        $total_rows = $this->asset_categories_m->count_group_asset_categories();
        $pagination = create_pagination('group/asset_categories/listing/pages', $total_rows,50,5,TRUE);
        $data['pagination'] = $pagination;
        $data['posts'] = $this->asset_categories_m->limit($pagination['limit'])->get_group_asset_categories();
        $this->template->title(translate('List Group Asset Categories'))->build('group/listing',$data);
    }

    public function create(){
        $post = new stdClass();
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $id = $this->asset_categories_m->insert(array(
                'name'  =>  $this->input->post('name'),
                'slug'  =>  $this->input->post('slug'),
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
                )
            );

            if($id){
                $this->session->set_flashdata('success',$this->input->post('name').' as a asset category was successfully created');
                if($this->input->post('new_item')){
                    redirect('group/asset_categories/create');
                }else{
                    redirect('group/asset_categories/listing');
                }
            }else{
                $this->session->set_flashdata('error','Unable to create a new asset category');
                redirect('group/asset_categories/create');
            }
        }else{
            foreach ($this->validation_rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }
        $data['post'] = $post;
        $data['id'] = '';
        $this->template->title('Create Asset Category')->build('group/form',$data);
    }

     public function ajax_create(){
        $response =array();        
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $name= $this->input->post('name');
            $asset_category = array(
                'name'  =>  $this->input->post('name'),
                'slug'  =>  $this->input->post('slug'),
                'group_id'  =>  $this->group->id,
                'active'    =>  1,
                'created_by'    =>  $this->user->id,
                'created_on'    =>  time(),
            );
            $id = $this->asset_categories_m->insert($asset_category);
            if($id){
                $asset_category['id'] = $id;
                $response = array(
                    'status' => 1,
                    'id'=>$id,
                    'name'=>$name,
                    'asset_category'=>$asset_category,
                    'message' => 'Created successfully.',
                    'validation_errors' =>'',
                    'refer'=>site_url('group/asset_categories/listing')
                );
            }else{
                $response = array(
                    'status' => 0,
                    'message' => 'Could not add asset category.',
                    'validation_errors' =>'',
                );
            }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'There are some errors on the form. Please review and try again.',
                'validation_errors' => $this->form_validation->error_array(),
            );
        }

        echo json_encode($response);
    }

    function ajax_edit(){
        $response =array();
        $id = $this->input->post('id');
        if($id){
            $post = $this->asset_categories_m->get($id);
            if($post){
                $this->form_validation->set_rules($this->validation_rules);
                if($this->form_validation->run()){
                    $name= $this->input->post('name');
                    $update = $this->asset_categories_m->update($post->id,array(
                        'name'      =>  $this->input->post('name'),
                        'slug'      =>  $this->input->post('slug'),
                        'modified_by'=> $this->user->id,
                        'modified_on'=> time(),
                    ));
                    if($update){
                        $response = array(
                            'status' => 1,
                            'message' => $this->input->post('name').' successfully updated',
                            'refer'=>site_url('group/asset_categories/listing')
                        ); 
                    }else{
                        $response = array(
                            'status' => 0,
                            'message' => 'Unable to update Asset Category',
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
            }else{
               $response = array(
                    'status' => 0,
                    'message' => 'Asset category details missing.',
                ); 
           }
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Asset category id is missing in JSON Payload.',
            );
        }
        echo json_encode($response);
    }

    public function edit($id=0){
        $id OR redirect('group/asset_categories/listing');
        $post = new stdClass();
        $post = $this->asset_categories_m->get($id);
        if(!$post){
            $this->session->set_flashdata('info','Sorry the asset category does not exist');
            redirect('group/asset_categories/listing');
        }else{
            $this->form_validation->set_rules($this->validation_rules);
            if($this->form_validation->run()){
                $update = $this->asset_categories_m->update($post->id,array(
                    'name'      =>  $this->input->post('name'),
                    'slug'      =>  $this->input->post('slug'),
                    'modified_by'=> $this->user->id,
                    'modified_on'=> time(),
                ));
                if($update){
                    $this->session->set_flashdata('success',$this->input->post('name').' successfully updated');
                    if($this->input->post('new_item')){
                        redirect('group/asset_categories/create');
                    }else{
                        redirect('group/asset_categories/listing');
                    }
                }else{
                    $this->session->set_flashdata('error','Unable to update Fine Category');
                    redirect('group/asset_categories/listing');
                }
            }else{
                foreach (array_keys($this->validation_rules) as $field){
                    if (isset($_POST[$field])){
                        $post->$field = $this->form_validation->$field;
                    }
                }
            }
            $data['post'] = $post;
            $data['id'] = $id;
            $this->template->title('Edit Group Asset Category')->build('group/form',$data);
        }
    }

    public function action(){   
        switch ($this->input->post('btnAction')){ 
            case 'hide':
                $this->hide();
                break;
            case 'unhide':
                $this->unhide();
                break;
            default:
                redirect('admin/asset_categories/listing');
                break;
        }
    }

    function hide($id=0,$redirect=TRUE){
        $id OR redirect('group/asset_categories/listing');
        $post = $this->asset_categories_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('Error','Sorry, the Asset Category does not exist');
            redirect('group/asset_categories/listing');
            return FALSE; 
        }

        if($post->is_hidden){
            $this->session->set_flashdata('Error','Sorry, the Asset Category is already hidden');
            redirect('group/asset_categories/listing');
            return FALSE; 
        }
        $res = $this->asset_categories_m->update($post->id,array('is_hidden'=>1,'modified_by'=>$this->user->id,'modified_on'=>time()));
        if($res){
            $this->session->set_flashdata('success','Asset category successfully hidden');
            if($redirect){
                redirect('group/asset_categories/listing');
            }
        }else{
            $this->session->set_flashdata('error','Unable to hide the Asset Category');
            if($redirect){
                redirect('group/asset_categories/listing');
            }
        }
    }

    function unhide($id=0,$redirect=TRUE){
        $id OR redirect('group/asset_categories/listing');
        $post = $this->asset_categories_m->get($id);
        if(empty($post)){
            $this->session->set_flashdata('Error','Sorry, the Asset Category does not exist');
            redirect('group/asset_categories/listing');
            return FALSE; 
        }
        if(!$post->is_hidden){
            $this->session->set_flashdata('Error','Sorry, the Asset Category is not hidden');
            redirect('group/asset_categories/listing');
            return FALSE; 
        }

        $res = $this->asset_categories_m->update($post->id,array('is_hidden'=>NULL,'modified_by'=>$this->user->id,'modified_on'=>time()));

        if($res){
            $this->session->set_flashdata('success','Asset category successfully unhidden');
            if($redirect){
                redirect('group/asset_categories/listing');
            }
        }else{
            $this->session->set_flashdata('error','Unable to unhide the Fine Category');
            if($redirect){
                redirect('group/asset_categories/listing');
            }
        }
    }

    function ajax_get_asset_category($id = 0){
        $id = $this->input->post('id');
        if($asset_category = $this->asset_categories_m->get($id)){
            echo json_encode($asset_category);
        }else{
            echo "error";
        }
    }

}


<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller
{

    protected $rules=array(
            array(
                    'field' =>  'name',
                    'label' =>  'Menu Name',
                    'rules' =>  'required',
                ),
             array(
                    'field' =>  'url',
                    'label' =>  'Menu URL',
                    'rules' =>  'required',
                ),
              array(
                    'field' =>  'icon',
                    'label' =>  'Menu Icon',
                    'rules' =>  'required',
                ),
               array(
                    'field' =>  'parent_id',
                    'label' =>  'Parent Menu',
                    'rules' =>  '',
                ),
               array(
                    'field' =>  'color',
                    'label' =>  'Color',
                    'rules' =>  'required',
                ),
               array(
                    'field' =>  'size',
                    'label' =>  'Menu Size',
                    'rules' =>  '',
                ),
        );
    
    protected $tile_sizes = array(
        '' => 'Normal',
        'double' => 'Big',
    );

    protected $tile_colors = array(
        'Blue' => array(
            'blue' => 'Blue (Default)',
            'blue-hoki' => 'Blue Hoki',
            'blue-steel' => 'Blue Steel',
            'blue-madison' => 'Blue Madison',
            'blue-chambray' => 'Blue Chambray',
            'blue-ebonyclay' => 'Blue Ebonyclay',
            ),
        'Green' => array(
            'green' => 'Green',
            'green-meadow' => 'Green Meadow',
            'green-seagreen' => 'Green Sea Green',
            'green-turquoise' => 'Green Turquoise' ,
            'green-haze' => 'Green Haze',
            'green-jungle' => 'Green Jungle',
            ),
        'Red' => array(
            'red' => 'Red',
            'red-pink' => 'Red Pink',
            'red-sunglo' => 'Red Sunglo',
            'red-intense' => 'Red Intense',
            'red-thunderbird' => 'Red Thunderbird',
            'red-flamingo' => 'Red Flamingo',
            ),
        'Yellow' => array(
            'yellow' => 'Yellow',
            'yellow-gold' => 'Yellow Gold',
            'yellow-casablanca' => 'Yellow Casablanca',
            'yellow-crusta' => 'Yellow Crusta',
            'yellow-lemon' => 'Yellow Lemon',
            'yellow-saffron' => 'Yellow Saffron',
            ),
        'Purple' => array(
            'purple' => 'Purple',
            'purple-plum' => 'Purple Plum',
            'purple-medium' => 'Purple Medium',
            'purple-studio' => 'Purple Studio',
            'purple-wisteria' => 'Purple Wisteria',
            'purple-seance' => 'Purple Seance',
            ),
        'Grey' => array(
            'grey' => 'Grey',
            'grey-cascade' => 'Grey Cascade',
            'grey-silver' => 'Grey Silver',
            'grey-steel' => 'Grey Steel',
            'grey-cararra' => 'Grey Cararra',
            'grey-gallery' => 'Grey Gallery',
            ),
    ); 


    protected $data=array();

	function __construct()
    {
        parent::__construct();
        $this->load->model('partner_menus_m');
    }

    function menu()
    {
       //$this->partner_menus_m->great_great_grand_child_is_active('',uri_string());
       $data['posts'] = array();
       $this->template->build('admin/listing',$data);
    }

    function index()
    {
        /*$this->partner_menus_m->generate_side_bar_menu();die;
        $this->template->title('partner Menus')->build('admin/index');*/
    }

    function delete($id = 0,$redirect= TRUE)
    {
        $id OR redirect('admin/partner_menus/listing');

        $post = $this->partner_menus_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the partner Menu does not exist');
            redirect('admin/partner_menus/listing');
        }

        $id = $this->partner_menus_m->delete($post->id);

        if($id)
        {
            $this->session->set_flashdata('success',$post->name.' was successfully deleted');
        }
        else
        {
            $this->session->set_flashdata('error','Unable to delete '.$post->name.' partner menu');
        }
        if($redirect)
        {
            redirect('admin/partner_menus/listing');
        }
        return TRUE;
    }

    function hide($id=0,$redirect=TRUE)
    {
        $id OR redirect('admin/partner_menus/listing');

        $post = $this->partner_menus_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the partner Menu does not exist');
            redirect('admin/partner_menus/listing');
        }
        if($post->active=='')
        {
            $this->session->set_flashdata('error','Sorry, the partner Menu is already hidden');
            redirect('admin/partner_menus/listing');
        }

        $id = $this->partner_menus_m->update($post->id,array
            (
                'active'=>NULL,
                'modified_on' => time(),
                'modified_by' => $this->ion_auth->get_user()->id,
            ));

        if($id)
        {
            $this->session->set_flashdata('success',$post->name.' was successfully hidden');
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' admin menu');
        }
        if($redirect)
        {
            redirect('admin/partner_menus/listing');
        }
        return TRUE;
    }

    function activate($id=0,$redirect=TRUE)
    {
        $id OR redirect('admin/partner_menus/listing');

        $post = $this->partner_menus_m->get($id);

        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the partner Menu does not exist');
            redirect('admin/partner_menus/listing');
        }
        if($post->active)
        {
            $this->session->set_flashdata('error','Sorry, the partner Menu is already activated');
            redirect('admin/partner_menus/listing');
        }

        $id = $this->partner_menus_m->update($post->id,array
            (
                'active'=>1,
                'modified_on' => time(),
                'modified_by' => $this->ion_auth->get_user()->id,
            ));

        if($id)
        {
            $this->session->set_flashdata('success',$post->name.' was successfully activated');
        }
        else
        {
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' partner menu');
        }
        if($redirect)
        {
            redirect('admin/partner_menus/listing');
        }
        return TRUE;
    }

    function listing()
    {
        $this->data['posts'] = $this->partner_menus_m->get_parent_links();
        $this->data['side_bar_menu_options'] = $this->partner_menus_m->get_options();
        $this->data['tile_sizes'] = $this->tile_sizes;
        $this->data['tile_colors'] = $this->tile_colors;
        $this->template->title('partner Menus')->build('admin/listing',$this->data);
    }

    function create()
    {
        $post = new StdClass();
        $this->form_validation->set_rules($this->rules);
        
        if($this->form_validation->run())
        {
             $data = array(
                'parent_id'=>$this->input->post('parent_id')?$this->input->post('parent_id'):0,
                'name'=>$this->input->post('name'),
                'url'=>$this->input->post('url'),
                'icon'=>$this->input->post('icon'),
                'color'=>$this->input->post('color'),
                'size'=>$this->input->post('size'),
                'created_by'=>$this->ion_auth->get_user()->id,
                'created_on'=>time(),
                'active'=>1,
            );
            $id = $this->partner_menus_m->insert($data);
            if($id)
            {
                $this->session->set_flashdata('success','Menu Item Created Successfully.');
                if($this->input->post('new_item'))
                {
                    redirect('admin/partner_menus/create','refresh');
                }
                else
                {
                    redirect('admin/partner_menus/edit/'.$id);
                }
                
            }
            else{
                $this->session->set_flashdata('error','Menu Item could not be Created.');
                redirect('admin/partner_menus/create');
            }
        }
        else
        {
            foreach ($this->rules as $key => $field) {
                $field_value = $field['field'];
                $post->$field_value = set_value($field['field']);
            }
        }

        $this->data['menus'] = $this->partner_menus_m->get_options();
        $this->data['tile_colors']=$this->tile_colors;
        $this->data['tile_sizes']=$this->tile_sizes;
        $this->data['post'] = $post;
        $this->template->title('partner Menus Form')->build('admin/form',$this->data);
    }

    function action()
    {
        $action_to = $this->input->post('action_to');

        $action = $this->input->post('btnAction');

        if($action == 'bulk_delete')
        {
            for($i=0;$i<count($action_to);$i++)
            {
                $this->delete($action_to[$i],FALSE);
            }
        }

        redirect('admin/partner_menus/listing');
    }

    function sort()
    {
        $this->data['posts'] = $this->partner_menus_m->get_parent_links();
        $this->template->title('Sort Menus')->build('admin/sort', $this->data);
    }

    function edit($id=0)
    {
        $id OR redirect('admin/partner_menus/listing');

        $post = new StdClass();

        $post = $this->partner_menus_m->get($id);
        if(!$post)
        {
            $this->session->set_flashdata('error','Sorry, the partner Menu does not exist');
            redirect('admin/partner_menus/listing');
        }

        $this->form_validation->set_rules($this->rules);

        if($this->form_validation->run())
        {
             $data = array(
                'parent_id'=>$this->input->post('parent_id')?$this->input->post('parent_id'):0,
                'name'=>$this->input->post('name'),
                'url'=>$this->input->post('url'),
                'icon'=>$this->input->post('icon'),
                'color'=>$this->input->post('color'),
                'size'=>$this->input->post('size'),
                'modified_by'=>$this->ion_auth->get_user()->id,
                'modified_on'=>time(),
            );
            $update = $this->partner_menus_m->update($id,$data);
            if($update)
            {
                $this->session->set_flashdata('success',$this->input->post('name').' successfully updated');
                if($this->input->post('new_item'))
                {
                    redirect('admin/partner_menus/create','refresh');
                }
                else
                {
                    redirect('admin/partner_menus/listing','refresh');
                }
            }
            else
            {
                $this->session->set_flashdata('error','Unable to update');
                redirect('admin/partner_menus/listing','refresh');
            }
        }
        else
        {
            foreach (array_keys($this->rules) as $field){
                 if (isset($_POST[$field])){
                    $post->$field = $this->form_validation->$field;
                }
            }
        }

        

        $this->data['menus'] = $this->partner_menus_m->get_options();
        $this->data['tile_colors'] = $this->tile_colors;
        $this->data['tile_sizes']=$this->tile_sizes;
        $this->data['post'] = $post;

        $this->template->title('Edit partner Menu')->build('admin/form',$this->data);
    }


    function ajax_sort_update()
    {
        $data = json_decode($this->input->post('json'));
        for($i=0;$i<count($data);$i++){
            $this->partner_menus_m->update($data[$i]->id,array(
                'position'=>$i,
                'modified_on' => time(),
                'modified_by' => $this->ion_auth->get_user()->id,
            ));
            $this->_children($data[$i],0,$i);
        }
    }

    private function _children($pt,$parent_id,$position){
        echo "Dashboard I:".$pt->id."P:".$parent_id."||";
        $this->partner_menus_m->update($pt->id,array(
            'position'=>$position,
            'parent_id'=>$parent_id,
            'modified_on' => time(),
            'modified_by' => $this->ion_auth->get_user()->id,
        ));
        $k=0;
        if(isset($pt->children)){
            foreach($pt->children as $child){
                $k++;
                $this->_children($child,$pt->id,$k);
            }
        }

    }

}
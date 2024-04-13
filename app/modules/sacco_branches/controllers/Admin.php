<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{

	protected $validation_rules=array(
        array(
                'field' =>  'name',
                'label' =>  'Sacco Name',
                'rules' =>  'trim|required',
            ),
        array(
                'field' =>  'sacco_id',
                'label' =>  'Sacco',
                'rules' =>  'trim|required|numeric',
            ),
        array(
                'field' =>  'code',
                'label' =>  'Sacco Code',
                'rules' =>  'trim|required',
            )
    );

	function __construct(){
        parent::__construct();
        $this->load->model('sacco_branches_m');
        $this->load->model('saccos/saccos_m');
    }

    public function create($sacco_id = ''){
    	$data = array();
        $post = new stdClass();      
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                'name'=>$this->input->post('name'),
                'sacco_id'=>$this->input->post('sacco_id'),
                'code'=>$this->input->post('code'),
                'active'=>1,
                'created_on'=>time(),
                'created_by'=>$this->ion_auth->get_user()->id
            );
            $id = $this->sacco_branches_m->insert($data);
            if($id){
                $this->session->set_flashdata('success','Sacco branch created successfully');
            }else{
                $this->session->set_flashdata('error','Sacco branch could not be created');
            }
            if($this->input->post('new_item')){
                redirect('admin/sacco_branches/create','refresh');
            }else{
                redirect('admin/sacco_branches/listing');
            }
        }else{
        	foreach ($this->validation_rules as $key => $field){
                $post->$field['field'] = set_value($field['field']);
            }
        }
        $data['id'] = '';
        $data['post'] = $post;
        $data['sacco_id'] = $sacco_id;
        $data['sacco_options'] = $this->saccos_m->get_admin_sacco_options();
        $this->template->title('Create Sacco Branch')->build('admin/form',$data);
    }

    public function listing(){
    	$data = array();
    	$post = '';
    	$sacco_id = $this->input->get('sacco_id');
        $total_rows = $this->sacco_branches_m->count_all($sacco_id);
        $pagination = create_pagination('admin/sacco_branches/listing/pages', $total_rows,50,5,TRUE);
    	$data['posts'] = $this->sacco_branches_m->limit($pagination['limit'])->get_all($sacco_id);
    	if($sacco_id){
    		$post = $this->saccos_m->get($sacco_id);
    	}
    	$data['post'] = $post;
        $data['pagination'] = $pagination;
        $data['sacco_options'] = $this->saccos_m->get_admin_sacco_options();
        $this->template->title('List Sacco Branches')->build('admin/listing',$data);
    }

    public function edit($id = 0){
        $id OR redirect('admin/sacco_branches/listing');
        $data = array();
        $post = new stdClass();  
        $post = $this->sacco_branches_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the sacco branches does not exist');
            redirect('admin/sacco_branches/listing');
        } 
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run()){
            $data = array(
                'name'=>$this->input->post('name'),
                'sacco_id'=>$this->input->post('sacco_id'),
                'code'=>$this->input->post('code'),
                'modified_on'=>time(),
                'modified_by'=>$this->ion_auth->get_user()->id
            );
            $result = $this->sacco_branches_m->update($id,$data);
            if($result){
                $this->session->set_flashdata('success','Sacco branches changes saved successfully');
            }else{
                $this->session->set_flashdata('error','Changes could not be saved');
            }
            if($this->input->post('new_item')){
                redirect('admin/sacco_branches/create','refresh');
            }else{
                redirect('admin/sacco_branches/edit/'.$id);
            }
        }else{

        }
        $data['id'] = $id;
        $data['post'] = $post;
        $data['sacco_options'] = $this->saccos_m->get_admin_sacco_options();
        $this->template->title('Edit Sacco Branches')->build('admin/form',$data);
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
        }else if($action == 'bulk_delete'){
            for($i=0;$i<count($action_to);$i++){
                $this->delete($action_to[$i],FALSE);
            }
        }
        redirect('admin/sacco_branches/listing');
    }

    function hide($id = 0,$redirect= TRUE){
        $id OR redirect('admin/sacco_branches/listing');
        $post = $this->sacco_branches_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Sacco branch does not exist');
            redirect('admin/sacco_branches/listing');
        }
        $result = $this->sacco_branches_m->update($post->id,array('active'=>0,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Sacco branches were successfully hidden');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Sacco branch');
        }

        if($redirect){
            redirect('admin/sacco_branches/listing');
        }
        return TRUE;
    }

    function activate($id = 0,$redirect= TRUE){
        $id OR redirect('admin/sacco_branches/listing');
        $post = $this->sacco_branches_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Sacco branch does not exist');
            redirect('admin/sacco_branches/listing');
        }

        $result = $this->sacco_branches_m->update($post->id,array('active'=>1,'modified_on'=>time(),'modified_by'=>$this->ion_auth->get_user()->id));

        if($result){
            $this->session->set_flashdata('success','Sacco branches were successfully activated');
        }else{
            $this->session->set_flashdata('error','Unable to hide '.$post->name.' Sacco branches');
        }

        if($redirect){
            redirect('admin/sacco_branches/listing');
        }
        return TRUE;
    }

    function delete($id = 0,$redirect= TRUE){
        $id OR redirect('admin/sacco_branches/listing');
        $post = $this->sacco_branches_m->get($id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry, the Sacco branch does not exist');
            redirect('admin/sacco_branches/listing');
        }

        $result = $this->sacco_branches_m->delete($post->id);

        if($result){
            $this->session->set_flashdata('success','Sacco branches were successfully deleted');
        }else{
            $this->session->set_flashdata('error','Unable to delete '.$post->name.' Sacco branches');
        }

        if($redirect){
            redirect('admin/sacco_branches/listing');
        }
        return TRUE;
    }

    function import($sacco_id = 0){
    	$sacco_id OR redirect('admin/sacco_branches/listing');
    	$post = $this->saccos_m->get($sacco_id);
        if(!$post){
            $this->session->set_flashdata('error','Sorry the sacco does not exist');
            redirect('admin/sacco_branches/listing');
        } 
    	$data = array();
    	if($this->input->post('import')){
	        $config['upload_path'] = FCPATH . 'uploads/files/csvs/';
	        $config['allowed_types'] = 'xls|xlsx|csv';
	        $config['max_size'] = '1024';
	        $this->load->library('upload',$config);
	        if($this->upload->do_upload('branch_list_file')){
	        	$upload_data = $this->upload->data();
	        	$file_path = $upload_data['full_path'];
        		$this->load->library('Excel');
        		$excel_sheet = new PHPExcel();
		        if(file_exists($file_path)){
		        	$file_type = PHPExcel_IOFactory::identify($file_path);
                	$excel_reader = PHPExcel_IOFactory::createReader($file_type);
                	$excel_book = $excel_reader->load($file_path);
                	$sheet = $excel_book->getSheet(0);
                	$allowed_column_headers = array('Branch Name','Code');
                	$count = count($allowed_column_headers)-1;
                	for($column = 0; $column <= $count; $column++){
                    	$value = $sheet->getCellByColumnAndRow($column, 1)->getValue();
	                    if(in_array(trim($value), $allowed_column_headers)){
	                        $column_validation = true;
	                    }else{
	                        $column_validation = false;
	                        break;
	                    }
	                }

	                if($column_validation){
	                	$branches = array();
                    	$highestRow = $sheet->getHighestRow();
                    	$branches = array();
                    	for($row = 2; $row <= $highestRow; $row++){
                    		$branch_name = '';
                    		$code = '';
	                        for($column = 0; $column <= $count; $column++){
	                        	if($column == 0){
                                	$branch_name = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                            	}else if($column == 1){
                                	$code = $sheet->getCellByColumnAndRow($column,$row)->getValue();
                            	}
	                        }
	                        $branches[] = array(
	                        	'branch_name' => $branch_name,
	                        	'code' => $code,
	                        );
	                    }

	                    if(empty($branches)){
	                    	$this->session->set_flashdata('info','Branch list file does not have any branches to import');
	                    }else{
	                    	$sacco_branch_options = $this->sacco_branches_m->get_sacco_branch_options_by_sacco_id($sacco_id);
	                    	$branches = (object)$branches;
	                    	$successes = 0;
	                    	$duplicates = 0;
	                    	$ignores = 0;
	                    	$errors = 0;

	                    	foreach($branches as $branch){
	                    		$branch = (object)$branch;
	                    		if($branch->branch_name&&$branch->code){
	                    			if(in_array($branch->branch_name,$sacco_branch_options)){
	                    				$duplicates++;
	                    			}else{
		                    			$data = array(
											'name'=>$branch->branch_name,
		            						'sacco_id'=>$sacco_id,
		            						'code'=>$branch->code,
		            						'active'=>1,
		            						'created_on'=>time(),
		            						'created_by'=>$this->ion_auth->get_user()->id
		                    			);
		                    			if($result = $this->sacco_branches_m->insert($data)){
		                    				$sacco_branch_options[$result] = $branch->branch_name;
		                    				$successes++;
		                    			}else{
		                    				$errors++;
		                    			}
									}
	                    		}else{
	                    			$ignores++;
	                    		}
	                    	}

	                    	if($successes){
	                    		$this->session->set_flashdata('success',$successes.' branches imported successfully.');
	                    	}

	                    	if($errors){
	                    		$this->session->set_flashdata('errors',$errors.' occurred during import.');
	                    	}

	                    	if($duplicates){
	                    		$this->session->set_flashdata('info',$duplicates.' duplicates were found during import and ignored.');
	                    	}

	                    	redirect('admin/sacco_branches/listing/?sacco_id='.$sacco_id);
	                    }
	                }else{
	        			$this->session->set_flashdata('error','Branch list file does not have the correct format');
	                }
		        }else{
	        		$this->session->set_flashdata('error','Branch list file was not found');
		        }
	        }else{
	        	$this->session->set_flashdata('error','Branch list file type is not allowed');
	        }
    	}
    	$data['post'] = $post;
        $this->template->title('Import Sacco Branches')->build('admin/import',$data);
    }
}

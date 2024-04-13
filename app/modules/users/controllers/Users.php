<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Public_Controller{

  public function __construct(){
    parent::__construct();
    $this->load->model('users/users_m');
  }

  public function create_avatar_thumbnail(){
    $this->load->library('image_lib');
    ini_set('memory_limit','256M');
    $users = $this->users_m->get_all_users();
    $users_with_avatar = 0;
    foreach($users as $user):
      if($user->avatar){
        if(is_file(FCPATH.'uploads/groups/'.$user->avatar)){
          $config['image_library'] = 'gd2';
          $config['source_image'] = FCPATH.'uploads/groups/'.$user->avatar;
          $config['create_thumb'] = FALSE;
          $config['maintain_ratio'] = TRUE;
          $config['width'] = 100;
          $config['height'] = 100;
          $this->image_lib->clear();
          $this->image_lib->initialize($config);
          if($this->image_lib->resize()){
            $users_with_avatar++;
          }else{
            echo "Resize Failed.<br/>";
          }
        }
      }
    endforeach;
    echo $users_with_avatar;
  }

  function update_user_user_groups(){
    $users = $this->users_m->get_all_users();
    foreach ($users as $user) {
      if($this->ion_auth->is_in_group($user->id,2) || $this->ion_auth->is_in_group($user->id,1)){

      }else{
        $this->ion_auth->add_to_group(array(2),$user->id);
      }
    }
  }

  function user_registration_by_year(){
    echo json_encode($this->users_m->user_registration_by_year());
  }

  function group_registration_by_year(){
    echo json_encode($this->groups_m->group_registration_by_year());
  }

}

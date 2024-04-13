<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{

    protected $data = array();
	
	function __construct(){
        parent::__construct();
        set_time_limit(0);
        ini_set('memory_limit','2048M');
        ini_set('max_execution_time', 1200);
        $this->load->model('menus/menus_m');
    }


    function ajax_get_menus($menu_key = ''){
        //consider unnacceptable slugs
        //empty slug redir to dashboard
        $menu = $this->menus_m->get_link_by_language_key($menu_key);
        if($menu){
            $sub_menus = $this->menus_m->get_children_links($menu->id);
            $sub_menus_with_children = array();
            foreach ($sub_menus as $key => $value) {
                if($this->menus_m->has_children($value->id)){
                    $sub_menus_with_children[$value->id] = $value->id;
                }
            }
            $notification_counts = array(
                'UNRECONCILED_DEPOSITS_COUNT' => isset($this->unreconciled_deposits_count)?$this->unreconciled_deposits_count:'',
                'UNRECONCILED_WITHDRAWALS_COUNT' => isset($this->unreconciled_withdrawals_count)?$this->unreconciled_withdrawals_count:'',
                'WITHDRAWAL_TASKS_COUNT' => isset($this->withdrawal_tasks_count)?$this->withdrawal_tasks_count:'',
                'ACTIVE_LOAN_APPLICATIONS' => isset($this->active_loan_applications)?$this->active_loan_applications:'',
                'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT' => isset($this->pending_withdrawal_approval_requests_count)?$this->pending_withdrawal_approval_requests_count:'',
            );
            // foreach ($sub_menus as $key => $value) {
            //     echo '<div class="col-md-3">
            //         <div class="m-portlet" style="-webkit-box-shadow: 0px 0px 0px 0px rgba(0,0,0,0.0)!important;box-shadow: 0px 0px 0 0px rgba(0,0,0,0.0)!important;">
            //             <div class="m-portlet__body m--align-center">
            //                 <a href="';
            //                 echo isset($sub_menus_with_children[$value->id])?'#':site_url($value->url);
            //                 echo '" id="'.$value->id.'"';
            //                 echo isset($sub_menus_with_children[$value->id])?'onclick="toggle_menu_click('.$value->id.')"':'';
            //                 echo 'style="text-decoration: none!important;" class="link">

            //                     <div class=""><i class="'.$value->icon.'" style="font-size:50px;"></i></div>
            //                     <h5>';
            //                         foreach ($notification_counts as $k => $v){
            //                             $count_class = "";
            //                             if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
            //                                 $count_class = "deposits_count";
            //                             }else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
            //                                 $count_class = "withdrawals_count";
            //                             }
            //                             if($v){
            //                                 $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $value->name);
            //                                 if($name!==$value->name){
            //                                     break;
            //                                 }
            //                             }else{
            //                                 $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info d-none '.$count_class.' ">'.$v.'</span>', $value->name);
            //                                 if($name!==$value->name){
            //                                     break;
            //                                 }
            //                             }
            //                         }
            //                         echo $name;
            //                         echo '</h5>
            //                                 <p class="mb-0" style="color: #000;">';
            //                         echo (isset($value->description)?$value->description:'Lorem Ipsum is simply dummy text of the printing and typesetting industry').'</p>';
            //                         echo isset($sub_menus_with_children[$value->id])?'<i class="la la-angle-double-right"></i>':'';
            //                         echo '
            //                 </a>
            //             </div>
            //         </div>
            //     </div>';
            // } 
            
            foreach ($sub_menus as $key => $value) {
            ?>
            <div class="col-md-4">
                <a href="<?php echo isset($sub_menus_with_children[$value->id])?'#':site_url($value->url); ?>">
                    <div class="menu_item">
                        <div class="menu_img">
                            <i class="img <?php echo $value->icon; ?>"></i>
                        </div>
                        <div class="menu_cont">
                            <div class="menu_cont_hdr">
                                <div class="overflow_text">
                                    <?php 
                                    foreach ($notification_counts as $k => $v){
                                        $count_class = "";
                                        if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
                                            $count_class = "deposits_count";
                                        }else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
                                            $count_class = "withdrawals_count";
                                        }
                                        if($v){
                                            $name = preg_replace('/\['.$k.'\]/', '<span class="menu_cont_notif_count m-badge m-badge--success '.$count_class.' ">'.$v.'</span>', $value->name);
                                            if($name!==$value->name){
                                                break;
                                            }
                                        }else{
                                            $name = preg_replace('/\['.$k.'\]/', '<span class="menu_cont_notif_count m-badge m-badge--success d-none '.$count_class.' ">'.$v.'</span>', $value->name);
                                            if($name!==$value->name){
                                                break;
                                            }
                                        }
                                    }
                                    echo $name; ?>
                                </div>
                            </div>
                            <div class="menu_cont_descr">
                                <span><?php echo (isset($value->description)?$value->description:'Lorem ipsum dolor sit amet consectetur adipisicing elit. Possimus laudantium, consectetur molestiae obcaecati mollitia provident dolores natus'); ?> </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php }   
        }
    }

    function ajax_get_submenus($menu_id = ''){
        //consider unnacceptable slugs
        //empty slug redir to dashboard
        $menu = $this->menus_m->get($menu_id);
        $parent_menu = $this->menus_m->get($menu->parent_id);
        if($menu){
            $sub_menus = $this->menus_m->get_children_links($menu_id);
            $sub_menus_with_children = array();
            foreach ($sub_menus as $key => $value) {
                if($this->menus_m->has_children($value->id)){
                    $sub_menus_with_children[$value->id] = $value->id;
                }
            }
            $notification_counts = array(
                'UNRECONCILED_DEPOSITS_COUNT' => isset($this->unreconciled_deposits_count)?$this->unreconciled_deposits_count:'',
                'UNRECONCILED_WITHDRAWALS_COUNT' => isset($this->unreconciled_withdrawals_count)?$this->unreconciled_withdrawals_count:'',
                'WITHDRAWAL_TASKS_COUNT' => isset($this->withdrawal_tasks_count)?$this->withdrawal_tasks_count:'',
                'ACTIVE_LOAN_APPLICATIONS' => isset($this->active_loan_applications)?$this->active_loan_applications:'',
                'PENDING_WITHDRAWAL_APPROVAL_REQUESTS_COUNT' => isset($this->pending_withdrawal_approval_requests_count)?$this->pending_withdrawal_approval_requests_count:'',
            );
            echo '
                <div class="col-md-12">
                    <button type="button" class="btn m-btn--square btn-outline-info btn-sm m-btn m-btn--custom" onclick="toggle_menu_click('.$parent_menu->id.')"><i class="la la-arrow-left"></i> Back to '.$parent_menu->name.
                    '</button>
                </div>';
            foreach ($sub_menus as $key => $value) {
                echo '<div class="col-md-3">
                    <div class="m-portlet" style="-webkit-box-shadow: 0px 0px 0px 0px rgba(0,0,0,0.0)!important;box-shadow: 0px 0px 0 0px rgba(0,0,0,0.0)!important;">
                        <div class="m-portlet__body m--align-center">
                            <a href="';
                            echo isset($sub_menus_with_children[$value->id])?'#':site_url($value->url);
                            echo '" id="'.$value->id.'"';
                            echo isset($sub_menus_with_children[$value->id])?'onclick="toggle_menu_click('.$value->id.')"':'';
                            echo 'style="text-decoration: none!important;" class="link">

                                <div class=""><i class="'.$value->icon.'" style="font-size:50px;"></i></div>
                                <h5>';
                                    foreach ($notification_counts as $k => $v){
                                        $count_class = "";
                                        if($k == 'UNRECONCILED_DEPOSITS_COUNT'){
                                            $count_class = "deposits_count";
                                        }else if($k == 'UNRECONCILED_WITHDRAWALS_COUNT'){
                                            $count_class = "withdrawals_count";
                                        }
                                        if($v){
                                            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info '.$count_class.' ">'.$v.'</span>', $value->name);
                                            if($name!==$value->name){
                                                break;
                                            }
                                        }else{
                                            $name = preg_replace('/\['.$k.'\]/', '<span class="badge badge-info d-none '.$count_class.' ">'.$v.'</span>', $value->name);
                                            if($name!==$value->name){
                                                break;
                                            }
                                        }
                                    }
                                    echo $name;
                                    echo '</h5>
                                            <p class="mb-0" style="color: #000;">';
                                    echo isset($value->description)?$value->description:'Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>';
                                    echo isset($sub_menus_with_children[$value->id])?'<i class="la la-angle-double-right"></i>':'';
                                    echo '
                            </a>
                        </div>
                    </div>
                </div>';
            }      
        }
    }

}
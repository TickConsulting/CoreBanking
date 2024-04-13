<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->model('notifications_m');
    }

    function get_notifications_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'from' => $from,
            'to' => $to,
            'is_read' => $this->input->get('is_read'),
        );
        $total_rows = $this->notifications_m->count_member_notifications($filter_parameters);
        $pagination = create_pagination('group/notifications/listing/pages',$total_rows,50,5,TRUE);
        $posts = $this->notifications_m->limit($pagination['limit'])->get_member_notifications($filter_parameters);
        if(!empty($posts)){
            echo form_open('group/notifications/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                echo '
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Notifications</p>';
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
                                '.translate('Notification Details').'
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        foreach($posts as $post):
                            echo '
                            <tr ';
                            echo $post->is_read==0?"class='unread'":''; 
                            echo '
                            >
                                <td>
                                    <label class="m-checkbox">
                                        <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>
                                    <a href="'.site_url($post->call_to_action_link).'" class="notification-item">';
                                        echo '<span class="font-dark">'.$post->message.'</span></br>';
                                        echo '<small>'.timestamp_to_datetime($post->created_on).' - <cite>'.timestamp_to_time_elapsed($post->created_on).'</cite></small>'; 
                                    echo '
                                    </a>
                                </td> 
                                <td>';
                                    if($post->is_read==0){
                                        echo '
                                            <a href="'.site_url('member/notifications/mark_as_read/'.$post->id).'" class="btn btn-sm btn-primary m-btn m-btn--icon action_button">
                                                <span>
                                                    <i class="la la-eye"></i>
                                                    <span>
                                                        '.translate('Mark as Read').' &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>
                                        ';
                                    }else{
                                        echo '
                                            <a href="'.site_url('member/notifications/mark_as_unread/'.$post->id).'" class="btn btn-sm btn-danger m-btn m-btn--icon action_button">
                                                <span>
                                                    <i class="la la-eye-slash"></i>
                                                    <span>
                                                        '.translate('Mark as Unread').' &nbsp;&nbsp; 
                                                    </span>
                                                </span>
                                            </a>
                                        ';
                                    }
                                echo '
                                </td>
                            </tr>';
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
                    <button class="btn btn-sm btn-primary confirmation_bulk_action" name=\'btnAction\' value=\'bulk_mark_as_read\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-eye\'></i> '.translate('Bulk Mark As Read').'</button>
                    <button class="btn btn-sm btn-danger confirmation_bulk_action" name=\'btnAction\' value=\'bulk_mark_as_unread\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-eye-slash\'></i> '.translate('Bulk Mark As Unread').'</button>';
                endif;
            echo form_close();
        }else{
            echo '
                <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
                    '.translate('Oooops').'!'.translate('Looks like you have no notifications to display').'.                       
                </div>
            ';
        } 
    }

    function get_group_notifications_listing(){
        $from = strtotime($this->input->get('from'))?:'';
        $to = strtotime($this->input->get('to'))?:'';
        $filter_parameters = array(
            'from' => $from,
            'to' => $to,
            'is_read' => $this->input->get('is_read'),
        );
        $total_rows = $this->notifications_m->count_member_notifications($filter_parameters);
        $pagination = create_pagination('group/notifications/listing/pages',$total_rows,50,5,TRUE);
        $posts = $this->notifications_m->limit($pagination['limit'])->get_member_notifications($filter_parameters);
        if(!empty($posts)){
            echo form_open('group/notifications/action', ' id="form"  class="form-horizontal"');
                if ( ! empty($pagination['links'])):
                echo '
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Notifications</p>';
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                endif; 
                echo '  
                <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">
                    <thead>
                        <tr>
                            <th width=\'2%\'>
                                 <input type="checkbox" name="check" value="all" class="check_all">
                            </th>
                            <th>
                                Notification Details
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>';
                        foreach($posts as $post):
                            echo '
                            <tr ';
                            echo $post->is_read==0?"class='unread'":''; 
                            echo '
                            >
                                <td><input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" /></td>
                                <td>
                                    <a href="'.site_url($post->call_to_action_link).'">';
                                        echo '<span class="font-dark">'.$post->message.'</span></br>';
                                        echo '<small>'.timestamp_to_datetime($post->created_on).' - <cite>'.timestamp_to_time_elapsed($post->created_on).'</cite></small>'; 
                                    echo '
                                    </a>
                                </td> 
                                <td>';
                                    if($post->is_read==0){
                                        echo '
                                        <a href="'.site_url('group/notifications/mark_as_read/'.$post->id).'" class="btn btn-xs default">
                                            <i class="fa fa-check"></i> '.translate('Mark as Read').' &nbsp;&nbsp; 
                                        </a>';
                                    }else{
                                        echo '
                                        <a href="'.site_url('group/notifications/mark_as_unread/'.$post->id).'" class="btn btn-xs default">
                                            <i class="fa fa-remove"></i> '.translate('Mark as Unread').' &nbsp;&nbsp; 
                                        </a>';
                                    }
                                echo '
                                </td>
                            </tr>';
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
                    <button class="btn btn-sm btn-default confirmation_bulk_action" name=\'btnAction\' value=\'bulk_mark_as_read\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-eye\'></i> '.translate('Bulk Mark As Read').'</button>
                    <button class="btn btn-sm btn-default confirmation_bulk_action" name=\'btnAction\' value=\'bulk_mark_as_unread\' data-toggle="confirmation" data-placement="top"> <i class=\'fa fa-eye-slash\'></i> '.translate('Bulk Mark As Unread').'</button>';
                endif;
            echo form_close();
        }else{
            echo '
            <div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No notifications to display.
                </p>
            </div>';
        } 
    }


}

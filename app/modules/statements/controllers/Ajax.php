<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends Ajax_Controller{
	
	protected $data = array();
    
    function __construct(){
        parent::__construct();
        $this->load->model('statements_m');
        $this->load->model('accounts/accounts_m');
        $this->load->model('members/members_m');
        $this->load->model('contributions/contributions_m');
        $this->load->model('fine_categories/fine_categories_m');
        $this->load->library('transactions');
    }
    
    function get_statements_listing(){
        $filter_parameters = array(
            'member_id' => $this->input->get('member_id'),
        );
        $total_rows = $this->members_m->count_active_group_members($this->group->id,$filter_parameters);
        $pagination = create_pagination('group/members/listing/pages',$total_rows,50,5,TRUE);
        $posts = $this->members_m->limit($pagination['limit'])->get_active_group_members($this->group->id,$filter_parameters);
        if(!empty($posts)){ 
            if ( ! empty($pagination['links'])):
                echo '
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Members</p>';
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
                        <th width=\'2%\'>
                            #
                        </th>
                        <th>
                            '.translate('Name').'
                        </th>';
                        if($this->show_membership_number){
                            echo '<th>Membership Number</th>';
                        }
                        echo '
                        <th width="70%">
                            '.translate('Actions').'
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    $i = $this->uri->segment(5, 0); 
                    foreach($posts as $post):
                        echo '
                            <tr>
                                <td scope="row">
                                    <label class="m-checkbox">
                                        <input name=\'action_to[]\' type="checkbox" class="checkboxes" value="'.$post->id.'" />
                                        <span></span>
                                    </label>
                                </td>
                                <td>'.($i+1).'</td>
                                <td>'.$post->first_name.' '.$post->last_name.'</td>';
                                if($this->show_membership_number){
                                    echo '<td>'.$this->membership_numbers[$post->id].'</td>';
                                }
                                echo '
                                <td>
                                    <a href="'.site_url('group/statements/view/'.$post->id).'" class="btn btn-sm btn-info blue btn-xs default">
                                        <i class="fa fa-eye"></i> &nbsp;'.translate('Contribution Statement').' &nbsp;&nbsp; 
                                    </a>
                                    <a href="'.site_url('group/statements/fine_statement/'.$post->id).'" class="btn btn-sm btn-danger red btn-xs default">
                                        <i class="fa fa-eye"></i>&nbsp;'.translate('Fine Statement').' &nbsp;&nbsp; 
                                    </a>  

                                    <a href="'.site_url('group/statements/miscellaneous_statement/'.$post->id).'" class="btn btn-sm btn-success btn-xs default">
                                        <i class="fa fa-eye"></i>'.translate('Miscellaneous Statement').' &nbsp;&nbsp; 
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
        }else{ 
            echo '
            <div class="alert alert-info">
                <h4 class="block">Information! No records to display</h4>
                <p>
                    No Members to display.
                </p>
            </div>';
        }
    }


}
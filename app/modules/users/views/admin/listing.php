<style type="text/css">
    td>ul>li>a{
        color:#000;
    }
    td>ul>li>a:hover{
        text-decoration: none;
    }
    td>ul{
        padding-left: 15px !important;
    }
</style>
<div class="row">

	<div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
               	<div class="caption">
                    <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">
                <div class="row">
                    <div class="col-md-12"> 
                        <div class="btn-group  margin-bottom-20 search-button hold-on-click">
                            <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> Search
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">
                                <?php echo form_open(current_url(),'method="GET" class="filter"');?>
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label>User's Name</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                <i class="fa fa-book"></i>
                                                </span>
                                                <?php echo form_input('name',$this->input->get('name'),'class="form-control input-sm" placeholder="User\'s Name"'); ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Users Email/phone number</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                <i class="fa fa-book"></i>
                                                </span>
                                                <?php echo form_input('identity',$this->input->get('identity'),'class="form-control input-sm" placeholder="Email address or Phone number"'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>User Group</label>
                                            <div class="input-group">
                                                <div class="input-group">
                                                    <?php echo form_dropdown('user_group',array(''=>'--All user groups--')+$groups,$this->input->get('user_group'),'class="form-control input-sm select2" placeholder="Select user group"'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Investment Group Options</label>
                                            <div class="input-group">
                                                <div class="input-group">
                                                    <?php echo form_dropdown('group_option[]',$group_options,$this->input->get('group_option'),'class="form-control input-sm select2-multiple" multiple="multiple" placeholder="Select user group"'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-search"></i></button>
                                        <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                        <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                                    </div>
                                <?php echo form_close();?>
                            </div>
                        </div>
                        <?php 
                            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
                            echo '
                            <div class="btn-group margin-bottom-20 search-button">
                                <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.current_url().$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Deposits">
                                    Export To Excel <i class="fa fa-file-excel-o"></i>
                                </a>
                            </div>';
                        ?>

                        <?php 
                            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
                            echo '
                            <div class="btn-group margin-bottom-20 search-button">
                                <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.site_url('admin/users/export_users').$query.'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Deposits">
                                    Export Users Localy <i class="fa fa-download"></i>
                                </a>
                            </div>';
                        ?>

                        <?php 
                            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
                            echo '
                            <div class="btn-group margin-bottom-20 search-button">
                                <a class="btn btn-primary btn-sm  generate_excel_document_button" type="button" href="'.site_url('admin/users/import_users').'" data-toggle="modal" data-content="#generate_excel_deposit_list" data-title="Generating Excel Document For Group Deposits">
                                    Import Users <i class="fa fa-upload"></i>
                                </a>
                            </div>';
                        ?>
                    </div>
                </div>

                <div class="clearfix"></div>


				<?php if(!empty($posts)){ ?>
			        <?php echo form_open('admin/users/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Users</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?> 
                        
			             <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed table-searchable">
                            <thead>
                                <tr>
                                    <th width='2%'>
                                         <input type="checkbox" name="check" value="all" class="check_all">
                                    </th>
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        User Name
                                    </th>
                                    <th>
                                        Contact
                                    </th>
                                    <th>
                                        User Groups
                                    </th>
                                    <th>
                                        Investment Groups
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th width="">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=$this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo $post->first_name.' '.$post->middle_name.' '.$post->last_name; ?></td>
                                        <td><?php echo $post->phone.'<br/>'.$post->email; ?></td>
                                        <td>
                                            <?php $user_groups = $this->ion_auth->get_user_groups($post->id);
                                                foreach ($user_groups as $user_group) {
                                                    echo '<span class="label label-sm label-success">'.$groups[$user_group].'</span>';
                                                    echo '&nbsp;&nbsp;';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                $investment_groups = $this->groups_m->get_groups_for_user($post->id);
                                                echo '<ul>';
                                                foreach ($investment_groups as $group){
                                                   echo '<li><a href="'.site_url('admin/groups/view/'.$group->group_id).'">'.$group->name;
                                                   echo '</a></li>';
                                                }
                                                echo '</ul>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if($post->active)
                                            {
                                                echo '<span class="label label-sm label-primary"> Active</spam>';
                                            }else{
                                                echo '<span class="label label-sm label-danger"> Inactive</spam>';

                                                }?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('admin/users/edit/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            <?php if($post->active):?>
                                                <a href="<?php echo site_url('admin/users/disable/'.$post->id); ?>" class="btn btn-xs btn-default confirmation_link">
                                                    <i class="fa fa-close"></i> Disable &nbsp;&nbsp; 
                                                </a>
                                            <?php else:?>
                                            <a href="<?php echo site_url('admin/users/activate/'.$post->id); ?>" class="btn btn-xs btn-primary confirmation_link">
                                                <i class="icon-check"></i> Activate &nbsp;&nbsp; 
                                            </a>
                                        <?php endif;?>
                                        </td>
                                    </tr>
                                    <?php $i++;
                                    endforeach; ?>
                            </tbody>
                        </table>

                        <div class="clearfix"></div>
                        <div class="row col-md-12">
                        <?php 
                            if( ! empty($pagination['links'])): 
                            echo $pagination['links']; 
                            endif; 
                        ?>  
                        </div>
                        <div class="clearfix"></div>

			            <div class="clearfix"></div>
			            <?php if($posts):?>
                            <button class="btn btn-sm btn-primary confirmation_bulk_action" name='btnAction' value='bulk_activate' data-toggle="confirmation" data-placement="top"> <i class='icon-check'></i> Bulk Activate</button>
			                <button class="btn btn-sm btn-default confirmation_bulk_action" name='btnAction' value='bulk_disable' data-toggle="confirmation" data-placement="top"> <i class='fa fa-close'></i> Bulk Disable</button>

			            <?php endif;?>
			        <?php echo form_close(); ?>
			    <?php }else{ ?>
			        <div class="alert alert-info">
			            <h4 class="block">Information! No records to display</h4>
			            <p>
			                No User records to display.
			            </p>
			        </div>
			    <?php } ?>

			</div>

		</div>


	</div>

</div>
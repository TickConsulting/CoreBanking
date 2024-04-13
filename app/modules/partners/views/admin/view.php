<div class="row">
    <div class="col-md-12">
        <div class="portlet light portlet-fit bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-green bold uppercase">                   
                        <?php echo $this->admin_menus_m->generate_page_title();?>
                    </span>
                    <div class="caption-desc font-grey-cascade"><?php echo $post->name; ?> Partner Profile | <?php echo $this->application_settings->protocol.$this->application_settings->url.'/signup/'.$post->slug; ?> </div>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body">
                <div class="mt-element-list">
                    <div class="mt-list-head list-todo red">
                        <div class="list-head-title-container">
                            <h3 class="list-title">Partner Profile Details</h3>
                            <div class="list-head-count">
                                <div class="list-head-count-item">
                                    <i class="fa fa-users"></i> Partner Users: <?php echo $user_count; ?> </div>
                                <div class="list-head-count-item">
                                    <i class="fa fa-hand-paper-o"></i> Groups: <?php echo $group_count; ?></div>
                                <div class="list-head-count-item">
                                    <i class="fa fa-link"></i> Sign Up Link: <?php echo $this->application_settings->protocol.$this->application_settings->url.'/signup/'.$post->slug; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-list-container list-todo">
                        <div class="list-todo-line"></div>
                        <ul>
                            <li class="mt-list-item">
                                <div class="list-todo-icon bg-white">
                                    <i class="fa fa-info"></i>
                                </div>
                                <div class="list-todo-item dark">
                                    <a class="list-toggle-container" data-toggle="collapse" href="#task-1" aria-expanded="false">
                                        <div class="list-toggle done uppercase">
                                            <div class="list-toggle-title bold">User Information</div>
                                            <div class="badge badge-default pull-right bold"></div>
                                        </div>
                                    </a>
                                    <div class="task-list panel-collapse collapse in" id="task-1">
                                        <ul>
                                            <li class="task-list-item done">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-users"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;"><?php echo $post->name; ?> Users</a>
                                                    </h4>
                                                    <div class="table-scrollable margin-top-10">
                                                        <table class="table table-hover table-light table-condensed table-striped table-bordered">
                                                            <thead>
                                                                <tr class="">
                                                                    <th width="8px">#</th>
                                                                    <th> Name </th>
                                                                    <th> Phone </th>
                                                                    <th> Email </th>
                                                                </tr>
                                                            </thead>
                                                            <?php $count = 1; foreach($users as $user): ?>
                                                                <tr>
                                                                    <td> <?php echo $count++ ?> </td>
                                                                    <td> <?php echo $user->first_name." ".$user->last_name; ?> </td>
                                                                    <td> <?php echo $user->phone; ?> </td>
                                                                    <td> <?php echo $user->email; ?> </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </table>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="task-footer bg-grey">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <a class="task-trash" href="<?php echo site_url("admin/groups/edit/".$post->id); ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </div>                                                
                                                <div class="col-xs-6">
                                                    <a class="task-trash" target="_blank" href="<?php echo $this->application_settings->protocol.$post->slug.'.'.$this->application_settings->url; ?>">
                                                        <i class="fa fa-user-secret"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="mt-list-item">
                                <div class="list-todo-icon bg-white">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="list-todo-item dark">
                                    <a class="list-toggle-container" data-toggle="collapse" href="#task-2" aria-expanded="false">
                                        <div class="list-toggle done uppercase">
                                            <div class="list-toggle-title bold">Groups</div>
                                            <div class="badge badge-default pull-right bold"><?php echo $group_count; ?></div>
                                        </div>
                                    </a>
                                    <div class="task-list panel-collapse collapse" id="task-2">
                                        <ul>
                                            <li class="task-list-item done">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-list-alt"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;">Groups List</a>
                                                    </h4>
                                                    <p>
                                                    <div class="table-scrollable margin-top-10">
                                                        <table class="table table-hover table-light table-condensed table-striped table-bordered">
                                                            <thead>
                                                                <tr class="">
                                                                    <th width="8px">#</th>
                                                                    <th> Sign Up Date </th>
                                                                    <th> Name </th>
                                                                </tr>
                                                            </thead>
                                                            <?php $count = 1; foreach($groups as $group): ?>
                                                                <tr>
                                                                    <td> <?php echo $count++ ?> </td>
                                                                    <td> <?php echo timestamp_to_date($group->created_on); ?> </td>
                                                                    <td> <?php echo $group->name; ?> </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </table>
                                                    </div>

                                                    </p>
                                                </div>
                                            </li>
                                        </ul>

                                        <div class="task-footer bg-grey">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <a class="task-trash" href="<?php echo site_url("admin/groups/edit/".$post->id); ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </div>                                                
                                                <div class="col-xs-6">
                                                    <a class="task-trash" target="_blank" href="<?php echo $this->application_settings->protocol.$post->slug.'.'.$this->application_settings->url; ?>">
                                                        <i class="fa fa-user-secret"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="mt-list-item">
                                <div class="list-todo-icon bg-white">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="list-todo-item dark">
                                    <a class="list-toggle-container" data-toggle="collapse" href="#task-2" aria-expanded="false">
                                        <div class="list-toggle done uppercase">
                                            <div class="list-toggle-title bold">Commission Matrix+</div>
                                            <div class="badge badge-default pull-right bold"><?php echo $group_count; ?></div>
                                        </div>
                                    </a>
                                    <div class="task-list panel-collapse collapse" id="task-2">
                                        <ul>
                                            <li class="task-list-item done">
                                                <div class="task-icon">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-list-alt"></i>
                                                    </a>
                                                </div>
                                                <div class="task-content">
                                                    <h4 class="uppercase bold">
                                                        <a href="javascript:;">Groups List</a>
                                                    </h4>
                                                    <p>
                                                    <div class="table-scrollable margin-top-10">
                                                        <table class="table table-hover table-light table-condensed table-striped table-bordered">
                                                            <thead>
                                                                <tr class="">
                                                                    <th width="8px">#</th>
                                                                    <th> Sign Up Date </th>
                                                                    <th> Name </th>
                                                                </tr>
                                                            </thead>
                                                            <?php $count = 1; foreach($groups as $group): ?>
                                                                <tr>
                                                                    <td> <?php echo $count++ ?> </td>
                                                                    <td> <?php echo timestamp_to_date($group->created_on); ?> </td>
                                                                    <td> <?php echo $group->name; ?> </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </table>
                                                    </div>

                                                    </p>
                                                </div>
                                            </li>
                                        </ul>

                                        <div class="task-footer bg-grey">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <a class="task-trash" href="<?php echo site_url("admin/groups/edit/".$post->id); ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </div>                                                
                                                <div class="col-xs-6">
                                                    <a class="task-trash" target="_blank" href="<?php echo $this->application_settings->protocol.$post->slug.'.'.$this->application_settings->url; ?>">
                                                        <i class="fa fa-user-secret"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  

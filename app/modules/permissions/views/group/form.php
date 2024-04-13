<div class="note note-success">
    <p> To assign Access Permissions to specific roles, kindly check the box that corresponds with the task.
        <p style="font-size: 4px;">&nbsp;</p>
        </p><strong>NB: </strong>Roles assigned to the <strong>Group Administrator</strong> will be automatically be overriden by the system's permissions.
</div>
 <?php echo form_open_multipart($this->uri->uri_string(), ' role="form" class="form_submit" '); ?>
    <div class="form-body">
        <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed">
            <thead>
                <tr>
                    <th>
                        #
                    </th>
                    <th>
                        <?php
                            $default_message='Roles';
                            $this->languages_m->translate('roles',$default_message);
                        ?>
                    </th>
                    <?php foreach($group_roles as $role){
                        echo '<th>'.$role->name.'</th>';
                    }?>                   
                </tr>
            </thead>
            <tbody>
                <?php $i=0; foreach ($parent_menus as $menu):
                    if(preg_match('/Dashboard/', $menu->name)):?>
                <tr>
                    <td>
                        <?php echo ++$i;?>
                    </td>
                    <td>
                        <?php echo $menu->name;?>
                    </td>
                    <?php foreach($group_roles as $role){
                        $check=0;
                        if(empty($posts)){
                            $check=1;
                    }else if(array_key_exists($role->id, $posts)){
                        if($posts[$role->id]){
                            if(array_key_exists($menu->id,$posts[$role->id])){
                                $check=1;
                            }
                        }
                    }
                    echo '<td>'.form_checkbox($menu->id.'[]',$role->id,$check,' id="menu_checkbox'.$menu->id.'"').'</td>';
                    }?>
                </tr>
                <?php else:
                ?>
                <tr>
                    <td>
                        <?php echo ++$i;?>
                    </td>
                    <td>
                        <?php 
                            if(preg_match('/\[UNRECONCILED_DEPOSITS_COUNT/', $menu->name)){
                                echo str_replace('[UNRECONCILED_DEPOSITS_COUNT]','', $menu->name);
                            }else if(preg_match('/\[UNRECONCILED_WITHDRAWALS_COUNT/', $menu->name)){
                                echo str_replace('[UNRECONCILED_WITHDRAWALS_COUNT]','', $menu->name);
                            }else{
                                echo $menu->name;
                            }
                        ?>
                    </td>
                    <?php foreach($group_roles as $role){
                        $check=0;
                        if(array_key_exists($role->id, $posts)){
                            if($posts[$role->id]){
                                if(array_key_exists($menu->id,$posts[$role->id])){
                                    $check=1;
                                }
                            }
                        }
                        echo '<td>'.form_checkbox($menu->id.'[]',$role->id,$check,' id="menu_checkbox'.$menu->id.'"').'</td>';
                    }?>
                </tr>
                <?php endif; endforeach;?>
            </tbody>
        </table>       
    </div>
    <div class="form-actions">
        <button type="submit"  class="btn blue submit_form_button" name="submit" value="save_changes">
            <?php
                $default_message='Save Changes';
                $this->languages_m->translate('save_changes',$default_message);
            ?>
        </button>

        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i>
            <?php
                $default_message='Processing';
                $this->languages_m->translate('processing',$default_message);
            ?>
        </button> 
        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">
            <?php
                $default_message='Cancel';
                $this->languages_m->translate('cancel',$default_message);
            ?>

        </button></a>
    </div>
<?php echo form_close(); ?>

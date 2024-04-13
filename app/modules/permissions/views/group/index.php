<div class="note note-success">
    <p> To assign Access Permissions to specific roles, kindly check the box that corresponds with the task.
        <p style="font-size: 4px;">&nbsp;</p>
        </p><strong>NB: </strong>Roles assigned to the <strong>Group Administrator</strong> will be automatically be overriden by the system's permissions.
</div>
 <?php echo form_open_multipart($this->uri->uri_string(), ' role="form" class="form_submit" '); ?>
    <div class="form-body">
        <table class="table m-table m-table--head-separator-primary">
            <thead>
                <tr>
                    <th width='8px'>
                        #
                    </th>
                    <th>
                        Roles
                    </th>
                    <?php foreach($group_roles as $role){
                        echo '<th>'.$role->name.'</th>';
                    }?>                   
                </tr>
            </thead>
            <tbody>
                <?php $i=0; 
                    foreach($parent_menus as $parent_menu):
                        $has_active_children = $this->menus_m->has_active_children($parent_menu->id);
                        $children = $this->menus_m->get_children_links($parent_menu->id);
                        $hide_display_child_menu_rows[$parent_menu->id] = TRUE;
                        foreach($group_roles as $role){
                            if(array_key_exists($role->id, $posts)){
                                if($posts[$role->id]){
                                    if(array_key_exists($parent_menu->id,$posts[$role->id])){
                                    }else{
                                        $hide_display_child_menu_rows[$parent_menu->id] = FALSE;
                                    }
                                }
                            }
                        }

                        if($hide_display_child_menu_rows[$parent_menu->id]){

                        }else{
                            $hide_display_child_menu_rows[$parent_menu->id] = TRUE;
                            foreach($group_roles as $role):
                                $child_checked = 0;
                                $child_unchecked = 0;
                                foreach ($children as $child_menu):
                                    if(array_key_exists($role->id, $posts)){
                                        if($posts[$role->id]){
                                            if(array_key_exists($child_menu->id,$posts[$role->id])){
                                                $child_checked++;
                                            }else{
                                                $child_unchecked++;
                                            }
                                        }
                                    }
                                endforeach;
                                if($child_checked&&$child_unchecked):
                                    $hide_display_child_menu_rows[$parent_menu->id] = FALSE;
                                    break;
                                endif;
                            endforeach;
                        }

                ?>
                    <tr>
                        <td><?php echo ++$i;?></td>
                        <td>
                            <?php if($has_active_children): ?>
                                <?php if($hide_display_child_menu_rows[$parent_menu->id]): ?>
                                    <a data-id='<?php echo $parent_menu->id; ?>' class="parent expand-child-menus" href="javascript:;" ><span><i class="fa fa-plus-square-o"></i></span> </a>
                                <?php else: ?>
                                    <a data-id='<?php echo $parent_menu->id; ?>' class="parent collapse-child-menus" href="javascript:;" ><span><i class="fa fa-minus-square-o"></i></span> </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php 
                                if(preg_match('/\[UNRECONCILED_DEPOSITS_COUNT/', $parent_menu->name)){
                                    echo str_replace('[UNRECONCILED_DEPOSITS_COUNT]','', $parent_menu->name);
                                }else if(preg_match('/\[UNRECONCILED_WITHDRAWALS_COUNT/', $parent_menu->name)){
                                    echo str_replace('[UNRECONCILED_WITHDRAWALS_COUNT]','', $parent_menu->name);
                                }else{
                                    echo $parent_menu->name;
                                }
                            ?>
                        </td>
                    <?php 
                    foreach($group_roles as $role){
                        $check=0;
                        if(array_key_exists($role->id, $posts)){
                            if($posts[$role->id]){
                                if(array_key_exists($parent_menu->id,$posts[$role->id])){
                                    $check=1;
                                }else{
                                }
                            }
                        }
                        echo '<td>'.form_checkbox($parent_menu->id.'[]',$role->id,$check,' data-content = "'.$role->id.'" id="menu_checkbox'.$parent_menu->id.'" data-id="'.$parent_menu->id.'"  class="checkboxes  parent_menu_check_box menu-id-'.$parent_menu->id.' " ').'</td>';
                    }

                    ?>
                    </tr>

                <?php
                    if($has_active_children){
                        $child_count = 1;
                        if($children){
                            foreach ($children as $child_menu){
                                $has_active_grand_children = $this->menus_m->has_active_grand_children($child_menu->id);
                                
                                ?>
                                <tr class="success child-menus 
                                <?php
                                    $hide_display_grand_child_menu_rows[$child_menu->id] = TRUE;
                                    foreach($group_roles as $role){
                                        if(array_key_exists($role->id, $posts)){
                                            if($posts[$role->id]){
                                                if(array_key_exists($child_menu->id,$posts[$role->id])){

                                                }else{
                                                    $hide_display_grand_child_menu_rows[$child_menu->id] = FALSE;
                                                }
                                            }
                                        }
                                    }

                                    $grand_children = $this->menus_m->get_children_links($child_menu->id);

                                    if($hide_display_grand_child_menu_rows[$child_menu->id]){

                                    }else{
                                        $hide_display_grand_child_menu_rows[$child_menu->id] = TRUE;
                                        foreach($group_roles as $role):
                                            $grand_child_checked = 0;
                                            $grand_child_unchecked = 0;
                                            foreach ($grand_children as $grand_child_menu):
                                                if(array_key_exists($role->id, $posts)){
                                                    if($posts[$role->id]){
                                                        if(array_key_exists($grand_child_menu->id,$posts[$role->id])){
                                                            $grand_child_checked++;
                                                        }else{
                                                            $grand_child_unchecked++;
                                                        }
                                                    }
                                                }
                                            endforeach;
                                            if($grand_child_checked&&$grand_child_unchecked):
                                                $hide_display_grand_child_menu_rows[$child_menu->id] = FALSE;
                                            $hide_display_child_menu_rows[$parent_menu->id] = FALSE;
                                                break;
                                            endif;
                                        endforeach;
                                    }
                                    if($hide_display_child_menu_rows[$parent_menu->id]&&$hide_display_grand_child_menu_rows[$child_menu->id]){

                                    }else{
                                        echo ' display-child-menus ';
                                    }
                                ?> 
                                       child-<?php echo $parent_menu->id; ?>">
                                    <td><?php echo '&nbsp;&nbsp;'.$i.'.'.$child_count++; ?></td>
                                    <td>
                                        <?php if($has_active_grand_children): ?>
                                            <?php if($hide_display_grand_child_menu_rows[$child_menu->id]): ?>
                                                <a data-id='<?php echo $child_menu->id; ?>' class="child-parent expand-grand-child-menus" href="javascript:;" ><span><i class="fa fa-plus-square-o"></i></span> </a>
                                            <?php else: ?>
                                                <a data-id='<?php echo $child_menu->id; ?>' class="child-parent collapse-grand-child-menus" href="javascript:;" ><span><i class="fa fa-minus-square-o"></i></span> </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php 
                                            if(preg_match('/\[UNRECONCILED_DEPOSITS_COUNT/', $child_menu->name)){
                                                echo str_replace('[UNRECONCILED_DEPOSITS_COUNT]','', $child_menu->name);
                                            }else if(preg_match('/\[UNRECONCILED_WITHDRAWALS_COUNT/', $child_menu->name)){
                                                echo str_replace('[UNRECONCILED_WITHDRAWALS_COUNT]','', $child_menu->name);
                                            }else{
                                                echo $child_menu->name;
                                            }
                                        ?>
                                    </td>
                                    <?php 
                                    foreach($group_roles as $role){
                                        $child_check=0;
                                        if(array_key_exists($role->id, $posts)){
                                            if($posts[$role->id]){
                                                if(array_key_exists($child_menu->id,$posts[$role->id])){
                                                    $child_check=1;
                                                }
                                            }
                                        }
                                        echo '<td>'.form_checkbox($child_menu->id.'[]',$role->id,$child_check,' data-parent-id="'.$parent_menu->id.'" data-id ="'.$child_menu->id.'" data-content = "'.$role->id.'"  id="menu_checkbox'.$child_menu->id.'" class=" child_parent_menu_check_box menu-id-'.$child_menu->id.' role-id-'.$role->id.' parent-menu-id-'.$parent_menu->id.' "').'</td>';
                                    }?>
                                </tr>
                            <?php
                                if($has_active_grand_children){
                                    $grand_children = $this->menus_m->get_children_links($child_menu->id);

                                    if($hide_display_grand_child_menu_rows[$child_menu->id]){

                                    }else{
                                        $hide_display_grand_child_menu_rows[$child_menu->id] = TRUE;
                                        foreach($group_roles as $role):
                                            $grand_child_checked = 0;
                                            $grand_child_unchecked = 0;
                                            foreach ($grand_children as $grand_child_menu):
                                                if(array_key_exists($role->id, $posts)){
                                                    if($posts[$role->id]){
                                                        if(array_key_exists($grand_child_menu->id,$posts[$role->id])){
                                                            $grand_child_checked++;
                                                        }else{
                                                            $grand_child_unchecked++;
                                                        }
                                                    }
                                                }
                                            endforeach;
                                            if($grand_child_checked&&$grand_child_unchecked):
                                                $hide_display_grand_child_menu_rows[$child_menu->id] = FALSE;
                                                break;
                                            endif;
                                        endforeach;
                                    }

                                    if($grand_children){
                                        $grand_child_count = 1;
                                        foreach ($grand_children as $grand_child_menu){
                                            $has_active_great_grand_children = $this->menus_m->has_active_grand_children($grand_child_menu->id);
                                            $great_grand_child= $this->menus_m->get_children_links($grand_child_menu->id);
                                            $hide_display_great_grand_child_menu_rows[$grand_child_menu->id] = TRUE;
                                            foreach($group_roles as $role):
                                                $great_grand_child_checked = 0;
                                                $great_grand_child_unchecked = 0;
                                                foreach ($great_grand_child as $great_grand_child_menu):
                                                    if(array_key_exists($role->id, $posts)){
                                                        if($posts[$role->id]){
                                                            if(array_key_exists($great_grand_child_menu->id,$posts[$role->id])){
                                                                $great_grand_child_checked++;
                                                            }else{
                                                                $great_grand_child_unchecked++;
                                                            }
                                                        }
                                                    }
                                                endforeach;
                                                if($great_grand_child_checked&&$great_grand_child_unchecked):
                                                    $hide_display_great_grand_child_menu_rows[$grand_child_menu->id] = FALSE;
                                                    break;
                                                endif;
                                            endforeach;
                                            ?>
                                            <tr data-id='<?php echo $parent_menu->id; ?>' class="warning
                                            <?php
                                                if($hide_display_grand_child_menu_rows[$child_menu->id]){

                                                }else{
                                                    echo ' display-child-menus ';
                                                }
                                            ?>     
                                            great-grand-parent-<?php echo $parent_menu->id; ?>  grand-parent-<?php echo $child_menu->id; ?> grand-child-menus grand-child-<?php echo $child_menu->id; ?>">
                                                <td><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$i.'.'.($child_count-1).'.'.$grand_child_count++; ?></td>
                                                <td>
                                                    <?php if($has_active_great_grand_children): ?>
                                                        <?php if($hide_display_great_grand_child_menu_rows[$grand_child_menu->id]): ?>
                                                            <a data-id='<?php echo $grand_child_menu->id; ?>' class="grand-child-parent expand-great-grand-child-menus" href="javascript:;" ><span><i class="fa fa-plus-square-o"></i></span> </a>
                                                        <?php else: ?>
                                                            <a data-id='<?php echo $grand_child_menu->id; ?>' class="grand-child-parent collapse-great-grand-child-menus" href="javascript:;" ><span><i class="fa fa-minus-square-o"></i></span> </a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php 
                                                        if(preg_match('/\[UNRECONCILED_DEPOSITS_COUNT/', $grand_child_menu->name)){
                                                            echo str_replace('[UNRECONCILED_DEPOSITS_COUNT]','', $grand_child_menu->name);
                                                        }else if(preg_match('/\[UNRECONCILED_WITHDRAWALS_COUNT/', $grand_child_menu->name)){
                                                            echo str_replace('[UNRECONCILED_WITHDRAWALS_COUNT]','', $grand_child_menu->name);
                                                        }else{
                                                            echo $grand_child_menu->name;
                                                        }
                                                    ?>
                                                </td>
                                                <?php foreach($group_roles as $role){
                                                    $grand_child_check=0;
                                                    if(array_key_exists($role->id, $posts)){
                                                        if($posts[$role->id]){
                                                            if(array_key_exists($grand_child_menu->id,$posts[$role->id])){
                                                                $grand_child_check=1;
                                                            }
                                                        }
                                                    }
                                                    echo '<td>'.form_checkbox($grand_child_menu->id.'[]',$role->id,$grand_child_check,' data-parent-id="'.$child_menu->id.'" data-grand-parent-id = "'.$parent_menu->id.'" data-id = "'.$grand_child_menu->id.'" data-content = "'.$role->id.'" id="menu_checkbox'.$grand_child_menu->id.'" class=" grand_child_parent_menu_check_box menu-id-'.$grand_child_menu->id.' role-id-'.$role->id.' parent-menu-id-'.$child_menu->id.' grand-parent-menu-id-'.$parent_menu->id.' "').'</td>';
                                                }?>
                                            </tr>
                                        <?php
                                            if($has_active_great_grand_children){
                                                if($great_grand_child){
                                            
                                                    $great_grand_child_count = 1;
                                                    foreach ($great_grand_child as $great_grand_child_menu){?>
                                                       <tr class="info   
                                                        <?php
                                                            if($hide_display_great_grand_child_menu_rows[$grand_child_menu->id]){

                                                            }else{
                                                                echo ' display-child-menus ';
                                                            }
                                                        ?>   
                                                           grand-parent-<?php echo $child_menu->id; ?> great-grand-parent-<?php echo $parent_menu->id; ?>  great-grand-child-menus great-grand-child-<?php echo $grand_child_menu->id; ?>">
                                                            <td><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$i.'.'.($child_count-1).'.'.$grand_child_count.'.'.$great_grand_child_count++; ?></td>
                                                            <td>
                                                                <?php 
                                                                    if(preg_match('/\[UNRECONCILED_DEPOSITS_COUNT/', $great_grand_child_menu->name)){
                                                                        echo str_replace('[UNRECONCILED_DEPOSITS_COUNT]','', $great_grand_child_menu->name);
                                                                    }else if(preg_match('/\[UNRECONCILED_WITHDRAWALS_COUNT/', $great_grand_child_menu->name)){
                                                                        echo str_replace('[UNRECONCILED_WITHDRAWALS_COUNT]','', $great_grand_child_menu->name);
                                                                    }else{
                                                                        echo $great_grand_child_menu->name;
                                                                    }
                                                                ?>
                                                            </td>
                                                            <?php foreach($group_roles as $role){
                                                                $great_grand_child_check=0;
                                                                if(array_key_exists($role->id, $posts)){
                                                                    if($posts[$role->id]){
                                                                        if(array_key_exists($great_grand_child_menu->id,$posts[$role->id])){
                                                                            $great_grand_child_check=1;
                                                                        }
                                                                    }
                                                                }
                                                                echo '<td>'.form_checkbox($great_grand_child_menu->id.'[]',$role->id,$great_grand_child_check,' data-parent-id="'.$grand_child_menu->id.'" data-grand-parent-id="'.$child_menu->id.'" data-great-grand-parent-id="'.$parent_menu->id.'" id="menu_checkbox'.$great_grand_child_menu->id.'" data-content="'.$role->id.'" class="great_grand_child_parent_menu_check_box menu-id-'.$great_grand_child_menu->id.' role-id-'.$role->id.' parent-menu-id-'.$grand_child_menu->id.'  grand-parent-menu-id-'.$child_menu->id.' great-grand-parent-menu-id-'.$parent_menu->id.' "').'</td>';
                                                            }?>
                                                        </tr> 
                                                    <?php
                                                    }
                                                }

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                 endforeach;?>
            </tbody>
        </table>       
    </div>
    <div class="form-actions">
        <button type="submit"  class="btn blue submit_form_button" name="submit" value="save_changes">Save Changes</button>
        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
    </div>
<?php echo form_close(); ?>
<script>
    $(document).ready(function(){

        $('.parent').on('click',function(){
            var parent_id = $(this).attr('data-id');
            if($(this).hasClass('expand-child-menus')){
                $('.child-'+parent_id).each(function(){
                    $(this).slideDown();
                });
                $(this).removeClass('expand-child-menus');
                $(this).addClass('collapse-child-menus');
                $(this).find('.fa-plus-square-o').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
            }else if($(this).hasClass('collapse-child-menus')){
                $('.child-'+parent_id).each(function(){
                    $(this).slideUp();
                    $(this).find('.fa-minus-square-o').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                });

                $('.grand-parent-'+parent_id).each(function(){
                    $(this).slideUp();
                    $(this).find('.fa-minus-square-o').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                });

                $('.great-grand-parent-'+parent_id).each(function(){
                    $(this).slideUp();
                    $(this).find('.fa-minus-square-o').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                });

                $(this).removeClass('collapse-child-menus');
                $(this).addClass('expand-child-menus');
                $(this).find('.fa-minus-square-o').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
        });

        $('.child-parent').on('click',function(){
            var parent_id = $(this).attr('data-id');
            if($(this).hasClass('expand-grand-child-menus')){
                $('.grand-child-'+parent_id).each(function(){
                    $(this).slideDown();
                });
                $(this).removeClass('expand-grand-child-menus');
                $(this).addClass('collapse-grand-child-menus');
                $(this).find('.fa-plus-square-o').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
            }else if($(this).hasClass('collapse-grand-child-menus')){
                $('.grand-child-'+parent_id).each(function(){
                    $(this).slideUp();
                    $(this).find('.fa-minus-square-o').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                });
                $('.grand-parent-'+parent_id).each(function(){
                    $(this).find('.fa-minus-square-o').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
                    $(this).slideUp();
                });
                $(this).removeClass('collapse-grand-child-menus');
                $(this).addClass('expand-grand-child-menus');
                $(this).find('.fa-minus-square-o').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
        });

        $('.grand-child-parent').on('click',function(){
            var parent_id = $(this).attr('data-id');
            if($(this).hasClass('expand-great-grand-child-menus')){
                $('.great-grand-child-'+parent_id).each(function(){
                    $(this).slideDown();
                });
                $(this).removeClass('expand-great-grand-child-menus');
                $(this).addClass('collapse-great-grand-child-menus');
                $(this).find('.fa-plus-square-o').removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
            }else if($(this).hasClass('collapse-great-grand-child-menus')){
                $('.great-grand-child-'+parent_id).each(function(){
                    $(this).slideUp();
                });
                $(this).removeClass('collapse-great-grand-child-menus');
                $(this).addClass('expand-great-grand-child-menus');
                $(this).find('.fa-minus-square-o').removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
        });
        
        $('.parent_menu_check_box').click(function(){
            var id = $(this).attr('data-id');
            var role_id = $(this).attr('data-content');
            if($(this).prop("checked") == true){
                $('.parent-menu-id-'+id+'[data-content='+role_id+']').parent().addClass('checked');
                $( ".parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", true );
                $('.grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().addClass('checked');
                $( ".grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", true );
                $('.great-grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().addClass('checked');
                $( ".great-grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", true );
            }else if($(this).prop("checked") == false){
                $('.parent-menu-id-'+id+'[data-content='+role_id+']').parent().removeClass('checked');
                $( ".parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", false );
                $('.grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().removeClass('checked');
                $( ".grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", false );
                $('.great-grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().removeClass('checked');
                $( ".great-grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", false );
            }
        });

        $('.child_parent_menu_check_box').click(function(){
            var id = $(this).attr('data-id');
            var role_id = $(this).attr('data-content');
            if($(this).prop("checked") == true){
                $('.parent-menu-id-'+id+'[data-content='+role_id+']').parent().addClass('checked');
                $( ".parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", true );
                $('.grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().addClass('checked');
                $( ".grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", true );
                $('.great-grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().addClass('checked');
                $( ".great-grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", true );
            }else if($(this).prop("checked") == false){
                $('.parent-menu-id-'+id+'[data-content='+role_id+']').parent().removeClass('checked');
                $( ".parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", false );
                $('.grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().removeClass('checked');
                $( ".grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", false );
                $('.great-grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().removeClass('checked');
                $( ".great-grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", false );
            }
        });

        $('.grand_child_parent_menu_check_box').click(function(){
            var id = $(this).attr('data-id');
            var role_id = $(this).attr('data-content');
            if($(this).prop("checked") == true){
                $('.parent-menu-id-'+id+'[data-content='+role_id+']').parent().addClass('checked');
                $( ".parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", true );
                $('.grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().addClass('checked');
                $( ".grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", true );
                $('.great-grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().addClass('checked');
                $( ".great-grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", true );
            }else if($(this).prop("checked") == false){
                $('.parent-menu-id-'+id+'[data-content='+role_id+']').parent().removeClass('checked');
                $( ".parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", false );
                $('.grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().removeClass('checked');
                $( ".grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", false );
                $('.great-grand-parent-menu-id-'+id+'[data-content='+role_id+']').parent().removeClass('checked');
                $( ".great-grand-parent-menu-id-"+id+'[data-content='+role_id+']' ).prop( "checked", false );
            }
        });

        $('.child_parent_menu_check_box').on('click',function(){

            var parent_id = $(this).attr('data-parent-id');
            var role_id = $(this).attr('data-content');

            var check_parent = true;

            $('.parent-menu-id-'+parent_id+'[data-content='+role_id+']').each(function(){
                if($(this).prop("checked") == true){

                }else if($(this).prop("checked") == false){
                    check_parent = false;
                }
            });

            if(check_parent){
                $('.parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').parent().addClass('checked');
                $('.parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').prop( "checked", true );
            }else{
                $('.parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').parent().removeClass('checked');
                $('.parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').prop( "checked", false );
            }

        });
    
        $('.grand_child_parent_menu_check_box').on('click',function(){

            var parent_id = $(this).attr('data-parent-id');
            var grand_parent_id = $(this).attr('data-grand-parent-id');
            var role_id = $(this).attr('data-content');
            var check_parent = true;
            var check_grand_parent = true;

            $('.parent-menu-id-'+parent_id+'[data-content='+role_id+']').each(function(){
                if($(this).prop("checked") == true){

                }else if($(this).prop("checked") == false){
                    check_parent = false;
                }
            });

            if(check_parent){
                $('.child_parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').parent().addClass('checked');
                $('.child_parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').prop( "checked", true );
            }else{
                $('.child_parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').parent().removeClass('checked');
                $('.child_parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').prop( "checked", false );
            }

            $('.parent-menu-id-'+grand_parent_id+'[data-content='+role_id+']').each(function(){
                if($(this).prop("checked") == true){

                }else if($(this).prop("checked") == false){
                    check_grand_parent = false;
                }
            });


            if(check_grand_parent){
                $('.parent_menu_check_box[data-id='+grand_parent_id+'][data-content='+role_id+']').parent().addClass('checked');
                $('.parent_menu_check_box[data-id='+grand_parent_id+'][data-content='+role_id+']').prop( "checked", true );
            }else{
                $('.parent_menu_check_box[data-id='+grand_parent_id+'][data-content='+role_id+']').parent().removeClass('checked');
                $('.parent_menu_check_box[data-id='+grand_parent_id+'][data-content='+role_id+']').prop( "checked", false );
            }
            
        });

        $('.great_grand_child_parent_menu_check_box').on('click',function(){
            var parent_id = $(this).attr('data-parent-id');
            var grand_parent_id = $(this).attr('data-grand-parent-id');
            var great_grand_parent_id = $(this).attr('data-great-grand-parent-id');
            var role_id = $(this).attr('data-content');
            var check_parent = true;
            var check_grand_parent = true;
            var check_great_grand_parent = true;

            $('.parent-menu-id-'+parent_id+'[data-content='+role_id+']').each(function(){
                if($(this).prop("checked") == true){

                }else if($(this).prop("checked") == false){
                    check_parent = false;
                }
            });

            if(check_parent){
                $('.grand_child_parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').parent().addClass('checked');
                $('.grand_child_parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').prop( "checked", true );
            }else{
                $('.grand_child_parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').parent().removeClass('checked');
                $('.grand_child_parent_menu_check_box[data-id='+parent_id+'][data-content='+role_id+']').prop( "checked", false );
            }

            $('.parent-menu-id-'+grand_parent_id+'[data-content='+role_id+']').each(function(){
                if($(this).prop("checked") == true){

                }else if($(this).prop("checked") == false){
                    check_grand_parent = false;
                }
            });

            if(check_grand_parent){
                $('.child_parent_menu_check_box[data-id='+grand_parent_id+'][data-content='+role_id+']').parent().addClass('checked');
                $('.child_parent_menu_check_box[data-id='+grand_parent_id+'][data-content='+role_id+']').prop( "checked", true );
            }else{
                $('.child_parent_menu_check_box[data-id='+grand_parent_id+'][data-content='+role_id+']').parent().removeClass('checked');
                $('.child_parent_menu_check_box[data-id='+grand_parent_id+'][data-content='+role_id+']').prop( "checked", false );
            }

            $('.parent-menu-id-'+great_grand_parent_id+'[data-content='+role_id+']').each(function(){
                if($(this).prop("checked") == true){

                }else if($(this).prop("checked") == false){
                    check_great_grand_parent = false;
                }
            });


            if(check_great_grand_parent){
                $('.parent_menu_check_box[data-id='+great_grand_parent_id+'][data-content='+role_id+']').parent().addClass('checked');
                $('.parent_menu_check_box[data-id='+great_grand_parent_id+'][data-content='+role_id+']').prop( "checked", true );
            }else{
                $('.parent_menu_check_box[data-id='+great_grand_parent_id+'][data-content='+role_id+']').parent().removeClass('checked');
                $('.parent_menu_check_box[data-id='+great_grand_parent_id+'][data-content='+role_id+']').prop( "checked", false );
            }
        });

    });
</script>

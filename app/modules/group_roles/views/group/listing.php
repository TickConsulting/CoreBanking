<?php if(!empty($posts)){ ?> 
<?php if ( ! empty($pagination['links'])): ?>
    <div class="row col-md-12">
        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Group Roles</p>
    <?php 
        echo '<div class ="top-bar-pagination">';
        echo $pagination['links']; 
        echo '</div></div>';
        endif; 
    ?>  
    <table class="table m-table m-table--head-separator-primary">
        <thead>
            <tr>
                <th width='2%'>
                    #
                </th>
                <th>
                    <?php echo translate(' Name');?>
                </th>
                <th>
                    <?php echo translate(' Status');?>
                </th>
                <th>
                    &nbsp;
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                <tr>
                    <td><?php echo $i+1;?></td>
                    <td><?php echo $post->name; ?></td>
                    <td>
                        <?php 
                            if($post->active){
                                echo "<span class='m-badge m-badge--info m-badge--wide'>Active</span> &nbsp;&nbsp;";
                            }else{
                                echo "<span class='m-badge m-badge--info m-badge--default'>Hidden</span> &nbsp;&nbsp;";
                            }
                            if($post->is_editable){
                                //do nothing
                            }else{
                                echo "<span class='m-badge m-badge--danger m-badge--wide'>Locked</span> &nbsp;&nbsp;";
                            }
                            if(in_array($post->id,$member_group_role_ids)){
                                echo "<span class='m-badge m-badge--info m-badge--wide'>In Use</span> &nbsp;&nbsp;";
                            }else{
                                echo "<span class='m-badge m-badge--secondary m-badge--wide'>Not In Use</span> &nbsp;&nbsp;";
                            }
                        ?>
                    </td>
                    <td>
                        <?php if($post->is_editable){ ?>
                            <a href="<?php echo site_url('group/group_roles/edit/'.$post->id); ?>" class="btn btn-sm btn-primary m-btn m-btn--icon action_button">
                                <span>
                                    <i class="la la-edit"></i>
                                    <span>
                                        <?php echo translate('edit');?>&nbsp;&nbsp; 
                                    </span>
                                </span>
                            </a>
                            <?php if($post->active){ ?>
                                <a href="<?php echo site_url('group/group_roles/hide/'.$post->id); ?>" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button">
                                    <span>
                                        <i class="la la-eye-slash"></i>
                                        <span>
                                            <?php echo translate('Hide');?>
                                        </span>
                                    </span>
                                </a>
                            <?php }else{ ?>
                                <a href="<?php echo site_url('group/group_roles/unhide/'.$post->id); ?>" class="btn btn-sm confirmation_link btn-success m-btn m-btn--icon action_button">
                                    <span>
                                        <i class="la la-eye"></i>
                                        <span>
                                            <?php echo translate('Unhide');?>
                                        </span>
                                    </span>
                                </a>
                            <?php } } ?>
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
<?php }else{ ?>
    <div class="m-alert m-alert--outline alert alert-info fade show" role="alert">
        <strong><?php echo translate('Sorry'); ?>!</strong> <?php translate('No records to display.'); ?>
    </div>
<?php } ?>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on('click','.confirmation_link',function(){
            var element = $(this);
            bootbox.confirm({
                message: "Are you sure you want to Hide this group role?",
                // title: "Before you proceed",
                callback: function(result) {
                    if(result==true){
                        if (result === null) {
                            return true;
                        }else{
                            var href = element.attr('href');
                            window.location = href;
                        }
                    }else{
                        return true;
                    }
                }
            });
            return false;
        });

    });
</script>


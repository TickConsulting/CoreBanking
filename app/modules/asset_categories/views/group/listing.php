
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
                   <?php echo translate('Asset Category Name') ?>
                </th>
                <th>
                   <?php
                    if($this->lang->line('status')){
                    echo $this->lang->line('status');
                    }else{
                    echo "Status";
                    }
                ?>
                </th>
                <th width="30%">
                   <?php
                    if($this->lang->line('actions')){
                    echo $this->lang->line('actions');
                    }else{
                    echo "Actions";
                    }
                ?>
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
                        if($post->is_hidden){
                            echo "<span class='m-badge m-badge--info m-badge--wide tooltips'>Hidden</span>";
                        }
                        else
                        {
                             if($post->active){
                                echo "<span data-original-title='This asset category is active' class='m-badge m-badge--success m-badge--wide tooltips'>Active</span>";
                            }else{
                                echo "<span class='m-badge m-badge--info m-badge--wide tooltips'>Hidden</span>";
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <a href="<?php echo site_url('group/asset_categories/edit/'.$post->id); ?>" class="btn btn-sm btn-primary m-btn m-btn--icon action_button" data-original-title="Edit Group Asset Category" >
                            <span>
                                <i class="la la-pencil"></i>
                                <span>
                                    <?php echo translate('Edit')?> &nbsp;&nbsp;
                                </span>
                            </span>
                        </a>
                        <?php if($post->is_hidden):?>
                            <a data-original-title="Unhide this category to appear on the selection list" href="<?php echo site_url('group/asset_categories/unhide/'.$post->id); ?>" class="btn btn-sm confirmation_link btn-info m-btn m-btn--icon action_button">
                                <span>
                                    <i class="la la-eye"></i>
                                    <span>
                                        <?php echo translate('Unhide')?> &nbsp;&nbsp;
                                    </span>
                                </span>
                            </a>
                        <?php else:?>
                            <a data-original-title="Hide this category from the selection list" href="<?php echo site_url('group/asset_categories/hide/'.$post->id); ?>" class="btn btn-sm confirmation_link btn-danger m-btn m-btn--icon action_button">
                                <span>
                                    <i class="la la-eye-slash"></i>
                                    <span>
                                        <?php echo translate('Hide')?> &nbsp;&nbsp;
                                    </span>
                                </span>
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
<?php }else{ ?>
    <div class="m-alert m-alert--outline alert alert-info alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        <strong>Sorry!</strong>
       Information! No records to display                        
    </div>
<?php } ?>
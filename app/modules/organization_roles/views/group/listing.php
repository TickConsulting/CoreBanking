
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
     <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed">
        <thead>
            <tr>
                <th width='2%'>
                    #
                </th>
                <th>
                    <?php
                        $default_message='Name';
                        $this->languages_m->translate('name',$default_message);
                    ?>
                </th>
                <th>
                    <?php
                        $default_message='Status';
                        $this->languages_m->translate('status',$default_message);
                    ?>
                </th>
                <th width="30%">
                    <?php
                        $default_message='Actions';
                        $this->languages_m->translate('actions',$default_message);
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
                            if($post->active){
                                echo "<span class='label label-success'>Active</span>";
                            }else{
                                echo "<span class='label label-default'>Hidden</span>";
                            }
                            if($post->is_editable){
                                //do nothing
                            }else{
                                echo "<span class='label label-default'>Locked</span>";
                            }
                            if(in_array($post->id,$member_oranization_role_ids)){
                                echo "<span class='label label-info'>In Use</span>";
                            }else{
                                echo "<span class='label label-warning'>Not In Use</span>";
                            }
                        ?>
                    </td>
                    <td>
                        <?php if($post->is_editable){ ?>
                            <a href="<?php echo site_url('group/organization_roles/edit/'.$post->id); ?>" class="btn btn-xs default">
                                <i class="icon-pencil"></i> 
                                    <?php
                                        $default_message='Edit';
                                        $this->languages_m->translate('edit',$default_message);
                                    ?>
                                 &nbsp;&nbsp; 
                            </a>
                            <?php if($post->active){ ?>
                                <a href="<?php echo site_url('group/organization_roles/hide/'.$post->id); ?>" class="btn btn-xs default confirmation_link">
                                    <i class="fa fa-eye-slash"></i> 
                                            <?php
                                                $default_message='Hide';
                                                $this->languages_m->translate('hide',$default_message);
                                            ?>
                                     &nbsp;&nbsp; 
                                </a>
                            <?php }else{ ?>
                                <a href="<?php echo site_url('group/organization_roles/unhide/'.$post->id); ?>" class="btn btn-xs green confirmation_link">
                                    <i class="icon-eye"></i> 
                                            <?php
                                                $default_message='Unhide';
                                                $this->languages_m->translate('unhide',$default_message);
                                            ?>
                                     &nbsp;&nbsp; 
                                </a>
                            <?php } ?>
                        <?php } ?>
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
<div class="alert alert-info">
    <h4 class="block">
        <?php
            $default_message='Information! No records to display';
            $this->languages_m->translate('no_records_to_display',$default_message);
        ?>
        
    </h4>
    <p>
        No Group Organization Roles to display.
    </p>
</div>
<?php } ?>
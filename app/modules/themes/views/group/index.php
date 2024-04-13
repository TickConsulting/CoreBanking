
<div class="mt-element-card mt-element-overlay">
    <div class="row">
        <?php foreach($posts as $post): ?>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="theme-box mt-card-item">
                    <div class="mt-card-avatar mt-overlay-1">
                        <img src="<?php echo is_file(FCPATH.'uploads/files/'.$post->logo)?site_url('uploads/files/'.$post->logo):site_url('uploads/logos/'.$this->application_settings->paper_header_logo); ?>" />
                        <div class="mt-overlay">
                            <ul class="mt-info">
                                <li>
                                    <?php if($this->group->theme==$post->slug){ ?>
                                        <a  id="<?php echo $post->id; ?>" class="remove_theme btn default btn-outline" href="javascript:;">
                                            <i class="fa fa-remove"></i> Remove Theme
                                        </a>
                                    <?php }else{ ?>
                                        <a  id="<?php echo $post->id; ?>" class="set_theme btn default btn-outline" href="javascript:;">
                                            <i class="fa fa-pencil"></i> Set Theme
                                        </a>
                                    <?php } ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class='clearfix'></div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.set_theme').on('click',function(){
            var id = $(this).attr('id');
            $.post('<?php echo base_url("group/groups/ajax_set_theme"); ?>',{'id':id,},function(data){
                toastr['success']('You have successfully set a new theme for your group.','Theme settings saved successfully');
            });   
        });
        $('.remove_theme').on('click',function(){
            $.post('<?php echo base_url("group/groups/ajax_remove_theme"); ?>',{'id':'',},function(data){
                toastr['success']('You have successfully removed theme for your group. Kindly refresh to load the default theme','Theme settings saved successfully');
            });   
        });
    });
</script>
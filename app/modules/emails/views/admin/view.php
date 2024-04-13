<style type="text/css">
    .new-line-indented{
        text-indent: 25px;
        line-height: 1.6;
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
            <?php if($post):?>
                <div>
                    <h4><strong>Subject: </strong><?php echo $post->subject; ?></h4>
                    <?php if($group_name){?>
                    <h5><strong>Group Name: </strong><?php echo $group_name->name; ?></h5>
                    <?php }?>
                    <h5><strong>To: </strong><?php
                                    $email_to = explode(',', $post->email_to);
                                     if(is_array($email_to)){
                                        for ($j=0;$j<count($email_to); $j++){ 
                                            if($j==0){
                                               echo $email_to[$j].',<br/>'; 
                                            }else{
                                                echo '<div class="new-line-indented">'.$email_to[$j].',</div>';
                                            }
                                        }
                                     }else{
                                        echo $post->email_to;
                                     }
                                    ?>
                                </h5>
                    <h5><strong>From: </strong><?php echo $post->email_from; ?></h5>
                    <h5><strong>Created On: </strong><?php echo timestamp_to_datetime($post->created_on); ?></h5>
                    <div>
                        <?php echo $post->message; ?>
                    </div>
                </div>
            <?php else:?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                           No content in the email
                        </p>
                    </div>
                <?php endif;?>

            </div>

        </div>



    </div>

</div>

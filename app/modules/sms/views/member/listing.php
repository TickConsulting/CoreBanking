<?php if(!empty($posts)){ ?>
    <?php echo form_open('admin/saccos/action', ' id="form"  class="form-horizontal"'); ?> 

    <?php if ( ! empty($pagination['links'])): ?>
        <div class="row col-md-12">
            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> SMSes</p>
        <?php 
            echo '<div class ="top-bar-pagination">';
            echo $pagination['links']; 
            echo '</div></div>';
            endif; 
        ?>  
         <table class="table table-striped table-bordered table-hover table-header-fixed table-condensed table-searchable">
            <thead>
                <tr>                    
                    <th>
                        #
                    </th>
                    <th>
                       SMS From
                    </th>
                    <th>
                       <?php
                            $default_message='Message';
                            $this->languages_m->translate('message',$default_message);
                        ?>
                    </th>
                    <th>
                        Received On
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post):$sent_by = $this->ion_auth->get_user($post->created_by); ?>
                    <tr>
                        <td><?php echo $i+1;?></td>
                        <td><?php echo $sent_by->first_name.' '.$sent_by->last_name;?></td>
                        <td><?php echo $post->message;?></td>
                        <td>
                            <?php if($post->sms_result_id){echo timestamp_to_datetime($post->created_on);}?>
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
        
    <?php echo form_close(); ?>
<?php }else{ ?>
    <div class="alert alert-info">
        <h4 class="block">
            <?php
                $default_message='Information! No records to display';
                $this->languages_m->translate('no_records_to_display',$default_message);
            ?>
            
        </h4>
        <p>
            No SMSes to display.
        </p>
    </div>
<?php } ?>              

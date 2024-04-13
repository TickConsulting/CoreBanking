<?php if(!empty($posts)){ ?>
<?php echo form_open('group/invoices/action', ' id="form"  class="form-horizontal"'); ?> 

<?php if ( ! empty($pagination['links'])): ?>
    <div class="row col-md-12">
        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Invoices</p>
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
                <th width='2%'>
                    #
                </th>
                <th>
                    <?php
                        $default_message='Invoice Date';
                        $this->languages_m->translate('invoice_date',$default_message);
                    ?>
                </th>
                <th>
                    <?php
                        $default_message='Member';
                        $this->languages_m->translate('member',$default_message);
                    ?>
                </th>
                <th>
                    <?php
                        $default_message='Description';
                        $this->languages_m->translate('description',$default_message);
                    ?>
                </th>
                <th class='text-right'>
                    <?php
                        $default_message='Payable';
                        $this->languages_m->translate('payable',$default_message);
                    ?>

                     (<?php echo $this->group_currency; ?>)
                </th> 
                <th class='text-right'>
                    <?php
                        $default_message='Paid';
                        $this->languages_m->translate('paid',$default_message);
                    ?>

                     (<?php echo $this->group_currency; ?>)
                </th>  
                <th>
                    <?php
                        $default_message='Actions';
                        $this->languages_m->translate('actions',$default_message);
                    ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $i = $this->uri->segment(5, 0); $i++; foreach($posts as $post): ?>
                <tr>
                    <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo timestamp_to_date($post->invoice_date); ?></td>
                    <td><?php echo $this->group_member_options[$post->member_id]; ?></td>
                    <td>
                        <?php
                            if($post->type==1){
                                echo $this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                            }else if($post->type==2){
                                echo $this->invoice_type_options[$post->type].' for '.$contribution_options[$post->contribution_id].' contribution';
                            }else if($post->type==3){
                                echo $this->invoice_type_options[$post->type].' for '.$fine_category_options[$post->fine_category_id];
                            }else if($post->type==4){
                                echo $this->invoice_type_options[$post->type].' for '.$post->description;
                            }
                        ?>
                    </td>
                    <td class='text-right'>
                        <?php echo number_to_currency($post->amount_payable); ?>
                    </td>
                    <td  class='text-right'>
                        <?php echo number_to_currency($post->amount_paid); ?>
                    </td>  
                    <td>
                        <a href="<?php echo site_url('member/invoices/view/'.$post->id); ?>" class="btn btn-xs default">
                            <i class="fa fa-eye"></i> 
                                <?php
                                    $default_message='View';
                                    $this->languages_m->translate('view',$default_message);
                                ?>
                             &nbsp;&nbsp; 
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
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
            No invoices to display.
        </p>
    </div>
<?php } ?>
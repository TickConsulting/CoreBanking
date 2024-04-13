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
                <?php if(!empty($posts)){ ?>
                    <?php echo form_open('admin/coupons/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Countries</p>
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
                                         <input type="checkbox" name="check" value="all" class="check_all">
                                    </th>
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Coupon
                                    </th>
                                    <th>
                                        Type
                                    </th>
                                    <th>
                                        Active From
                                    </th>
                                    <th>
                                        Expiry Date
                                    </th>
                                    <th>
                                        Distribution
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th width="20%">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo $post->name; ?></td>
                                        <td><?php echo $post->coupon; ?></td>
                                        <td><?php
                                            if($post->type == 1){
                                                echo 'KES '.number_to_currency($post->fixed_amount).' ';
                                            }else if($post->type==2){
                                                echo $post->percentage_value.'% - ';
                                            }else if($post->type==3){
                                                if($post->coupon_waiver_type == 1){
                                                    echo $coupon_waiver_types[$post->coupon_waiver_type].' - ';
                                                }else{
                                                    echo $post->partial_waiver_period.' months '.$coupon_waiver_types[$post->coupon_waiver_type].' starting '.timestamp_to_mobile_time($post->partial_waiver_start_date).' - ';
                                                }
                                            }else if($post->type == 4){
                                                echo $post->free_months.'  ';
                                            }
                                            echo $coupon_types[$post->type];
                                        ?></td>
                                        <td><?php echo timestamp_to_mobile_shorttime($post->date_active_from);?></td>
                                        <td>
                                            <?php echo timestamp_to_mobile_shorttime($post->expiry_date); ?>
                                        </td>
                                        <td>
                                            <?php if ($post->distribution_limit ==1) {
                                                echo 'Unlimited distribution';
                                            }else{
                                                echo 'Limited distribution of '.$post->limited_users.' groups';
                                            }?>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->active){
                                                    echo "<span class='label label-success'>Active</span>";
                                                }else{
                                                    echo "<span class='label label-default'>In active</span>";
                                                }
                                            ?>
                                        </td>

                                        <td>
                                            <a href="<?php echo site_url('admin/coupons/edit/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            <a href="<?php echo site_url('admin/coupons/delete/'.$post->id); ?>" class="btn btn-xs btn-danger confirmation-link">
                                                <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
                                            </a>
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
                        <?php if($posts):?>
                            <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_delete' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye-slash'></i> Bulk Delete</button>
                        <?php endif;?>
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No coupons to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
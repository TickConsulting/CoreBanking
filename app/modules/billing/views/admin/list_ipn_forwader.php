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
                    <?php echo form_open('admin/billing/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> IPN FOrwards</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
                        <div class="table-responsive" >
                            <table class="table  table-striped table-bordered table-hover table-header-fixed table-condensed table-searchable">
                                <thead>
                                    <tr>
                                        <th width='2%'>
                                             <input type="checkbox" name="check" value="all" class="check_all">
                                        </th>
                                        <th width='2%'>
                                            #
                                        </th>
                                        <th>
                                            Title
                                        </th>
                                        <th>
                                            Account Validation
                                        </th>
                                        <th>
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                        <tr>
                                            <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                            <td><?php echo $i+1;?></td>
                                            <td><?php echo $post->title; ?></td>
                                            <td>
                                                <?php echo $post->account_validation_end_point; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo site_url('admin/billing/edit_billing_payments_forwarder/'.$post->id); ?>" class="btn btn-xs default">
                                                    <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                                </a>
                                                <a href="<?php echo site_url('admin/billing/delete_billing_payments_forwarder/'.$post->id); ?>" class="confirmation_link btn btn-xs red">
                                                    <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
                                                </a>
                                            </td>
                                        </tr>
                                        <?php $i++;
                                        endforeach; ?>
                                </tbody>
                            </table>
                        </div>

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
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No Billing Payment forwarders to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
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

            <?php if(!empty($posts)){ ?>
                <div class="clearfix">
                    <a href="<?php echo current_url().'?generate_excel=1'?>" class="btn btn-xs blue pull-right">Export</a>
                    <br/><br/>
                </div>
                <?php echo form_open('admin/bank_accounts/pages', ' id="form"  class="form-horizontal"'); ?> 

                <?php if ( ! empty($pagination['links'])): ?>
                    <div class="row col-md-12">
                        <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Bank Accounts</p>
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
                                    Account Name
                                </th>
                                <th>
                                    Bank
                                </th>
                                <th>
                                    Branch 
                                </th>
                                <th>
                                    Account Number
                                </th>
                                 <th>
                                    Verified On
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                <tr>
                                    <td><?php echo $i+1;?></td>
                                    <td><?php echo $post->account_name; ?></td>
                                    <td><?php echo $bank_options[$post->bank_id]; ?></td>
                                    <td>
                                        <?php echo $bank_branch_options[$post->bank_branch_id];?>
                                    </td>
                                    <td>
                                        <?php echo $post->account_number;?>
                                    </td>
                                    <td>
                                        <?php echo timestamp_to_date_and_time($post->verified_on);?>
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
                    <h4 class="block">Information! No records to display</h4>
                    <p>
                        No Bank Accounts to display.
                    </p>
                </div>
            <?php } ?>
            </div>
        </div> 
    </div>
</div>    
                       

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

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Invoices</p>
                        <?php 
                            echo '<div class ="top-bar-pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                            endif; 
                        ?>  
                         <table class="table table-responsive table-searchable table-striped table-bordered table-hover table-header-fixed table-condensed">
                            <thead>
                                <tr>
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        Date
                                    </th>
                                    <th>
                                        Group
                                    </th>
                                    <th>
                                        Member
                                    </th>
                                    <th>
                                        Created On
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo timestamp_to_date($post->invoice_date); ?></td>
                                        <td><?php echo $group_options[$post->group_id]; ?></td>
                                        <td><?php echo $member_options[$post->member_id]; ?></td>
                                        <td><?php echo timestamp_to_datetime($post->created_on); ?></td>
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
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No invoices to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
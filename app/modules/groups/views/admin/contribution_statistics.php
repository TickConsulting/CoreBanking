<style type="text/css"> 
    hr, p {
        margin: 5px 0;
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

                <div class="btn-group search-button hold-on-click">
                    <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> Search
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">
                        <?php echo form_open(current_url(),'method="GET" class="filter"');?>
                            <div class="form-body">
                                <div class="form-group">
                                    <label>Investment Groups</label>
                                    <div class="input-group">
                                        <?php echo form_dropdown('name[]',$group_options,$this->input->get('name'),'class="form-control input-sm" multiplr="multiple" select2-multiplr placeholder="Investment Group Name"'); ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-actions">
                                <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-search"></i></button>
                                <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                            </div>

                        <?php echo form_close();?>

                                    
                       
                    </div>
                </div>

               <?php if($posts):?>
            <?php echo form_open('admin/groups/action', ' id="form"  class="form-horizontal"'); ?> 
                
            <?php if ( ! empty($pagination['links'])): ?>
                <div class="row col-md-12">
                    <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Investment Groups</p>
                <?php 
                    echo '<div class ="top-bar-pagination">';
                    echo $pagination['links']; 
                    echo '</div></div>';
                    endif; 
                ?>  

                <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed table-searchable">
                    <thead>
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                              Details
                            </th>
                            <th>
                                Size
                            </th>
                            <th>
                               Dep
                            </th>
                            <th>
                                With
                            </th>
                            <th class="text-right">
                                Mon. Total Dep.
                            </th>
                            <th class="text-right">
                                Av. Mon. Dep.
                            </th>
                            <th class="text-right">
                                Current Billing
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i=$this->uri->segment(4, 0);
                        $total_deposits_amount = 0;
                        $total_average_month_deposits_amount  = 0;
                        foreach($posts as $post):
                            $user = $this->ion_auth->get_user($post->owner);
                        ?>
                            <tr>
                                <td><?php echo $i+1;?></td>
                                <td><?php echo $post->name; 
                                    ?>
                                </td>
                                <td>
                                    <?php echo $post->active_size?:1;?>
                                </td>
                                <td>
                                    <?php echo $total_deposits[$post->id];?>
                                </td>
                                <td>
                                    <?php echo $total_withdrawals[$post->id];?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_to_currency($total_contributions[$post->id]);?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_to_currency($average_contributions[$post->id]);?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_to_currency($current_billing[$post->id]->amount + $current_billing[$post->id]->tax);?>
                                </td>
                                
                            </tr>
                        <?php 
                            $total_deposits_amount+=$total_contributions[$post->id];
                            $total_average_month_deposits_amount+=$average_contributions[$post->id];
                        $i++; endforeach; 
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                            </td>
                            <td colspan="4">
                                Totals
                            </td>
                            <td class="text-right">
                                <?php echo number_to_currency($total_deposits_amount);?>
                            </td>
                            <td class="text-right">
                                <?php echo number_to_currency($total_average_month_deposits_amount);?>
                            </td>
                            <td>
                                
                            </td>
                        </tr>
                    </tfoot>
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
        

            <?php echo form_close();?>
            <?php else:?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                           There are no group's records to display
                        </p>
                    </div>
                <?php endif;?>

            </div>

        </div>



    </div>

</div>

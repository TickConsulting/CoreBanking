<style type="text/css">
    td.textual-message{
        line-height: 1.8 !important;
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

                <?php if(!empty($posts)){ ?>
                    <?php echo form_open('admin/billing/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Countries</p>
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
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Billing Type
                                    </th>
                                    <th>
                                        Payment
                                    </th>
                                    <th>
                                        SMSes
                                    </th>
                                    <th>
                                        Tax
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th width="16%">
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
                                        <td><?php echo $billing_type[$post->billing_type]; ?></td>
                                         <td class="textual-message"><?php
                                                if($post->billing_type==1){
                                                    echo "<strong>Monthly :</strong> ".$this->default_country->currency_code." ".number_to_currency($post->monthly_amount).'<br/>';
                                                    echo "<strong>Quarterly :</strong> ".$this->default_country->currency_code." ".number_to_currency($post->quarterly_amount).'<br/>';
                                                    echo "<strong>Annual :</strong> ".$this->default_country->currency_code." ".number_to_currency($post->annual_amount).'<br/>';
                                                }else{
                                                    echo $post->rate.'% on '.$billing_percentage_on[$post->rate_on];
                                                }
                                            ?>
                                        </td>
                                        <td class="textual-message"><?php
                                                echo "<strong>Monthly :</strong> ".$post->monthly_smses.' SMSes<br/>';
                                                echo "<strong>Quarterly :</strong> ".$post->quarterly_smses.' SMSes<br/>';
                                                echo "<strong>Annual :</strong> ".$post->annual_smses.' SMSes<br/>';
                                            ?>
                                        </td>
                                        <td><?php if($post->enable_tax){echo $post->percentage_tax.'%';}else{echo "<span class='label label-default'>Not Taxed</span>";}?></td>
                                        <td>
                                            <?php 
                                                if($post->active){
                                                    echo "<span class='label label-success'>Active</span>";
                                                }else{
                                                    echo "<span class='label label-default'>Hidden</span>";
                                                }

                                                if($post->is_default){
                                                    echo "<span class='margin-left-5 label label-info'>Default</span>";
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('admin/billing/edit_billing_package/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            <?php if($post->active){ ?>
                                                <a href="<?php echo site_url('admin/billing/disable_billing_package/'.$post->id); ?>" class="btn btn-xs default confirmation_link">
                                                    <i class="fa fa-eye-slash"></i> disable &nbsp;&nbsp; 
                                                </a>

                                                <a href="<?php echo site_url('admin/billing/menu_pairing/'.$post->id); ?>" class="btn btn-xs btn-success">
                                                    <i class="fa fa-book"></i> Menu Pairing &nbsp;&nbsp; 
                                                </a>
                                            <?php }else{ ?>
                                                <a href="<?php echo site_url('admin/billing/activate_billing_package/'.$post->id); ?>" class="btn btn-xs green confirmation_link">
                                                    <i class="icon-eye"></i> Activate &nbsp;&nbsp; 
                                                </a>
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
                        <?php if($posts):?>
                            <button class="btn btn-sm btn-info confirmation_bulk_action" name='btnAction' value='set_as_default' data-toggle="confirmation" data-placement="top"> <i class='fa fa-check-square-o'></i> Set as Default</button>
                            <button class="btn btn-sm btn-success confirmation_bulk_action" name='btnAction' value='bulk_activate' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye'></i> Bulk Activate</button>
                            <button class="btn btn-sm btn-default confirmation_bulk_action" name='btnAction' value='bulk_disable' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye-slash'></i> Bulk Disable</button>
                        <?php endif;?>
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No Billing Packages to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
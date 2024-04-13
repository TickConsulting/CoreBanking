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
            <div class="portlet-body form logos">

                <?php if(!empty($post)){ ?>
                    <?php echo form_open('admin/banks/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Banks</p>
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
                                        Sender ID
                                    </th>
                                    <th>
                                        Home Page Controller
                                    </th>
                                    <th>
                                        URL
                                    </th>
                                    <th>
                                        Trial Days
                                    </th>
                                    <th>
                                        Bill Start Number
                                    </th>
                                    <th>
                                        Color Pallete
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0);?>
                                    <tr>
                                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo $post->application_name; ?></td>
                                        <td><?php echo $post->sender_id; ?></td>
                                        <td><?php echo $post->home_page_controller; ?></td>
                                        <td><?php echo $post->protocol.''.$post->url; ?></td>
                                        <td><?php echo $post->trial_days; ?> Days</td>
                                        <td><?php echo $post->bill_number_start; ?></td>
                                        <td>
                                            <span class='label tooltips' data-placement="top" data-original-title="Primary Color: <?php echo $post->primary_color; ?>" style='background:<?php echo $post->primary_color; ?>;'> &nbsp;&nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Secondary Color: <?php echo $post->secondary_color; ?>" style='background:<?php echo $post->secondary_color; ?>;'> &nbsp;&nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Tertiary Color: <?php echo $post->tertiary_color; ?>" style='background:<?php echo $post->tertiary_color; ?>;'> &nbsp;&nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Text Color: <?php echo $post->text_color; ?>" style='background:<?php echo $post->text_color; ?>;' >&nbsp;&nbsp;</span>
                                            <span class='label tooltips' data-placement="top" data-original-title="Link Color: <?php echo $post->link_color; ?>" style='background:<?php echo $post->link_color; ?>;' >&nbsp;&nbsp;</span>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->active){
                                                    echo "<span class='label label-success'>Active</span>";
                                                }else{
                                                    echo "<span class='label label-default'>Hidden</span>";
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('admin/settings/edit/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-eye"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                        </td>
                                    </tr>
                            </tbody>
                        </table>

                        <h4><?php echo $post->application_name; ?> Logos </h4>
                        <div class="row">

                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 text-center">
                                Favicon:
                               <div class="col-md-12">
                                    <div class="mt-overlay-1 mt-scroll-up">
                                        <img src="<?php echo site_url($path.'/'.$post->favicon);?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 text-center">
                                Logo:
                               <div class="col-md-12">
                                    <div class="mt-overlay-1 mt-scroll-up">
                                        <img src="<?php echo site_url($path.'/'.$post->logo);?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 text-center">
                                Responsive Logo:
                               <div class="col-md-12">
                                    <div class="mt-overlay-1 mt-scroll-up">
                                        <img src="<?php echo site_url($path.'/'.$post->responsive_logo);?>" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 text-center">
                                <?php echo $post->application_name; ?> Paper Logo Header:
                               <div class="col-md-12">
                                    <div class="mt-overlay-1 mt-scroll-up">
                                        <img src="<?php echo site_url($path.'/'.$post->paper_header_logo);?>" />
                                    </div>
                                </div>
                            </div>


                            

                        </div>

                        <div class="row" style="margin-top:5px;">

                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 text-center">
                                <?php echo $post->application_name; ?> Paper Logo Footer:
                               <div class="col-md-12">
                                    <div class="mt-overlay-1 mt-scroll-up">
                                        <img src="<?php echo site_url($path.'/'.$post->paper_footer_logo);?>" />
                                    </div>
                                </div>
                            </div>

                             <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 text-center">
                                <?php echo $post->application_name; ?> Admin Login Logo:
                               <div class="col-md-12">
                                    <div class="mt-overlay-1 mt-scroll-up">
                                        <img src="<?php echo site_url($path.'/'.$post->admin_login_logo);?>" />
                                    </div>
                                </div>
                            </div>


                             <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 text-center">
                                <?php echo $post->application_name; ?> Group Login Logo:
                               <div class="col-md-12">
                                    <div class="mt-overlay-1 mt-scroll-up">
                                        <img src="<?php echo site_url($path.'/'.$post->group_login_logo);?>" />
                                    </div>
                                </div>
                            </div>
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
                            No Settings to display.
                        </p>
                    </div>
                <?php } ?>

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
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

                <?php if(!empty($posts)){ ?>
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
                                        Details
                                    </th>
                                    <th>
                                        Logo
                                    </th>
                                    <th>
                                        Countries
                                    </th>
                                    <th>
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = $this->uri->segment(5, 0);
                                foreach ($posts as $key => $post) {?>
                                    <tr>
                                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php 
                                                echo 'Name: '.$post->name.'<br/>'; 
                                                echo 'Channel Number: '.$post->channel;
                                            ?>
                                        </td>
                                        <td><?php if(is_file($path.'/'.$post->logo)){
                                            echo '<img src="'.base_url().$path.'/'.$post->logo.'" style="width: 50px;">';
                                        }else{
                                            echo '--no file--';
                                        } ?></td>
                                        <td>
                                            <?php $country_ids = isset($country_wallet_pairings[$post->id])?$country_wallet_pairings[$post->id]:'';
                                                if($country_ids){
                                                    foreach ($country_ids as $country_id) {
                                                        echo $countries[$country_id].'<br/>';
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('admin/wallets/edit/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-eye"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            &nbsp;&nbsp;
                                            <a href="<?php echo site_url('admin/wallets/delete/'.$post->id); ?>" class="btn btn-xs red confirmation_link">
                                                <i class="icon-trash"></i> Delete &nbsp;&nbsp; 
                                            </a>
                                        </td>
                                    </tr>
                                <?php }?>
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
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No wallet channels to display.
                        </p>
                    </div>
                <?php } ?>

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
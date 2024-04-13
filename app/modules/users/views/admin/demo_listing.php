<style type="text/css">
    td>ul>li>a{
        color:#000;
    }
    td>ul>li>a:hover{
        text-decoration: none;
    }
    td>ul{
        padding-left: 15px !important;
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
                <div class="clearfix"></div>


				<?php if(!empty($posts)){ ?>
			        <?php echo form_open('admin/users/action', ' id="form"  class="form-horizontal"'); ?> 

                    <?php if ( ! empty($pagination['links'])): ?>
                        <div class="row col-md-12">
                            <p class="paging">Showing from <span class="greyishBtn"><?php echo $pagination['from']; ?></span> to <span class="greyishBtn"><?php echo $pagination['to']; ?></span> of <span class="greyishBtn"><?php echo $pagination['total']; ?></span> Users</p>
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
                                        User Name
                                    </th>
                                    <th>
                                        Contact
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <!-- <th width="">
                                        Actions
                                    </th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=$this->uri->segment(5, 0); foreach($posts as $post): ?>
                                    <tr>
                                        <td><input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" /></td>
                                        <td><?php echo $i+1;?></td>
                                        <td><?php echo $post->first_name.' '.$post->middle_name.' '.$post->last_name; ?></td>
                                        <td><?php echo $post->phone.'<br/>'.$post->email; ?></td>
                                        <td>
                                            <?php if($post->active)
                                            {
                                                echo '<span class="label label-sm label-primary"> Active</spam>';
                                            }else{
                                                echo '<span class="label label-sm label-danger"> Inactive</spam>';

                                                }?>
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

			        <?php echo form_close(); ?>
			    <?php }else{ ?>
			        <div class="alert alert-info">
			            <h4 class="block">Information! No records to display</h4>
			            <p>
			                No User records to display.
			            </p>
			        </div>
			    <?php } ?>

			</div>

		</div>


	</div>

</div>
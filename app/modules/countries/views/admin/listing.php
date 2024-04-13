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
                                    <label>Country Name</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                        <i class="fa fa-book"></i>
                                        </span>
                                        <?php echo form_input('name',$this->input->get('name'),'class="form-control input-sm" placeholder="Country Name"'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Currency</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                            </span>
                                            <?php echo form_input('currency',$this->input->get('currency'),'class="form-control input-sm" placeholder="Currency"'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Calling Code</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <i class="fa fa-mobile"></i>
                                            </span>
                                            <?php echo form_input('calling_code',$this->input->get('calling_code'),'class="form-control input-sm" placeholder="Calling Code"'); ?>
                                        </div>
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

                <?php if(!empty($posts)){ ?>
                    <?php echo form_open('admin/countries/action', ' id="form"  class="form-horizontal"'); ?> 

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
                                        Flag
                                    </th>
                                    <th>
                                        Code
                                    </th>
                                    <th>
                                        Currency
                                    </th>
                                    <th>
                                        Currency Code
                                    </th>
                                    <th>
                                        Calling Code
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th width="30%">
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
                                        <td>{group:theme:image file="flags/<?php echo strtolower($post->code).'.png'; ?>" width="30px;"}</td>
                                        <td><?php echo $post->code; ?></td>
                                        <td>
                                            <?php echo $post->currency; ?>
                                        </td>
                                        <td>
                                            <?php echo $post->currency_code; ?>
                                        </td>
                                        <td>
                                            <?php echo $post->calling_code;?>
                                        </td>
                                        <td>
                                            <?php 
                                                if($post->active){
                                                    echo "<span class='label label-success'>Active</span>";
                                                }else{
                                                    echo "<span class='label label-default'>Hidden</span>";
                                                }

                                                if($post->default_country){
                                                    echo "<span class='margin-left-5 label label-info'>Default</span>";
                                                }
                                            ?>
                                        </td>

                                        <td>
                                            <a href="<?php echo site_url('admin/countries/edit/'.$post->id); ?>" class="btn btn-xs default">
                                                <i class="icon-pencil"></i> Edit &nbsp;&nbsp; 
                                            </a>
                                            <?php if($post->active){ ?>
                                                <a href="<?php echo site_url('admin/countries/hide/'.$post->id); ?>" class="btn btn-xs default confirmation_link">
                                                    <i class="fa fa-eye-slash"></i> Hide &nbsp;&nbsp; 
                                                </a>
                                            <?php }else{ ?>
                                                <a href="<?php echo site_url('admin/countries/activate/'.$post->id); ?>" class="btn btn-xs green confirmation_link">
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
                            <button class="btn btn-sm btn-default confirmation_bulk_action" name='btnAction' value='bulk_hide' data-toggle="confirmation" data-placement="top"> <i class='fa fa-eye-slash'></i> Bulk Hide</button>
                        <?php endif;?>
                    <?php echo form_close(); ?>
                <?php }else{ ?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                            No Countries to display.
                        </p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
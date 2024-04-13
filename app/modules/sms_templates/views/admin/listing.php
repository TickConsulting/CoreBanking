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
            <div class="row" >
                <div class="col-md-12"> 
                  <?php 
                        $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
                    ?> 
                    <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url().'/'.$query;?>" target="_blank"><i class='fa fa-file'></i>&nbsp;
                        <?php echo translate('Export To Excel'); ?>
                    </a> 
                </div>
            </div>

            <div class="portlet-body form">
               <?php if($posts):?>
            <?php echo form_open('admin/sms_templates/action', ' id="form"  class="form-horizontal"'); ?> 
                
                <table class="table table-bordered table-condensed table-striped table-hover table-header-fixed">
                    <thead>
                        <tr>
                            <th width='2%'>
                                 <input type="checkbox" name="check" value="all" class="check_all">
                            </th>
                            <th>
                                #
                            </th>
                            <th width="30%">
                               Title
                            </th>
                            <th width="30%">
                                Slug 
                            </th>
                            <th width="30%">
                                Language 
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i=$this->uri->segment(5, 0);
                        foreach($posts as $post):
                        ?>
                            <tr>
                                <td>
                                    <input name='action_to[]' type="checkbox" class="checkboxes" value="<?php echo $post->id; ?>" />
                                </td>
                                <td><?php echo $i+1;?></td>
                                <td><?php echo $post->title;?></td>
                                <td><?php echo $post->slug;?></td>
                                <td><?php echo $languages[$post->language_id];?></td>
                                <td >
                                    <a href="<?php echo site_url('admin/sms_templates/edit/'.$post->id);?>" class="btn btn-xs default">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                    <a href="<?php echo site_url('admin/sms_templates/delete/'.$post->id);?>" class="btn btn-xs btn-danger confirmation_link" data-toggle="confirmation" data-placement="left">
                                        <i class="icon-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php $i++; endforeach; 
                        ?>
                    </tbody>
                </table>

                <div class="clearfix"></div>

                <?php if($posts):?>
                    <button class="btn btn-sm btn-danger confirmation_bulk_action" name='btnAction' value='bulk_delete' data-toggle="confirmation" data-placement="top"> <i class='icon-trash'></i> Bulk Delete</button>
                <?php endif;?>
                
                <?php echo form_close();?>
            <?php else:?>
                    <div class="alert alert-info">
                        <h4 class="block">Information! No records to display</h4>
                        <p>
                           No SMS Templates to display
                        </p>
                    </div>
                <?php endif;?>

            </div>

        </div>



    </div>

</div>

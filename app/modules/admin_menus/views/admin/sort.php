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
                <div id="sort">
                    <div class="dd" id="admin_menu_sort">
                        <ol class="dd-list">

                            <?php foreach($posts as $post): ?>
                                <li class="dd-item" data-id="<?php echo $post->id?>">
                                    <div class="dd-handle"> 
                                        <?php 
                                            echo $post->name;
                                            echo $post->active==1?' - Active':' - Hidden'; 
                                        ?>
                                        
                                    </div>
                                    <?php $this->admin_menus_m->display_children($post->id); ?>
                                </li>
                            <?php endforeach;?>
                        </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h3>Serialised Output (per list)</h3>
                        <textarea id="admin_menu_sort_output" class="form-control col-md-12 margin-bottom-10"></textarea>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
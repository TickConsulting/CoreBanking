<div class="row">
    <div id="default" class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                   <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form">
                <form class="form-horizontal">
                    <div class="form-group form-group-lg">
                        <div class="col-sm-9">
                           
                            <select id="group_search" class="form-control group-search">
                                <?php 
                                    if($group){
                                ?>
                                    <option value="<?php echo $group_id; ?>" selected="selected"><?php echo $group->name; ?></option>
                                <?php 
                                    }else{
                                ?>
                                    <option value="" selected="selected">Search for a Group</option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div id="group_profile" class="col-sm-9">
                    </div>
                    <div class="col-sm-9">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        <?php if($group_id){ ?>
            var group_id = "<?php echo $group_id; ?>";
            load_group_profile(group_id);
        <?php } ?>
        $('#group_search').change(function(){                  
            load_group_profile($(this).val());
        });
    });

    function load_group_profile(group_id){
        $('#group_profile').html("");
            $('#group_profile').css("min-height","100px");
            App.blockUI({
                target: '#group_profile',
                overlayColor: 'white',
                animate: true
            });
            $.ajax({
            type: "GET",
            url: '<?php echo base_url("admin/groups/ajax_view/"); ?>/'+group_id,
            dataType : "html",
                success: function(response) {
                    $('#group_profile').html(response);
                    App.unblockUI('#group_profile');
                }
            }
        );
    }
</script>
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
                            <select id="user_search" class="form-control user-search">
                                <option value="" selected="selected">Search for a User</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div id="user_profile" class="col-sm-9">
                    </div>
                    <div class="col-sm-3">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#user_search').change(function(){
            $('#user_profile').html("");
            $('#user_profile').css("min-height","100px");
            App.blockUI({
                target: '#user_profile',
                overlayColor: 'white',
                animate: true
            });
            var user_id = $(this).val();
            $.ajax({
            type: "GET",
            url: '<?php echo base_url("admin/users/ajax_view/"); ?>/'+user_id,
            dataType : "html",
                success: function(response) {
                    $('#user_profile').html(response);
                    App.unblockUI('#user_profile');
                }
            }
            );
        });
    });
</script>
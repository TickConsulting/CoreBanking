<div class="search-page search-content-1">
    <?php echo form_open('group/members', ' id="form"  class="form-horizontal" method="get" '); ?>
        <div class="search-bar bordered">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <input value="<?php echo $this->input->get('name'); ?>" type="text" name="name" class="form-control" placeholder="<?php echo translate('Search for') ?>..." autocomplete="off">
                        <span class="input-group-btn">
                            <button class="btn btn-primary uppercase bold" type="submit" style="border-top-left-radius:0px;border-bottom-left-radius:0px;">
                                <?php echo $this->lang->line("search")?:"Search";?>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php echo form_close(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="search-container bordered" id="member_directory_space" style="min-height: ">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(window).on('load',function(){
        load_member_directory();
    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_member_directory(){
        mApp.block('#member_directory_space', {
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/members/get_members_directory/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#member_directory_space').html(response);
                    $('.select2').select2({width:"100%"});
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#member_directory_space');
                }
            }
        );
    }
</script>
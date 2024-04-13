<style type="text/css">
    .small-text{
        font-size:9px;
    }
    .m-alert{
        margin: 0px 20px 20px 20px;
    }
</style>
<!-- BEGIN PAGE BASE CONTENT -->
<div class="inbox" id="custom_inbox">
    <script>
        $('.m-portlet__body').addClass('px-0');
    </script>
    <div class="row">
        <div class="col-md-2">
            <div class="inbox-sidebar">
                <ul class="inbox-nav">
                    <li data-type="inbox" data-title="Received" class="received active">
                        <a href="javascript:;" data-type="Received" data-title="Received" class="received"> 
                            <i class="mdi mdi-send"></i>
                            <?php echo translate('Received');?>
                            <span class="badge badge-success badge-pill inbox-message"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-10 pr-5">
            <div class="inbox-body">
                <div class="inbox-header">
                    <!-- <h1 class="pull-left"><?php if(preg_match('/view/',current_url())){ echo 'View Message';}else{echo 'Received';}?></h1> -->
                    <!--<form class="form-inline pull-right search-form" action="<?php echo current_url();?>">
                        <div class="input-group input-medium">
                            <input type="text" name="params" value="<?php echo $this->input->get('params');?>" class="form-control" placeholder="Search....">
                            <span class="input-group-btn">
                                <button type="submit" class="btn green">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>-->
                </div>
                <div class="inbox-content"> </div>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE BASE CONTENT -->

<?php $url_code = 'ajax'; ?>



<script type="text/javascript">
    var received_smses_url = '<?php echo site_url($url_code.'/sms/get_member_received_sms_listing');?>';
    var mAppInbox = function () {
        var content = $('.inbox-content');
        var listListing = '';
        var loadInbox = function (el, name) {
            var title = el.attr('data-title');
            listListing = name;            
            mApp.block( content,{
                overlayColor: 'none',
                animate: true,
                message: "Loading SMSes...."
            });
            toggleButton(el);
            $.ajax({
                type: "GET",
                cache: false,
                url: '<?php echo site_url('ajax/sms/get_member_received_sms_listing');?>',
                dataType: "html",
                success: function(res) {
                    toggleButton(el);
                    mApp.unblock('.inbox-content');
                    $('.inbox-nav > li.active').removeClass('active');
                    el.closest('li').addClass('active');
                    $('.inbox-header > h1').text(title);
                    content.html(res);
                    list_mails();
                },
                error: function(xhr, ajaxOptions, thrownError){
                    toggleButton(el);
                },
                async: true
            });

            // handle group checkbox:
            jQuery('body').on('change', '.mail-group-checkbox', function () {
                var set = jQuery('.mail-checkbox');
                var checked = jQuery(this).is(":checked");
                jQuery(set).each(function () {
                    $(this).attr("checked", checked);
                });
                jQuery.uniform.update(set);
            });
        }

        var toggleButton = function(el) {
            if (typeof el == 'undefined') {
                return;
            }
            if (el.attr("disabled")) {
                el.attr("disabled", false);
            } else {
                el.attr("disabled", true);
            }
        }

        return {
            //main function to initiate the module
            init: function () {
                // handle inbox listing
                $('.inbox-nav > li.received').click(function () {
                    loadInbox($(this), 'received');
                });

                $(document).on('click','.pagination a',function(e){
                    received_smses_url = $(this).attr("href");
                    loadInbox($(this), 'inbox');
                    e.preventDefault();
                });
                var pathname = window.location.pathname;

                //handle loading content based on URL parameter
                if (getUrlParameter("a") === "compose") {
                    loadCompose();
                }else {
                   $('.inbox-nav > li:first').click();
                }
            }

        };

        function list_mails(){
            var content = $('.inbox-content');
            $('.mark-as-read').click(function(){
                var url = '<?php echo site_url($url_code.'/emails/action');?>';
                var checkValues = $('input[name="list-mails[]"]:checked').map(function()
                {
                    return $(this).val();
                }).get();
                $.ajax({
                    url: url,
                    type: 'post',
                    data: { ids: checkValues,'action':'bulk_mark_as_read'},
                    async:true,
                    success:function(data){
                        loadInbox($(this), 'inbox');
                    }
                });
            });
        }

    }();

jQuery(document).ready(function() {
    mAppInbox.init();
});
</script>


<style type="text/css">
    .small-text{
        font-size:9px;
    }
    .m-alert{
        margin: 2px 8px !important;
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
                <?php if($this->application_settings->enable_compose_sms || preg_match('/tick/i',$this->application_settings->application_name)):?>
                    <a href="javascript:;" data-title="Compose" class="btn btn-primary m-btn--pill mb-4 compose-btn">
                        <i class="fa fa-edit"></i> 
                        <?php
                            $default_message='Compose';
                            $this->languages_m->translate('compose',$default_message);
                        ?>
                    </a>
                <?php endif; ?>
                <ul class="inbox-nav">
                    <li data-type="inbox" data-title="Sent" class="sent active">
                        <a href="javascript:;" data-type="Sent" data-title="Sent" class="sent"> 
                            <i class="mdi mdi-send"></i>
                            <?php echo translate('Sent SMSes');?>
                            <span class="badge badge-success badge-pill inbox-message"></span>
                        </a>
                    </li>

                    <li data-type="Queued" data-title="Queued" class="queued">
                        <a href="javascript:;" data-type="queued" data-title="Queued" class="queued"> 
                        <i class="mdi mdi-progress-clock"></i>
                        <?php echo translate('Queued');?>
                         </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-10 pr-5">
            <div class="inbox-body">
                <div class="inbox-header">
                    <h1 ><?php if(preg_match('/view/',current_url())){ echo translate('View Message');}else{echo translate('Sent');}?></h1>
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

<?php
 if(preg_match('/member/',$this->uri->segment(1))){
        $url_code = 'member';
    }else{
          $url_code = 'ajax';
        }
        ?>



<script type="text/javascript">
    var sent_smses_url = '<?php echo site_url($url_code.'/sms/get_sms_listing');?>';
    var queued_smses_url = '<?php echo site_url($url_code.'/sms/get_queued_sms_listing');?>';
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
                url: sent_smses_url,
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

        var loadQueued = function (el, name) {
            var title = el.attr('data-title');
            listListing = name;            
            mApp.block( content,{
                overlayColor: 'none',
                animate: true,
                message: "Loading queued SMSes...."
            });
            toggleButton(el);
            $.ajax({
                type: "GET",
                cache: false,
                url: queued_smses_url,
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

        var loadCompose = function (el) {
            var url = '<?php echo site_url($url_code.'/sms/compose');?>';
            mApp.block(content,{
                overlayColor: 'none',
                animate: true
            });

            toggleButton(el);

            // load the form via ajax
            $.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                success: function(res){
                    mApp.unblock(content);
                    toggleButton(el);
                    $('.inbox-nav > li.active').removeClass('active');
                    $('.inbox-header > h1').text('Compose');
                    content.html(res);
                    submit_form();
                },
                error: function(xhr, ajaxOptions, thrownError)
                {
                    toggleButton(el);
                },
                async: true
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
                $('.inbox').on('click', '.compose-btn', function () {
                    loadCompose($(this));
                });

                // handle inbox listing
                $('.inbox-nav > li.sent').click(function () {
                    loadInbox($(this), 'sent');
                });

                $('.inbox-nav > li.queued').click(function () {
                    loadQueued($(this), 'queued');
                });

                

                $(document).on('click','.pagination a',function(e){
                    sent_smses_url = $(this).attr("href");
                    loadInbox($(this), 'inbox');
                    e.preventDefault();
                });
                var pathname = window.location.pathname;
                console.log(getUrlParameter("a")+" url");
                //handle loading content based on URL parameter
                if (getUrlParameter("a") === "compose") {
                    loadCompose();
                }else if(getUrlParameter("a") === "queued" || getUrlParameter("a") === "queued_sms") {
                    loadQueued($('.inbox-nav > li.queued'),'queued');
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

function submit_form(){
    $(document).on('click','.cancel_form',function(e){
        $('.inbox-nav > li:first').click();
        console.log('it was clicked');
        $(this).preventDefault();
    });
    $(".m-select2").select2({
        placeholder:{
            id: '-1',
            text: "--Select option--",
        }, 
    });
    var send_to = $('select[name="send_to"]').val();        
    if(send_to){
        $('select[name="send_to"]').trigger('change');
    }

    $(document).on('change','select[name="send_to"]',function(){
        var send_to = $(this).val();
        if(send_to==1){
            $('.member_input').slideUp();
        }else if(send_to==2){
            $('.member_input').slideDown();
        }else{
            $('.member_input').hide();
        }
        $('.m-select2').select2();
    });
}

jQuery(document).ready(function() {
    mAppInbox.init();
    SnippetComposeSms.init();
});
</script>


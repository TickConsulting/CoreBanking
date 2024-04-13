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
                <?php if($this->group->enable_compose_email || preg_match('/eazzykikundi/i',$this->application_settings->application_name)): ?>
                    <a href="javascript:;" data-title="Compose" class="btn btn-primary m-btn--pill mb-4 compose-btn">
                        <i class="fa fa-edit"></i> 
                        <?php echo translate('compose');
                        ?>
                    </a>
                <?php endif; ?>
                <ul class="inbox-nav">
                    <li data-type="inbox" data-title="<?php echo translate('Inbox');?>" class="inbox active">
                        <a href="javascript:;" data-type="inbox" data-title="<?php echo translate('Inbox');?>" class="inbox"> 
                            <i class="mdi mdi-inbox"></i>
                            <?php echo translate('Inbox');
                            ?>
                            <span class="badge badge-success badge-pill inbox-message"></span>
                        </a>
                    </li>

                    <li data-type="sent" data-title="<?php echo translate('Sent');?>" class="sent">
                        <a href="javascript:;" data-type="sent" data-title="<?php echo translate('Sent');?>" class="sent"> 
                        <i class="mdi mdi-send"></i>
                        <?php echo translate('sent');
                        ?>
                         </a>
                    </li>
                    <li data-type="drafts" data-title="<?php echo translate('Drafts');?>" class="drafts">
                        <a href="javascript:;" data-type="drafts" data-title="<?php echo translate('Drafts');?>" class="drafts"> 
                        <i class="mdi mdi-file"></i>
                        <?php echo translate('drafts');
                        ?> 
                            <span class="badge badge-success badge-pill draft-message"></span>
                        </a>
                    </li>
                    <li data-type="outbox" data-title="<?php echo translate('Outbox');?>" class="queued">
                        <a href="javascript:;" data-type="outbox" data-title="<?php echo translate('Outbox');?>" class="queued"> 
                            <i class="mdi mdi-email"></i>
                            <?php echo translate('outbox');
                            ?>

                        <span class="badge badge-success badge-pill outbox-message"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-10 pr-5">
            <div class="inbox-body">
                <div class="inbox-header">
                    <h1 class="pull-left"><?php if(preg_match('/view/',current_url())){ echo 'View Message';}else{echo 'Inbox';}?></h1>
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
      $url_code = 'group';
    }
?>



<script type="text/javascript">
    var mAppInbox = function () {


    var content = $('.inbox-content');
    var listListing = '';

    var loadInbox = function (el, name) {
        var url = '<?php echo site_url($url_code.'/emails/app_app_inbox');?>';
        var title = el.attr('data-title');
        listListing = name;

        mApp.block( content,{
            overlayColor: 'none',
            animate: true
        });

        toggleButton(el);

        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            success: function(res) 
            {
                toggleButton(el);

                mApp.unblock('.inbox-content');

                $('.inbox-nav > li.active').removeClass('active');
                el.closest('li').addClass('active');
                $('.inbox-header > h1').text(title);

                content.html(res);

                // if (Layout.fixContentHeight) {
                //     //Layout.fixContentHeight();
                // }

                //mApp.initUniform();
                list_mails();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
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

    var loadMessage = function (el, name, resetMenu) {
        var url = '<?php echo site_url($url_code.'/emails/app_inbox_view');?>';

        mApp.block(content,{
            overlayColor: 'none',
            animate: true
        });

        toggleButton(el);

        var message_id = el.parent('tr').attr("data-messageid");  
        
        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            data: {'message_id': message_id},
            success: function(res) 
            {
                mApp.unblock(content);

                toggleButton(el);

                if (resetMenu) {
                    $('.inbox-nav > li.active').removeClass('active');
                }
                $('.inbox-header > h1').text('View Message');

                content.html(res);
                //Layout.fixContentHeight();
                //mApp.initUniform();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: true
        });
    }

    var loadMessageview = function(el, name, resetMenu){
        var url = '<?php echo site_url($url_code.'/emails/app_inbox_view');?>';

        mApp.block(content,{
            overlayColor: 'none',
            animate: true
        });

        toggleButton(el);

        var message_id = '<?php echo $this->uri->segment(4);?>'; 
        
        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            data: {'message_id': message_id},
            success: function(res) 
            {
                mApp.unblock(content);

                toggleButton(el);

                if (resetMenu) {
                    $('.inbox-nav > li.active').removeClass('active');
                }
                $('.inbox-header > h1').text('View Message');

                content.html(res);
                //Layout.fixContentHeight();
                //mApp.initUniform();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: true
        });
    }

    var loadQueuedMessage = function (el, name, resetMenu) {
        var url = '<?php echo site_url($url_code.'/emails/app_inbox_view_queued');?>';

        mApp.block(content,{
            overlayColor: 'none',
            animate: true
        });

        toggleButton(el);

        var message_id = el.parent('tr').attr("data-messageid");  
        
        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            data: {'message_id': message_id},
            success: function(res) 
            {
                App.unblock(content);

                toggleButton(el);

                if (resetMenu) {
                    $('.inbox-nav > li.active').removeClass('active');
                }
                $('.inbox-header > h1').text('View Message');

                content.html(res);
                //Layout.fixContentHeight();
                //mApp.initUniform();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: true
        });
    }

    var loadDraftMessage = function (el, name, resetMenu) {
        var url = '<?php echo site_url($url_code.'/emails/app_inbox_view_draft');?>';

        mApp.block(content,{
            overlayColor: 'none',
            animate: true
        });

        toggleButton(el);

        var message_id = el.parent('tr').attr("data-messageid");  
        
        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            data: {'message_id': message_id},
            success: function(res) 
            {
                mApp.unblock(content);

                toggleButton(el);

                if (resetMenu) {
                    $('.inbox-nav > li.active').removeClass('active');
                }
                $('.inbox-header > h1').text('View Message');

                content.html(res);
                //Layout.fixContentHeight();
                //mApp.initUniform();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: true
        });
    }

    var loadSentMessage = function (el, name, resetMenu) {
        var url = '<?php echo site_url($url_code.'/emails/app_inbox_view_sent');?>';

        mApp.block(content,{
            overlayColor: 'none',
            animate: true
        });

        toggleButton(el);

        var message_id = el.parent('tr').attr("data-messageid");  
        
        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            data: {'message_id': message_id},
            success: function(res) 
            {
                mApp.unblock(content);

                toggleButton(el);

                if (resetMenu) {
                    $('.inbox-nav > li.active').removeClass('active');
                }
                $('.inbox-header > h1').text('View Message');

                content.html(res);
                //Layout.fixContentHeight();
                //mApp.initUniform();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: true
        });
    }

    var initWysihtml5 = function () {
        $('.inbox-wysihtml5').wysihtml5({
            "stylesheets": ["<?php echo base_url();?>templates/admin_themes/groups/css/bootstrap-wysihtml5/bootstrap-wysihtml5.css"]
        });
    }

    var initFileupload = function () {

        $('#fileupload').fileupload({
            url: '<?php echo site_url($url_code.'/emails/attachments')?>',
            autoUpload: true
        });

        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: '<?php echo site_url($url_code.'/emails/attachments')?>',
                type: 'HEAD'
            }).fail(function () {
                $('<span class="alert alert-error"/>')
                    .text('Upload server currently unavailable - ' +
                    new Date())
                    .appendTo('#fileupload');
            });
        }
         else {
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
            $.ajax({
                // Uncomment the following to send cross-domain cookies:
                //xhrFields: {withCredentials: true},
                url: $('#fileupload').fileupload('option', 'url'),
                dataType: 'json',
                context: $('#fileupload')[0]
            }).always(function () {
                $(this).removeClass('fileupload-processing');
            }).done(function (result) {
                $(this).fileupload('option', 'done')
                    .call(this, $.Event('done'), {result: result});
            });
        }
    }

    var initSelect2 = function(){
        $('.select2-multiple').select2({ allowClear:true, });
    }

    var loadCompose = function (el) {
        var url = '<?php echo site_url($url_code.'/emails/compose');?>';

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
            success: function(res) 
            {
                mApp.unblock(content);
                toggleButton(el);

                $('.inbox-nav > li.active').removeClass('active');
                $('.inbox-header > h1').text('Compose');

                content.html(res);

                //initFileupload();
                
                wysiwyg_editor.init();
                
                $(".m-select2").select2({
                    placeholder:{
                        id: '-1',
                        text: "--Select option--",
                    }, 
                    width: "100%"
                });
                // initSelect2();

                $('.inbox-wysihtml5').focus();
                //Layout.fixContentHeight();
                //mApp.initUniform();

                submit_form();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: true
        });
    }

    var loadSent = function (el, name) {
        var url = '<?php echo site_url($url_code.'/emails/sent_emails');?>';
        var title = el.attr('data-title');
        listListing = name;

        mApp.block(content,{
            overlayColor: 'none',
            animate: true
        });

        toggleButton(el);

        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            success: function(res) 
            {
                toggleButton(el);

                mApp.unblock('.inbox-content');

                $('.inbox-nav > li.active').removeClass('active');
                el.closest('li').addClass('active');
                $('.inbox-header > h1').text(title);

                content.html(res);

                //if (Layout.fixContentHeight) {
                    //Layout.fixContentHeight();
                //}

                //mApp.initUniform();
                list_mails();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
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

    var loadOutbox = function (el, name) {
        var url = '<?php echo site_url($url_code.'/emails/outbox_emails');?>';
        var title = el.attr('data-title');
        listListing = name;

        mApp.block(content,{
            overlayColor: 'none',
            animate: true
        });

        toggleButton(el);

        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            success: function(res) 
            {
                toggleButton(el);

                mApp.unblock('.inbox-content');

                $('.inbox-nav > li.active').removeClass('active');
                el.closest('li').addClass('active');
                $('.inbox-header > h1').text(title);

                content.html(res);

                //if (Layout.fixContentHeight) {
                    //Layout.fixContentHeight();
                //}

                //mApp.initUniform();
                list_mails();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
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

    var loadDrafts = function (el, name) {
        var url = '<?php echo site_url($url_code.'/emails/draft_emails');?>';
        var title = el.attr('data-title');
        listListing = name;

        mApp.block(content,{
            overlayColor: 'none',
            animate: true
        });

        toggleButton(el);

        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "html",
            success: function(res) 
            {
                toggleButton(el);

                mApp.unblock('.inbox-content');

                $('.inbox-nav > li.active').removeClass('active');
                el.closest('li').addClass('active');
                $('.inbox-header > h1').text(title);

                content.html(res);

                //if (Layout.fixContentHeight) {
                    //Layout.fixContentHeight();
                //}

                //mApp.initUniform();
                list_mails();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
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

    var loadReply = function (el) {
        var messageid = $(el).attr("data-messageid");
        var url = '<?php echo site_url($url_code.'/emails/app_inbox_reply');?>';
        
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
            data:{'message_id':messageid},
            success: function(res) 
            {
                mApp.unblock(content);
                toggleButton(el);

                $('.inbox-nav > li.active').removeClass('active');
                $('.inbox-header > h1').text('Reply');

                content.html(res);
                //$('[name="message"]').val($('#reply_email_content_body').html());

                handleCCInput(); // init "CC" input field

                //initFileupload();
                // initWysihtml5();
                //Layout.fixContentHeight();
                //mApp.initUniform();
                initSelect2();


                submit_form();
            },
            error: function(xhr, ajaxOptions, thrownError)
            {
                toggleButton(el);
            },
            async: true
        });
    }

    var handleCCInput = function () {
        var the = $('.inbox-compose .mail-to .inbox-cc');
        var input = $('.inbox-compose .input-cc');
        the.hide();
        input.show();
        $('.close', input).click(function () {
            input.hide();
            the.show();
        });
        initSelect2();
    }

    var handleBCCInput = function () {

        var the = $('.inbox-compose .mail-to .inbox-bcc');
        var input = $('.inbox-compose .input-bcc');
        the.hide();
        input.show();
        $('.close', input).click(function () {
            input.hide();
            the.show();
        });
        initSelect2();
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

            // handle compose btn click
            $('.inbox').on('click', '.compose-btn', function () {
                loadCompose($(this));
            });

            //handle sent emails
            $(document).on('click','.sent',function(e){
                loadSent($(this));
            });

            //handle Outbox emails
            $(document).on('click','.queued',function(e){
                loadOutbox($(this));
            });

            //handle draft emails
            $(document).on('click','.drafts',function(e){
                loadDrafts($(this));
            });


            // handle reply and forward button click
            $('.inbox').on('click', '.reply-btn', function () {
                loadReply($(this));
            });

            // handle view message
            $('.inbox').on('click', '.view-message', function () {
                loadMessage($(this));
            });

             // handle view message from outbox list
            $('.inbox').on('click', '.view-queued-message', function () {
                loadQueuedMessage($(this));
            });

            // handle view message from outbox list
            $('.inbox').on('click', '.view-draft-message', function () {
                loadDraftMessage($(this));
            });

            // handle view message from sent list
            $('.inbox').on('click', '.view-sent-message', function () {
                loadSentMessage($(this));
            });

            // handle inbox listing
            $('.inbox-nav > li.inbox').click(function () {
                loadInbox($(this), 'inbox');
            });

            //handle compose/reply cc input toggle
            $('.inbox-content').on('click', '.mail-to .inbox-cc', function () {
                handleCCInput();
            });

            //handle compose/reply bcc input toggle
            $('.inbox-content').on('click', '.mail-to .inbox-bcc', function () {
                handleBCCInput();
            });

            var pathname = window.location.pathname;

            //handle loading content based on URL parameter
            if (getUrlParameter("a") === "view") {
                loadMessage();
            } else if (getUrlParameter("a") === "compose") {
                loadCompose();
            } else if(pathname.match(/view/g)){
                loadMessageview($(this));
            }
            else {
               $('.inbox-nav > li:first').click();
            }

            
        }

    };

    function get_messages_count()
        {
            $.post('<?php echo site_url($url_code.'/emails/count_mails')?>',{},function(data)
                {
                    if(data)
                    {
                        if(data.outbox_emails>0){
                            $('.outbox-message').html(data.outbox_emails).show();
                        }else{
                            $('.outbox-message').hide();
                        }

                        if((data.inbox_emails>0)){
                            $('.inbox-message').html(data.inbox_emails).show();
                        }else{
                            $('.inbox-message').hide();
                        } 

                        if(data.draft_emails>0){
                            $('.draft-message').html(data.draft_emails).show();
                        }else{
                            $('.draft-message').hide();
                        } 
                    }

                    else
                    {
                        //there was an error
                        alert('none');
                    }

                },"json");
        } 

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

            get_messages_count();
        });

        $('.mark-as-unread').click(function(){
            var url = '<?php echo site_url($url_code.'/emails/action');?>';
            var checkValues = $('input[name="list-mails[]"]:checked').map(function()
            {
                return $(this).val();
            }).get();
            $.ajax({
                url: url,
                type: 'post',
                data: { ids: checkValues,'action':'bulk_mark_as_unread'},
                async:true,
                success:function(data){
                    if(data){
                        loadInbox($(this), 'inbox');
                    }
                }
            });

            get_messages_count();
        });

        $('.set_draft').click(function(){
            var url = '<?php echo site_url($url_code.'/emails/action');?>';
            var checkValues = $('input[name="list-mails[]"]:checked').map(function()
            {
                return $(this).val();
            }).get();
            $.ajax({
                url: url,
                type: 'post',
                data: { ids: checkValues,'action':'bulk_save_as_draft'},
                async:true,
                success:function(data){
                    if(data){
                        loadOutbox($(this));
                    }
                }
            });

            get_messages_count();
        });

        $('.delete_outbox').click(function(){
            var url = '<?php echo site_url($url_code.'/emails/action');?>';
            var checkValues = $('input[name="list-mails[]"]:checked').map(function()
            {
                return $(this).val();
            }).get();
            $.ajax({
                url: url,
                type: 'post',
                data: { ids: checkValues,'action':'bulk_delete_outbox'},
                async:true,
                success:function(data){
                    if(data){
                        //window.location.reload();
                        loadOutbox($(this));
                    }
                }
            });

            get_messages_count();
        });

        $('.send_draft').click(function(){
            var url = '<?php echo site_url($url_code.'/emails/action');?>';
            var checkValues = $('input[name="list-mails[]"]:checked').map(function()
            {
                return $(this).val();
            }).get();
            $.ajax({
                url: url,
                type: 'post',
                data: { ids: checkValues,'action':'bulk_send_draft'},
                async:true,
                success:function(data){
                    if(data){
                        //window.location.reload();
                        loadDrafts($(this));
                    }
                }
            });

            get_messages_count();
        });

        $('.delete_draft').click(function(){
            var url = '<?php echo site_url($url_code.'/emails/action');?>';
            var checkValues = $('input[name="list-mails[]"]:checked').map(function()
            {
                return $(this).val();
            }).get();
            $.ajax({
                url: url,
                type: 'post',
                data: { ids: checkValues,'action':'bulk_delete_draft'},
                async:true,
                success:function(data){
                    if(data){
                        //window.location.reload();
                        loadDrafts($(this));
                    }
                }
            });

            get_messages_count();
        });
    }

}();




function submit_form(){
    $(document).on('click','button.discard',function(){
            window.location.reload();
        });

    $('.form_submit').submit(function(e){
        if($('select[name="member_id_to[]"]').val()==null){
            toastr['error']('You must include at least on recipient.','Select mail recipient');
        }else if($('input[name=subject]').val()==''){
            $('.subject-warning').slideDown().css('color','red');
            toastr['error']('Please add the subject of this email as it cannot send if its empty.','Email Subject is required');
        }else if($('textarea[name=message]').val()==''){
            toastr['error']('Kindly compose message to send as it is a required field.','Email Message required');
        }else{
            //$('.submit_form_button').hide();
            $('.submit_form_button').prop('disabled', true);
            $('.submit_form_button').html('<i class="fa fa-spinner fa-spin" style="margin-top:-4px;"></i> Processing');
            $('.discard').attr('disabled','disabled');
            $('.draft').attr('disabled','disabled');
            // $('.processing_form_button').show();
            return true;
        }
        return false;
    });

    $('input[name=subject]').keyup(function(){
        if($(this).val()!=''){
            $('.subject-warning').hide(); 
        }else{
             $('.subject-warning').slideDown().css('color','red');
        }
    }).keydown(function(){
        if($(this).val()!=''){
            $('.subject-warning').hide(); 
        }else{
             $('.subject-warning').slideDown().css('color','red');
        }
    });


    toastr.options = {
      "positionClass": "toast-top-right",
    }
}

jQuery(document).ready(function() {
    mAppInbox.init();
});
</script>

<script type="text/javascript">

    $(document).ready(function(){

        setInterval(function() 
        {
            get_messages_count();
        },36000);

        function get_messages_count()
        {
            $.post('<?php echo site_url('ajax/emails/count_mails')?>',{},function(data)
                {
                    if(data)
                    {
                        if(data.outbox_emails>0){
                            $('.outbox-message').html(data.outbox_emails).show();
                        }else{
                            $('.outbox-message').hide();
                        }

                        if((data.inbox_emails>0)){
                            $('.inbox-message').html(data.inbox_emails).show();
                        }else{
                            $('.inbox-message').hide();
                        } 

                        if(data.draft_emails>0){
                            $('.draft-message').html(data.draft_emails).show();
                        }else{
                            $('.draft-message').hide();
                        } 
                    }

                    else
                    {
                        //there was an error
                        alert('none');
                    }

                },"json");
        } 
        get_messages_count();
    });
</script>

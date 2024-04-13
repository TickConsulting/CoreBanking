<div class="container mt-2 mb-2">
	<div class="row pt-3 pb-5">
		<div class="col-md-6 offset-md-3">

            <nav class="mb-0 pb-4">
                <div class="">
                    <div class="float-right mr-2 m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="hover" aria-expanded="true">
                        <div class="checkin_prof_snap m-dropdown__toggle">
                            <span class="m-topbar__username m--padding-right-5">
                                <span><?php echo $this->user->last_name; ?></span>
                            </span>    
                            <a href="#" class="btn btn-link m-btn--icon m-btn--icon-only m-btn--pill">
                                <span class="m-topbar__userpic">
                                    <?php if(preg_match('/eazzykikundi/i', $this->application_settings->application_name)){ ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo $this->user->first_name; ?>+<?php echo $this->user->last_name; ?>&background=da720d&color=fff&size=32&bold=true" class="m--img-rounded m--marginless m--img-centered" alt=""/>
                                    <?php } 
                                    if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo $this->user->first_name; ?>+<?php echo $this->user->last_name; ?>&background=00abf2&color=fff&size=32&" class="m--img-rounded m--marginless m--img-centered" alt=""/>
                                    <?php } ?>
                                </span>
                            </a>
                        </div>
                        <div class="m-dropdown__wrapper" style="z-index: 101;">
                            <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 21.5px;"></span>
                            <div class="m-dropdown__inner">
                                <div class="m-dropdown__body">
                                    <div class="m-dropdown__content">
                                        <ul class="m-nav">
                                            <?php if($this->application_settings->allow_self_onboarding){?>
                                                <li class="m-nav__item">
                                                    <a href="#" class="m-nav__link">
                                                        <i class="m-nav__link-icon flaticon-plus"></i>
                                                        <span class="m-nav__link-text"><?php echo translate('Register New Group');?></span>
                                                    </a>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="#" class="m-nav__link">
                                                        <i class="m-nav__link-icon flaticon-app"></i>
                                                        <span class="m-nav__link-text"><?php echo translate('Try out our demo');?></span>
                                                    </a>
                                                </li>
                                            <?php }?>
                                            <li class="m-nav__separator m-nav__separator--fit">
                                            <li class="m-nav__item">
                                                <a href="https://help.chamasoft.com" class="m-nav__link">
                                                    <i class="m-nav__link-icon flaticon-info"></i>
                                                    <span class="m-nav__link-text"><?php echo translate('FAQ');?></span>
                                                </a>
                                            </li>
                                            <li class="m-nav__item">
                                                <a href="#" class="m-nav__link">
                                                    <i class="m-nav__link-icon flaticon-support"></i>
                                                    <span class="m-nav__link-text"><?php echo translate('Need help');?>?</span>
                                                </a>
                                            </li>
                                            <li class="m-nav__separator m-nav__separator--fit">
                                            </li>
                                            <li class="m-nav__item">
                                                <a href="<?php echo site_url('logout'); ?>" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm"><?php echo translate('Log out');?></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="checkin_header mt-0 mb-2 text-center" style="width:100%;float:left;">
                <a class="navbar-brand font-bold mb-2" href="<?php echo site_url('') ?>"><img src="<?php echo $this->application_settings?site_url('uploads/logos/'.$this->application_settings->logo):base_url('/templates/admin_themes/groups/img/').'logo_contrast.png'; ?>" draggable="false" height="60px" alt="Logo"></a>
                <h4><?php echo translate('Check in to');?>  <?php echo $this->application_settings->application_name; ?></h4>
                <p><small><?php echo translate('Choose an option to continue');?></small></p>
            </div>
            <div class="row mb-2  " style="width:100%;float:left;">
                <?php if($this->application_settings->allow_self_onboarding){?>
                    <div class="col-md-6 pr-lg-1">
                        <a href="<?php echo site_url('create_group');?>" class="btn m-btn--pill cust_checkin_btn btn-success m-btn m-btn--outline-2x btn-block mt-3"><i class="la la-plus"></i> <?php echo translate('Register New Group');?></a>
                    </div>
                    <div class="col-md-6 pl-lg-1">
                        <button onclick="window.location='<?php echo site_url('demo')?>'" type="button" class="btn m-btn--pill cust_checkin_btn btn-outline-success m-btn m-btn--outline-2x btn-block mt-3"><i class="la la-check"></i> <?php echo translate('Try out our Demo');?></button>

                        <button class="btn m-btn--pill btn-primary btn-sm float-right d-none update_button" data-toggle="modal" data-target="#email_modal"></button>
                    </div>
                <?php }else{?>
                    <div class="col-md-3"></div>
                <?php }?>
            </div>

            <div class="checkin_options_shell" style="width:100%;float:left;">
                <?php if($this->ion_auth->is_admin() || $this->ion_auth->is_bank_admin()){ ?>
                    <div class="chk_dmin_options pb-3">
                        <p class="chk-guide"><?php echo $this->application_settings->application_name; ?> <?php echo translate('Admin Options');?></p>
                        <?php if($this->ion_auth->is_admin()){ 
                            if($this->session->userdata('active_checkin') == "admin"){
                                $class_active = 'active';
                            }else{
                                $class_active = '';
                            }
                        ?>
                            <a href="<?php echo site_url('admin');?>" class="checkin_item <?php echo $class_active;?>">
                                <div>
                                    <i class="la la-user admin_img"></i>
                                    <span class="cht_title"><?php echo $this->application_settings->application_name; ?> Admin Panel</span>
                                    <span class="cht_sub"><small><?php echo translate('Administrator');?></small></span>
                                </div>
                                <i class="la la-lock cht_icn"></i>
                            </a>
                        <?php }?>

                         <?php if($this->ion_auth->is_bank_admin()||$this->ion_auth->is_admin()){ 
                            if($this->session->userdata('active_checkin') == "bank"){
                                $class_active = 'active';
                            }else{
                                $class_active = '';
                            }
                        ?>
                            <a href="<?php echo site_url('bank');?>" class="checkin_item <?php echo $class_active;?>">
                                <div>
                                    <i class="la la-user admin_img"></i>
                                    <span class="cht_title"><?php echo $this->application_settings->application_name; ?> Bank Panel</span>
                                    <span class="cht_sub"><small><?php echo translate('Bank Administrator');?></small></span>
                                </div>
                                <i class="la la-lock cht_icn"></i>
                            </a>
                        <?php }?>
                       


                        <?php if($this->ion_auth->is_in_group($this->user->id,5)||$this->ion_auth->is_admin()): 
                            if($partner_accounts){
                                foreach ($partner_accounts as $partner) {
                                        if($this->session->userdata('active_checkin') == $partner->id){
                                            $class_active = 'active';
                                        }else{
                                            $class_active = '';
                                        }
                                    ?>
                                    <a href="<?php echo $this->application_settings->protocol.''.$this->application_settings->url.'/bank/checkin?slug='.$bank->slug; ?>" class="checkin_item <?php echo $class_active;?>">
                                        <div>
                                            <i class="la la-user admin_img"></i>
                                            <span class="cht_title"><?php echo $partner->name; ?> Panel</span>
                                            <span class="cht_sub"><small><?php echo translate('Administrator');?></small></span>
                                        </div>
                                        <i class="la la-lock cht_icn"></i>
                                    </a>
                        <?php
                                }
                            }
                        endif;?>
                    </div>
                <?php }?>

                <?php if(!empty($groups)){ ?>
                    <p class="chk-guide pt-3"><?php echo translate('Select a Group you belong to below, you can switch later');?>.</p>
                    <?php foreach($groups as $group){ 
                        if($this->session->userdata('active_checkin') == $group->id){
                            $class_active = 'active';
                        }else{
                            $class_active = '';
                        }
                    ?>
                        
                        <a href="<?php echo site_url('login_to_group/'.$group->id); ?>" class="checkin_item <?php echo $class_active;?>">
                            <div>
                                <span class="cht_img">
                                    <?php 
                                        if($group->avatar){
                                            if(is_file('./uploads/groups'.$group->avatar)){
                                                $path = base_url()."uploads/groups".$group->avatar;
                                            }else{
                                                $path = "";
                                            }
                                        }else{
                                            $path ="";
                                        }
                                    if($path){
                                        echo '<img src="<?=$path;?>" alt="group avatar" height="43px">';
                                    }else{
                                        echo '<i class="la la-users no_grp"></i>';
                                    }
                                ?>
                                </span>
                                <span class="cht_title"><?php echo $group->name;?></span>
                                <?php if($group->subscription_status == 1){?>
                                    <span class="cht_sub"><small><strong><?php echo $group->trial_days;?> Trial days remaining</strong></small></span>
                                <?php }else{?>
                                     <span class="cht_sub"><small><strong><?php echo 'Group Active';?> </strong></small></span>
                                <?php }?>
                                <span class="cht_sub"><small>
                                    <?php
                                        $admin = $group->is_admin?'Administrator':'';
                                        $role = isset($group_role_options_array[$group->id][$group->group_role_id])?$group_role_options_array[$group->id][$group->group_role_id]:'Member';
                                        echo translate($role);
                                        echo ' - '.translate($admin);
                                        ?></small></span>
                            </div>
                            <i class="la la-check cht_icn"></i>
                        </a>
                    <?php } 
                }?>

                <?php if(!empty($groups_managed)){ ?>
                    <p class="chk-guide pt-3"><?php echo translate('Select a Group you manage');?>.</p>
                    <?php foreach($groups_managed as $group_managed){
                        if($this->session->userdata('active_checkin') == $group_managed->id){
                            $class_active = 'active';
                        }else{
                            $class_active = '';
                        }
                    ?>
                        <a href="<?php echo site_url('login_to_group/'.$group_managed->id); ?>" class="checkin_item <?php echo $class_active;?>">
                            <div>
                                <span class="cht_img">
                                    <?php 
                                        if($group_managed->avatar){
                                            if(is_file('./uploads/groups'.$group_managed->avatar)){
                                                $path = base_url()."uploads/groups".$group_managed->avatar;
                                            }else{
                                                $path = "";
                                            }
                                        }else{
                                            $path ="";
                                        }
                                        if($path){
                                            echo '<img src="<?=$path;?>" alt="group avatar" height="43px">';
                                        }else{
                                            echo '<i class="la la-users no_grp"></i>';
                                        }
                                    ?>
                                </span>
                                <span class="cht_title"><?php echo $group_managed->name;?></span>
                                <?php if($group_managed->subscription_status == 1){?>
                                    <span class="cht_sub"><small><strong><?php echo $group_managed->trial_days;?> <?php echo translate('Trial days remaining');?></strong></small></span>
                                <?php }?>
                                <span class="cht_sub"><small><?php echo translate('Administrator');?></small></span>
                            </div>
                            <i class="la la-check cht_icn"></i>
                        </a>
                    <?php }
                }?>
            </div>
            
            <div class="row mt-5 mb-2" style="width:100%;float:left;">
                <div class="col-sm-4 offset-sm-4">
                    <a href="<?php echo site_url('logout'); ?>" class="btn m-btn--pill btn-danger btn-block"><i class="la la-power-off"></i> <?php echo translate('Log out');?></a>
                </div>
            </div>

		</div>
	</div>
	<div class="row pt-2 pb-2">
		<div class="col-md-12">
			<p class="text-center small">&copy; <?php echo date('Y');?> Risk Tick Credit Limited &middot; <a href="<?php echo site_url('terms_of_use') ?>" class="m-link"><?php echo translate('Terms of use');?></a></p>
		</div>
	</div>
</div>

<script type="text/javascript">
    var set_value = false;
    $(document).ready(function(){
        // SnippetUpdateUserEmail.init();
        // SnippetCreateDemo.init(false,true);
        // $(document).on('click','#demo_link',function(){
        //     var message = 'Coming soon';
        //     bootbox.alert({
        //         message: message,
        //         size: 'small',
        //         buttons: {
        //             ok: {
        //                 label: 'Okay',
        //                 className: 'btn-success'
        //             }
        //         }
        //     });
        //     return false;
        // });

        // $('#email_modal').on('hidden.bs.modal', function () {
        //     set_value = true;
        //     update_later();
        // });

        <?php if($this->session->userdata('update_later') == 1 || $this->user->email){?>
        <?php }else{?>
            //$('.update_button').trigger('click');
        <?php }?>
    });

    // function update_later(){
    //     if(set_value == true){
    //         '<?php $this->session->set_userdata('update_later',1);?>';
    //     }
    // }

    // var SnippetUpdateUserEmail = function(){
    //     $("#UpdateEmail");
    //     var t = function (redirect,modal) {
    //         $(document).on('click',".btn#update_email_address_button",function (t) {
    //             t.preventDefault();
    //             var e = $(this),
    //             a = $("#update_email_address");   
    //             var email_address = a.find('input[name="email"]').val();
    //             console.log("email"+email_address);
    //             mApp.block(a, {
    //                 overlayColor: 'grey',
    //                 animate: true,
    //                 type: 'loader',
    //                 state: 'primary',
    //                 message: 'Processing...'
    //             });
    //             RemoveDangerClass();
    //             (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
    //                 type: "POST",
    //                 url: base_url+"/ajax/users/update_email_address",
    //                 success: function (t, i, n, r) {
    //                     if(isJson(t)){
    //                         response = $.parseJSON(t);
    //                         if(response.status == '1'){
    //                             setTimeout(function () {
    //                                 e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                                     mApp.unblock(a);
    //                                     a.find(".alert").html('').slideUp();
    //                                     Toastr.show("Success",response.message,'success');
    //                                     $('#email_modal_dismiss').trigger('click');
    //                             }, 2e3)
    //                         }else if(response.status == '202'){
    //                             Toastr.show("Session Expired",response.message,'error');
    //                             window.location.href = response.refer;
    //                         }else{
    //                             var message = response.message;
    //                             var validation_errors = '';
    //                             var fine_validation_errors = '';
    //                             if(response.hasOwnProperty('validation_errors')){
    //                                 validation_errors = response.validation_errors;
    //                             }
    //                             if(response.hasOwnProperty('fine_validation_errors')){
    //                                 fine_validation_errors = response.fine_validation_errors;
    //                             }
    //                             setTimeout(function () {
    //                                 mApp.unblock(a);
    //                                 e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                                 function (t, e, a) {
    //                                     var i = $('<div class="m-alert--air mb-2 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
    //                                     t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
    //                                 }(a, "danger", message);
    //                                 if(validation_errors){
    //                                     $.each(validation_errors, function( key, value ) {
    //                                         var error_message ='<div class="form-control-feedback">'+value+'</div>';
    //                                         $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
    //                                         $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
    //                                     });
    //                                 }
    //                                 if(fine_validation_errors){
    //                                     $.each(fine_validation_errors, function( key, value ) {
    //                                         if(value){
    //                                             $.each(value,function(keyval, valueval){
    //                                                 var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
    //                                                 $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
    //                                                 $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
    //                                             });
    //                                         }
    //                                     });
    //                                 }
    //                                 mUtil.scrollTop();
    //                             }, 2e3)
    //                         }
    //                     }else{
    //                         setTimeout(function () {
    //                             mApp.unblock(a);
    //                             e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                                 function (t, e, a) {
    //                                     var i = $('<div class="m-alert--air mb-2 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
    //                                     t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
    //                                 }(a, "danger", "Could not complete processing the request at the moment.")
    //                         }, 2e3)
    //                     }
    //                 },
    //                 error: function(){
    //                     setTimeout(function () {
    //                         mApp.unblock(a);
    //                         e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                             function (t, e, a) {
    //                                 var i = $('<div class="m-alert--air mb-2 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
    //                                 t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
    //                             }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
    //                     }, 2e3)
    //                 },
    //                 always: function(){
    //                     setTimeout(function () {
    //                         mApp.unblock(a);
    //                         e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                             function (t, e, a) {
    //                                 var i = $('<div class="m-alert--air mb-2 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
    //                                 t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
    //                             }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
    //                     }, 2e3)
    //                 }
    //             }));
    //         })
    //     };
    //     return {
    //         init: function (redirect = true,modal = false) {
    //             t(redirect,modal)
    //         }
    //     }
    // }();

    // var SnippetCreateDemo =  function(){ 
    //     var t = function (redirect,modal) {
    //         $(document).on('click',".btn#continue_to_demo",function (t) {
    //             t.preventDefault();
    //             var e = $(this),
    //             a = $("#demo_form");  
    //             mApp.block(a, {
    //                 overlayColor: 'grey',
    //                 animate: true,
    //                 type: 'loader',
    //                 state: 'primary',
    //                 message: 'Processing...'
    //             });
    //             RemoveDangerClass();
    //             (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
    //                 type: "POST",
    //                 url: base_url+"/ajax/users/create_demo_users",
    //                 success: function (t, i, n, r) {
    //                     if(isJson(t)){
    //                         response = $.parseJSON(t);
    //                         if(response.status == '1'){
    //                             setTimeout(function () {
    //                                 e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                                     mApp.unblock(a);
    //                                     a.find(".alert").html('').slideUp();
    //                                     Toastr.show("Success",response.message,'success');
    //                                     if(response.hasOwnProperty('refer')){
    //                                         window.location = response.refer;
    //                                     }else{

    //                                     }
                                        
    //                             }, 2e3)
    //                         }else if(response.status == '202'){
    //                             Toastr.show("Session Expired",response.message,'error');
    //                             window.location.href = response.refer;
    //                         }else{
    //                             var message = response.message;
    //                             var validation_errors = '';
    //                             var fine_validation_errors = '';
    //                             if(response.hasOwnProperty('validation_errors')){
    //                                 validation_errors = response.validation_errors;
    //                             }
    //                             if(response.hasOwnProperty('fine_validation_errors')){
    //                                 fine_validation_errors = response.fine_validation_errors;
    //                             }
    //                             setTimeout(function () {
    //                                 mApp.unblock(a);
    //                                 e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                                 function (t, e, a) {
    //                                     var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
    //                                     t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
    //                                 }(a, "danger", message);
    //                                 if(validation_errors){
    //                                     $.each(validation_errors, function( key, value ) {
    //                                         var error_message ='<div class="form-control-feedback">'+value+'</div>';
    //                                         $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
    //                                         $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
    //                                     });
    //                                 }
    //                                 if(fine_validation_errors){
    //                                     $.each(fine_validation_errors, function( key, value ) {
    //                                         if(value){
    //                                             $.each(value,function(keyval, valueval){
    //                                                 var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
    //                                                 $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
    //                                                 $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
    //                                             });
    //                                         }
    //                                     });
    //                                 }
    //                                 mUtil.scrollTop();
    //                             }, 2e3)
    //                         }
    //                     }else{
    //                         setTimeout(function () {
    //                             mApp.unblock(a);
    //                             e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                                 function (t, e, a) {
    //                                     var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
    //                                     t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
    //                                 }(a, "danger", "Could not complete processing the request at the moment.")
    //                         }, 2e3)
    //                     }
    //                 },
    //                 error: function(){
    //                     setTimeout(function () {
    //                         mApp.unblock(a);
    //                         e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                             function (t, e, a) {
    //                                 var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
    //                                 t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
    //                             }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
    //                     }, 2e3)
    //                 },
    //                 always: function(){
    //                     setTimeout(function () {
    //                         mApp.unblock(a);
    //                         e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
    //                             function (t, e, a) {
    //                                 var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
    //                                 t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
    //                             }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
    //                     }, 2e3)
    //                 }
    //             }));
    //         })
    //     };
    //     return {
    //         init: function (redirect = true,modal = false) {
    //             t(redirect,modal)
    //         }
    //     }
    // }();

</script>

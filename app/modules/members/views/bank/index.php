<div class="row page_menus mt-">
    <div class="settings-menu-icon col-md-6 p-0">
        <a href="<?php echo site_url('group/members/add_members')?>" class="">
            <div class="menu_item">
                <div class="menu_img">
                    <i class="img settings-icon mdi mdi-account-plus-outline"></i>
                </div>
                <div class="menu_cont">
                    <div class="menu_cont_hdr uppercase" style="font-size:16px;font-weight:600;">
                        <div class="overflow_text">
                            <?php
                                $default_message='Add Members'; $this->languages_m->translate('add_members',$default_message);
                            ?>
                        </div>
                    </div>
                    <div class="menu_cont_descr">
                        <span><strong>Add members one-by-one</strong>.<br>While adding your members, click 'Add New Line' to add another entry.</span>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="settings-menu-icon col-md-6 p-0">
        <a href="<?php echo site_url('group/members/import_members')?>" class="">
            <div class="menu_item">
                <div class="menu_img">
                    <i class="img settings-icon mdi mdi-account-multiple-plus-outline"></i>
                </div>
                <div class="menu_cont">
                    <div class="menu_cont_hdr uppercase" style="font-size:16px;font-weight:600;">
                        <div class="overflow_text">
                            <?php
                                $default_message='Import Members'; $this->languages_m->translate('import_members',$default_message);
                            ?>
                        </div>
                    </div>
                    <div class="menu_cont_descr">
                        <span><strong>Import your members from an excel sheet</strong> <br/>If you have saved your member name in an excel, click import and read the guidelines on how to import.</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
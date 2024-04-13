<div class="m-portlet m-portlet--creative m-portlet--bordered-semi mt-0 pt-0">
    <div class="m-portlet__body m-demo__preview">
        <h4><?php echo translate('Invite Members Instructions') ?></h4>
        <?php echo form_open_multipart($this->uri->uri_string(), ' class="form_submit m-form m-form--state" role="form" id="upload_excel_file"'); ?>
        <ol style="line-height: 2.2rem;">
            <li>
                <div class="d-flex">
                    <?php echo translate('Copy the group link');?>
                    <button class='ml-3 btn m-btn--pill btn-default btn-sm float-right' id='copy-member-registration-link-btn'>
                        <i class='la la-copy'></i>
                        <?php echo  translate('Copy Link') ?>                       
                    </button>
                </div>
            </li>
            <li>
                <?php echo translate('Share the below generated link with members to be invited.');?>
            </li>
        </ol>
        <?php echo form_close();?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        // SnippetImportMembers.init(true);
        $("#copy-member-registration-link-btn").on('click',function(e){
            e.preventDefault();
                var textarea = document.createElement("textarea");
                document.body.appendChild(textarea);
                var protocol = `<?php echo $this->application_settings->protocol ?>`;
                var link = `<?php echo $this->application_settings->url ?>`;
                var join_code = `<?php echo $join_code ?>`;
                textarea.value = protocol +  link + '/join_group/' + join_code;
                textarea.select();
                document.execCommand("copy");
                document.body.removeChild(textarea);        
                $('#copy-member-registration-link-btn').html('Copied');
                setTimeout(function(){
                    $('#copy-member-registration-link-btn').html('Copy');
                },2000);
        });
    });
</script>
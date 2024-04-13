<div class="row">
    <div class="col-md-12">
        <div class="row pt- mt-2">
            <div class="col-md-12">
                <div class="m-dropdown m-dropdown--inline m-dropdown--large m-dropdown--arrow" m-dropdown-toggle="click" m-dropdown-persistent="1">
                    <a href="#" class="m-dropdown__toggle btn btn-primary btn-sm dropdown-toggle">
                        <?php echo translate('Search');?>
                    </a>
                    <div class="m-dropdown__wrapper">
                        <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                        <div class="m-dropdown__inner">
                            <div class="m-dropdown__body">              
                                <div class="m-dropdown__content">
                                    <?php echo form_open(current_url(),'method="GET" class="filter"');?>
                                        <div class="form-body">
                                            <div class="form-group">
                                                <label class="control-label ">
                                                    <?php translate('Member'); ?>
                                                </label>
                                                <div class="">
                                                <?php
                                                    echo form_dropdown('member_id[]',array()+$this->group_member_options,$this->input->get('member_id'),'class="form-control select2" multiple="multiple"');
                                                ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm">Filter<i class="mdi mdi-filter"></i>
                                                <?php translate('Filter'); ?>
                                            </button>
                                            <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                                            <button class="btn btn-sm btn-danger close-filter" type="button"><i class="mdi mdi-close-circle-outline"></i></button>
                                        </div>
                                    <?php echo form_close();?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-group margin-bottom-20 search-button">
                    <a href="<?php echo site_url('group/statements/send_email_statements');?>" data-title="Enter your password to delete all records pertaining to <br/>" data-content="Are you sure you want to send email statement to <strong>ALL</strong> members?" class="btn btn-primary btn-sm prompt_confirmation_message_link_no_password send_email_statements">
                        <?php echo translate('Send Email Statement (ALL)')?><i class="fa fa-file-o"></i> &nbsp;&nbsp; 
                    </a>
                </div>
            </div>
        </div>
        <div class="row pt-2">
            <div class="col-md-12">
                <div id="member_statements_listing"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.select2').select2({width:"100%"});
        function generate_query(member_ids){
            var query = '';
            if(member_ids){
                $.each(member_ids,function(index,name){
                    query+="&"+$.param(name);
                });
            }
            $('a.send_email_statements').attr("href",'<?php echo site_url("group/statements/send_email_statements?")?>'+query);
        }

        $(document).on('click','input[name="check"]',function(){
            if($(this).prop("checked") == true){
                $('.checkboxes').parent().addClass('checked');
                $( ".checkboxes" ).prop( "checked", true );
            }else if($(this).prop("checked") == false){
                $('.checkboxes').parent().removeClass('checked');
                $( ".checkboxes" ).prop( "checked", false );
            }
        });
        if($('input[name="check"]').prop("checked") == true){
            $('.checkboxes').parent().addClass('checked');
            $( ".checkboxes" ).prop( "checked", true );
        }else if($('input[name="check"]').prop("checked") == false){
            $('.checkboxes').parent().removeClass('checked');
            $( ".checkboxes" ).prop( "checked", false );
        }


        $(document).on('click','input[name="check"]',function(){
            var count = 0;
            var member_ids = [];
            var members = [];
            $('input[name="action_to[]"]').each(function(){
                var this_checked = $(this);
                if(this_checked.prop('checked') == true){
                    member_id = $(this).val();
                    members[count] = member_id;
                    ++count;
                    $('a.send_email_statements').html('<?php echo translate('Send Email Statement')?> ('+count+')<i class="fa fa-file-o"></i> &nbsp;&nbsp; ').attr('data-content',"Are you sure you want to send email statement to the <strong>"+count+"</strong> selected members?");
                }
            });
            if(count == 0){
               $('a.send_email_statements').html('<?php echo translate('Send Email Statement (ALL)') ?><i class="fa fa-file-o"></i> &nbsp;&nbsp; ').attr('data-content',"Are you sure you want to send email statement to <strong>ALL</strong> members?");;
            }
            member_ids.push({members});
            generate_query(member_ids);
        });

        $(document).on('change','input[name="action_to[]"]',function(){
            var singlecount = 0;
            var member_ids = [];
            var members = [];
            $('input[name="action_to[]"]').each(function(){
                var this_checked = $(this);
                if(this_checked.prop('checked') == true){
                    ++singlecount;
                    member_id = $(this).val();
                    members[singlecount] = member_id;
                    $('a.send_email_statements').html('<?php echo translate('Send Email Statement') ?> ('+singlecount+')<i class="fa fa-file-o"></i> &nbsp;&nbsp; ').attr('data-content',"Are you sure you want to send email statement to the <strong>"+singlecount+"</strong> selected members?");;
                }
            });

            if(singlecount == 0){
                $('a.send_email_statements').html('<?php echo translate('Send Email Statement (ALL)') ?><i class="fa fa-file-o"></i> &nbsp;&nbsp; ').attr('data-content',"Are you sure you want to send email statement to ALL members?");;
            }
            member_ids.push({members});
            generate_query(member_ids);
        });

       
    });

    $(window).on('load',function(){

        load_member_statements_listing();

    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_member_statements_listing(){
        mApp.block('#member_statements_listing', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Processing...'
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/statements/get_statements_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#member_statements_listing').html(response);
                    mApp.unblock('#member_statements_listing');
                }
            }
        );
    }

</script>
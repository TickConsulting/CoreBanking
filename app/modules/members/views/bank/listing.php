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
                                            echo form_dropdown('member_id[]',array()+$group_member_options,$this->input->get('member_id'),'class="form-control select2" multiple="multiple"');
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
        <?php 
            $query = $_SERVER['QUERY_STRING']?'?generate_excel=1&'.$_SERVER['QUERY_STRING']:'?generate_excel=1';
            echo '
            <div class="btn-group margin-bottom-20 search-button">
                <a href="'.site_url('group/members/listing').$query.'" class="btn btn-sm btn-primary m-btn m-btn--icon">
                    <span>
                        <i class="la la-file-excel-o"></i>
                        <span>'.
                            translate('Export To Excel').
                        '</span>
                    </span>
                </a>
            </div>';
        ?>
    </div>
</div>

<div class="row pt-2">
    <div class="col-md-12">
        <span class="error"></span>
        <div id="members_listing"></div>
    </div>
</div>

<a class="d-none inline" data-toggle="modal" data-target="#suspend_member_pop_up" data-title="Suspend Member" data-id="suspend_member" id="suspend_member"  >
    <?php echo translate('Suspend Member');?>
</a>

<div class="modal fade" id="suspend_member_pop_up" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="member_details_holder" >
                    <?php echo translate('Suspend Member : &nbsp;') ;?><span id="member_details" class="badge_holder">
                </span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open($this->uri->uri_string(),'class="form_submit m-form m-form--state suspend_member_form" role="form" id="suspend_member_form"'); ?>
                <div  class="form-body">
                    <fieldset class="row m--margin-top-0">   
                        <div class="col-sm-12 m-form__group-sub m-input--air">
                            <label><?php echo translate('Suspension Reason');?><span class="required">*</span></label>
                            <?php 
                                $textarea = array(
                                    'name'=>'comment',
                                    'id'=>'',
                                    'value'=> $this->input->post('comment')?$this->input->post('comment'):'',
                                    'cols'=>40,
                                    'rows'=>5,
                                    'maxlength'=>200,
                                    'class'=>'form-control',
                                    'placeholder'=>'Reason for suspending Member'
                                ); 
                                echo form_textarea($textarea); 
                            ?>                         
                        </div>
                        <div class="col-sm-12 pt-0 m--padding-10 m-form__group-sub m-input--air">
                            <label><?php echo translate('Password');?><span class="required">*</span></label>
                            <?php echo form_password('password','','class="form-control m-input"') ?>
                        </div>

                        <?php echo form_hidden('member_id')?>
                    </fieldset>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-info" id="suspend_member_action">
                    <?php echo translate('Save Changes');?>                                         
                </button>
               <button type="button" class="btn btn-sm btn-danger" id="cancel_suspend_member_action" data-dismiss="modal">
                    <?php echo translate('cancel');?>                                                
                </button>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.select2').select2({width:"100%"});
        SnippetSuspendMember.init();


        $(document).on('click','.suspend_member',function(){
            var id = $(this).attr("id");
            if(id){
                mApp.block('#'+id+'_active_row', {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                });
                $.ajax({
                    type:'POST',
                    url: '<?php echo base_url("ajax/members/get_member/"); ?>',
                    data:{ 'id':id},
                    success: function(response){
                        if(isJson(response)){
                            var data = $.parseJSON(response);                           
                            var member = data.data;
                            $('#member_details').html(member.first_name+' '+member.last_name);
                            //$('.member_id').val(member.id)
                            $('[name="member_id"]').val(member.id)
                            mApp.unblock('#'+id+'_active_row');
                            $('#suspend_member').trigger('click');
                        }else{
                            mApp.unblock('#'+id+'_active_row');
                            $('.error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response+'</div>');
                        }
                    },
                    error: function(response){
                        mApp.unblock('#'+id+'_active_row');
                    },
                    always: function(){
                        mApp.unblock('#'+id+'_active_row');
                    }
                });
            }
        });

        $(document).on('click','.send-invitation',function(){
            var id = $(this).attr("id");
            if(id){
                mApp.block('#'+id+'_active_row', {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                });
                $.ajax({
                    type:'POST',
                    url:'<?php echo site_url('ajax/members/send_invitation')?>',                    
                    data:{'member_id':id},
                    success: function(response){
                        if(response){
                            var result = $.parseJSON(response)
                            if(result.status =='1'){
                                mApp.unblock('#'+id+'_active_row');
                                swal("success", result.message, "success");
                                if(result.hasOwnProperty('refer')){
                                    window.location = result.refer;
                                }
                            }else{
                                mApp.unblock('#'+id+'_active_row');
                                swal("Cancelled",result.message, "error"); 
                            }
                        }else{
                            swal("Cancelled",response, "error");
                        }
                    },
                    error: function(response){
                        mApp.unblock('#'+id+'_active_row');
                        swal("error",'Error occured. Try again', "error")
                    },
                    always : function(response){
                        mApp.unblock('#'+id+'_active_row');
                        swal("error",'Error occured. Try again', "error")
                    }

                });
            }else{
                swal("Cancelled", "Choose a member to send invitation", "error")
            }
        });

       /* $('.prompt_confirmation_message_link').on('click',function(e){
            e.preventDefault();
            var id = $(this).attr("id");
            if(id){
                bootbox.prompt({
                    title: "Enter your password to delete this member",
                    inputType: 'password',
                    required: true,
                    callback: function(password){
                        if(password){
                            mApp.block('#'+id+'_active_row', {
                                overlayColor: 'grey',
                                animate: true,
                                type: 'loader',
                                state: 'primary',
                            });
                            $.ajax({
                                type:'POST',
                                url: '<?php echo base_url("ajax/members/get_member/"); ?>',
                                data:{ 'id':id},
                                success: function(response){
                                    if(isJson(response)){
                                        var data = $.parseJSON(response);                           
                                        var member = data.data;
                                        $('#member_details').html(member.first_name+' '+member.last_name);
                                        //$('.member_id').val(member.id)
                                        $('[name="member_id"]').val(member.id)
                                        mApp.unblock('#'+id+'_active_row');
                                        $('#suspend_member').trigger('click');

                                    }else{
                                        $('.error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response+'</div>');
                                    }
                                },
                                error: function(response){

                                }
                            });
                        }
                    }
                });
            }
        });*/
      

        /*$(document).on('click','.prompt_confirmation_message_link',function(){
            var id = $(this).attr("id");
            if(id){
                mApp.block('#'+id+'_active_row', {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                });
                $.ajax({
                    type:'POST',
                    url: '<?php echo base_url("ajax/members/get_member/"); ?>',
                    data:{ 'id':id},
                    success: function(response){
                        if(isJson(response)){
                            var data = $.parseJSON(response);                           
                            var member = data.data;
                            $('#member_details').html(member.first_name+' '+member.last_name);
                            //$('.member_id').val(member.id)
                            $('[name="member_id"]').val(member.id)
                            mApp.unblock('#'+id+'_active_row');
                            $('#suspend_member').trigger('click');

                        }else{
                            $('.error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response+'</div>');
                        }
                    },
                    error: function(response){

                    }
                });
            }
        });*/

         

    });

    $(window).on('load',function(){

        load_members_listing();

    });

    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";

    function load_members_listing(){
        mApp.block('#members_listing', {
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/members/get_members_listing/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType : "html",
                success: function(response) {
                    $('#members_listing').html(response);
                    $('.date-picker').datepicker({autoclose:true});
                    mApp.unblock('#members_listing');
                }
            }
        );
    }

</script>
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
    </div>
</div>

<div class="row pt-2">
    <div class="col-md-12">
        <span class="error"></span>
        <div id="members_listing"></div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.select2').select2({width:"100%"});


        // $(document).on('click','.suspend_member',function(){
        //     var id = $(this).attr("id");
        //     if(id){
        //         mApp.block('#'+id+'_active_row', {
        //             overlayColor: 'grey',
        //             animate: true,
        //             type: 'loader',
        //             state: 'primary',
        //         });
        //         $.ajax({
        //             type:'POST',
        //             url: '<?php echo base_url("ajax/members/get_member/"); ?>',
        //             data:{ 'id':id},
        //             success: function(response){
        //                 if(isJson(response)){
        //                     var data = $.parseJSON(response);                           
        //                     var member = data.data;
        //                     $('#member_details').html(member.first_name+' '+member.last_name);
        //                     //$('.member_id').val(member.id)
        //                     $('[name="member_id"]').val(member.id)
        //                     mApp.unblock('#'+id+'_active_row');
        //                     $('#suspend_member').trigger('click');
        //                 }else{
        //                     mApp.unblock('#'+id+'_active_row');
        //                     $('.error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+response+'</div>');
        //                 }
        //             },
        //             error: function(response){
        //                 mApp.unblock('#'+id+'_active_row');
        //             },
        //             always: function(){
        //                 mApp.unblock('#'+id+'_active_row');
        //             }
        //         });
        //     }
        // });

        // $(document).on('click','.send-invitation',function(){
        //     var id = $(this).attr("id");
        //     if(id){
        //         mApp.block('#'+id+'_active_row', {
        //             overlayColor: 'grey',
        //             animate: true,
        //             type: 'loader',
        //             state: 'primary',
        //         });
        //         $.ajax({
        //             type:'POST',
        //             url:'<?php echo site_url('ajax/members/send_invitation')?>',                    
        //             data:{'member_id':id},
        //             success: function(response){
        //                 if(response){
        //                     var result = $.parseJSON(response)
        //                     if(result.status =='1'){
        //                         mApp.unblock('#'+id+'_active_row');
        //                         swal("success", result.message, "success");
        //                         if(result.hasOwnProperty('refer')){
        //                             window.location = result.refer;
        //                         }
        //                     }else{
        //                         mApp.unblock('#'+id+'_active_row');
        //                         swal("Cancelled",result.message, "error"); 
        //                     }
        //                 }else{
        //                     swal("Cancelled",response, "error");
        //                 }
        //             },
        //             error: function(response){
        //                 mApp.unblock('#'+id+'_active_row');
        //                 swal("error",'Error occured. Try again', "error")
        //             },
        //             always : function(response){
        //                 mApp.unblock('#'+id+'_active_row');
        //                 swal("error",'Error occured. Try again', "error")
        //             }

        //         });
        //     }else{
        //         swal("Cancelled", "Choose a member to send invitation", "error")
        //     }
        // });

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
            url: '<?php echo base_url("ajax/members/get_membership_requests/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
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
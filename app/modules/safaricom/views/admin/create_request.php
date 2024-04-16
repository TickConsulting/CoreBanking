<style type="text/css">
	.CodeMirror {
	    height: 400px;
	    //width: 491px;
	}
	.no-padding{
	    padding-left: 0px !important;
	    padding-right: 0px !important;
	}
	.process-check{
		display: none;
	}

</style>
<div class="row">
	<div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
               	<div class="caption">
                    <?php echo $this->admin_menus_m->generate_page_title();?>
                </div>
                <?php echo $this->admin_menus_m->generate_page_quick_action_menus();?>
            </div>
            <div class="portlet-body form logos">
            	<!-- BEGIN PORTLET-->
            	<div class="row">
            		<div class="col-lg-6 col-xs-12">
		             <?php echo form_open_multipart($this->uri->uri_string(), ' role="form" class="form_submit" '); ?>
		             <div class="form-body">
				        <div class="form-group">
				            <label>Request<span class="required">*</span></label>
				            <div class="input-group col-xs-12 response-request">

<textarea id="code_editor_demo_1" name="request">
<?php echo $request_sample;?>
</textarea>
						</div>
					</div>
					<div class="form-actions">
				        <button type="submit"  class="btn blue submit_form_button" name="submit">Submit</button>
				        <button type="button" class="btn btn-md blue processing_form_button disabled" name="processing" value="Processing"><i class="fa fa-spinner fa-spin"></i> Processing</button> 
				        <a href="<?php echo $this->agent->referrer()?>"><button type="button" class="btn default">Cancel</button></a>
				    </div>
				</div>

				<?php form_close();?>

				</div>
				
				<div class="col-lg-6 col-xs-12">
		             <div class="form-body">
				        <div class="form-group">
				            <label>Response</label>
				            <div class="input-group col-xs-12 response-request">

<textarea id="code_editor_demo_2" readonly="readonly" name="response_json"><?php print_r($response);?>
</textarea>
						</div>
					</div>
				</div>

				</div>
			</div>

			<div class="row">
				<div class="col-md-12 no-padding">
					<div class="form-body">
					    <div class="form-group  col-md-8">
					        <label>Paste Request id and click Check Result to Update<span class="required">*</span></label>
					        <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-envelope"></i>
				                </span>
				                <?php
				                	$textarea = array(
				                			'name' => 'request_id',
				                			'value' => $this->input->post('request_id'),
				                			'class' => "form-control",
				                			'rows' => "4",
				                		);
				                 echo form_textarea($textarea);?>
				            </div>
					    </div>
					    <div class="form-actions col-md-4">
					        <button type="button"  class="btn btn-sm btn-primary check" name="check">Check Result</button>
					        <button type="button"  class="btn btn-sm btn-primary process-check disabled" name="check"><i class="fa fa-spinner fa-spin"></i> processing</button>
					    </div>
					</div>
				</div>
			</div>
                    
                <!-- END PORTLET-->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	
	var ComponentsCodeEditors = function () {
    
    var handleDemo1 = function () {
        var myTextArea = document.getElementById('code_editor_demo_1');
        var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
            lineNumbers: true,
            matchBrackets: true,
            styleActiveLine: true,
            theme:"ambiance",
            mode: 'javascript'
        });
    }

    var handleDemo2 = function () {
        var myTextArea = document.getElementById('code_editor_demo_2');
        var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
            lineNumbers: true,
            matchBrackets: true,
            styleActiveLine: true,
            theme:"material",
            mode: 'javascript',
            readOnly: true
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleDemo1();
            handleDemo2();
        }
    };

}();



var handleDemo2_post = function () {
       var handleDemo2 = function () {
        var myTextArea = document.getElementById('code_editor_demo_2');
        var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
            lineNumbers: true,
            matchBrackets: true,
            styleActiveLine: true,
            theme:"material",
            mode: 'javascript',
            readOnly: true
        });
    }
    return {
        //main function to initiate the module
        init: function () {
            handleDemo2();
        }
    };
}();

jQuery(document).ready(function() {    
   ComponentsCodeEditors.init(); 

   $('button.check').on('click',function(){
   		var request_id = $('textarea[name="request_id"]').val();
   		<?php if(preg_match('/create_b2c_request/', current_url())){?>
   			var request_type = 1;
   		<?php }else{?>
   			var request_type = 2;
   		<?php }?>
   		if(request_id){
   			$('button.check').hide();
   			$('.process-check').show();
   			var response = $('textarea[name="response_json"]').val();
	   		var b2c_request = $('textarea[name="b2c_request"]').val();
	   		if(response){
	   			$.post('<?php echo base_url("admin/safaricom/ajax_get_request_status"); ?>',{'request_id':request_id,'response':response,'request_type':request_type},function(data)
		   		{
   					$('.process-check').hide();
		   			$('button.check').show();
		   			if(data=='error'){
		   				toastr['error']('No response from the request');
		   			}else{
		   				$('textarea[name="response_json"]').val(data);
		   				$('textarea[name="b2c_request"]').val(b2c_request);
		   				$('.cm-s-material').css('display','none');
		   				handleDemo2_post.init(); 
		   				toastr['success']('Updated the response');
		   			}
		   		});	
	   		}
   		}else{
   			 toastr['error']('Enter a valid Request Id from the response');
   		}
   });

    toastr.options = {
      "positionClass": "toast-top-right",
    }

    //var x = $("#typehead").parent().width();
    //alert($('.response-request').innerWidth()-10);
    $(".CodeMirror").css('width', $('.response-request').innerWidth()-3);

});
</script>
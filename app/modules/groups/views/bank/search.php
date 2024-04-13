<style type="text/css">
	.m-accordion.m-accordion--bordered .m-accordion__item, .m-accordion.m-accordion--default .m-accordion__item {
	    border: 1px solid #d8511f;
	}
</style>
<div class="form-group m-form__group row">
	<div class="col-sm-12">
		<select class="form-control m-select2" id="group_search" name="param">
			<option></option>
		</select>
	</div>
</div>
<div id="group-details" style="display:none">
	<div class="table-responsive" id="group">
	</div>

	<div class="m-accordion m-accordion--_default m-accordion--solid m-accordion--section  m-accordion--toggle-arrow" id="m_accordion_7" role="tablist">
		<!--begin::Item-->
		<div class="m-accordion__item">
			<div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_7_item_1_head" data-toggle="collapse" href="#m_accordion_7_item_1_body" aria-expanded="false">
				<span class="m-accordion__item-icon">
					<i class="fa flaticon-user-ok"></i>
				</span>
				<span class="m-accordion__item-title">
					<?php echo translate('Group Members') ?>
				</span>
				<span class="m-accordion__item-mode"></span>
			</div>
			<div class="m-accordion__item-body collapse" id="m_accordion_7_item_1_body" role="tabpanel" aria-labelledby="m_accordion_7_item_1_head" data-parent="#m_accordion_7">
				<div class="m-accordion__item-content" id="members" style="padding: 0;">
				</div>
			<!-- 	<div class="m-accordion__item-content" id="member" style="padding: 0;">
				</div> -->
				<!-- <p>
					Lorem Ipsum has been the industry's
					<a href="#" class="m-link m--font-boldest">
						Example boldest link
					</a>
				</p> -->
			</div>
		</div>
		<!--end::Item--> 
		<!--begin::Item-->
		<div class="m-accordion__item">
			<div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_7_item_2_head" data-toggle="collapse" href="#m_accordion_7_item_2_body" aria-expanded="    false">
				<span class="m-accordion__item-icon">
					<i class="fa  flaticon-suitcase"></i>
				</span>
				<span class="m-accordion__item-title">
					<?php echo translate('Group Accounts')?>
				</span>
				<span class="m-accordion__item-mode"></span>
			</div>
			<div class="m-accordion__item-body collapse" id="m_accordion_7_item_2_body" role="tabpanel" aria-labelledby="m_accordion_7_item_2_head" data-parent="#m_accordion_7">
				<div class="m-accordion__item-content m--padding-30" id="accounts">
				</div>
			</div>
		</div>
		<!--end::Item--> 
		<!--begin::Item-->
		<div class="m-accordion__item">
			<div class="m-accordion__item-head collapsed" role="tab" id="m_accordion_7_item_3_head" data-toggle="collapse" href="#m_accordion_7_item_3_body" aria-expanded="    false">
				<span class="m-accordion__item-icon">
					<i class="fa  flaticon-alert-2"></i>
				</span>
				<span class="m-accordion__item-title">
					<?php echo translate('Group Contribution Settings')?>
				</span>
				<span class="m-accordion__item-mode"></span>
			</div>
			<div class="m-accordion__item-body collapse" id="m_accordion_7_item_3_body" role="tabpanel" aria-labelledby="m_accordion_7_item_3_head" data-parent="#m_accordion_7">
				<div class="m-accordion__item-content m--padding-30" id="contributions">
				</div>
			</div>
		</div>
		<!--end::Item-->
	</div>
</div>

<div id="search-placeholder">
	<div class="alert m-alert--outline alert-metal">
        <h4 class="block">Waiting for search.</h4>
        <p>
            Search for a group above to view it's details.
        </p>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		<?php if($group_id){ ?>
            var group_id = "<?php echo $group_id; ?>";
			console.log(group_id)
            get_group_data(group_id);
        <?php } ?>

		$("#group_search").select2({
            placeholder: "Search Groups",
            width: "100%",
            allowClear: true,
            ajax: {
            	url: base_url+'/groups/bank/ajax_search_options',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatGroup, // omitted for brevity, see the source of this page
            templateSelection: formatGroupSelection
        });

        $('#group_search').on('change',function(){
        	if($(this).val()){
        		get_group_data($(this).val());
        	}else{
        		$('#search-placeholder').slideDown();
        		$('#group-details').slideUp();
        	}
        });
    });

    function get_group_data(group_id){
    	mApp.block('#group-details, #search-placeholder', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Fetching Group Information...'
        });

		$.ajax({
    		url: base_url+'/ajax/groups/get_group_information/'+group_id,
    		method: 'POST',
        	dataType: 'json',
    		success: function(response){
    			$('#members').html(response.members)
    			$('#contributions').html(response.contributions)
    			$('#accounts').html(response.bank_accounts)
    			$('#group').html(response.group_data)
    			$('#group-details').slideDown();
				$('#search-placeholder').slideUp();
           		mApp.unblock('#group-details, #search-placeholder', {});
    		},
    		error: function(error){
           		mApp.unblock('#group-details, #search-placeholder', {});
    		},

		});
    }


    function formatGroup(group) {
        if (group.loading) return 'Searching';
        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + group.name + " ("+ group.account_number+")</div>";
        if (group.email) {
            markup += "<div class='select2-result-repository__description'><strong>Email:</strong> " + group.email + "</div>";
        }
        if (group.phone) {
            markup += "<div class='select2-result-repository__description'><strong>Phone:</strong> " + group.phone + "</div>";
        }
            markup += "<div class='select2-result-repository__description'><strong>Registered By:</strong> " + group.first_name + ' ' +group.last_name + "</div>";
        // markup += "<div class='select2-result-repository__statistics'>" +
        //     "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + group.forks_count + " Forks</div>" +
        //     "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + group.size + " Active members</div>" +
        //     "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + group.watchers_count + " Watchers</div>" +
        //     "</div>" +
        //     "</div></div>";
        return markup;
    }

 	function formatGroupSelection(group) {
        return group.name || 'Search..';
    }
</script>
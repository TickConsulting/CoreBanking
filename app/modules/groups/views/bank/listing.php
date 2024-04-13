<!--begin: Search Form -->
<div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
	<div class="row align-items-center">
		<div class="col-xl-8 order-2 order-xl-1">
			<div class="form-group m-form__group row align-items-center">
				<div class="col-md-4">
					<div class="m-input-icon m-input-icon--left">
						<input type="text" class="form-control m-input" placeholder="Search..." id="search">
						<span class="m-input-icon__icon m-input-icon__icon--left">
							<span>
								<i class="la la-search"></i>
							</span>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--end: Search Form -->
<!--begin: Datatable -->
<div class="m_datatable"></div>
	<!--end: Datatable -->
<script type="text/javascript">
	jQuery(document).ready(function() {
		var datatable = $('.m_datatable').mDatatable({
		  // datasource definition
			data: {
			    type: 'remote',
			    source: {
			      read: {
			        url: '<?php echo site_url("groups/ajax/listing"); ?>',
			      },
			    },
			    pageSize: 10, // display 20 records per page
			    serverPaging: true,
			    serverFiltering: true,
			    serverSorting: true,
			},

			  // layout definition
			layout: {
			    theme: 'default',
			    scroll: false,
			    height: null,
			    footer: false,
			},

			  // column sorting
			sortable: true,

			pagination: true,

			search: {
			    input: $('#<?php echo translate('search')?>'),
			},

		  	// columns definition
		  	columns: [
		    	// {
			    //   field: 'checkbox',
			    //   title: '',
			    //   template: '{{id}}',
			    //   sortable: false,
			    //   width: 20,
			    //   textAlign: 'center',
			    //   selector: {class: 'm-checkbox--solid m-checkbox--brand'},
			    // }, 
			    {
			      field: 'name',
			      title: '<?php echo translate('Group Name')?>',
			      sortable: 'asc',
			    },
			    {
			      field: 'active_size',
			      title: '<?php echo translate('Active Members')?>',
			      textAlign: 'center',
			    },
			    {
			      field: 'created_on',
			      title: '<?php echo translate('Onboarded On')?>',
			      template: function (row) {
			      	return new Date(row.created_on*1000).toDateString();
			      }
			    },
			    {
			      field: 'owner',
			      title: '<?php echo translate('Registered By')?>',
			      template: function (row) {
			      	return row.owner_first_name + ' '+row.owner_last_name + '<br>' +(row.owner_phone?row.owner_phone+' ':'')+(row.owner_email?row.owner_email+' ':'');
			      }
			    },
			   {
			      field: 'Actions',
			      width: 110,
			      title: '<?php echo translate('Actions')?>',
			      sortable: false,
			      overflow: 'visible',
			      template: function (row, index, datatable) {
			        var dropup = (datatable.getPageSize() - index) <= 4 ? 'dropup' : '';
			        return '\
						<div class="dropdown' + dropup + '">\
							<a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown">\
		                        <i class="la la-ellipsis-h"></i>\
		                    </a>\
						  	<div class="dropdown-menu dropdown-menu-right">\
						    	<a class="dropdown-item" href="'+base_url+'/bank/groups/search/'+row.id+'"><i class="la la-eye"></i> View Details</a>\
						    	<a class="dropdown-item d-none" href="#"><i class="la la-edit"></i> Edit Details</a>\
						    	<a class="dropdown-item prompt_password_confirmation_message_link" href="'+base_url+'/bank/groups/delete/'+row.id+'" data-title="Enter the delete code to delete the group and its data permanently."><i class="la la-trash"></i> Delete Group</a>\
						  	</div>\
						</div>\
						<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill d-none" title="Edit details">\
							<i class="la la-edit"></i>\
						</a>\
						<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill d-none" title="Delete">\
							<i class="la la-trash"></i>\
						</a>\
					';
			    },
		    }],
		});
		setTimeout(function(){
			$('.m_datatable').find('table').addClass('table').addClass('table-bordered').addClass('table-hover').addClass('table-condensed');
		},2000);
		
	});
</script>
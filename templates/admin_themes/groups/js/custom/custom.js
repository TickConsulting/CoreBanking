var base_url = window.location.origin;
var edit_bank_account_init = 0;
var edit_sacco_account_init = 0;
var edit_mobile_money_account_init = 0;
var edit_petty_cash_account_init = 0;
var edit_contribution_init = 0;
var edit_loan_type_init = 0;

var Inputmask = {
	init: function () {
		if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|mobile|CriOS/i.test(navigator.userAgent)) {
		}else{
			$(".currency").inputmask('decimal', {
				radixPoint: ".",
				groupSeparator: ",",
				digits: 12,
				autoGroup: true,
				greedy: false,
				prefix: '',
				rightAlign: false,
                /*min: 0,
                allowPlus: false,
                allowMinus: false,*/
			}).attr('autocomplete', 'off'),
			$(".numeric").inputmask('decimal', {
				/*mask: "9.",
				groupSeparator: ",", 
				digits: 2,
				repeat: 100,
				greedy: false*/
				radixPoint: ".",
				autoGroup: true,
				groupSeparator: ".",
				groupSize: 3,
				greedy: false,
				digits: 2,
				rightAlign: false
			}).attr('autocomplete', 'off'),
			$(".date").inputmask("mm/dd/yyyy", {
				autoUnmask: !0
			}),
			$(".custom-date").inputmask("mm/dd/yyyy", {
				placeholder: "*"
			}),
			$(".email_address").inputmask({
				mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
				greedy: !1,
				onBeforePaste: function (m, a) {
					return (m = m.toLowerCase()).replace("mailto:", "")
				},
				definitions: {
					"*": {
						validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~-]",
						cardinality: 1,
						casing: "lower"
					}
				}
			});
		}
	}
};

var FormInputMask = function () {
    var handleInputMasks = function () {
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|mobile|CriOS/i.test(navigator.userAgent) ) {
		 	// some code..
		}else{
			
	        $(".currency").inputmask('decimal', {
	            radixPoint:".", 
	            groupSeparator: ",", 
	            digits: 12,
	            autoGroup: true,
	            greedy: false,
	            prefix: '',
	            rightAlign: false,
                min: 0,
                allowPlus: false,
                allowMinus: false,
	        }).attr('autocomplete','off');

		}

        $(".numeric").inputmask('decimal',{
            /*mask: "9.",
            groupSeparator: ",", 
            digits: 2,
            repeat: 100,
            greedy: false*/
            radixPoint: ".", 
	        autoGroup: true, 
	        groupSeparator: ".", 
	        groupSize: 3, 
	        greedy: false,
	        digits: 2,
	        rightAlign: false
        }).attr('autocomplete','off');
    }

    return {
        //main function to initiate the module
        init: function () {
            handleInputMasks();
        }
    };

}();

var BootstrapSwitch = {
    init: function(){
        $("[data-switch=true]").bootstrapSwitch()
    }
};

var TotalAmount = function () {
    var handleTotalAmount = function () {
        var totalAmountVal = $('.total-amount');
        var total_amount = 0;
        if($('.amount ').val()){
            $('.amount ').each(function(){
                var str = $(this).val();
                var amount = str.replace_all(',','');
                amount_value =  parseFloat(amount);
                if(!isNaN(amount_value)){
                    total_amount+=amount_value;
                }
            });
        }
        if($('.price_per_share').val()){
            $('.price_per_share').each(function(){
                var str = $(this).val();
                var amount = str.replace_all(',','');
                amount_value =  parseFloat(amount);
                if(!isNaN(amount_value)){
                    total_amount+=amount_value;
                }
            });
        }
        
        totalAmountVal.html(total_amount.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
    }
    return {
        init: function () {
            handleTotalAmount();
        }
    };

}();

function number_to_currency(amount=0){
    if(amount){
        var amount = parseFloat(amount);
        return amount.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }else{
        return '0.00';
    }
    
}


var Datatable = function() {
    var tableOptions; // main options
    var dataTable; // datatable object
    var table; // actual table jquery object
    var tableContainer; // actual table container object
    var tableWrapper; // actual table wrapper jquery object
    var tableInitialized = false;
    var ajaxParams = {}; // set filter mode
    var the;

    var countSelectedRecords = function() {
        var selected = $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', table).size();
        var text = tableOptions.dataTable.language.metronicGroupActions;
        if (selected > 0) {
            $('.table-group-actions > span', tableWrapper).text(text.replace("_TOTAL_", selected));
        } else {
            $('.table-group-actions > span', tableWrapper).text("");
        }
    };

    return {
        //main function to initiate the module
        init: function(options) {

            if (!$().dataTable) {
                return;
            }

            the = this;

            // default settings
            options = $.extend(true, {
                src: "", // actual table  
                filterApplyAction: "filter",
                filterCancelAction: "filter_cancel",
                resetGroupActionInputOnSuccess: true,
                loadingMessage: 'Loading...',
                dataTable: {
                    "dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r><'table-responsive't><'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>", // datatable layout
                    "pageLength": 10, // default records per page
                    "language": { // language settings
                        // metronic spesific
                        "metronicGroupActions": "_TOTAL_ records selected:  ",
                        "metronicAjaxRequestGeneralError": "Could not complete request. Please check your internet connection",

                        // data tables spesific
                        "lengthMenu": "<span class='seperator'>|</span>View _MENU_ records",
                        "info": "<span class='seperator'>|</span>Found total _TOTAL_ records",
                        "infoEmpty": "No records found to show",
                        "emptyTable": "No data available in table",
                        "zeroRecords": "No matching records found",
                        "paginate": {
                            "previous": "Prev",
                            "next": "Next",
                            "last": "Last",
                            "first": "First",
                            "page": "Page",
                            "pageOf": "of"
                        }
                    },

                    "orderCellsTop": true,
                    "columnDefs": [{ // define columns sorting options(by default all columns are sortable extept the first checkbox column)
                        'orderable': false,
                        'targets': [0]
                    }],

                    "pagingType": "bootstrap_extended", // pagination type(bootstrap, bootstrap_full_number or bootstrap_extended)
                    "autoWidth": false, // disable fixed width and enable fluid table
                    "processing": false, // enable/disable display message box on record load
                    "serverSide": true, // enable/disable server side ajax loading

                    "ajax": { // define ajax settings
                        "url": "", // ajax URL
                        "type": "POST", // request type
                        "timeout": 20000,
                        "data": function(data) { // add request parameters before submit
                            $.each(ajaxParams, function(key, value) {
                                data[key] = value;
                            });
                            mApp.block({
                                message: tableOptions.loadingMessage,
                                target: tableContainer,
                                overlayColor: 'none',
                                cenrerY: true,
                                boxed: true
                            });
                        },
                        "dataSrc": function(res) { // Manipulate the data returned from the server
                            if (res.customActionMessage) {
                                mApp.alert({
                                    type: (res.customActionStatus == 'OK' ? 'success' : 'danger'),
                                    icon: (res.customActionStatus == 'OK' ? 'check' : 'warning'),
                                    message: res.customActionMessage,
                                    container: tableWrapper,
                                    place: 'prepend'
                                });
                            }

                            if (res.customActionStatus) {
                                if (tableOptions.resetGroupActionInputOnSuccess) {
                                    $('.table-group-action-input', tableWrapper).val("");
                                }
                            }

                            if ($('.group-checkable', table).size() === 1) {
                                $('.group-checkable', table).attr("checked", false);
                                $.uniform.update($('.group-checkable', table));
                            }

                            if (tableOptions.onSuccess) {
                                tableOptions.onSuccess.call(undefined, the, res);
                            }

                            mApp.unblockUI(tableContainer);

                            return res.data;
                        },
                        "error": function() { // handle general connection errors
                            if (tableOptions.onError) {
                                tableOptions.onError.call(undefined, the);
                            }

                            mApp.alert({
                                type: 'danger',
                                icon: 'warning',
                                message: tableOptions.dataTable.language.metronicAjaxRequestGeneralError,
                                container: tableWrapper,
                                place: 'prepend'
                            });

                            mApp.unblockUI(tableContainer);
                        }
                    },

                    "drawCallback": function(oSettings) { // run some code on table redraw
                        if (tableInitialized === false) { // check if table has been initialized
                            tableInitialized = true; // set table initialized
                            table.show(); // display table
                        }
                        mApp.initUniform($('input[type="checkbox"]', table)); // reinitialize uniform checkboxes on each table reload
                        countSelectedRecords(); // reset selected records indicator

                        // callback for ajax data load
                        if (tableOptions.onDataLoad) {
                            tableOptions.onDataLoad.call(undefined, the);
                        }
                    }
                }
            }, options);

            tableOptions = options;

            // create table's jquery object
            table = $(options.src);
            tableContainer = table.parents(".table-container");

            // apply the special class that used to restyle the default datatable
            var tmp = $.fn.dataTableExt.oStdClasses;

            $.fn.dataTableExt.oStdClasses.sWrapper = $.fn.dataTableExt.oStdClasses.sWrapper + " dataTables_extended_wrapper";
            $.fn.dataTableExt.oStdClasses.sFilterInput = "form-control input-xs input-sm input-inline";
            $.fn.dataTableExt.oStdClasses.sLengthSelect = "form-control input-xs input-sm input-inline";

            // initialize a datatable
            dataTable = table.DataTable(options.dataTable);

            // revert back to default
            $.fn.dataTableExt.oStdClasses.sWrapper = tmp.sWrapper;
            $.fn.dataTableExt.oStdClasses.sFilterInput = tmp.sFilterInput;
            $.fn.dataTableExt.oStdClasses.sLengthSelect = tmp.sLengthSelect;

            // get table wrapper
            tableWrapper = table.parents('.dataTables_wrapper');

            // build table group actions panel
            if ($('.table-actions-wrapper', tableContainer).size() === 1) {
                $('.table-group-actions', tableWrapper).html($('.table-actions-wrapper', tableContainer).html()); // place the panel inside the wrapper
                $('.table-actions-wrapper', tableContainer).remove(); // remove the template container
            }
            // handle group checkboxes check/uncheck
            $('.group-checkable', table).change(function() {
                var set = table.find('tbody > tr > td:nth-child(1) input[type="checkbox"]');
                var checked = $(this).prop("checked");
                $(set).each(function() {
                    $(this).prop("checked", checked);
                });
                $.uniform.update(set);
                countSelectedRecords();
            });

            // handle row's checkbox click
            table.on('change', 'tbody > tr > td:nth-child(1) input[type="checkbox"]', function() {
                countSelectedRecords();
            });

            // handle filter submit button click
            table.on('click', '.filter-submit', function(e) {
                e.preventDefault();
                the.submitFilter();
            });

            // handle filter cancel button click
            table.on('click', '.filter-cancel', function(e) {
                e.preventDefault();
                the.resetFilter();
            });
        },

        submitFilter: function() {
            the.setAjaxParam("action", tableOptions.filterApplyAction);

            // get all typeable inputs
            $('textarea.form-filter, select.form-filter, input.form-filter:not([type="radio"],[type="checkbox"])', table).each(function() {
                the.setAjaxParam($(this).attr("name"), $(this).val());
            });

            // get all checkboxes
            $('input.form-filter[type="checkbox"]:checked', table).each(function() {
                the.addAjaxParam($(this).attr("name"), $(this).val());
            });

            // get all radio buttons
            $('input.form-filter[type="radio"]:checked', table).each(function() {
                the.setAjaxParam($(this).attr("name"), $(this).val());
            });

            dataTable.ajax.reload();
        },

        resetFilter: function() {
            $('textarea.form-filter, select.form-filter, input.form-filter', table).each(function() {
                $(this).val("");
            });
            $('input.form-filter[type="checkbox"]', table).each(function() {
                $(this).attr("checked", false);
            });
            the.clearAjaxParams();
            the.addAjaxParam("action", tableOptions.filterCancelAction);
            dataTable.ajax.reload();
        },

        getSelectedRowsCount: function() {
            return $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', table).size();
        },

        getSelectedRows: function() {
            var rows = [];
            $('tbody > tr > td:nth-child(1) input[type="checkbox"]:checked', table).each(function() {
                rows.push($(this).val());
            });

            return rows;
        },

        setAjaxParam: function(name, value) {
            ajaxParams[name] = value;
        },

        addAjaxParam: function(name, value) {
            if (!ajaxParams[name]) {
                ajaxParams[name] = [];
            }

            skip = false;
            for (var i = 0; i < (ajaxParams[name]).length; i++) { // check for duplicates
                if (ajaxParams[name][i] === value) {
                    skip = true;
                }
            }

            if (skip === false) {
                ajaxParams[name].push(value);
            }
        },

        clearAjaxParams: function(name, value) {
            ajaxParams = {};
        },

        getDataTable: function() {
            return dataTable;
        },

        getTableWrapper: function() {
            return tableWrapper;
        },

        gettableContainer: function() {
            return tableContainer;
        },

        getTable: function() {
            return table;
        }
    };

};

var ManageSetupWizard = function(startStep=1) {
	return {
		init: function(startStep) {
            $("#m_wizard"), i = $(".form_submit"), (r = new mWizard("m_wizard", {
                startStep: startStep
            })),r.on("change", function (e) {
                mUtil.scrollTop()
            });
            position = 0;
			var active = $('.m-wizard__nav').find('.m-wizard__step--current');
			var target = active.attr('m-wizard-target');
            var previous_button = $('.m-wizard.m-wizard--step-between [data-wizard-action="prev"]');
            var save_and_continue = $('.m-wizard.m-wizard--step-last [data-wizard-action="next"]');
            var save_and_continue_between= $('.m-wizard.m-wizard--step-between [data-wizard-action="next"]');
            save_and_continue.attr('style','display:inline-block !important');
			if(target == 'm_wizard_form_step_1'){
                $('.btn.btn-setup-previous').hide();
                previous_button.removeAttr('style');
				$('#create_group_panel').slideDown();
                position = 1;
			}else if(target == 'm_wizard_form_step_2'){
				$('#create_group_contribution_panel').hide();
				$('#create_group_panel').hide();
				$('#create_group_loan_types_panel').hide();
                $('#create_group_bank_account_panel').hide();
                $('#confirm_group_setup_panel').hide();
                previous_button.removeAttr('style');
                save_and_continue_between.attr('style','display:inline-block !important');
				$('#create_group_members_panel').slideDown();
                position = 2;
				load_members();
			}else if(target == 'm_wizard_form_step_3'){
				$('#create_group_panel').hide();
				$('#create_group_members_panel').hide();
				$('#create_group_loan_types_panel').hide();
                $('#confirm_group_setup_panel').hide();
                $('#create_group_bank_account_panel').hide();
                previous_button.attr('style','display:inline-block !important');
                save_and_continue_between.attr('style','display:inline-block !important');
				$('#create_group_contribution_panel').slideDown();
                position = 3;
				load_contributions();
			}else if(target == 'm_wizard_form_step_4'){
				$('#create_group_contribution_panel').hide();
				$('#create_group_panel').hide();
				$('#create_group_members_panel').hide();
                $('#confirm_group_setup_panel').hide();
                $('#create_group_bank_account_panel').hide();
                position = 4;
                previous_button.attr('style','display:inline-block !important');
                save_and_continue_between.attr('style','display:inline-block !important');
				$('#create_group_loan_types_panel').slideDown();
                if(group  && group.group_offer_loans == 1){
                    $('.create_loan_type_settings_layout,#create_loan_type_options').hide();
                    $('.load_group_loan_types,#create_loan_type_header').slideDown();
                }else{
                    $('.load_group_loan_types,#create_loan_type_header').hide();
                    $('.create_loan_type_settings_layout,#create_loan_type_options').slideDown();
                }
                load_loan_types();
			}else if(target == 'm_wizard_form_step_5'){
				$('#create_group_contribution_panel').hide();
				$('#create_group_panel').hide();
				$('#create_group_loan_types_panel').hide();
                $('#confirm_group_setup_panel').hide();
				$('#create_group_members_panel').hide();
                previous_button.attr('style','display:inline-block !important');
                save_and_continue_between.attr('style','display:inline-block !important');
                $('#create_group_bank_account_panel').slideDown();
                position = 5;
                load_group_members();
                load_bank_accounts();
			}else if(target == 'm_wizard_form_step_6'){
				$('#create_group_contribution_panel').hide();
				$('#create_group_loan_types_panel').hide();
				$('#create_group_panel').hide();
				$('#create_group_members_panel').hide();
                $('#create_group_bank_account_panel').hide();
                previous_button.attr('style','display:inline-block !important');
                save_and_continue_between.attr('style','display:none !important');
                position = 6;
                $('#confirm_group_setup_panel').slideDown();
                load_complete_setup();
                save_and_continue.removeAttr('style');
			}
            Select2.init();
			$('.scrollable-datatable').DataTable({
				scrollY: "50vh",
				scrollX: !0,
				scrollCollapse: !0,
			});
            UpdateGroupSetupPosition(position);
		}
	}
}();

var Select2 = function (selector) {
    return {
        init: function (selector) {
            var selectvar = $(".m-select2");
            if(selector){
                //if($('select'+selector).data('select2')){
                if($(selector).hasClass('select2-hidden-accessible')){
                    $('select'+selector).select2('destroy');
                }
                $('select'+selector).select2({
                    //width: "100%",
                    placeholder:{
                        id: '-1',
                        text: "--Select option--",
                    }, 
                    // allowClear: !0
                });
            }else{
               // if (selectvar.data('select2')) {
                if($(selector).hasClass('select2-hidden-accessible')){
                   selectvar.select2('destroy');
                }
                selectvar.select2({
                    width: "100%",
                    placeholder: {
                      id: '-1',
                      text: "--Select option--",
                    },
                    // allowClear: !0
                });
            }

            $("#m_select2_6").select2({
                placeholder: "Search for git repositories",
                // allowClear: !0,
                ajax: {
                    url: "https://api.github.com/search/repositories",
                    dataType: "json",
                    delay: 250,
                    data: function (e) {
                        return {
                            q: e.term,
                            page: e.page
                        }
                    },
                    processResults: function (e, t) {
                        return t.page = t.page || 1, {
                            results: e.items,
                            pagination: {
                                more: 30 * t.page < e.total_count
                            }
                        }
                    },
                    cache: !0
                },
                escapeMarkup: function (e) {
                    return e
                },
                minimumInputLength: 1,
                templateResult: function (e) {
                    if (e.loading) return e.text;
                    var t = "<div class='select2-result-repository clearfix'><div class='select2-result-repository__meta'><div class='select2-result-repository__title'>" + e.full_name + "</div>";
                    return e.description && (t += "<div class='select2-result-repository__description'>" + e.description + "</div>"), t += "<div class='select2-result-repository__statistics'><div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + e.forks_count + " Forks</div><div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + e.stargazers_count + " Stars</div><div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + e.watchers_count + " Watchers</div></div></div></div>"
                },
                templateSelection: function (e) {
                    return e.full_name || e.text
                }
            });
        }
    }
}();

var Datepicker = function () {
    var t;
    if (typeof mUtil === "undefined"){     
    }else{
        t = mUtil.isRTL() ? {
            leftArrow: '<i class="la la-angle-right"></i>',
            rightArrow: '<i class="la la-angle-left"></i>'
        } : {
            leftArrow: '<i class="la la-angle-left"></i>',
            rightArrow: '<i class="la la-angle-right"></i>'
        };
        return {
            init: function () {
                $(".datepicker").datepicker({
                    rtl: mUtil.isRTL(),
                    todayHighlight: !0,
                    orientation: "bottom left",
                    templates: t,
                    autoclose: true,
                    format: "dd-M-yyyy",
                })
            }
        }
    }
}();

var Toastr = function () {
    return {
        show: function(title='',message='',type='success',positionClass=''){
            toastr.options = {
              "closeButton": false,
              "debug": false,
              "newestOnTop": false,
              "progressBar": false,
              "positionClass": (positionClass?positionClass:"toast-top-right"),
              "preventDuplicates": false,
              "onclick": null,
              "showDuration": "300",
              "hideDuration": "1000",
              "timeOut": "5000",
              "extendedTimeOut": "1000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
            };
            if(type){
                if(type=='success'){
                    toastr.success(message,title);
                }else if(type=='info'){
                    toastr.info(message,title);
                }else if(type=='warning'){
                    toastr.warning(message,title);
                }else if(type=='error'){
                    toastr.error(message,title);
                }
                
            }
        }
    }
}();

var UpdateSaveContinueButton = function(step=1){
    return {
        init: function(step){
            if(step == 1){
                //$('.btn.btn-setup-next').attr("id","create_group");
            }
        }
    }
}();


var EmailPhoneValidation = function(){
    return {
        init: function(){
            $.validator.addMethod('custom_email', function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i.test(value);
            }, 'Your email is invalid');

            $(".cust_login_phone").keydown(function(event) {
                if ((event.keyCode == 46 || event.keyCode == 8) || (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) ) {
                }
                else {
                    if (event.keyCode < 48 || event.keyCode > 57 ) {
                        event.preventDefault(); 
                    }   
                }
            });
        }
    }
}();

function isValidEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}


$(document).on('change keyup','.m-form.m-form--state input, .m-form.m-form--state select, .m-form.m-form--state textarea',function(){
    if(($(this).parent()).hasClass('has-danger')){
        ($(this).parent()).removeClass('has-danger');
        ($(this).parent()).find('.form-control-feedback').remove();
        ($(this).parent()).find('.m-form__help').slideDown();
    }
    $('.m-form.m-form--state').find(".alert").html('').slideUp();
});

/*$(document).on('change keyup','input.currency',function(){
    alert($(this).val())
   if($(this).val() < 1){
        ($(this).parent()).addClass('has-danger');
        ($(this).parent()).find('.form-control-feedback').remove();
        ($(this).parent()).find('.m-form__help').slideDown();
    }
});*/

function RemoveDangerClass(form=''){
    var dangerclasses = $('.m-form.m-form--state input, .m-form.m-form--state select ,  .m-form.m-form--state textarea');
    $.each(dangerclasses,function(){
        if(($(this).parent()).hasClass('has-danger')){
            ($(this).parent()).removeClass('has-danger');
            ($(this).parent()).find('.form-control-feedback').remove();
            ($(this).parent()).find('.m-form__help').slideDown();
        }
    });
    $('.m-form.m-form--state').find(".alert").html('').slideUp();
    $('.m-form.m-form--state').find(".cancel_form").attr("disabled","disabled");
    if(form){
        form.find(".alert").html('').slideUp();
        form.find(".cancel_form").attr("disabled","disabled");
    }
}

function UpdateGroupSetupPosition($position=0){
    if(group){
        if(position>group.group_setup_position){
            group.group_setup_position = position;
            $.post({
                url: base_url+"/ajax/update_group_setup_position",
                data: {group_id:group.id, group_setup_position:position},
                type: "POST",
            });
        }
    }
}

$(document).ready(function(){
    $(document).on('hidden.bs.modal', '.modal',function () {
        $('.m-form.m-form--state').find(".cancel_form").removeAttr("disabled");
    });
    
    $('#m_login_signin_submit,#m_login_signup_submit,#recover_password_submit_btn').removeAttr('disabled');
    $(document).on('click','a.m-wizard__step-number',function(){
        ManageSetupWizard.init();
    });
    $(document).on('click','.date-picker',function(){
        if($('.datepicker').is(':visible')){
            //console.log($('.date-picker').css('z-index'));
            $('.datepicker').css('z-index','+120');
        }
    });

    
});

function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

String.prototype.replace_all = function(search,replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

var PhoneInputCountry = function(selector = ''){
    return {
        init: function(selector=''){
            $("#multiple-entries .cust_login_phone,.form_submit .cust_login_phone").each(function(){
                if(this.hasAttribute("data-intl-tel-input-id")){
                }else{
                    window.intlTelInput(this, {
                        allowDropdown: true,
                         // autoHideDialCode: false,
                         // autoPlaceholder: "off",
                         // dropdownContainer: document.body,
                         // excludeCountries: ["us"],
                        formatOnDisplay: true,
                         // hiddenInput: "full_number",
                        initialCountry: default_country_code,
                         // localizedCountries: { 'de': 'Deutschland' },
                         // nationalMode: false,
                         // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                         // placeholderNumberType: "MOBILE",
                        preferredCountries: ['ke', 'tz', 'ug'],
                        separateDialCode: true,
                        utilsScript: base_url+'/templates/admin_themes/admin/intl-tel-input/js/utils.js',
                    });
                }
            });
            EmailPhoneValidation.init();
        }
    }
}();



var SnippetCreateContribution = function(){
    $("#create_contribution");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_contribution_button",function (t) {
            t.preventDefault();
            var e = $(this),
            a = $("#create_contribution");   
            var id = a.find('input[name="id"]').val();
            if(id){
            }else{
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                RemoveDangerClass();
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+"/ajax/contributions/create",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        mApp.unblock(a);
                                        a.find(".alert").html('').slideUp();
                                        Toastr.show("Success",response.message,'success');
                                        $('#cancel_create_contribution_form').trigger('click');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            if(current_row != undefined){
                                                $('select.contribution').each(function(){
                                                    $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                                });
                                                $('select[name="contributions['+current_row+']"]').val(response.id).trigger('change');
                                            } 
                                        }else{
                                            load_contributions();
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

const modifyGroupSettings = () => {
    $(document).on('change','.email-statements-checkbox',function() {
        const field = $(this).attr('name');
        if($(this).val()){
            $(this).attr('disabled',true).parent().hide().parent().append('<div class="m-loader" style="width: 30px; display: inline-block;"></div>');;
            // const field = $(this).attr('name');
            const id = 0;
            $.post(base_url+"/ajax/groups/edit_settings",{id: id,enable_send_monthly_email_statements: 1,statement_send_date:1},
            response => {
                if(isJson(response)){
                    var data = $.parseJSON(response);
                    if(data.status == 1){
                        toastr['success'](data.message,'Success');
                    }else{
                        toastr['error'](data.message,'An error occured saving your settings');
                    }
                }else{
                    toastr['error']('<strong>Sorry!</strong> There was a problem processing your request, please resfresh the page and try again','An error occured saving your settings');
                }
                $('input[name="'+field+'"]').attr('disabled',false).parent().show().parent().find('.m-loader').remove();
            });
        }else{
            $(this).attr('disabled',true).parent().parent().append('<div class="m-loader" style="width: 30px; display: inline-block;"></div>');;
            const id = 0;
            $.post(base_url+"/ajax/groups/edit_settings",{id: id,[field]:$(this).val()},
            response => {
                if(isJson(response)){
                    var data = $.parseJSON(response);
                    if(data.status == 1){
                        toastr['success'](data.message,'Success');
                    }else{
                        toastr['error'](data.message,'An error occured saving your settings');
                    }
                }else{
                    toastr['error']('<strong>Sorry!</strong> There was a problem processing your request, please resfresh the page and try again','An error occured saving your settings');
                }
                $('input[name="'+field+'"]').attr('disabled',false).parent().show().parent().find('.m-loader').remove();
            });
        }
    });

    $(document).on('change','.setting-dropdown',function() {
        mApp.block('.email-statements-config', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Applying...'
        });
        const field = $(this).attr('name');
        const id = 0;
        $.post(base_url+"/ajax/groups/edit_settings",{id: id,enable_send_monthly_email_statements: 1,[field]:$(this).val()},
        response => {
            if(isJson(response)){
                var data = $.parseJSON(response);
                if(data.status == 1){
                    toastr['success'](data.message,'Success');
                }else{
                    toastr['error'](data.message,'An error occured saving your settings');
                }
            }else{
                toastr['error']('<strong>Sorry!</strong> There was a problem processing your request, please resfresh the page and try again','An error occured saving your settings');
            }
            mApp.unblock('.email-statements-config');
        });
    });

    $(document).on('change','.setting-checkbox',function() {
        $(this).attr('disabled',true).parent().hide().parent().append('<div class="m-loader" style="width: 30px; display: inline-block;"></div>');
        const field = $(this).attr('name');
        const id = 0;
        $.post(base_url+"/ajax/groups/edit_settings",{id: id,[field]:$(this).prop('checked')},
        response => {
            if(isJson(response)){
                var data = $.parseJSON(response);
                if(data.status == 1){
                    toastr['success'](data.message,'Success');
                }else{
                    toastr['error'](data.message,'An error occured saving your settings');
                }
            }else{
                toastr['error']('<strong>Sorry!</strong> There was a problem processing your request, please resfresh the page and try again','An error occured saving your settings');
            }
            $('input[name="'+field+'"]').attr('disabled',false).parent().show().parent().find('.m-loader').remove();
        });
    });

    $(document).on('click','#submit-more-settings-form',function() {
        mApp.block('.modal-body', {
            overlayColor: 'grey',
            animate: true,
            type: 'loader',
            state: 'primary',
            message: 'Loading...'
        });
        $('#submit-more-settings-form').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
        const form = $('#more-settings-form');
        form.ajaxSubmit({
            type: "POST",
            url: base_url+"/ajax/groups/edit_settings",
            success: function(response) {
                if(isJson(response)){
                    var data = $.parseJSON(response);
                    if(data.status == 1){
                        toastr['success'](data.message,'Success');
                        $('.modal .close').trigger('click');
                    }else{
                        $('#more-settings-form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>').slideDown();
                        $(".modal").animate({ scrollTop: 0 }, 600);
                        if(data.validation_errors){
                            $.each(data.validation_errors, function( key, value ) {
                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                $('#more-settings-form input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                $('#more-settings-form select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                            });
                        }
                    }
                }else{
                    toastr['error']('<strong>Sorry!</strong> There was a problem processing your request, please resfresh the page and try again','An error occured saving your settings');
                }
                $('#submit-more-settings-form').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                mApp.unblock('.modal-body', {});
            }
        });
    });
}

var SnippetEditContribution = function(){
    $("#create_contribution");
    var t = function (redirect) {
        edit_contribution_init = 1;
        $(document).on('click',".btn#create_contribution_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_contribution");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting contribution updates...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/ajax/contributions/edit",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    if(response.hasOwnProperty('refer') && redirect==true){
                                        window.location = response.refer;
                                    }else{
                                        $('#cancel_create_contribution_form').trigger('click');
                                        load_contributions();
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
            
        })
    };
    return {
        init: function (redirect=true) {
            if(edit_contribution_init == 0){
                t(redirect)
            }
        }
    }
}();

var SnippetCreateContributionRefund = function(){ 
    $("#create_contribution_refund_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_contribution_refund",function (t) {
            t.preventDefault();
            var e = $(this),
            a = $("#create_contribution_refund_form");               
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/ajax/contribution_refunds/create",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    mApp.unblock(a);
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    $('#cancel_create_contribution_form').trigger('click');
                                    if(response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        if(current_row != undefined){
                                            $('select.contribution').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('select[name="contributions['+current_row+']"]').val(response.id).trigger('change');
                                        } 
                                    }else{
                                        //load_contributions();
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetCreateLoanType = function(){
    $("#create_loan_type");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_loan_type_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_loan_type");
            var id = a.find('input[name="id"]').val();
            if(id){
            }else{
                RemoveDangerClass();
                mApp.block(a,{
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary'
                });
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url:   base_url+"/ajax/loan_types/create",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                        a.find(".alert").html('').slideUp();
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('select.loan_type').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                        }else{ 
                                            group.group_offer_loans = 1;
                                            $('#cancel_create_loan_type_form').trigger('click');                                     
                                            load_loan_types();
                                        }                                    
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditLoanType = function(){
    $("#create_loan_type");
    var t = function (redirect=true){
        edit_loan_type_init = 1;
        $(document).on('click',".btn#create_loan_type_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_loan_type");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting data...'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/loan_types/edit",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    if(response.hasOwnProperty('refer') && redirect){
                                        window.location = response.refer;
                                    }else{
                                        $('#cancel_create_loan_type_form').trigger('click');
                                        load_loan_types();
                                    }
                                    
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));                
        })
    };
    return {
        init: function (redirect=true) {
            if(edit_loan_type_init==0){
                t(redirect)
            }
        }
    }
}();

function validate_fine_member_form(){
    var entries_are_valid = true;
    $('.fine-category-table input.payment_dates').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.fine-category-table select.member').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().parent().removeClass('has-danger');
        }
    });

    $('.fine-category-table select.fine_category').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().parent().removeClass('has-danger');
        }
    });

    $('.fine-category-table input.amount').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            var amount = $(this).val();
            regex = /^[0-9.,\b]+$/;;
            if(regex.test(amount)){
                if(amount < 1){
                    $(this).parent().addClass('has-danger');
                    entries_are_valid = false;
                }else{
                    $(this).parent().removeClass('has-danger');
                }
            }else{ 
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }
        }
    });

    if(entries_are_valid){
        return true;
    }else{
        error_message = "Sorry! There are errors on the form, please review the highlighted fields and try submitting again.";
        return false;
    }
}

var SnippetFineMembers = function(){        
    $("#fine_members_form");
    var t = function () {
        $(document).on('click',".btn#fine_members",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#fine_members_form");
            RemoveDangerClass();
            if(validate_fine_member_form(a)==false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url:   base_url+"/ajax/fines/fine_members",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        Toastr.show("Success",response.message,'success');
                                        if(response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else{

                                        }

                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);                                    
                                    if(validation_errors){
                                        var count = Object.keys(validation_errors.members).length;
                                        for (var i = 0; i <= count; i++) {
                                           $.each(validation_errors, function( key, value ) {
                                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                                $('input[name="'+key+"["+i+']"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="'+key+"["+i+']"]').parent().addClass('has-danger').append(error_message);
                                                ($('input[name="'+key+"["+i+']"]').parent()).find('.m-form__help').slideUp();
                                            });
                                        }
                                        
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }            
        })
    };
    return {
        init: function () {
            t()
        }
    }
}();

var SnippetAddBankAccount = function(){
    $("#add_bank_account");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#add_bank_account_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#add_bank_account");
            var id = a.find('input[name="id"]').val();
            if(id){
            }else{
                RemoveDangerClass();
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url:   base_url+"/ajax/bank_accounts/create",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp(),mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('#cancel_add_bank_account_form').trigger('click');
                                        }else{
                                            $('#cancel_add_bank_account_form').trigger('click');
                                            load_bank_accounts(response.id,response.is_default?true:false);
                                        }                                   
                                        
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message),mApp.unblock(a);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'[]"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'[]"]').parent()).find('.m-form__help').slideUp();
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditBankAccount = function(){
    $("#add_bank_account");
    var t = function (redirect,modal) {
        edit_bank_account_init = 1;
        $(document).on('click',".btn#add_bank_account_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#add_bank_account");
            var id = a.find('input[name="id"]').val();
            if(id){
                RemoveDangerClass();
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'processing...'
                });
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url:   base_url+"/ajax/bank_accounts/edit",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp(),mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('#cancel_add_bank_account_form').trigger('click');
                                        }else{
                                            $('#cancel_add_bank_account_form').trigger('click');
                                            load_bank_accounts(response.id,response.is_default?true:false);
                                        }                                   
                                        
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message),mApp.unblock(a);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'[]"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'[]"]').parent()).find('.m-form__help').slideUp();
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            if(edit_bank_account_init == 0){
                t(redirect,modal);
            }else{

            }
        }
    }
}();

var SnippetComposeSms = function(){
    $("#add_compose_message");
    var t = function () {
        $(document).on('click',".btn#compose_sms_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#add_compose_message");
            RemoveDangerClass();
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                'message': 'submitting message data...'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/sms/create",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    if(response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else{

                                    }

                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('textarea[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
            
        })
    };
    return {
        init: function () {
            t()
        }
    }
}();

var error_message;
function valid_form(form){
    var valid = true;
    if(form){
        error_message='';
        var names = form.find('.names');
        invalid_name = false;
        invalid_phone = false;
        invalid_email_addresses = false;
        invalid_group_role = false;
        names.each(function(){
            if($(this).val()){
                if($(this).parent().hasClass('has-danger')){
                    $(this).parent().removeClass('has-danger');
                }
            }else{
                $(this).parent().addClass('has-danger');
                invalid_name = true;
            }
        });
        if(invalid_name){
            error_message = "Ensure all the names fields are not empty<br/>";
            valid = false;
        }

        var phones = form.find('.phones');
        phones.each(function(){
            if($(this).val()){
                if($(this).parent().hasClass('has-danger')){
                    $(this).parent().removeClass('has-danger');
                }
            }else{
                
                $(this).parent().addClass('has-danger');
                invalid_phone = true;
            }
        });
        if(invalid_phone){
            error_message +="Ensure all the phone number fields are not empty<br/>";
            valid = false;
        }

        var email_addresses = form.find('.email_addresses');
        email_addresses.each(function(){
            if($(this).val()){
                email = $(this).val();
                if(isValidEmail(email)){
                    $(this).parent().removeClass('has-danger');
                }else{
                    $(this).parent().addClass('has-danger');
                }
            }else{
                $(this).parent().removeClass('has-danger');
            }
        });

        if(invalid_email_addresses){
            error_message +="Ensure you enter valid email address <br/>";
            valid = false;
        }

        var group_role_ids = form.find('.group_role_ids');
        var group_roles = [];
        group_role_ids.each(function(){
            val = $(this).val()+"";
            if(val == "0" || val==""){

            }else{
                //console.log("val and array "+val+" "+group_roles+" in array "+$.inArray(val,group_roles));
                if($.inArray(val,group_roles) >= 0){
                    $(this).parent().addClass('has-danger');
                    invalid_group_role = true;
                }else{
                    $(this).parent().removeClass('has-danger');
                    group_roles.push(val);
                }
            }
        });
        //console.log(group_roles);
        if(invalid_group_role){
            error_message +="Two members cannot have the same group role <br/>";
            valid = false;
        }

    }
    return valid;
}

var SnippetAddMembersLine = function(){
    $("#add_new_members_line");
    var t = function (redirect) {
        $(document).on('click',".btn#add_new_members_line_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#add_new_members_line");
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                RemoveDangerClass();
            if(valid_form(a)==false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{

                a.find(".alert").html('').slideUp();
                var calling_codes = [];
                $('.selected-dial-code').each(function(key,value){
                    value  = $(this).html();
                    calling_codes.push(value);
                });
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    data: {"calling_codes": calling_codes},
                    url: base_url+"/ajax/members/add_members",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp(),mApp.unblock(a);
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect){                                        
                                            if(response.hasOwnProperty('refer')){
                                                window.location = response.refer;
                                            }
                                        }else{
                                            $('#cancel_add_members_form').trigger('click');
                                            load_members();
                                        }                                        
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                    $.each(validation_errors, function( key, value ) {
                                        message+= value+"<br/>";
                                    });
                                }else{
                                    message = response.message;
                                }
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger",message)
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
            
        })
    };
    return {
        init: function (redirect = true) {
            t(redirect)
        }
    }
}();

var SnippetEditMemberLine = function(){  
    $("#edit_members_line");
    var t = function (redirect) {
        $(document).on('click',".btn#edit_members_form",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#edit_members_line");
                /*mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });*/
                RemoveDangerClass();
            if(valid_form(a)==false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{
                a.find(".alert").html('').slideUp();
                var calling_codes = [];
                $('.selected-dial-code').each(function(key,value){
                    value  = $(this).html();
                    calling_codes.push(value);
                });
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    data: {"calling_codes": calling_codes},
                    url: base_url+"/ajax/members/edit_members",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect){                                        
                                            if(response.hasOwnProperty('refer')){
                                                window.location = response.refer;
                                            }
                                        }else{
                                            $('#cancel_add_members_form').trigger('click');
                                            load_members();
                                        }                                        
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                    $.each(validation_errors, function( key, value ) {
                                        message+= value+"<br/>";
                                    });
                                }else{
                                    message = response.message;
                                }
                                mApp.unblock(a);
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger",message)
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
            
        })
    };
    return {
        init: function (redirect = true) {
            t(redirect)
        }
    }

}();

var SnippetCreateGroupRole = function(){
    $("#add_new_member_role_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#add_new_role_btn",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#add_new_member_role_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+'/ajax/group_roles/create',
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    mApp.unblock(a);
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        $('#group_role').each(function(){
                                            $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                        });
                                    }else{                                       
                                        $('#close_add_role').trigger('click');
                                    }

                                    $('select.group_role_ids').each(function(){
                                        $(this).prepend('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                    });
                                    $('select[name="group_role_ids['+add_new_role_position+']"]').val(response.id).trigger('change');
                                    $('#close_add_role').trigger('click');
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditGroupRoles = function(){ 
    $("#group_roles");
    var t = function () {
        $(document).on('click',".btn#create_group_roles",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#group_roles");
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/group_roles/edit",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    if(response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else{

                                    }

                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
            
        })
    };
    return {
        init: function () {
            t()
        }
    }
}();

var SnippetUploadContributionPayments = function () {
    $("#upload_contribution_payments");
    var t = function () {
        $(document).on('click',".btn#upload_contribution_payments_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#upload_contribution_payments");
            a.find(".alert").html('').slideUp();
            var form = $('#upload_contribution_payments');
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass();
            if($('.contribution_import_file').val()){
                var data = new FormData();
                // var data = new FormData(document.getElementById("upload_contribution_payments"));
                jQuery.each(jQuery('.contribution_import_file')[0].files, function(i, file) {
                    data.append('contribution_imports', file);
                });
                data.append("import", true);
                data.append("account_id", $('#account_id').val());
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0), $.ajax({
                    url: base_url+'/ajax/deposits/upload_contribution_payments',
                    data: data,
                    type: "POST",
                    enctype: 'multipart/form-data',
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        Toastr.show("Success",response.message,'success');
                                        if(response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }                                        
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                        form.find('#choose_contribution_file').html("Choose Excel File").removeClass("selected");
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }else{
                setTimeout(function () {
                    mApp.unblock(a);
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger", "Kindly choose upload file first.")
                }, 2e3)
            }
        })
    };
    return {
        init: function (redirect=false) {
            t(redirect)
        }
    }
}();

var SnippetImportMembers = function () {
    // $("#upload_excel_file");
    var t = function (redirect) {
        $(document).on('click',".btn#upload_excel_file_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#upload_excel_file");
            a.find(".alert").html('').slideUp();
            var form = $('#upload_excel_file');
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass();
            if($('.member_import_file').val()){
                var data = new FormData();
                jQuery.each(jQuery('.member_import_file')[0].files, function(i, file) {
                    data.append('member_imports', file);
                });
                data.append("import", true);
                form.find('#choose_member_file').html("Choose Excel File").removeClass("selected");
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0), $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: base_url+'/ajax/members/import_members',
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect){
                                            if(response.hasOwnProperty('refer')){
                                                window.location = response.refer;
                                            }else{
                                                $('#cancel_upload_members_excel').trigger('click');
                                                load_members();
                                            }
                                        }else{
                                            $('#cancel_upload_members_excel').trigger('click');
                                            load_members();
                                        }                                        
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                    $.each(validation_errors, function( key, value ) {
                                        message+= value+"<br/>";
                                    });
                                }else{
                                    message = response.message;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger",message)
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }else{
                setTimeout(function () {
                    mApp.unblock(a);
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger", "Kindly choose upload file first.")
                }, 2e3)
            }
        })
    };
    return {
        init: function (redirect=false) {
            t(redirect)
        }
    }
}();

var SnippetCreateInvoice = function(){
    $("#create_invoice");
    var t = function (redirect) {
        $(document).on('click',".btn#create_invoice_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_invoice");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+'/ajax/invoices/create',
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    mApp.unblock(a);
                                    Toastr.show("Success",response.message,'success');                                       
                                    if(response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true) {
            t(redirect)
        }
    }
}();

var SnippetCreateFIneCategory = function(){    
    $("#create_fine_category");
    var t = function (redirect) {
        $(document).on('click',".btn#create_fine_category_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_fine_category");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+'/ajax/fine_categories/create',
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    mApp.unblock(a);
                                    Toastr.show("Success",response.message,'success');                                       
                                    if(response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true) {
            t(redirect)
        }
    }
}();

var SnippetEditFIneCategory = function(){    
    $("#create_fine_category");
    var t = function (redirect) {
        $(document).on('click',".btn#create_fine_category_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_fine_category");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+'/ajax/fine_categories/edit',
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    mApp.unblock(a);
                                    Toastr.show("Success",response.message,'success');                                       
                                    if(response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true) {
            t(redirect)
        }
    }
}();

var SnippetCreateContributionTransfer = function(){  
    $("#record_contribution_transfer");
    var t = function (redirect) {
        $(document).on('click',".btn#create_contribution_transfers_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#record_contribution_transfer");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+'/ajax/deposits/record_contribution_transfers',
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    mApp.unblock(a);
                                    Toastr.show("Success",response.message,'success');                                       
                                    if(response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true) {
            t(redirect)
        }
    }
}();

var SnippetCreateAsset = function(){  
    $("#record_asset_name");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#asset_name",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#record_asset_name");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+'/ajax/assets/create',
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);

                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    mApp.unblock(a);
                                    Toastr.show("Success",response.message,'success'); 
                                    $('#cancel_asset_name_modal').trigger('click');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        $('.table-multiple-items .assets').each(function(){
                                            $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                        });
                                    }else{                                       
                                        $('#cancel_asset_name_modal').trigger('click');
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        // ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        // ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        // ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

function validate_asset_purchase_form(){
    var entries_are_valid = true;
    $('.table-multiple-items input.payment_dates').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.table-multiple-items select.assets').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.table-multiple-items select.accounts').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.table-multiple-items select.payment_methods').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.table-multiple-items input.amounts').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            var amount = $(this).val();
            regex = /^[0-9.,\b]+$/;;
            if(regex.test(amount)){
                if(amount < 1){
                    $(this).parent().addClass('has-danger');
                    entries_are_valid = false;
                }else{
                    $(this).parent().removeClass('has-danger');
                }
            }else{ 
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }
        }
    });

    if(entries_are_valid){
        return true;
    }else{
        error_message = "Sorry! There are errors on the form, please review the highlighted fields and try submitting again.";
        return false;
    }
}

var SnippetRecordeAssetPurchase = function(){
    $("#record_asset_purchase");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#submit_form_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#record_asset_purchase");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_asset_purchase_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/ajax/assets/record_asset_purchase_payments',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);

                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success'); 
                                        $('#cancel_asset_name_modal').trigger('click');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.table-multiple-items .assets').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                        }else{                                       
                                            $('#cancel_asset_name_modal').trigger('click');
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();
var current_row = 0;
$(document).on('select2:open','.expense_category', function(e) {
    // do something
    var length = $('.expense_category').children('option').length;
    var count  = $('.expense_category').length;
    current_row = count;
});

var SnippetCreateExpenseCategory = function(){
    $("#record_expense_category");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#category_expense_category_btn",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#record_expense_category");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);               
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+'/group/expense_categories/ajax_create',
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    mApp.unblock(a);
                                    Toastr.show("Success",response.message,'success');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        $('.record_expense_table select.expense_category').each(function(){
                                            $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                        });
                                        $('.record_expense_table select[name="expense_categories['+current_row+']"]').val(response.id).trigger('change');
                                        $('#cancel_expense_name_modal').trigger('click');
                                    }else{                                       
                                        $('#cancel_expense_name_modal').trigger('click');
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

function validate_expense_form(){
    var entries_are_valid = true;
    $('.record_expense_table input.expense_dates').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.record_expense_table select.expense_category').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.record_expense_table select.account').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.record_expense_table select.withdrawal_method').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('record_expense_table.table-multiple-items input.amount').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            var amount = $(this).val();
            regex = /^[0-9.,\b]+$/;;
            if(regex.test(amount)){
                if(amount < 1){
                    $(this).parent().addClass('has-danger');
                    entries_are_valid = false;
                }else{
                    $(this).parent().removeClass('has-danger');
                }
            }else{ 
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }
        }
    });

    if(entries_are_valid){
        return true;
    }else{
        error_message = "Sorry! There are errors on the form, please review the highlighted fields and try submitting again.";
        return false;
    }
}

function validate_dividends_form(){
    var entries_are_valid = true;
    $('#record_dividends_form input.expense_dates').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('#record_dividends_form select.member_ids').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('#record_dividends_form select.account').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('#record_dividends_form select.withdrawal_method').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('record_dividends_form input.amount').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            var amount = $(this).val();
            regex = /^[0-9.,\b]+$/;;
            if(regex.test(amount)){
                if(amount < 1){
                    $(this).parent().addClass('has-danger');
                    entries_are_valid = false;
                }else{
                    $(this).parent().removeClass('has-danger');
                }
            }else{ 
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }
        }
    });

    if(entries_are_valid){
        return true;
    }else{
        error_message = "Sorry! There are errors on the form, please review the highlighted fields and try submitting again.";
        return false;
    }
}

var SnippetRecordExpense = function(){
    $("#create_expense_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#record_expense",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_expense_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_expense_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/ajax/withdrawals/record_expenses',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);

                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.record_expense_table select.expense_category').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }else{                                       
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var recordDividendPayments = function(){
    var t = function (redirect,modal) {
        $(document).on('click',".btn#record_dividends",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#record_dividends_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_dividends_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/ajax/withdrawals/record_dividend_payments',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.record_expense_table select.expense_category').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }else{                                       
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                               
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                   
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetRecordAccountTransfer = function(){    
    $("#record_transfer");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#record_transfer_btn",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#record_transfer");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_expense_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/ajax/accounts/ajax_record_transfer',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);

                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.record_expense_table select.expense_category').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }else{                                       
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetSuspendMember = function(){
    $("#suspend_member_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#suspend_member_action",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#suspend_member_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            swal( {
                title: "Are you sure?", text: "You won't be able to revert this!", type: "warning", showCancelButton: !0, confirmButtonText: "Yes, Suspend!", cancelButtonText: "No, cancel!", reverseButtons: !0
            }).then(function(alert_e) {
                if(alert_e.value){
                    (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                        type: "POST",
                        url: base_url+'/ajax/members/suspend_member',
                        success: function (t, i, n, r) {
                            if(isJson(t)){
                                response = $.parseJSON(t);
                                if(response.status == '1'){
                                    setTimeout(function () {
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                            a.find(".alert").html('').slideUp();
                                            mApp.unblock(a);
                                            if(redirect && response.hasOwnProperty('refer')){
                                                alert_e.value&&swal("Success!",response.message,"success").then(function(){
                                                    window.location = response.refer;
                                                });                                                
                                            }else if(modal){                                                
                                                $('#cancel_suspend_member_action').trigger('click');
                                            }else{                                       
                                                $('#cancel_suspend_member_action').trigger('click');
                                            }
                                    }, 2e3)
                                }else if(response.status == '202'){
                                    Toastr.show("Session Expired",response.message,'error');
                                    window.location.href = response.refer;
                                }else{
                                    var message = response.message;
                                    var validation_errors = '';
                                    var fine_validation_errors = '';
                                    if(response.hasOwnProperty('validation_errors')){
                                        validation_errors = response.validation_errors;
                                    }
                                    if(response.hasOwnProperty('fine_validation_errors')){
                                        fine_validation_errors = response.fine_validation_errors;
                                    }
                                    setTimeout(function () {
                                        mApp.unblock(a);
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", message);
                                        if(validation_errors){
                                            $.each(validation_errors, function( key, value ) {
                                                var error_message ='<div class="form-control-feedback">'+value+'</div>';                                                $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp(error_message);
                                                ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp(error_message);
                                                $('textarea[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                
                                            });
                                        }
                                        if(fine_validation_errors){
                                            $.each(fine_validation_errors, function( key, value ) {
                                                if(value){
                                                    $.each(value,function(keyval, valueval){
                                                        var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                        $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                        $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    });
                                                }
                                            });
                                        }
                                        mUtil.scrollTop();
                                    }, 2e3)
                                }
                            }else{
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", "Could not complete processing the request at the moment.")
                                }, 2e3)
                            }
                        },
                        error: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        },
                        always: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        }
                    }));
                }else{
                    mApp.unblock(a);
                    swal("Cancelled", "Member Was Not Suspended ", "error")
                }                
            });                
                
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetCreateExpenseCategory = function(){
    $("#create_expense_category");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_expense_category_button",function (t) {
            t.preventDefault();
            var e = $(this),
            a = $("#create_expense_category");               
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/group/expense_categories/ajax_create",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    mApp.unblock(a);
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    $('#cancel_create_contribution_form').trigger('click');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        if(current_row != undefined){
                                            $('select.contribution').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('select[name="contributions['+current_row+']"]').val(response.id).trigger('change');
                                        } 
                                    }else{
                                        load_contributions();
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditExpenseCategory = function(){
    $("#create_expense_category");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_expense_category_button",function (t) {
            t.preventDefault();
            var e = $(this),
            a = $("#create_expense_category");               
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/group/expense_categories/ajax_edit",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    mApp.unblock(a);
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    $('#cancel_create_contribution_form').trigger('click');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        if(current_row != undefined){
                                            $('select.contribution').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('select[name="contributions['+current_row+']"]').val(response.id).trigger('change');
                                        } 
                                    }else{
                                        load_contributions();
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetCreateIncomeCategory = function(){
    $("#create_income_categories");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_income_categories_button",function (t) {
            t.preventDefault();
            var e = $(this),
            a = $("#create_income_categories");               
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/group/income_categories/ajax_create",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    mApp.unblock(a);
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    $('#cancel_create_contribution_form').trigger('click');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        if(current_row != undefined){
                                            $('select.contribution').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('select[name="contributions['+current_row+']"]').val(response.id).trigger('change');
                                        } 
                                    }else{
                                        load_contributions();
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditIncomeCategory = function(){
    $("#create_income_categories");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_income_categories_button",function (t) {
            t.preventDefault();
            var e = $(this),
            a = $("#create_income_categories");               
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/group/income_categories/ajax_edit",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    mApp.unblock(a);
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    $('#cancel_create_contribution_form').trigger('click');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        if(current_row != undefined){
                                            $('select.contribution').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('select[name="contributions['+current_row+']"]').val(response.id).trigger('change');
                                        } 
                                    }else{
                                        load_contributions();
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }

}();

var SnippetAssetCreateCategory = function(){
    $("#create_income_categories");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_income_categories_button",function (t) {
            t.preventDefault();
            var e = $(this),
            a = $("#create_income_categories");               
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/group/asset_categories/ajax_create",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    mApp.unblock(a);
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    $('#cancel_create_contribution_form').trigger('click');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        $('select.asset_category_id').each(function(){
                                            $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                        });
                                        $('select[name="asset_category_id"]').val(response.id).trigger('change');
                                        $('#asset_category_form .close').trigger('click');
                                        /* no multiple items table uses asset category */
                                        // $('select.asset_categories').each(function(){
                                        //     $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                        // });
                                        // if(current_row){
                                        //     $('select[name="asset_categories['+current_row+']"]').val(response.id).trigger('change');
                                        // } 
                                    }else{
                                        //load_contributions(); //but why?????
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    if(validation_errors.hasOwnProperty('name') && validation_errors.hasOwnProperty('slug')){
                                        $.each(validation_errors, function( key, value ) {
                                            if(key == 'slug'){
                                                //do nothing
                                            }else{
                                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                                $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            }
                                        });
                                    }else{
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            if(key == 'slug'){
                                                $('input[name="name"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="name"]').parent().addClass('has-danger').append(error_message);
                                            }else{
                                                $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            }
                                        });
                                    }
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetAssetEditCategory = function(){
    $("#create_income_categories");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_income_categories_button",function (t) {
            t.preventDefault();
            var e = $(this),
            a = $("#create_income_categories");               
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/group/asset_categories/ajax_edit",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    mApp.unblock(a);
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    $('#cancel_create_contribution_form').trigger('click');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        if(current_row != undefined){
                                            $('select.contribution').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('select[name="contributions['+current_row+']"]').val(response.id).trigger('change');
                                        } 
                                    }else{
                                        load_contributions();
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

function validate_group_account_managers_form(){
    var entries_are_valid = true;    
    $('.account-managers-table input.first_name').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.account-managers-table input.last_name').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.account-managers-table input.phone').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    if(entries_are_valid){
        return true;
    }else{
        error_message = "Sorry! There are errors on the form, please review the highlighted fields and try submitting again.";
        return false;
    }
}

var SnippetAddGroupAccountManagers = function(){
    $("#add_group_account_managers_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#add_group_account_managers",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#add_group_account_managers_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_group_account_managers_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/group/group_account_managers/ajax_add_group_account_managers',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);

                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.record_expense_table select.expense_category').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }else{                                       
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditGroupAccountManagers = function(){    
    $("#edit_group_account_managers_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#edit_group_manager_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#edit_group_account_managers_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_group_account_managers_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/group/group_account_managers/ajax_edit_group_account_managers',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);

                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.record_expense_table select.expense_category').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }else{                                       
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetCreateSaccoAccount = function(){
    $("#create_sacco_accounts");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_sacco_account_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_sacco_accounts");
            var id = a.find('input[name="id"]').val();
            if(id){
            }else{
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                RemoveDangerClass(a);
                if(validate_group_account_managers_form(a)== false){
                    mApp.unblock(a);
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger",error_message)
                    }, 2e2)
                }else{                
                    (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                        type: "POST",
                        url: base_url+'/ajax/sacco_accounts/create',
                        success: function (t, i, n, r) {
                            if(isJson(t)){
                                response = $.parseJSON(t);

                                if(response.status == '1'){
                                    setTimeout(function () {
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                            a.find(".alert").html('').slideUp();
                                            mApp.unblock(a);
                                            Toastr.show("Success",response.message,'success');
                                            if(redirect && response.hasOwnProperty('refer')){
                                                window.location = response.refer;
                                            }else if(modal){
                                                $('.record_expense_table select.expense_category').each(function(){
                                                    $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                                });
                                                $('#cancel_expense_name_modal').trigger('click');
                                            }else{                                       
                                                $('#cancel_add_bank_account_form').trigger('click');
                                                load_bank_accounts();
                                            }
                                    }, 2e3)
                                }else if(response.status == '202'){
                                    Toastr.show("Session Expired",response.message,'error');
                                    window.location.href = response.refer;
                                }else{
                                    var message = response.message;
                                    var validation_errors = '';
                                    var fine_validation_errors = '';
                                    if(response.hasOwnProperty('validation_errors')){
                                        validation_errors = response.validation_errors;
                                    }
                                    if(response.hasOwnProperty('fine_validation_errors')){
                                        fine_validation_errors = response.fine_validation_errors;
                                    }
                                    setTimeout(function () {
                                        mApp.unblock(a);
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", message);
                                        if(validation_errors){
                                            $.each(validation_errors, function( key, value ) {
                                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                                $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                
                                            });
                                        }
                                        if(fine_validation_errors){
                                            $.each(fine_validation_errors, function( key, value ) {
                                                if(value){
                                                    $.each(value,function(keyval, valueval){
                                                        var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                        $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                        $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    });
                                                }
                                            });
                                        }
                                        mUtil.scrollTop();
                                    }, 2e3)
                                }
                            }else{
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", "Could not complete processing the request at the moment.")
                                }, 2e3)
                            }
                        },
                        error: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        },
                        always: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        }
                    }));
                }
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditSaccoAccount = function(){ 
    var t = function (redirect,modal) {
        edit_sacco_account_init = 1;
        $(document).on('click',".btn#create_sacco_account_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_sacco_accounts");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_group_account_managers_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/ajax/sacco_accounts/edit',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.record_expense_table select.expense_category').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }else{                                       
                                            $('#cancel_add_bank_account_form').trigger('click');
                                            load_bank_accounts();
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            if(edit_sacco_account_init == 0){
                t(redirect,modal)
            }
        }
    }
}();

var SnippetCreateMobileMoneyAccount = function(){
    $("#mobile_money_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_mobile_money_form_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#mobile_money_form");
            var id = a.find('input[name="id"]').val();
            if(id){
            }else{
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                RemoveDangerClass(a);
                if(validate_group_account_managers_form(a)== false){
                    mApp.unblock(a);
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger",error_message)
                    }, 2e2)
                }else{                
                    (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                        type: "POST",
                        url: base_url+'/ajax/mobile_money_accounts/create',
                        success: function (t, i, n, r) {
                            if(isJson(t)){
                                response = $.parseJSON(t);

                                if(response.status == '1'){
                                    setTimeout(function () {
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                            a.find(".alert").html('').slideUp();
                                            mApp.unblock(a);
                                            Toastr.show("Success",response.message,'success');
                                            if(redirect && response.hasOwnProperty('refer')){
                                                window.location = response.refer;
                                            }else if(modal){
                                                $('.record_expense_table select.expense_category').each(function(){
                                                    $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                                });
                                                $('#cancel_expense_name_modal').trigger('click');
                                            }else{                                       
                                                $('#cancel_add_bank_account_form').trigger('click');
                                                load_bank_accounts();
                                            }
                                    }, 2e3)
                                }else if(response.status == '202'){
                                    Toastr.show("Session Expired",response.message,'error');
                                    window.location.href = response.refer;
                                }else{
                                    var message = response.message;
                                    var validation_errors = '';
                                    var fine_validation_errors = '';
                                    if(response.hasOwnProperty('validation_errors')){
                                        validation_errors = response.validation_errors;
                                    }
                                    if(response.hasOwnProperty('fine_validation_errors')){
                                        fine_validation_errors = response.fine_validation_errors;
                                    }
                                    setTimeout(function () {
                                        mApp.unblock(a);
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", message);
                                        if(validation_errors){
                                            $.each(validation_errors, function( key, value ) {
                                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                                $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                
                                            });
                                        }
                                        if(fine_validation_errors){
                                            $.each(fine_validation_errors, function( key, value ) {
                                                if(value){
                                                    $.each(value,function(keyval, valueval){
                                                        var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                        $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                        $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    });
                                                }
                                            });
                                        }
                                        mUtil.scrollTop();
                                    }, 2e3)
                                }
                            }else{
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", "Could not complete processing the request at the moment.")
                                }, 2e3)
                            }
                        },
                        error: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        },
                        always: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        }
                    }));
                }
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditMobileMoneyAccount = function(){
    $("#mobile_money_form");
    var t = function (redirect,modal) {
        edit_mobile_money_account_init = 1;
        $(document).on('click',".btn#create_mobile_money_form_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#mobile_money_form");

            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_group_account_managers_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/ajax/mobile_money_accounts/edit',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);

                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.record_expense_table select.expense_category').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }else{                                       
                                            $('#cancel_add_bank_account_form').trigger('click');
                                            load_bank_accounts();
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            if(edit_mobile_money_account_init==0){
                t(redirect,modal)
            }            
        }
    }
}();

var SnippetCreatePettyCashAccount = function(){
    $("#petty_cash_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_petty_cash_form_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#petty_cash_form");
            var id = a.find('input[name="id"]').val();
            if(id){
            }else{
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                RemoveDangerClass(a);
                if(validate_group_account_managers_form(a)== false){
                    mApp.unblock(a);
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger",error_message)
                    }, 2e2)
                }else{                
                    (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                        type: "POST",
                        url: base_url+'/ajax/petty_cash_accounts/create',
                        success: function (t, i, n, r) {
                            if(isJson(t)){
                                response = $.parseJSON(t);

                                if(response.status == '1'){
                                    setTimeout(function () {
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                            a.find(".alert").html('').slideUp();
                                            mApp.unblock(a);
                                            Toastr.show("Success",response.message,'success');
                                            if(redirect && response.hasOwnProperty('refer')){
                                                window.location = response.refer;
                                            }else if(modal){
                                                $('.record_expense_table select.expense_category').each(function(){
                                                    $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                                });
                                                $('#cancel_expense_name_modal').trigger('click');
                                            }else{                                       
                                                $('#cancel_add_bank_account_form').trigger('click');
                                                load_bank_accounts();
                                            }
                                    }, 2e3)
                                }else if(response.status == '202'){
                                    Toastr.show("Session Expired",response.message,'error');
                                    window.location.href = response.refer;
                                }else{
                                    var message = response.message;
                                    var validation_errors = '';
                                    var fine_validation_errors = '';
                                    if(response.hasOwnProperty('validation_errors')){
                                        validation_errors = response.validation_errors;
                                    }
                                    if(response.hasOwnProperty('fine_validation_errors')){
                                        fine_validation_errors = response.fine_validation_errors;
                                    }
                                    setTimeout(function () {
                                        mApp.unblock(a);
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", message);
                                        if(validation_errors){
                                            $.each(validation_errors, function( key, value ) {
                                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                                $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                
                                            });
                                        }
                                        if(fine_validation_errors){
                                            $.each(fine_validation_errors, function( key, value ) {
                                                if(value){
                                                    $.each(value,function(keyval, valueval){
                                                        var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                        $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                        $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    });
                                                }
                                            });
                                        }
                                        mUtil.scrollTop();
                                    }, 2e3)
                                }
                            }else{
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", "Could not complete processing the request at the moment.")
                                }, 2e3)
                            }
                        },
                        error: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        },
                        always: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        }
                    }));
                }
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditPettyCashAccount = function(){
    $("#petty_cash_form");
    var t = function (redirect,modal) {
        edit_petty_cash_account_init = 1;
        $(document).on('click',".btn#create_petty_cash_form_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#petty_cash_form");
            var id = a.find('input[name="id"]').val();
            if(id){
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                RemoveDangerClass(a);
                if(validate_group_account_managers_form(a)== false){
                    mApp.unblock(a);
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger",error_message)
                    }, 2e2)
                }else{                
                    (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                        type: "POST",
                        url: base_url+'/ajax/petty_cash_accounts/edit',
                        success: function (t, i, n, r) {
                            if(isJson(t)){
                                response = $.parseJSON(t);

                                if(response.status == '1'){
                                    setTimeout(function () {
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                            a.find(".alert").html('').slideUp();
                                            mApp.unblock(a);
                                            Toastr.show("Success",response.message,'success');
                                            if(redirect && response.hasOwnProperty('refer')){
                                                window.location = response.refer;
                                            }else if(modal){
                                                $('.record_expense_table select.expense_category').each(function(){
                                                    $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                                });
                                                $('#cancel_expense_name_modal').trigger('click');
                                            }else{                                       
                                                $('#cancel_add_bank_account_form').trigger('click');
                                                load_bank_accounts();
                                            }
                                    }, 2e3)
                                }else if(response.status == '202'){
                                    Toastr.show("Session Expired",response.message,'error');
                                    window.location.href = response.refer;
                                }else{
                                    var message = response.message;
                                    var validation_errors = '';
                                    var fine_validation_errors = '';
                                    if(response.hasOwnProperty('validation_errors')){
                                        validation_errors = response.validation_errors;
                                    }
                                    if(response.hasOwnProperty('fine_validation_errors')){
                                        fine_validation_errors = response.fine_validation_errors;
                                    }
                                    setTimeout(function () {
                                        mApp.unblock(a);
                                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", message);
                                        if(validation_errors){
                                            $.each(validation_errors, function( key, value ) {
                                                var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                                $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                                ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                                
                                            });
                                        }
                                        if(fine_validation_errors){
                                            $.each(fine_validation_errors, function( key, value ) {
                                                if(value){
                                                    $.each(value,function(keyval, valueval){
                                                        var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                        $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                        $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    });
                                                }
                                            });
                                        }
                                        mUtil.scrollTop();
                                    }, 2e3)
                                }
                            }else{
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger", "Could not complete processing the request at the moment.")
                                }, 2e3)
                            }
                        },
                        error: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        },
                        always: function(){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                            }, 2e3)
                        }
                    }));
                }
            }else{
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            if(edit_petty_cash_account_init==0){
                t(redirect,modal)
            }
        }
    }
}();

function validate_stock_purchase_form(){
    var entries_are_valid = true;    
    $('.stock-purchase-table input.purchase_date').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.stock-purchase-table input.name').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.stock-purchase-table input.number_of_share').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.stock-purchase-table select.account').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.stock-purchase-table input.price_per_share').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            var amount = $(this).val();
            regex = /^[0-9.,\b]+$/;;
            if(regex.test(amount)){
                if(amount < 1){
                    $(this).parent().addClass('has-danger');
                    entries_are_valid = false;
                }else{
                    $(this).parent().removeClass('has-danger');
                }
            }else{ 
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }
        }
    });

    if(entries_are_valid){
        return true;
    }else{
        error_message = "Sorry! There are errors on the form, please review the highlighted fields and try submitting again.";
        return false;
    }
}

var SnippetRecordStockPurchase  = function(){
    $("#record_stock_purchase_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_stock_purchase",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#record_stock_purchase_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_stock_purchase_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/ajax/stocks/record_stock_purchase',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);

                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.record_expense_table select.expense_category').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }else{                                       
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetSellStocks = function(){ 
    $("#sell_stock");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#sell_stock_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#sell_stock");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+'/ajax/stocks/sell',
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);

                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    mApp.unblock(a);
                                    Toastr.show("Success",response.message,'success');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        $('.record_expense_table select.expense_category').each(function(){
                                            $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                        });
                                        $('#cancel_expense_name_modal').trigger('click');
                                    }else{                                       
                                        $('#cancel_expense_name_modal').trigger('click');
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetSellAsset = function(){ 
    $("#record_asset_sell");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#sell_asset_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#record_asset_sell");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+'/ajax/assets/sell',
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);

                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    a.find(".alert").html('').slideUp();
                                    mApp.unblock(a);
                                    Toastr.show("Success",response.message,'success');
                                    if(redirect && response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }else if(modal){
                                        $('.record_expense_table select.expense_category').each(function(){
                                            $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                        });
                                        $('#cancel_expense_name_modal').trigger('click');
                                    }else{                                       
                                        $('#cancel_expense_name_modal').trigger('click');
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetCreateMoneyMarketInvestments = function(){
    $("#create_money_market_accounts");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_money_market_investments_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_money_market_accounts");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting money market investments....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/money_market_investments/create",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    window.location = response.refer;                                 
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

function encryptdata(pass,data){
    if(data){
        item = {};
        $(data).each(function(i,element){
            name = element.name;
            value = (CryptoJS.AES.encrypt(JSON.stringify(element.value),passphrase, {format: CryptoJSAesJson}).toString());
            item[name] = value;
        });
        return (item);
    }
}

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        var key = decodeURIComponent(sParameterName[0]);
        var value = decodeURIComponent(sParameterName[1]);
        if (key === sParam) {
            return value === undefined ? true : value;
        }
    }
}

function add_months(dt, n) {
    return new Date(dt.setMonth(dt.getMonth() + n));      
}

$(document).on('click','.confirmation_link',function(){
    var element = $(this);
    var message = (element.attr('data-message'))?element.attr('data-message'):'Are you sure you want to proceed';
    //console.log(message);
    bootbox.confirm({
        message: message,
        callback: function(result) {
            if(result==true){
                if (result === null) {
                    return true;
                }else{
                    var href = element.attr('href');
                    window.location = href;
                }
            }else{
                return true;
            }
        }
    });
    return false;
});

$(document).on('click','.confirmation_bulk_action',function(e){
    var element = $(this);
    $('<input />').attr('type', 'hidden').attr('id',"extra_post").attr('name',"btnAction").attr('value',$(this).val()).appendTo($(this));
    bootbox.confirm("Are you sure, you want to proceed?", function(result) {
       if(result==true){
            //submit the form
            form = element.closest('form');
            form.submit();
       }else{
            //close the dialog
            $('#extra_post').on('remove',function(){})
            return true;
       }
    });
    e.preventDefault(); 
});


$(document).on('click','.confirmation_bulk_action',function(e){
        var element = $(this);
        $('<input />').attr('type', 'hidden').attr('id',"extra_post").attr('name',"btnAction").attr('value',$(this).val()).appendTo($(this));
        bootbox.confirm("Are you sure, you want to proceed?", function(result) {
           if(result==true){
                //submit the form
                form = element.closest('form');
                form.submit();
           }else{
                //close the dialog
                $('#extra_post').on('remove',function(){})
                return true;
           }
        });
    e.preventDefault(); 
});

$(document).on('click','.prompt_confirmation_message_link',function(){
    var element = $(this);
    bootbox.confirm({
        message: element.attr('data-content'),
        title: "Before you proceed",
        callback: function(result) {
           if(result==true){
                bootbox.prompt({
                    title: element.attr('data-title'), 
                    inputType: "password",
                    callback: function(result) {
                        if (result === null) {
                            return true;
                        } else {
                            var href = element.attr('href');
                            window.location = href+'?confirmation_string='+encodeURIComponent(result);
                        }
                    }
                });
            }else{
                return true;
            }
        }
    });
    return false;
});



var SnippetCreateMemberLoan = function(){
    $("#create_member_loan_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_member_loan_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_member_loan_form");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting member loan data....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/loans/create",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    window.location = response.refer;                                 
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditMemberLoan = function(){
    $("#create_member_loan_form");
    var t = function (loan_id) {
        $(document).on('click',".btn#create_member_loan_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_member_loan_form");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Updating member loan data....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/loans/edit/"+loan_id,
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    window.location = response.refer;                                 
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else if(response.status == '100'){
                            Toastr.show("Loan update error",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        //console.log('its an else');
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (loan_id) {
            t(loan_id)
        }
    }
}();

var SnippetCreateBankLoan = function(){
    $("#create_bank_loans");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_bank_loan_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_bank_loans");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting member loan data....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/bank_loans/create",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    window.location = response.refer;                                 
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditBankLoan = function(){
    $("#create_bank_loans");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_bank_loan_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_bank_loans");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting member loan data....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/bank_loans/edit",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    window.location = response.refer;                                 
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetCreateDebtor = function(){
    $("#add_new_debtor_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_debtor_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#add_new_debtor_form");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting debtor data....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/debtors/create",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');                                 
                            }, 2e3)
                            if(redirect && response.hasOwnProperty('refer')){
                                window.location = response.refer;
                            }else if(modal){
                                $('select.debtor_form').each(function(){
                                    $(this).append('<option value="' + response.debtor.id + '">' + response.debtor.name + '</option>').trigger('change');
                                });
                                $('select[name="debtor_id['+response.debtor.id+']"]').val(response.debtor.id).trigger('change');
                                $('#debtor_close_modal').click();
                            }else{
                                //load_contributions();
                            }                            
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetCreateDebtorLoan  = function(){
    $("#create_external_loans");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_external_loan_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_external_loans");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting debtor loan data....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/debtors/create_loan",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');                                 
                            }, 2e3)
                            if(redirect && response.hasOwnProperty('refer')){
                                window.location = response.refer;
                            }else if(modal){
                                $('select.debtor_form').each(function(){
                                    $(this).append('<option value="' + response.debtor.id + '">' + response.debtor.name + '</option>').trigger('change');
                                });
                                $('select[name="debtor_id['+response.debtor.id+']"]').val(response.debtor.id).trigger('change');
                                $('#debtor_close_modal').click();
                            }else{
                                //load_contributions();
                            }                            
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetEditDebtorLoan  = function(){
    $("#create_external_loans");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#create_external_loan_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#create_external_loans");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting debtor loan data....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/debtors/edit_loan",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');                                 
                            }, 2e3)
                            if(redirect && response.hasOwnProperty('refer')){
                                window.location = response.refer;
                            }else if(modal){
                                $('select.debtor_form').each(function(){
                                    $(this).append('<option value="' + response.debtor.id + '">' + response.debtor.name + '</option>').trigger('change');
                                });
                                $('select[name="debtor_id['+response.debtor.id+']"]').val(response.debtor.id).trigger('change');
                                $('#debtor_close_modal').click();
                            }else{
                                //load_contributions();
                            }                            
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

function validate_loan_repayments_form(){
    var entries_are_valid = true;
    $('.table-multiple-items input.deposit_date').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.table-multiple-items select.loan').each(function(){
        if($(this).val()==''){
            $(this).parent().parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().parent().removeClass('has-danger');
        }
    });

    $('.table-multiple-items select.debtor').each(function(){
        if($(this).val()==''){
            $(this).parent().parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().parent().removeClass('has-danger');
        }
    });

    $('.table-multiple-items select.deposit_method').each(function(){
        if($(this).val()==''){
            $(this).parent().parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().parent().removeClass('has-danger');
        }
    });

    $('.table-multiple-items input.amount').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            var amount = $(this).val();
            regex = /^[0-9.,\b]+$/;;
            if(regex.test(amount)){
                if(amount < 1){
                    $(this).parent().addClass('has-danger');
                    entries_are_valid = false;
                }else{
                    $(this).parent().removeClass('has-danger');
                }
            }else{ 
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }
        }
    });

    $('.table-multiple-items select.account').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    if(entries_are_valid){
        return true;
    }else{
        return false;
    }
}

var SnippetRepayDebtorLoan  = function(){
    $("#loan_repayments_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#submit_form_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#loan_repayments_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            RemoveDangerClass(a);
            if(validate_loan_repayments_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{                
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url: base_url+'/ajax/deposits/record_debtor_loan_repayments',
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);

                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp();
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect && response.hasOwnProperty('refer')){
                                            window.location = response.refer;
                                        }else if(modal){
                                            $('.record_expense_table select.expense_category').each(function(){
                                                $(this).append('<option value="' + response.id + '">' + response.name + '</option>').trigger('change');
                                            });
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }else{                                       
                                            $('#cancel_expense_name_modal').trigger('click');
                                        }
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    mApp.unblock(a);
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('textarea[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

function validate_bank_loan_repayment_form(){
    var entries_are_valid = true;    
    $('.bank_loan-table input.repayment_date').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.bank_loan-table select.account_id').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.bank_loan-table select.repayment_method').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.bank_loan-table textarea.repayment_descriptions').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            $(this).parent().removeClass('has-danger');
        }
    });

    $('.bank_loan-table input.amount').each(function(){
        if($(this).val()==''){
            $(this).parent().addClass('has-danger');
            entries_are_valid = false;
        }else{
            var amount = $(this).val();
            regex = /^[0-9.,\b]+$/;;
            if(regex.test(amount)){
                if(amount < 1){
                    $(this).parent().addClass('has-danger');
                    entries_are_valid = false;
                }else{
                    $(this).parent().removeClass('has-danger');
                }
            }else{ 
                $(this).parent().addClass('has-danger');
                entries_are_valid = false;
            }
        }
    });

    if(entries_are_valid){
        return true;
    }else{
        error_message = "Sorry! There are errors on the form, please review the highlighted fields and try submitting again.";
        return false;
    }
}
var SnippetBankLoanRepayment = function(){
    $("#bank_loan_repayment_form");
    var t = function (redirect,modal) {
        $(document).on('click',".btn#loan_repayment_form_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#bank_loan_repayment_form");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'submitting member loan data....'
            });
            if(validate_bank_loan_repayment_form(a)== false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{ 
                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    url:   base_url+"/ajax/bank_loans/record_repayment",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                        a.find(".alert").html('').slideUp();
                                        Toastr.show("Success",response.message,'success');
                                        window.location = response.refer;                                 
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = response.message;
                                var validation_errors = '';
                                var fine_validation_errors = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                }
                                if(response.hasOwnProperty('fine_validation_errors')){
                                    fine_validation_errors = response.fine_validation_errors;
                                }
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", message);
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        });
                                    }
                                    if(fine_validation_errors){
                                        $.each(fine_validation_errors, function( key, value ) {
                                            if(value){
                                                $.each(value,function(keyval, valueval){
                                                    var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                    $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                    $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                });
                                            }
                                        });
                                    }
                                    mUtil.scrollTop();
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
        })
    };
    return {
        init: function (redirect = true,modal = false) {
            t(redirect,modal)
        }
    }
}();

var SnippetMemberApplyLoan = function(){
    $("#apply_member_loan_form");
    var t = function () {
        $(document).on('click',".btn#apply_member_loan_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#apply_member_loan_form");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Applying member loan ....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/loans/apply",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    window.location = response.refer;                                 
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else if(response.status == '100'){
                            Toastr.show("Loan update error",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        //console.log('its an else');
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (loan_id) {
            t(loan_id)
        }
    }
}();

var SnippetGuarantorAction = function(){
    $("#guarantor_approval_loan_form");
    var t = function () {
        $(document).on('click',".btn#approve_member_loan_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#guarantor_approval_loan_form");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Submitting guarantor response....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/loans/guarantor_action",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    window.location = response.refer;                                 
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else if(response.status == '100'){
                            Toastr.show("Loan update error",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        //console.log('its an else');
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (loan_id) {
            t(loan_id)
        }
    }
}();

var SnippetSignatoryAction = function(){
    $("#signatory_approval_form");
    var t = function () {
        $(document).on('click',".btn#approve_as_signatory_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#signatory_approval_form");
            RemoveDangerClass();
            mApp.block(a,{
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Submitting signatory response....'
            });
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url:   base_url+"/ajax/loans/signatory_action",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    window.location = response.refer;                                 
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else if(response.status == '100'){
                            Toastr.show("Loan update error",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        //console.log('its an else');
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                    }, 2e3)
                }
            }));
        })
    };
    return {
        init: function (loan_id) {
            t(loan_id)
        }
    }
}();

var SnippetConnectBankAccount = function(){
    $("#connect_bank_account_form");
    var t = function (redirect) {
        $(document).on('click',".btn#connect_bank_account_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#connect_bank_account_form");
                mApp.block(a, {
                    overlayColor: 'grey',
                    animate: true,
                    type: 'loader',
                    state: 'primary',
                    message: 'Processing...'
                });
                RemoveDangerClass();
            if(valid_form(a)==false){
                mApp.unblock(a);
                setTimeout(function () {
                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                        function (t, e, a) {
                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                        }(a, "danger",error_message)
                }, 2e2)
            }else{

                a.find(".alert").html('').slideUp();
                var calling_codes = [];
                $('.selected-dial-code').each(function(key,value){
                    value  = $(this).html();
                    calling_codes.push(value);
                });

                (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                    type: "POST",
                    data: {"calling_codes": calling_codes},
                    url: base_url+"/ajax/bank_accounts/connect_bank_account",
                    success: function (t, i, n, r) {
                        if(isJson(t)){
                            response = $.parseJSON(t);
                            if(response.status == '1'){
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                        a.find(".alert").html('').slideUp(),mApp.unblock(a);
                                        mApp.unblock(a);
                                        Toastr.show("Success",response.message,'success');
                                        if(redirect){                                        
                                            if(response.hasOwnProperty('refer')){
                                                window.location = response.refer;
                                            }
                                        }else{
                                            $('#cancel_add_members_form').trigger('click');
                                            load_members();
                                        }                                        
                                }, 2e3)
                            }else if(response.status == '202'){
                                Toastr.show("Session Expired",response.message,'error');
                                window.location.href = response.refer;
                            }else{
                                var message = '';
                                if(response.hasOwnProperty('validation_errors')){
                                    validation_errors = response.validation_errors;
                                    $.each(validation_errors, function( key, value ) {
                                        message+= value+"<br/>";
                                    });
                                    if(validation_errors){
                                        $.each(validation_errors, function( key, value ) {
                                            var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                            $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                            ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                            ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
                                        });
                                    }
                                }else{
                                    message = response.message;
                                }
                                console.log(message)
                                setTimeout(function () {
                                    e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                        function (t, e, a) {
                                            var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                            t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                        }(a, "danger",message)
                                }, 2e3)
                            }
                        }else{
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                    function (t, e, a) {
                                        var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                        t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                    }(a, "danger", "Could not complete processing the request at the moment.")
                            }, 2e3)
                        }
                    },
                    error: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    },
                    always: function(){
                        setTimeout(function () {
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
                        }, 2e3)
                    }
                }));
            }
            
        })
    };
    return {
        init: function (redirect = true) {
            t(redirect)
        }
    }
}();

// var SnippetVerifyBankAccountOwnership = function(){
//     $("#verify_bank_account_ownership_form");
//     var t = function (redirect) {
//         $(document).on('click',".btn#verify_bank_account_ownership_button",function (t) {
//             t.preventDefault();
//             var e = $(this),
//                 a = $("#verify_bank_account_ownership_form");
//                 mApp.block(a, {
//                     overlayColor: 'grey',
//                     animate: true,
//                     type: 'loader',
//                     state: 'primary',
//                     message: 'Processing...'
//                 });
//                 RemoveDangerClass();
//             if(valid_form(a)==false){
//                 mApp.unblock(a);
//                 setTimeout(function () {
//                     e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
//                         function (t, e, a) {
//                             var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
//                             t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
//                         }(a, "danger",error_message)
//                 }, 2e2)
//             }else{

//                 a.find(".alert").html('').slideUp();
//                 var calling_codes = [];
//                 $('.selected-dial-code').each(function(key,value){
//                     value  = $(this).html();
//                     calling_codes.push(value);
//                 });
//                 (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
//                     type: "POST",
//                     data: {"calling_codes": calling_codes},
//                     url: base_url+"/ajax/bank_accounts/verify_ownership",
//                     success: function (t, i, n, r) {
//                         if(isJson(t)){
//                             response = $.parseJSON(t);
//                             if(response.status == '1'){
//                                 setTimeout(function () {
//                                     e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
//                                         a.find(".alert").html('').slideUp(),mApp.unblock(a);
//                                         mApp.unblock(a);
//                                         Toastr.show("Success",response.message,'success');
//                                         if(redirect){                                        
//                                             if(response.hasOwnProperty('refer')){
//                                                 window.location = response.refer;
//                                             }
//                                         }else{
//                                             $('#cancel_add_members_form').trigger('click');
//                                             load_members();
//                                         }                                        
//                                 }, 2e3)
//                             }else if(response.status == '202'){
//                                 Toastr.show("Session Expired",response.message,'error');
//                                 window.location.href = response.refer;
//                             }else{
//                                 var message = '';
//                                 if(response.hasOwnProperty('validation_errors')){
//                                     validation_errors = response.validation_errors;
//                                     $.each(validation_errors, function( key, value ) {
//                                         message+= value+"<br/>";
//                                     });
//                                     if(validation_errors){
//                                         $.each(validation_errors, function( key, value ) {
//                                             var error_message ='<div class="form-control-feedback">'+value+'</div>';
//                                             $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
//                                             $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
//                                             ($('input[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
//                                             ($('select[name="'+key+'"]').parent()).find('.m-form__help').slideUp();
//                                         });
//                                     }
//                                 }else{
//                                     message = response.message;
//                                 }
//                                 console.log(message)
//                                 setTimeout(function () {
//                                     e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
//                                         function (t, e, a) {
//                                             var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
//                                             t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
//                                         }(a, "danger",message)
//                                 }, 2e3)
//                             }
//                         }else{
//                             setTimeout(function () {
//                                 e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
//                                     function (t, e, a) {
//                                         var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
//                                         t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
//                                     }(a, "danger", "Could not complete processing the request at the moment.")
//                             }, 2e3)
//                         }
//                     },
//                     error: function(){
//                         setTimeout(function () {
//                             e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
//                                 function (t, e, a) {
//                                     var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
//                                     t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
//                                 }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
//                         }, 2e3)
//                     },
//                     always: function(){
//                         setTimeout(function () {
//                             e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),mApp.unblock(a),
//                                 function (t, e, a) {
//                                     var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
//                                     t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
//                                 }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try registration later.")
//                         }, 2e3)
//                     }
//                 }));
//             }
            
//         })
//     };
//     return {
//         init: function (redirect = true) {
//             t(redirect)
//         }
//     }
// }();

var SnippetLinkBankAccount= function(){
    var t = function (getDetails,accountId,bankId,isModal) {
        if(getDetails){
            var id = $('input[name="id"]').val();
            var bank_id = $('input[name="bank_id"]').val();
            if(isModal){
                var content = $('#connect_equity_account_form .withdrawal_item');
                id = accountId;
                bank_id = bankId;
            }else{
                var content = $('.body_content_portlet');
            }
            mApp.block(content, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Kindly wait as we retrieve account details...'
            });
            $.ajax({
                type: "POST",
                url: base_url+'/ajax/bank_accounts/initiate_account_linkage/'+id+'/'+bank_id,
                data: id,
                success: function(response){
                    if(isJson(response)){
                        var res = $.parseJSON(response);
                        if(res.status==1){
                            $('#otp-not').slideDown();
                            PhoneInputCountry.init();
                            $('#form_actions_holder').slideDown();
                        }else if(res.status == 2){
                            if(res.hasOwnProperty('notification_keys')){
                                var notificationChannels = res.notification_keys;
                                var notificationSpace = '<div class="m-radio-list">';
                                for(var i=0;i<notificationChannels.length;i++){
                                    notificationSpace+='<label class="m-radio m-radio--solid m-radio--brand"><input type="radio" name="notification_channel" value="'+(notificationChannels[i].recipientKey)+'">'+(notificationChannels[i].destination)+' ('+(notificationChannels[i].recipientValue)+')'+'<span></span></label>';
                                }
                                notificationSpace+='</div>';
                                $('#account_notificaton_space').html(notificationSpace);
                                $('#linkage-not').slideDown();
                                $('#form_actions_holder').slideDown();
                            }else{
                                $('p.text-panel').html('An error occured. Kindly refresh page and try again');$('#link-error-alert').slideDown();
                            }
                        }else{
                            $('p.text-panel').html(res.message+'. Try again later. If the error persist, kindly contact support');
                            $('#link-error-alert').slideDown();
                        }
                        $('input[name="linkage_type"]').val(res.linkage_type);
                    }else{
                        $('p.text-panel').html('An error occured. Kindly refresh page and try again');$('#link-error-alert').slideDown();
                    }
                    // Toastr.show("Success",message,'success');
                    mApp.unblock(content);
                },
                error:function(){
                    $('p.text-panel').html('An error occured. Kindly refresh page and try again');$('#link-error-alert').slideDown();
                    mApp.unblock(content);
                },
                always:function(){
                    $('p.text-panel').html('An error occured. Kindly refresh page and try again');$('#link-error-alert').slideDown();
                    mApp.unblock(content); 
                }
            });
        }
    };
    var s = function(){
        $(document).on('click',".btn#connect_bank_account_button1",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#connect_bank_account_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing request...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/ajax/bank_accounts/connect_bank_account",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    mApp.unblock(a);
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    if(response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                }
            }));
        });
    };
    var v = function(){
        $(document).on('click',".btn#verify_bank_account_ownership_button",function (t) {
            t.preventDefault();
            var e = $(this),
                a = $("#verify_bank_account_ownership_form");
            mApp.block(a, {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing request...'
            });
            RemoveDangerClass();
            (e.addClass("m-loader m-loader--right m-loader--light").attr("disabled", !0),a.ajaxSubmit({
                type: "POST",
                url: base_url+"/ajax/bank_accounts/verify_ownership",
                success: function (t, i, n, r) {
                    if(isJson(t)){
                        response = $.parseJSON(t);                        
                        if(response.status == '1'){
                            setTimeout(function () {
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                    mApp.unblock(a);
                                    a.find(".alert").html('').slideUp();
                                    Toastr.show("Success",response.message,'success');
                                    if(response.hasOwnProperty('refer')){
                                        window.location = response.refer;
                                    }
                            }, 2e3)
                        }else if(response.status == '202'){
                            Toastr.show("Session Expired",response.message,'error');
                            window.location.href = response.refer;
                        }else{
                            var message = response.message;
                            var validation_errors = '';
                            var fine_validation_errors = '';
                            if(response.hasOwnProperty('validation_errors')){
                                validation_errors = response.validation_errors;
                            }
                            if(response.hasOwnProperty('fine_validation_errors')){
                                fine_validation_errors = response.fine_validation_errors;
                            }
                            setTimeout(function () {
                                mApp.unblock(a);
                                e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", message);
                                if(validation_errors){
                                    $.each(validation_errors, function( key, value ) {
                                        var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                        $('input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                        $('select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    });
                                }
                                if(fine_validation_errors){
                                    $.each(fine_validation_errors, function( key, value ) {
                                        if(value){
                                            $.each(value,function(keyval, valueval){
                                                var fine_error_message ='<div class="form-control-feedback">'+valueval+'</div>';
                                                $('input[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                                $('select[name="'+keyval+"["+key+']"]').parent().addClass('has-danger').append(fine_error_message);
                                            });
                                        }
                                    });
                                }
                                mUtil.scrollTop();
                            }, 2e3)
                        }
                    }else{
                        setTimeout(function () {
                            mApp.unblock(a);
                            e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                                function (t, e, a) {
                                    var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                    t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                                }(a, "danger", "Could not complete processing the request at the moment.")
                        }, 2e3)
                    }
                },
                error: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                },
                always: function(){
                    setTimeout(function () {
                        mApp.unblock(a);
                        e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),e.removeClass("m-loader m-loader--right m-loader--light").attr("disabled", !1),$(".cancel_form").removeAttr("disabled"),
                            function (t, e, a) {
                                var i = $('<div class="m-alert--air mb-5 m-alert alert alert-' + e + ' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
                                t.find(".alert").remove(), i.prependTo(t), mUtil.animateClass(i[0], "fadeIn animated"), i.find("span").html(a)
                            }(a, "danger", "Could not complete processing the request at the moment. You can refresh the page or try again later.")
                    }, 2e3)
                }
            }));
        });
    };
    return {
        init: function (getDetails = false,accountId = 0,bankId = 0, isModal = false) {
            t(getDetails,accountId,bankId,isModal);
            s();
            v();
        }
    }
}();


function translate(str=''){
    var strings = str.split("<br/>");
    if(strings.length>0){
        $.each(strings,function(i,j){
            if(j.length>0){
                finalizeTranslate(j,i>0?true:false);
            }
        });
    }else{
        finalizeTranslate(str,false);
    }
}

function finalizeTranslate(str='',space=false){
    $.post({
        url: base_url+"/migrate/translate",
        data: {str:str},
        type: "POST",
        success: function(res){
            //console.log(res);
            var brk = space==true?"<br/>":"";
            $('.alert-danger.alert-dismissible').append(brk+res);
        },error:function(){
            console.log("error");
        },always:function(){
            console.log("always");
        }
    }).fail(function(){

    }).always(function(){

    }).done(function(){

    });
}

function initSearch(table){
    tableClass = table.replace(".", "");
    $(".datatable_"+tableClass+" input[type='search']").off();
    var execute = function(){
        var searchValue = $(".datatable_"+tableClass+" input[type='search']").val();
        $(table).DataTable().search(searchValue).draw();
    }
    var timer = null;
    $(".datatable_"+tableClass+" input[type='search']").keydown(function(){
        clearTimeout(timer); 
        timer = setTimeout(execute,900);
    });
}

(function($) {
    $.fn.extend({
        triggerAll: function (events, params) {
            var el = this, i, evts = events.split(' ');
            for (i = 0; i < evts.length; i += 1) {
                el.trigger(evts[i], params);
            }
            return el;
        }
    });
})(jQuery);

var wysiwyg_editor={
    init:function(){
        $(".html_editor").summernote({
            height:200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']],
            ],
        })
    }
};


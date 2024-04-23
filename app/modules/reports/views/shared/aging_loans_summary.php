<div class="invoice-content-2 bordered document-border">
    <div class="row">
        <div class="col-lg-12">
            <div class="m-dropdown m-dropdown--inline m-dropdown--large m-dropdown--arrow" m-dropdown-toggle="click" m-dropdown-persistent="1">
                <a href="#" class="m-dropdown__toggle btn btn-sm btn-primary dropdown-toggle">
                    <?php echo translate('Filter Records'); ?>
                </a>
                <div class="m-dropdown__wrapper">
                    <span class="m-dropdown__arrow m-dropdown__arrow--left"></span>
                    <div class="m-dropdown__inner">
                        <div class="m-dropdown__body">
                            <div class="m-dropdown__content">
                                <?php echo form_open(current_url(), 'method="GET" class="filter m-form m-form--label-align-right"'); ?>
                                <div class="form-group m-form__group row">
                                    <div class="col-lg-12">
                                        <label>
                                            <?php echo translate('Lending Date Range'); ?>
                                        </label>
                                        <div class="input-daterange input-group date-picker" id="m_datepicker_5" data-date-format="dd-mm-yyyy">
                                            <?php echo form_input('from', timestamp_to_datepicker($from), ' class="form-control" '); ?>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="la la-ellipsis-h"></i></span>
                                            </div>
                                            <?php echo form_input('to', timestamp_to_datepicker($to), ' class="form-control" '); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group m-form__group pt-0">
                                    <label>
                                        <?php echo translate('Select Applicant'); ?>
                                    </label>
                                    <?php echo form_dropdown('member_ids[]', array(), $this->input->get('member_ids') ? $this->input->get('member_ids') : '', 'id="member_id_options_list" class="form-control select2" multiple="multiple"'); ?>
                                </div>
                                <!-- <div class="form-group m-form__group pt-0">
                                    <label>
                                        A Number
                                    </label>
                                    <?php
                                    echo form_dropdown('member_ids[]', array(), $this->input->get('member_ids'), 'id="membership_no_options_list" class="form-control select2" multiple="multiple"');
                                    ?>
                                </div> -->
                                <?php if ($additional_member_details && count($additional_member_details) > 0) : ?>
                                    <?php foreach ($additional_member_details as $additional_member_detail) : ?>
                                        <div class="form-group m-form__group pt-0">
                                            <label>
                                                <?php echo $additional_member_detail->field_name; ?>
                                            </label>
                                            <?php
                                            echo form_dropdown('member_ids[]', array(), $this->input->get('member_id'), 'id="' . $additional_member_detail->slug . '" class="form-control select2" multiple="multiple"');
                                            ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <div class="form-group m-form__group pt-0">
                                    <label>
                                        <?php echo translate('Select Loan Type'); ?>
                                    </label>
                                    <?php echo form_dropdown('loan_type_ids[]', array() + translate($loan_type_options), $this->input->get('loan_type_ids') ? $this->input->get('loan_type_ids') : '', 'class="form-control m-select2-search" multiple="multiple"'); ?>
                                </div>
                                <div class="m-form__actions m--align-right p-0">
                                    <button name="filter" value="filter" type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-filter"></i>
                                        <?php echo translate('Filter'); ?>
                                    </button>
                                    <button class="btn btn-sm btn-danger close-filter d-none" type="reset">
                                        <i class="fa fa-close"></i>
                                        <?php echo translate('Reset'); ?>
                                    </button>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <hr />
    <div class="print_page">
        <div id="statement_paper">
            <div id="loans_summary" class="pt-3">
            </div>
            <div id="statement_footer" style="display: none;">
                <p style="text-align:center;">© <?php echo date('Y') ?> . This statement was issued with no alteration </p>
                <p style="text-align:center;">
                    <strong>Powered by:</strong>
                    <br>
                    <img width="150px" src="<?php echo site_url('uploads/logos/' . $this->application_settings->paper_header_logo); ?>" alt="<?php echo $this->application_settings->application_name; ?> Logo" ?="">
                </p>
            </div>
        </div>
    </div>
    <div class="col-xs-12 pt-3" style="display: none;" id="print_holder">
        <button class="btn btn-sm btn-info hidden-print uppercase print-btn" onclick="javascript:window.print();"><i class='fa fa-print'></i>
            <?php echo translate('Print'); ?>
        </button>
        <!-- &nbsp;&nbsp;&nbsp;
        <?php
        $query = $_SERVER['QUERY_STRING'] ? '?generate_excel=1&' . $_SERVER['QUERY_STRING'] : '?generate_excel=1';
        ?>
        <a class="btn btn-sm btn-primary uppercase" href="<?php echo current_url() . '/' . $query; ?>" target="_blank"><i class='fa fa-file'></i>&nbsp;
            <?php echo translate('Generate Excel'); ?>
        </a>

        
        &nbsp;&nbsp;&nbsp;
        <?php $search_string = substr(basename($_SERVER['REQUEST_URI']),strpos(basename($_SERVER['REQUEST_URI']), "?"));?>
        <a class="btn btn-sm btn-primary uppercase" href="<?php echo site_url('ajax/reports/get_aging_loans_summary_pdf').'/'.TRUE.$search_string;?>" target="_blank" id="generate_pdf"><i class='fa fa-file'></i>
            <?php echo translate('Generate PDF'); ?>
        </a> -->
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".m-select2-search").select2({
            placeholder: {
                id: '-1',
                text: "--Select option--",
            },
            width: "100%"
        });
        $('.date-picker').datepicker({
            dateFormat: 'dd-mm-yy',
            autoclose: true
        });
        var additional_member_details = JSON.parse('<?php echo json_encode($additional_member_details) ?>');
        // For Member name search
        $("#member_id_options_list").select2({
            width: "100%",
            placeholder: {
                id: '-1',
                text: "--Select option--",
            },
            ajax: {
                url: '<?php echo site_url("bank/members/ajax_active_group_member_options_using_name"); ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, page) {
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 2,
            templateResult: formatSettings,
            templateSelection: formatSettingsSelection,
            allowClear: true,
            placeholder: "Search Loan Applicant Member"
        });

        // For Membership number search
        $("#membership_no_options_list").select2({
            width: "100%",
            placeholder: {
                id: '-1',
                text: "--Select option--",
            },
            ajax: {
                url: '<?php echo site_url("group/members/ajax_active_group_member_options_using_membership_no"); ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, page) {
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data
                    console.log("data ", data);
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 4,
            templateResult: formatSettings,
            templateSelection: formatSettingsSelection,
            allowClear: true,
            placeholder: "Search Group Member"
        });

        // For additional Applicant Details
 search.
        if (additional_member_details.length > 0) {
            // loop through the additional Applicant Details
.
            additional_member_details.forEach((element, index) => {
                console.log(`slug ${element.slug}`);
                $(`#${element.slug}`).select2({
                    width: "100%",
                    placeholder: {
                        id: '-1',
                        text: "--Select option--",
                    },
                    ajax: {
                        url: '<?php echo site_url("group/members/ajax_active_group_member_options_using_additional_member_detail"); ?>',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                field_slug: element.slug,
                                page: params.page
                            };
                        },
                        processResults: function(data, page) {
                            // parse the results into the format expected by Select2.
                            // since we are using custom formatting functions we do not need to
                            // alter the remote JSON data
                            return {
                                results: data.items
                            };
                        },
                        cache: true
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    }, // let our custom formatter work
                    minimumInputLength: 2,
                    templateResult: formatSettingsForAdditionalDetails,
                    templateSelection: formatSettingsSelectionForAdditionalDetails,
                    allowClear: true,
                    placeholder: "Search Group Member"
                });
            });
        }
    });

    $(window).on('load', function() {
        load_account_balances();
    });



    var first_uri_segment = "<?php echo $this->uri->segment(4, 0); ?>";
    var second_uri_segment = "<?php echo $this->uri->segment(5, 0); ?>";
    var get_string = "<?php echo $_SERVER['QUERY_STRING']; ?>";


    function load_account_balances() {
        mApp.block('#loans_summary', {
            overlayColor: 'white',
            animate: true
        });
        $.ajax({
            type: "GET",
            url: '<?php echo base_url("ajax/reports/get_aging_loans_summary/'+first_uri_segment+'/'+second_uri_segment+'?'+get_string+'"); ?>',
            dataType: "html",
            success: function(response) {
                $('#loans_summary').html(response);
                $('.tooltips').tooltip();
                $('#statement_footer').slideDown();
                $("#print_holder").slideDown();
                mApp.unblock('#loans_summary');
            }
        });
    }

    function formatSettings(repo) {
        if (repo.loading) return repo.text;
        var show_membership_number = 1;
        var markup = repo.membership_number ? ("<div class=''>" + repo.first_name + ' ' + repo.last_name + ' ' + repo.membership_number + "</div>") : (
            "<div class=''>" + repo.first_name + ' ' + repo.last_name + "</div>");
        return markup;
    }

    function formatSettingsForAdditionalDetails(repo) {
        if (repo.loading) return repo.text;
        var markup = "<div class=''>" + repo.first_name + ' ' + repo.last_name + ' - ' + repo.value + "</div>";
        return markup;
    }

    function formatSettingsSelection(repo) {
        if (repo.loading) return repo.text;
        var markup = repo.membership_number ? ("<div class=''>" + repo.first_name + ' ' + repo.last_name + ' ' + repo.membership_number + "</div>") : (
            "<div class=''>" + repo.first_name + ' ' + repo.last_name + "</div>");
        return markup;
    }

    function formatSettingsSelectionForAdditionalDetails(repo) {
        if (repo.loading) return repo.text;
        var markup = '<div class="">' + repo.first_name + ' ' + repo.last_name + ' - ' + repo.value +
            "</div>";
        return markup;
    }
</script>
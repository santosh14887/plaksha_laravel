<!DOCTYPE html>
<!--
Template Name: NobleUI - HTML Bootstrap 5 Admin Dashboard Template
Author: NobleUI
Website: https://www.nobleui.com
Portfolio: https://themeforest.net/user/nobleui/portfolio
Contact: nobleui123@gmail.com
Purchase: https://1.envato.market/nobleui_admin
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Responsive HTML Admin Dashboard Template based on Bootstrap 5">
    <meta name="author" content="NobleUI">
    <meta name="keywords"
        content="nobleui, bootstrap, bootstrap 5, bootstrap5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <!-- <link href="{{ asset('noble_ui/css2?family=Roboto:wght@300;400;500;700;900&display=swap') }}" rel="stylesheet"> -->
    <!-- End fonts -->
    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('noble_ui/vendors/core/core.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('css/vertical-layout-light/style.css') }}"> -->
    <!-- endinject -->
    <!-- Plugin css for this page -->

    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- <link rel="stylesheet" href="{{ asset('noble_ui/fonts/feather-font/css/iconfont.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('noble_ui/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <!-- endinject -->
    <!-- Layout styles -->
    <!-- <link rel="stylesheet" href="{{ asset('vendors/select2/select2.min.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('noble_ui/css/demo1/style.min.css') }}">
    <!-- End layout styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.css') }}">

    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/simple-line-icons/css/simple-line-icons.css') }}">

    <!--- datatable css --->
    <link rel="stylesheet" href="{{ asset('css/datatable/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable/buttons.dataTables.min.css') }}">
    <!--- end datatable css --->

    <link rel="stylesheet" href="{{ asset('vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/for_sticky.css') }}">
    <script src="{{ asset('noble_ui/vendors/core/core.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('noble_ui/vendors/flatpickr/flatpickr.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
</head>

<body class="sidebar-dark">
    <div class="main-wrapper">
        <div class="modal fade booking_calender" id="booking_calender" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLongTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Book Now for a Live Demo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="position:relative; height: 600px;">
                        <iframe
                            src='https://outlook.office365.com/owa/calendar/FleetElevate@centuryinfotek.com/bookings/'
                            width='100%' height='100%' scrolling='yes' style='border:0'></iframe>
                    </div>
                    <!-- <div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary">Save changes</button>
						</div> -->
                </div>
            </div>
        </div>

        <!-- partial:../../partials/_sidebar.html -->
        @include('partials.sidebar')

        <!-- partial -->

        <div class="page-wrapper">

            <!-- partial:../../partials/_navbar.html -->
            @include('partials.navbar')

            <!-- partial -->

            <div class="page-content">
                @yield('content')

            </div>

            <!-- partial:../../partials/_footer.html -->
            @include('partials.footer')

            <!-- partial -->

        </div>
    </div>
    @php
    $base_url = URL::to('/');
    @endphp
    @if($base_url == 'https://demo.fleetelevate.com')
    @if ( ! Session::has('sidebar_help'))
    <script>
    const myTimeout = setTimeout(AutoSidebarClick, 2000);

    function AutoSidebarClick() {
        $(".sticky-target").addClass('hover_clcik_cls');
        var options = {};
        options.type = 'GET';
        options.url = "{{url('/update_sidebar_help')}}";
        options.data = {
            "_token": "{{ csrf_token() }}"
        };
        options.success = function(res) {}
        $.ajax(options);
    }
    </script>

    @endif
    <div class="sticky-widget">
        <a href="#sticky-target" id="sticky_widget_a" class="sticky-trigger">
            <span class="svg-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path
                        d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-7v2h2v-2h-2zm2-1.645A3.502 3.502 0 0 0 12 6.5a3.501 3.501 0 0 0-3.433 2.813l1.962.393A1.5 1.5 0 1 1 12 11.5a1 1 0 0 0-1 1V14h2v-.645z">
                    </path>
                </svg>
            </span>
            Need Help?</a>

        <div class="sticky-target" id="sticky-target">

            <img src="{{ asset('images/stuck.jpg') }}" class="img-media">

            <h5>Are you struggling to explore all the features of our product?</h5>
            <p>Let us help! Schedule a free call with our expert team for a detailed demo tailored to your business
                needs.</p>
            <a data-wow-delay="0.6s" data-toggle="modal" data-target="#booking_calender"
                class="btn btn-primary me-2 sticky_btn_cs">Get Started</a>
        </div>
    </div>
    @endif
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('noble_ui/vendors/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('noble_ui/js/flatpickr.js') }}"></script>
    <script src="{{ asset('noble_ui/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <!-- End plugin js for this page -->

    <!-- inject:js -->
    <script src="{{ asset('noble_ui/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('noble_ui/js/template.js') }}"></script>
    <!-- endinject -->

    <!-- Custom js for this page -->
    <script src="{{ asset('noble_ui/js/dashboard-light.js') }}"></script>
    <!-- <script src="{{ asset('noble_ui/vendors/select2/select2.min.js') }}"></script>
  <script src="{{ asset('noble_ui/js/select2.js') }}"></script> -->

    <script src="{{ asset('vendors/chart.js/Chart.min.js') }}"></script>
    <!-- End custom js for this page-->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.bundle.min.js"></script> -->
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-select.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>-->
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker.min.js') }}"></script>
    <!--- datatable js --->
    <script src="{{ asset('js/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/datatable/jszip.min.js') }}"></script>
    <script src="{{ asset('js/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/datatable/buttons.html5.min.js') }}"></script>
    <script>
    var colors = {
        //  primary: "#D84E4C",
        primary: "#6571ff",
        secondary: "#7987a1",
        success: "#05a34a",
        info: "#66d1d1",
        warning: "#fbbc06",
        danger: "#ff3366",
        light: "#e9ecef",
        dark: "#060c17",
        muted: "#7987a1",
        gridBorder: "rgba(77, 138, 240, .15)",
        bodyColor: "#000",
        cardBg: "#fff"
    }

    var fontFamily = "'Roboto', Helvetica, sans-serif"


    function add_transaction(user_id, amount, trans_type, message, table_name) {
        var options = {};
        options.type = 'POST';
        options.url = "{{url('reports/add_user_transaction')}}";
        options.data = {
            "_token": "{{ csrf_token() }}",
            'user_id': user_id,
            'amount': amount,
            'trans_type': trans_type,
            'message': message,
            'table_name': table_name,
        };
        options.success = function(res) {
            var parsedJson = $.parseJSON(res);
            console.log(parsedJson.msg);
            let current_amount = parsedJson.current_amount;
            if (parsedJson.msg == 'save') {
                transaction_form_empty();
                $("#trans_action").hide();
                $(".trans_action_msg").html('<span style="color:green">You have added successfully</span>');
                setTimeout(function() {
                    location.reload();
                }, 2000);
                //return false;
            } else {
                //add_transaction(user_id,amount,type,message);
            }
        };
        $.ajax(options);
    }

    function transaction_form_empty() {
        $("#amount").val('');
        $("#type").val('');
        $("#message").val('');
    }

    function checked_all_clicked(class_name = '', checked_type = '') {
        $('.' + class_name).each(function() {
            $(this).prop('checked', checked_type);
        });
    }

    function check_all_permission_checkbox_selected() {
        $('.view').each(function() {
            if ($(this).is(':checked')) {
                $("#view_all").prop('checked', true);
            } else {
                $("#view_all").prop('checked', false);
                return false;
            }
        });
        $('.action').each(function() {
            if ($(this).is(':checked')) {
                $("#action_all").prop('checked', true);
            } else {
                $("#action_all").prop('checked', false);
                return false;
            }
        });
    }
    $(document).ready(function() {
        employee_rate_show();
        show_cancel_form_msg_box();
        check_all_permission_checkbox_selected();
        $("#all_selected_box_span").hide();
        let cancel_count_start = $("#cancel_count_start").val();
        if (cancel_count_start > 0) {
            $("#all_selected_box_span").show();
        } else {}
        $(".action").click(function() {
            var permission_name = $(this).data('id');
            if ($(this).is(':checked')) {
                $("#view_" + permission_name).prop('checked', true);
            } else {
                $("#view_" + permission_name).prop('checked', false);
            }
        });
        $(".view_all_cls").click(function() {
            var class_name = $(this).data('id');
            if ($(this).is(':checked')) {
                checked_all_clicked(class_name, true);
                if (class_name == 'action') {
                    $("#view_all").prop('checked', true);
                    checked_all_clicked('view', true);
                }
            } else {
                checked_all_clicked(class_name, false);
                if (class_name == 'action') {
                    $("#view_all").prop('checked', false);
                    checked_all_clicked('view', false);
                }
            }
        });
		$(".select_all_invoice").click(function() {
			var class_name = "invoice_checkbox";
            if ($(this).is(':checked')) {
                checked_all_invoice_status(class_name, true);
            } else {
                checked_all_invoice_status(class_name, false);
            }
        });
		$(".select_all_emp_bill").click(function() {
			var class_name = "employee_invoice_checkbox";
            if ($(this).is(':checked')) {
                checked_all_invoice_status(class_name, true);
            } else {
                checked_all_invoice_status(class_name, false);
            }
        });
		function checked_all_invoice_status(class_name = '', checked_type = '') {
        $('.' + class_name).each(function() {
            $(this).prop('checked', checked_type);
        });
		}
        $(".download_img").click(function() {
            let path = $(this).data('id');
            saveAs(path);
        });
        $(".quickbook_download_pdf").click(function() {
            let path = $(this).data('id');
            let quickbookid = $(this).data('quickbookid');
            let rowid = $(this).data('rowid');
            $("#all_invoice_quickbook_" + rowid).html(
                '<span style="color:green;">Please wait...</span>');
            $(this).hide();
            var options = {};
            options.type = 'POST';
            options.url = "{{url('reports/quickbook_download_pdf')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'rowid': rowid,
                'quickbookid': quickbookid,
            };
            options.success = function(data) {
                if (data.status == 'error') {
                    $("#all_invoice_quickbook_" + rowid).html(
                        '<span style="color:red;">' + data.message + '</span>');
                } else {
                    saveAs(path + '/' + data.message);
                    $("#all_invoice_quickbook_" + rowid).html(
                        '<span style="color:green;">Downloaded successfully...</span>');
                    setTimeout(function() {
                        $("#all_invoice_quickbook_" + rowid).html(
                            '');
                    }, 2000);
                }
            };
            $.ajax(options);
        });
		$(".employee_quickbook_bill_download_pdf").click(function() {
            let path = $(this).data('id');
            let quickbookid = $(this).data('quickbookid');
            let rowid = $(this).data('rowid');
            $("#employee_all_payroll_quickbook_" + rowid).html(
                '<span style="color:green;">Please wait...</span>');
            $(this).hide();
            var options = {};
            options.type = 'POST';
            options.url = "{{url('reports/quickbook_employee_bill_download_pdf')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'rowid': rowid,
                'quickbookid': quickbookid,
            };
            options.success = function(data) {
                if (data.status == 'error') {
                    $("#employee_all_payroll_quickbook_" + rowid).html(
                        '<span style="color:red;">' + data.message + '</span>');
                } else {
                    saveAs(path + '/' + data.message);
                    $("#employee_all_payroll_quickbook_" + rowid).html(
                        '<span style="color:green;">Downloaded successfully...</span>');
                    setTimeout(function() {
                        $("#employee_all_payroll_quickbook_" + rowid).html(
                            '');
                    }, 2000);
                }
            };
            $.ajax(options);
        });
        $(".trans_action").click(function() {
            let table_name = $(this).data('table');
            let amount = $("#amount").val();
            let type = $("#type").val();
            let message = $("#message").val();
            let user_id = $("#user_id").val();
            if (amount == '' || type == '' || message == '') {
                $(".trans_action_msg").html(
                    '<span style="color:red">Please fill the form to continue...</span>');
                return;
            } else if (amount < 1) {
                $(".trans_action_msg").html(
                    '<span style="color:red">Minimum amount should be greter the 0</span>');
                return;
            } else {
                if (type == 'debit') {
                    $(".trans_action_msg").html('<span style="color:green">Please wait...</span>');
                    var options = {};
                    options.type = 'POST';
                    options.url = "{{url('reports/get_user_current_amount')}}";
                    options.data = {
                        "_token": "{{ csrf_token() }}",
                        'user_id': user_id,
                        'amount': amount,
                        'table_name': table_name,
                    };
                    options.success = function(res) {
                        var parsedJson = $.parseJSON(res);
                        console.log(parsedJson.msg);
                        let current_amount = parsedJson.current_amount;
                        if (parsedJson.msg == 'error') {
                            if (current_amount < 1) {
                                $(".trans_action_msg").html(
                                    '<span style="color:red">Balance is not sufficient for deduction</span>'
                                );
                            } else {
                                $(".trans_action_msg").html(
                                    '<span style="color:red">Maximum amount can be debit ' +
                                    current_amount + '</span>');
                            }

                            return false;
                        } else {
                            add_transaction(user_id, amount, type, message, table_name);
                        }
                    };
                    $.ajax(options);
                } else {
                    add_transaction(user_id, amount, type, message, table_name);
                }
            }

        });
        /* $('.ticket_number').each(function() {
        	$(this).hide();
        }); */


        $('.datatable').DataTable();
        $('#customer_revenue').DataTable({
            dom: 'Bfrtip',
            buttons: [
                // 'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
        $('#vehicle_assignment_tbl').DataTable({
            //dom: 'Bfrtip',
            // buttons: [
            //    // 'copyHtml5',
            //     'excelHtml5',
            //     'csvHtml5',
            //     'pdfHtml5'
            // ]
        });
    });

    function employee_rate_show() {
        let type_val = $(".job_type option:selected").val();
        if (type_val == 'load') {
            $(".employee_rate_div").show();
        } else {
            $("#employee_rate").val('');
            $(".employee_rate_div").hide();
        }
    }

    $('.job_type').change(function() {
        employee_rate_show();

    });

    $('#start_time').datetimepicker();
    $('#end_time').datetimepicker();
    //$('#on_date').datepicker();
    $('#daterangepicker').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });
    $('#last_air_filter_date').datetimepicker();
    $('input[name="graph_date"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        filterSubmit();
    });
    $('input[name="graph_date"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    $('input[name="issue_date"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        filterSubmit();
    });
    $('input[name="issue_date"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    $('input[name="dispatch_date"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        filterSubmit();
    });
    $('input[name="dispatch_date"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    $(".duration").change(function() {
        filterOption();
    });
    $("#issue_filter_status").change(function() {
        filterSubmit();
    });
    $("#remark_save").click(function() {
        var remark = $("#popup_remark").val();
        var remark_ticket_id = $("#remark_ticket_id").val();
        if (remark != '') {
            $(".remark_error").html('<span style="color:green;">Please wait..</span>');
            $(this).hide();
            var options = {};
            options.type = 'POST';
            options.url = "{{url('add_ticket_remark')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'remark': remark,
                'row_id': remark_ticket_id,
            };
            options.success = function(data) {
                $(".remark_error").html('<span style="color:green;">Updated successfully..</span>');
                setTimeout(function() {
                    location.reload();
                }, 2000);

            };
            $.ajax(options);
        } else {
            $(".remark_error").html('<span style="color:red;">Please add remark to continue...</span>');
        }
    });
    $(".remark_model_btn").click(function() {
        $(".remark_error").html('');
        var ticket_id = $(this).data('id');
        var remark_html = $(".remark_div_" + ticket_id).html();
        $("#popup_remark").val(remark_html);
        $("#remark_ticket_id").val(ticket_id);
    });

    function changePwdEmpty() {
        $("#change_password_error").html('');
        $("#change_password_error").removeClass('alert-danger');
        $("#change_password_error").removeClass('alert-info');
        $("#confirm_password").val('');
        $("#password").val('');
    }
    $("#change_password_a").click(function() {
        changePwdEmpty();
    });

    function ticket_error_empty() {
        $(".all_generate_invoice_err").html('');
        $('.ticket_number_span').each(function() {
            $(this).html('');
        });
    }

    function remove_border_class() {
        $('.ticket_number').each(function() {
            $(this).removeClass('border_color_red');
        });
    }
    $('.update_dispatch_ticket_status').change(function() {
        if (confirm('Are you sure to update status ?')) {
            let id = $(this).attr('id');
            let user_id = $(this).data('userid');
            let dispatch_id = $(this).data('dispatchid');
            var options = {};
            options.type = 'POST';
            options.url = "{{url('dispatch_tickets/update_status')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'id': id,
                'user_id': user_id,
                'dispatch_id': dispatch_id,
            };
            options.success = function(res) {
                console.log(res);
                if (res.status == 'err') {
                    $(".ticket_index_err_" + id).html(res.error);
                } else {
                    $("#td_" + id).html('Completed');
                }


            };
            $.ajax(options);
        } else {
            return false;
        }
    });
    $('.update_invoice_status').change(function() {
        if (confirm('Are you sure to update status ?')) {
            let id = $(this).attr('id');
            var options = {};
            options.type = 'POST';
            options.url = "{{url('reports/update_status')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'id': id,
            };
            options.success = function(res) {
                $("#td_" + id).html('Completed');
            };
            $.ajax(options);
        } else {
            return false;
        }
    });
	 $('.update_employee_payroll_bill_status').change(function() {
        if (confirm('Are you sure to update status ?')) {
            let id = $(this).attr('id');
            var options = {};
            options.type = 'POST';
            options.url = "{{url('reports/update_employee_payroll_bill_status')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'id': id,
            };
            options.success = function(res) {
				if (res.status == 'error') {
                    $("#td_" + id).html('<span style="colo:red">'+ res.message+'</span>');
                } else {
                    $("#td_" + id).html('Completed');
                }
            };
            $.ajax(options);
        } else {
            return false;
        }
    });
    $('.select_all_assign_dispatch').change(function() {
        if (this.checked) {
            $('.assign_dispatches_checkbox').each(function() {
                $(this).attr('checked', true);
            });
        } else {
            $('.assign_dispatches_checkbox').each(function() {
                $(this).attr('checked', false);
            });
        }
    });
    $(".all_cancel_dispatches").click(function() {
        let checked_count = selected_vals = 0;
        $(".all_cancel_dispatches_err").html('');
        let dispatch_id = $("#assign_dispatches_dispatch_id").val();
        let cancel_message = $("#cancel_message").val();
        if (cancel_message == '') {
            $(".all_cancel_dispatches_err").html('Please add message to continue...');
            return false;
        } else {}
        let assign_dispatch_ids = [];
        $('input[name="assign_dispatches_checkbox"]:checked').each(function() {
            let id = $(this).attr('id');
            assign_dispatch_ids[id] = id;
        });
        if (assign_dispatch_ids.length === 0) {
            $(".all_cancel_dispatches_err").html('Please select user to continue...');
            return false;
        } else {
            $(".all_cancel_dispatches").hide();
            $(".all_cancel_dispatches_success").html('Please wait...');
            var options = {};
            options.type = 'POST';
            options.url = "{{url('cancel_dispatch_from_users')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'assign_dispatch_ids': assign_dispatch_ids,
                'dispatch_id': dispatch_id,
                'cancel_message': cancel_message,
            };
            options.success = function(res) {
                // console.log(res);
                $(".all_cancel_dispatches_success").html('Updated successfully...');
                setTimeout(function() {
                    location.reload();
                }, 2000);

            };
            $.ajax(options);
        }
    });
    $('.invoice_checkbox').change(function() {
        $(".all_generate_invoice_err").html('');
        let id = $(this).attr('id');
        //$("#ticket_number_"+id).val('');
        $("#ticket_number_" + id).removeClass('border_color_red');
        $("#ticket_number_span_" + id).html('');

        /*if(this.checked) {
			$("#ticket_number_"+id).show();
        } else {
			$("#ticket_number_"+id).hide();
		} */
    });

    function show_cancel_form_msg_box() {
        $("#cancel_dispatch_msg_div").hide();
        $('.assign_dispatch_status_select').each(function() {
            var selected_val = $(this, ':selected').val();
            if (selected_val == 'cancelled') {
                $("#cancel_dispatch_msg_div").show();
                return;
            }
        });
    }
    $('.assign_dispatch_status_select').change(function() {
        show_cancel_form_msg_box();
    });
	$(".employee_all_generate_invoice").click(function() {
        let checked_count = selected_vals = 0;
        ticket_error_empty();
        remove_border_class();
        let ticket_arr = [];
        let dispatch_ids = [];
        let ticket_ids = [];
        let ticket_numbers = [];
        $('input[name="employee_invoice_checkbox"]:checked').each(function() {
            let id = $(this).attr('id');
            let ticketId = $(this).data('ticketid');
			//alert(ticketId);
            let ticket_num = $("#employee_ticket_number_" + id).val();
            dispatch_ids[id] = id;
            ticket_ids[ticketId] = ticketId;
            ticket_numbers[ticketId] = ticket_num;
                let dispatch_obj = {
                    "dispatch_id": id,
                    "ticket_number": ticket_num,
                    "ticket_id": ticketId
                }
                ticket_arr.push(dispatch_obj);
        });
        if (ticket_arr.length === 0) {
            $(".employee_all_generate_invoice_err").html(
                '<span style="color:red;">Please add dispatch to continue...</span>');
            return false;
        } else {
            $(".employee_all_generate_invoice_err").html('<span style="color:green;">Please wait...</span>');
            var options = {};
            options.type = 'POST';
            options.url = "{{url('reports/generate_employee_invoice')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'ticket_arr': ticket_arr,
                'dispatch_ids': dispatch_ids,
                'ticket_ids': ticket_ids,
                'ticket_numbers': ticket_numbers,
            };
            options.success = function(data) {
                if (data.status == 'error') {
                    $(".employee_all_generate_invoice_err").html(
                        '<span style="color:red;">' + data.message + '</span>');
                    return false;
                } else if (data.type == 'quickbook') {
                    $(".employee_all_generate_invoice_err").html(
                        '<span style="color:green;">Quickbook invoice Generated successfully...</span>');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    saveAs(data.message);
                    $(".employee_all_generate_invoice_err").html(
                        '<span style="color:green;">Generated successfully...</span>');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            };
            $.ajax(options);
        }
    });
    $(".all_generate_invoice").click(function() {
        let checked_count = selected_vals = 0;
        ticket_error_empty();
        remove_border_class();
        let ticket_arr = [];
        let dispatch_ids = [];
        $('input[name="invoice_checkbox"]:checked').each(function() {
            let id = $(this).attr('id');
            let ticket_num = $("#ticket_number_" + id).val();
            if (ticket_num == '') {
                $("#ticket_number_span_" + id).html('Please add ticket number');
                $("#ticket_number_" + id).addClass('border_color_red');
                return false;
            } else {
                dispatch_ids.push(id);
                let dispatch_obj = {
                    "dispatch_id": id,
                    "ticket_number": ticket_num
                }
                ticket_arr.push(dispatch_obj);
            }
        });
        if (ticket_arr.length === 0) {
            $(".all_generate_invoice_err").html(
                '<span style="color:red;">Please add dispatch to continue...</span>');
            return false;
        } else {
            $(".all_generate_invoice_err").html('<span style="color:green;">Please wait...</span>');
            var options = {};
            options.type = 'POST';
            options.url = "{{url('reports/generate_multi_row_pdf')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'ticket_arr': ticket_arr,
                'dispatch_ids': dispatch_ids,
            };
            options.success = function(data) {
                if (data.status == 'error') {
                    $(".all_generate_invoice_err").html(
                        '<span style="color:red;">' + data.message + '</span>');
                    return false;
                } else if (data.type == 'quickbook') {
                    $(".all_generate_invoice_err").html(
                        '<span style="color:green;">Quickbook invoice Generated successfully...</span>');
                    setTimeout(function() {
                        location.reload();
                    }, 2000); 
                } else {
                    saveAs(data.message);
                    $(".all_generate_invoice_err").html(
                        '<span style="color:green;">Generated successfully...</span>');
                    setTimeout(function() {
                        location.reload();
                    }, 2000); 
                }
            };
            $.ajax(options);
        }
    });
    $(".invoice_sent").click(function() {
        if (confirm('Are you sure to update Invoice sent status ?')) {
            let id = $(this).data('id');
            var options = {};
            options.type = 'POST';
            options.url = "{{url('reports/invoice_sent')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'id': id
            };
            options.success = function(data) {
                $("#invoice_sent_" + id).html('<span style="color:green">Updated successfully</span>');
            };
            $.ajax(options);
        } else {
            return false;
        }

    });
    $("#change_pwd_save").click(function() {
        $("#change_password_error").html('');
        $("#change_password_error").removeClass('alert-danger');
        $("#change_password_error").removeClass('alert-info');
        let password = $("#password").val();
        let confirm_password = $("#confirm_password").val();
        if (password == '' || confirm_password == '') {
            $("#change_password_error").addClass('alert-danger');
            $("#change_password_error").html('Please fill the form to continue...');
            return false;
        }
        if (password != confirm_password) {
            $("#change_password_error").addClass('alert-danger');
            $("#change_password_error").html('Confirm password is not same...');
            return false;
        }
        $("#change_password_error").addClass('alert-info');
        $("#change_password_error").html('Please wait...');
        var options = {};
        options.type = 'POST';
        options.url = "{{url('admin_change_password')}}";
        options.data = {
            "_token": "{{ csrf_token() }}",
            'password': password
        };
        options.success = function(data) {
            $("#change_password_error").html('Updated successfully');
            setTimeout(function() {
                changePwdEmpty();
                $(".change_pwd_close").click();
            }, 2000);

        };
        $.ajax(options);
    });

    function filterSubmit() {
        $(".filter_btn").click();
    }
    $(".assignment_date").change(function() {
        $(".assignment_vehicle_form").submit();
    });
    $(".vehicle_assignment_filter_status").change(function() {
        $(".assignment_vehicle_form").submit();
    });

    function filterOption() {
        $(".date_picker_div").hide();
        let val = $(".duration option:selected").val();
        if (val == 'custom') {
            $(".date_picker_div").show();
        } else {
            $('input[name="graph_date"]').val('');
            filterSubmit();
        }
    }
    </script>
    <script>
    // completed order chart on dashboard
    @php
    if (isset($complete_order_json) && !empty($complete_order_json)) {
        @endphp
        var complete_order_option = {
            chart: {
                type: "line",
                height: 60,
                sparkline: {
                    enabled: !0
                }
            },
            series: [{
                name: '',
                data: <?php echo $complete_order_json['data']; ?>
            }],
            xaxis: {
                // type: 'datetime',
                categories: <?php echo $complete_order_json['level']; ?>,
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 0
            },
            colors: [colors.primary],
        };
        new ApexCharts(document.querySelector("#completed_order_chart"), complete_order_option).render();
        @php
    }
    @endphp
    // end completed order chart on dashboard
    // pending order chart on dashboard
    @php
    if (isset($pending_order_json) && !empty($pending_order_json)) {
        @endphp
        var pending_order_option = {
            chart: {
                type: "line",
                height: 60,
                sparkline: {
                    enabled: !0
                }
            },
            series: [{
                name: '',
                data: <?php echo $pending_order_json['data']; ?>
            }],
            xaxis: {
                // type: 'datetime',
                categories: <?php echo $pending_order_json['level']; ?>,
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 0
            },
            colors: [colors.primary],
        };
        new ApexCharts(document.querySelector("#pending_order_chart"), pending_order_option).render();
        @php
    }
    @endphp
    // end pending order chart on dashboard
    // employee chart on dashboard
    @php
    if (isset($employee_graph_data_json) && !empty($employee_graph_data_json)) {
        @endphp
        var employee_option = {
            chart: {
                type: "line",
                height: 60,
                sparkline: {
                    enabled: !0
                }
            },
            series: [{
                name: '',
                data: <?php echo $employee_graph_data_json['data']; ?>
            }],
            xaxis: {
                // type: 'datetime',
                categories: <?php echo $employee_graph_data_json['level']; ?>,
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 0
            },
            colors: [colors.primary],
        };
        new ApexCharts(document.querySelector("#employee_chart"), employee_option).render();
        @php
    }
    @endphp
    // end employee chart on dashboard
    // broker chart on dashboard
    @php
    if (isset($broker_graph_data_json) && !empty($broker_graph_data_json)) {
        @endphp
        var broker_option = {
            chart: {
                type: "line",
                height: 60,
                sparkline: {
                    enabled: !0
                }
            },
            series: [{
                name: '',
                data: <?php echo $broker_graph_data_json['data']; ?>
            }],
            xaxis: {
                //  type: 'datetime',
                categories: <?php echo $broker_graph_data_json['level']; ?>,
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 0
            },
            colors: [colors.primary],
        };
        new ApexCharts(document.querySelector("#broker_chart"), broker_option).render();
        @php
    }
    @endphp
    // end broker chart on dashboard
    // customer chart on dashboard
    @php
    if (isset($customer_graph_data_json) && !empty($customer_graph_data_json)) {
        @endphp
        var customer_option = {
            chart: {
                type: "line",
                height: 60,
                sparkline: {
                    enabled: !0
                }
            },
            series: [{
                name: '',
                data: <?php echo $customer_graph_data_json['data']; ?>
            }],
            xaxis: {
                //    type: 'datetime',
                categories: <?php echo $customer_graph_data_json['level']; ?>,
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 0
            },
            colors: [colors.primary],
        };
        new ApexCharts(document.querySelector("#customer_chart"), customer_option).render();
        @php
    }
    @endphp
    // end customer chart on dashboard
    // Income chart on dashboard
    @php
    if (isset($income_arr_data_json) && !empty($income_arr_data_json)) {
        @endphp
        var income_option = {
            chart: {
                type: "line",
                height: 60,
                sparkline: {
                    enabled: !0
                }
            },
            series: [{
                name: '',
                data: <?php echo $income_arr_data_json; ?>
            }],
            xaxis: {
                //    type: 'datetime',
                categories: <?php echo $income_arr_level_json; ?>,
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 0
            },
            colors: [colors.primary],
        };
        new ApexCharts(document.querySelector("#income_chart"), income_option).render();
        @php
    }
    @endphp
    // end Income chart on dashboard
    // Income chart on dashboard
    @php
    if (isset($expense_arr_data_json) && !empty($expense_arr_data_json)) {
        @endphp
        var expense_option = {
            chart: {
                type: "line",
                height: 60,
                sparkline: {
                    enabled: !0
                }
            },
            series: [{
                name: '',
                data: <?php echo $expense_arr_data_json; ?>
            }],
            xaxis: {
                //    type: 'datetime',
                categories: <?php echo $expense_arr_level_json; ?>,
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 0
            },
            colors: [colors.primary],
        };
        new ApexCharts(document.querySelector("#expense_chart"), expense_option).render();
        @php
    }
    @endphp
    // end Expense chart on dashboard
    // Income chart on dashboard
    @php
    if (isset($profit_arr_data_json) && !empty($profit_arr_data_json)) {
        @endphp
        var profit_option = {
            chart: {
                type: "line",
                height: 60,
                sparkline: {
                    enabled: !0
                }
            },
            series: [{
                name: '',
                data: <?php echo $profit_arr_data_json; ?>
            }],
            xaxis: {
                //    type: 'datetime',
                categories: <?php echo $profit_arr_level_json; ?>,
            },
            stroke: {
                width: 2,
                curve: "smooth"
            },
            markers: {
                size: 0
            },
            colors: [colors.primary],
        };
        new ApexCharts(document.querySelector("#profit_chart"), profit_option).render();
        @php
    }
    @endphp
    // end profit chart on dashboard
    @php
    if (isset($income_arr_level_json) && $income_arr_level_json != '') {
        @endphp
        if ($('#dashboard_revenue_graph').length) {
            new Chart($('#dashboard_revenue_graph'), {
                type: 'line',
                data: {
                    labels: <?php echo $income_arr_level_json; ?>,
                    datasets: [{
                        data: <?php echo $income_arr_data_json; ?>,
                        label: "Income",
                        borderColor: colors.info,
                        backgroundColor: "transparent",
                        fill: true,
                        pointBackgroundColor: colors.cardBg,
                        pointBorderWidth: 2,
                        pointHoverBorderWidth: 3,
                        tension: .3
                    }, {
                        data: <?php echo $expense_arr_data_json; ?>,
                        label: "Expense",
                        borderColor: colors.danger,
                        backgroundColor: "transparent",
                        fill: true,
                        pointBackgroundColor: colors.cardBg,
                        pointBorderWidth: 2,
                        pointHoverBorderWidth: 3,
                        tension: .3
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: colors.bodyColor,
                                font: {
                                    size: '13px',
                                    family: fontFamily
                                }
                            }
                        },
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: true,
                                color: colors.gridBorder,
                                borderColor: colors.gridBorder,
                            },
                            ticks: {
                                color: colors.bodyColor,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: true,
                                color: colors.gridBorder,
                                borderColor: colors.gridBorder,
                            },
                            ticks: {
                                color: colors.bodyColor,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        }

        @php
    }
    @endphp
    </script>
</body>

</html>
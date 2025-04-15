@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Main</li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</nav>
<div class="row home-tab">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">Dashboard
                <span class="float_right">
                    @if(request('duration') != '' || request('start_time') != '' || request('till_date') != '')
                    <a href="{{ url('/home') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i
                                class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
                    @endif
                </span>
            </div>
            <div class="card-body">

                {!! Form::open(['method' => 'GET', 'url' => '/home', 'class' => 'float-right', 'role' => 'search']) !!}
                <div class="row">

                    <div class="col-sm-4">
                        <div class="mb-3">
                            @php
                            $request_duration_sel = '';
                            $graph_text = $graph_text_drop_down = 'All';
                            $show_date_div = 'none';
                            $special_arr = array('all' => 'All','current_week' => 'Current Week','current_month' =>
                            'Current Month','custom' => 'Custom');
                            if(null != request('duration')) {
                            $request_duration_sel = request('duration');
                            $graph_text = ucwords(str_replace("_"," ",$request_duration_sel));
                            $graph_text_drop_down = $graph_text;
                            }
                            if(null != request('graph_date')) {
                            $show_date_div = 'block';
                            $graph_text = request('graph_date');
                            }
                            @endphp
                            {!! Form::label('Name', 'Duration') !!}
                            <select id="duration" name="duration" class="form-control selectpicker duration">
                                <option value="">Select Duration</option>
                                @foreach ($special_arr as $key => $value)
                                @php
                                $selected_value = '';
                                if($request_duration_sel == $key) {
                                $selected_value = 'selected';
                                }
                                @endphp
                                <option value="{{ $key }}" {{ $selected_value }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 date_picker_div" style="display:{{$show_date_div}};">
                        <div class="mb-3">
                            {!! Form::label('start_time', 'Date', ['class' => 'control-label','for'=>'acct_dateClosed'])
                            !!}
                            <div class="cale">
                                <span class="iconcalender"></span>
                                {!! Form::text('graph_date',request('graph_date'),['class' =>
                                'form-control','id'=>'daterangepicker','autocomplete' => "off"]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {!! Form::submit('Submit', ['class' => 'btn btn-primary filter_btn btn-sm','style' =>
                            'display:none']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="row">
                    <div class="col-12 col-xl-12 stretch-card">
                        <div class="row flex-grow-1">
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">

                                            <h6 class="card-title mb-0">Pending Orders</h6>
                                            <div class="dropdown mb-2">
                                                <a href="{{ URL::to('dispatches?search=pending') }}"
                                                    title="Pending Orders">
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h3 class="mb-2">{{ $pending_order }}</h3>
                                                <div class="d-flex align-items-baseline">
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="pending_order_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-0">Complete Orders</h6>
                                            <div class="dropdown mb-2">
                                                <a href="{{ URL::to('dispatches?search=completed') }}"
                                                    title="Complete Orders">
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h3 class="mb-2">{{ $completed_order }}</h3>
                                                <div class="d-flex align-items-baseline">
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="completed_order_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-0">Total Orders</h6>
                                            <div class="dropdown mb-2">
                                                <a href="{{ URL::to('dispatches') }}" title="Total Orders">
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h3 class="mb-2">{{ $total_orders }}</h3>
                                                <div class="d-flex align-items-baseline">
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="total_order_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-12 stretch-card">
                        <div class="row flex-grow-1">
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-0">Customers</h6>
                                            <div class="dropdown mb-2">
                                                <a href="{{ URL::to('customers') }}" title="Customers">
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h3 class="mb-2">{{ $total_customers }}</h3>
                                                <div class="d-flex align-items-baseline">

                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="customer_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-0">Brokers</h6>
                                            <div class="dropdown mb-2">
                                                <a href="{{ URL::to('brokers') }}" title="Brokers">
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h3 class="mb-2">{{ $total_broker }}</h3>
                                                <div class="d-flex align-items-baseline">

                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="broker_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <a>
                                                <h6 class="card-title mb-0">Employees</h6>
                                            </a>
                                            <div class="dropdown mb-2">
                                                <a href="{{ URL::to('employees') }}" title="Employees">
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h3 class="mb-2">{{ $total_employee }}</h3>
                                                <div class="d-flex align-items-baseline">

                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="employee_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-12 stretch-card">
                        <div class="row flex-grow-1">
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-0">Income</h6>
                                            <div class="dropdown mb-2">
                                                <!-- <a type="button" id="income_dropdown" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false" >
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h6 class="mb-2">{{ $total_income }}</h6>
                                                <div class="d-flex align-items-baseline">

                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="income_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-0">Expense</h6>
                                            <div class="dropdown mb-2">
                                                <!-- <a type="button" id="expense_dropdown" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h6 class="mb-2">{{ $total_expense }}</h6>
                                                <div class="d-flex align-items-baseline">

                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="expense_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-0">Profit</h6>
                                            <div class="dropdown mb-2">
                                                <!-- <a type="button" id="profit_dropdown" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h6 class="mb-2">{{ $total_profit }}</h6>
                                                <div class="d-flex align-items-baseline">

                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="profit_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <h6 class="card-title mb-0">Pending to Company</h6>
                                            <div class="dropdown mb-2">
                                                <!-- <a type="button" id="pending_to_company_dropdown"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="icon-lg text-muted pb-3px"
                                                        data-feather="more-horizontal"></i>
                                                </a> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 col-md-12 col-xl-5">
                                                <h6 class="mb-2">{{ $pending_from_cust }}</h6>
                                                <div class="d-flex align-items-baseline">

                                                </div>
                                            </div>
                                            <div class="col-6 col-md-12 col-xl-7">
                                                <div id="pending_to_company_chart" class="mt-md-3 mt-xl-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- row -->
                <div class="row">
                    @if($income_arr_level_json != '')
                    <div class="row flex-grow">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card card-rounded">
                                <div class="card-body">
                                    <div class="d-sm-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="card-title card-title-dash">Revenue Graph</h4>
                                            <p class="card-subtitle card-subtitle-dash">{{ $graph_text }} Data</p>
                                        </div>
                                    </div>
                                    <canvas id="dashboard_revenue_graph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
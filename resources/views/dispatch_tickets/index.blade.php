@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
<div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Dispatches</li>
        <li class="breadcrumb-item active" aria-current="page">Dispatch Tickets</li>
    </ol>
</nav>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">Dispatch Ticket
                <span class="float_right">
                    @if(request('search') != '' || request('dispatch_date') != '' || request('status') != '' || request('ticket') != '' || request('customer') != '' || request('driver') != '')
                    <a href="{{ url('/dispatch_tickets') }}" title="Remove Filter"><button
                            class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove
                            Filter</button></a>
                    @endif
                    @if(Auth::user()->can('viewMenu:ActionDispatchTicket') || Auth::user()->can('viewMenu:All') ||
                    Auth::user()->type == 'admin')
                    <a class="btn btn-success btn-sm" href="{{ URL::to('dispatch_tickets/create') }}"><i
                            class="fa fa-plus" aria-hidden="true"></i> Create Ticket</a>
                    @endif
                </span>
            </div>
            <div class="card-body">
                {!! Form::open(['method' => 'GET', 'url' => '/dispatch_tickets', 'class' => 'float-right', 'role' =>
                'search']) !!}
                <div class="row form-group">

                    <div class="col-md-3 date_picker_div">
                        <div class="form-group">
                            {!! Form::label('start_time', 'Date', ['class' => 'form-label','for'=>'acct_dateClosed'])
                            !!}
                            <div class="cale">
                                <span class="iconcalender"></span>
                                {!! Form::text('dispatch_date',request('dispatch_date'),['class' =>
                                'form-control','id'=>'daterangepicker','autocomplete' => "off"]) !!}
                            </div>
                        </div>
                    </div>
                    @php
                    $status_type_arr = array('pending' => 'Pending','completed' => 'Completed');
                    @endphp
                    <div class="col-md-2 resize2_widthinn">
                        <div class="form-group">
                            {!! Form::label('Name', 'Status', ['class' => 'form-label']) !!}
                            <select id="issue_filter_status" name="status" class="form-control selectpicker"
                                data-live-search="true">
                                <option value="">Select Status</option>
                                @foreach ($status_type_arr as $key => $value)
                                @php
                                $selected_value = '';
                                if(request('status') == $key) {
                                $selected_value = 'selected';
                                }
                                @endphp
                                <option value="{{ $key }}" {{ $selected_value }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
					<div class="col-md-3">
                        {!! Form::label('customer', 'Customer', ['class' => 'form-label','for'=>'customer']) !!}
                        <div class="input-group">
                            <input type="text" class="form-control  search_input" value="{{ request('customer') }}"
                                name="customer" placeholder="Search customer...">
                        </div>
                    </div>
					<div class="col-md-2">
                        {!! Form::label('ticket', 'Ticket Number', ['class' => 'form-label','for'=>'ticket']) !!}
                        <div class="input-group">
                            <input type="text" class="form-control  search_input" value="{{ request('ticket') }}"
                                name="ticket" placeholder="Search ticket...">
                        </div>
                    </div>
					<div class="col-md-2">
                        {!! Form::label('driver', 'Driver', ['class' => 'form-label','for'=>'driver']) !!}
                        <div class="input-group">
                            <input type="text" class="form-control  search_input" value="{{ request('driver') }}"
                                name="driver" placeholder="Search driver...">
                        </div>
                    </div>
                </div>
				<div class="row form-group margin_top_one_per">
				<div class="col-md-4 ">
                        <button class="btn btn-secondary" type="submit">
										<i class="fa fa-search"></i> Search
									</button>
							
                    </div>
				</div>
                {!! Form::close() !!}
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Customer</th>
                                <th>Driver Name</th>
                               <!-- <th>Created At</th>-->
                                <th>Ticket Number</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($dispatch_tickets) > 0)
                            @foreach($dispatch_tickets as $key => $value)
                            <tr>
                                <td>{{ $dispatch_tickets->firstItem() + $key }}</td>
                                <td>
                                    @php
                                    $customer_name = '';
                                    if((null !== $value->getDispatch)) {
                                    echo ucfirst($value->getDispatch->customer_company_name);
                                    @endphp
                                    <br>
                                    @php
                                    echo $customer_name .= '( ' .$value->getDispatch->start_location.'<br> TO<br>
                                    '.$value->getDispatch->dump_location.' <br>'.$value->getDispatch->start_time.' )';
                                    }
                                    @endphp
                                </td>
                                <td>{{  ucfirst($value->driver_name) }}</td>
                                <!--<td>{{ (null !== $value->created_at) ? $value->created_at : '-' }}</td>-->
                                <td>{{ (null !== $value->ticket_number) ? $value->ticket_number : '-' }}</td>
                                <td>
                                    <span class="ticket_index_err_{{$value->id}}" style="color:red;"></span><br>
                                    <div id="td_{{$value->id}}">
                                        @php
                                        $user_status_arr = array('pending' => 'Pending','completed' => 'Completed');
                                        $status_val = $value->status;
                                        @endphp
                                        @if($status_val == 'pending')
                                        <label style="margin-bottom:5px;">Update Status</label>
                                        <select id="{{$value->id}}" data-userid="{{ $value->user_id}}"
                                            data-dispatchid="{{ $value->dispatch_id}}"
                                            class="form-control update_dispatch_ticket_status select_height">
                                            <option value="" selected disabled>Select Status</option>
                                            <option value="pending" selected>Pending</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                        @else
                                        Completed
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ url('/dispatch_tickets/' . $value->id) }}" title="View contact"><button
                                            class="btn btn-info btn-sm"><i class="fa fa-eye"
                                                aria-hidden="true"></i></button></a>
                                    @if(Auth::user()->can('viewMenu:ActionDispatchTicket') ||
                                    Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                                    <!--{!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/dispatch_tickets', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Dispatch" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>-->
                                    <a class="" href="{{ URL::to('dispatch_tickets/' . $value->id . '/edit') }}"
                                        title="Edit Dispatch"><button class="btn btn-primary btn-sm"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                    @endif

                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="7">No data Found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pagination"> {!! $dispatch_tickets->appends(['search' =>
                        Request::get('search'),'dispatch_date' =>
                        Request::get('dispatch_date'),'status' =>
                        Request::get('status'),'customer' =>
                        Request::get('customer'),'ticket' =>
                        Request::get('ticket'),'driver' =>
                        Request::get('driver')])->render() !!} </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
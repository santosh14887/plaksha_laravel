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
				<li class="breadcrumb-item active" aria-current="page">Dispatches</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header"> Orders<span class="float_right">
				  @if(request('search') != '' || request('dispatch_date') != '' || request('status') != '' || request('start_location') != '' ||request('end_location') != '' || request('customer') != '')
					<a href="{{ url('/dispatches') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					@if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
					<a class="btn btn-success btn-sm" href="{{ URL::to('dispatches/create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Add New</a>
				@endif
				  </span></div>
                <div class="card-body">
                  <div class="row">
					{!! Form::open(['method' => 'GET', 'url' => '/dispatches', 'class' => 'float-right', 'role' => 'search'])  !!}
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
                        {!! Form::label('start_location', 'Start Location', ['class' => 'form-label','for'=>'start_location']) !!}
                        <div class="input-group">
                            <input type="text" class="form-control  search_input" value="{{ request('start_location') }}"
                                name="start_location" placeholder="Search Start Location...">
                        </div>
                    </div>
					<div class="col-md-2">
                        {!! Form::label('end_location', 'End Location', ['class' => 'form-label','for'=>'end_location']) !!}
                        <div class="input-group">
                            <input type="text" class="form-control  search_input" value="{{ request('end_location') }}"
                                name="end_location" placeholder="Search End Location...">
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
					</div>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
						<!--<th>Ticket Number</th>-->
                        <th>Customer Name</th>
                        <th>Start Time</th>
                        <th> Location</th>
                        <!--<th>Job Type</th>-->
                        <th>Status</th>
						<th>Required Unit</th>
                        <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($dispatches) > 0)
                            @foreach($dispatches as $key => $value)
							@php
								$accepted_count = 0;
								if(null !== $value->getAssignDispatchAssign) {
									foreach($value->getAssignDispatchAssign as $val)
									$accepted_count += $val->no_of_vehicles;
								}
							@endphp
                                <tr>
								<td>{{ $dispatches->firstItem() + $key }}</td>
                                    <!--<td>{{ $value->default_dispatch_number }}</td>-->
                                    <td>{{ $value->getCustomer->company_name }}</td>
                                    <td>{{ $value->start_time }}</td>
                                    <td>{{ $value->start_location }} <br> TO <br>{{ $value->dump_location }}</td>
                                    <!--<td>{{ ($value->job_type == 'load') ? 'Rate per load' : ucfirst($value->job_type) }}</td>-->
                                    <td>{{ ucfirst($value->status) }}</td>
									<td><a href="{{ URL::to('assigned_dispatche/' . $value->id) }}" title="Assign Order">{{ $value->required_unit }} (Assign Order)</a></td>
                                    
                                    <td>
										<a class="btn btn-sm btn-info" href="{{ URL::to('/dispatches/' . $value->id) }}" title="View Employee"><i class=" menu-icon fa fa-lg fa-eye"></i></a>
										@if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
										@if($value->status == 'pending')
                                        <a class="" href="{{ URL::to('dispatches/' . $value->id . '/edit') }}" title="Edit Dispatch"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
										@endif
									<!--{!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/dispatches', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Dispatch" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>-->
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
                    <div class="pagination"> {!! $dispatches->appends(['search' =>
                        Request::get('search'),'dispatch_date' =>
                        Request::get('dispatch_date'),'status' =>
                        Request::get('status'),'customer' =>
                        Request::get('customer'),'location' =>
                        Request::get('location')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

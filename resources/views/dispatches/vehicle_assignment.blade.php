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
				<li class="breadcrumb-item active" aria-current="page">Vehicle Assignment</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header"> Vehicle Assignment<span class="float_right">
				  @if(request('search') != '')
					<a href="{{ url('/dispatch/vehicle_assignment') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					<!--<a class="btn btn-success btn-sm" href="{{ URL::to('dispatches/create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Add New</a>-->
				  </span></div>
                <div class="card-body">
				{!! Form::open(['method' => 'GET', 'url' => '/dispatch/vehicle_assignment', 'class' => 'float-right assignment_vehicle_form', 'role' => 'search'])  !!}
				@php
				$filter_time = date('Y-m-d');
				if(null !== request('assignment_date')) {
					$filter_time = request('assignment_date');
				}
				@endphp
				   <div class="row">
						<div class="col-sm-4 date_picker_div">
							<div class="mb-3">
								  {!! Form::label('start_time', 'Date', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
								  
								  <div class="input-group flatpickr" id="flatpickr-date">
								  <input type="text" class="form-control assignment_date" name="assignment_date" value="{{ $filter_time }}" placeholder="Select date" data-input="">
									<span class="input-group-text input-group-addon" data-toggle=""><i data-feather="calendar"></i></span>
								</div>
							   </div>
						</div>
						@php
					$status_type_arr = array('all' => 'All','assigned' => 'Assigned','unassigned' => 'UnAssigned')
					@endphp
					<div class="col-md-3 resize2_widthinn">
						<div class="form-group">
						{!! Form::label('Name', 'Status', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
						<select id="vehicle_assignment_filter_status" name="status" class="form-control selectpicker vehicle_assignment_filter_status" data-live-search="true">
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
						<!--<div class="col-sm-4">
							<div class="mb-3">
						{!! Form::label('Search', 'Search', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
							<div class="input-group">
								<input type="text" class="form-control  search_input" value="{{ request('search') }}" name="search" placeholder="Search...">
								<span class="input-group-append">
									<button class="btn filter_btn btn-secondary" type="submit">
										<i class="fa fa-search"></i>
									</button>
								</span>
							</div>
						
						</div>
					</div>-->
				</div>
				{!! Form::close() !!}
                  <div class="table-responsive">
                    <table id="vehicle_assignment_tbl" class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
                        <th>Vehicle Number</th>
                        <th>Vin Number</th>
                        <th> Driver Name</th>
                        <th> Dispatch Assigned</th>
                        <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
						@php
							$count_start = 1;
							$vehicle_id_arr = array();
						@endphp
                            @if(count($all_data) > 0)
                            @foreach($all_data as $key => $value)
							@php
							$driver_name = $start_time = $dispatch_assign = $status = '';
								$vehicle_id = $value['id'];
								$vehicle_id_arr[$vehicle_id] = $vehicle_id;
								$vehicle_number = $value['vehicle_number'];
								$vin_number = $value['vin_number'];
								if(isset($value['get_owner']) && !empty($value['get_owner'])) {
									$driver_name = $value['get_owner']['name'];
								} else if(isset($value['get_owner']) && empty($value['get_owner'])) {
								} else {
									$start_time = (isset($value['start_time'])) ? $value['start_time'] : '';
									$company_name = (isset($value['company_name'])) ? $value['company_name'] : '';
									$dispatch_assign = ($company_name != '') ? $company_name.'('.$start_time.')' : '';
									$status = (isset($value['status'])) ? $value['status'] : '';
									$driver_name = (isset($value['name'])) ? $value['name'] : '';
								}
								$status_text_color = '';
								switch ($status) {
									case "submitted":
									$status_text_color = 'blue';
									break;
									case "accepted":
									$status_text_color = 'green';
									break;
									case "pending":
									$status_text_color = 'gray';
									break;
									default:
									$status_text_color = 'red';
								}
								
							@endphp
                                <tr>
								<td>{{ $count_start++ }}</td>
                                    <td>{{ $vehicle_number }}</td>
                                    <td>{{ $vin_number }}</td>
                                    <td>{{ $driver_name }}</td>
                                    <td>{{ $dispatch_assign }}</td>
                                    <td style="color:{{$status_text_color}};">{{ ucfirst($status) }}</td>
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="6">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
                    </div>
                </div>
              </div>
            </div>
          </div>
@endsection

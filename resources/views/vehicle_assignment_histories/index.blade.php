@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Vehicles</li>
				<li class="breadcrumb-item active" aria-current="page">Assignment History</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Assignment History
					<span class="float_right">
					@if(request('search') != '' || request('on_date') != '' || request('status') != '')
					<a href="{{ url('/vehicle_assignment_histories') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
				  <a href="{{ url('/vehicle_assignment_histories/create') }}" class="btn btn-success btn-sm" title="Add Fuel History">
                            <i class="fa fa-plus" aria-hidden="true"></i> Assign Vehicle
                        </a>
						@endif
					</span></div>
                <div class="card-body">
				  {!! Form::open(['method' => 'GET', 'url' => '/vehicle_assignment_histories', 'class' => 'float-right', 'role' => 'search'])  !!}
				   <div class="row form-group">
				   
						<div class="col-md-3 date_picker_div">
							   <div class="form-group">
								  {!! Form::label('start_time', 'Date', ['class' => 'control-label','for'=>'acct_dateClosed']) !!}
								  <div class="cale">
									 <span class="iconcalender"></span>
									 {!! Form::text('on_date',request('on_date'),['class' => 'form-control','id'=>'daterangepicker','autocomplete' => "off"]) !!}
								  </div>
							   </div>
						</div>
						<div class="col-md-4">
						{!! Form::label('Search', 'Search', ['class' => 'control-label','for'=>'acct_dateClosed']) !!}
							<div class="input-group">
								<input type="text" class="form-control  search_input" value="{{ request('search') }}" name="search" placeholder="Search...">
								<span class="input-group-append">
									<button class="btn filter_btn btn-secondary" type="submit">
										<i class="fa fa-search"></i>
									</button>
								</span>
							</div>
						
					</div>
				</div>
				{!! Form::close() !!}
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
                        <th>User Name</th>
                        <th>User Email</th>
                        <th>vehicle</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Vehicle Type</th>
                        <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($vehicle_assignment_histories) > 0)
                            @foreach($vehicle_assignment_histories as $key => $value)
                                <tr>
								<td>{{ $vehicle_assignment_histories->firstItem() + $key }}</td>
                                    <td>{{ (null !== $value->user_name) ? ucfirst($value->user_name) : '-' }}</td>
                                    <td>{{ (null !== $value->user_email) ? $value->user_email : '-' }}</td>
                                    <td>{{ (null !== $value->vehicle_number) ? $value->vehicle_number : '-' }}</td>
                                    <td>{{ (null !== $value->start_time) ? date('d M Y h:i:s',strtotime($value->start_time)) : '-' }}</td>
                                    <td>{{ (null !== $value->end_time) ? date('d M Y h:i:s',strtotime($value->end_time)) : '-' }}</td>
									<td>{{ ucfirst($value->user_vehicle_type) }}</td>
                                    <td>
                                    <!--{!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/vehicle_assignment_histories', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-small" title="Delete Dispatch" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>-->
											<a class="btn btn-sm btn-info" href="{{ URL::to('/vehicle_assignment_histories/' . $value->id) }}" title="View Assignment"><i class=" menu-icon fa fa-lg fa-eye"></i></a>
										@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
											@if($value->user_vehicle_type == 'temporary')
                                        <a class="" href="{{ URL::to('vehicle_assignment_histories/' . $value->id . '/edit') }}" title="Edit Dispatch"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
										@endif
										@endif

                                    </td>
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="5">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
					<div class="pagination"> {!! $vehicle_assignment_histories->appends(['search' => Request::get('search')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

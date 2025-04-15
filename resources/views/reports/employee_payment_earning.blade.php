@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Reports</li>
				<li class="breadcrumb-item active" aria-current="page">Reports</li>
				<li class="breadcrumb-item active" aria-current="page">Employee Payment and Earning</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Employee Payment and Earning
					  <span class="float_right">
					  @if(request('search') != '' || request('dispatch_date') != '' || request('status') != '' || request('ticket') != '' || request('customer') != '' || request('driver') != '')
					<a href="{{ url('/reports/employee_payment_earning') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					  </span></div>
                <div class="card-body">
				  {!! Form::open(['method' => 'GET', 'url' => '/reports/employee_payment_earning', 'class' => 'float-right', 'role' => 'search'])  !!}
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
					<div class="col-md-3">
                        {!! Form::label('driver', 'Driver / Employee', ['class' => 'form-label','for'=>'driver']) !!}
                        <div class="input-group">
                            <input type="text" class="form-control  search_input" value="{{ request('driver') }}"
                                name="driver" placeholder="Search driver...">
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
					
                </div>
				<div class="row form-group margin_top_one_per margin_bottom_one_per">
				<div class="col-md-4 ">
                        <button class="btn btn-secondary" type="submit">
										<i class="fa fa-search"></i> Search
									</button>
							
                    </div>
				</div>
				{!! Form::close() !!}
                  <div class="table-responsive">
                    <table id="customer_revenue" class="table table-hover" data-show-pagination-switch="true">
                      <thead>
                        <tr>
						<th>S.No</th>
						<th>Truck No</th>
                        <th>Employee</th>
						<th>Dispatch At</th>
						<th>Customer</th>
						<th>ticket No</th>
                        <th>Hours</th>
                        <th>Load</th>
                        <th>Employee Pay</th>
                        <th>Remark</th>
                        
                        </tr>
                      </thead>
                      <tbody>
					  @php
					  $count_statr = 0;
					  @endphp
                            @if(null != $dispatch_tickets)
                            @foreach($dispatch_tickets as $key => $value)
						@php
						$hours_time = $load_time = 0;
						if(null !== $value->getDispatch && $value->getDispatch->job_type == 'hourly') {
							$hours_time = $value->hour_or_load;
						}
						if(null !== $value->getDispatch && $value->getDispatch->job_type == 'load') {
							$load_time = $value->hour_or_load;
						}
						@endphp
                                <tr>
								<td>{{ ++$count_statr }}</td>
								<td>{{ $value->unit_vehicle_number }}</td>
								
								<td>{{ $value->driver_name }}</td>
								<td>{{ (null !== $value->getDispatch) ? date('d M Y',strtotime($value->getDispatch->start_time)) : '-' }}</td>
								<td>{{ (null !== $value->getDispatch->customer_company_name) ? $value->getDispatch->customer_company_name : '-' }}</td>
								<td>{{ (null !== $value->ticket_number) ? $value->ticket_number : '-' }}</td>
								<td>{{ $hours_time }}</td>
								<td>{{ $load_time }}</td>
								<td>{{ $value->expense }}</td>
								<td>
								<div class="remark_div_{{ $value->id }}">{{ $value->remark }}</div>
									<a class="remark_model_btn" href="javascript:void(0)" data-id="{{ $value->id }}" data-toggle="modal" data-target="#remark_model"
                                        title="Add / Update Remark"><button class="btn btn-primary btn-sm"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
								</td>
								
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="10">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
			    	<div id="remark_model" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
					  <div class="modal-dialog">
						<!-- Modal content-->
						<div class="modal-content">
						  <div class="modal-header">
							<h4 class="modal-title">Remark</h4>
							<button type="button" class="close remark_close" data-dismiss="modal">&times;</button>
						  </div>
						  <div class="modal-body">
								<div class="form-group row">
									<div class="col-md-12 ">
										<div class="form-group">
										{!! Form::label('Name', 'Remark') !!}
										<textarea id="popup_remark" class="form-control" name="remark" rows="5"></textarea>
										</div>
										<input type="hidden" id="remark_ticket_id" value="">
									</div>
								</div>
								<p class="col-md-12 remark_error alert" id="remark_error"></p>
						  </div>
						  <div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary remark_save" id="remark_save">Save changes</button>
						  </div>
						</div>
					  </div>
					</div>
            </div>
          </div>
		  
@endsection

@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Reports</li>
				<li class="breadcrumb-item active" aria-current="page">Bill</li>
				<li class="breadcrumb-item active" aria-current="page">Generate Employee Bill</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Generate Employee Bill
					  <span class="float_right">
					  @if(request('search') != '' || request('dispatch_date') != '' || request('employee_id') != '')
					<a href="{{ url('/reports/employee_bill') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					  </span></div>
                <div class="card-body">
				  {!! Form::open(['method' => 'GET', 'url' => '/reports/employee_bill', 'class' => 'float-right', 'role' => 'search'])  !!}
				   <div class="row">
						<div class="col-sm-4">
						<div class="mb-3">
							{!! Form::label('Name', 'Employee', ['class' => 'form-label']) !!}
							<select id="issue_filter_status" name="employee_id" class="form-control selectpicker" data-live-search="true" required>
								<option value="">Select User</option>
								@foreach ($employees as $key => $value)
								@php
								$selected_value = '';
								if(request('employee_id') == $key) {
									$selected_value = 'selected';
								}
								@endphp
									<option value="{{ $key }}" {{ $selected_value }}>{{ $value }}</option>
								@endforeach
							</select> 
							</div>
						</div>
						<div class="col-sm-4 date_picker_div">
							<div class="mb-3">
							  {!! Form::label('start_time', 'Date', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
							  <div class="cale">
								 <span class="iconcalender"></span>
								 {!! Form::text('dispatch_date',request('dispatch_date'),['class' => 'form-control invoice_date_select','id'=>'daterangepicker','autocomplete' => "off",'required' => "required"]) !!}
							  </div>
							</div>
						</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
							<div class="mb-3">
					<input type="submit" value="submit" class="btn btn-primary me-2">
					</div>
				</div>
				</div>
				{!! Form::close() !!}
                  <div class="table-responsive max_height_fifty">
                    <table id="table" class="table table-hover">
                      <thead>
                        <tr>
						<!--<th>Ticket Number</th>-->
						<th><div class="inline_checkbox"><input type="checkbox" name="select_all_emp_bill" data-id="view" id="select_all_emp_bill" class=" checkbox_css select_all_emp_bill" value="select_all_emp_bill"> <span class="checkbox_span">All</span></div> Select Dispatch</th>
						<th>Dispatch Date</th>
						<th>Location</th>
                        <th>User Name</th>
                        <th>Ticket Number</th>
                        <th>Total Amount</th>
                        <th>Dispatch Status</th>
                        <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($dispatch_data) > 0)
                            @foreach($dispatch_data as $key => $value)
						@php
						$income_amount = 0;
						$income_amount += $value->income;
						@endphp
                                <tr>
								<td>
								@if($value->employee_invoice_generate_status == 'pending')
								<input type="checkbox" id="{{$value->id}}" class="checkbox_css employee_invoice_checkbox" name="employee_invoice_checkbox" value="{{$value->getDispatch->id}}" data-ticketid = "{{ $value->id }}"> <!--<span>{{ $value->default_dispatch_number	}}</span> -->
							<input type="text" name = "employee_ticket_number" class="employee_ticket_number" id="employee_ticket_number_{{$value->id}}"  value="{{ $value->ticket_number	}}" autocomplete="off" style="display:none;"><br>
								<span class="ticket_number_span" id="ticket_number_span_{{$value->id}}" style="color:red;"></span>
								@else
								<!-- {{ $value->default_dispatch_number }}  --->
								@endif
								</td>
								<td>{{ $value->getDispatch->start_time }}</td>
								<td>{{ $value->getDispatch->start_location.' To '.$value->getDispatch->dump_location }}</td>
								<td>{{ $value->getUser->name }}</td>
								<td>{{ $value->ticket_number }}</td>
								<td>{{ $income_amount }}</td>
								<td>{{ ucfirst($value->getDispatch->status) }}</td>
								<td id="invoice_sent_{{$value->id}}">
								@if($value->employee_invoice_generate_status == 'pending')
								<span style="color:green">Pending</span>
								@else
									<span style="color:green">Generated</span>
								@endif
							</td>
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="8">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
					
                  </div>
				  <div class="col-md-12 margin_top_one_per">
				  
					 @if(count($dispatch_data) > 0)
						 <a href="javascript:void(0)" class=" employee_all_generate_invoice btn btn-primary me-2" title="generate Invoice">Generate Invoice</a>
					 <span class="employee_all_generate_invoice_err" style="color:red;"></span>
						 @endif
				  </div>
				  
                </div>
              </div>
            </div>
          </div>
@endsection

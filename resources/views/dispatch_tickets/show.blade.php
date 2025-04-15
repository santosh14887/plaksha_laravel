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
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/dispatch_tickets') }}">Dispatch Tickets</a></li>
				<li class="breadcrumb-item active" aria-current="page">View Ticket</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 show_page grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">{{ $dispatch_tickets->getDispatch->customer_company_name }}</div>
                <div class="card-body">
				  <div class="button-col">
				  <a href="{{ url('/dispatch_tickets') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
				  @if(Auth::user()->can('viewMenu:ActionDispatchTicket') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
					<a href="{{ url('/dispatch_tickets/'.$dispatch_tickets->id . '/edit') }}"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
					@endif
					</div>
				  <div class="table-responsive">
					<table class="table">
					  <tbody>
						<tr>
						  <th scope="row">Job Type</th>
						  <td>{{ ($dispatch_tickets->getDispatch->job_type == 'load') ? 'Rate per load' : ucfirst($dispatch_tickets->getDispatch->job_type) }}</td>
						  <td><strong>Customer Name</strong></td>
						  <td>{{ $dispatch_tickets->getDispatch->customer_company_name.' ( '.$dispatch_tickets->getDispatch->start_location.' To '.$dispatch_tickets->getDispatch->dump_location.' '.$dispatch_tickets->getDispatch->start_time.' )' }}</td>
						</tr>
						<tr>
						  <th scope="row">Employee/Broker Hour Rate</th>
						  <td>{{ ucfirst($dispatch_tickets->emp_brok_hour_rate) }}</td>
						  <td><strong>Employee/Broker Load Rate</strong></td>
						  <td>{{ ucfirst($dispatch_tickets->emp_brok_load_rate) }}</td>
						</tr>
						<tr>
						  <th scope="row">Order Rate</th>
						  <td>{{ $dispatch_tickets->getDispatch->job_rate }}</td>
						  <td><strong>Total {{ ($dispatch_tickets->getDispatch->job_type == 'load') ? 'Load' : 'Hours' }}</strong></td>
						  <td>{{ $dispatch_tickets->hour_or_load }}</td>
						</tr>
						
						<tr>
						  <th scope="row">Income</th>
						  <td>{{ $dispatch_tickets->income }}</td>
						  <td><strong>Salary</strong></td>
						  <td>{{ $dispatch_tickets->expense }}</td>
						</tr>
						<tr>
						  <th scope="row">Profit</th>
						  <td>{{ $dispatch_tickets->profit }}</td>
						  <td><strong>Fuel Expense</strong></td>
						  <td>{{ $dispatch_tickets->fuel_amount_paid }}</td>
						</tr>
						@if($dispatch_tickets->emploee_hour_over_load > 0)
							<tr>
						  <th scope="row">Convert Loads to Hours</th>
						  <td>{{ ($dispatch_tickets->emploee_hour_over_load > 0) ? $dispatch_tickets->emploee_hour_over_load : ''}}</td>
						  <td><strong>Employee Payment</strong></td>
						  <td>{{ $dispatch_tickets->emploee_hour_over_load_amount }}</td>
						</tr>
						@endif
						<tr>
						  <th scope="row">Shift Type</th>
						  <td>{{ ucfirst($dispatch_tickets->shift_type) }}</td>
						  <td><strong>User Type</strong></td>
						  <td>{{ ucfirst($dispatch_tickets->user_type) }}</td>
						</tr>
						<tr>
						  <!-- <th scope="row">Employee/Broker Name</th>
						  <td>{{ ucfirst($dispatch_tickets->getUser->name) }}</td> -->
						  <td><strong>Driver Name</strong></td>
						  <td>{{ ucfirst($dispatch_tickets->driver_name) }}</td>
						</tr>
						<tr>
						  <th scope="row">Unit / Vehicle Number</th>
						  <td>{{ $dispatch_tickets->unit_vehicle_number }}</td>
						  <td><strong>Contact Number</strong></td>
						  <td>{{ $dispatch_tickets->contact_number }}</td>
						</tr>
						<tr>
						  <th scope="row">Starting Km</th>
						  <td>{{ $dispatch_tickets->starting_km }}</td>
						  <td><strong>Status</strong></td>
						  <td><strong>{{ ucfirst($dispatch_tickets->status) }}</strong></td>
						</tr>
						@if($dispatch_tickets->user_type == 'employee')
						<tr>
						  <th scope="row">Gas Station Location</th>
						  <td>{{ $dispatch_tickets->ticket_number }}</td>
						  <td><strong>Ending Km</strong></td>
						  <td>{{ $dispatch_tickets->ending_km }}</td>
						</tr>
						<tr>
						  <th scope="row">Total Km</th>
						  <td>{{ $dispatch_tickets->total_km }}</td>
						  <td><strong>Fuel Quantity</strong></td>
						  <td>{{ $dispatch_tickets->fuel_qty }}</td>
						</tr>
						<tr>
						  <th scope="row">Created Date</th>
						  <td>{{ $dispatch_tickets->created_at }}</td>
						  <td><strong></strong></td>
						  <td></td>
						</tr>
						<tr>
						  <th scope="row">Fuel Card Number</th>
						  <td>{{ $dispatch_tickets->fuel_card_number }}</td>
						  <td><strong>Fuel Receipt</strong></td>
						  <td>
						  @if(null != $dispatch_tickets->fuel_receipt)
						  <img class="download_img" title="Click here to download" src="{{ asset('images/fuel_receipt/'.$dispatch_tickets->fuel_receipt) }}" data-id="{{ asset('images/fuel_receipt/'.$dispatch_tickets->fuel_receipt) }}"  />
						@endif
						  </td>
						</tr>
						<tr>
						  <th scope="row">Def Quantity</th>
						  <td>{{ $dispatch_tickets->def_qty }}</td>
						  <td><strong>Def Receipt</strong></td>
						  <td>
						  @if(null != $dispatch_tickets->def_receipt)
						  <img class="download_img" title="Click here to download" src="{{ asset('images/def_receipt/'.$dispatch_tickets->def_receipt) }}" data-id="{{ asset('images/def_receipt/'.$dispatch_tickets->def_receipt) }}"  />
						@endif
						  </td>
						</tr>
						@endif
						<tr>
						  <td><strong>Ticket Number</strong></td>
						  <td>{{ $dispatch_tickets->ticket_number }}</td>
						  <td><strong>Ticket Receipt</strong></td>
						  <td><img class="download_img" title="Click here to download" src="{{ asset('images/ticket_img/'.$dispatch_tickets->ticket_img) }}"  data-id="{{ asset('images/ticket_img/'.$dispatch_tickets->ticket_img) }}" /></td>
						</tr>
					  </tbody>
					</table>
					</div>
                </div>
              </div>
            </div>
          </div>
@endsection

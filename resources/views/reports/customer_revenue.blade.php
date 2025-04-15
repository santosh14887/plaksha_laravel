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
				<li class="breadcrumb-item active" aria-current="page">Customers Revenue</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Customer revenue
					  <span class="float_right">
					  @if(request('search') != '' || request('dispatch_date') != '' || request('status') != '' || request('ticket') != '' || request('customer') != '')
					<a href="{{ url('/reports/customer_revenue') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					  </span></div>
                <div class="card-body">
				  {!! Form::open(['method' => 'GET', 'url' => '/reports/customer_revenue', 'class' => 'float-right', 'role' => 'search'])  !!}
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
                    <table id="customer_revenue" class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
						<th>Dispatch</th>
						<th>Ticket Number</th>
                        <th>Customer Name</th>
                        <th>Dispatch date</th>
                        <th>Earning</th>
                        <th>Expense</th>
                        </tr>
                      </thead>
                      <tbody>
					  @php
					  $count_statr = 0;
					  @endphp
                            @if(null !== $dispatch_tickets)
                            @foreach($dispatch_tickets as $key => $value)
                                <tr>
								<td>{{ ++$count_statr }}</td>
								<td>{{ (null !== $value->getDispatch->start_location) ? ucfirst($value->getDispatch->start_location).' TO '.ucfirst($value->getDispatch->dump_location) : '-' }}</td>
								<td>{{ $value->ticket_number }}</td>
                                <td>{{ (null !== $value->getDispatch) ? ucfirst($value->getDispatch->getCustomer->company_name) : '-' }}</td>
								<td>{{ (null !== $value->getDispatch) ? ucfirst($value->getDispatch->start_time) : '-' }}</td>
                                <td>{{ (null !== $value->income) ? $value->income : '-' }}</td>
                                <td>{{ (null !== $value->expense) ? $value->expense : '-' }}</td>
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="7">No data Found</td>
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

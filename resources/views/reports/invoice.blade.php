@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Reports</li>
				<li class="breadcrumb-item active" aria-current="page">Invoice</li>
				<li class="breadcrumb-item active" aria-current="page">Generate Invoice</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Generate Invoice
					  <span class="float_right">
					  @if(request('search') != '' || request('dispatch_date') != '' || request('customer_id') != '')
					<a href="{{ url('/reports/invoice') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					  </span></div>
                <div class="card-body">
				  {!! Form::open(['method' => 'GET', 'url' => '/reports/invoice', 'class' => 'float-right', 'role' => 'search'])  !!}
				   <div class="row">
						<div class="col-sm-4">
						<div class="mb-3">
							{!! Form::label('Name', 'Customer', ['class' => 'form-label']) !!}
							<select id="issue_filter_status" name="customer_id" class="form-control selectpicker" data-live-search="true" required>
								<option value="">Select Customer</option>
								@foreach ($customer as $key => $value)
								@php
								$selected_value = '';
								if(request('customer_id') == $key) {
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
						<th> <div class="inline_checkbox"><input type="checkbox" name="select_all_invoice" data-id="view" id="select_all_invoice" class=" checkbox_css select_all_invoice" value="select_all_invoice"> <span class="checkbox_span">All</span></div> Select Dispatch</th>
						<th>Dispatch Date</th>
						<th>Location</th>
                        <th>Customer</th>
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
						foreach($value->getDispatchTicket as $vals) {
							$income_amount += $vals->income;
						}
						@endphp
                                <tr>
								<td>
								@if($value->invoice_sent == 'pending')
								<input type="checkbox" id="{{$value->id}}" class="invoice_checkbox" name="invoice_checkbox" value="{{$value->id}}"> <!--<span>{{ $value->default_dispatch_number	}}</span> -->
							<input type="text" name = "ticket_number" class="ticket_number" id="ticket_number_{{$value->id}}" value="{{ $value->default_dispatch_number	}}" autocomplete="off" style="display:none;"><br>
								<span class="ticket_number_span" id="ticket_number_span_{{$value->id}}" style="color:red;"></span>
								@else
								<!-- {{ $value->default_dispatch_number }}  --->
								@endif
								</td>
								<td>{{ $value->start_time }}</td>
								<td>{{ $value['start_location'].' To '.$value['dump_location'] }}</td>
								<td>{{ $value->getCustomer->company_name }}</td>
								<td>{{ $income_amount }}</td>
								<td>{{ ucfirst($value->status) }}</td>
								<td id="invoice_sent_{{$value->id}}">
								@if($value->invoice_sent == 'pending')
								<span style="color:green">Pending</span>
								@else
									<span style="color:green">Generated</span>
								@endif
							</td>
								<!--<td id="invoice_sent_{{$value->id}}">
								@if($value->invoice_sent == 'pending')
								<a href="{{ url('/reports/generate_pdf/'.$value->id) }}" title="generate Invoice">Generate Invoice</a> / <a href="javascript:void(0)" class="invoice_sent" data-id="{{ $value->id}}">Invoice Sent</a>
								@else
									<span style="color:green">Invoice Sent</span>
								@endif
							</td>-->
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
				  
				  
				  
				  <div class="col-md-12 margin_top_one_per">
					@if(count($dispatch_data) > 0)
						 <a href="javascript:void(0)" class=" all_generate_invoice btn btn-primary me-2" title="generate Invoice">Generate Invoice</a>
					 <span class="all_generate_invoice_err" style="color:red;"></span>
						 @endif
				  </div>
				  
				  
				  
				  

                </div>
              </div>
            </div>
          </div>
@endsection

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
				<li class="breadcrumb-item active" aria-current="page">All Invoices</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">All Invoices
					  <span class="float_right">
					  @if(request('search') != '' || request('issue_date') != '' || request('customer_id') != '')
					<a href="{{ url('/reports/all_invoices') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					  </span></div>
                <div class="card-body">
				  {!! Form::open(['method' => 'GET', 'url' => '/reports/all_invoices', 'class' => 'float-right', 'role' => 'search'])  !!}
				   <div class="row">
						<div class="col-sm-4">
							<div class="mb-3">
							{!! Form::label('Name', 'Customer', ['class' => 'form-label']) !!}
							<select id="issue_filter_status" name="customer_id" class="form-control selectpicker" data-live-search="true" >
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
									 {!! Form::text('dispatch_date',request('dispatch_date'),['class' => 'form-control ','id'=>'daterangepicker','autocomplete' => "off"]) !!}
								  </div>
							   </div>
						</div>
						@php
				$status_type_arr = array('pending' => 'Pending','completed' => 'Completed');
				@endphp
				<div class="col-sm-4">
					<div class="mb-3">
						{!! Form::label('Name', 'Status', ['class' => 'form-label']) !!}
						<select id="invoice_filter_status" name="status" class="form-control selectpicker" data-live-search="true">
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
				<div class="row">
					<div class="col-sm-4 date_picker_div">
						<div class="mb-3">
							<input type="submit" value="submit" class="btn btn-primary me-2">
						</div>
				</div>
				</div>
				{!! Form::close() !!}
                  <div class="table-responsive">
                    <table id="table" class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
						<th>Customer Name</th>
						<th>Invoice Number</th>
						<!--<th>Ticket Number</th>-->
						<th>Invoice Date</th>
						<th>Subtotal</th>
						<th>HST</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Invoice</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($dispatch_data) > 0)
                            @foreach($dispatch_data as $key => $value)
						@php
						$ticket_num_arr = array();
						$ticket_num_str = '';
						foreach($value->dispatches as $dispatches_vals) {
							$ticket_num_arr[] = $dispatches_vals['default_dispatch_number'];
						}
						$ticket_num_str = implode(', ',$ticket_num_arr);
						@endphp
                                <tr>
								<td>{{ $dispatch_data->firstItem() + $key }}</td>
								<td>{{ $value->getCustomer->company_name }}</td>
								<td>{{ $value->invoice_number }}</td>
								<!--<td>{{ $ticket_num_str }}</td>-->
								<td>{{ $value->invoice_date }}</td>
								<td>{{ $value->subtotal}}</td>
								<td>{{ $value->hst_amount}}</td>
								<td>{{ $value->total}}</td>
								<td id="td_{{$value->id}}">
								
										@php
										$status_val = $value->status;
										@endphp
										@if(Auth::user()->can('viewMenu:ActionInvoice') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
										@if($status_val == 'pending')
											<label style="margin-bottom:5px;">Update Status</label>
										<select id="{{$value->id}}" class="form-control update_invoice_status select_height">
											<option value="" selected disabled>Select Status</option>
											<option value="pending" selected>Pending</option>
											<option value="completed">Completed</option>
										</select>
										@else
											Completed
										@endif
										@else
										{{ucfirst($status_val)}}
								@endif
									</td>
									@if($value->invoice_pdf != '')
										<td> <i class="menu-icon fa fa-lg fa-file download_img" data-id="{{ asset('images/pdf/'.$value->invoice_pdf) }}"></i></td>
										@else
											<td>
										<i class="menu-icon fa fa-lg fa-file quickbook_download_pdf" data-id="{{ asset('images/pdf') }}" data-rowid ="{{ $value->id }}" data-quickbookid ="{{ $value->quickbook_invoice_id }}"></i>
										<br>
										<span id="all_invoice_quickbook_{{ $value->id }}"></span>
										</td>
									@endif
								
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="8">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
					<div class="pagination"> {!! $dispatch_data->appends(['search' => Request::get('search')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
@endsection

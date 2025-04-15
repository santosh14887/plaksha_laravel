@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('customers') }}">Customers</a></li>
				<li class="breadcrumb-item active" aria-current="page">Transactions</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">
			  <a href="{{ url('customers') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>  Transactions ({{ $user_data['company_name'] }}) Current Amount :- {{ $user_data['current_amount']}}<span class="float_right">
					@if(request('search') != '')
					<a href="{{ url('/transactions') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif <!--<a href="{{ URL::to('transactions/create') }}">Create Employee</a>-->
				  </span></div>
                <div class="card-body">
				<input type="hidden" id="user_id" value="{{$user_data['id']}}">
                  <!--<div class="row">
						<div class="col-md-8"></div>
							<div class="col-md-4 float_right">
							  {!! Form::open(['method' => 'GET', 'url' => '/transactions', 'class' => 'float-right', 'role' => 'search'])  !!}
									<div class="input-group">
										<input type="text" class="form-control search_input" value="{{ request('search') }}" name="search" placeholder="Search...">
										<span class="input-group-append">
											<button class="btn btn-secondary" type="submit">
												<i class="fa fa-search"></i>
											</button>
										</span>
									</div>
								{!! Form::close() !!}
						</div>
					</div>-->
					@if(Auth::user()->can('viewMenu:ActionTransaction') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
					<div class="row">
				<div class="col-sm-3">
						<div class="mb-3">
					{{ Form::label('amount', 'Amount *') }}
					{{ Form::text('amount', Input::old('amount'), array('class' => 'form-control')) }}
					{!! $errors->first('amount','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				@php
				$status_arr = array('credit' => 'Credit','debit' => 'Debit')
				@endphp
				<div class="col-sm-3">
						<div class="mb-3">
					{!! Form::label('type', 'Type *') !!}
					<select id="type" name="type" class="form-control selectpicker" data-live-search="true">
						<option value="">Select Type</option>
						@foreach ($status_arr as $key => $value)
							<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</select> 
					{!! $errors->first('type','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-6">
						<div class="mb-3">
						{{ Form::label('message', 'Message * ', ['class' => 'form-label']) }}
						{{ Form::textarea('message', Input::old('message'), array('class' => 'form-control','rows' => 5)) }}
						{!! $errors->first('message','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
			</div>
				<div class="row">
				<div class="col-md-3">
					<input type="button" id="trans_action" data-table="customers" value="Submit" class="btn btn-primary trans_action me-2">
				</div>
				<div class="col-md-9">
					<p id="trans_action_msg" class="trans_action_msg"></p>
				</div>
			</div>
			@endif
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
                        <th>Transaction Number</th>
                        <th>Type</th>
                        <th>On Date</th>
                        <th>Amount</th>
                        <th>Total Amount</th>
                        <th>Message</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($transactions) > 0)
                            @foreach($transactions as $key => $value)
                                <tr>
									<td>{{ $transactions->firstItem() + $key }}</td>
                                    <td>{{ $value->default_transaction_number }}</td>
                                    <td>{{ ucfirst($value->type) }}</td>
                                    <td>{{ $value->created_at }}</td>
                                    <td>{{ $value->amount }}</td>
                                    <td>{{ $value->total_amount }}</td>
                                    <td>{{ $value->message }}</td>
                                    <!--<td>
                                    {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/transactions', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                                <!-- {!! Form::button('Delete', array(
                                                        'type' => 'submit',
                                                        'class' => 'btn btn-danger btn-small',
                                                        'title' => 'Delete Employee',
                                                        'onclick'=>'return confirm("Confirm delete?")'
                                                )) !!}
                                            {!! Form::close() !!} -->
											<!--<a class="btn btn-sm btn-info" href="{{ URL::to('/transactions/' . $value->id) }}" title="View Employee"><i class=" menu-icon fa fa-lg fa-eye"></i></a>
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Employee" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>
                                        <a class="btn btn-sm btn-info" href="{{ URL::to('transactions/' . $value->id . '/edit') }}" title="Edit Employee"><i class=" menu-icon fa fa-lg fa-edit"></i></a>
										<a class="btn btn-sm btn-info" href="{{ URL::to('transaction/employee/' . $value->id) }}" title="View Transaction"><i class=" menu-icon fa fa-lg fa fa-exchange"></i></a>

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
                    <div class="pagination"> {!! $transactions->appends(['search' => Request::get('search')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Users</li>
				<li class="breadcrumb-item active" aria-current="page">Customers</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Customers List<span class="float_right">
					@if(request('search') != '')
					<a href="{{ url('/customers') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					@if(Auth::user()->can('viewMenu:ActionCustomer') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
					<a class="btn btn-success btn-sm" href="{{ URL::to('customers/create') }}"><i class="fa fa-plus" aria-hidden="true"></i> Add New</a>
					@endif
				  </span></div>
                <div class="card-body">
                  <div class="row">
            <div class="col-md-8"></div>
                <div class="col-md-4 float_right">
                  {!! Form::open(['method' => 'GET', 'url' => '/customers', 'class' => 'float-right', 'role' => 'search'])  !!}
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
        </div>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
                        <th>Company Name</th>
                        <th>Address</th>
                        <th>HST</th>
						<th>Income</th>
                        <th>Pending</th>
                       <!-- <th>Hourly Rate</th>
                        <th>Rate per load</th>-->
						
                        <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($customers) > 0)
                            @foreach($customers as $key => $value)
                                <tr>
								<td>{{ $customers->firstItem() + $key }}</td>
                                    <td>{{ $value->company_name }}</td>
                                    <td>{{ $value->street_line.' ,'.$value->city.' , '.$value->postal_code.' , '.$value->country }}</td>
                                    <td>{{ $value->customer_hst }}</td>
									<td>{{ $value->total_amount }}</td>
                                    <td>{{ $value->current_amount }}</td>
                                    <!--<td>{{ $value->hourly_rate }}</td>
                                    <td>{{ ( $value->rate_per_load == 'load') ? 'Rate per load ' : $value->rate_per_load }}</td>-->
                                    <td>
										
										 <!-- {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/customers', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}-->
											<a class="btn btn-sm btn-info" href="{{ URL::to('/customers/' . $value->id) }}" title="View Customer"><i class=" menu-icon fa fa-lg fa-eye"></i></a>
                                            <!--<button type="submit" class="btn btn-danger btn-sm" title="Delete Customer" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>-->
									@if(Auth::user()->can('viewMenu:ActionCustomer') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
                                        <a class="" href="{{ URL::to('customers/' . $value->id . '/edit') }}" title="Edit Customer"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
									@endif
									@if(Auth::user()->can('viewMenu:Transaction') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
										<a class="" href="{{ URL::to('transaction/customer/' . $value->id) }}" title="View Transaction"><button class="btn btn-sm  btn-secondary"><i class="fa fa-exchange" aria-hidden="true"></i></button></a>
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
                    <div class="pagination"> {!! $customers->appends(['search' => Request::get('search')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

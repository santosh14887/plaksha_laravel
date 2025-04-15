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
				<li class="breadcrumb-item active" aria-current="page">Fuel History</li>
			</ol>
		</nav>
		<div class="row">
            <div class="col-md-12">
                <div class="card">
				<div class="card">
                    <div class="card-header">Fuel History<span class="float_right">
				  @if(request('search') != '')
					<a href="{{ url('/fuel_histories') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
				  <a href="{{ url('/fuel_histories/create') }}" class="btn btn-success btn-sm" title="Add Fuel History">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>
						@endif
				  </span></div>
                <div class="card-body">
                  <div class="row">
					<div class="col-md-8"></div>
					<div class="col-md-4 float_right">
					  {!! Form::open(['method' => 'GET', 'url' => '/fuel_histories', 'class' => 'float-right', 'role' => 'search'])  !!}
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
                        <th>Vehicle Number</th>
                        <th>Total Km</th>
                        <th>Fue Quantity</th>
                        <th>Fue Receipt</th>
                        <th>Card Number</th>
						 <th>Fuel date</th>
                        <th>Source</th>
                        <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($fuel_histories) > 0)
                            @foreach($fuel_histories as $key => $value)
                                <tr>
                                    <td>{{ $fuel_histories->firstItem() + $key }}</td>
                                    <td><a href="{{ url('/vehicles/' . $value->vehicle_id) }}">{{ $value->vehicle_number }}</a></td>
                                    <td>{{ $value->total_km }}</td>
                                    <td>{{ $value->fuel_qty }}</td>
                                    <td>
									@if($value->source == 'Manual')
									@if(null != $value->fuel_receipt)
									  <img class="download_img" title="Click here to download" src="{{ asset('images/fuel_receipt/'.$value->fuel_receipt) }}" data-id="{{ asset('images/fuel_receipt/'.$value->fuel_receipt) }}"  />
									@endif
									@else  
									@if(null != $value->getTicket->fuel_receipt)
									  <img class="download_img" title="Click here to download" src="{{ asset('images/fuel_receipt/'.$value->getTicket->fuel_receipt) }}" data-id="{{ asset('images/fuel_receipt/'.$value->getTicket->fuel_receipt) }}"  />
									@endif	
									@endif
									</td>
                                    <td>{{ $value->fuel_card_number }}</td>
                                    <td>{{ $value->on_date }}</td>
                                    <td>{{ $value->source }}</td>
                                    <td>
									<a href="{{ url('/fuel_histories/' . $value->id) }}" title="View Fuel History"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
									@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
									@if($value->source == 'Manual')
									<!--<a href="{{ URL::to('fuel_histories/' . $value->id . '/edit') }}" title="Edit vehicle"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>-->
                                     {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/fuel_histories', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                                {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i>', array(
                                                        'type' => 'submit',
                                                        'class' => 'btn btn-danger btn-sm',
                                                        'title' => 'Delete History',
                                                        'onclick'=>'return confirm("Confirm delete?")'
                                                )) !!}
                                            {!! Form::close() !!}
									@endif
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
                    <div class="pagination"> {!! $fuel_histories->appends(['search' => Request::get('search')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
@endsection

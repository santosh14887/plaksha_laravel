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
				<li class="breadcrumb-item active" aria-current="page">Meter History</li>
			</ol>
		</nav>
		<div class="row">
            <div class="col-md-12">
                <div class="card">
				<div class="card">
                    <div class="card-header">Meter History<span class="float_right">
				  @if(request('search') != '')
					<a href="{{ url('/meter_histories') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
				  <a href="{{ url('/meter_histories/create') }}" class="btn btn-success btn-sm" title="Add Meter History">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>
						@endif
				  </span></div>
                <div class="card-body">
                  <div class="row">
					<div class="col-md-8"></div>
					<div class="col-md-4 float_right">
					  {!! Form::open(['method' => 'GET', 'url' => '/meter_histories', 'class' => 'float-right', 'role' => 'search'])  !!}
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
                        <th>Meter value</th>
						 <th>Meter date</th>
                        <th>Source</th>
                        <th>Comment</th>
                        <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($meter_histories) > 0)
                            @foreach($meter_histories as $key => $value)
                                <tr>
                                    <td>{{ $meter_histories->firstItem() + $key }}</td>
                                    <td><a href="{{ url('/vehicles/' . $value->vehicle_id) }}">{{ $value->vehicle_number }}</a></td>
                                    <td>{{ $value->total_km }}</td>
                                    <td>{{ $value->on_date }}</td>
                                    <td>{{ $value->source }}</td>
                                    <td>{{ $value->comment }}</td>
                                    <td>
									@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
									@if($value->source == 'Manual')
									<!--<a href="{{ url('/meter_histories/' . $value->id) }}" title="View Vehicle"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>-->
									<!--<a href="{{ URL::to('meter_histories/' . $value->id . '/edit') }}" title="Edit vehicle"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>-->
                                     {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/meter_histories', $value->id],
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
                    <div class="pagination"> {!! $meter_histories->appends(['search' => Request::get('search')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
@endsection

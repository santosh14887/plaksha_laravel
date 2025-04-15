@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Settings</li>
				<li class="breadcrumb-item active" aria-current="page">Fuel List</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Fuel List<span class="float_right">
				  @if(request('search') != '')
					<a href="{{ url('/fuels') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					@if( Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
					<a href="{{ URL::to('fuels/create') }}" class="btn btn-success btn-sm" title="Add New Permission">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Fuel Price
                    </a>
					@endif
				  </span></div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($fuels) > 0)
                            @foreach($fuels as $key => $value)
                                <tr>
								<td>{{ $fuels->firstItem() + $key }}</td>
                                    <td>{{ (null !== $value->on_date) ? date("d M Y", strtotime($value->on_date)) : '-' }}</td>
                                    <td>{{ (null !== $value->amount) ? $value->amount : '-' }}</td>
                                     <td>
									 @if( Auth::user()->can('viewMenu:ActionFuel') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
									 <a href="{{ URL::to('fuels/' . $value->id . '/edit') }}" title="Edit Fuel"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                    <!-- {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/fuels', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Fuel" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button> -->
                                        @endif

                                    </td> 
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="4">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
					<div class="pagination"> {!! $fuels->appends(['search' => Request::get('search')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

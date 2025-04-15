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
				<li class="breadcrumb-item active" aria-current="page">Dispatches</li>
				<li class="breadcrumb-item active" aria-current="page">Assign Dispatches</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Assigned Dispatch for ( {{ $dispatches->getCustomer->company_name.' '.$dispatches->start_time}})
				  @if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin') 
					<span class="float_right">
				 @php
				 if($dispatches->status != 'completed') { 
				@endphp
				@if(count($assign_dispatches) > 0)
					<a class="btn btn-success btn-sm" href="{{ URL::to('assign_dispatches/' . $dispatches->id . '/edit') }}" title="Edit Dispatch">
				<i class=" menu-icon fa fa-lg fa-edit">Update Assign Dispatch</i></a>
					@else
						<a class="btn btn-success btn-sm" href="{{ URL::to('assign_dispatches/create/'.$dispatches->id) }}"><i class="fa fa-plus" aria-hidden="true"></i> Assign Dispatch</a>
				@endif
				 @php
				 }
				 @endphp
						
					</span>
					@endif
					</div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
						<th><span class="all_selected_box_span" id="all_selected_box_span"><input type="checkbox" id="select_all_assign_dispatch" class="checkbox select_all_assign_dispatch" name="select_all_assign_dispatch" value="select_all_assign_dispatch"> Select All</span> S.No</th>
                        <th>User Type</th>
                        <th>User Name</th>
                        <th>Unit Number</th>
                        <th>Number of Vehicle</th>
                        <th>Status</th>
                        <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
					  @php
					  $count_start = 1;
					  $cancel_count_start = 0;
					  $dispatch_id = 0;
					  @endphp
                            @if(count($assign_dispatches) > 0)
                            @foreach($assign_dispatches as $key => $value)
                                <tr>
								<td>
								@php 
								$dispatch_id = $value->dispatch_id;
								// if(null == $value->getDispatchTicket && $value->status != 'cancelled') { 
								// ++$cancel_count_start;
								@endphp
									<!-- <input type="checkbox" id="{{$value->id}}" name="assign_dispatches_checkbox" class="checkbox assign_dispatches_checkbox" value="{{$value->id}}"> -->
								@php
								// } else{} 
								echo $count_start++
								@endphp
								</td>
                                    <td>{{ (null !== $value->user_type) ? ucfirst($value->user_type) : '-' }}</td>
                                    <td>{{ (null !== $value->user_name) ?  $value->user_name : ((null !== $value->getUser) ? $value->getUser->name : '-') }}</td>
									<td>{{ (null !== $value->vehicle_number) ?  $value->vehicle_number : ((null !== $value->getVehicle) ? $value->getVehicle->vehicle_number : '-') }}</td>
                                    <td>{{ $value->no_of_vehicles }}</td>
									<td>{{ ucfirst($value->status) }}</td>
									<td>
									@if($value->status == 'accepted' && $value->user_type == 'broker')
									<a class="btn btn-sm  btn-secondary" href="{{ URL::to('assigned_dispatche_broker_vehicles/' . $value->id) }}" title="Assigned Vehicles"><i class=" menu-icon fa fa-lg fa-list"> Assigned Vehicles</i></a>
									@endif
									</td>
                                    <!-- <td>
                                    {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/assign_dispatches', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-small" title="Delete Dispatch" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>
                                        <a class="btn btn-small btn-info" href="{{ URL::to('assign_dispatches/' . $value->id . '/edit') }}" title="Edit Dispatch"><i class=" menu-icon fa fa-lg fa-edit"></i></a>

                                    </td> -->
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="7">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
					<input type="hidden" id="assign_dispatches_dispatch_id" value="{{ $dispatch_id }}">
					<input type="hidden" id="cancel_count_start" value="{{ $cancel_count_start }}">
					@php
					// if($cancel_count_start > 0) {
					@endphp
					<!-- <div class="margin_top_one_per">
					<div class="form-group">
						<div class="col-md-6">
							<div class="mb-3">
							{{ Form::label('name', 'Message *', ['class' => 'form-label']) }}
							{{ Form::textarea('cancel_message', null, array('class' => 'form-control','id' => 'cancel_message','rows' => 5)) }}
							</div>
						</div>
					</div>
					 <a href="javascript:void(0)" class=" all_cancel_dispatches btn btn-primary me-2" title="Cancel Dispatches">Cancel Dispatch</a>
					 <span class="all_cancel_dispatches_err" style="color:red;"></span>
					 <span class="all_cancel_dispatches_success" style="color:green;"></span>
					 </div> -->
					 @php
					// }
					@endphp
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

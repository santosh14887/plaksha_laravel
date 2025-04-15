<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Dispatches</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/dispatches') }}">Dispatches</a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('assigned_dispatche_broker_vehicles/'.$assigned_dispatch->id) }}">Broker Vehicle</a></li>
				<li class="breadcrumb-item active" aria-current="page">Vehicle</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">@php
			$action_status = '';
			if (!empty($assign_dispatch_broker_vehicles)) {
				$action_status = 'Update';
			} else {
				$action_status = 'Create';
			}
			@endphp
			  {{$action_status}} Broker Vehicle for Dispatch ( {{ $assigned_dispatch->getDispatch->getCustomer->company_name.' '.$assigned_dispatch->getDispatch->start_time}})<br>
				  Broker Name :- {{$assigned_dispatch->getUser->name}}</div>
		<div class="card-body">
			<input type="hidden" name ="assign_dispatch_id" value= "{{ $assigned_dispatch->id }}" id="assign_dispatch_id">
			<!-- <p class="card-description">
			Basic form layout
			</p> -->
			@php
			if(null !== app('request')->input('id'))
			{
				echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
			}
			$passport_count_start = 0;
			$driver_name = array();
			$rows_id = array();
			$vehicle_number = array();
			$contact_number = array();
			$no_of_vehicles_provide = $assigned_dispatch->no_of_vehicles_provide;
				for($start = 0; $start < $no_of_vehicles_provide; $start++) {
					$driver_name[$start] = "";
					$contact_number[$start] = "";
					$vehicle_number[$start] = "";
					$rows_id[$start] = "";
				}
			@endphp	
			@if(null !== old('driver_name') && old('driver_name'))
				@foreach(old('driver_name') as $driver_name_key => $driver_name_val)
				@php
				$driver_name[$driver_name_key] = $driver_name_val;
				 $contact_number[$driver_name_key] = old('contact_number')[$driver_name_key];
				 $vehicle_number[$driver_name_key] = old('vehicle_number')[$driver_name_key];
				@endphp
				@endforeach
			@else
				@php
					if(isset($assign_dispatch_broker_vehicles->driver_name) && !empty($assign_dispatch_broker_vehicles->driver_name)){
					  $driver_name = json_decode($assign_dispatch_broker_vehicles->driver_name,true);
					}
					if(isset($assign_dispatch_broker_vehicles->vehicle_number) && !empty($assign_dispatch_broker_vehicles->vehicle_number)){
						  $vehicle_number=json_decode($assign_dispatch_broker_vehicles->vehicle_number,true);
					}
					 if(isset($assign_dispatch_broker_vehicles->contact_number) && !empty($assign_dispatch_broker_vehicles->contact_number)){
						  $contact_number=json_decode($assign_dispatch_broker_vehicles->contact_number,true);
					}
					if(isset($assign_dispatch_broker_vehicles->rows_id) && !empty($assign_dispatch_broker_vehicles->rows_id)){
						  $rows_id=json_decode($assign_dispatch_broker_vehicles->rows_id,true);
					}
				@endphp
			@endif
				@foreach($driver_name as $passport_key => $passport_val)
				@php
				$passport_qty  = $passport_count_start = $passport_key;
				$passport_count_show = $passport_key + 1;
				@endphp
				<input type="hidden" name="rows_id[]" value="{{$rows_id[$passport_key]}}">
				<div class="row seprate_div">
					<div class="col-sm-4">
						<div class="mb-3">
						{!! Form::label('driver_name', 'Driver Name *', ['class' => 'form-label']) !!}
						{!! Form::text('driver_name[]',$driver_name[$passport_key],['class' => 'form-control ','id' => 'driver_name'.$passport_key,'placeholder'=>'Driver Name']) !!}
						{!! $errors->first('driver_name.'.$passport_key,'<span class="help-inline text-danger">:message</span>') !!}	
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{!! Form::label('vehicle_number', 'Vehicle Number *', ['class' => 'form-label']) !!}
						{!! Form::text('vehicle_number[]',$vehicle_number[$passport_key],['class' => 'form-control ','id' => 'vehicle_number'.$passport_key,'placeholder'=>'Vehicle Number']) !!}
						{!! $errors->first('vehicle_number.'.$passport_key,'<span class="help-inline text-danger">:message</span>') !!}	
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{!! Form::label('contact_number', 'Contact Number *', ['class' => 'form-label']) !!}
						{!! Form::text('contact_number[]',$contact_number[$passport_key],['class' => 'form-control ','id' => 'contact_number'.$passport_key,'placeholder'=>'Vehicle Number']) !!}
						{!! $errors->first('contact_number.'.$passport_key,'<span class="help-inline text-danger">:message</span>') !!}	
						</div>
					</div>
				</div>
			@endforeach	
			<div class="form-group top_ten_per">
		  {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary btn-sm']) !!}
		  <a href="{{ url('assigned_dispatche_broker_vehicles/'.$assigned_dispatch->id) }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
		  </div>
		  <p id="passport_count_get" style="display:none;">{{$passport_count_start}}</p>
		</div>
	</div>
</div>
</div>
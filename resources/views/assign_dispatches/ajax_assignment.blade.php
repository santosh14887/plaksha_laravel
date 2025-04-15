	<div class="col-md-12">
	<a href="javascript:void('0')" id="remove_assignment_{{ $count }}" class="remove_schedule_btn remove_assignment"><i class=" menu-icon fa fa-lg fa-times-circle remove_schedule_btn"></i></a>
	<div class="main_div row">
				@php
				$user_type_arr = array('employee' => 'Employee','broker' => 'Broker')
				@endphp
				<div class="col-sm-2">
					<div class="mb-3">
					{!! Form::label('Name', 'User Type *', ['class' => 'form-label']) !!}
					<select id="user_type{{$count}}" name="user_type[]" data-id="{{$count}}" class="form-control user_type selectpicker" data-live-search="true">
						<option value="">Select User Type</option>
						@foreach ($user_type_arr as $key => $value)
							<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</select> 
					{!! $errors->first('user_type'.$count,'<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="mb-3">
					{!! Form::label('Name', 'User Name *', ['class' => 'form-label']) !!}
					<select id="user_id{{$count}}" name="user_id[]" data-id="{{$count}}" class="form-control user_id selectpicker" data-live-search="true">
						<option value="">Select User Name</option>
					</select> 
					{!! $errors->first('user_id.'.$count,'<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-3" id="unit_number_div{{$count}}">
					<div class="mb-3">
					{!! Form::label('Name', 'Unit Number ', ['class' => 'form-label']) !!}
					<select id="vehicle_id{{$count}}" name="vehicle_id[]" data-id="{{$count}}" class="form-control vehicle_id selectpicker" data-live-search="true">
						<option value="">Select Unit Number</option>
						 <!-- @foreach ($vehicle as $key => $value)
							<option value="{{ $key }}">{{ $value }}</option>
						@endforeach -->
					</select>  
					
					{!! $errors->first('vehicle_id'.$count,'<span class="help-inline text-danger">:message</span>') !!}
					<input type="hidden" name="vehicle_number[]" id="vehicle_number{{$count}}">
					</div>
				</div>
				<div class="col-sm-2">
					<div class="mb-3">
					{{ Form::label('unit_assigned', 'Unit Assigned *', ['class' => 'form-label']) }}
					{{ Form::text('no_of_vehicles[]','', array('class' => 'form-control','id' => 'no_of_vehicles'.$count)) }}
					{!! $errors->first('no_of_vehicles'.$count,'<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				@php
				$user_status_arr = array('pending' => 'Pending','accepted' => 'Accepted')
				@endphp
				<div class="col-sm-2">
					<div class="mb-3">
					{!! Form::label('Name', 'Status', ['class' => 'form-label']) !!}
					<select id="status{{$count}}" name="status[]"  data-id="{{$count}}" class="form-control select_height">
						<option value="">Select Status</option>
						@foreach ($user_status_arr as $key => $value)
							<option value="{{ $key }}" {{ 'pending' == $key ? 'selected' : ''}}>{{ $value }}</option>
						@endforeach
					</select> 
					{!! $errors->first('status.'.$count,'<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				
	</div>	
	</div>	
@extends('layouts.after_login')

@section('content')
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-body">
			<h4 class="card-title">{{ __('Edit Emplyee') }}<span class="float_right"><a href="{{ URL::to('employees') }}">View All Employee</a></span></h4>
			<!-- <p class="card-description">
			Basic form layout
			</p> -->
			
			{{ Form::model($employees, array('route' => array('employees.update', $employees->id), 'method' => 'PUT')) }}
			<div class="form-group row">
				<div class="col-md-3">
					{{ Form::label('name', 'First Name *') }}
					{{ Form::text('first_name', Input::old('first_name'), array('class' => 'form-control')) }}
					{!! $errors->first('first_name','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				<div class="col-md-3">
					{{ Form::label('name', 'Last Name *') }}
					{{ Form::text('last_name', Input::old('last_name'), array('class' => 'form-control')) }}
					{!! $errors->first('last_name','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				<div class="col-md-3">
					{{ Form::label('name', 'Email *') }}
					{{ Form::text('email', Input::old('email'), array('class' => 'form-control')) }}
					{!! $errors->first('email','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				<div class="col-md-3">
					{{ Form::label('name', 'Password') }}
					{{ Form::text('password_string', Input::old('password_string'), array('class' => 'form-control')) }}
					{!! $errors->first('password_string','<span class="help-inline text-danger">:message</span>') !!}
				</div>
			</div>
			<div class="form-group row">
			<div class="col-md-3">
					{{ Form::label('name', 'Phone *') }}
					{{ Form::text('phone', Input::old('phone'), array('class' => 'form-control')) }}
					{!! $errors->first('phone','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				<div class="col-md-2">
					{{ Form::label('name', 'Zip Code *') }}
					{{ Form::text('zip_code', Input::old('zip_code'), array('class' => 'form-control')) }}
					{!! $errors->first('zip_code','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				<div class="col-md-3">
					{{ Form::label('name', 'HST *') }}
					{{ Form::text('hst', Input::old('hst'), array('class' => 'form-control')) }}
					{!! $errors->first('hst','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				<div class="col-md-3 resize2_widthinn">
					<div class="form-group">
					{!! Form::label('Name', 'Unit Number ') !!}
					<select id="vehicle_id" name="vehicle_id" class="form-control selectpicker" data-live-search="true">
						<option value="">Select Unit Number</option>
						@foreach ($vehicle as $key => $value)
						@php
						$unit_active = '';
						if($employees->vehicle_id == $key) {
								$unit_active = 'selected';
							} else if(Input::old('vehicle_id') == $key) {
							$unit_active = 'selected';
						}
						@endphp
							<option value="{{ $key }}" {{ $unit_active }}>{{ $value }}</option>
						@endforeach
					</select>  
					</div>
					{!! $errors->first('vehicle_id','<span class="help-inline text-danger">:message</span>') !!}
				</div>
			</div>
			<div class="form-group row">
				<div class="col-md-3">
					{{ Form::label('name', 'License Number') }}
					{{ Form::text('license_number', Input::old('license_number'), array('class' => 'form-control')) }}
					{!! $errors->first('license_number','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				<div class="col-md-3">
					{{ Form::label('name', 'Company / Corporation Name') }}
					{{ Form::text('company_corporation_name', Input::old('company_corporation_name'), array('class' => 'form-control')) }}
					{!! $errors->first('company_corporation_name','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				@php
				$status_arr = array('active' => 'Active','inactive' => 'Inactive')
				@endphp
				<div class="col-md-2 resize2_widthinn">
					<div class="form-group">
					{!! Form::label('status', 'Status *') !!}
					<select id="status" name="status" class="form-control selectpicker" data-live-search="true">
						<option value="">Select Status</option>
						@foreach ($status_arr as $key => $value)
						@php
							$status_active = '';
							if($employees->status == $key) {
								$status_active = 'selected';
							} else if(Input::old('status') == $key) {
								$status_active = 'selected';
							}
						@endphp
							<option value="{{ $key }}" {{ $status_active }}>{{ $value }}</option>
						@endforeach
					</select> 
					</div>
					{!! $errors->first('status','<span class="help-inline text-danger">:message</span>') !!}
				</div>
			</div>
			<div class="form-group row">
				<div class="col-md-6">
					{{ Form::label('name', 'Hourly Rate ( Format : 1.00 )') }}
					{{ Form::text('hourly_rate', null, array('class' => 'form-control')) }}
					{!! $errors->first('hourly_rate','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				<!--<div class="col-md-6">
					{{ Form::label('name', 'Load Per ( Format : 1.00 )') }}
					{{ Form::text('load_per', null, array('class' => 'form-control')) }}
					{!! $errors->first('load_per','<span class="help-inline text-danger">:message</span>') !!}
				</div>-->
			</div>
			<div class="form-group form-group shadow-textarea">
				{{ Form::label('name', 'Address *') }}
				{{ Form::textarea('address', Input::old('address'), array('class' => '')) }}
				{!! $errors->first('address','<span class="help-inline text-danger">:message</span>') !!}
			</div>
			{{ Form::submit('Edit Employee!', array('class' => 'btn btn-primary me-2')) }}
			<a class="btn btn-back btn-sm" href="{{ url('employees') }}" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> Cancel</a> 
			{{ Form::close() }}
		</div>
		</div>
	</div>
</div>
@endsection

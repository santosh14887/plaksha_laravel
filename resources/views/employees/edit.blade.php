@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Users</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/employees') }}">Employees</a></li>
				<li class="breadcrumb-item active" aria-current="page">Edit Employee</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">Edit Employee</div>
		<div class="card-body">
		
			{{ Form::model($employees, array('route' => array('employees.update', $employees->id), 'method' => 'PUT')) }}
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'First Name *', ['class' => 'form-label']) }}
					{{ Form::text('first_name', Input::old('first_name'), array('class' => 'form-control')) }}
					{!! $errors->first('first_name','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Last Name *', ['class' => 'form-label']) }}
					{{ Form::text('last_name', Input::old('last_name'), array('class' => 'form-control')) }}
					{!! $errors->first('last_name','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Email *', ['class' => 'form-label']) }}
					{{ Form::text('email', Input::old('email'), array('class' => 'form-control')) }}
					{!! $errors->first('email','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				
			</div>
			<div class="row">
			@php
			$pwd_str = '';
			if(null != Input::old('password')) {
				$pwd_str = Input::old('password');
			} else {
				$pwd_str = $employees->password_string;
			}
			@endphp
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Password', ['class' => 'form-label']) }}
					{{ Form::text('password', $pwd_str, array('class' => 'form-control')) }}
					{!! $errors->first('password','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					@php
					$country_code = array('+1' => '+1','+91' => '+91')
					@endphp
					<div class="mb-3">
						{{ Form::label('name', 'Phone *', ['class' => 'form-label']) }}
						<div class="row">
							<div class="col-sm-4 padding_right_zero">
								<select id="country_code" name="country_code" class="form-control selectpicker">
									@foreach ($country_code as $key => $value)
									@php
									$country_status_active = '';
									if($employees->country_code == $key) {
										$country_status_active = 'selected';
									} else if(Input::old('country_code') == $key) {
										$country_status_active = 'selected';
									} else if($key == '+1') {
										$country_status_active = 'selected';
									}
									@endphp
										<option value="{{ $key }}" {{ $country_status_active }}>{{ $value }}</option>
									@endforeach
								</select>
							</div>
							<div class="col-sm-8 padding_left_zero">
							{{ Form::text('phone', Input::old('phone'), array('class' => 'form-control')) }}
							</div>
						</div>
						{!! $errors->first('phone','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'HST *', ['class' => 'form-label']) }}
					{{ Form::text('hst', Input::old('hst'), array('class' => 'form-control')) }}
					{!! $errors->first('hst','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('Name', 'Unit Number ', ['class' => 'form-label']) !!}
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
					{!! $errors->first('vehicle_id','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Hourly Rate ( Format : 1.00 )', ['class' => 'form-label']) }}
					{{ Form::text('hourly_rate', Input::old('hourly_rate'), array('class' => 'form-control')) }}
					{!! $errors->first('hourly_rate','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'License Number', ['class' => 'form-label']) }}
					{{ Form::text('license_number', Input::old('license_number'), array('class' => 'form-control')) }}
					{!! $errors->first('license_number','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Company / Corporation Name *', ['class' => 'form-label']) }}
					{{ Form::text('company_corporation_name', Input::old('company_corporation_name'), array('class' => 'form-control')) }}
					{!! $errors->first('company_corporation_name','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				@php
				$status_arr = array('active' => 'Active','inactive' => 'Inactive')
				@endphp
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('status', 'Status *', ['class' => 'form-label']) !!}
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
					{!! $errors->first('status','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="mb-3">
					{{ Form::label('name', 'Street / Line *', ['class' => 'form-label']) }}
					{{ Form::text('street_line', Input::old('street_line'), array('class' => 'form-control')) }}
					{!! $errors->first('street_line','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-6">
					<div class="mb-3">
					{{ Form::label('name', 'City *', ['class' => 'form-label']) }}
					{{ Form::text('city', Input::old('city'), array('class' => 'form-control')) }}
					{!! $errors->first('city','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Country *', ['class' => 'form-label']) }}
					{{ Form::text('country', 'Canada', array('class' => 'form-control')) }}
					{!! $errors->first('country','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Country Devision Code *', ['class' => 'form-label']) }}
					{{ Form::text('country_devision_code', 'CA', array('class' => 'form-control')) }}
					{!! $errors->first('country_devision_code','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Zip Code *', ['class' => 'form-label']) }}
					{{ Form::text('zip_code', Input::old('zip_code'), array('class' => 'form-control')) }}
					{!! $errors->first('zip_code','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<!--<div class="row">
			<div class="col-sm-6">
						<div class="mb-3">
						{{ Form::label('name', 'address *', ['class' => 'form-label']) }}
						{{ Form::textarea('address', Input::old('address'), array('class' => 'form-control','rows' => 5)) }}
						{!! $errors->first('address','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
			</div>-->
			<div class="form-group">
			{{ Form::submit('Edit Employee!', array('class' => 'btn btn-primary me-2')) }}
			<a href="{{ url('employees') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
			{{ Form::close() }}
			</div>
		</div>
		</div>
	</div>
</div>
@endsection

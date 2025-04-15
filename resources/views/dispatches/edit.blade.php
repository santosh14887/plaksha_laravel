@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Dispatches</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/dispatches') }}">Dispatches</a></li>
				<li class="breadcrumb-item active" aria-current="page">Edit Order</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">Edit Order</div>
		<div class="card-body">
			
			{{ Form::model($dispatches, array('route' => array('dispatches.update', $dispatches->id), 'method' => 'PUT')) }}
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('customer_name', 'Customer Name *') !!}
					<select id="customer_id" name="customer_id" class="form-control selectpicker" data-live-search="true">
						<option value="">Select Customer</option>
						@foreach ($customer as $key => $value)
						@php
						$value_selected = '';
						if($dispatches->customer_id == $key) {
							$value_selected = 'selected';
						} else if(Input::old('customer_id') == $key) {
							$value_selected = 'selected';
						}
						@endphp
							<option value="{{ $key }}" {{ $value_selected }}>{{ $value }}</option>
						@endforeach
					</select> 
					{!! $errors->first('customer_id','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
				    @php
						 if(Input::old('start_time') != '') {
							$start_time = Input::old('start_time');
						} else {
							$start_time = date("m/d/Y h:i:s a", strtotime($dispatches->start_time));
						}
				   @endphp
					{!! Form::label('start_time', 'Start Time *', ['class' => 'control-label']) !!}
					  <div class="cale">
						 <span class="iconcalender"></span>
						 {!! Form::text('start_time',$start_time, array('class' => 'form-control')) !!}
					  </div>
					  {!! $errors->first('start_time','<span class="help-inline text-danger">:message</span>') !!}
				   </div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('start_location', 'Start Location *') }}
					{{ Form::text('start_location', Input::old('start_location'), array('class' => 'form-control')) }}
					{!! $errors->first('start_location','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
			<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('dump_location', 'Dump Location *') }}
					{{ Form::text('dump_location', Input::old('dump_location'), array('class' => 'form-control')) }}
					{!! $errors->first('dump_location','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				@php
				$job_type_arr = array('hourly' => 'Hourly','load' => 'Rate per load');
				@endphp
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('job_type', 'Job Type *') !!}
					<select id="job_type" name="job_type" class="form-control job_type selectpicker" data-live-search="true">
						<option value="">Select Job Type</option>
						@foreach ($job_type_arr as $key => $value)
						@php
						$value_selected = '';
						if($dispatches->job_type == $key) {
							$value_selected = 'selected';
						} else if(Input::old('job_type') == $key) {
							$value_selected = 'selected';
						}
						@endphp
							<option value="{{ $key }}" {{ $value_selected }}>{{ $value }}</option>
						@endforeach
					</select>
					{!! $errors->first('job_type','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4 employee_rate_div">
					<div class="mb-3">
					{{ Form::label('name', 'Employee Rate') }}
					{{ Form::text('employee_rate', Input::old('employee_rate'), array('class' => 'form-control','id' => 'employee_rate')) }}
					{!! $errors->first('employee_rate','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Job Rate *') }}
					{{ Form::text('job_rate', Input::old('job_rate'), array('class' => 'form-control')) }}
					{!! $errors->first('job_rate','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('required_unit', 'Required Units ') }}
					{{ Form::text('required_unit', Input::old('required_unit'), array('class' => 'form-control')) }}
					{!! $errors->first('required_unit','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
			<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('supervisor_name', 'Supervisor Name ') }}
					{{ Form::text('supervisor_name', Input::old('supervisor_name'), array('class' => 'form-control')) }}
					{!! $errors->first('supervisor_name','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('supervisor_contact', 'Supervisor Contact ') }}
					{{ Form::text('supervisor_contact', Input::old('supervisor_contact'), array('class' => 'form-control')) }}
					{!! $errors->first('supervisor_contact','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="mb-3">
					{{ Form::label('name', 'Comment ', ['class' => 'form-label']) }}
					{{ Form::textarea('comment', Input::old('comment'), array('class' => 'form-control','rows' => 5)) }}
					{!! $errors->first('comment','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="form-group">
			{{ Form::submit('Edit Order!', array('class' => 'btn btn-primary me-2')) }}
			<a href="{{ url('dispatches') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
			{{ Form::close() }}
			</div>
		</div>
		</div>
	</div>
</div>
@endsection

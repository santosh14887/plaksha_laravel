@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Dispatches</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/dispatches') }}">Dispatches</a></li>
				<li class="breadcrumb-item active" aria-current="page">Create Order</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">Create Order</div>
		<div class="card-body">
			
			{{ Form::open(array('url' => 'dispatches')) }}
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('Name', 'Customer Name *', ['class' => 'form-label']) !!}
					<select id="customer_id" name="customer_id" class="form-control selectpicker" data-live-search="true">
						<option value="">Select Customer</option>
						@foreach ($customer as $key => $value)
						@php
						$selected_value = '';
						if(Input::old('customer_id') == $key) {
							$selected_value = 'selected';
						}
						@endphp
							<option value="{{ $key }}" {{ $selected_value }}>{{ $value }}</option>
						@endforeach
					</select> 
					{!! $errors->first('customer_id','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					  {!! Form::label('start_time', 'Start Time *', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
					  <div class="cale">
						 <span class="iconcalender"></span>
						 {!! Form::text('start_time',Input::old('start_time'),['class' => 'form-control','id'=>'start_time']) !!}
					  </div>
					  {!! $errors->first('start_time','<span class="help-inline text-danger">:message</span>') !!}
				   </div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Start Location *', ['class' => 'form-label']) }}
					{{ Form::text('start_location', Input::old('start_location'), array('class' => 'form-control')) }}
					{!! $errors->first('start_location','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Dump Location *', ['class' => 'form-label']) }}
					{{ Form::text('dump_location', Input::old('dump_location'), array('class' => 'form-control')) }}
					{!! $errors->first('dump_location','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				@php
				$job_type_arr = array('hourly' => 'Hourly','load' => 'Rate per load')
				@endphp
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('Name', 'Job Type *', ['class' => 'form-label']) !!}
					<select id="job_type" name="job_type" class="form-control job_type selectpicker" data-live-search="true">
						<option value="">Select Job Type</option>
						@foreach ($job_type_arr as $key => $value)
						@php
						$selected_value = '';
						if(Input::old('job_type') == $key) {
							$selected_value = 'selected';
						}
						@endphp
							<option value="{{ $key }}" {{ $selected_value }}>{{ $value }}</option>
						@endforeach
					</select> 
					{!! $errors->first('job_type','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4 employee_rate_div">
					<div class="mb-3">
					{{ Form::label('name', 'Employee Rate', ['class' => 'form-label']) }}
					{{ Form::text('employee_rate', Input::old('employee_rate'), array('class' => 'form-control','id' => 'employee_rate')) }}
					{!! $errors->first('employee_rate','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Job Rate *', ['class' => 'form-label']) }}
					{{ Form::text('job_rate', Input::old('job_rate'), array('class' => 'form-control')) }}
					{!! $errors->first('job_rate','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Required Units *', ['class' => 'form-label']) }}
					{{ Form::text('required_unit', Input::old('required_unit'), array('class' => 'form-control')) }}
					{!! $errors->first('required_unit','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Supervisor Name ', ['class' => 'form-label']) }}
					{{ Form::text('supervisor_name', Input::old('supervisor_name'), array('class' => 'form-control')) }}
					{!! $errors->first('supervisor_name','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Supervisor Contact ', ['class' => 'form-label']) }}
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
			{{ Form::submit('Add Order!', array('class' => 'btn btn-primary me-2')) }}
			<a href="{{ url('dispatches') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
			{{ Form::close() }}
			</div>
		</div>
		</div>
	</div>
</div>
@endsection

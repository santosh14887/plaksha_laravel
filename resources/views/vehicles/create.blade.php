@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/vehicles') }}">Vehicles Units</a></li>
				<li class="breadcrumb-item active" aria-current="page">Add Vehicle</li>
			</ol>
		</nav>
	<div class="row">
	<div class="col-md-12">
                <div class="card">
                    <div class="card-header">Add New Vehicle</div>
                    <div class="card-body">
			{{ Form::open(array('url' => 'vehicles')) }}
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Vehicle Number *', ['class' => 'form-label']) }}
					{{ Form::text('vehicle_number', Input::old('vehicle_number'), array('class' => 'form-control')) }}
					{!! $errors->first('vehicle_number','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Service Due Every KM *', ['class' => 'form-label']) }}
					{{ Form::text('service_due_every_km', Input::old('service_due_every_km'), array('class' => 'form-control')) }}
					{!! $errors->first('service_due_every_km','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('air_filter_days', 'Air Filter Days *', ['class' => 'form-label']) !!}
					{{ Form::text('air_filter_after_days', Input::old('air_filter_after_days'), array('class' => 'form-control')) }}
					{!! $errors->first('air_filter_after_days','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Total Km') }}
					{{ Form::text('total_km', Input::old('total_km'), array('class' => 'form-control')) }}
					{!! $errors->first('total_km','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					  {!! Form::label('last_air_filter_date', 'Last Air Filter Date',['class' => 'form-label']) !!}
					  <div class="cale">
						 <span class="iconcalender"></span>
						 {!! Form::text('last_air_filter_date',Input::old('last_air_filter_date'),['class' => 'form-control datepicker','id'=>'last_air_filter_date']) !!}
					  </div>
					  {!! $errors->first('last_air_filter_date','<span class="help-inline text-danger">:message</span>') !!}
				   </div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Licence Plate', ['class' => 'form-label']) }}
					{{ Form::text('licence_plate', Input::old('licence_plate'), array('class' => 'form-control')) }}
					{!! $errors->first('licence_plate','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Vin Number', ['class' => 'form-label']) }}
					{{ Form::text('vin_number', Input::old('vin_number'), array('class' => 'form-control')) }}
					{!! $errors->first('vin_number','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Annual Safty Renewal', ['class' => 'form-label']) }}
					{{ Form::text('annual_safty_renewal', Input::old('annual_safty_renewal'), array('class' => 'form-control')) }}
					{!! $errors->first('annual_safty_renewal','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Licence Plate Sticker', ['class' => 'form-label']) }}
					{{ Form::text('licence_plate_sticker', Input::old('licence_plate_sticker'), array('class' => 'form-control')) }}
					{!! $errors->first('licence_plate_sticker','<span class="help-inline text-danger">:message</span>') !!}
				</div>
			</div>
			<div class="form-group">
			{{ Form::submit('Add Vehicle!', array('class' => 'btn btn-primary me-2')) }}
			<a href="{{ url('/vehicles') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
			</div>			
			{{ Form::close() }}
		</div>
		</div>
	</div>
</div>
</div>
@endsection

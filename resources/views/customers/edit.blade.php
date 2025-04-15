@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Users</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/customers') }}">Customers</a></li>
				<li class="breadcrumb-item active" aria-current="page">Edit Customer</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">Edit Customer</div>
		<div class="card-body">
			
			{{ Form::model($customers, array('route' => array('customers.update', $customers->id), 'method' => 'PUT')) }}
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Company Name *', ['class' => 'form-label']) }}
					{{ Form::text('company_name', Input::old('company_name'), array('class' => 'form-control')) }}
					{!! $errors->first('company_name','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'HST *', ['class' => 'form-label']) }}
					{{ Form::text('customer_hst', Input::old('customer_hst'), array('class' => 'form-control')) }}
					{!! $errors->first('customer_hst','<span class="help-inline text-danger">:message</span>') !!}
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
					{{ Form::label('name', 'Postal Code *', ['class' => 'form-label']) }}
					{{ Form::text('postal_code', Input::old('postal_code'), array('class' => 'form-control')) }}
					{!! $errors->first('postal_code','<span class="help-inline text-danger">:message</span>') !!}
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
			{{ Form::submit('Edit Customer!', array('class' => 'btn btn-primary me-2')) }}
			<a href="{{ url('customers') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
			{{ Form::close() }}
			</div>
		</div>
		</div>
	</div>
</div>
@endsection

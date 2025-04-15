@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Vehicles</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/vehicles') }}">Vehicles Units</a></li>
				<li class="breadcrumb-item active" aria-current="page">Edit Vehicle</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">Edit Vehicle</div>
			<div class="card-body">
			{{ Form::model($vehicles, array('route' => array('vehicles.update', $vehicles->id), 'method' => 'PUT')) }}
			<div class="example">
              <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="imp-line-tab" data-bs-toggle="tab" href="#line-imp" role="tab" aria-controls="line-imp" aria-selected="true">Important</a>
                </li>
				@php
				$vehicle_desc = $vehicles->vehicle_desc;
				@endphp
				@if($vehicle_desc != '' && $vehicle_desc != null)
                <li class="nav-item">
                  <a class="nav-link" id="desc-line-tab" data-bs-toggle="tab" href="#line-desc" role="tab" aria-controls="line-desc" aria-selected="false">Description</a>
                </li>
				@endif
              </ul>
              <div class="tab-content mt-3" id="lineTabContent">
			  
                <div class="tab-pane fade show active" id="line-imp" role="tabpanel" aria-labelledby="imp-line-tab">
				  @include('vehicles.important_fields')
                </div>
				@if($vehicle_desc != '' && $vehicle_desc != null)
                <div class="tab-pane fade" id="line-desc" role="tabpanel" aria-labelledby="desc-line-tab">
                  @include('vehicles.description_fields')
                </div>
				@endif
              </div>
            </div>
			
			<div class="form-group">
			{{ Form::submit('Edit Vehicle!', array('class' => 'btn btn-primary me-2')) }}
			<a href="{{ url('/vehicles') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
			</div>
			{{ Form::close() }}
		</div>
		</div>
	</div>
</div>
@endsection

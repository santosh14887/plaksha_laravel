<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Vehicles</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/fuel_histories') }}">Fuel History</a></li>
				<li class="breadcrumb-item active" aria-current="page">History</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">@if (isset($fuel_histories) && null !== $fuel_histories && !empty($fuel_histories))
			  Update Fuel History for ( {{ $fuel_histories->vehicle_number}})
			  @else
				  Add Fuel History
			  @endif</div>
		<div class="card-body">
			@php
			$redirect_from_vehicle_page = '';
			$on_date_val = $vehicle_id_val = $starting_km_val = $fuel_qty_val = $comment_val = $fuel_receipt_val = $fuel_card_number_val = '';
			if(null !== app('request')->input('id'))
			{
				echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
			} 
			if(null !== app('request')->input('vehicle_id'))
			{
				$redirect_from_vehicle_page = app('request')->input('vehicle_id');
			} 
			$on_date_val = (null !== old('on_date')) ? old('on_date') : '';
			$vehicle_id_val = (null !== old('vehicle_id')) ? old('vehicle_id') : '';
			$fuel_receipt_val = (null !== old('fuel_receipt')) ? old('fuel_receipt') : '';
			$starting_km_val = (null !== old('starting_km')) ? old('starting_km') : '';
			$fuel_qty_val = (null !== old('fuel_qty')) ? old('fuel_qty') : '';
			$fuel_card_number_val = (null !== old('fuel_card_number')) ? old('fuel_card_number') : '';
			$comment_val = (null !== old('comment')) ? old('comment') : '';
			if(isset($fuel_histories)){
				$on_date_val = (isset($fuel_histories->on_date)) ? $fuel_histories->on_date : '';
				$vehicle_id_val = (isset($fuel_histories->vehicle_id)) ? $fuel_histories->vehicle_id : '';
				$starting_km_val = (isset($fuel_histories->starting_km)) ? $fuel_histories->starting_km : '';
				$fuel_qty_val = (isset($fuel_histories->fuel_qty)) ? $fuel_histories->fuel_qty : '';
				$fuel_card_number_val = (isset($fuel_histories->fuel_card_number)) ? $fuel_histories->fuel_card_number : '';
				$comment_val = (isset($fuel_histories->comment)) ? $fuel_histories->comment : '';
				$fuel_receipt_val = (isset($fuel_histories->fuel_receipt)) ? $fuel_histories->fuel_receipt : '';
			}
			if($on_date_val != '') {
				$on_date_val = date("Y-m-d", strtotime($on_date_val));
			}
			if($vehicle_id_val != '') {
				$redirect_from_vehicle_page = $vehicle_id_val;
			}
			@endphp
				<div class="row">
					<div class="col-sm-4">
						<div class="mb-3">
					  {!! Form::label('on_date', 'Date *', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
					  <div class="input-group flatpickr" id="fuel-flatpickr-date">
					  <input type="text" class="form-control" name="on_date" value="{{ $on_date_val }}" placeholder="Select date" data-input="">
						<span class="input-group-text input-group-addon" data-toggle=""><i data-feather="calendar"></i></span>
					</div>
					  
					  {!! $errors->first('on_date','<span class="help-inline text-danger">:message</span>') !!}
					   </div>
					</div>
					<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('Name', 'Vehicle/Unit Number * ', ['class' => 'form-label']) !!}
					<select id="vehicle_id" name="vehicle_id" class="form-control selectpicker" data-live-search="true">
						<option value="">Select Number</option>
						@foreach ($vehicle as $key => $value)
						@php
						$unit_active = '';
						 if($vehicle_id_val == $key || $redirect_from_vehicle_page == $key) {
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
						{{ Form::label('fuel_qty', 'Fuel Quantity *', ['class' => 'form-label']) }}
						{{ Form::text('fuel_qty', $fuel_qty_val, array('class' => 'form-control','id' => 'fuel_qty')) }}
						{!! $errors->first('fuel_qty','<span class="help-inline text-danger">:message</span>') !!}
						</div>
				</div>
				<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('fuel_card_number', 'Fuel Card Number', ['class' => 'form-label']) }}
						{{ Form::text('fuel_card_number', $fuel_card_number_val, array('class' => 'form-control','id' => 'fuel_card_number')) }}
						{!! $errors->first('fuel_card_number','<span class="help-inline text-danger">:message</span>') !!}
						</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Fuel Receipt', ['class' => 'form-label']) }}
					 {{ Form::file('fuel_receipt', Input::old('fuel_receipt'),  ["class"=>"required form-control","multiple"=>false,'id' => 'fuel_receipt']) }}
					{!! $errors->first('fuel_receipt','<span class="help-inline text-danger">:message</span>') !!}
					@php
					$path = public_path()."/images/fuel_receipt/".$fuel_receipt_val;
					@endphp
					@if(isset($fuel_receipt_val) && $fuel_receipt_val != '' && file_exists($path))
					<img src="{{ asset('images/fuel_receipt/'.$fuel_receipt_val) }}" style="width:100px;" />
					@endif
					</div>
				</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('starting_km', 'Starting Km / Meter Value*', ['class' => 'form-label']) }}
						{{ Form::text('starting_km', $starting_km_val, array('class' => 'form-control','id' => 'starting_km')) }}
						{!! $errors->first('starting_km','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					<label for="comment" class="form-label">Comment</label>
					{{ Form::textarea('comment', $comment_val, array('class' => 'form-control','rows' => 5)) }}
					{!! $errors->first('comment','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			<div class="form-group">
		  {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary btn-sm']) !!}
		   <a href="{{ url('/fuel_histories') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
		  </div>
		</div>
		</div>
	</div>
</div>
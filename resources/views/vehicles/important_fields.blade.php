<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Vehicle Number *' ,['class' => 'form-label']) }}
					{{ Form::text('vehicle_number', Input::old('vehicle_number'), array('class' => 'form-control')) }}
					{!! $errors->first('vehicle_number','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Service Due Every KM *',['class' => 'form-label']) }}
					{{ Form::text('service_due_every_km', Input::old('service_due_every_km'), array('class' => 'form-control')) }}
					{!! $errors->first('service_due_every_km','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Total Km',['class' => 'form-label']) }}
					{{ Form::text('total_km', Input::old('total_km'), array('class' => 'form-control')) }}
					{!! $errors->first('total_km','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('air_filter_days', 'Air Filter Days *',['class' => 'form-label']) !!}
					{{ Form::text('air_filter_after_days', Input::old('air_filter_after_days'), array('class' => 'form-control')) }}
					{!! $errors->first('air_filter_after_days','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
				    @php
					$last_air_filter_date = '';
						 if(Input::old('last_air_filter_date') != '') {
							$last_air_filter_date = Input::old('last_air_filter_date');
						} else {
							if(null!= $vehicles->last_air_filter_date) {
								$last_air_filter_date = date("m/d/Y h:i:s a", strtotime($vehicles->last_air_filter_date));
							}
							
						}
				   @endphp
					{!! Form::label('last_air_filter_date', 'Last Air Filter Date', ['class' => 'form-label']) !!}
					  <div class="cale">
						 <span class="iconcalender"></span>
						 {!! Form::text('last_air_filter_date',$last_air_filter_date, array('class' => 'form-control')) !!}
					  </div>
					  {!! $errors->first('last_air_filter_date','<span class="help-inline text-danger">:message</span>') !!}
				   </div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Licence Plate',['class' => 'form-label']) }}
					{{ Form::text('licence_plate', Input::old('licence_plate'), array('class' => 'form-control')) }}
					{!! $errors->first('licence_plate','<span class="help-inline text-danger">:message</span>') !!}
				</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Vin Number',['class' => 'form-label']) }}
					{{ Form::text('vin_number', Input::old('vin_number'), array('class' => 'form-control')) }}
					{!! $errors->first('vin_number','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Annual Safty Renewal',['class' => 'form-label']) }}
					{{ Form::text('annual_safty_renewal', Input::old('annual_safty_renewal'), array('class' => 'form-control')) }}
					{!! $errors->first('annual_safty_renewal','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{{ Form::label('name', 'Licence Plate Sticker',['class' => 'form-label']) }}
					{{ Form::text('licence_plate_sticker', Input::old('licence_plate_sticker'), array('class' => 'form-control')) }}
					{!! $errors->first('licence_plate_sticker','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
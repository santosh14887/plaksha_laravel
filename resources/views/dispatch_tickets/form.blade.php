<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Dispatches</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/dispatch_tickets') }}">Dispatch Tickets</a></li>
				<li class="breadcrumb-item active" aria-current="page">Ticket</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">
		@php
			$action_status = '';
			$dispatch_ticket_id = '';
			if (!empty($dispatch_tickets)) {
				$action_status = 'Update';
				$dispatch_ticket_id = $dispatch_tickets->id;
			} else {
				$action_status = 'Create';
			}
			@endphp
			  {{$action_status}} Dispatch Ticket</div>
		<div class="card-body">
		
			@php
			$dispatch_id_val = $status_val = $shift_type_val = $user_type_val = $user_id_val = $broker_vehicle_id_val = '';
			$driver_name_val = $unit_vehicle_number_val = '';
			$contact_number_val = $starting_km_val = $ending_km_val = $fuel_qty_val = $fuel_card_number_val = '';
			$fuel_receipt_val = $def_qty_val = $def_receipt_val = $gas_station_location_val = '';
			$ticket_number_val = $ticket_img_val = $hour_or_load_val = '';
			$emploee_hour_over_load = $emploee_hourly_rate_over_load = '';
			$fuel_amount_paid = $emploee_hour_over_load_amount = '0';
			if(null !== app('request')->input('id'))
			{
				echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
			} 
			$dispatch_id_val = (null !== old('dispatch_id')) ? old('dispatch_id') : '';
			$status_val = (null !== old('status')) ? old('status') : '';
			$assign_dispatch_id_val = (null !== old('assign_dispatch_id')) ? old('assign_dispatch_id') : '';
			$shift_type_val = (null !== old('shift_type')) ? old('shift_type') : '';
			$user_type_val = (null !== old('user_type')) ? old('user_type') : '';
			$user_id_val = (null !== old('user_id')) ? old('user_id') : '';
			$broker_vehicle_id_val = (null !== old('broker_vehicle_id')) ? old('broker_vehicle_id') : '';
			$driver_name_val = (null !== old('driver_name')) ? old('driver_name') : '';
			$unit_vehicle_number_val = (null !== old('unit_vehicle_number')) ? old('unit_vehicle_number') : '';
			$contact_number_val = (null !== old('contact_number')) ? old('contact_number') : '';
			$starting_km_val = (null !== old('starting_km')) ? old('starting_km') : '';
			$ending_km_val = (null !== old('ending_km')) ? old('ending_km') : '';
			$fuel_qty_val = (null !== old('fuel_qty')) ? old('fuel_qty') : '';
			$fuel_card_number_val = (null !== old('fuel_card_number')) ? old('fuel_card_number') : '';
			$fuel_receipt_val = (null !== old('fuel_receipt')) ? old('fuel_receipt') : '';
			$def_qty_val = (null !== old('def_qty')) ? old('def_qty') : '';
			$def_receipt_val = (null !== old('def_receipt')) ? old('def_receipt') : '';
			$gas_station_location_val = (null !== old('gas_station_location')) ? old('gas_station_location') : '';
			$ticket_number_val = (null !== old('ticket_number')) ? old('ticket_number') : '';
			$ticket_img_val = (null !== old('ticket_img')) ? old('ticket_img') : '';
			$hour_or_load_val = (null !== old('hour_or_load')) ? old('hour_or_load') : '';
			$emploee_hour_over_load = (null !== old('emploee_hour_over_load')) ? old('emploee_hour_over_load') : '';
			$fuel_amount_paid = (null !== old('fuel_amount_paid')) ? old('fuel_amount_paid') : '';
			$emploee_hour_over_load_amount = (null !== old('emploee_hour_over_load_amount')) ? old('emploee_hour_over_load_amount') : '';
			if(isset($dispatch_tickets)){
				$dispatch_id_val = (isset($dispatch_tickets->dispatch_id)) ? $dispatch_tickets->dispatch_id : '';
				$status_val = (isset($dispatch_tickets->status)) ? $dispatch_tickets->status : '';
				$assign_dispatch_id_val = (isset($dispatch_tickets->assign_dispatch_id)) ? $dispatch_tickets->assign_dispatch_id : '';
				$shift_type_val = (isset($dispatch_tickets->shift_type)) ? $dispatch_tickets->shift_type : '';
				$user_type_val = (isset($dispatch_tickets->user_type)) ? $dispatch_tickets->user_type : '';
				$user_id_val = (isset($dispatch_tickets->user_id)) ? $dispatch_tickets->user_id : '';
				$broker_vehicle_id_val = (isset($dispatch_tickets->broker_vehicle_id)) ? $dispatch_tickets->broker_vehicle_id : '';
				$driver_name_val = (isset($dispatch_tickets->driver_name)) ? $dispatch_tickets->driver_name : '';
				$unit_vehicle_number_val = (isset($dispatch_tickets->unit_vehicle_number)) ? $dispatch_tickets->unit_vehicle_number : '';
				$contact_number_val = (isset($dispatch_tickets->contact_number)) ? $dispatch_tickets->contact_number : '';
				$starting_km_val = (isset($dispatch_tickets->starting_km)) ? $dispatch_tickets->starting_km : '';
				$ending_km_val = (isset($dispatch_tickets->ending_km)) ? $dispatch_tickets->ending_km : '';
				$fuel_qty_val = (isset($dispatch_tickets->fuel_qty)) ? $dispatch_tickets->fuel_qty : '';
				$fuel_card_number_val = (isset($dispatch_tickets->fuel_card_number)) ? $dispatch_tickets->fuel_card_number : '';
				$fuel_receipt_val = (isset($dispatch_tickets->fuel_receipt)) ? $dispatch_tickets->fuel_receipt : '';
				$def_qty_val = (isset($dispatch_tickets->def_qty)) ? $dispatch_tickets->def_qty : '';
				$def_receipt_val = (isset($dispatch_tickets->def_receipt)) ? $dispatch_tickets->def_receipt : '';
				$gas_station_location_val = (isset($dispatch_tickets->gas_station_location)) ? $dispatch_tickets->gas_station_location : '';
				$ticket_number_val = (isset($dispatch_tickets->ticket_number)) ? $dispatch_tickets->ticket_number : '';
				$ticket_img_val = (isset($dispatch_tickets->ticket_img)) ? $dispatch_tickets->ticket_img : '';
				$hour_or_load_val = (isset($dispatch_tickets->hour_or_load)) ? $dispatch_tickets->hour_or_load : '';
				$emploee_hour_over_load = (isset($dispatch_tickets->emploee_hour_over_load)) ? $dispatch_tickets->emploee_hour_over_load : '';
				$fuel_amount_paid = (isset($dispatch_tickets->fuel_amount_paid)) ? $dispatch_tickets->fuel_amount_paid : '';
				$emploee_hour_over_load_amount = (isset($dispatch_tickets->emploee_hour_over_load_amount)) ? $dispatch_tickets->emploee_hour_over_load_amount : '';
			} else {}
			$emploee_hour_over_load = ($emploee_hour_over_load > 0) ? $emploee_hour_over_load : '';
			$emploee_hour_over_load_amount = ($emploee_hour_over_load_amount > 0) ? $emploee_hour_over_load_amount : '';
				
			@endphp
				<div class=" row">
				<input type="hidden" id="dispatch_ticket_id" value = "{{ $dispatch_ticket_id }}">
				<input type="hidden" id="load_type" value = "">
				<input type="hidden" id="prev_user_id" value = "{{ $user_id_val }}">
				<input type="hidden" id="prev_status" value = "{{ $status_val }}">
				<input type="hidden" id="prev_broker_vehicle_id" value = "{{ $broker_vehicle_id_val }}">
				<input type="hidden" name="assign_dispatch_id" id="assign_dispatch_id" value = "{{ $assign_dispatch_id_val }}">
				<input type="hidden" name="old_ticket_img" id="old_ticket_img" value = "{{ $ticket_img_val }}">
				<input type="hidden" name="old_def_receipt" id="old_def_receipt" value = "{{ $def_receipt_val }}">
				<input type="hidden" name="old_fuel_receipt" id="old_fuel_receipt" value = "{{ $fuel_receipt_val }}">
					<div class="col-sm-4">
						<div class="mb-3">
						{!! Form::label('Name', 'Dispatch *', ['class' => 'form-label']) !!}
						<select id="dispatch_id" name="dispatch_id" class="form-control selectpicker dispatch_id select_height" data-live-search="true">
							<option value="">Select Dispatch</option>
							@foreach ($dispatches as $key => $value)
								@php
								$dispatch_selected = '';
									if($dispatch_id_val == $value->id) {
										$dispatch_selected = 'selected';
									}
								@endphp
								<option value="{{ $value->id }}" {{ $dispatch_selected }}>{{ $value->getCustomer->company_name.' ( '.$value->start_location.' To '.$value->dump_location.' '.$value->start_time.' )' }}</option>
							@endforeach
						</select> 
						{!! $errors->first('dispatch_id','<span class="help-inline text-danger">:message</span>') !!}
						<span class="help-inline on_select_error dispatch_id_err"></span>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						@php
						$user_type = array('employee' => 'Employee','broker' => 'Broker')
						@endphp
						{!! Form::label('Name', 'User Type *', ['class' => 'form-label']) !!}
						<select id="user_type" name="user_type" class="form-control user_type select_height">
							<option value="">Select User Type</option>
							@foreach ($user_type as $key => $value)
							@php
								$user_type_selected = '';
									if($user_type_val == $key) {
										$user_type_selected = 'selected';
									}
								@endphp
								<option value="{{ $key }}" {{ $user_type_selected }}>{{ $value }}</option>
							@endforeach
						</select> 
						{!! $errors->first('user_type','<span class="help-inline text-danger">:message</span>') !!}
						<span class="user_type_err"></span>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{!! Form::label('Name', 'Employee / Broker Name *', ['class' => 'form-label']) !!}
						<select id="user_id" name="user_id" class="form-control user_id select_height">
							<option value="">Select Name</option>
						</select> 
						{!! $errors->first('user_id','<span class="help-inline text-danger">:message</span>') !!}
						
						<span class="user_id_err"></span>
						</div>
					</div>
					<div class="col-sm-4 broker_vehicle_id_div">
						<div class="mb-3">
						{!! Form::label('Name', 'Broker Vehicle *', ['class' => 'form-label']) !!}
						<select id="broker_vehicle_id" name="broker_vehicle_id" class="form-control broker_vehicle_id select_height">
							<option value="">Select vehicle</option>
						</select> 
						{!! $errors->first('broker_vehicle_id','<span class="help-inline text-danger">:message</span>') !!}
						<span class="broker_vehicle_id_err"></span>
						</div>
					</div>
				</div>	
				<div class="row seprate_div margin_bottom_one_per">
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Driver Name', ['class' => 'form-label']) }}
						{{ Form::text('driver_name', $driver_name_val, array('class' => 'form-control','id' => 'driver_name','readonly')) }}
						{!! $errors->first('driver_name','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Vehicle / Unit Number', ['class' => 'form-label']) }}
						{{ Form::text('unit_vehicle_number', $unit_vehicle_number_val, array('class' => 'form-control','id' => 'unit_vehicle_number','readonly')) }}
						{!! $errors->first('unit_vehicle_number','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Contact Number', ['class' => 'form-label']) }}
						{{ Form::text('contact_number', $contact_number_val, array('class' => 'form-control','id' => 'contact_number','readonly')) }}
						{!! $errors->first('contact_number','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<div class="mb-3">
						@php
						$shift_type = array('day' => 'Day','night' => 'Night')
						@endphp
						{!! Form::label('Name', 'Shift Type *', ['class' => 'form-label']) !!}
						<select id="shift_type" name="shift_type" class="form-control shift_type select_height">
							<option value="">Select Shift Type</option>
							@foreach ($shift_type as $key => $value)
							@php
								$shift_type_selected = '';
									if($shift_type_val == $key) {
										$shift_type_selected = 'selected';
									}
								@endphp
								<option value="{{ $key }}" {{ $shift_type_selected }}>{{ $value }}</option>
							@endforeach
						</select> 
						{!! $errors->first('shift_type','<span class="help-inline text-danger">:message</span>') !!}
						<span class="shift_type_err"></span>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Starting Km *', ['class' => 'form-label']) }}
						{{ Form::text('starting_km', $starting_km_val, array('class' => 'form-control','id' => 'starting_km','autocomplete' => 'off')) }}
						{!! $errors->first('starting_km','<span class="help-inline text-danger">:message</span>') !!}
						<span class="help-inline on_select_error starting_km_err"></span>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Ending Km *', ['class' => 'form-label']) }}
						{{ Form::text('ending_km', $ending_km_val, array('class' => 'form-control','id' => 'ending_km','autocomplete' => 'off')) }}
						{!! $errors->first('ending_km','<span class="help-inline text-danger">:message</span>') !!}
						<span class="help-inline on_select_error ending_km_err"></span>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Ticket Number *', ['class' => 'form-label']) }}
						{{ Form::text('ticket_number', $ticket_number_val, array('class' => 'form-control','id' => 'ticket_number','autocomplete' => 'off')) }}
						{!! $errors->first('ticket_number','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Ticket Image *', ['class' => 'form-label']) }}
						 {{ Form::file('ticket_img',Input::old('ticket_img'), array('class' => 'form-control','id' => 'ticket_img')) }}
						{!! $errors->first('ticket_img','<span class="help-inline text-danger">:message</span>') !!}
						@if($ticket_img_val != '')
						<img src="{{ asset('images/ticket_img/'.$ticket_img_val) }}" style="width:100px;" />
						@endif
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						@php
						$user_status_arr = array('pending' => 'Pending','completed' => 'Completed');
						@endphp
						{!! Form::label('Name', 'Status *', ['class' => 'form-label']) !!}
						<select id="status" name="status" class="form-control select_height">
							<option value="">Select Status</option>
							@foreach ($user_status_arr as $key => $value)
								<option value="{{ $key }}" {{ $status_val == $key ? 'selected' : ''}}>{{ $value }}</option>
							@endforeach
						</select> 
						{!! $errors->first('status','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Total Hour / Load*', ['class' => 'form-label hour_load_level']) }}
						{{ Form::text('hour_or_load', $hour_or_load_val, array('class' => 'form-control','id' => 'hour_or_load','autocomplete' => 'off')) }}
						{!! $errors->first('hour_or_load','<span class="help-inline text-danger">:message</span>') !!}
						<span class="help-inline hour_or_load_err"></span>
						</div>
					</div>
					<div class="col-sm-4 employee_hour_over_load_div">
						<div class="mb-3">
						{{ Form::label('name', 'Convert Loads to Hours', ['class' => 'form-label']) }}
						{{ Form::text('emploee_hour_over_load', $emploee_hour_over_load, array('class' => 'form-control emploee_hour_over_load','id' => 'emploee_hour_over_load','autocomplete' => 'off')) }}
						{!! $errors->first('emploee_hour_over_load','<span class="help-inline text-danger">:message</span>') !!}
						<span class="help-inline emploee_hour_over_load_err"></span>
						</div>
					</div>
					<div class="col-sm-4 employee_hour_over_load_div">
						<div class="mb-3">
						{{ Form::label('name', 'Employee Load to Hours Payment', ['class' => 'form-label label_100_prec emploee_hour_over_load_amount']) }}
						<span class="emploee_hour_over_load_amount_span">{{ $emploee_hour_over_load_amount }}</span>
						{{ Form::hidden('emploee_hour_over_load_amount', $emploee_hour_over_load_amount, array('class' => 'form-control emploee_hour_over_load_amount','id' => 'emploee_hour_over_load_amount')) }}
						</div>
					</div>
				</div>
				<div class="row for_employee">
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Fuel Quantity', ['class' => 'form-label']) }}
						{{ Form::text('fuel_qty', $fuel_qty_val, array('class' => 'form-control','id' => 'fuel_qty','autocomplete' => 'off')) }}
						{!! $errors->first('fuel_qty','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Fuel card Number', ['class' => 'form-label']) }}
						{{ Form::text('fuel_card_number', $fuel_card_number_val, array('class' => 'form-control','id' => 'fuel_card_number','autocomplete' => 'off')) }}
						{!! $errors->first('fuel_card_number','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Fuel Receipt', ['class' => 'form-label']) }}
						 {{ Form::file('fuel_receipt',Input::old('fuel_receipt'),  ["class"=>"required","multiple"=>false,'id' => 'fuel_receipt']) }}
						{!! $errors->first('fuel_receipt','<span class="help-inline text-danger">:message</span>') !!}
						@if($fuel_receipt_val != '')
						<img src="{{ asset('images/fuel_receipt/'.$fuel_receipt_val) }}" style="width:100px;" />
						@endif
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Def Quantity', ['class' => 'form-label']) }}
						{{ Form::text('def_qty', $def_qty_val, array('class' => 'form-control','id' => 'def_qty','autocomplete' => 'off')) }}
						{!! $errors->first('def_qty','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Def Receipt', ['class' => 'form-label']) }}
						 {{ Form::file('def_receipt',Input::old('def_receipt'), ["class"=>"required","multiple"=>false,'id' => 'def_receipt']) }}
						{!! $errors->first('def_receipt','<span class="help-inline text-danger">:message</span>') !!}
						@if($def_receipt_val != '')
						<img src="{{ asset('images/def_receipt/'.$def_receipt_val) }}" style="width:100px;" />
						@endif
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Gas Station Location', ['class' => 'form-label']) }}
						{{ Form::text('gas_station_location', $gas_station_location_val, array('class' => 'form-control','id' => 'gas_station_location','autocomplete' => 'off')) }}
						{!! $errors->first('gas_station_location','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('name', 'Fuel Expense', ['class' => 'form-label label_100_prec']) }}
						<span class="fuel_amount_paid_span on_select_error label_100_prec">{{ $fuel_amount_paid }}</span>
						{{ Form::hidden('fuel_amount_paid', $fuel_amount_paid, array('class' => 'form-control fuel_amount_paid','id' => 'fuel_amount_paid')) }}
						{!! $errors->first('fuel_amount_paid','<span class="help-inline fuel_amount_paid_err_on_load text-danger">:message</span>') !!}
						<span class="help-inline label_100_prec fuel_amount_paid_err"></span>
						</div>
					</div>
				</div>
				
			<div class="form-group">
		  <div class="btn_bot">
		  {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary btn-sm']) !!}
		   <a class="btn btn-back btn-sm" href="{{ url('dispatch_tickets') }}" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> cancel</a> 
		  </div></div>
		</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	let prev_val_get = $("#prev_user_id").val();
	$(".employee_hour_over_load_div").hide();
	var selected_user_type_id = $(".user_type").val();
	if(selected_user_type_id > 0) {
		user_type_fun(selected_user_type_id);
	}
		
	if(prev_val_get > 0) {
	} else {
		$("#prev_user_id").val('');
		$("#prev_broker_vehicle_id").val('');
	}
	var sel_dispatch_val = $("#dispatch_id").val();
	if(sel_dispatch_val > 0) {
		get_hour_or_load(sel_dispatch_val);
	} else {}
	
	broker_vehicle_empty();
	for_employee_show();
	 jQuery(document).on('keyup','#fuel_qty',function(e){
		fuel_amount_show_status();
	});
	jQuery(document).on('blur','#ending_km',function(e){
		fuel_amount_show_status();
	});
	jQuery(document).on('blur','#starting_km',function(e){
		fuel_amount_show_status();
	});
	//fuel_amount_show_status();
	function on_select_error_empty() {
		$(".on_select_error").each(function(){
			$(this).html('');
		});
	}
	function fuel_amount_show_status() {
		on_select_error_empty();
		$(".fuel_amount_paid_err_on_load").html('');
		var ending_km = $("#ending_km").val();
		var starting_km = $("#starting_km").val();
			var dispatch_id = $("#dispatch_id").val();
			if(dispatch_id > 0 && ending_km > 0 && starting_km > 0) {
				$(".fuel_amount_paid_err").html('<b style="color:green">Please wait...</b>');
				var options={};
				options.type='POST';
					options.url="{{url('dispatch_tickets/verify_fuel_amount')}}";
					options.data={"_token": "{{ csrf_token() }}",'dispatch_id':dispatch_id,'ending_km':ending_km,'starting_km':starting_km};
					options.success=function(data){
						console.log(data.total_amount);
						$(".fuel_amount_paid_err").html('');
						let html_show = data.total_amount;
						let invalid_num = data.invalid_num;
						let invalid_num_class_name = data.invalid_num_class_name;
						//let html_show = data.html_show;
						if(invalid_num != '') {
							$(".fuel_amount_paid_err").html('<b style="color:red;">'+invalid_num+'</b>');
							$("."+invalid_num_class_name+"_err").html('<b style="color:red;">'+invalid_num+'</b>');
						}
						$(".fuel_amount_paid").val(html_show);
						$(".fuel_amount_paid_span").html('<b style="color:green;">'+html_show+'</b>');
					};
					options.error=function(data){
						$(".fuel_amount_paid_err").html('<b style="color:red;">Try again.some error occured</b>');
					};
					$.ajax(options);
			} else {
				return false;
			}
	}
	function for_employee_show() {
	let type_val = $("#user_type option:selected").val();
	if(type_val == 'employee') {
		$(".for_employee").show();
	} else {
		$(".for_employee").hide();
	}
}

	var user_type_val = $("#user_type").val();
	var prev_user_id_val = $("#prev_user_id").val();
	if(prev_user_id_val !== '') {
		get_broker_vehicle(prev_user_id_val);
	}
	jQuery(document).on('change','.user_id',function(e){
		var selected_id = $(this).val();
		get_broker_vehicle(selected_id);
	});
	jQuery(document).on('keyup','.emploee_hour_over_load',function(e){
		emploee_hour_over_load_fun();
	});
	jQuery(document).on('keyup','#hour_or_load',function(e){
		emploee_hour_over_load_fun();
		
	});
	function emploee_hour_over_load_fun() {
		$(".emploee_hour_over_load_amount_span").html('0');
		$(".emploee_hour_over_load_amount").val('0');
		var hour_time = $(".emploee_hour_over_load").val();
		var hour_or_load = $("#hour_or_load").val();
		var emp_type = $("#user_type").val();
		var emp_val = $("#user_id").val();
		if(hour_time != '' && hour_or_load != '') {
			if(emp_type == '' || emp_val == '') {
				$(".emploee_hour_over_load_err").html('<b style="color:red;">Please select user to continue...</b>');
				$(".emploee_hour_over_load").val('');
			} else {
				$(".emploee_hour_over_load_err").html('<b style="color:green;">Please wait to verify amount...</b>');
				var options={};
				options.type='POST';
					options.url="{{url('dispatch_tickets/load_over_hours_amount')}}";
					options.data={"_token": "{{ csrf_token() }}",'hour_time':hour_time,'emp_type':emp_type,'emp_val':emp_val,'hour_or_load':hour_or_load};
					options.success=function(data){
						console.log(data.total_amount);
						$(".emploee_hour_over_load_err").html('');
						let html_show = data.total_amount;
						let invalid_num = data.invalid_num;
						if(invalid_num != '') {
							$(".emploee_hour_over_load_err").html('<b style="color:red;">Invalid number</b>');
						}
						$(".emploee_hour_over_load_amount_span").html('<b style="color:green;">'+html_show+'</b>');
						$(".emploee_hour_over_load_amount").val(html_show);
						return false;
					};
					options.error=function(data){
						$(".emploee_hour_over_load_err").html('<b style="color:red;">Try again.some error occured</b>');
					};
					$.ajax(options);
			}
		} else {
			return false;
		}
	}
	function get_broker_vehicle(selected_id) {
		make_empty();
		broker_vehicle_empty();
		$(".user_id_err").html('');
		var selected_id = selected_id;
		var user_type = $("#user_type").val();
		var dispatch_id = $("#dispatch_id").val();
		var prev_user_id = $("#prev_user_id").val();
		var broker_vehicle_id = $("#prev_broker_vehicle_id").val();
		var prev_status = $("#prev_status").val();
		var dispatch_ticket_id = $("#dispatch_ticket_id").val();
		if(selected_id != '') {
			if(user_type == 'broker') {
			var options={};
			options.type='GET';
				options.url="{{url('get_broker_users')}}";
				options.data={'selected_id':user_type,'dispatch_id':dispatch_id,'prev_user_id':prev_user_id,'broker_vehicle_id':broker_vehicle_id,'prev_status':prev_status,'dispatch_ticket_id':dispatch_ticket_id};
				options.success=function(data){
					$(".broker_vehicle_id_div").show();
					$("#broker_vehicle_id").html(data);
				};
				options.error=function(data){
					$("#broker_vehicle_id").html('');
				};
				$.ajax(options);
			} else {
				get_vehicle_detail('employee',selected_id);
			}
		} else {
			$(".user_id_err").html('<b style="color:red">Please select User</b>');
		}
	}
	if(user_type_val !== '') {
		user_type_change(user_type_val);
	}
	jQuery(document).on('change','.user_type',function(e){
		var selected_id = $(this).val();
		user_type_fun(selected_id)
	});
	function user_type_fun(selected_id) {
		for_employee_show();
		user_type_change(selected_id);
	}
	function user_type_change(selected_id) {
		$("#user_id").html('');
		make_empty();
		broker_vehicle_empty();
		$(".user_type_err").html('');
		var selected_id = selected_id;
		var dispatch_id = $("#dispatch_id").val();
		var prev_user_id = $("#prev_user_id").val();
		var prev_status = $("#prev_status").val();
		var dispatch_ticket_id = $("#dispatch_ticket_id").val();
		if(dispatch_id > 0) {
			if(selected_id != '') {
			var options={};
			options.type='GET';
				options.url="{{url('get_dispatch_users')}}";
				options.data={'selected_id':selected_id,'dispatch_id':dispatch_id,'prev_user_id':prev_user_id,'prev_status':prev_status,'dispatch_ticket_id':dispatch_ticket_id};
				options.success=function(data){
					$("#user_id").html(data);
				};
				options.error=function(data){
					$("#user_id").html('');
				};
				$.ajax(options);
			} else {
				$("#user_id").html('');
				$(".user_type_err").html('<b style="color:red">Please select User Type</b>');
			}
		} else {
			$("#user_id").html('');
			$(".user_type_err").html('<b style="color:red">Please select Dispatch</b>');
		}
	}
	var prev_broker_vehicle_id_val = $("#prev_broker_vehicle_id").val();
	if(prev_broker_vehicle_id_val !== '') {
		get_vehicle_detail('broker',prev_broker_vehicle_id_val);
	}
	jQuery(document).on('change','.broker_vehicle_id',function(e){
		var selected_id = $(this).val();
		get_vehicle_detail('broker',selected_id);
	});
	function get_vehicle_detail(user_type,row_id) {
		make_empty();
		$(".broker_vehicle_id_err").html('');
		if(row_id > 0) {
		var dispatch_id = $("#dispatch_id").val();
		var user_id = $("#user_id").val();
		var prev_user_id = $("#prev_user_id").val();
		var dispatch_ticket_id = $("#dispatch_ticket_id").val();
		if(prev_user_id > 0) {
			user_id = prev_user_id; 
		}
		var options={};
			options.type='GET';
				options.url="{{url('get_assign_user_vehicle')}}";
				options.data={'user_type':user_type,'row_id':row_id,'dispatch_id':dispatch_id,'user_id':user_id,'dispatch_ticket_id':dispatch_ticket_id};
				options.success=function(data){
					$("#unit_vehicle_number").val(data.vehicle_number);
					$("#contact_number").val(data.contact_number);
					$("#driver_name").val(data.driver_name);
					if(data.status_show != '') {
						$(".user_id_err").html(data.status_show);
					} else {
						$(".user_id_err").html('');
					}
					
					$("#assign_dispatch_id").val(data.assign_dispatch_id);
				};
				$.ajax(options);
		} else {
			$(".broker_vehicle_id_err").html('<b style="color:red">Please select user</b>');
		}
		
	}
	function make_empty() {
		$("#unit_vehicle_number").val('');
		$("#contact_number").val('');
		$("#driver_name").val('');
	}
	function broker_vehicle_empty() {
		$(".broker_vehicle_id_div").hide();
		$("#broker_vehicle_id").html('');
	}
	function get_hour_or_load(row_id) {
		$(".employee_hour_over_load_div").hide();
		$("#load_type").val('');
		var options={};
		options.type='GET';
			options.url="{{url('get_hour_or_load')}}";
			options.data={'row_id':row_id};
			options.success=function(data){
				var hour_load_label = 'Hours';
				let data_type = data.type;
				let lower_data_type = data_type.toLowerCase();
				$("#load_type").val(lower_data_type);
				
				if(lower_data_type == 'load') {
					hour_load_label = 'Load';
					$(".employee_hour_over_load_div").show();
				}
				$(".hour_load_level").html('Total '+hour_load_label+' *');
			};
			$.ajax(options);
	}
	jQuery(document).on('change','.dispatch_id',function(e){
		$("#user_type").val('');
		$("#user_id").html('');
		broker_vehicle_empty();
		make_empty();
		let sel_val = $(this).val();
		if(sel_val > 0) {
			get_hour_or_load(sel_val);
			fuel_amount_show_status();	
		} else {
			$(".hour_load_level").html('Total Hour / Load*');
		}
	});
	});
</script>
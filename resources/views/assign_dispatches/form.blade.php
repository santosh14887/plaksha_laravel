<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Dispatches</li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/dispatches') }}">Dispatches</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a
                href="{{ url('assigned_dispatche/'.$dispatches->id) }}">Assign Dispatchs</a></li>
        <li class="breadcrumb-item active" aria-current="page">Assign Dispatch</li>
    </ol>
</nav>
<div class="row">
    <!-- @if ($errors->any())
					 @foreach ($errors->all() as $error)
						 <div class="row col-lg-12">
						<div class="alert alert-danger">
							<span>{{$error}}</span>
						</div>
					</div>
					 @endforeach
				 @endif -->
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">@if (isset($assign_dispatches) && null !== $assign_dispatches &&
                !empty($assign_dispatches))
                Update Assign Dispatch for ( {{ $dispatches->getCustomer->company_name.' '.$dispatches->start_time}})
                @else
                Assign Dispatch for ( {{ $dispatches->getCustomer->company_name.' '.$dispatches->start_time}})
                @endif</div>
            <div class="card-body">
                <input type="hidden" name="dispatch_id" value="{{ $dispatches->id }}" id="dispatch_id">
                <!-- <p class="card-description">
			Basic form layout
			</p> -->
                @php
                if(null !== app('request')->input('id'))
                {
                echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
                }
                $passport_count_start = 0;
                $user_type = array();
                $vehicle_id = array();
                $user_id = array();
                $user_status = array();
                $vehicle_number_as_comma_seprated_arr = array();
                $no_of_vehicles = array();
                $db_status = $status = array();
                $user_type['0'] = '';
                $user_id['0'] = '';
                $user_status['0'] = '';
                $vehicle_id['0'] = array();
                $status['0'] = 'pending';
                $vehicle_number_as_comma_seprated_arr['0'] = '';
                $no_of_vehicles['0'] = $dispatches->required_unit;
                @endphp
                @if(null !== old('user_type') && old('user_type'))
                @foreach(old('user_type') as $user_type_key => $user_type_val)
                @php

                $user_type[$user_type_key] = $user_type_val;
                // $vehicle_id[$user_type_key] = old('vehicle_id')[$user_type_key];
                $user_id[$user_type_key] = old('user_id')[$user_type_key];
                $status[$user_type_key] = old('status')[$user_type_key];
                if(isset(old('vehicle_number')[$user_type_key]) && null !== old('vehicle_number')[$user_type_key] &&
                old('vehicle_number')[$user_type_key] != '') {
                $vehicle_id[$user_type_key] = explode(",",old('vehicle_number')[$user_type_key]);
                $vehicle_number_as_comma_seprated_arr[$user_type_key] = old('vehicle_number')[$user_type_key];
                } else {
                $vehicle_id[$user_type_key] = array();
                $vehicle_number_as_comma_seprated_arr[$user_type_key] = '';
                }

                $no_of_vehicles[$user_type_key] = old('no_of_vehicles')[$user_type_key];
                @endphp
                @endforeach
                @else
                @php
                if(isset($assign_dispatches->user_type) && !empty($assign_dispatches->user_type)){
                $user_type = json_decode($assign_dispatches->user_type,true);
                }
                if(isset($assign_dispatches->vehicle_id) && !empty($assign_dispatches->vehicle_id)){
                $vehicle_id=json_decode($assign_dispatches->vehicle_id,true);
                $vehicle_number_as_comma_seprated_arr=json_decode($assign_dispatches->vehicle_id,true);
                }
                if(isset($assign_dispatches->no_of_vehicles) && !empty($assign_dispatches->no_of_vehicles)){
                $no_of_vehicles=json_decode($assign_dispatches->no_of_vehicles,true);
                }
                if(isset($assign_dispatches->status) && !empty($assign_dispatches->status)){
                $status=json_decode($assign_dispatches->status,true);
                $db_status = $status;
                }
                if(isset($assign_dispatches->user_id) && !empty($assign_dispatches->user_id)){
                $user_id=json_decode($assign_dispatches->user_id,true);
                $user_status=json_decode($assign_dispatches->user_status,true);
                }
                @endphp
                @endif
                @foreach($user_type as $passport_key => $passport_val)
                @php
                $passport_qty = $passport_count_start = $passport_key;
                $passport_count_show = $passport_key + 1;
                $vehicle_selected_id = '';
                $vehicle_selected_value = '';
                $disabledbutton = '';
                $main_div = '';
                if(isset($status[$passport_key]) && ($status[$passport_key] == 'completed' || $status[$passport_key] == 'submitted')) {
                $disabledbutton = 'disabledbutton';
                }
                @endphp
                <div class="col-md-12 {{ $disabledbutton }}">
                    @if($passport_qty != '0' && $disabledbutton == '')
                    @php
                    $main_div = 'main_div';
                    @endphp
                    <a href="javascript:void('0')" id="remove_assignment_{{ $passport_count_start }}"
                        class="remove_schedule_btn remove_assignment"><i
                            class=" menu-icon fa fa-lg fa-times-circle remove_schedule_btn"></i></a>
                    @endif
                    <div class="row {{ $main_div }}">
                        @php
                        $user_type_arr = array('employee' => 'Employee','broker' => 'Broker')
                        @endphp
                        <div class="col-sm-2">
                            <div class="mb-3">
                                {!! Form::label('Name', 'User Type *', ['class' => 'form-label']) !!}
                                <select id="user_type{{$passport_key}}" name="user_type[]" data-id="{{$passport_key}}"
                                    class="form-control user_type {{ $disabledbutton }} selectpicker"
                                    data-live-search="true">
                                    <option value="">Select User Type</option>
                                    @foreach ($user_type_arr as $key => $value)
                                    <option value="{{ $key }}" {{ $user_type[$passport_key] == $key ? 'selected' : ''}}>
                                        {{ $value }}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('user_type.'.$passport_key,'<span
                                    class="help-inline text-danger">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                {!! Form::label('Name', 'User Name *', ['class' => 'form-label']) !!}
                                <select id="user_id{{$passport_key}}" name="user_id[]" data-id="{{$passport_key}}"
                                    class="form-control user_id {{ $disabledbutton }} selectpicker"
                                    data-live-search="true">
                                    <option value="">Select User Name</option>
                                    @if($user_type[$passport_key] != '')
                                    @if($user_type[$passport_key] == 'employee')
                                    @foreach ($employee_data as $key => $value)

                                    @if($user_id[$passport_key] == $value->id)
                                    @php
                                    if(null !== $value->getVehicle && $value->getVehicle->id > 0) {
                                    $vehicle_selected_id = $value->getVehicle->id;
                                    $vehicle_selected_value = $value->getVehicle->vehicle_number;
                                    $vehicle_number_as_comma_seprated_arr[$passport_key] = $vehicle_selected_id;
                                    }
                                    @endphp
                                    @endif
                                    <option value="{{ $value->id }}"
                                        {{ $user_id[$passport_key] == $value->id ? 'selected' : ''}}>{{ $value->name }}
                                    </option>
                                    @endforeach
                                    @else
                                    @foreach ($broker_data as $key => $value)
                                    <option value="{{ $value->id }}"
                                        {{ $user_id[$passport_key] == $value->id ? 'selected' : ''}}>{{ $value->name }}
                                    </option>
                                    @endforeach
                                    @endif
                                    @endif
                                </select>
                                {!! $errors->first('user_id.'.$passport_key,'<span
                                    class="help-inline text-danger">:message</span>') !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3" id="unit_number_div{{$passport_key}}">
                                {!! Form::label('Name', 'Unit Number *', ['class' => 'form-label']) !!}
                                <select id="vehicle_id{{$passport_key}}" name="vehicle_id[]" multiple
                                    class="form-control {{ $disabledbutton }}  selectpicker vehicle_id"
                                    data-live-search="true" data-id="{{$passport_key}}">
                                    @if($user_type[$passport_key] == '')
                                    <option value="">Select Unit Number</option>
                                    @elseif($vehicle_selected_value == '' && $user_type[$passport_key] == 'employee')
                                    <option value="">Select Unit Number</option>
                                    @elseif($user_type[$passport_key] == 'broker')
                                    <option value="0" selected>Broker Vehicle</option>
                                    @else
                                    <option value="{{ $vehicle_selected_id }}" selected> {{ $vehicle_selected_value }}
                                    </option>
                                    @endif
                                </select>
                                {!! $errors->first('vehicle_number.'.$passport_key,'<span
                                    class="help-inline text-danger">:message</span>') !!}
                                <input type="hidden" name="vehicle_number[]" id="vehicle_number{{$passport_key}}"
                                    value="{{ $vehicle_number_as_comma_seprated_arr[$passport_key] }}">
									@if(isset($user_status[$passport_key]) && null != $user_status[$passport_key] && $user_status[$passport_key] != 'active')
									<?php
										$page_url = url('/').'/employees/' . $user_id[$passport_key] . '/edit';
										echo $status_show = '<span style="color:red;">Employee is not active.Kindly verify to continue...<a href="'.$page_url.'">click here</a></span>';
							
									?>
									@endif
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
                                @php
                                $disabledbutton_class = '';
                                if($user_type[$passport_key] == 'employee') {
                                $no_of_vehicles[$passport_key] = 1;
                                $disabledbutton_class = 'disabledbutton';
                                }
                                @endphp
                                {!! Form::label('unit_assigned', 'Unit Assigned *', ['class' => 'form-label']) !!}
                                {!! Form::text('no_of_vehicles[]',$no_of_vehicles[$passport_key],['class' =>
                                'form-control '.$disabledbutton.' '.$disabledbutton_class,'id' =>
                                'no_of_vehicles'.$passport_key,'placeholder'=>'Unit Assigned']) !!}
                                {!! $errors->first('no_of_vehicles.'.$passport_key,'<span
                                    class="help-inline text-danger">:message</span>') !!}
                            </div>
                        </div>
                        @php
                        if (isset($assign_dispatches) && null !== $assign_dispatches && !empty($assign_dispatches)) {
                        $user_status_arr = array('pending' => 'Pending','accepted' => 'Accepted','decline' =>
                        'Decline','cancelled' => 'Cancelled');
                        } else {
                        $user_status_arr = array('pending' => 'Pending','accepted' => 'Accepted');
                        }

                        @endphp
                        <div class="col-sm-2">
                            <div class="mb-3">
                                {!! Form::label('Name', 'Status *', ['class' => 'form-label']) !!}
                                <select id="status{{$passport_key}}" name="status[]" data-id="{{$passport_key}}"
                                    class="assign_dispatch_status_select form-control {{ $disabledbutton }} select_height">
                                    <option value="">Select Status</option>
                                    @if($status[$passport_key] == 'submitted')
                                    <option value="submitted" selected>Submitted</option>
								@elseif($status[$passport_key] == 'completed')
                                    <option value="completed" selected>Completed</option>
                                    @else
                                    @foreach ($user_status_arr as $key => $value)
                                    <option value="{{ $key }}" {{ $status[$passport_key] == $key ? 'selected' : ''}}>
                                        {{ $value }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                {!! $errors->first('status.'.$passport_key,'<span
                                    class="help-inline text-danger">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div id="append_data"></div>
                <div class="add_passwordcol col-md-12">
                    <div class="row">
                        <div class="margin_bottom">
                            <button id="add_more_btn" type="button"
                                class="btn btn-primary btn-sm add_more_btn_employee">&plus; Add New</button>
                        </div>
                    </div>
                </div>
				<div class="margin_bottom_one_per cancel_dispatch_msg_div" id="cancel_dispatch_msg_div">
					<div class="form-group">
						<div class="col-md-6">
							<div class="mb-3">
							{{ Form::label('name', 'Cancel Dispatch Message ', ['class' => 'form-label']) }}
							{{ Form::textarea('cancel_message', null, array('class' => 'form-control','id' => 'cancel_message_form','rows' => 5)) }}
							</div>
						</div>
					</div>
					 <span class="all_cancel_dispatches_err" style="color:red;"></span>
					 <span class="all_cancel_dispatches_success" style="color:green;"></span>
				</div>
                <div class="form-group">
                    <div class="btn_bot">
                        {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary
                        btn-sm']) !!}
                        <a class="btn btn-back btn-sm" href="{{ url('assigned_dispatche/'.$dispatches->id) }}"
                            role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> cancel</a>
                    </div>
                </div>
                <p id="passport_count_get" style="display:none;">{{$passport_count_start}}</p>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    jQuery(document).on('change', '.vehicle_id', function(e) {
        e.preventDefault();
        let id = $(this).attr('id');
        let data_id = $(this).data('id');
        let vehicle_selected = $("#vehicle_id" + data_id).find('option:selected').val();
        /* var selectedText = $(this).find('option:selected').map(function() {
        		return $(this).val();
        	  }).get().join(','); */
        //  alert(selectedText);
        $("#vehicle_number" + data_id).val(selectedText);
    });
    jQuery(document).on('change', '.user_type', function(e) {
        e.preventDefault();
        let data_id = $(this).data('id');
        if (typeof data_id !== "undefined") {
            let user_arr = '';
            $('.user_id').each(function() {
                let user_selected = $(this).find('option:selected').val();
                if (user_selected > 0) {
                    user_arr += user_selected + ',';
                }
            });
            let selected_val = $(this).find('option:selected').val();
            if (selected_val == 'employee') {
                $("#no_of_vehicles" + data_id).val('1');
                $("#no_of_vehicles" + data_id).addClass('disabledbutton');
            } else {
                $("#no_of_vehicles" + data_id).removeClass('disabledbutton');
            }
            var options = {};
            options.type = 'POST';
            options.url = "{{url('get_users')}}";
            options.data = {
                "_token": "{{ csrf_token() }}",
                'type': selected_val,
                'user_arr': user_arr
            };
            options.success = function(data) {
                $("#user_id" + data_id).html(data);
                $("#user_id" + data_id).change();
                $('.selectpicker').selectpicker('refresh');
            };
            options.error = function(data) {
                $("#user_id" + data_id).html(data);
            };
            $.ajax(options);
        }
    });
    jQuery(document).on('change', '.user_id', function(e) {
        e.preventDefault();
        var data_id = $(this).data('id');
        if (typeof data_id !== "undefined") {
            let selected_val = $(this).find('option:selected').val();
            let user_type = $("#user_type" + data_id).find('option:selected').val();
            add_more_count = data_id;
            var options = {};
            options.type = 'GET';
            options.url = "{{url('get_user_vehicle')}}";
            options.data = {
                'user_id': selected_val,
                'user_type': user_type,
                'count': add_more_count
            };
            options.success = function(data) {
                if (data.success) {
                    let data_id = data.count;
                    $("#unit_number_div" + data_id).html(data.html);
                    let vehicle_selected = $("#vehicle_id" + data_id).find('option:selected').val();
                    $("#vehicle_number" + data_id).val(vehicle_selected);
                    $("#status" + data_id).val('pending');
                    $('.selectpicker').selectpicker({
                        liveSearch: true
                    });
                }
            };
            options.error = function(data) {
                $("#unit_number_div" + data_id).html(data.html);
            };
            $.ajax(options);
        } else {
            let option_val = "<option value=''>Select Unit Number</option>";;
            $("#vehicle_id" + data_id).val(option_val);
        }
    });
    jQuery(document).on('click', 'a.remove_assignment', function(e) {
        e.preventDefault();
        $(this).parent().detach();
    });
    $(document).on("click", "button#add_more_btn", function() {

        var options = {};
        add_more_count = $("#passport_count_get").html();
        var dispatch_id = $("#dispatch_id").val();
        add_more_count = +add_more_count + +1;
        $("#passport_count_get").html(add_more_count);
        options.type = 'GET';
        options.url = "{{url('add_more_dispach_assignment')}}";
        options.data = {
            "display": "add_more_dispach_assignment",
            'count': add_more_count,
            'dispatch_id': dispatch_id
        };
        options.success = function(data) {
            console.log(data);
            if (data.success) {

                $("#append_data").append(data.html);
                $('.selectpicker').selectpicker({
                    liveSearch: true
                });
            }
        };
        options.error = function(data) {
            $("#append_data").append(data);
        };
        $.ajax(options);
    });
});
</script>
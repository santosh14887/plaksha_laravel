{!! Form::label('Name', 'Unit Number *') !!}
<select id="vehicle_id{{$count}}" @if($multiple != '') multiple @endif name="vehicle_id[]" class="form-control selectpicker vehicle_id" data-live-search="true" data-id="{{$count}}">
	@if(isset($users_vehicle['vehicle_number']))
		<option value="{{ $users_vehicle['id'] }}" selected >{{ $users_vehicle['vehicle_number'] }}</option>
	@elseif(isset($user_id) && $user_id > 0 && $user_type == 'employee')
	<option value="">Select Unit Number</option>
	@elseif($user_type == 'broker')
	<option value="0" selected >Broker Vehicle</option>
	@else
		<option value="">Select Unit Number</option>
	@endif
		
	
</select> 
@if( !isset($users_vehicle['vehicle_number']) && $user_type == 'employee' && $user_id > 0)
	<span style="color:red;">Vehicle not assigned.To add <a href="{{ URL::to('employees/' . $user_id . '/edit') }}">click here</a></span>
@endif
@if($user_type == 'employee' && $user_status != 'active')
	<span style="color:red;">Employee is not active.Kindly verify to continue...<a href="{{ URL::to('employees/' . $user_id . '/edit') }}">click here</a></span>
@endif
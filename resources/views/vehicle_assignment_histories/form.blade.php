
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Vehicles</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0)">Assignment History</a></li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">
			@php
			$action_status = '';
			if (!empty($vehicle_assignment_histories)) {
				if(null != $vehicle_assignment_histories->name) {
					$action_status = 'Assign Vehicle for '.$vehicle_assignment_histories->name;
				} else {
					$action_status = 'User does not exist';
				}
				
			} else {
				$action_status = 'Assign Vehicle';
			}
			@endphp
			{{ $action_status }}
			</div>
			@php
			if(null !== app('request')->input('id'))
			{
				echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
			}
			$passport_count_start = 0;
			$comment = '';
			$user_id = $vehicle_id_val = $end_time = '';
			$start_time =  date('m/d/Y h:i:s a');
			@endphp	
			@if(null !== old('start_time') && old('start_time'))
				@php
				$comment = old('comment');
				$vehicle_id_val = (null !== old('vehicle_id')) ? old('vehicle_id') : '';
				$user_id = old('user_id');
				$start_time = (null !== old('start_time')) ? old('start_time') : $start_time;
				$end_time = (null !== old('end_time')) ? old('end_time') : $end_time;
				@endphp
			@else
				@php
					
					if(isset($vehicle_assignment_histories->comment) && !empty($vehicle_assignment_histories->comment)){
						  $comment = $vehicle_assignment_histories->comment;
					}
					 if(isset($vehicle_assignment_histories->user_id) && !empty($vehicle_assignment_histories->user_id)){
						  $user_id = $vehicle_assignment_histories->user_id;
					}
					$start_time = (isset($vehicle_assignment_histories->start_time)) ? $vehicle_assignment_histories->start_time : $start_time;
					$end_time = (isset($vehicle_assignment_histories->end_time)) ? $vehicle_assignment_histories->end_time : $end_time;
					$vehicle_id_val = (isset($vehicle_assignment_histories->vehicle_id)) ? $vehicle_assignment_histories->vehicle_id : '';
				@endphp
			@endif
			@php
			if(Input::old('start_time') != '') {
				$start_time = Input::old('start_time');
			} else {
				$start_time = date("m/d/Y h:i:s a", strtotime($start_time));
			}
			if($end_time != '') {
				$end_time = date("m/d/Y h:i:s a", strtotime($end_time));
			}
			@endphp
		<div class="card-body">
			@if ($errors->any())
					 @foreach ($errors->all() as $error)
						 <div class="row col-lg-12">
						<div class="alert alert-danger">
							<span>{{$error}}</span>
						</div>
					</div>
					 @endforeach
				 @endif
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('Name', 'User Name *', ['class' => 'form-label']) !!}
					<select id="user_id" name="user_id" class="form-control user_id selectpicker" data-live-search="true">
						<option value="">Select User Name</option>
							@foreach ($user_arr as $key => $value)
								<option value="{{ $value->id }}" {{ $user_id == $value->id ? 'selected' : ''}}>{{ $value->name }}</option>
							@endforeach
					</select> 
					{!! $errors->first('user_id','<span class="help-inline text-danger">:message</span>') !!}
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
						 if($vehicle_id_val == $key) {
							$unit_active = 'selected';
						}
						@endphp
							<option value="{{ $key }}" {{ $unit_active }}>{{ $value }}</option>
						@endforeach
					</select> 
					{!! $errors->first('vehicle_id','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('start_time', 'Start Time *', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
					  <div class="cale">
						 <span class="iconcalender"></span>
						 {!! Form::text('start_time',$start_time,['class' => 'form-control','id'=>'start_time']) !!}
					  </div>
					  {!! $errors->first('start_time','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('end_time', 'End Time', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
					  <div class="cale">
						 <span class="iconcalender"></span>
						 {!! Form::text('end_time',$end_time,['class' => 'form-control','id'=>'end_time']) !!}
					  </div>
					  {!! $errors->first('end_time','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
			</div>
			
		
			<div class="row">
			<div class="col-sm-6">
						<div class="mb-3">
						{{ Form::label('name', 'Comment *', ['class' => 'form-label']) }}
						{{ Form::textarea('comment', $comment, array('class' => 'form-control','rows' => 5)) }}
						{!! $errors->first('comment','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
			</div>
			<div class="form-group">
			{!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary btn-sm']) !!}
		   <a class="btn btn-back btn-sm" href="{{ url('vehicle_assignment_histories') }}" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
			{{ Form::close() }}
			</div>
		</div>
		</div>
	</div>
</div>

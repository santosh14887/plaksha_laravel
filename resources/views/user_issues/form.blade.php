
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Issue</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0)">User Issue</a></li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">
			@php
			$action_status = '';
			if (!empty($user_issues)) {
				if(null != $user_issues->getUser) {
					$action_status = 'Update issue for '.$user_issues->getUser->name;
				} else {
					$action_status = 'User does not exist';
				}
				
			} else {
				$action_status = 'Create Issue';
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
			$title = '';
			$description = '';
			$user_id = '';
			$issue_category = '';
			$start_time =  date('m/d/Y h:i:s a');
			$status =  'pending';
			@endphp	
			@if(null !== old('title') && old('title'))
				@php
				$title = old('title');
				$description = old('description');
				$user_id = old('user_id');
				$issue_category = old('issue_category');
				$start_time = old('start_time');
				$status = old('status');
				@endphp
			@else
				@php
					if(isset($user_issues->title) && !empty($user_issues->title)){
					  $title = $user_issues->title;
					}
					if(isset($user_issues->description) && !empty($user_issues->description)){
						  $description = $user_issues->description;
					}
					 if(isset($user_issues->user_id) && !empty($user_issues->user_id)){
						  $user_id = $user_issues->user_id;
					}
					if(isset($user_issues->issue_category) && !empty($user_issues->issue_category)){
						  $issue_category = $user_issues->issue_category;
					}
					if(isset($user_issues->start_time) && !empty($user_issues->start_time)){
						  $start_time = $user_issues->start_time;
					}
					if(isset($user_issues->status) && !empty($user_issues->status)){
						  $status = $user_issues->status;
					}
				@endphp
			@endif
			@php
			if(Input::old('start_time') != '') {
				$start_time = Input::old('start_time');
			} else {
				$start_time = date("m/d/Y h:i:s a", strtotime($start_time));
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
					{!! Form::label('Category', 'Category *', ['class' => 'form-label']) !!}
					<select id="issue_category" name="issue_category" class="form-control issue_category selectpicker" data-live-search="true">
						<option value="">Select Category</option>
							@foreach ($issue_category_arr as $key => $value)
								<option value="{{ $value->id }}" {{ $issue_category == $value->id ? 'selected' : ''}}>{{ $value->title }}</option>
							@endforeach
					</select>
					{!! $errors->first('issue_category','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				
			</div>
			<div class="row">
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('start_time', 'On Date *', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
					  <div class="cale">
						 <span class="iconcalender"></span>
						 {!! Form::text('start_time',$start_time,['class' => 'form-control','id'=>'start_time']) !!}
					  </div>
					  {!! $errors->first('start_time','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					@php
				$status_type_arr = array('on_hold' => 'On Hold','pending' => 'Pending','rejected' => 'Rejected','resolved' => 'Resolved')
				@endphp
					<div class="mb-3">
						{!! Form::label('Name', 'Status *', ['class' => 'form-label']) !!}
					<select id="status" name="status" class="form-control selectpicker" data-live-search="true">
						<option value="">Select Status</option>
						@foreach ($status_type_arr as $key => $value)
						@php
						$selected_value = '';
						if($status == $key) {
							$selected_value = 'selected';
						}
						@endphp
							<option value="{{ $key }}" {{ $selected_value }}>{{ $value }}</option>
						@endforeach
					</select> 
					{!! $errors->first('status','<span class="help-inline text-danger">:message</span>') !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="mb-3">
					{!! Form::label('title', 'Title *', ['class' => 'form-label']) !!}
						{!! Form::text('title',$title,['class' => 'form-control ','id' => 'title','placeholder'=>'Title']) !!}
						{!! $errors->first('title','<span class="help-inline text-danger">:message</span>') !!}	
					</div>
				</div>
			</div>
			
		
			<div class="row">
			<div class="col-sm-6">
						<div class="mb-3">
						{{ Form::label('name', 'Description *', ['class' => 'form-label']) }}
						{{ Form::textarea('description', $description, array('class' => 'form-control','rows' => 5)) }}
						{!! $errors->first('description','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
			</div>
			<div class="form-group">
			{!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary btn-sm']) !!}
		   <a class="btn btn-back btn-sm" href="{{ url('user_issues/'.$user_issues->id) }}" role="button"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</a>
			{{ Form::close() }}
			</div>
		</div>
		</div>
	</div>
</div>

<div class="row">
@if($errors->any())
    {!! implode('', $errors->all('<div>:message</div>')) !!}
@endif
	<div class="col-sm-4">
		<div class="mb-3">
			{!! Form::label('name', 'Name * ', ['class' => 'form-label']) !!}
			{!! Form::text('name', null, ['autocomplete' => "off",'class' => 'form-control']) !!}
			{!! $errors->first('name', '<p class="help-inline text-danger">:message</p>') !!}
		</div>
	</div><!-- Col -->
	<div class="col-sm-4">
		<div class="mb-3">
			{!! Form::label('email', 'Email * ', ['class' => 'form-label']) !!}
			{!! Form::email('email', null, ['autocomplete' => "off",'class' => 'form-control']) !!}
			{!! $errors->first('email', '<p class="help-inline text-danger">:message</p>') !!}
		</div>
	</div><!-- Col -->
	<div class="col-sm-4">
		<div class="mb-3">
		@php
			$pwd_str = '';
			if(isset($user) && null != $user) {
				
				$pwd_str = $user->password_string;
			}
			else if(null != Input::old('password')) {
				$pwd_str = Input::old('password');
			} else {
				$pwd_str = '';
			}
			@endphp
			{{ Form::label('name', 'Password', ['class' => 'form-label']) }}
			{{ Form::text('password', $pwd_str, array('class' => 'form-control')) }}
			{!! $errors->first('password','<span class="help-inline text-danger">:message</span>') !!}
		</div>
	</div><!-- Col -->
	<div class="col-sm-4">
		<div class="mb-3">
			{!! Form::label('roles', 'Roles', ['class' => 'form-label','for'=>'roles']) !!}
			   <div class="select-wrap">
				  <select multiple="multiple" name="roles[]"
					 id="roles"
					 class="form-control form-item__element--select selectpicker"
					 data-live-search="true">
				  @foreach($roles as $aKey => $aSport)
				  @php
				  $selected_val = '';
				  if(isset($user_roles) && !empty($user_roles))
				  {
					  foreach($user_roles as $aItemKey => $aItemval)
					  {
						  if($aKey == $aItemval)
						  {
							$selected_val = 'selected';
						  }
					  }
				  }
				  else
				  {
					  if(null !== old('roles') && !empty(old('roles')))
							{
							  foreach(old('roles') as $aItemKey => $aItemval)
							  {
								  if($aKey == $aItemval)
								  {
									$selected_val = 'selected';
								  }
							  } 
							}
				  }
				  @endphp
				  <option value="{{$aKey}}" {{ $selected_val }}>{{$aSport}}</option>
				  @endforeach
				  </select>
			   </div>
			   {!! $errors->first('roles', '
			   <p class="help-inline text-danger">:message</p>
			   ') !!}
		</div>
	</div><!-- Col -->
</div>

<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
	<a href="{{ url('/users') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
</div>
<script>
$(document).ready(function(){
 $('#roles').selectpicker({
           liveSearch: true
       });
       });
</script>
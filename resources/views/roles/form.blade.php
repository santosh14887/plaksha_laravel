<div class="row">
	<div class="col-sm-4">
		<div class="mb-3">
			{!! Form::label('name', 'Name * ', ['class' => 'form-label']) !!}
			{!! Form::text('name', null, ['class' => 'form-control']) !!}
			{!! $errors->first('name', '<p class="help-inline text-danger">:message</p>') !!}
		</div>
	</div><!-- Col -->
	<!--<div class="col-sm-4">
		<div class="mb-3">
			{!! Form::label('label', 'Label ', ['class' => 'form-label']) !!}
			{!! Form::text('label', null, ['class' => 'form-control']) !!}
			{!! $errors->first('label', '<p class="help-inline text-danger">:message</p>') !!}
		</div>
	</div>-->
	<div class="col-sm-4">
		<div class="mb-3">
		 
   <!--<div class="select-wrap">
	  <select multiple="multiple" name="permissions[]"
		 id="permissions"
		 class="form-control form-item__element--select selectpicker"
		 data-live-search="true">
		  @foreach($permissions as $aKey => $aSport)
		  @php
		  $selected_val = '';
		  if(isset($role_permission) && !empty($role_permission))
		  {
			  foreach($role_permission as $aItemKey => $aItemval)
			  {
				  if($aKey == $aItemKey)
				  {
					$selected_val = 'selected';
				  }
			  }
		  }
		  else
		  {
			  if(null !== old('permissions') && !empty(old('permissions')))
					{
					  foreach(old('permissions') as $aItemKey => $aItemval)
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
	   </div> -->
		</div>
	</div><!-- Col -->
</div><!-- Row -->
<div class="row margin_bottom_one_per">
{!! Form::label('permissions', 'Permissions', ['class' => 'form-label','for'=>'permissions']) !!}
	<div class="col-md-3">Select All</div>
	<div class="col-md-9">
		<div class="inline_checkbox"><input type="checkbox" name="view_all" data-id="view" id="view_all" class="invoice_checkbox view_all_cls" value="view_all"> <span class="checkbox_span">View</span></div>
		<div class="inline_checkbox"><input type="checkbox" name="action_all" data-id="action" id="action_all" class="invoice_checkbox view_all_cls" value="action_all"> <span class="checkbox_span">Read / Write</span></div></div>
</div>
@foreach($permissions as $vals)
@php
$lower_str = strtolower($vals);
$add_view_cls = ($lower_str == 'all') ? '' : 'view';
$add_view_id = 'view_'.$lower_str;
$add_action_id = 'action_'.$lower_str;
$view = 'viewMenu:'.$vals;
$action = 'viewMenu:Action'.$vals;
  $view_selected_val = $action_selected_val = '';
  if(isset($role_permission) && !empty($role_permission))
  {
	  foreach($role_permission as $aItemKey => $aItemval)
	  {
		  if($view == $aItemval)
		  {
			$view_selected_val = 'checked';
		  }
		  if($action == $aItemval)
		  {
			$action_selected_val = 'checked';
		  }
	  }
  }
  else
  {
	  if(null !== old('permissions') && !empty(old('permissions')))
			{
			  foreach(old('permissions') as $aItemKey => $aItemval)
			  {
				  if($view == $aItemval)
				  {
					$view_selected_val = 'checked';
				  } else {}
				  if($action == $aItemval)
				  {
					$action_selected_val = 'checked';
				  } else {}
			  } 
			}
  }
  @endphp
<div class="row margin_bottom_one_per">
	@if($lower_str == 'all')
	<div class="col-md-3"><b>Full Site Permission</b></div>
@else
	<div class="col-md-3">{{ $vals }}</div>
@endif
	<div class="col-md-9">
		<div class="inline_checkbox"><input type="checkbox" class="invoice_checkbox {{ $add_view_cls }}" data-id="{{ $lower_str }}" id="{{ $add_view_id }}" name="permissions[]" value="{{ $view }}" {{ $view_selected_val }}> 
		<span class="checkbox_span">
		@if($lower_str == 'all')
			View / Read / Write / Delete
			@else
		View
		@endif
		</span>
		</div>
		@if($lower_str != 'all')
		<div class="inline_checkbox"><input type="checkbox" class="invoice_checkbox action" data-id="{{ $lower_str }}" id="{{ $add_action_id }}" name="permissions[]" value="{{ $action }}" {{ $action_selected_val }}> <span class="checkbox_span">Read / Write</span></div>
		@endif
	</div>
</div>
@endforeach
<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
	<a href="{{ url('/roles') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
</div>
<script>
/* $(document).ready(function(){
 $('#permissions').selectpicker({
           liveSearch: true
       });
       }); */
</script>
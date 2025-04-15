<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Settings</li>
				<li class="breadcrumb-item active" aria-current="page">Fuel</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">@if (isset($fuels) && null !== $fuels && !empty($fuels))
			  Update Fuel Price
			  @else
				  Add Fuel Price
			  @endif</div>
		<div class="card-body">
			@php
			$on_date_val = $service_type_val = $amount_val = $comment_val = '';
			if(null !== app('request')->input('id'))
			{
				echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
			} 
			$on_date_val = (null !== old('on_date')) ? old('on_date') : '';
			$amount_val = (null !== old('amount')) ? old('amount') : '';
			if(isset($fuels)){
				$on_date_val = (isset($fuels->on_date)) ? $fuels->on_date : '';
				$amount_val = (isset($fuels->amount)) ? $fuels->amount : '';
			}
			if($on_date_val != '') {
				$on_date_val = date("Y-m-d", strtotime($on_date_val));
			}
			
			@endphp
				<div class="row">
					<div class="col-sm-4">
						<div class="mb-3">
					  {!! Form::label('on_date', 'Date *', ['class' => 'control-label','for'=>'acct_dateClosed']) !!}
					  <div class="input-group flatpickr" id="flatpickr-date">
					  <input type="text" class="form-control" name="on_date" value="{{ $on_date_val }}" placeholder="Select date" data-input="">
						<span class="input-group-text input-group-addon" data-toggle=""><i data-feather="calendar"></i></span>
					</div>
					  
					  {!! $errors->first('on_date','<span class="help-inline text-danger">:message</span>') !!}
					   </div>
					</div>
					
					<div class="col-sm-4">
						<div class="mb-3">
						{{ Form::label('amount', 'Amount *') }}
						{{ Form::text('amount', $amount_val, array('class' => 'form-control','id' => 'amount')) }}
						{!! $errors->first('amount','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
				</div>
			<div class="form-group">
		  {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary btn-sm']) !!}
		   <a href="{{ url('/fuels') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
		  </div>
		</div>
		</div>
	</div>
</div>
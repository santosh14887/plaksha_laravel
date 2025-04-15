<div class="row">
	<div class="col-sm-6">
		<div class="mb-3">
			{!! Form::label('name', 'Name * ', ['class' => 'form-label']) !!}
			{!! Form::text('name', null, ['class' => 'form-control']) !!}
			{!! $errors->first('name', '<p class="help-inline text-danger">:message</p>') !!}
		</div>
	</div><!-- Col -->
	<!--<div class="col-sm-6">
		<div class="mb-3">
			{!! Form::label('label', 'Label ', ['class' => 'form-label']) !!}
			{!! Form::text('label', null, ['class' => 'form-control']) !!}
			{!! $errors->first('label', '<p class="help-inline text-danger">:message</p>') !!}
		</div>
	</div>-->
</div><!-- Row -->

<div class="form-group">
    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary']) !!}
	<a href="{{ url('/permissions') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
</div>

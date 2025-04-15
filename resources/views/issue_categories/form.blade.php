<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Issue</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/issue_categories') }}">Issue Category</a></li>
				<li class="breadcrumb-item active" aria-current="page">Category</li>
			</ol>
		</nav>
<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
		<div class="card-header">
					@if (isset($issue_categories) && null !== $issue_categories && !empty($issue_categories))
				  Update Issue Category for ( {{ $issue_categories->title}})
				  @else
					  Add Issue Category
				  @endif
		</div>
		<div class="card-body">
			@php
			$title_val = $description_val = '';
			if(null !== app('request')->input('id'))
			{
				echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
			} 
			$title_val = (null !== old('title')) ? old('title') : '';
			$description_val = (null !== old('description')) ? old('description') : '';
			if(isset($issue_categories)){
				$title_val = (isset($issue_categories->title)) ? $issue_categories->title : '';
				$description_val = (isset($issue_categories->description)) ? $issue_categories->description : '';
			}
			@endphp
				<div class="row">
					<div class="col-sm-6">
						<div class="mb-3">
						{{ Form::label('title', 'Title *', ['class' => 'form-label']) }}
						{{ Form::text('title', $title_val, array('class' => 'form-control','id' => 'title')) }}
						{!! $errors->first('title','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
					<div class="col-sm-6">
						<div class="mb-3">
						{{ Form::label('name', 'Description ', ['class' => 'form-label']) }}
						{{ Form::textarea('description', $description_val, array('class' => 'form-control','rows' => 5)) }}
						{!! $errors->first('description','<span class="help-inline text-danger">:message</span>') !!}
						</div>
					</div>
				</div>
			<div class="form-group">
		  {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary btn-sm']) !!}
		  <a href="{{ url('/issue_categories') }}" title="Back"><span class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
		  </div>
		</div>
		</div>
	</div>
</div>
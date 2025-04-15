@extends('layouts.after_login')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
					<div class="card-header">{{ $books->name }} </div>

				<div class="card-body">
					<nav class="navbar navbar-inverse">
						<ul class="nav navbar-nav">
							<li><a href="{{ URL::to('books') }}">View All books</a></li>
						</ul>
					</nav>
					@if(null !== $books->reviews)
					@foreach($books->reviews as $values)
					<div class="main_div">
						<div class="row col-md-12">
						<div class="col-md-6">Added By : {{$values->getUser->name}}</div>
							<div class="col-md-6">Rating : {{$values->rating}}</div>
							<div class="col-md-12">
								Comment : {{$values->comment}}
							</div>
						</div>
						
					</div>
					@endforeach
					@endif
					{{ Form::open(array('url' => 'books/' . $books->id . '/review', 'method' => 'PUT')) }}
						<div class="form-group">
						@php
						for($rate_start = 1; $rate_start < 6; $rate_start++)
						{
							$rating_arr[$rate_start] = $rate_start;
						}
						array_unshift($rating_arr , '...select rating...');
						@endphp
							{{ Form::label('Rating', 'Rating *') }}
							{{ Form::select('rating', $rating_arr, Input::old('rating'), array('class' => 'form-control rating','id' => 'rating')) }}
							{!! $errors->first('rating','<span class="help-inline text-danger">:message</span>') !!}
						</div>
						<div class="form-group">
							{{ Form::label('Comment', 'Comment *') }}
							{{ Form::textarea('comment', Input::old('comment'), array('class' => 'form-control')) }}
							{!! $errors->first('comment','<span class="help-inline text-danger">:message</span>') !!}
						</div>
						{{ Form::submit('Add Review!', array('class' => 'btn btn-primary')) }}

					{{ Form::close() }}


				</div>
			</div>
		</div>
	</div>
</div>
@endsection

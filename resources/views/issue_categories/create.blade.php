@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/issue_categories', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('issue_categories.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

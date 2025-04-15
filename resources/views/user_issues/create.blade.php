@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/user_issues', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('user_issues.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

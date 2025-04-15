@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/assign_dispatches', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('assign_dispatches.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/fuels', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('fuels.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/fuel_histories', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('fuel_histories.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

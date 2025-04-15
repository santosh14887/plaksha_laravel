@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/vehicle_services', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('vehicle_services.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/assign_dispatch_broker_vehicles', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('assign_dispatch_broker_vehicles.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

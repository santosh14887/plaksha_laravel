@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/meter_histories', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('meter_histories.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

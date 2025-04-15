@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/vehicle_assignment_histories', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('vehicle_assignment_histories.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

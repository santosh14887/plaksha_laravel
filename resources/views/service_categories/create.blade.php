@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/service_categories', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('service_categories.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

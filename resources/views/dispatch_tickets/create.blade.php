@extends('layouts.after_login')
@section('content')
    {!! Form::open(['url' => '/dispatch_tickets', 'class' => 'form-horizontal', 'files' => true]) !!}
                        @include ('dispatch_tickets.form', ['formMode' => 'create'])
	{!! Form::close() !!}
@endsection

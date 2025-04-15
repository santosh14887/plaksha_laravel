@extends('layouts.after_login')

@section('content')
{!! Form::model($dispatch_tickets, [
                            'method' => 'PATCH',
                            'url' => ['/dispatch_tickets', $dispatch_tickets->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('dispatch_tickets.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
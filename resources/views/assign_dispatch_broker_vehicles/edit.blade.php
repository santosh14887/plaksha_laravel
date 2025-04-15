@extends('layouts.after_login')

@section('content')
{!! Form::model($assign_dispatch_broker_vehicles, [
                            'method' => 'PATCH',
                            'url' => ['/assign_dispatch_broker_vehicles', $assigned_dispatch->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('assign_dispatch_broker_vehicles.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
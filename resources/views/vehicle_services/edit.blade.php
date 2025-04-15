@extends('layouts.after_login')

@section('content')
{!! Form::model($vehicle_services, [
                            'method' => 'PATCH',
                            'url' => ['/vehicle_services', $vehicle_services->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('vehicle_services.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
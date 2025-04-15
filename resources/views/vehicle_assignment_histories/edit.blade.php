@extends('layouts.after_login')

@section('content')
{!! Form::model($vehicle_assignment_histories, [
                            'method' => 'PATCH',
                            'url' => ['/vehicle_assignment_histories', $vehicle_assignment_histories->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('vehicle_assignment_histories.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
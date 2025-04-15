@extends('layouts.after_login')

@section('content')
{!! Form::model($fuel_histories, [
                            'method' => 'PATCH',
                            'url' => ['/fuel_histories', $fuel_histories->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('fuel_histories.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
@extends('layouts.after_login')

@section('content')
{!! Form::model($meter_histories, [
                            'method' => 'PATCH',
                            'url' => ['/meter_histories', $meter_histories->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('meter_histories.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
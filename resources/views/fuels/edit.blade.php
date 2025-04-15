@extends('layouts.after_login')

@section('content')
{!! Form::model($fuels, [
                            'method' => 'PATCH',
                            'url' => ['/fuels', $fuels->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('fuels.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
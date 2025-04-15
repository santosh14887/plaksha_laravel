@extends('layouts.after_login')

@section('content')
{!! Form::model($assign_dispatches, [
                            'method' => 'PATCH',
                            'url' => ['/assign_dispatches', $dispatches->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('assign_dispatches.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
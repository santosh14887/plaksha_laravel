@extends('layouts.after_login')

@section('content')
{!! Form::model($service_categories, [
                            'method' => 'PATCH',
                            'url' => ['/service_categories', $service_categories->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('service_categories.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
@extends('layouts.after_login')

@section('content')
{!! Form::model($user_issues, [
                            'method' => 'PATCH',
                            'url' => ['/user_issues', $user_issues->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('user_issues.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
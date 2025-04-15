@extends('layouts.after_login')

@section('content')
{!! Form::model($issue_categories, [
                            'method' => 'PATCH',
                            'url' => ['/issue_categories', $issue_categories->id],
                            'class' => 'form-horizontal',
                            'files' => true
                        ]) !!}

                        @include ('issue_categories.form', ['formMode' => 'edit'])

 {!! Form::close() !!}
@endsection
@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Users</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/users') }}">Admin Users</a></li>
				<li class="breadcrumb-item active" aria-current="page">Add Admin User</li>
			</ol>
		</nav>
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Create New User</div>
                    <div class="card-body">

                        {!! Form::open(['url' => '/users', 'class' => 'form-horizontal']) !!}

                        @include ('users.form', ['formMode' => 'create'])

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
@endsection

@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Settings</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/roles') }}">Roles</a></li>
				<li class="breadcrumb-item active" aria-current="page">Add Role</li>
			</ol>
		</nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Create New Role</div>
                    <div class="card-body">

                        {!! Form::open(['url' => '/roles', 'class' => 'form-horizontal']) !!}

                        @include ('roles.form', ['formMode' => 'create'])

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
@endsection

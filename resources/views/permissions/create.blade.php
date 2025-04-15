@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Settings</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/permissions') }}">Permission</a></li>
				<li class="breadcrumb-item active" aria-current="page">Add Permission</li>
			</ol>
		</nav>
        <div class="row">
            <div class="col-md-12 stretch-card">
                <div class="card">
                    <div class="card-header">Create New Permission</div>
                    <div class="card-body">

                        {!! Form::open(['url' => '/permissions', 'class' => 'form-horizontal']) !!}

                        @include ('permissions.form', ['formMode' => 'create'])

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
@endsection

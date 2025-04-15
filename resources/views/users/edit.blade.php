@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Users</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/users') }}">Admin Users</a></li>
				<li class="breadcrumb-item active" aria-current="page">Edit admin User</li>
			</ol>
		</nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Edit User</div>
                    <div class="card-body">

                        {!! Form::model($user, [
                            'method' => 'PATCH',
                            'url' => ['/users', $user->id],
                            'class' => 'form-horizontal'
                        ]) !!}

                        @include ('users.form', ['formMode' => 'edit'])

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
@endsection

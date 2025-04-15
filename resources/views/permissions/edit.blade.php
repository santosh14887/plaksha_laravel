@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Settings</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/permissions') }}">Permission</a></li>
				<li class="breadcrumb-item active" aria-current="page">Edit Permission</li>
			</ol>
		</nav>
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Edit Permission</div>
                    <div class="card-body">

                        {!! Form::model($permission, [
                            'method' => 'PATCH',
                            'url' => ['/permissions', $permission->id],
                            'class' => 'form-horizontal'
                        ]) !!}

                        @include ('permissions.form', ['formMode' => 'edit'])

                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
@endsection

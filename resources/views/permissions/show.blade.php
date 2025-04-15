@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Settings</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/permissions') }}">Permission</a></li>
				<li class="breadcrumb-item active" aria-current="page">View Permission</li>
			</ol>
		</nav>
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">View Permission</div>
                    <div class="card-body">

                        <a href="{{ url('/permissions') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
						@if( Auth::user()->can('viewMenu:ActionPermission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                        <!-- <a href="{{ url('/permissions/' . $permission->id . '/edit') }}" title="Edit Permission"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                        {!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/permissions', $permission->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Permission',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!} -->
						@endif
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID.</th> <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $permission->id }}</td> <td> {{ $permission->name }} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection

@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Users</li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/users') }}">Admin Users</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Admin User</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">View Admin User</div>
            <div class="card-body">

                <a href="{{ url('/users') }}" title="Back"><button class="btn btn-warning btn-sm"><i
                            class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                @if(Auth::user()->can('viewMenu:ActionUser') || Auth::user()->can('viewMenu:All') || Auth::user()->type
                == 'admin')
                <a href="{{ url('/users/' . $user->id . '/edit') }}" title="Edit User"><button
                        class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        Edit</button></a>
                {!! Form::open([
                'method' => 'DELETE',
                'url' => ['/users', $user->id],
                'style' => 'display:inline'
                ]) !!}
                {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                'type' => 'submit',
                'class' => 'btn btn-danger btn-sm',
                'title' => 'Delete User',
                'onclick'=>'return confirm("Confirm delete?")'
                ))!!}
                {!! Form::close() !!}
                @endif
                <br />
                <br />

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> {{ $user->name }} </td>
                                <td> {{ $user->email }} </td>
                                <td>
                                    @if(null != $user->roles)
                                    @foreach($user->roles as $role_val)
                                    {{ ucfirst($role_val->name).' '}}
                                    @endforeach
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
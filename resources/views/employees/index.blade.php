@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
<div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Users</li>
        <li class="breadcrumb-item active" aria-current="page">Employee</li>
    </ol>
</nav>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">Employee List<span class="float_right">
                    @if(request('search') != '')
                    <a href="{{ url('/employees') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i
                                class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
                    @endif 
					@if(Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
					<a class="btn btn-success btn-sm" href="{{ URL::to('vehicles/create') }}">
						<i class="fa fa-plus" aria-hidden="true"></i> Add Vehicle
					</a>
					@endif 
					@if(Auth::user()->can('viewMenu:ActionEmployee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
                    <a class="btn btn-success btn-sm" href="{{ URL::to('employees/create') }}">
						<i class="fa fa-plus" aria-hidden="true"></i> Add New
					</a>
					@endif 
                </span></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4 float_right">
                        {!! Form::open(['method' => 'GET', 'url' => '/employees', 'class' => 'float-right', 'role' =>
                        'search']) !!}
                        <div class="input-group">
                            <input type="text" class="form-control search_input" value="{{ request('search') }}"
                                name="search" placeholder="Search...">
                            <span class="input-group-append">
                                <button class="btn btn-secondary" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Unit</th>
                                <th>Income</th>
                                <th>Pending</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($employees) > 0)
                            @foreach($employees as $key => $value)
                            <tr>
                                <td>{{ $employees->firstItem() + $key }}</td>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->email }}</td>
                                <td>
                                    @php
                                    if(null != $value->getVehicle) {
                                    echo $value->getVehicle->vehicle_number;
                                    } else {
                                    @endphp
                                    <a class="" href="{{ URL::to('employees/' . $value->id . '/edit') }}"
                                        title="Add / Update Unit">Add / Update Unit</a>
                                    @php
                                    }
                                    @endphp
                                </td>
                                <td>{{ $value->total_income }}</td>
                                <td>{{ $value->current_amount }}</td>
                                <td>
                                    <!-- {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/employees', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}-->
                                    <a class="btn btn-sm btn-info" href="{{ URL::to('/employees/' . $value->id) }}"
                                        title="View Employee"><i class=" menu-icon fa fa-lg fa-eye"></i></a>
                                    <!--<button type="submit" class="btn btn-danger btn-sm" title="Delete Employee" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>-->
									@if(Auth::user()->can('viewMenu:ActionEmployee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
                                    <a class="" href="{{ URL::to('employees/' . $value->id . '/edit') }}"
                                        title="Edit Employee"><button class="btn btn-primary btn-sm"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
									@endif
									@if(Auth::user()->can('viewMenu:Transaction') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
                                    <a class="" href="{{ URL::to('transaction/employee/' . $value->id) }}"
                                        title="View Transaction"><button class="btn btn-sm  btn-secondary"><i
                                                class="fa fa-exchange" aria-hidden="true"></i></button></a>
									@endif

                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="6">No data Found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pagination"> {!! $employees->appends(['search' => Request::get('search')])->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
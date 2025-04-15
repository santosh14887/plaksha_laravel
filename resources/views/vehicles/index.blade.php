@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
<div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Vehicles</li>
        <li class="breadcrumb-item active" aria-current="page">Vehicles Units</li>
    </ol>
</nav>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card">
                <div class="card-header">Vehicles List<span class="float_right">
                        @if(request('search') != '')
                        <a href="{{ url('/vehicles') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i
                                    class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
                        @endif
                        @if( Auth::user()->can('viewMenu:ActionEmployee') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                        <a class="btn btn-success btn-sm" href="{{ URL::to('employees/create') }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Employee
                        </a>
                        @endif
                        @if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                        <a href="{{ url('/vehicles/create') }}" class="btn btn-success btn-sm" title="Add New Vehicle">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>
                        @endif
                    </span></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4 float_right">
                            {!! Form::open(['method' => 'GET', 'url' => '/vehicles', 'class' => 'float-right', 'role' =>
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
                                    <th>Vehicle Name</th>
                                    <th>Employee / Driver</th>
                                    <th>Service Due Every KM</th>
                                    <th>Air Filter Days</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($vehicles) > 0)
                                @foreach($vehicles as $key => $value)
                                <tr>
                                    <td>{{ $vehicles->firstItem() + $key }}</td>
                                    <td>{{ $value->vehicle_number }}</td>
                                    <td>
                                        @php
                                        if(null != $value->getOwner) {
                                        echo $value->getOwner->name;
                                        } else {
                                        @endphp
                                        <a class="" href="{{ URL::to('employees') }}" title="Add / Update Unit">Add /
                                            Update Employee</a>
                                        @php
                                        }
                                        @endphp
                                    <td>{{ $value->service_due_every_km }}</td>
                                    <td>{{ $value->air_filter_after_days }}</td>
                                    <td>
                                        @if( Auth::user()->can('viewMenu:VehicleService') ||
                                        Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                                        <a class="btn btn-sm  btn-secondary"
                                            href="{{ URL::to('specific_vehicle_service/' . $value->id) }}"
                                            title="Vehicle Expense"><i class=" menu-icon fa fa-sm fa-wrench"></i></a>
                                        @endif
                                        <a href="{{ url('/vehicles/' . $value->id) }}" title="View Vehicle"><button
                                                class="btn btn-info btn-sm"><i class="fa fa-eye"
                                                    aria-hidden="true"></i></button></a>
                                        @if( Auth::user()->can('viewMenu:ActionVehicle') ||
                                        Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                                        <a href="{{ URL::to('vehicles/' . $value->id . '/edit') }}"
                                            title="Edit vehicle"><button class="btn btn-primary btn-sm"><i
                                                    class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                        <!--{!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/vehicles', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                                <!-- {!! Form::button('Delete', array(
                                                        'type' => 'submit',
                                                        'class' => 'btn btn-danger btn-small',
                                                        'title' => 'Delete Vehicle',
                                                        'onclick'=>'return confirm("Confirm delete?")'
                                                )) !!}
                                            {!! Form::close() !!} -->
                                        <!--<button type="submit" class="btn btn-danger btn-sm" title="Delete Broker" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-sm fa-remove"></i></button>-->
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
                        <div class="pagination"> {!! $vehicles->appends(['search' => Request::get('search')])->render()
                            !!} </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
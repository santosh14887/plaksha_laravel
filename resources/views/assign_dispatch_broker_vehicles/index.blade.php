@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
<div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Dispatches</li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/dispatches') }}">Dispatches</a></li>
        <li class="breadcrumb-item active" aria-current="page">Broker Vehicle</li>
    </ol>
</nav>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">Broker Vehicle for Dispatch (
                {{ $assigned_dispatch->getDispatch->getCustomer->company_name.' '.$assigned_dispatch->getDispatch->start_time}})<br>
                Broker Name :- {{$assigned_dispatch->getUser->name}}
                <span class="float_right">
                    @if(count($assign_dispatche_broker_vehicles) > 0)
                    <a class="btn btn-primary btn-sm"
                        href="{{ URL::to('assign_dispatch_broker_vehicles/' . $assigned_dispatch->id . '/edit') }}"
                        title="Edit Dispatch">
                        <i class=" menu-icon fa fa-lg fa-edit"> Update Vehicles</i></a>
                    @else
                    <a class="btn btn-success btn-sm"
                        href="{{ URL::to('assign_dispatch_broker_vehicles/create/'.$assigned_dispatch->id) }}"><i
                            class="fa fa-plus" aria-hidden="true"></i> Assign Vehicles</a>
                    @endif

                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Driver Name</th>
                                <th>Vehicle Number</th>
                                <th>Contact Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($assign_dispatche_broker_vehicles) > 0)
                            @foreach($assign_dispatche_broker_vehicles as $key => $value)
                            <tr>
                                <td>{{ (null !== $value->driver_name) ? ucfirst($value->driver_name) : '-' }}</td>
                                <td>{{ (null !== $value->vehicle_number) ? $value->vehicle_number : '-' }}</td>
                                <td>{{ (null !== $value->contact_number) ? $value->contact_number : '-' }}</td>
                                <!-- <td>
                                    {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/assigned_dispatch', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-small" title="Delete Dispatch" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>
                                        <a class="btn btn-small btn-info" href="{{ URL::to('assigned_dispatch/' . $value->id . '/edit') }}" title="Edit Dispatch"><i class=" menu-icon fa fa-lg fa-edit"></i></a>

                                    </td> -->
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="3">No data Found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
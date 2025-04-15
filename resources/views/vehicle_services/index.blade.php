@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
<div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Vehicles Units</li>
        <li class="breadcrumb-item active" aria-current="page">Expense List</li>
    </ol>
</nav>
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">Vehicle Expense for ( {{ $vehicles->vehicle_number}})<span class="float_right">
                    @if(request('search') != '')
                    <a href="{{ url('/users') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i
                                class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
                    @endif
                    @if( Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') ||
                    Auth::user()->type == 'admin')
                    <a href="{{ URL::to('vehicle_services/create/'.$vehicles->id) }}" class="btn btn-success btn-sm"
                        title="Add Expense">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Expense
                    </a>
                    @endif
                </span></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Km</th>
                                <th>Expense Category</th>
                                <th>SUb Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($vehicle_services) > 0)
                            @foreach($vehicle_services as $key => $value)
                            <tr>
                                <td>{{ $vehicle_services->firstItem() + $key }}</td>
                                <td>{{ (null !== $value->on_date) ? $value->on_date : '-' }}</td>
                                <td>{{ (null !== $value->expense_amount && $value->expense_amount > 0) ? $value->expense_amount : '-' }}
                                </td>
                                <td>{{ (null !== $value->on_km && $value->on_km > 0) ? $value->on_km : '-' }}</td>
                                <td>{{ (null !== $value->parent_service_type) ? ucwords(str_replace('_',' ',$value->parent_service_type)) : '-' }}
                                </td>
                                <td>{{ (null !== $value->service_type) ? ucwords(str_replace('_',' ',$value->service_type)) : '-' }}
                                </td>
                                <td>
                                    @if( Auth::user()->can('viewMenu:ActionVehicleService') ||
                                    Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                                    <a href="{{ URL::to('vehicle_services/' . $value->id . '/edit') }}"
                                        title="Edit Expense"><button class="btn btn-primary btn-sm"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                    {!! Form::open([
                                    'method' => 'DELETE',
                                    'url' => ['/vehicle_services', $value->id],
                                    'style' => 'display:inline'
                                    ]) !!}
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Expense"
                                        onclick="return confirm('Confirm delete?')"><i
                                            class=" menu-icon fa fa-lg fa-remove"></i></button>
                                    @endif

                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="7">No data Found</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="pagination"> {!! $vehicle_services->appends(['search' =>
                        Request::get('search')])->render() !!} </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
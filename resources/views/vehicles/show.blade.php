@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Vehicles</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/vehicles') }}">Vehicles Units</a></li>
				<li class="breadcrumb-item active" aria-current="page">View Vehicles</li>
			</ol>
		</nav>
        <div class="row">

            <div class="col-md-12 show_page">
                <div class="card">
                    <div class="card-header">Vehicles/Units</div>
                    <div class="card-body">

                        <a href="{{ url('/vehicles') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
						@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                        <a href="{{ url('/vehicles/' . $vehicles->id . '/edit') }}" title="Edit Vehicles"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                       <!-- {!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/vehicles', $vehicles->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Vehicle',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!}-->
						@endif
                        <br/>
                        <br/>
						<div class="example">
						  <ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
							<li class="nav-item">
							  <a class="nav-link active" id="overview-line-tab" data-bs-toggle="tab" href="#line-overview" role="tab" aria-controls="line-overview" aria-selected="true">Overview</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link" id="Specifications-line-tab" data-bs-toggle="tab" href="#line-Specifications" role="tab" aria-controls="line-Specifications" aria-selected="false">Specifications</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link" id="assignHistory-line-tab" data-bs-toggle="tab" href="#line-assignHistory" role="tab" aria-controls="line-assignHistory" aria-selected="false">Vehicle Assignment History</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link" id="fuelHistory-line-tab" data-bs-toggle="tab" href="#line-fuelHistory" role="tab" aria-controls="line-fuelHistory" aria-selected="false">Fuel History</a>
							</li>
							<!--<li class="nav-item">
							  <a class="nav-link" id="expensesHistory-line-tab" data-bs-toggle="tab" href="#line-expensesHistory" role="tab" aria-controls="line-expensesHistory" aria-selected="false">Expenses History</a>
							</li>-->
							<li class="nav-item">
							  <a class="nav-link" id="serviceReminder-line-tab" data-bs-toggle="tab" href="#line-serviceReminder" role="tab" aria-controls="line-serviceReminder" aria-selected="false">Service Reminders</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link" id="serviceHistory-line-tab" data-bs-toggle="tab" href="#line-serviceHistory" role="tab" aria-controls="line-serviceHistory" aria-selected="false">Expense History</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link" id="meterHistory-line-tab" data-bs-toggle="tab" href="#line-meterHistory" role="tab" aria-controls="line-meterHistory" aria-selected="false">Meter History</a>
							</li>
							<li class="nav-item">
							  <a class="nav-link" id="issueHistory-line-tab" data-bs-toggle="tab" href="#line-issueHistory" role="tab" aria-controls="line-issueHistory" aria-selected="false">Issues</a>
							</li>
						  </ul>
						  <div class="tab-content mt-3" id="lineTabContent">
							<div class="tab-pane fade show active" id="line-overview" role="tabpanel" aria-labelledby="overview-line-tab">
							  @include('vehicles.view_overview_fields')
							</div>
							<div class="tab-pane fade" id="line-Specifications" role="tabpanel" aria-labelledby="Specifications-line-tab">
							  @include('vehicles.view_specifications_fields')
							</div>
							<div class="tab-pane fade" id="line-assignHistory" role="tabpanel" aria-labelledby="assignHistory-line-tab">
							  @include('vehicles.view_assignHistory_fields')
							</div>
							<div class="tab-pane fade" id="line-fuelHistory" role="tabpanel" aria-labelledby="fuelHistory-line-tab">
							  @include('vehicles.view_fuelHistory_fields')
							</div>
							<!--<div class="tab-pane fade" id="line-expensesHistory" role="tabpanel" aria-labelledby="expensesHistory-line-tab">
							  @include('vehicles.view_expensesHistory_fields')
							</div>-->
							<div class="tab-pane fade" id="line-serviceReminder" role="tabpanel" aria-labelledby="ServiceReminder-line-tab">
							  @include('vehicles.view_serviceReminder_fields')
							</div>
							<div class="tab-pane fade" id="line-serviceHistory" role="tabpanel" aria-labelledby="serviceHistory-line-tab">
							  @include('vehicles.view_serviceHistory_fields')
							</div>
							<div class="tab-pane fade" id="line-meterHistory" role="tabpanel" aria-labelledby="meterHistory-line-tab">
							  @include('vehicles.view_meterHistory_fields')
							</div>
							<div class="tab-pane fade" id="line-issueHistory" role="tabpanel" aria-labelledby="issueHistory-line-tab">
							  @include('vehicles.view_issueHistory_fields')
							</div>
						  </div>
						</div>
                    </div>
                </div>
            </div>
        </div>
@endsection

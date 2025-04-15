@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Dispatches</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/dispatches') }}">Dispatches</a></li>
				<li class="breadcrumb-item active" aria-current="page">View Order</li>
			</ol>
		</nav>
        <div class="row">
            <div class="col-md-12 show_page">
                <div class="card">
                    <div class="card-header">View Dispatch</div>
                    <div class="card-body">

                        <a href="{{ url('/dispatches') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
						@if(Auth::user()->can('viewMenu:ActionDispatch') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
						@if($dispatches->status == 'pending')
                        <a href="{{ url('/dispatches/' . $dispatches->id . '/edit') }}" title="Edit Dispatchs"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
					@endif
					@endif
                       <!-- {!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/dispatches', $dispatches->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Dispatch',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!}-->
                        <br/>
                        <br/>
						@php
								$accepted_count = 0;
								if(null !== $dispatches->getAssignDispatchAssign) {
									foreach($dispatches->getAssignDispatchAssign as $val)
									$accepted_count += $val->no_of_vehicles;
								}
							@endphp
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
								<tr>
								  <th scope="row">Order Status</th>
								  <td>{{ ucfirst($dispatches->status) }}</td>
								  <!--<th scope="row">Ticket Number</th>
								   <td><td>{{ $dispatches->default_dispatch_number }}</td></td>-->
								</tr>
								<tr>
								  <th scope="row">Customer Name</th>
								  <td>{{ (null != $dispatches->customer_company_name) ? $dispatches->customer_company_name :  $dispatches->getCustomer->company_name }}</td>
								  <th scope="row">Start Time</th>
								   <td>{{ $dispatches->start_time }}</td>
								</tr>
								<tr>
								  <th scope="row">Start Location</th>
								  <td>{{ $dispatches->start_location }}</td>
								  <th>Dump Location</th>
								  <td>{{ $dispatches->dump_location }}</td>
								</tr>
								<tr>
								  <th scope="row">Job Type</th>
								  <td>{{ ($dispatches->job_type == 'load') ? 'Rate per load' : ucfirst($dispatches->job_type) }}</td>
								  <th scope="row">Job Rate</th>
								  <td> {{ $dispatches->job_rate }} </td>
								</tr>
								<tr>
								  <th scope="row">Employee Rate</th>
								  <td>{{ $dispatches->employee_rate }}</td>
								  <th scope="row"></th>
								  <td></td>
								</tr>
								<tr>
								  <th scope="row">Required Unit</th>
								  <td><a href="{{ URL::to('assigned_dispatche/' . $dispatches->id) }}" title="Assign Order">{{ $dispatches->required_unit }} (Assign Order)</a></td>
								  <th scope="row">Accepted Unit</th>
								  <td> <b> {{ $accepted_count }} </b></td>
								</tr>
								<tr>
								  <th scope="row">Supervisor Name</th>
								  <td>{{ $dispatches->supervisor_name }}</td>
								  <th scope="row">Supervisor Contact</th>
								  <td>{{ $dispatches->supervisor_contact }}</td>
								</tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection

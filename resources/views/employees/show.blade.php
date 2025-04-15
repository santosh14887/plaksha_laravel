@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Users</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/employees') }}">Employees</a></li>
				<li class="breadcrumb-item active" aria-current="page">View Employee</li>
			</ol>
		</nav>
        <div class="row">

            <div class="col-md-12 show_page">
                <div class="card">
                    <div class="card-header">View Employee</div>
                    <div class="card-body">

                        <a href="{{ url('/employees') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
						@if(Auth::user()->can('viewMenu:ActionEmployee') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
                        <a href="{{ url('/employees/' . $employees->id . '/edit') }}" title="Edit Employees"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                        <!--{!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/employees', $employees->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Employee',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!}-->
						@endif
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
								<tr>
								  <th scope="row">First Name</th>
								  <td>{{ $employees->first_name }}</td>
								  <th scope="row">Last Name</th>
								  <td>{{ $employees->last_name }}</td>
								</tr>
								<tr>
								  <th scope="row">Email</th>
								  <td>{{ $employees->email }}</td>
								  <th>Phone</th>
								  <td>{{ $employees->country_code.' '.$employees->phone }}</td>
								</tr>
								<tr>
								  <th scope="row">Address</th>
								  <td>{{ $employees->address }}</td>
								  <th>Zip Code</th>
								  <td>{{ $employees->zip_code }}</td>
								</tr>
								<tr>
								  <th scope="row">Unit Number</th>
								  <td>
									@if(isset($employees->getVehicle->vehicle_number))
									{{ $employees->getVehicle->vehicle_number }}
										@else
											<span style="color:red;"><b>No Unit Assigned</b></span>
									@endif
									</td>
								  <th scope="row">License Number</th>
								  <td>
									@if(isset($employees->license_number))
									{{ $employees->license_number }}
										@else
											<span style="color:red;"><b>No Licience Number</b></span>
									@endif
									</td>
								</tr>
								<tr>
								  <th scope="row">Company / Corporate Name</th>
								  <td>
									@if(isset($employees->company_corporation_name))
									{{ $employees->company_corporation_name }}
										@else
											<span style="color:red;"><b>No Corporate Name</b></span>
									@endif
									</td>
								  <th scope="row">HST</th>
								  <td>{{ $employees->hst }}</td>
								</tr>
								<tr>
								  <th scope="row">Hourly Rate</th>
								  <td>{{ $employees->hourly_rate }}</td>
								  <th>Unit /Vehicle</th>
								  <td>
								   @php
                                    if(null != $employees->getVehicle) {
                                    echo $employees->getVehicle->vehicle_number;
                                    } else {
                                    @endphp
                                    <a class="" href="{{ URL::to('employees/' . $employees->id . '/edit') }}"
                                        title="Add / Update Unit">Add / Update Unit</a>
                                    @php
                                    }
                                    @endphp
								  </td>
								</tr>
								<tr>
								  <th scope="row">Status</th>
								  <td>{{ ucfirst($employees->status) }}</td>
								</tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection

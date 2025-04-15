@extends('layouts.after_login')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-12 show_page">
                <div class="card">
                    <div class="card-header">User Issue</div>
                    <div class="card-body">

                        <a href="{{ url('/vehicle_assignment_histories') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
						@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
							@if($vehicle_assignment_histories->user_vehicle_type == 'temporary')
                        <a href="{{ url('/vehicle_assignment_histories/' . $vehicle_assignment_histories->id . '/edit') }}" title="Edit Assignment"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                        <!--{!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/vehicle_assignment_histories', $vehicle_assignment_histories->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Issue',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!}
                        <br/>-->
						@endif
						@endif
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
								<tr>
								  <th scope="row">User Name</th>
								  <td>{{ (null!== $vehicle_assignment_histories->user_name) ? $vehicle_assignment_histories->user_name : '' }}</td>
								  <th scope="row">User Email</th>
								  <td>{{ $vehicle_assignment_histories->user_email }}</td>
								</tr>
								<tr>
								  <th scope="row">vehicle</th>
								  <td>{{ $vehicle_assignment_histories->vehicle_number }}</td>
								  <th>Start Time</th>
								  <td>{{ date('d M Y h:i:s',strtotime($vehicle_assignment_histories->start_time)) }}</td>
								</tr>
								<tr>
								  <th scope="row">End Time</th>
								  <td>{{ (null !== $vehicle_assignment_histories->end_time) ? date('d M Y h:i:s',strtotime($vehicle_assignment_histories->end_time)) : '-' }}</td>
								</tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

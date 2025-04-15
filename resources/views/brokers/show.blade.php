@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Users</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/brokers') }}">Brokers</a></li>
				<li class="breadcrumb-item active" aria-current="page">View Broker</li>
			</ol>
		</nav>
        <div class="row">

            <div class="col-md-12 show_page">
                <div class="card">
                    <div class="card-header">View Broker</div>
                    <div class="card-body">

                        <a href="{{ url('/brokers') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
						@if(Auth::user()->can('viewMenu:ActionBroker') || Auth::user()->can('viewMenu:All') ||  Auth::user()->type == 'admin')
                        <a href="{{ url('/brokers/' . $brokers->id . '/edit') }}" title="Edit Brokers"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                        <!--{!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/brokers', $brokers->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Broker',
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
								  <td>{{ $brokers->first_name }}</td>
								  <th scope="row">Last Name</th>
								  <td>{{ $brokers->last_name }}</td>
								</tr>
								<tr>
								  <th scope="row">Email</th>
								  <td>{{ $brokers->email }}</td>
								  <th>Wsib Quarterly</th>
								  <td>{{ $brokers->wsib_quarterly }}</td>
								</tr>
								<tr>
								  <th scope="row">Incorporation Name</th>
								  <td>{{ $brokers->incorporation_name }}</td>
								  <th scope="row">Phone</th>
								  <td>{{ $brokers->country_code.' '.$brokers->phone }}</td>
								</tr>
								<tr>
								  <th scope="row">Address</th>
								  <td>{{ $brokers->address }}</td>
								  <th scope="row">Zip Code</th>
								  <td>{{ $brokers->zip_code }}</td>
								</tr>
								<tr>
								  <th scope="row">Hourly Rate</th>
								  <td>{{ $brokers->hourly_rate }}</td>
								  <!--<th>Load Per</th>
								  <td>{{ $brokers->load_per }}</td>-->
								</tr>
								<tr>
								  <th scope="row">Available Unit</th>
								  <td>{{ $brokers->available_unit }}</td>
								  <th scope="row">Status</th>
								  <td>{{ ucfirst($brokers->status) }}</td>
								</tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection

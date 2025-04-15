@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Users</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/customers') }}">Customers</a></li>
				<li class="breadcrumb-item active" aria-current="page">View Customer</li>
			</ol>
		</nav>
        <div class="row">

            <div class="col-md-12 show_page">
                <div class="card">
                    <div class="card-header">View Customer</div>
                    <div class="card-body">

                        <a href="{{ url('/customers') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <a href="{{ url('/customers/' . $customers->id . '/edit') }}" title="Edit Customers"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                        <!--{!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/customers', $customers->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Customer',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!}-->
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
								<tr>
								  <th scope="row">Company Name</th>
								  <td>{{ $customers->company_name }}</td>
								  <th scope="row">HST</th>
								  <td>{{ $customers->customer_hst }}</td>
								</tr>
								<tr>
								  <th scope="row">Address</th>
								  <td>{{ $customers->street_line.' ,'.$customers->city.' , '.$customers->postal_code.' , '.$customers->country }}</td>
								  
								</tr>
								<!--<tr>
								<th>Hourly Rate</th>
								  <td>{{ $customers->hourly_rate }}</td>
								  <th scope="row">Rate per load</th>
								  <td>{{ ( $customers->rate_per_load == 'load') ? 'Rate per load ' : $customers->rate_per_load }}</td>
								</tr>-->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection

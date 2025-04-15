@extends('layouts.after_login')

@section('content')
	<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Settings</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/fules') }}">Fuel</a></li>
				<li class="breadcrumb-item active" aria-current="page">View Fuel Price</li>
			</ol>
		</nav>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">View Fuel Price</div>
                    <div class="card-body">

                        <a href="{{ url('/fules') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
						 @if( Auth::fules()->can('viewMenu:ActionFuel') || Auth::fules()->can('viewMenu:All') || Auth::fules()->type == 'admin')
                        <a href="{{ url('/fules/' . $fules->id . '/edit') }}" title="Edit Fuel Price"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                        <!-- {!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/fules', $fules->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete User',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!} -->
						@endif
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th><th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> {{ date("d M Y", strtotime($fules->on_date)) }} </td>
										<td> {{ $fules->amount }} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

@endsection

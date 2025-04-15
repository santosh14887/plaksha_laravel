@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Vehicles</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/fuel_histories') }}">Fuel Histories</a></li>
			</ol>
		</nav>
        <div class="row">

            <div class="col-md-12 show_page">
                <div class="card">
                    <div class="card-header">View Fuel History</div>
                    <div class="card-body">

                        <a href="{{ url('/fuel_histories') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
								<tr>
								  <th scope="row">Vehicle Number</th>
								  <td>{{ $fuel_histories->vehicle_number }}</td>
								  <th scope="row">Total Km</th>
								  <td>{{ $fuel_histories->total_km }}</td>
								</tr>
								<tr>
								  <th scope="row">Starting Km</th>
								  <td>{{ $fuel_histories->starting_km }}</td>
								  <th scope="row">Ending Km</th>
								  <td>{{ $fuel_histories->ending_km }}</td>
								</tr>
								<tr>
								  <th scope="row">Fuel Quantity</th>
								  <td>{{ $fuel_histories->fuel_qty }}</td>
								  <th scope="row">Fuel date</th>
								  <td>{{ $fuel_histories->on_date }}</td>
								</tr>
								<tr>
								  <th scope="row">Fuel Card Number</th>
								  <td>{{ $fuel_histories->fuel_card_number }}</td>
								  <th scope="row"></th>
								  <td></td>
								</tr>
								<tr>
								  <th scope="row">Fuel Receipt</th>
								  <td>
								  @if($fuel_histories->source == 'Manual')
									@if(null != $fuel_histories->fuel_receipt)
									  <img class="download_img" title="Click here to download" src="{{ asset('images/fuel_receipt/'.$fuel_histories->fuel_receipt) }}" data-id="{{ asset('images/fuel_receipt/'.$fuel_histories->fuel_receipt) }}"  />
									@endif
									@else  
									@if(null != $fuel_histories->getTicket->fuel_receipt)
									  <img class="download_img" title="Click here to download" src="{{ asset('images/fuel_receipt/'.$fuel_histories->getTicket->fuel_receipt) }}" data-id="{{ asset('images/fuel_receipt/'.$fuel_histories->getTicket->fuel_receipt) }}"  />
									@endif	
									@endif
								  </td>
								  <th scope="row">Fuel Expense</th>
								  <td>{{ ($fuel_histories->fuel_expense > 0) ? $fuel_histories->fuel_expense : '' }}</td>
								</tr>
								<tr>
								  <th scope="row">Source</th>
								  <td>{{ $fuel_histories->source }}</td>
								  <th scope="row">Comment</th>
								  <td>{{ $fuel_histories->comment }}</td>
								</tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection

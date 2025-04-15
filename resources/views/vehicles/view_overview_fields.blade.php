<div class="table-responsive">
                            <table class="table">
                                <tbody>
								<tr>
								  <th scope="row">Vehicle Name</th>
								  <td>{{ $vehicles->vehicle_number }}</td>
								  <th scope="row">Service Due Every KM</th>
								  <td>{{ $vehicles->service_due_every_km }}</td>
								</tr>
								<tr>
								  <th scope="row">Air Filter Days</th>
								  <td>{{ $vehicles->air_filter_after_days }}</td>
								  <th>Licence Plate</th>
								  <td>{{ $vehicles->licence_plate }}</td>
								</tr>
								<tr>
								  <th scope="row">Vin Number</th>
								  <td>{{ $vehicles->vin_number }}</td>
								  <th>Annual Safty Renewal</th>
								  <td>{{ $vehicles->annual_safty_renewal }}</td>
								</tr>
								<tr>
								  <th scope="row">Total Km</th>
								  <td>{{ $vehicles->total_km }}</td>
								  <th scope="row">Last Air Filter Date</th>
								  <td>{{ $vehicles->last_air_filter_date }}</td>
								</tr>
								<tr>
								  <th scope="row">Licence Plate Sticker</th>
								  <td>{{ $vehicles->licence_plate_sticker }}</td>
								  <th scope="row">Employee / Driver</th>
								  <td>@php
                                    if(null != $vehicles->getOwner) {
                                    echo $vehicles->getOwner->name;
                                    } else {
                                    @endphp
                                    <a class="" href="{{ URL::to('employees') }}"
                                        title="Add / Update Unit">Add / Update Employee</a>
                                    @php
                                    }
                                    @endphp</td>
								</tr>
								
                                </tbody>
                            </table>
                        </div>
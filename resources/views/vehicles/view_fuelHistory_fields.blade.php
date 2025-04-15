<div class="table-responsive">
@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
				  <a href="{{ url('/fuel_histories/create?vehicle_id='.$vehicles->id) }}" class="btn btn-success btn-sm float_right margin_bottom_one_per"  title="Add Fuel History">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Fuel History
                        </a>
						@endif
                            <table id="fuel_meter_history" class="table datatable table-hover">
							<thead>
							<tr>
							<th>S.No</th>
							<th>Date</th>
							<th>Meter Entry</th>
							<th>Fuel Economy</th>
							<th>Receipt</th>
							<th>Fuel Qty</th>
							<th>Fuel Price</th>
							<th>Fuel Expense</th>
							<th>Source</th>
							<th>Action</th>
							</tr>
							</thead>
                                <tbody>
								@php
								$getFuelHistory = $vehicles->getFuelHistory;
								if(null !== $getFuelHistory) {
									$fuel_count_start = 1;
									foreach($getFuelHistory as $getIssue_parm => $value) {
										$tr_bg_color = 'table-warning';
										if($fuel_count_start % 2 == 0) {
											$tr_bg_color = 'table-info';
										} else{}
									@endphp
									<tr class="{{ $tr_bg_color }}">
									<td>{{ $fuel_count_start }}</td>
									<td>{{ $value->on_date }}</td>
									<td>{{ $value->starting_km }}</td>
									<td>{{ $value->fuel_economy }}</td>
									<td>
										@if($value->source == 'Manual')
										@if(null != $value->fuel_receipt)
										  <img class="download_img" title="Click here to download" src="{{ asset('images/fuel_receipt/'.$value->fuel_receipt) }}" data-id="{{ asset('images/fuel_receipt/'.$value->fuel_receipt) }}"  />
										@endif
										@else  
										@if(null != $value->getTicket->fuel_receipt)
										  <img class="download_img" title="Click here to download" src="{{ asset('images/fuel_receipt/'.$value->getTicket->fuel_receipt) }}" data-id="{{ asset('images/fuel_receipt/'.$value->getTicket->fuel_receipt) }}"  />
										@endif	
										@endif
									</td>
									<td>{{ $value->fuel_qty }}</td>
									<td>{{ $value->per_liter_amount }}</td>
									<td>{{ $value->fuel_expense }}</td>
                                    <td>
									@if($value->source != 'Manual')
										@if(null != $value->getTicket)
										{{ $value->getTicket->getDispatch->customer_company_name.'('.$value->getTicket->getDispatch->start_location.' TO '.$value->getTicket->getDispatch->dump_location.' '.$value->getTicket->getDispatch->start_time.')'}}
									@endif
									@else
									{{ $value->source }}
									@endif
									</td>
                                    <td>
                                    @if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
									@if($value->source == 'Manual')
										<a href="{{ url('/fuel_histories/' . $value->id) }}" title="View Fuel History"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
                                     {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/fuel_histories', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                                {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i>', array(
                                                        'type' => 'submit',
                                                        'class' => 'btn btn-danger btn-sm',
                                                        'title' => 'Delete History',
                                                        'onclick'=>'return confirm("Confirm delete?")'
                                                )) !!}
                                            {!! Form::close() !!}
									@endif
									@endif
                                    </td>
									</tr>
									@php
									$fuel_count_start++;
									}
								} else {
									echo 'Not yet added';
								}
								@endphp
                                </tbody>
                            </table>
                        </div>
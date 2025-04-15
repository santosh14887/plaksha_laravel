<div class="table-responsive">
@if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
				  <a href="{{ url('/meter_histories/create?vehicle_id='.$vehicles->id) }}" class="btn btn-success btn-sm float_right margin_bottom_one_per" title="Add Meter History">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Meter History
                        </a>
						@endif
                            <table id="vehicle_meter_history" class="table datatable table-hover">
							<thead>
							<tr>
							<th>S.No</th>
							<th>Date</th>
							<th>Meter</th>
							<th>Source</th>
							<th>Comment</th>
							<th>Action</th>
							</tr>
							</thead>
                                <tbody>
								@php
								$getMeterHistory = $vehicles->getMeterHistory;
								if(null !== $getMeterHistory && count($getMeterHistory) > 0) {
									$meter_count_start = 1;
									foreach($getMeterHistory as $getIssue_parm => $value) {
										$tr_bg_color = 'table-warning';
										if($meter_count_start % 2 == 0) {
											$tr_bg_color = 'table-info';
										} else{}
									@endphp
									<tr class="{{ $tr_bg_color }}">
									<td>{{ $meter_count_start }}</td>
									<td>{{ $value->on_date }}</td>
									<td>{{ $value->total_km }}</td>
                                    <td>
									@if($value->source != 'Manual')
										@if(null != $value->getTicket)
										{{ $value->getTicket->getDispatch->customer_company_name.'('.$value->getTicket->getDispatch->start_location.' TO '.$value->getTicket->getDispatch->dump_location.' '.$value->getTicket->getDispatch->start_time.')'}}
									@endif
									@else
									{{ $value->source }}
									@endif
									</td>
                                    <td>{{ $value->comment }}</td>
                                    <td>
                                    @if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
									@if($value->source == 'Manual')
                                     {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/meter_histories', $value->id],
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
									$meter_count_start++;
									}
								} else {
									echo '<tr><td colspan="6" style="position: relative;text-align: center;">No data available in table</td></tr>';
								}
								@endphp
                                </tbody>
                            </table>
                        </div>
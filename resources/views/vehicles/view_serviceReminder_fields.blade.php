<div class="table-responsive">
@if( Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
					<a href="{{ URL::to('vehicle_services/create/'.$vehicles->id) }}" class="btn btn-success btn-sm float_right margin_bottom_one_per" title="Add New Permission">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Service
                    </a>
					@endif
                            <table id="service_reminder_history" class="table datatable table-hover">
							<thead>
							<tr>
							<th>S.No</th>
							<th>Due Airfilter Service</th>
							<th>Current Km</th>
							<th>Normal Service on KM</th>
							</tr>
							</thead>
                                <tbody>
								@if(null != $vehicles->due_air_filter_date || $total_run_km > 0 || $service_on_km > 0)
									<tr class="table-warning">
									<td>1</td>
									<td>{{ (null != $vehicles->due_air_filter_date) ? date('d M Y',strtotime($vehicles->due_air_filter_date)) : '' }}</td>
									<td>{{ ($total_run_km > 0 ) ? $total_run_km : ''}}</td>
									<td>{{ ($service_on_km > 0 ) ? $service_on_km : ''}}</td>
									<tr>
									@else
										<tr><td colspan="4" style="position: relative;text-align: center;">No data available in table</td></tr>
									@endif
									</tr>
                                </tbody>
                            </table>
                        </div>
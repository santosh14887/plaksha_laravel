<div class="table-responsive">
                            <table id="vehicle_assign_history" class="table datatable table-hover">
							<thead>
							<tr>
							<th>S.No</th>
							<th>User Name</th>
							<th>Email</th>
							<th>Vehicle</th>
							<th>Start Time</th>
							<th>End Time</th>
							<!--<th>Comment</th>
							<th>Action</th>-->
							</tr>
							</thead>
                                <tbody>
								@php
								$getVehicleAssignmentHistory = $vehicles->getVehicleAssignmentHistory;
								if(null !== $getVehicleAssignmentHistory) {
									$assign_count_history = 1;
									foreach($getVehicleAssignmentHistory as $getIssue_parm => $value) {
										$tr_bg_color = 'table-warning';
										if($assign_count_history % 2 == 0) {
											$tr_bg_color = 'table-info';
										} else{}
									@endphp
									<tr class="{{ $tr_bg_color }}">
									<td>{{ $assign_count_history }}</td>
									<td>{{ $value->user_name }}</td>
									<td>{{ $value->user_name }}</td>
									<td>{{ $value->vehicle_number }}</td>
									<td>{{ (null !== $value->start_time) ? date('d M Y h:i:s',strtotime($value->start_time)) : '-' }}</td>
									<td>{{ (null !== $value->end_time) ? date('d M Y h:i:s',strtotime($value->end_time)) : '-' }}</td>
                                   <!-- <td>{{ $value->comment }}</td>
                                    <td>-->
                                    @if( Auth::user()->can('viewMenu:ActionVehicle') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
										@if($value->user_vehicle_type == 'temporary')
										<!-- <a class="" href="{{ URL::to('vehicle_assignment_histories/' . $value->id . '/edit') }}" title="Edit Dispatch"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a> -->
                                     <!-- {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/vehicle_assignment_histories', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                                {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i>', array(
                                                        'type' => 'submit',
                                                        'class' => 'btn btn-danger btn-sm',
                                                        'title' => 'Delete History',
                                                        'onclick'=>'return confirm("Confirm delete?")'
                                                )) !!}
                                            {!! Form::close() !!} -->
									@endif
									@endif
                                    <!-- </td> -->
									</tr>
									@php
									$assign_count_history++;
									}
								} else {
									echo 'Not yet added';
								}
								@endphp
                                </tbody>
                            </table>
                        </div>
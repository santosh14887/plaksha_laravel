<div class="table-responsive">
                            <table id="vehicle_issue_history" class="table datatable table-hover">
							<thead>
							<tr>
							<th>S.No</th>
							<th>Name</th>
							<th>Email</th>
							<th>Contact</th>
							<th>Issue Category</th>
							<th>Title</th>
							<th>Description</th>
							<th>Status</th>
							<th>Action</th>
							</tr>
							</thead>
                                <tbody>
								@php
								$getIssue = $vehicles->getIssue;
								if(null !== $getIssue && count($getIssue) > 0) {
									$issue_count_start = 1;
									foreach($getIssue as $getIssue_parm => $value) {
										$tr_bg_color = 'table-warning';
										if($issue_count_start % 2 == 0) {
											$tr_bg_color = 'table-info';
										} else{}
									@endphp
									<tr class="{{ $tr_bg_color }}">
									
										<td>{{ $issue_count_start }}</td>
										<td>{{ (null !== $value->user_name) ? ucfirst($value->user_name) : '-' }}</td>
										<td>{{ (null !== $value->user_email) ? ucfirst($value->user_email) : '-' }}</td>
										<td>{{ (null !== $value->user_phone) ? ucfirst($value->user_phone) : '-' }}</td>
                                    <td>{{ (null !== $value->getIssueCatgory) ? $value->getIssueCatgory->title : '-' }}</td>
                                    <td>{{ (null !== $value->title) ? $value->title : '-' }}</td>
                                    <td>{{ (null !== $value->description) ? $value->description : '-' }}</td>
                                    <td>{{ (null !== $value->status) ? ($value->status == 'on_hold') ? 'On Hold' : ucfirst($value->status) : '-' }}</td>
                                    <td>
                                    <!--{!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/user_issues', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-small" title="Delete Dispatch" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>-->
											<a class="btn btn-sm btn-info" href="{{ URL::to('/user_issues/' . $value->id) }}" title="View Issues"><i class=" menu-icon fa fa-lg fa-eye"></i></a>
										@if( Auth::user()->can('viewMenu:ActionUserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                                        <a class="" href="{{ URL::to('user_issues/' . $value->id . '/edit') }}" title="Edit Dispatch"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
										@endif

                                    </td>
									</tr>
									@php
									$issue_count_start++;
									}
								} else {
									echo '<tr><td colspan="9" style="position: relative;text-align: center;">No data available in table</td></tr>';
								}
								@endphp
                                </tbody>
                            </table>
                        </div>
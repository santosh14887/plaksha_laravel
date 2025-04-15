<div class="table-responsive">
                            <table class="table">
                                <tbody>
								@php
								$vehicle_desc = $vehicles->vehicle_desc;
								if($vehicle_desc != '' && $vehicle_desc != null) {
									$vehicle_desc = json_decode($vehicle_desc);
									unset($vehicle_desc->ErrorText);
									unset($vehicle_desc->VIN);
									unset($vehicle_desc->ErrorCode);
									$count_start = 1;
									foreach($vehicle_desc as $vehicle_parm => $vehicle_desc_val) {
										$data_val = $vehicle_desc_val;
									@endphp
									@if($count_start % 2 != 0)
									<tr>
									@endif
									
										<th scope="row">{{ $vehicle_parm }}</th>
										<td>{{ $data_val }}</td>
									@if($count_start % 2 == 0)
									</tr>
									@endif
									@php
									$count_start++;
									}
								} else {
									echo 'Not yet added';
								}
								@endphp
                                </tbody>
                            </table>
                        </div>
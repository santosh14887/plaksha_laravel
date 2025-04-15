<div class="table-responsive">
    @if( Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type
    == 'admin')
    <a href="{{ URL::to('vehicle_services/create/'.$vehicles->id) }}"
        class="btn btn-success btn-sm float_right margin_bottom_one_per" title="Add New Permission">
        <i class="fa fa-plus" aria-hidden="true"></i> Add Service
    </a>
    @endif
    <table id="vehicle_service_history" class="table datatable table-hover">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Date</th>
                <th>Km</th>
                <th>Expense Amount</th>
                <th>Expense Category</th>
                <th>SUb Category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
            $getServiceHistory = $vehicles->getServiceHistory;
            if(null !== $getServiceHistory && count($getServiceHistory) > 0) {
            $service_count_start = 1;
            foreach($getServiceHistory as $getIssue_parm => $value) {
            $tr_bg_color = 'table-warning';
            if($service_count_start % 2 == 0) {
            $tr_bg_color = 'table-info';
            } else{}
            @endphp
            <tr class="{{ $tr_bg_color }}">
                <td>{{ $service_count_start}}</td>
                <td>{{ (null !== $value->on_date) ? $value->on_date : '-' }}</td>
                <td>{{ (null !== $value->on_km) ? $value->on_km : '-' }}</td>
                <td>{{ (null !== $value->expense_amount && $value->expense_amount > 0) ? $value->expense_amount : '-' }}
                </td>
                <td>{{ (null !== $value->parent_service_type) ? ucwords(str_replace('_',' ',$value->parent_service_type)) : '-' }}
                </td>
                <td>{{ (null !== $value->service_type) ? ucwords(str_replace('_',' ',$value->service_type)) : '-' }}
                </td>
                <td>
                    @if( Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') ||
                    Auth::user()->type == 'admin')
                    <a href="{{ URL::to('vehicle_services/' . $value->id . '/edit') }}" title="Edit Service"><button
                            class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"
                                aria-hidden="true"></i></button></a>
                    {!! Form::open([
                    'method' => 'DELETE',
                    'url' => ['/vehicle_services', $value->id],
                    'style' => 'display:inline'
                    ]) !!}
                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Service"
                        onclick="return confirm('Confirm delete?')"><i
                            class=" menu-icon fa fa-lg fa-remove"></i></button>
                    @endif

                </td>
            </tr>
            @php
            $service_count_start++;
            }
            } else {
            echo '<tr>
                <td colspan="7" style="position: relative;text-align: center;">No data available in table</td>
            </tr>';
            }
            @endphp
        </tbody>
    </table>
</div>
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Vehicles</li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/meter_histories') }}">Meter History</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">History</li>
    </ol>
</nav>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">@if (isset($meter_histories) && null !== $meter_histories &&
                !empty($meter_histories))
                Update meter History for ( {{ $meter_histories->vehicle_number}})
                @else
                Add meter History
                @endif</div>
            <div class="card-body">
                @php
                $redirect_from_vehicle_page = '';
                $on_date_val = $vehicle_id_val = $total_km_val = $comment_val = '';
                if(null !== app('request')->input('id'))
                {
                echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
                }
                if(null !== app('request')->input('vehicle_id'))
                {
                $redirect_from_vehicle_page = app('request')->input('vehicle_id');
                }
                $on_date_val = (null !== old('on_date')) ? old('on_date') : '';
                $vehicle_id_val = (null !== old('vehicle_id')) ? old('vehicle_id') : '';
                $total_km_val = (null !== old('total_km')) ? old('total_km') : '';
                $comment_val = (null !== old('comment')) ? old('comment') : '';
                if(isset($meter_histories)){
                $on_date_val = (isset($meter_histories->on_date)) ? $meter_histories->on_date : '';
                $vehicle_id_val = (isset($meter_histories->vehicle_id)) ? $meter_histories->vehicle_id : '';
                $total_km_val = (isset($meter_histories->total_km)) ? $meter_histories->total_km : '';
                $comment_val = (isset($meter_histories->comment)) ? $meter_histories->comment : '';
                }
                if($on_date_val != '') {
                $on_date_val = date("Y-m-d", strtotime($on_date_val));
                }
                if($vehicle_id_val != '') {
                $redirect_from_vehicle_page = $vehicle_id_val;
                }
                @endphp
                <div class="row">
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {!! Form::label('on_date', 'Date *', ['class' => 'form-label','for'=>'acct_dateClosed']) !!}
                            <div class="input-group flatpickr" id="flatpickr-date">
                                <input type="text" class="form-control" name="on_date" value="{{ $on_date_val }}"
                                    placeholder="Select date" data-input="">
                                <span class="input-group-text input-group-addon" data-toggle=""><i
                                        data-feather="calendar"></i></span>
                            </div>

                            {!! $errors->first('on_date','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {!! Form::label('Name', 'Vehicle/Unit Number * ', ['class' => 'form-label']) !!}
                            <select id="vehicle_id" name="vehicle_id" class="form-control selectpicker"
                                data-live-search="true">
                                <option value="">Select Number</option>
                                @foreach ($vehicle as $key => $value)
                                @php
                                $unit_active = '';
                                if($vehicle_id_val == $key || $redirect_from_vehicle_page == $key) {
                                $unit_active = 'selected';
                                }
                                @endphp
                                <option value="{{ $key }}" {{ $unit_active }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('vehicle_id','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('total_km', 'Total Km *', ['class' => 'form-label']) }}
                            {{ Form::text('total_km', $total_km_val, array('class' => 'form-control','id' => 'total_km')) }}
                            {!! $errors->first('total_km','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        {{ Form::textarea('comment', $comment_val, array('class' => 'form-control','rows' => 5)) }}
                        {!! $errors->first('comment','<span class="help-inline text-danger">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary btn-sm'])
                    !!}
                    <a href="{{ url('/meter_histories') }}" title="Back"><span class="btn btn-warning btn-sm"><i
                                class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
                </div>
            </div>
        </div>
    </div>
</div>
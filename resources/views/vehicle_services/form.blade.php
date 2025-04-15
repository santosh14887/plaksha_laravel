<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Vehicles Units</li>
        <li class="breadcrumb-item active" aria-current="page"><a
                href="{{ url('/specific_vehicle_service/'.$vehicles->id) }}">Vehicles Expense</a></li>
        <li class="breadcrumb-item active" aria-current="page">Expense</li>
    </ol>
</nav>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">@if (isset($vehicle_services) && null !== $vehicle_services &&
                !empty($vehicle_services))
                Update Vehicle Expense for ( {{ $vehicles->vehicle_number}})
                @else
                Add Vehicle Expense for ( {{ $vehicles->vehicle_number}})
                @endif</div>
            <div class="card-body">
                @php
                $on_date_val = $service_type_val = $on_km_val = $comment_val = $expense_amount_val = '';
                $service_cat_id_val = '';
                if(null !== app('request')->input('id'))
                {
                echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
                }
                $on_date_val = (null !== old('on_date')) ? old('on_date') : '';
                $service_type_val = (null !== old('service_type')) ? old('service_type') : '';
                $service_cat_id_val = (null !== old('service_cat_id')) ? old('service_cat_id') : '';
                $on_km_val = (null !== old('on_km')) ? old('on_km') : '';
                $expense_amount_val = (null !== old('expense_amount')) ? old('expense_amount') : '';
                $comment_val = (null !== old('comment')) ? old('comment') : '';
                if(isset($vehicle_services)){
                $on_date_val = (isset($vehicle_services->on_date)) ? $vehicle_services->on_date : '';
                $service_type_val = (isset($vehicle_services->service_type)) ? $vehicle_services->service_type : '';
                $service_cat_id_val = (isset($vehicle_services->service_cat_id)) ? $vehicle_services->service_cat_id :
                '';
                $on_km_val = (isset($vehicle_services->on_km)) ? $vehicle_services->on_km : '';
                $comment_val = (isset($vehicle_services->comment)) ? $vehicle_services->comment : '';
                $expense_amount_val = (isset($vehicle_services->expense_amount) && $vehicle_services->expense_amount >
                0) ? $vehicle_services->expense_amount : '';
                }
                if($on_date_val != '') {
                $on_date_val = date("Y-m-d", strtotime($on_date_val));
                }

                @endphp
                <input type="hidden" name="prev_sucat_id" id="prev_sucat_id" value={{ $service_type_val }}>
                <div class="row">
                    <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $vehicles->id }}">
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {!! Form::label('on_date', 'Date *', ['class' => 'control-label','for'=>'acct_dateClosed'])
                            !!}
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
                            {!! Form::label('service_cat_id', 'Expense Category *', ['class' =>
                            'form-label','for'=>'roles']) !!}
                            <div class="select-wrap">
                                <select name="service_cat_id" id="service_cat_id"
                                    class="form-control service_cat_id form-item__element--select selectpicker"
                                    data-live-search="true">
                                    <option value="">Select Expense Category</option>
                                    @foreach($added_service_categories as $aKey => $aSport)
                                    @php
                                    $name = $aSport->name;
                                    $row_id = $aSport->id;
                                    $selected_val = '';
                                    if($row_id == $service_cat_id_val)
                                    {
                                    $selected_val = 'selected';
                                    }
                                    @endphp
                                    <option value="{{$row_id}}" {{ $selected_val }}>{{$name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            {!! $errors->first('service_cat_id', '
                            <p class="help-inline text-danger">:message</p>
                            ') !!}
                        </div>
                    </div><!-- Col -->
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {!! Form::label('service_type', 'Sub Category *', ['class' => 'form-label']) !!}
                            <select id="service_type" name="service_type"
                                class="form-control service_type select_height">
                                <option value="">Select Subcategory</option>
                            </select>
                            {!! $errors->first('service_type','<span class="help-inline text-danger">:message</span>')
                            !!}

                            <span class="service_type_err"></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('on_km', 'On Km') }}
                            {{ Form::text('on_km', $on_km_val, array('class' => 'form-control','id' => 'on_km')) }}
                            {!! $errors->first('on_km','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('expense_amount', 'Amount *') }}
                            {{ Form::text('expense_amount', $expense_amount_val, array('class' => 'form-control','id' => 'expense_amount')) }}
                            {!! $errors->first('expense_amount','<span class="help-inline text-danger">:message</span>')
                            !!}
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
                    <a href="{{ url('/specific_vehicle_service/'.$vehicles->id) }}" title="Back"><span
                            class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i>
                            Back</span></a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    var selected_id = $(".service_cat_id").val();
    user_type_fun(selected_id);
    jQuery(document).on('change', '.service_cat_id', function(e) {
        var selected_id = $(this).val();
        user_type_fun(selected_id);
    });

    function user_type_fun(selected_id) {
        $("#service_type").html('');
        $(".service_type_err").html('');
        var selected_id = selected_id;

        var prev_sucat_id = $("#prev_sucat_id").val();
        if (selected_id != '') {
            var options = {};
            options.type = 'GET';
            options.url = "{{url('get_service_subcategory')}}";
            options.data = {
                'selected_id': selected_id,
                'prev_sucat_id': prev_sucat_id
            };
            options.success = function(data) {
                $("#service_type").html(data);
            };
            options.error = function(data) {
                $("#service_type").html('');
            };
            $.ajax(options);
        } else {
            $("#service_type").html('<option value="">Select Expense Subcategory</option>');
        }

    }
});
</script>
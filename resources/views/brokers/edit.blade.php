@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Users</li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/brokers') }}">Brokers</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Broker</li>
    </ol>
</nav>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">Edit Broker</div>
            <div class="card-body">

                {{ Form::model($brokers, array('route' => array('brokers.update', $brokers->id), 'method' => 'PUT')) }}
                <div class="row">
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('name', 'First Name *', ['class' => 'form-label']) }}
                            {{ Form::text('first_name', Input::old('first_name'), array('class' => 'form-control')) }}
                            {!! $errors->first('first_name','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('name', 'Last Name *', ['class' => 'form-label']) }}
                            {{ Form::text('last_name', Input::old('last_name'), array('class' => 'form-control')) }}
                            {!! $errors->first('last_name','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('name', 'Email *', ['class' => 'form-label']) }}
                            {{ Form::text('email', Input::old('email'), array('class' => 'form-control')) }}
                            {!! $errors->first('email','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>

                </div>
                <div class="row">
                    @php
                    $pwd_str = '';
                    if(null != Input::old('password')) {
                    $pwd_str = Input::old('password');
                    } else {
                    $pwd_str = $brokers->password_string;
                    }
                    @endphp
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('name', 'Password', ['class' => 'form-label']) }}
                            {{ Form::text('password', $pwd_str, array('class' => 'form-control')) }}
                            {!! $errors->first('password','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        @php
                        $country_code = array('+1' => '+1','+91' => '+91')
                        @endphp
                        <div class="mb-3">
                            {{ Form::label('name', 'Phone *', ['class' => 'form-label']) }}
                            <div class="row">
                                <div class="col-sm-4 padding_right_zero">
                                    <select id="country_code" name="country_code" class="form-control selectpicker">
                                        @foreach ($country_code as $key => $value)
                                        @php
                                        $country_status_active = '';
                                        if($brokers->country_code == $key) {
                                        $country_status_active = 'selected';
                                        } else if(Input::old('country_code') == $key) {
                                        $country_status_active = 'selected';
                                        } else if($key == '+1') {
                                        $country_status_active = 'selected';
                                        }
                                        @endphp
                                        <option value="{{ $key }}" {{ $country_status_active }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-8 padding_left_zero">
                                    {{ Form::text('phone', Input::old('phone'), array('class' => 'form-control')) }}
                                </div>
                            </div>
                            {!! $errors->first('phone','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('name', 'Zip Code *', ['class' => 'form-label']) }}
                            {{ Form::text('zip_code', Input::old('zip_code'), array('class' => 'form-control')) }}
                            {!! $errors->first('zip_code','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                </div>
                <div class=" row">
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('name', 'Hourly Rate ( Format : 1.00 )', ['class' => 'form-label']) }}
                            {{ Form::text('hourly_rate', Input::old('hourly_rate'), array('class' => 'form-control')) }}
                            {!! $errors->first('hourly_rate','<span class="help-inline text-danger">:message</span>')
                            !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('name', 'Available Unit *', ['class' => 'form-label']) }}
                            {{ Form::text('available_unit', Input::old('available_unit'), array('class' => 'form-control')) }}
                            {!! $errors->first('available_unit','<span class="help-inline text-danger">:message</span>')
                            !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('name', 'Wsib Quarterly', ['class' => 'form-label']) }}
                            {{ Form::text('wsib_quarterly', Input::old('wsib_quarterly'), array('class' => 'form-control')) }}
                            {!! $errors->first('wsib_quarterly','<span class="help-inline text-danger">:message</span>')
                            !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {{ Form::label('name', 'Incorporation Name', ['class' => 'form-label']) }}
                            {{ Form::text('incorporation_name', Input::old('incorporation_name'), array('class' => 'form-control')) }}
                            {!! $errors->first('incorporation_name','<span
                                class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                    @php
                    $status_arr = array('active' => 'Active','inactive' => 'Inactive')
                    @endphp
                    <div class="col-sm-4">
                        <div class="mb-3">
                            {!! Form::label('status', 'Status *') !!}
                            <select id="status" name="status" class="form-control selectpicker" data-live-search="true">
                                <option value="">Select Status</option>
                                @foreach ($status_arr as $key => $value)
                                @php
                                $status_active = '';
                                if($brokers->status == $key) {
                                $status_active = 'selected';
                                } else if(Input::old('status') == $key) {
                                $status_active = 'selected';
                                }
                                @endphp
                                <option value="{{ $key }}" {{ $status_active }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('status','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            {{ Form::label('name', 'address *', ['class' => 'form-label']) }}
                            {{ Form::textarea('address', Input::old('address'), array('class' => 'form-control','rows' => 5)) }}
                            {!! $errors->first('address','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::submit('Edit Broker!', array('class' => 'btn btn-primary me-2')) }}
                    <a href="{{ url('brokers') }}" title="Back"><span class="btn btn-warning btn-sm"><i
                                class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
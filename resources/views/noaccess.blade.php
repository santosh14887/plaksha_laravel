@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
<div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">Invalid Page</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12" style="color: red;
    text-align: center;
    font-size: 18px;">
					You don't have permission to access the page
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages</li>
				<li class="breadcrumb-item active" aria-current="page">Issue</li>
				<li class="breadcrumb-item active" aria-current="page">User Issue</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Users Issue List
					<span class="float_right">
					@if(request('search') != '' || request('issue_date') != '' || request('status') != '')
					<a href="{{ url('/user_issues') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					</span></div>
                <div class="card-body">
				  {!! Form::open(['method' => 'GET', 'url' => '/user_issues', 'class' => 'float-right', 'role' => 'search'])  !!}
				   <div class="row form-group">
				   
						<div class="col-md-3 date_picker_div">
							   <div class="form-group">
								  {!! Form::label('start_time', 'Date', ['class' => 'control-label','for'=>'acct_dateClosed']) !!}
								  <div class="cale">
									 <span class="iconcalender"></span>
									 {!! Form::text('issue_date',request('issue_date'),['class' => 'form-control','id'=>'daterangepicker','autocomplete' => "off"]) !!}
								  </div>
							   </div>
						</div>
						@php
				$status_type_arr = array('on_hold' => 'On Hold','pending' => 'Pending','rejected' => 'Rejected','resolved' => 'Resolved')
				@endphp
				<div class="col-md-3 resize2_widthinn">
					<div class="form-group">
					{!! Form::label('Name', 'Status') !!}
					<select id="issue_filter_status" name="status" class="form-control selectpicker" data-live-search="true">
						<option value="">Select Status</option>
						@foreach ($status_type_arr as $key => $value)
						@php
						$selected_value = '';
						if(request('status') == $key) {
							$selected_value = 'selected';
						}
						@endphp
							<option value="{{ $key }}" {{ $selected_value }}>{{ $value }}</option>
						@endforeach
					</select> 
					</div>
				</div>
						<div class="col-md-4">
						{!! Form::label('Search', 'Search', ['class' => 'control-label','for'=>'acct_dateClosed']) !!}
							<div class="input-group">
								<input type="text" class="form-control  search_input" value="{{ request('search') }}" name="search" placeholder="Search...">
								<span class="input-group-append">
									<button class="btn filter_btn btn-secondary" type="submit">
										<i class="fa fa-search"></i>
									</button>
								</span>
							</div>
						
					</div>
				</div>
				{!! Form::close() !!}
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
                        <th>User Name</th>
                        <th>Category Name</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($user_issues) > 0)
                            @foreach($user_issues as $key => $value)
                                <tr>
								<td>{{ $user_issues->firstItem() + $key }}</td>
                                    <td>{{ (null !== $value->getUser) ? ucfirst($value->getUser->name) : '-' }}</td>
                                    <td>{{ (null !== $value->getIssueCatgory) ? $value->getIssueCatgory->title : '-' }}</td>
                                    <td>{{ (null !== $value->title) ? $value->title : '-' }}</td>
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
                            @endforeach
                            @else
                            <tr>
                            <td colspan="6">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
					<div class="pagination"> {!! $user_issues->appends(['search' => Request::get('search')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

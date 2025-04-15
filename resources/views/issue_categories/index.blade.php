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
				<li class="breadcrumb-item active" aria-current="page">Issue Category</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Issue Category List<span class="float_right">
				  @if(request('search') != '')
					<a href="{{ url('/issue_categories') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					@if( Auth::user()->can('viewMenu:ActionIssueCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
				    <a href="{{ URL::to('issue_categories/create') }}" class="btn btn-success btn-sm" title="Add New Category">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                    </a>
					@endif
				  </span></div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                        <th>S.No</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($issue_categories) > 0)
                            @foreach($issue_categories as $key => $value)
                                <tr>
								<td>{{ $issue_categories->firstItem() + $key }}</td>
                                    <td>{{ (null !== $value->title) ? $value->title : '-' }}</td>
                                    <td>{{ (null !== $value->description) ? $value->description : '-' }}</td>
									<td>
									<!--<a class="btn btn-sm btn-info" href="{{ URL::to('/cat_user_issues/' . $value->id) }}" title="View Issues"><i class=" menu-icon fa fa-lg fa-eye"></i></a>-->
									@if( Auth::user()->can('viewMenu:UserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
									<a class="btn btn-sm btn-info" href="{{ URL::to('/user_issues?search=' . $value->title) }}" title="View Issues"><i class=" menu-icon fa fa-lg fa-eye"></i></a>
									@endif
									@if( Auth::user()->can('viewMenu:ActionIssueCategory') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
									 <a  href="{{ URL::to('issue_categories/' . $value->id . '/edit') }}" title="Edit Service"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                    {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/issue_categories', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Service" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>
                                        @endif

                                    </td> 
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="4">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
@if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
@endif
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Pages </li>
				<li class="breadcrumb-item active" aria-current="page">Issue</li>
				<li class="breadcrumb-item active" aria-current="page">User Issue</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Issue Category for  {{ ucfirst ($issue_categories->title) }} <a href="{{ url('/issue_categories') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
					<!--<span class="float_right">
					<a href="{{ URL::to('issue_categories/create') }}">Add Category</a>
						
					</span>--->
				  </h4>
				  <div class="row">
					<div class="col-md-8"></div>
						<div class="col-md-4 float_right">
						  {!! Form::open(['method' => 'GET', 'url' => '/user_issues/'.$id, 'class' => 'float-right', 'role' => 'search'])  !!}
								<div class="input-group">
									<input type="text" class="form-control search_input" value="{{ request('search') }}" name="search" placeholder="Search...">
									<span class="input-group-append">
										<button class="btn btn-secondary" type="submit">
											<i class="fa fa-search"></i>
										</button>
									</span>
								</div>
							{!! Form::close() !!}
					</div>
				</div>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                        <th>User Name</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Date</th>
                       <!-- <th>Action</th>-->
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($user_issues) > 0)
                            @foreach($user_issues as $key => $value)
                                <tr>
                                    <td>{{ (null !== $value->getUser->name) ? $value->getUser->name : '-' }}</td>
                                    <td>{{ (null !== $value->title) ? $value->title : '-' }}</td>
                                    <td>{{ (null !== $value->description) ? $value->description : '-' }}</td>
                                    <td>{{ (null !== $value->created_at) ? $value->created_at : '-' }}</td>
									<!--<td>
									<a class="btn btn-sm btn-info" href="{{ URL::to('/user_issues/' . $value->id) }}" title="View Issues"><i class=" menu-icon fa fa-lg fa-eye"></i></a>
									 <a class="btn btn-sm btn-info" href="{{ URL::to('issue_categories/' . $value->id . '/edit') }}" title="Edit Service"><i class=" menu-icon fa fa-lg fa-edit"></i></a>
                                    {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/issue_categories', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Service" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button>
                                        

                                    </td> -->
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="4">No data Found</td>
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

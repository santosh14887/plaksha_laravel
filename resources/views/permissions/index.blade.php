@extends('layouts.after_login')

@section('content')
		<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Settings</li>
				<li class="breadcrumb-item active" aria-current="page">Permissions</li>
			</ol>
		</nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Permissions List<span class="float_right">
				  @if(request('search') != '')
					<a href="{{ url('/permissions') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					@if( Auth::user()->can('viewMenu:ActionPermission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
					<!-- <a href="{{ url('/permissions/create') }}" class="btn btn-success btn-sm" title="Add New Permission">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                    </a> -->
					@endif
				  </span></div>
                    <div class="card-body">
						 <div class="row">
							<div class="col-md-8"></div>
								<div class="col-md-4 float_right">
								  {!! Form::open(['method' => 'GET', 'url' => '/permissions', 'class' => 'form-inline my-2 my-lg-0 float-right', 'role' => 'search'])  !!}
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
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th><th>Name</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($permissions as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td><a href="{{ url('/permissions', $item->id) }}">{{ $item->name }}</a></td>
                                        <td>
                                            <a href="{{ url('/permissions/' . $item->id) }}" title="View Permission"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a>
											@if( Auth::user()->can('viewMenu:ActionPermission') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                                            <!-- <a href="{{ url('/permissions/' . $item->id . '/edit') }}" title="Edit Permission"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/permissions', $item->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                                {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i>', array(
                                                        'type' => 'submit',
                                                        'class' => 'btn btn-danger btn-sm',
                                                        'title' => 'Delete Permission',
                                                        'onclick'=>'return confirm("Confirm delete?")'
                                                )) !!}
                                            {!! Form::close() !!}  -->
											@endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pagination"> {!! $permissions->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection

@extends('layouts.after_login')

@section('content')
<!-- will be used to show any messages -->
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Settings</li>
				<li class="breadcrumb-item active" aria-current="page">Expense Category List</li>
			</ol>
		</nav>
<div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
			  <div class="card-header">Expense Category List<span class="float_right">
				  @if(request('search') != '')
					<a href="{{ url('/service_categories') }}" title="Remove Filter"><button class="btn btn-warning btn-sm"><i class="fa fa-refresh" aria-hidden="true"></i> Remove Filter</button></a>
					@endif
					@if( Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
					<a href="{{ URL::to('service_categories/create') }}" class="btn btn-success btn-sm" title="Add New Category">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Expense Category
                    </a>
					@endif
				  </span></div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
						<th>S.No</th>
                        <th>Expense Category</th>
                        <th>Sub Category</th>
                        <th>Description</th>
                        <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                            @if(count($service_categories) > 0)
                            @foreach($service_categories as $key => $value)
                                <tr>
								<td>{{ $service_categories->firstItem() + $key }}</td>
                                    <td>{{ (null !== $value->name) ? $value->name : '-' }}</td>
                                    <td>
									@if(null !== $value->subCategory)
									 @if( Auth::user()->can('viewMenu:VehicleService') ||
                                        Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                                        <a class=""
                                            href="{{ URL::to('specific_service_sub_category/' . $value->id) }}"
                                            title="Subcategory">{{ count($value->subCategory) }}</a>
											@else
												{{ count($value->subCategory) }}
                                        @endif
									@else
										0
									@endif
									</td>
                                    <td>{{ (null !== $value->comment) ? $value->comment : '-' }}</td>
                                     <td>
									 
									 <!--<a href="{{ url('/service_categories/' . $value->id) }}" title="View Category"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></button></a> -->
									 @if( Auth::user()->can('viewMenu:ActionVehicleService') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
									 <a href="{{ URL::to('service_categories/' . $value->id . '/edit') }}" title="Edit Expense Category"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a>
                                    <!-- {!! Form::open([
                                                'method' => 'DELETE',
                                                'url' => ['/service_categories', $value->id],
                                                'style' => 'display:inline'
                                            ]) !!}
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete Expense Category" onclick="return confirm('Confirm delete?')"><i class=" menu-icon fa fa-lg fa-remove"></i></button> -->
                                        @endif

                                    </td> 
                                </tr>
                            @endforeach
                            @else
                            <tr>
                            <td colspan="5">No data Found</td>
                            </tr>
                            @endif
                      </tbody>
                    </table>
					<div class="pagination"> {!! $service_categories->appends(['search' => Request::get('search')])->render() !!} </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

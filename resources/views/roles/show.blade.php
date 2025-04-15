@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item">Settings</li>
				<li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/roles') }}">Roles</a></li>
				<li class="breadcrumb-item active" aria-current="page">View Role</li>
			</ol>
		</nav>
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">View Role</div>
                    <div class="card-body">

                        <a href="{{ url('/roles') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
						@if( Auth::user()->can('viewMenu:ActionRoles') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                        <a href="{{ url('/roles/' . $role->id . '/edit') }}" title="Edit Role"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                       <!-- {!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/roles', $role->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Role',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!} -->
						@endif
                        <br/>
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID.</th> <th>Name</th><th>Permissions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $role->id }}</td>
										<td> {{ $role->name }} </td>
										<td> 
										@if(null != $role->permissions)
												@foreach($permissions as $vals)
													@php
													$lower_str = strtolower($vals);
													$add_view_cls = ($lower_str == 'all') ? '' : 'view';
													$add_view_id = 'view_'.$lower_str;
													$add_action_id = 'action_'.$lower_str;
													$view = 'viewMenu:'.$vals;
													$action = 'viewMenu:Action'.$vals;
													  $view_selected_val = $action_selected_val = '';
														  foreach($role->permissions as $aItemKey => $aItemval)
														  {
															  $permisssion_name = $aItemval->name;
															  if($view == $permisssion_name)
															  {
																$view_selected_val = 'checked';
															  }
															  if($action == $permisssion_name)
															  {
																$action_selected_val = 'checked';
															  }
														  }
													  @endphp
													  @if($view_selected_val != '' || $action_selected_val != '')
													<div class="row margin_bottom_one_per">
														@if($lower_str == 'all')
														<div class="col-md-3"><b>Full Site Permission</b></div>
													@else
														<div class="col-md-3">{{ $vals }}</div>
													@endif
														<div class="col-md-9">
														@if($view_selected_val != '')
															<div class="inline_checkbox">
															<input type="checkbox" class="invoice_checkbox" {{ $view_selected_val }} disabled> 
															<span class="checkbox_span">
															@if($lower_str == 'all')
																View / Read / Write / Delete
																@else
															View
															@endif
															</span>
															</div>
															@endif
															@if($action_selected_val != '')
															@if($lower_str != 'all')
															<div class="inline_checkbox"><input type="checkbox" class="invoice_checkbox" {{ $action_selected_val }} disabled> <span class="checkbox_span">Read / Write</span></div>
															@endif
															@endif
														</div>
													</div>
													@endif
													@endforeach
										@endif
										</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection

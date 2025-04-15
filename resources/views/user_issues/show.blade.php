@extends('layouts.after_login')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-12 show_page">
                <div class="card">
                    <div class="card-header">User Issue</div>
                    <div class="card-body">

                        <a href="{{ url('/user_issues') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
						@if( Auth::user()->can('viewMenu:ActionUserIssue') || Auth::user()->can('viewMenu:All') || Auth::user()->type == 'admin')
                        <a href="{{ url('/user_issues/' . $user_issues->id . '/edit') }}" title="Edit Issue"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                        <!--{!! Form::open([
                            'method' => 'DELETE',
                            'url' => ['/user_issues', $user_issues->id],
                            'style' => 'display:inline'
                        ]) !!}
                            {!! Form::button('<i class="fa fa-trash-o" aria-hidden="true"></i> Delete', array(
                                    'type' => 'submit',
                                    'class' => 'btn btn-danger btn-sm',
                                    'title' => 'Delete Issue',
                                    'onclick'=>'return confirm("Confirm delete?")'
                            ))!!}
                        {!! Form::close() !!}
                        <br/>-->
						@endif
                        <br/>

                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
								<tr>
								  <th scope="row">User Name</th>
								  <td>{{ (null!== $user_issues->getUser) ? $user_issues->getUser->name : '' }}</td>
								  <th scope="row">Category Name</th>
								  <td>{{ $user_issues->getIssueCatgory->title }}</td>
								</tr>
								<tr>
								  <th scope="row">Issue Title</th>
								  <td>{{ $user_issues->title }}</td>
								  <th>Description</th>
								  <td>{{ $user_issues->description }}</td>
								</tr>
								<tr>
								  <th scope="row">Status</th>
								  <td>{{ (null !== $user_issues->status) ? ($user_issues->status == 'on_hold') ? 'On Hold' : ucfirst($user_issues->status) : '-' }}</td>
								  <th scope="row">Created On</th>
								  <td>{{ $user_issues->start_time }}</td>
								</tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

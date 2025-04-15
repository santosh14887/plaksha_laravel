@extends('layouts.after_login')

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Settings</li>
        <li class="breadcrumb-item active" aria-current="page">View Expense Category</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">View Expense Category</div>
            <div class="card-body">

                <a href="{{ url('/service_categories') }}" title="Back"><button class="btn btn-warning btn-sm"><i
                            class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                <a href="{{ url('/service_categories/' . $service_categories->id . '/edit') }}"
                    title="Edit Expense Category"><button class="btn btn-primary btn-sm"><i
                            class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>
                <br />
                <br />

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Expense category</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> {{ (null != $service_categories->name) ? $service_categories->name : '' }} </td>
                                <td> {{ (null != $service_categories->parentCategory) ? $service_categories->parentCategory->name : 'Root' }}
                                </td>
                                <td> {{ (null != $service_categories->comment) ? $service_categories->comment : '' }}
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
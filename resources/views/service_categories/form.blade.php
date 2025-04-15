<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Pages</li>
        <li class="breadcrumb-item active" aria-current="page">Settings</li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/service_categories') }}">Expense
                Category</a></li>
        <li class="breadcrumb-item active" aria-current="page">Category</li>
    </ol>
</nav>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                @if (isset($service_categories) && null !== $service_categories && !empty($service_categories))
                Update Expense Category for ( {{ $service_categories->name}})
                @else
                Add Expense Category
                @endif
            </div>
            <div class="card-body">
                @php
                $name_val = $comment_val = $parent_category_val = $root_selected_val = '';
                if(null !== app('request')->input('id'))
                {
                echo '<input type="hidden" name="parent_id" value="'.app('request')->input('id').'">';
                }
                $name_val = (null !== old('name')) ? old('name') : '';
                $parent_category_val = (null !== old('parent_category')) ? old('parent_category') : '';
                $comment_val = (null !== old('comment')) ? old('comment') : '';
                if(isset($service_categories)){
                $name_val = (isset($service_categories->name)) ? $service_categories->name : '';
                $comment_val = (isset($service_categories->comment)) ? $service_categories->comment : '';
                $parent_category_val = (isset($service_categories->parent_category)) ?
                $service_categories->parent_category : '';
                }
                if($root_selected_val == '') {
                $root_selected_val == 'selected';
                }
                @endphp
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            {{ Form::label('name', 'Name *', ['class' => 'form-label']) }}
                            {{ Form::text('name', $name_val, array('class' => 'form-control','id' => 'name')) }}
                            {!! $errors->first('name','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            {!! Form::label('service_categories', 'Expense Category', ['class' =>
                            'form-label','for'=>'roles']) !!}
                            <div class="select-wrap">
                                <select name="parent_category" id="service_categories"
                                    class="form-control form-item__element--select selectpicker"
                                    data-live-search="true">
                                    <option value="0" {{ $root_selected_val }}>Root</option>
                                    @foreach($added_service_categories as $aKey => $aSport)
                                    @php
                                    $name = $aSport->name;
                                    $row_id = $aSport->id;
                                    $selected_val = '';
                                    if($row_id == $parent_category_val)
                                    {
                                    $selected_val = 'selected';
                                    }
                                    @endphp
                                    <option value="{{$row_id}}" {{ $selected_val }}>{{$name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            {!! $errors->first('parent_category', '
                            <p class="help-inline text-danger">:message</p>
                            ') !!}
                        </div>
                    </div><!-- Col -->
                    <div class="col-sm-6">
                        <div class="mb-3">
                            {{ Form::label('name', 'Description ', ['class' => 'form-label']) }}
                            {{ Form::textarea('comment', $comment_val, array('class' => 'form-control','rows' => 5)) }}
                            {!! $errors->first('comment','<span class="help-inline text-danger">:message</span>') !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::submit($formMode === 'edit' ? 'Update' : 'Create', ['class' => 'btn btn-primary btn-sm'])
                    !!}
                    <a href="{{ url('/service_categories') }}" name="Back"><span class="btn btn-warning btn-sm"><i
                                class="fa fa-arrow-left" aria-hidden="true"></i> Back</span></a>
                </div>
            </div>
        </div>
    </div>
</div>
@extends('cms::layouts.dashboard')

@section('content')

    <div class="row">
        <h1 class="page-header">Widgets</h1>
    </div>

    @include('cms::modules.widgets.breadcrumbs', ['location' => ['create']])

    <div class="row">
        {!! Form::open(['route' => config('cms.backend-route-prefix', 'cms').'.widgets.store', 'class' => 'add']) !!}

            {!! FormMaker::fromTable('widgets', Config::get('cms.forms.widget')) !!}

            <div class="form-group text-right">
                <a href="{!! url(config('cms.backend-route-prefix', 'cms').'/widgets') !!}" class="btn btn-default raw-left">Cancel</a>
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
            </div>

        {!! Form::close() !!}
    </div>

@endsection

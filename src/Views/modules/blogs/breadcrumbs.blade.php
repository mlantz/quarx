<div class="row">
    <ol class="breadcrumb">
        <li><a href="{!! url(config('cms.backend-route-prefix', 'cms').'/blog') !!}">Blog</a></li>

            {!! Cms::breadcrumbs($location) !!}

        <li class="active"></li>
    </ol>
</div>
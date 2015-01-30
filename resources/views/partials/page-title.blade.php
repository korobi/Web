@if ($__env->yieldContent('title')) {{-- This is ugly :( http://laravel.io/forum/02-06-2014-check-if-yieldsomething-is-set --}}
    <title>@yield("title") - {{{ trans("branding.project-name") }}}</title>
@else
    <title>{{{ trans("branding.project-name") }}}</title>
@endif

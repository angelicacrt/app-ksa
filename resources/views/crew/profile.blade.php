@if(Auth::user()->hasRole('crew'))
    {{-- add your code here--}}
@else
    @include('../layouts/notAuthorized')
@endif
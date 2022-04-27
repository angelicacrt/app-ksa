@if(Auth::user()->hasRole('crew'))


@elseif(Auth::user()->hasRole('admin'))


@else   
    @include('../layouts/notAuthorized')
@endif
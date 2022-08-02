{{--@if(Auth::guard('web')->check())
    @include('layouts.navbars.navs.auth')
@elseif(Auth::guard('employee')->check())
    @include('layouts.navbars.navs.auth')
@endif--}}
@auth()
    @include('layouts.navbars.navs.auth')
@endauth

@guest()
    @include('layouts.navbars.navs.guest')
@endguest

<!-- Top navbar -->
<nav class="navbar navbar-top navbar-expand-md navbar-dark bg-gradient-green" id="navbar-main">
    <div class="container-fluid navbar-container">
        <span class="mr-2 collapse-sidebar">☰</span>
        <h4 class=" mb-0 text-white text-uppercase d-none d-lg-inline-block text-nowrap header-name" >{{isset($headerName) ? $headerName : 'Page'}}</h4>
        <div class="d-flex justify-content-end w-100">
            <ul class="navbar-nav align-items-center d-none d-md-flex">
                <li class="nav-item dropdown">
                    <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                            <img alt="Image placeholder" src="{{ asset('argon') }}/img/lang/{{ app()->getLocale() }}.svg">
                        </span>
                            <div class="media-body ml-2 d-none d-lg-block">
                                <span class="mb-0 text-sm  font-weight-bold">{{ app()->getLocale() }}</span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                        <a href="{{ url('set-locale/en') }}" class="dropdown-item d-flex align-items-center justify-content-start">
                        <span class="avatar avatar-sm rounded-circle">
                            <img alt="English" src="{{ asset('argon/img/lang/en.svg') }}">
                        </span>
                            <span class="ml-2">{{ __('English') }}</span>
                        </a>
                        <a href="{{ url('set-locale/sw') }}" class="dropdown-item d-flex align-items-center justify-content-start">
                        <span class="avatar avatar-sm rounded-circle ">
                            <img alt="Swedish" src="{{ asset('argon/img/lang/sw.svg') }}">
                        </span>
                            <span class="ml-2">{{ __('Swedish') }}</span>
                        </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle overflow-hidden">
{{--todo:check stegh borshvela, bazayum attachmentnerum anhaskanali pather en yngel, hin upload arats nkarnery chi bacum--}}
                            <img class="h-100" alt="Profile Image" src="{{ auth()->user()->attachment ? asset(auth()->user()->attachment->path) : asset('images/personlig/default.jpg')}}">
                        </span>
                            <div class="media-body ml-2 d-none d-lg-block">
                                <span class="mb-0 text-sm font-weight-bold userName">
                                    @if(Auth::guard('web')->check())
                                        {{ auth()->user()->name ?: '!' }}
                                    @elseif(Auth::guard('employee')->check())
                                        {{ auth()->user()->servit_username ?: '!' }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                        <div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0">{{ __('Welcome!') }}</h6>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="ni ni-single-02"></i>
                            <span>{{ __('My profile') }}</span>
                        </a>
                        <div class="dropdown-divider"></div>
{{--                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();--}}
                        <a href="#" class="dropdown-item" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                            <i class="ni ni-user-run"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                    </div>
                </li>
            </ul>

        </div>
    </div>
</nav>

<nav class="navbar navbar-top navbar-horizontal navbar-expand-md navbar-dark bg-gradient-green">
    <div class="container px-4">
        <div class="d-flex justify-content-between">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img style="width: 35%; height: auto;" src="{{ asset('images/personlig/logo-no-bg.png') }}" />
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse-main" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('argon/img/brand/blue.png') }}">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle sidenav">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Navbar items -->
            <ul class="navbar-nav ml-auto align-items-center">
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
                            <img alt="English" src="{{ asset('argon') }}/img/lang/en.svg">
                        </span>
                            <span class="ml-2">{{ __('English') }}</span>
                        </a>
                        <a href="{{ url('set-locale/sw') }}" class="dropdown-item d-flex align-items-center justify-content-start">
                        <span class="avatar avatar-sm rounded-circle ">
                            <img alt="Swedish" src="{{ asset('argon') }}/img/lang/sw.svg">
                        </span>
                            <span class="ml-2">{{ __('Swedish') }}</span>
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-icon" href="{{ url('/') }}">
                        <i class="ni ni-planet"></i>
                        <span class="nav-link-inner--text">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-icon open-login-modal" href="{{route('login')}}">
                        <i class="ni ni-key-25"></i>
                        <span class="nav-link-inner--text">{{ __('Login') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

</nav>

<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
    {{--        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">--}}
    {{--            <span class="navbar-toggler-icon"></span>--}}
    {{--        </button>--}}
    <!-- Brand -->
        <a class="navbar-brand pt-0" style="padding-bottom: 0;"
           href="{{ route('home',['date_range' => 'true', 'start' => $currStart, 'end' => $currEnd ]) }}">
            <img src="{{ asset('images/personlig/logo.png') }}" style="max-height: 6.5rem;" class="navbar-brand-img"
                 alt="Personlig">
        </a>
        <ul class="nav align-items-center d-md-none">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/lang/{{ app()->getLocale() }}.svg">
                        </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-left">
                    <a href="{{ url('set-locale/en') }}"
                       class="dropdown-item d-flex align-items-center justify-content-start">
                        <span class="avatar avatar-sm rounded-circle">
                            <img alt="English" src="{{ asset('argon/img/lang/en.svg') }}">
                        </span>
                        <span class="ml-2">{{ __('English') }}</span>
                    </a>
                    <a href="{{ url('set-locale/sw') }}"
                       class="dropdown-item d-flex align-items-center justify-content-start">
                        <span class="avatar avatar-sm rounded-circle ">
                            <img alt="Swedish" src="{{ asset('argon/img/lang/sw.svg') }}">
                        </span>
                        <span class="ml-2">{{ __('Swedish') }}</span>
                    </a>
                </div>
            </li>
        </ul>
        <ul class="nav align-items-center d-md-none ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                        <img alt="Image placeholder"
                             src="{{ auth()->user()->attachment ? asset(auth()->user()->attachment->path) : asset('images/personlig/default.jpg')}}">
                        </span>
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
                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                </div>
            </li>
        </ul>
        <!-- Collapse -->
        {{--        <div class="collapse navbar-collapse" id="sidenav-collapse-main">--}}
        <div>
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ route('home',['date_range' => 'true', 'start' => $currStart, 'end' => $currEnd ]) }}">
                            <img src="{{ asset('images/personlig/logo.png') }}">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse"
                                data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false"
                                aria-label="Toggle sidenav">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Navigation -->

            <ul class="navbar-nav">
                <li class="nav-item {{$activePage === 'dashboard' ? 'bg-light' : ''}}">
                    <a class="nav-link"
                       href="{{ route('home',['date_range' => 'true', 'start' => $currStart, 'end' => $currEnd ]) }}">
                        <i class="ni ni-app text-primary"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                @if(Auth::guard('employee')->check())
                    <li class="nav-item {{$activePage === 'userStatistics' ? 'bg-light' : ''}}">
                        <a class="nav-link" href="{{ route('employee.user_statistics') }}">
                            <i class="ni ni-badge text-primary"></i> {{ __('My Statistics') }}
                        </a>
                    </li>
                @endif
                @if(auth()->user()->email == "gabriella.varga.karlsson@personligtsvar.se")
                    <li class="nav-item">
                        @php
                            $inCompaniesPage = $activePage === 'companies' || $activePage === 'company_tags' || $activePage === 'company_dashboard';
                        @endphp
                        <a class="nav-link {{$inCompaniesPage ? '' : 'collapsed'}}" href="#company_pages"
                           data-toggle="collapse" role="button"
                           aria-expanded="{{$inCompaniesPage ? 'true' : 'false'}}" aria-controls="company_pages">
                            <i class="ni ni-building text-primary"></i>
                            <span class="nav-link-text">{{ __('Companies') }}</span>
                        </a>

                        <div class="collapse {{$inCompaniesPage ? 'show' : ''}}" id="company_pages">
                            <ul class="nav nav-sm flex-column pl-2">
                                <li class="nav-item {{$activePage === 'company_dashboard' ? 'bg-light' : ''}}">
                                    <a class="nav-link "
                                       href="{{ route('admin.companies.dashboard',['start' => $currStart, 'end' => $currEnd]) }}">
                                        {{ __('Dashboard') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(Auth::guard('web')->check())
                    <li class="nav-item {{$activePage === 'planning' ? 'bg-light' : ''}}">
                        <a class="nav-link " href="{{ route('admin.planning') }}">
                            <i class="ni ni-chart-pie-35 text-primary"></i> {{ __('Planning') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('full-screen') }}">
                            <i class="ni ni-tv-2 text-primary"></i> {{ __('Wallboard') }}
                        </a>
                    </li>


                    <li class="nav-item">
                        @php
                            $inUserPage = $activePage === 'users' || $activePage === 'roles & permissions';
                        @endphp
                        <a class="nav-link  {{$inUserPage ? '' : 'collapsed'}}" href="#user_pages"
                           data-toggle="collapse" role="button"
                           aria-expanded="{{$inUserPage ? 'true' : 'false'}}" aria-controls="user_pages">
                            <i class="ni ni-single-02 text-primary"></i>
                            <span class="nav-link-text">{{ __('User Management') }}</span>
                        </a>
                        <div class="collapse {{$inUserPage ? 'show' : ''}}" id="user_pages">
                            <ul class="nav nav-sm flex-column pl-2">
                                <li class="nav-item {{$activePage === 'users' ? 'bg-light' : ''}}">
                                    <a class="nav-link "
                                       href="{{ route('admin.users.index') }}">
                                        {{ __('Users') }}
                                    </a>
                                </li>

                                <li class="nav-item {{$activePage === 'roles & permissions' ? 'bg-light' : ''}}">
                                    <a class="nav-link " href="{{ route('admin.roles.index') }}">
                                        {{ __('Roles') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    {{--                    <li class="nav-item {{$activePage === 'users' ? 'bg-light' : ''}}">--}}
                    {{--                        <a class="nav-link" href="{{ route('admin.users.index') }}">--}}
                    {{--                            <i class="ni ni-single-02 text-primary"></i> {{ __('User Management') }}--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}
                    {{--                    <li class="nav-item {{$activePage === 'roles & permisssions' ? 'bg-light' : ''}}">--}}
                    {{--                        <a class="nav-link" href="{{ route('admin.roles.index') }}">--}}
                    {{--                            <i class="ni ni-single-02 text-primary"></i> {{ __('Roles') }}--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}



                    <li class="nav-item {{$activePage === 'providers' ? 'bg-light' : ''}}">
                        <a class="nav-link " href="{{route('admin.service-providers.index')}}">
                            <i class="fa fa-hands-helping text-primary pr-3"></i> {{ __('Service Providers') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        @php
                            $inCompaniesPage = $activePage === 'companies' || $activePage === 'company_tags' || $activePage === 'company_dashboard' ||  $activePage === 'company_compare';
                        @endphp
                        <a class="nav-link  {{$inCompaniesPage ? '' : 'collapsed'}}" href="#company_pages"
                           data-toggle="collapse" role="button"
                           aria-expanded="{{$inCompaniesPage ? 'true' : 'false'}}" aria-controls="company_pages">
                            <i class="ni ni-building text-primary"></i>
                            <span class="nav-link-text">{{ __('Companies') }}</span>
                        </a>

                        <div class="collapse {{$inCompaniesPage ? 'show' : ''}}" id="company_pages">
                            <ul class="nav nav-sm flex-column pl-2">
                                <li class="nav-item {{$activePage === 'company_dashboard' ? 'bg-light' : ''}}">
                                    <a class="nav-link "
                                       href="{{ route('admin.companies.dashboard',['start' => $currStart, 'end' => $currEnd]) }}">
                                        {{ __('Dashboard') }}
                                    </a>
                                </li>

                                <li class="nav-item {{$activePage === 'company_tags' ? 'bg-light' : ''}}">
                                    <a class="nav-link " href="{{ route('admin.tags.index') }}">
                                        {{ __('Tags') }}
                                    </a>
                                </li>
                                <li class="nav-item {{$activePage === 'company_compare' ? 'bg-light' : ''}}">
                                    <a class="nav-link"
                                       href="{{ route('admin.compare.companies.dateRange',['start' => $thisMonthStart, 'end' => $thisMonthEnd,'s_start' => $lastMonthStart, 's_end' => $lastMonthEnd]) }}">
                                        {{ __('Compare') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        @php
                            $inChatsPage = $activePage === 'departments';
                        @endphp
                        <a class="nav-link  {{$inChatsPage ? '' : 'collapsed'}}" href="#navbar-chat"
                           data-toggle="collapse" role="button"
                           aria-expanded="{{$inChatsPage ? 'true' : 'false'}}" aria-controls="navbar-chat">
                            <i class="ni ni-chat-round text-primary"></i>
                            <span class="nav-link-text">{{ __('Chats') }}</span>
                        </a>

                        <div class="collapse {{$inChatsPage ? 'show' : ''}}" id="navbar-chat">
                            <ul class="nav nav-sm flex-column pl-2">
                                <li class="nav-item {{$activePage === 'departments' ? 'bg-light' : ''}}">
                                    <a class="nav-link " href="{{ route('admin.department.index') }}">
                                        {{ __('Connect Departments') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        @php
                            $inBillingPage = $activePage === 'billing' || $activePage === 'default_prices';
                        @endphp
                        <a class="nav-link  {{$inBillingPage ? '' : 'collapsed'}}" href="#navbar-billing"
                           data-toggle="collapse" role="button"
                           aria-expanded="{{$inBillingPage ? 'true' : 'false'}}" aria-controls="navbar-billing">
                            <i class="ni ni-bold text-primary"></i>
                            <span class="nav-link-text">{{ __('Billing') }}</span>
                        </a>

                        <div class="collapse {{$inBillingPage ? 'show' : ''}}" id="navbar-billing">
                            <ul class="nav nav-sm flex-column pl-2">
                                <li class="nav-item {{$activePage === 'billing' ? 'bg-light' : ''}}">
                                    <a class="nav-link "
                                       href="{{ route('admin.billing.index',['start' => $start, 'end' => $end]) }}">
                                        {{ __('Dashboard') }}
                                    </a>
                                </li>
                                {{--                                <li class="nav-item {{$activePage === 'billing' ? 'bg-light' : ''}}">--}}
                                {{--                                    <a class="nav-link "--}}
                                {{--                                       href="{{ route('admin.billing.index',['start' => $start, 'end' => $end]) }}">--}}
                                {{--                                        {{ __('Companies') }}--}}
                                {{--                                    </a>--}}
                                {{--                                </li>--}}
                                <li class="nav-item {{$activePage === 'default_prices' ? 'bg-light' : ''}}">
                                    <a class="nav-link " href="{{ route('admin.settings.billing') }}">
                                        {{ __('Default Prices') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item {{$activePage === 'reports' ? 'bg-light' : ''}}">
                        <a class="nav-link " href="{{route('admin.reports.index')}}">
                            <i class="fa fa-file-invoice text-primary pr-4"></i> {{ __('Reports') }}
                        </a>
                    </li>

                    <li class="nav-item">
                        @php
                            $inIntegrationPage = $activePage === 'integrations';
                        @endphp
                        <a class="nav-link  {{$inIntegrationPage ? '' : 'collapsed'}}" href="#navbar-integration"
                           data-toggle="collapse" role="button"
                           aria-expanded="{{$inIntegrationPage ? 'true' : 'false'}}" aria-controls="navbar-integration">
                            <i class="ni ni-atom text-primary"></i>
                            <span class="nav-link-text">{{ __('Integrations') }}</span>
                        </a>

                        <div class="collapse {{$inIntegrationPage ? 'show' : ''}}" id="navbar-integration">
                            <ul class="nav nav-sm flex-column pl-2">
                                <li class="nav-item {{$activePage === 'integrations' ? 'bg-light' : ''}}">
                                    <a class="nav-link " href="{{ route('admin.integrations.index') }}">
                                        {{ __('Tele Two Users') }}
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>


                    {{--                    <li class="nav-item {{$activePage === 'integrations' ? 'bg-light' : ''}}">--}}
                    {{--                        <a class="nav-link" href="{{route('admin.integrations.index')}}">--}}
                    {{--                            <i class="ni ni-atom text-primary"></i> {{ __('Integrations') }}--}}
                    {{--                        </a>--}}
                    {{--                    </li>--}}

                    <li class="nav-item">
                        @php
                            $inSettingsPage = $activePage === 'variables' || $activePage === 'translations' || $activePage === 'translationsz';
                        @endphp
                        <a class="nav-link  {{$inSettingsPage ? '' : 'collapsed'}}" href="#navbar-examples"
                           data-toggle="collapse" role="button"
                           aria-expanded="{{$inSettingsPage ? 'true' : 'false'}}" aria-controls="navbar-examples">
                            <i class="ni ni-settings-gear-65 text-primary"></i>
                            <span class="nav-link-text">{{ __('Settings') }}</span>
                        </a>

                        <div class="collapse {{$inSettingsPage ? 'show' : ''}}" id="navbar-examples">
                            <ul class="nav nav-sm flex-column pl-2">
                                <li class="nav-item {{$activePage === 'variables' ? 'bg-light' : ''}}">
                                    <a class="nav-link " href="{{ route('admin.settings.variables.index') }}">
                                        {{ __('Variables') }}
                                    </a>
                                </li>
                                <li class="nav-item {{$activePage === 'translations' ? 'bg-light' : ''}}">
                                    <a class="nav-link " href="{{ route('admin.settings.translations.index') }}">
                                        {{ __('Translation') }}
                                    </a>
                                </li>
                                <li class="nav-item {{$activePage === 'translationsz' ? 'bg-light' : ''}}">
                                    <a class="nav-link " href="{{ route('admin.settings.translations.index.sp') }}">
                                        {{ __('Translation SPP') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

@extends('layouts.app', ['title' => __('Profile')])

@push('css')
    <style>
        #xslUpload > label {
            margin: 0;
            cursor: pointer;
        }

        #xslUpload > .file-input {
            display: none;
        }

        #xslUpload .importar {
            width: 30vh;
            height: 30vh;
            pointer-events: none;
            background-image: url({{ auth()->user()->attachment ? asset(auth()->user()->attachment->path) : asset('images/personlig/default.jpg')}});
            background-repeat: no-repeat;
            background-size: 30vh 30vh;
            color: #fff;
            border-radius: 0;

        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
                <div class="card card-profile shadow">
                    <form action="{{route('profile.update_image')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-8">
                                <div class="card-profile-image d-flex justify-content-center">
                                    <div id="xslUpload">
                                        <label for="file-input">
                                            <button class="btn importar rounded-circle" type="button"></button>
                                        </label>
                                        <input class="file-input" name="image" id="file-input" type="file"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4 card-header text-center border-0 d-flex justify-content-center justify-content-md-end align-items-center">
                                    <button type="submit"
                                            class="btn btn-sm btn-info float-right">{{ __('Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-xl-8 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <h3 class="mb-0">{{ __('Edit Profile') }}</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{route('profile.update')}}" autocomplete="off">
                            @csrf
                            @method('put')

                            <h6 class="heading-small text-muted mb-4">{{ __('User information') }}</h6>

                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="input-name"
                                           class="form-control form-control-alternative{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('Name') }}"
                                           value="{{ old('name', auth()->guard('web')->check() ? auth()->user()->name : auth()->user()->servit_username) }}" required autofocus>
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="input-email" disabled
                                           class="form-control form-control-alternative{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('Email') }}"
                                           value="{{ old('email', auth()->user()->email) }}" required>

                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </form>
                        <hr class="my-4"/>
                        <form method="post" action="{{route('profile.update.password')}}" autocomplete="off">
                            @csrf
                            @method('put')

                            <h6 class="heading-small text-muted mb-4">{{ __('Password') }}</h6>

                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('old_password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label"
                                           for="input-current-password">{{ __('Current Password') }}</label>
                                    <input type="password" name="old_password" id="input-current-password"
                                           class="form-control form-control-alternative{{ $errors->has('old_password') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('Current Password') }}" value="" required>
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label"
                                           for="input-password">{{ __('New Password') }}</label>
                                    <input type="password" name="password" id="input-password"
                                           class="form-control form-control-alternative{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                           placeholder="{{ __('New Password') }}" value="" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label"
                                           for="input-password-confirmation">{{ __('Confirm New Password') }}</label>
                                    <input type="password" name="password_confirmation" id="input-password-confirmation"
                                           class="form-control form-control-alternative"
                                           placeholder="{{ __('Confirm New Password') }}" value="" required>
                                </div>

                                <div class="text-center">
                                    <button type="submit"
                                            class="btn btn-success mt-4">{{ __('Change password') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.pages')
    </div>
@endsection

@push('js')
    <script>
        document.querySelector("#file-input").onchange = function() {
            const url = URL.createObjectURL(this.files[0]);
            document.querySelector(".importar").style.background = "url(" + url + ")";
        }
    </script>
@endpush


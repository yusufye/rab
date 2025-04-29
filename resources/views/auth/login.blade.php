@php
    $configData = Helper::appClasses();
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Login')

@section('page-style')
  
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover">
        <!-- Logo -->

        <!-- /Logo -->
        <div class="authentication-inner row m-0 justify-content-center align-items-center min-vh-100">

    <!-- Login -->
    <div class="d-flex col-12 col-lg-6 col-xl-4 align-items-center authentication-bg position-relative py-sm-5 px-4 py-4">
        <div class="w-px-400 mx-auto pt-5 pt-lg-0 text-center">
            <div>
                <!-- <img src="{{ asset('assets/img/branding/hros-logo.png') }}" class="ms-1 mx-auto mb-5" width="80" alt=""> -->
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">{{ env('APP_NAME'); }}</h4>
                    <p class="mb-4">Silahkan masuk ke akun anda</p>

                    @if (session('status'))
                        <div class="alert alert-success mb-3 rounded" role="alert">
                            <div class="alert-body">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif
                    @if (session('Success'))
                        <div class="alert alert-success mb-3 rounded" role="alert">
                            <div class="alert-body">
                                {{ session('Success') }}
                            </div>
                        </div>
                    @endif
                    @if (session('Gagal'))
                        <div class="alert alert-warning mb-3 rounded" role="alert">
                            <div class="alert-body">
                                {{ session('Gagal') }}
                            </div>
                        </div>
                    @endif

                    <!-- login -->
                    <div id="form-login">
                        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" autofocus value="{{ old('email') }}">
                                <label for="login-username">Email</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <span class="fw-medium">{{ $message }}</span>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="login-password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                            <label for="login-password">Password</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <span class="fw-medium">{{ $message }}</span>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 d-flex justify-content-between">
                                <!-- <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                                    <label class="form-check-label" for="remember-me">
                                        Remember Me
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="float-end mb-1">
                                        <span>Forgot Password?</span>
                                    </a>
                                @endif -->
                            </div>
                            <button class="btn btn-primary d-grid w-100 btn-login">
                                Masuk
                            </button>
                        </form>
                    </div>
                    <!-- login -->
                </div>
            </div>
        </div>
    </div>
    <!-- /Login -->
</div>

    </div>
    </div>
</div>



@endsection

@section('page-script')
    <!-- <script src="{{ asset('assets/js_custom/login-auth.js') }}?v={{ time() }}"></script> -->
@endsection

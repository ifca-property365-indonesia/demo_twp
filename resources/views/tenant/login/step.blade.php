@extends('layouts.auth')

@section('title', __('tenant/login.page_title'))

@section('content')
    <h1 class="login-title">{{ __('tenant/login.heading') }}</h1>

    @if (session('alert'))
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="cil-warning me-2"></i><div>{{ session('alert') }}</div>
        </div>
    @endif

    <form action="{{ url('/tenant/login') }}" method="POST" id="formlogin" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label" for="bsn">{{ __('tenant/login.business_name') }}</label>
            <div class="form-control-wrap">
                <div class="form-icon form-icon-left"><i class="cil-building"></i></div>
                <select id="bsn" name="bsn" class="form-select" required>
                    {!! $combo !!}
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label" for="password">{{ __('common.password') }}</label>
            <div class="password-wrap">
                <input type="password" class="form-control form-control-lg" id="password" name="password"
                       placeholder="{{ __('tenant/login.password_placeholder') }}" required autocomplete="current-password">
                <span class="toggle-password" data-target="password" title="{{ __('tenant/login.toggle_password') }}"><i class="cil-lock-locked"></i></span>
            </div>
        </div>
        <button type="submit" class="btn btn-lg btn-primary w-100">{{ __('tenant/login.submit') }}</button>
        <a href="{{ url('/') }}" class="btn btn-link w-100 mt-2">{{ __('tenant/login.back_to_login') }}</a>
    </form>
@endsection

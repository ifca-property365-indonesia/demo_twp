@extends('layouts.auth')

@section('title', 'Tenant Log in')

@section('content')
    <h1 class="login-title">Tenant Log In</h1>

    @if (session('alert'))
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="cil-warning me-2"></i><div>{{ session('alert') }}</div>
        </div>
    @endif

    <form action="{{ url('/tenant/login') }}" method="POST" id="formlogin" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label" for="bsn">Business Name</label>
            <div class="form-control-wrap">
                <div class="form-icon form-icon-left"><i class="cil-building"></i></div>
                <select id="bsn" name="bsn" class="form-select" required>
                    {!! $combo !!}
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label" for="password">Password</label>
            <div class="password-wrap">
                <input type="password" class="form-control form-control-lg" id="password" name="password"
                       placeholder="Enter your password" required autocomplete="current-password">
                <span class="toggle-password" data-target="password" title="Show / hide password"><i class="cil-lock-locked"></i></span>
            </div>
        </div>
        <button type="submit" class="btn btn-lg btn-primary w-100">Log In</button>
        <a href="{{ url('/') }}" class="btn btn-link w-100 mt-2">Back to login</a>
    </form>
@endsection

{{-- Layout portal tenant: kerangka CoreUI ada di layouts.app, di sini hanya sidebar & header tenant. --}}
@extends('layouts.app', ['appTitle' => __('tenant.header.title'), 'portal' => 'tenant'])

@section('sidebar')
    @include('tenant.template.sidebar')
@endsection

@section('header')
    @include('tenant.template.header')
@endsection

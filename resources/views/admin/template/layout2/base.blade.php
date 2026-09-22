{{-- Layout portal admin: kerangka CoreUI ada di layouts.app, di sini hanya sidebar & header admin. --}}
@extends('layouts.app', ['appTitle' => 'IFCA Web Admin', 'portal' => 'admin'])

@section('sidebar')
    @include('admin.template.layout2.sidebar')
@endsection

@section('header')
    @include('admin.template.layout2.header')
@endsection

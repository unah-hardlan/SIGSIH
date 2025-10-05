@extends('layouts.admin')

@php
    $rawMessage = '';
    if (isset($exception)) {
        $rawMessage = trim($exception->getMessage() ?? '');
    }
    if ($rawMessage === '' || strtolower($rawMessage) === 'forbidden') {
        $rawMessage = __('No cuentas con los permisos necesarios para acceder a esta sección.');
    }
@endphp

@section('page-view-name', 'access-denied')

@section('content')
    @include('admin.partials.access-denied', [
        'code' => 403,
        'title' => __('Acceso restringido'),
        'message' => $rawMessage,
        'helpText' => __('Comunícate con un administrador si consideras que deberías tener acceso.'),
    ])
@endsection

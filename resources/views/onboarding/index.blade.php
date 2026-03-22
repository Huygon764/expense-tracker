@extends('layouts.guest')

@section('title', __('messages.app_name') . ' - Onboarding')

@section('container_class', 'max-w-4xl')

@section('logo')
{{-- Logo is embedded inside step layouts --}}
@endsection

@section('card')
    @if($step === 1)
        @include('onboarding.partials.step1')
    @else
        @include('onboarding.partials.step2')
    @endif
@endsection

@extends('layouts.app')

@section('title', 'Onboarding')

@section('content')
<div class="max-w-lg">
    <div class="mb-8 flex gap-2">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full {{ $step >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-500' }} text-sm font-medium">1</span>
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full {{ $step >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-500' }} text-sm font-medium">2</span>
    </div>

    @if($step === 1)
        @include('onboarding.partials.step1')
    @else
        @include('onboarding.partials.step2')
    @endif
</div>
@endsection

@extends('layouts.app')

@section('page-title', __('messages.reports'))

@section('content')
<div class="space-y-8">

    {{-- Page header --}}
    <div>
        <h1 class="text-3xl font-display font-bold text-on-surface">{{ __('messages.reports') }}</h1>
        <p class="mt-2 text-sm text-on-surface-variant">{{ __('messages.report_help') }}</p>
    </div>

    {{-- Error display --}}
    @if($errors->any())
        <div class="bg-error-container rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <x-icon name="alert-triangle" class="w-5 h-5 text-error shrink-0 mt-0.5" />
                <ul class="text-sm text-error space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Date range and export --}}
    <x-card>
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input
                    name="date_from"
                    :label="__('messages.date_from')"
                    type="date"
                    icon="calendar"
                    :value="old('date_from', request('date_from', now()->startOfMonth()->format('Y-m-d')))"
                />
                <x-form-input
                    name="date_to"
                    :label="__('messages.date_to')"
                    type="date"
                    icon="calendar"
                    :value="old('date_to', request('date_to', now()->format('Y-m-d')))"
                />
            </div>

            <div style="border-top: 1px solid rgba(191,201,200,0.15);" class="pt-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-4">{{ __('messages.export_pdf') }} / {{ __('messages.export_excel') }}</p>
                <div class="flex flex-wrap gap-3">
                    <x-btn variant="danger" icon="download" id="btn-pdf" href="#">
                        {{ __('messages.export_pdf') }}
                    </x-btn>
                    <x-btn id="btn-excel" href="#" icon="download" class="bg-emerald-600 text-on-primary hover:bg-emerald-700 active:scale-[0.98]">
                        {{ __('messages.export_excel') }}
                    </x-btn>
                </div>
            </div>
        </form>
    </x-card>
</div>

@push('scripts')
<script>
(function() {
    var form = document.querySelector('form[action="{{ route('reports.index') }}"]');
    var dateFrom = document.getElementById('date_from');
    var dateTo = document.getElementById('date_to');
    var btnPdf = document.getElementById('btn-pdf');
    var btnExcel = document.getElementById('btn-excel');
    if (btnPdf) {
        btnPdf.addEventListener('click', function(e) {
            e.preventDefault();
            var from = dateFrom ? dateFrom.value : '';
            var to = dateTo ? dateTo.value : '';
            if (from && to) {
                window.location.href = '{{ route('reports.pdf') }}?date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to);
            } else {
                window.location.href = '{{ route('reports.pdf') }}?date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to);
            }
        });
    }
    if (btnExcel) {
        btnExcel.addEventListener('click', function(e) {
            e.preventDefault();
            var from = dateFrom ? dateFrom.value : '';
            var to = dateTo ? dateTo.value : '';
            window.location.href = '{{ route('reports.excel') }}?date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to);
        });
    }
})();
</script>
@endpush
@endsection

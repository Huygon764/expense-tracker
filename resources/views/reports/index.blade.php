@extends('layouts.app')

@section('title', 'Báo cáo')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Báo cáo</h1>

    @if($errors->any())
        <div class="rounded-md border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-3 text-sm text-red-800 dark:text-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('reports.index') }}" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 flex flex-wrap items-end gap-4">
        <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Từ ngày</label>
            <input type="date" name="date_from" id="date_from" value="{{ old('date_from', request('date_from', now()->startOfMonth()->format('Y-m-d'))) }}"
                class="mt-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 text-sm">
        </div>
        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Đến ngày</label>
            <input type="date" name="date_to" id="date_to" value="{{ old('date_to', request('date_to', now()->format('Y-m-d'))) }}"
                class="mt-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 text-sm">
        </div>
        <div class="flex gap-2">
            <a href="#" id="btn-pdf" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500">Tải PDF</a>
            <a href="#" id="btn-excel" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-500">Tải Excel</a>
        </div>
    </form>

    <p class="text-sm text-gray-500 dark:text-gray-400">Chọn khoảng thời gian rồi bấm Tải PDF hoặc Tải Excel để tải báo cáo (tổng chi, theo danh mục, bảng chi tiêu).</p>
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

@props([
    'name',
    'value' => '',
    'required' => false,
    'label' => __('messages.amount'),
    'disabled' => false,
])
@php
    $rawValue = old($name, $value);
    if ($rawValue !== '' && $rawValue !== null) {
        $rawValue = is_numeric($rawValue) ? (float) $rawValue : 0;
    } else {
        $rawValue = '';
    }
    $displayValue = $rawValue !== '' ? number_format((float) $rawValue, 0, '', ',') : '';
@endphp
<div class="amount-input-wrapper" data-amount-name="{{ $name }}">
    <label for="{{ $name }}-display" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>

    <div class="flex flex-wrap gap-2 mb-2 mt-1">
        <button type="button" class="amount-quick-btn rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600" data-amount="50000">50k</button>
        <button type="button" class="amount-quick-btn rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600" data-amount="100000">100k</button>
        <button type="button" class="amount-quick-btn rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600" data-amount="200000">200k</button>
        <button type="button" class="amount-quick-btn rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600" data-amount="500000">500k</button>
        <button type="button" class="amount-quick-btn rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600" data-amount="1000000">1M</button>
        <button type="button" class="amount-quick-btn rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600" data-amount="2000000">2M</button>
        <button type="button" class="amount-quick-btn amount-quick-custom rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600" data-amount="custom">Custom</button>
    </div>

    <input type="hidden" name="{{ $name }}" id="{{ $name }}-real" value="{{ $rawValue !== '' ? (int) round((float) $rawValue) : '' }}" @if($required) required @endif>

    <input type="text"
           id="{{ $name }}-display"
           inputmode="numeric"
           autocomplete="off"
           value="{{ $displayValue }}"
           class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-gray-900 dark:text-gray-100 @error($name) border-red-500 @enderror"
           @if($disabled) disabled @endif
    >

    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
<script>
(function() {
    var wrapper = document.querySelector('.amount-input-wrapper[data-amount-name="{{ $name }}"]');
    if (!wrapper) return;
    var hidden = document.getElementById('{{ $name }}-real');
    var display = document.getElementById('{{ $name }}-display');
    if (!hidden || !display) return;

    function formatNum(n) {
        if (n === '' || n === null || isNaN(n)) return '';
        var s = Math.floor(Math.abs(n)).toString();
        return s.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    function unformatNum(v) {
        if (v === null || v === undefined) return 0;
        var s = String(v).replace(/[^\d]/g, '');
        return s === '' ? 0 : parseInt(s, 10);
    }
    function getDigitCountBefore(str, pos) {
        return (str.slice(0, pos).match(/\d/g) || []).length;
    }
    function setCursorToDigitIndex(el, idx) {
        var str = el.value, count = 0, i;
        for (i = 0; i <= str.length; i++) {
            if (count === idx) { el.setSelectionRange(i, i); return; }
            if (/\d/.test(str[i])) count++;
        }
        el.setSelectionRange(str.length, str.length);
    }

    function syncHidden(val) {
        var num = val === '' || val === null ? '' : (typeof val === 'number' ? val : unformatNum(val));
        hidden.value = num === '' ? '' : (Math.round(Number(num)) || '');
    }

    function setValue(num) {
        if (num === '' || num === null || num === undefined) {
            display.value = '';
            hidden.value = '';
            return;
        }
        var n = typeof num === 'number' ? num : parseInt(num, 10);
        if (isNaN(n)) n = 0;
        hidden.value = n;
        display.value = formatNum(n);
    }

    wrapper.querySelectorAll('.amount-quick-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var amt = this.getAttribute('data-amount');
            if (amt === 'custom') {
                setValue('');
                display.focus();
                return;
            }
            var num = parseInt(amt, 10);
            if (!isNaN(num)) setValue(num);
        });
    });

    display.addEventListener('input', function() {
        var start = this.selectionStart != null ? this.selectionStart : this.value.length;
        var digitCountBefore = getDigitCountBefore(this.value, start);
        var raw = unformatNum(this.value);
        this.value = formatNum(raw);
        setCursorToDigitIndex(this, digitCountBefore);
        syncHidden(raw);
    });

    display.addEventListener('blur', function() {
        var raw = hidden.value ? parseInt(hidden.value, 10) : '';
        if (raw !== '' && !isNaN(raw)) display.value = formatNum(raw);
    });
})();
</script>
@endpush

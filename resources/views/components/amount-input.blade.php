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
    <label for="{{ $name }}-display" class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant mb-1.5">{{ $label }}</label>

    <div class="flex flex-wrap gap-1.5 mb-3">
        @foreach([['50000', '50k'], ['100000', '100k'], ['200000', '200k'], ['500000', '500k'], ['1000000', '1M'], ['2000000', '2M']] as [$amt, $lbl])
            <button type="button"
                    class="amount-quick-btn px-3 py-1.5 rounded-full text-xs font-semibold bg-surface-container text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-colors"
                    data-amount="{{ $amt }}">{{ $lbl }}</button>
        @endforeach
        <button type="button"
                class="amount-quick-btn amount-quick-custom px-3 py-1.5 rounded-full text-xs font-semibold bg-surface-container text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-colors"
                data-amount="custom">Custom</button>
    </div>

    <input type="hidden" name="{{ $name }}" id="{{ $name }}-real" value="{{ $rawValue !== '' ? (int) round((float) $rawValue) : '' }}" @if($required) required @endif>

    <input type="text"
           id="{{ $name }}-display"
           inputmode="numeric"
           autocomplete="off"
           value="{{ $displayValue }}"
           class="block w-full rounded-xl bg-surface-container-low text-on-surface text-sm py-3 px-4 placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-lowest transition-colors @error($name) ring-2 ring-error/30 @enderror"
           @if($disabled) disabled @endif
    >

    @error($name)
        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
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

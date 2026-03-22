@extends('layouts.app')

@section('page-title', __('messages.edit_recurring'))

@section('content')
<div class="max-w-lg mx-auto">
    <x-card>
        <h1 class="font-display text-2xl font-bold text-on-surface mb-6">{{ __('messages.edit_recurring') }}</h1>

        <form method="POST" action="{{ route('recurring-expenses.update', $recurringExpense) }}" class="space-y-5" id="recurring-form">
            @csrf
            @method('PATCH')

            <x-form-input name="title" :label="__('messages.title')" :value="old('title', $recurringExpense->title)" :required="true" maxlength="255" />

            <x-amount-input name="amount" :label="__('messages.amount')" :value="$recurringExpense->amount" :required="true" />

            <x-form-select name="category_id" :label="__('messages.category')">
                <option value="">{{ __('messages.no_category') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id', $recurringExpense->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </x-form-select>

            <x-form-select name="type" :label="__('messages.frequency')" :required="true">
                <option value="weekly" {{ old('type', $recurringExpense->type) === 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                <option value="monthly" {{ old('type', $recurringExpense->type) === 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
            </x-form-select>

            <div id="day-of-week-wrap" style="display: {{ old('type', $recurringExpense->type) === 'weekly' ? 'block' : 'none' }};">
                <x-form-select name="day_of_week" :label="__('messages.day_of_week')">
                    <option value="0" {{ old('day_of_week', $recurringExpense->day_of_week) === 0 ? 'selected' : '' }}>{{ __('messages.sunday') }}</option>
                    <option value="1" {{ old('day_of_week', $recurringExpense->day_of_week) === 1 ? 'selected' : '' }}>{{ __('messages.monday') }}</option>
                    <option value="2" {{ old('day_of_week', $recurringExpense->day_of_week) === 2 ? 'selected' : '' }}>{{ __('messages.tuesday') }}</option>
                    <option value="3" {{ old('day_of_week', $recurringExpense->day_of_week) === 3 ? 'selected' : '' }}>{{ __('messages.wednesday') }}</option>
                    <option value="4" {{ old('day_of_week', $recurringExpense->day_of_week) === 4 ? 'selected' : '' }}>{{ __('messages.thursday') }}</option>
                    <option value="5" {{ old('day_of_week', $recurringExpense->day_of_week) === 5 ? 'selected' : '' }}>{{ __('messages.friday') }}</option>
                    <option value="6" {{ old('day_of_week', $recurringExpense->day_of_week) === 6 ? 'selected' : '' }}>{{ __('messages.saturday') }}</option>
                </x-form-select>
            </div>

            <div id="day-of-month-wrap" style="display: {{ old('type', $recurringExpense->type) === 'monthly' ? 'block' : 'none' }};">
                <x-form-select name="day_of_month" :label="__('messages.day_of_month')">
                    @for($d = 1; $d <= 31; $d++)
                        <option value="{{ $d }}" {{ old('day_of_month', $recurringExpense->day_of_month) == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endfor
                </x-form-select>
            </div>

            <div>
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $recurringExpense->is_active) ? 'checked' : '' }}
                        class="rounded-md w-5 h-5 bg-surface-container-low text-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <span class="text-sm font-semibold text-on-surface">{{ __('messages.active') }}</span>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <x-btn variant="primary" type="submit" icon="check">
                    {{ __('messages.update') }}
                </x-btn>
                <x-btn variant="ghost" :href="route('recurring-expenses.index')" icon="arrow-left">
                    {{ __('messages.cancel') }}
                </x-btn>
            </div>
        </form>
    </x-card>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    var isWeekly = this.value === 'weekly';
    document.getElementById('day-of-week-wrap').style.display = isWeekly ? 'block' : 'none';
    document.getElementById('day-of-month-wrap').style.display = isWeekly ? 'none' : 'block';
});
</script>
@endsection

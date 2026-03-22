<div class="max-w-lg mx-auto w-full">
    <x-card class="p-8">
        {{-- Logo --}}
        <div class="text-center mb-6">
            <a href="/" class="inline-block font-display text-2xl font-bold text-primary tracking-tight">
                {{ __('messages.app_name') }}
            </a>
        </div>

        {{-- Step progress indicator --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            {{-- Step 1: completed --}}
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-on-primary text-sm font-semibold">
                    <x-icon name="check" class="w-4 h-4" />
                </span>
                <span class="text-xs font-medium text-on-surface-variant hidden sm:inline">{{ __('messages.categories') }}</span>
            </div>

            {{-- Connector --}}
            <div class="w-12 h-0.5 rounded-full bg-primary"></div>

            {{-- Step 2: active --}}
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gradient-primary text-on-primary text-sm font-bold shadow-editorial-sm">
                    2
                </span>
                <span class="text-xs font-semibold text-on-surface hidden sm:inline">{{ __('messages.budgets') }}</span>
            </div>
        </div>

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="font-display text-2xl font-bold text-on-surface">{{ __('messages.set_budget') }}</h1>
            <p class="text-sm text-on-surface-variant mt-1.5">{{ __('messages.set_budget_help') }}</p>
        </div>

        <form method="POST" action="{{ route('onboarding.storeStep2') }}" class="space-y-6">
            @csrf

            {{-- Period selector --}}
            <x-form-select name="type" :label="__('messages.period')" required>
                <option value="weekly" {{ old('type') === 'weekly' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                <option value="monthly" {{ old('type', 'monthly') === 'monthly' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
            </x-form-select>

            {{-- Amount input with quick buttons --}}
            <x-amount-input name="amount" :value="old('amount')" required :label="__('messages.amount')" />

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-2" style="border-top: 1px solid rgba(191,201,200,0.15);">
                <x-btn variant="ghost" :href="route('onboarding.step1')" icon="arrow-left">
                    {{ __('messages.back') }}
                </x-btn>
                <x-btn type="submit" icon="check">
                    {{ __('messages.finish') }}
                </x-btn>
            </div>
        </form>
    </x-card>
</div>

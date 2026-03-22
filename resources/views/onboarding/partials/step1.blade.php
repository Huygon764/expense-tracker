<div class="bg-surface-container-lowest rounded-2xl shadow-editorial overflow-hidden">
    <div class="flex flex-col lg:flex-row min-h-[540px]">
        {{-- Left: Branded gradient panel --}}
        <div class="lg:w-5/12 bg-gradient-primary p-8 lg:p-10 flex flex-col justify-between text-on-primary relative overflow-hidden">
            {{-- Decorative circles --}}
            <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-20 -left-10 w-56 h-56 rounded-full bg-white/5"></div>

            <div class="relative z-10">
                {{-- Logo --}}
                <a href="/" class="inline-block font-display text-2xl font-bold tracking-tight text-white">
                    {{ __('messages.app_name') }}
                </a>

                <div class="mt-10 lg:mt-16">
                    <h1 class="font-display text-3xl lg:text-4xl font-bold leading-tight">
                        Master your<br>wealth
                    </h1>
                    <p class="mt-3 text-white/70 text-sm leading-relaxed max-w-xs">
                        {{ __('messages.select_categories_help') }}
                    </p>
                </div>
            </div>

            {{-- Step indicator dots --}}
            <div class="relative z-10 flex items-center gap-3 mt-8">
                <span class="w-8 h-2 rounded-full bg-white"></span>
                <span class="w-2 h-2 rounded-full bg-white/40"></span>
            </div>
        </div>

        {{-- Right: Category selection --}}
        <div class="lg:w-7/12 p-6 lg:p-10 flex flex-col">
            <div class="mb-1">
                <x-badge color="primary">{{ __('messages.step') }} 1/2</x-badge>
            </div>
            <h2 class="font-display text-xl font-bold text-on-surface mt-2">{{ __('messages.select_categories') }}</h2>
            <p class="text-sm text-on-surface-variant mt-1 mb-6">{{ __('messages.select_categories_help') }}</p>

            <form method="POST" action="{{ route('onboarding.storeStep1') }}" class="flex flex-col flex-1">
                @csrf

                <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5 flex-1 content-start">
                    @foreach($defaultCategories as $dc)
                        <label class="group relative flex items-center gap-2.5 p-3 rounded-xl bg-surface-container-low cursor-pointer transition-all duration-150 hover:bg-primary/5 has-[:checked]:bg-primary/5 has-[:checked]:ring-2 has-[:checked]:ring-primary">
                            <input type="checkbox" name="default_category_ids[]" value="{{ $dc->id }}"
                                {{ in_array($dc->id, old('default_category_ids', [])) ? 'checked' : '' }}
                                class="sr-only peer">

                            {{-- Icon --}}
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-surface-container text-lg shrink-0"
                                  @if($dc->color) style="background-color: {{ $dc->color }}20; color: {{ $dc->color }};" @endif>
                                {{ $dc->icon ?? '...' }}
                            </span>

                            {{-- Name --}}
                            <span class="text-sm font-medium text-on-surface truncate">{{ $dc->name }}</span>

                            {{-- Check indicator --}}
                            <span class="absolute top-2 right-2 w-5 h-5 rounded-full bg-primary text-white items-center justify-center hidden peer-checked:flex">
                                <x-icon name="check" class="w-3 h-3" />
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('default_category_ids')
                    <p class="text-sm text-error mt-3">{{ $message }}</p>
                @enderror
                @if($errors->has('default_category_ids.*'))
                    <p class="text-sm text-error mt-3">{{ __('messages.select_at_least_one') }}</p>
                @endif

                <div class="mt-6 pt-4" style="border-top: 1px solid rgba(191,201,200,0.15);">
                    <x-btn type="submit" icon="chevron-right">
                        {{ __('messages.next') }}
                    </x-btn>
                </div>
            </form>
        </div>
    </div>
</div>

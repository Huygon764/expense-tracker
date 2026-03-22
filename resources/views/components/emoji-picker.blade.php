@props(['target' => 'icon'])

<div x-data="{ open: false }" class="relative">
    <button type="button"
            @click="open = !open"
            class="px-3 py-2 text-xs font-semibold rounded-lg bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors">
        {{ __('messages.pick_emoji') }}
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute z-50 mt-2 w-72 rounded-2xl bg-surface-container-lowest shadow-editorial-lg overflow-hidden"
         style="display: none;">

        <div class="max-h-64 overflow-y-auto p-3 space-y-3">
            @php
                $groups = [
                    'emoji_popular' => ['🍔','🚗','🛒','🎬','💡','💊','📚','✈️','🎁','📦'],
                    'emoji_food' => ['🍔','🍕','🍜','🍣','🍺','☕','🧁','🍎'],
                    'emoji_transport' => ['🚗','🚕','🚌','🚇','✈️','🚲','⛽'],
                    'emoji_shopping' => ['🛒','👕','👟','💄','📱','💻','🎮'],
                    'emoji_other' => ['💰','💳','🏠','🏥','🎓','💼','🔧'],
                ];
            @endphp

            @foreach($groups as $key => $emojis)
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-on-surface-variant mb-1.5">{{ __('messages.' . $key) }}</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($emojis as $emoji)
                            <button type="button"
                                    @click="document.getElementById('{{ $target }}').value = '{{ $emoji }}'; document.getElementById('{{ $target }}').dispatchEvent(new Event('input')); open = false;"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg text-lg hover:bg-surface-container-high transition-colors">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

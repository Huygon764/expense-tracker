{{-- Global confirm modal - include once in layout --}}
<div x-data="{
        open: false,
        title: '',
        message: '',
        formEl: null,
        show(msg, form) {
            this.message = msg;
            this.formEl = form;
            this.open = true;
        },
        confirm() {
            this.open = false;
            if (this.formEl) {
                this.formEl.removeAttribute('onsubmit');
                this.formEl.submit();
            }
        },
        cancel() {
            this.open = false;
            this.formEl = null;
        }
     }"
     x-on:confirm-modal.window="show($event.detail.message, $event.detail.form)"
     x-cloak>

    {{-- Backdrop --}}
    <template x-teleport="body">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background: rgba(25, 28, 29, 0.5); backdrop-filter: blur(4px);">

            {{-- Modal --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="cancel()"
                 @keydown.escape.window="cancel()"
                 class="w-full max-w-sm rounded-2xl bg-surface-container-lowest shadow-editorial-lg p-6">

                {{-- Icon --}}
                <div class="w-12 h-12 rounded-xl bg-error-container flex items-center justify-center mx-auto mb-4">
                    <x-icon name="alert-triangle" class="w-6 h-6 text-error" />
                </div>

                {{-- Message --}}
                <p class="text-sm text-on-surface text-center font-medium" x-text="message"></p>

                {{-- Actions --}}
                <div class="flex items-center gap-3 mt-6">
                    <button @click="cancel()"
                            type="button"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-xl bg-surface-container-high text-on-surface hover:bg-surface-container-highest transition-colors cursor-pointer">
                        {{ __('messages.cancel') }}
                    </button>
                    <button @click="confirm()"
                            type="button"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-xl bg-error text-on-primary hover:bg-error/90 transition-colors cursor-pointer">
                        {{ __('messages.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

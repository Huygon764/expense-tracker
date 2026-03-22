<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('messages.app_name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-on-surface font-sans flex flex-col items-center justify-center p-4">
    <main class="w-full @yield('container_class', 'max-w-md') space-y-6">
        {{-- Logo --}}
        @section('logo')
        <div class="text-center">
            <a href="/" class="inline-block font-display text-2xl font-bold text-primary tracking-tight">
                {{ __('messages.app_name') }}
            </a>
        </div>
        @show

        @if (session('status'))
            <div class="rounded-2xl bg-tertiary-container/50 p-4 text-sm text-on-surface">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl bg-error-container/50 p-4 text-sm text-error">
                {{ session('error') }}
            </div>
        @endif

        @section('card')
        <div class="bg-surface-container-lowest rounded-2xl shadow-editorial p-8">
            @yield('content')
        </div>
        @show
    </main>

    <script>
    (function() {
        var loadingText = '{{ __("messages.save") }}...';
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form && form.tagName === 'FORM' && form.method && form.method.toLowerCase() === 'post') {
                var btn = form.querySelector('button[type="submit"]');
                if (btn && !btn.disabled) {
                    if (!btn.hasAttribute('data-loading-original')) {
                        btn.setAttribute('data-loading-original', btn.textContent.trim() || btn.value || '');
                    }
                    btn.disabled = true;
                    btn.style.opacity = '0.6';
                    btn.textContent = loadingText;
                }
            }
        }, true);
    })();
    </script>
    @stack('scripts')
</body>
</html>

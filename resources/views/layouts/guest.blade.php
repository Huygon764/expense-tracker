<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-col items-center justify-center p-4">
    <main class="w-full max-w-md space-y-4">
        @if (session('status'))
            <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-md bg-red-50 dark:bg-red-900/20 p-4 text-sm text-red-800 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
    <script>
    (function() {
        var loadingText = 'Đang lưu…';
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form && form.tagName === 'FORM' && form.method && form.method.toLowerCase() === 'post') {
                var btn = form.querySelector('button[type="submit"]');
                if (btn && !btn.disabled) {
                    if (!btn.hasAttribute('data-loading-original')) {
                        btn.setAttribute('data-loading-original', btn.textContent.trim() || btn.value || '');
                    }
                    btn.disabled = true;
                    btn.textContent = loadingText;
                }
            }
        }, true);
    })();
    </script>
</body>
</html>

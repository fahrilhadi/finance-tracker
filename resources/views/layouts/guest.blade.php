<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="shortcut icon" href="{{ asset('money-bag.png') }}" type="image/x-icon">
        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center sm:pt-0 px-4 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="text-2xl" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white border border-gray-200 overflow-hidden rounded-xl">
                {{ $slot }}
            </div>
        </div>

        <script>
            document.getElementById('form').addEventListener('submit', function(e) {
                const btn = document.getElementById('submitBtn');
                const textBtn = document.getElementById('textBtn');
                const spinner = document.getElementById('spinner');

                textBtn.innerText = 'Processing...';
                spinner.classList.remove('hidden');

                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });
        </script>
    </body>
</html>

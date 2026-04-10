<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin Parking</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased selection:bg-blue-500 selection:text-white">
        <div class="min-h-screen bg-slate-50 dark:bg-gray-950 flex flex-col justify-center items-center p-4 sm:p-6">

            <div class="w-full max-w-[1100px] transition-all duration-300">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center md:hidden">
                <p class="text-sm text-gray-400 dark:text-gray-600">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Panel Pengurus Parkir.
                </p>
            </div>
        </div>
    </body>
</html>

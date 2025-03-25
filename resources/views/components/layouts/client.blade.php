<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-base-200/50 dark:bg-base-200 flex items-center justify-center">
    <main class="flex flex-col w-11/12 max-w-2xl mx-auto gap-3 my-6">
        <img src="{{ asset('cics.png') }}" alt="icon" class="w-[100px] max-w-[150px] mx-auto">
        <div>{{ $slot }}</div>
    </main>

    <x-toast />
</body>

</html>

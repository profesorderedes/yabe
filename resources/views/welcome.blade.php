<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} · Disponibilidad</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body class="min-h-screen bg-white text-gray-900 antialiased">
        <main class="min-h-screen">
            <div id="app" class="mx-auto w-full max-w-2xl px-4 py-12 sm:py-16"></div>
        </main>
    </body>
</html>
<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ERP Sistem</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>

<body class="h-full bg-gradient-to-br from-slate-900 via-gray-900 to-black flex items-center justify-center">

    <!-- WRAPPER -->
    <div class="w-full flex items-center justify-center px-4">

        <!-- LOGIN CARD -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 animate-fade-in">

            <!-- LOGO -->
            <div class="text-center mb-6">
                <div class="flex justify-center mb-3">
                    <img src="/logo.png" alt="Logo" class="w-16 h-16 object-contain">
                </div>

                <h2 class="text-2xl font-bold text-gray-800">
                    ERP Sistem
                </h2>

                <p class="text-sm text-gray-500 mt-2">
                    Prijavite se na poslovni sistem kompanije <b>Firma</b>
                </p>
            </div>

            <!-- CONTENT -->
            {{ $slot }}

            <!-- FOOTER -->
            <div class="mt-6 text-xs text-gray-400 text-center">
                Problemi sa pristupom? Kontaktirajte IT podršku.<br>
                Kontakt email: <a href="mailto:it@firma.com">it@firma.com</a><br>
                Telefon: 2201
            </div>

        </div>

    </div>

</body>
</html>
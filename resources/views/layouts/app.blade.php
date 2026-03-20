<!DOCTYPE html>
<html>

<head>
    <title>ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100">

    <div class="flex h-screen">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col">

            <div class="p-4 text-xl font-bold border-b border-gray-700">
                ERP Sistem
            </div>

            <nav class="flex-1 p-4 space-y-2">

                <a href="/dashboard"
                    class="block px-3 py-2 rounded {{ request()->is('dashboard') ? 'bg-gray-700 border-l-4 border-blue-400' : 'hover:bg-gray-700' }}">
                    📊 Dashboard
                </a>

                <a href="/employees"
                    class="block px-3 py-2 rounded {{ request()->is('employees*') ? 'bg-gray-700 border-l-4 border-blue-400' : 'hover:bg-gray-700' }}">
                    👥 Radnici
                </a>

                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-700">
                    🏢 Organizacija
                </a>

                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-700">
                    📅 Prisustvo
                </a>

            </nav>

            <div class="p-4 border-t border-gray-700 text-sm">
                Ulogovan korisnik
            </div>

        </aside>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col">

            <!-- TOPBAR -->
            <header class="bg-white shadow px-6 py-3 flex justify-between items-center">
                <h1 class="font-semibold text-lg">@yield('title')</h1>

                <div class="flex items-center gap-4">

                    <div class="text-sm text-gray-600">
                        👤 {{ auth()->user()->name ?? 'User' }}
                    </div>

                    <!-- LOGOUT -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-red-500 hover:text-red-700 text-sm font-medium">
                            Logout
                        </button>
                    </form>

                </div>
            </header>

            <!-- CONTENT -->
            <main class="p-6 overflow-y-auto">
                @yield('content')
            </main>

        </div>

    </div>

</body>

</html>
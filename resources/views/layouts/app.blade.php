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

            <div class="p-4 text-xl font-bold border-b border-gray-700 text-center">
                Poslovni softwer<br>Kompanije
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

                <a href="{{ route('organizational-units.index') }}"
                class="block px-3 py-2 rounded {{ request()->is('organizational-units*') ? 'bg-gray-700 border-l-4 border-blue-400' : 'hover:bg-gray-700' }}">
                    🏢 Organizacija
                </a>

                <a href="#" class="block px-3 py-2 rounded hover:bg-gray-700">
                    📅 Prisustvo
                </a>

            </nav>

            @auth
            <div class="text-sm text-gray-600">
                Ulogovan: 
                <span 
                    class="font-semibold cursor-pointer hover:underline hover:text-blue-600"
                    onclick="openMyProfile()"
                >
                    {{ auth()->user()->employee?->first_name }}
                    {{ auth()->user()->employee?->last_name ?? auth()->user()->email }}
                </span>
            </div>
            @endauth

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

    <div id="toast" class="fixed top-5 right-5 hidden px-4 py-2 rounded text-white shadow-lg z-50"></div>
    <div id="auth-data"
     data-employee-id="{{ auth()->user()->employee_id ?? '' }}">
</div>

</body>
<script>
function openMyProfile(){

    const el = document.getElementById('auth-data');
    const employeeId = el?.dataset.employeeId;

    if(!employeeId){
        alert('Korisnik nije povezan sa zaposlenim');
        return;
    }

    showEmployeeDetail(employeeId);
}
</script>
</html>
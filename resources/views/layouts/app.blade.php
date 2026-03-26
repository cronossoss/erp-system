<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html>

<head>
    <title>ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <div class="flex h-screen">

        <!-- SIDEBAR -->
        <aside class="w-56 bg-gray-900 text-white flex flex-col">

            <div class="p-4 text-xl font-bold border-b border-gray-700 text-center">
                Poslovni softwer<br>Kompanije
            </div>

            <nav class="flex-1 p-4 space-y-2">

                {{-- DASHBOARD --}}
                <a href="/dashboard"
                class="block px-3 py-2 rounded hover:bg-gray-700 transition {{ request()->is('dashboard') ? 'bg-blue-700' : '' }}">
                    🏠 Dashboard
                </a>

                {{-- ORGANIZACIJA --}}
                <div class="mt-6 text-xs uppercase text-gray-500 font-semibold">
                    Organizacija
                </div>

                <a href="{{ route('organizacija.overview') }}"
                class="block px-3 py-2 rounded mt-2 hover:bg-gray-700 transition {{ request()->is('organizacija') ? 'bg-blue-700' : '' }}">
                    📊 Pregled
                </a>

                <a href="{{ route('organizational-groups.index') }}"
                class="block px-3 py-2 rounded  hover:bg-gray-700 transition {{ request()->is('organizational-groups*') ? 'bg-blue-700' : '' }}">
                    🧩 Celine
                </a>

                <a href="{{ route('organizational-units.index') }}"
                class="block px-3 py-2 rounded  hover:bg-gray-700 transition {{ request()->is('organizational-units*') ? 'bg-blue-700' : '' }}">
                    🗂 Jedinice
                </a>

                {{-- ZAPOSLENI --}}
                <a href="{{ route('employees.index') }}"
                class="block px-3 py-2 rounded mt-2  hover:bg-gray-700 transition {{ request()->is('employees*') ? 'bg-blue-700' : '' }}">
                    👥 Zaposleni
                </a>

                <div class="mt-6 text-xs uppercase text-gray-500 font-semibold">
                    Podešavanja
                </div>

                <a href="{{ route('contract-types.index') }}"
                class="block px-3 py-2 rounded mt-2  hover:bg-gray-700 transition {{ request()->is('contract-types*') ? 'bg-blue-700' : '' }}">
                    ⚙️ Vrste ugovora
                </a>

                <a href="{{ route('work-entry-types.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-800">
                    📋 Vrste unosa
                </a>


            </nav>

            @auth
            <div class="text-sm text-gray-600">
                Ulogovan: 
                <span 
                    class="font-semibold text-white cursor-pointer hover:underline hover:text-blue-600"
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
            <header class="flex justify-between items-center px-4 py-2 bg-gray-100 border-b">

                {{-- LEVO --}}
                <div>
                    @if(auth()->user()->employee)
                        Dobrodošao: {{ auth()->user()->employee->first_name }}
                    @endif
                </div>

                {{-- DESNO --}}
                <div class="flex items-center gap-4">
                    👤 <span class="font-semibold">{{ auth()->user()->name }}</span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="font-semibold text-red-500 hover:underline">
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

    
    <div id="auth-data"
     data-employee-id="{{ auth()->user()->employee_id ?? '' }}">
    </div>
</div>
<div id="toast"
        class="hidden fixed top-5 left-1/2 -translate-x-1/2 px-4 py-2 rounded text-white bg-green-600 shadow-lg z-[9999]" style="position: fixed; z-index: 999999;">
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
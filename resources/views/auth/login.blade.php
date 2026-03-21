<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />


<div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

   

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <input type="email" name="email" placeholder="Email"
            class="w-full border rounded-lg px-3 py-2 mb-3">

        <input type="password" name="password" placeholder="Lozinka"
            class="w-full border rounded-lg px-3 py-2 mb-4">

        <button
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">
            Prijavi se
        </button>
    </form>
    <br>

    <div class="flex items-center mb-4">
        <input type="checkbox" name="remember" class="mr-2">
        <label class="text-sm text-gray-600">Zapamti me</label>
    </div>

    

</div>
</x-guest-layout>

@extends('layouts.app')

@section('content')
<div class="p-4">

    <h2 class="text-xl font-semibold mb-4">Dashboard</h2>

    <div class="grid grid-cols-3 gap-4">

        <div class="bg-white p-4 rounded shadow">
            <h5>Korisnici</h5>
            <h2 class="text-2xl font-bold">{{ $usersCount }}</h2>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h5>Org. jedinice</h5>
            <h2 class="text-2xl font-bold">{{ $organizationalUnitsCount }}</h2>
        </div>

    </div>

</div>
@endsection
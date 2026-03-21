@extends('layouts.app')

@section('content')
<div class="p-4">

    <h2 class="text-xl font-semibold mb-4">Dashboard</h2>

    <div class="bg-white p-4 rounded shadow mb-4">
    <h4 class="text-lg font-semibold">
        Dobrodošao,
        @if(auth()->user()->employee)
            {{ auth()->user()->employee->first_name }}
        @else
            <span class="text-red-500">Nema zaposlenog</span>
        @endif
        {{ auth()->user()->employee->last_name }}
    </h4>

    <p class="text-sm text-gray-600">
        Organizacija:
        {{ auth()->user()->employee->organizationalUnit->name ?? '-' }}
    </p>
</div>

    <div class="grid grid-cols-3 gap-4">

        <div class="bg-white p-4 rounded shadow">
            <h5>Zaposleni</h5>
            <h2 class="text-2xl font-bold">{{ $employeesCount }}</h2>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h5>Org. jedinice</h5>
            <h2 class="text-2xl font-bold">{{ $organizationalUnitsCount }}</h2>
        </div>

    </div>

</div>
@endsection
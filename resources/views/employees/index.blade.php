<!-- // resources/views/employees/index.blade.php -->

@extends('layouts.app')

@section('title', 'Radnici')

@section('content')

<div class="bg-white rounded shadow p-4">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Lista zaposlenih</h2>

        <input 
            type="text" 
            id="searchInput"
            placeholder="Pretraga zaposlenih..."
            class="border px-3 py-2 rounded w-64"
            onkeyup="searchEmployees()">

        <button onclick="openEmployeeModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Dodaj zaposlenog
        </button>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-200 text-xs uppercase">
            <tr>
                <th class="p-3 text-left">Matični broj</th>
                <th class="p-3 text-left">Ime</th>
                <th class="p-3 text-left">Pozicija</th>
                <th class="p-3 text-left">Jedinica</th>
                <th class="p-3 text-left">Ugovor</th>
                <th class="p-3 text-right">Akcije</th>
            </tr>
        </thead>

        <tbody>
            @foreach($employees as $employee)
            <tr data-id="{{ $employee->id }}" class="employee-row cursor-pointer hover:bg-gray-100">
                <td class="p-3">{{ $employee->employee_number }}</td>
                <td class="p-3">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                <td class="p-3">{{ $employee->position }}</td>
                <td class="p-3">{{ $employee->organizationalUnit->name ?? '-' }}</td>
                <td class="p-3">{{ $employee->contractType->name ?? '-' }}</td>

                <td class="p-3 text-right space-x-2">

                    <button class="delete-btn bg-red-500 text-white px-2 py-1 rounded"
                        data-id="{{ $employee->id }}">
                        🗑
                    </button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl p-6 w-96 text-center">

        <h3 class="text-lg font-semibold mb-4">
            Da li ste sigurni?
        </h3>

        <p class="text-gray-500 mb-6">
            Ova akcija je nepovratna.
        </p>

        <div class="flex justify-center gap-4">

            <button onclick="confirmDelete()" class="bg-red-600 text-white px-4 py-2 rounded">
                Da, obriši
            </button>

            <button onclick="closeDeleteModal()" class="bg-gray-300 px-4 py-2 rounded">
                Otkaži
            </button>

        </div>

    </div>

</div>

<div id="units-data"
     data-units='@json($units)'>
</div>


@include('modals.employee-modal')

@vite(['resources/js/app.js'])

@endsection
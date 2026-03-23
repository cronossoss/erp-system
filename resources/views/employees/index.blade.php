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

        <button onclick="openModal()"
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
            <tr onclick="window.openEmployeeModal({{ $employee->id }})"
                                    class="cursor-pointer hover:bg-gray-100">
                <td class="p-3">{{ $employee->employee_number }}</td>
                <td class="p-3">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                <td class="p-3">{{ $employee->position }}</td>
                <td class="p-3">{{ $employee->organizationalUnit->name ?? '-' }}</td>
                <td class="p-3">{{ $employee->contract_type }}</td>

                <td class="p-3 text-right space-x-2">

                    <button class="bg-yellow-400 px-2 py-1 rounded"
                        data-id="{{ $employee->id }}"
                        data-first="{{ $employee->first_name }}"
                        data-last="{{ $employee->last_name }}"
                        data-position="{{ $employee->position }}"
                        data-unit="{{ $employee->organizational_unit_id }}"
                        data-employee_number="{{ $employee->employee_number }}"
                        data-contract_type="{{ $employee->contract_type }}"
                        onclick="event.stopPropagation(); openEditModal(this)">
                        ✏️
                    </button>

                    <button class="bg-red-500 text-white px-2 py-1 rounded"
                        data-id="{{ $employee->id }}"
                        onclick="event.stopPropagation(); deleteEmployee(this)">
                        🗑
                    </button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    

</div>

<div id="units-data"
     data-units='@json($units)'>
</div>

@include('employees.partials.modals')
@include('modals.employee-modal')

@vite(['resources/js/app.js'])

@endsection
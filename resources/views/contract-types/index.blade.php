@extends('layouts.app')

@section('content')

<div class="bg-white rounded shadow p-4">

    <div class="flex justify-between mb-4">
        <h2 class="text-lg font-semibold">Vrste ugovora</h2>

        <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded">
            + Nova vrsta
        </button>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-200 text-xs uppercase">
            <tr>
                <th class="p-2 text-left w-32">Šifra</th>
                <th class="p-2 text-left">Naziv</th>
                <th class="p-2 text-right w-32">Akcije</th>
            </tr>
        </thead>
        <tbody>
            @foreach($types as $t)
<tr class="border-b">

    {{-- ŠIFRA --}}
    <td class="p-2">
        <span class="bg-gray-100 px-2 py-1 rounded font-mono">
            {{ $t->code }}
        </span>
    </td>

    {{-- NAZIV --}}
    <td class="p-2">
        {{ $t->name }}
    </td>

    {{-- AKCIJE --}}
    <td class="p-2 text-right space-x-2">

        <button 
            data-type='@json($t)'
            onclick="editType(this.dataset.type)"
            class="bg-yellow-400 px-2 py-1 rounded">
            ✏️
        </button>

        <button 
            onclick="deleteType({{ $t->id }})"
            class="bg-red-500 text-white px-2 py-1 rounded">
            🗑
        </button>

    </td>

</tr>
@endforeach
        </tbody>
    </table>

</div>

@include('contract-types.partials.modal')

@endsection

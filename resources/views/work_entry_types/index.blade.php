@extends('layouts.app')

@section('content')

<div class="bg-white shadow rounded p-4">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-lg font-semibold">Vrste prisustva/ odsustva</h1>

        <a href="{{ route('work-entry-types.create') }}"
        class="bg-blue-600 text-white px-3 py-1 rounded">
            + Nova vrsta
        </a>
    </div>

    <table class="w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left">Šifra</th>
                <th class="p-2 text-left">Naziv</th>
                <th class="p-2 text-left">Plaćeno</th>
                <th class="p-2 text-right">Akcije</th>
            </tr>
        </thead>

        <tbody>
            @foreach($types as $type)
                <tr class="border-b">
                    <td class="p-2">{{ $type->code }}</td>
                    <td class="p-2">{{ $type->name }}</td>

                    <td class="p-2">
                        {{ $type->is_paid ? 'Da' : 'Ne' }}
                    </td>

                    <td class="p-2 text-right flex gap-2 justify-end">
                        <a href="{{ route('work-entry-types.edit', $type->id) }}"
                            class="bg-yellow-400 px-2 py-1 rounded">
                                ✏️
                            </a>
                        <form action="{{ route('work-entry-types.destroy', $type->id) }}" method="POST"
                            onsubmit="return confirm('Da li si siguran?')">
                            @csrf
                            @method('DELETE')

                            <button class="bg-red-500 text-white px-2 py-1 rounded">
                                🗑
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@endsection
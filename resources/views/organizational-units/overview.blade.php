@extends('layouts.app')

@section('title', 'Organizacija - pregled')

@section('content')

<div class="space-y-6">

@foreach($groups as $group)

    <div class="bg-white rounded shadow p-4">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-3">

            <h2 class="text-lg font-semibold">
                [{{ $group->code }}] {{ $group->name }}
            </h2>

            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-sm">
                {{ $group->units->sum('employees_count') }} zaposlenih
            </span>

        </div>

        {{-- UNITS --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">

            @foreach($group->units as $unit)

                <div 
                    onclick="showEmployees({{ $unit->id }})"
                    class="border rounded p-3 hover:bg-gray-50 cursor-pointer transition">

                    <div class="font-medium">
                        {{ $unit->name }}
                    </div>

                    <div class="text-sm text-gray-500 mt-1">
                        {{ $unit->employees_count }} zaposlenih
                    </div>

                </div>

            @endforeach

        </div>

    </div>

@endforeach

</div>

@endsection
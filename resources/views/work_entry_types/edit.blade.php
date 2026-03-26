@extends('layouts.app')

@section('content')

<h1 class="text-lg font-semibold mb-4">Izmena vrste unosa</h1>

<form method="POST" action="{{ route('work-entry-types.update', $workEntryType->id) }}">
    @csrf
    @method('PUT')

    <input type="text" name="code" value="{{ $workEntryType->code }}" class="border p-2 mb-2 w-full">
    <input type="text" name="name" value="{{ $workEntryType->name }}" class="border p-2 mb-2 w-full">

    <label>
        <input type="checkbox" name="is_paid" {{ $workEntryType->is_paid ? 'checked' : '' }}>
        Plaćeno
    </label>

    <br>

    <label>
        <input type="checkbox" name="counts_as_work" {{ $workEntryType->counts_as_work ? 'checked' : '' }}>
        Računa se kao rad
    </label>

    <br>

    <label>
        <input type="checkbox" name="affects_vacation" {{ $workEntryType->affects_vacation ? 'checked' : '' }}>
        Utiče na godišnji
    </label>

    <br><br>

    <button class="bg-blue-600 text-white px-3 py-1 rounded">
        Sačuvaj
    </button>

</form>

@endsection
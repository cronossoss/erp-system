@extends('layouts.app')

@section('content')

<h1 class="text-lg font-semibold mb-4">Nova vrsta unosa</h1>

<input type="hidden" name="id" id="type_id">

<form id="typeForm" method="POST" action="{{ route('work-entry-types.store') }}">
    @csrf

    <input type="text" name="code" placeholder="Šifra" class="border p-2 mb-2 w-full">
    <input type="text" name="name" placeholder="Naziv" class="border p-2 mb-2 w-full">

    <label>
        <input type="checkbox" name="is_paid"> Plaćeno
    </label>

    <br>

    <label>
        <input type="checkbox" name="counts_as_work"> Računa se kao rad
    </label>

    <br>

    <label>
        <input type="checkbox" name="affects_vacation"> Utiče na godišnji
    </label>

    <br><br>

    <button class="bg-blue-600 text-white px-3 py-1 rounded">
        Sačuvaj
    </button>
</form>

@endsection

<script>
function editType(typeJson) {
    let type = JSON.parse(typeJson);

    document.getElementById('type_id').value = type.id;
    document.querySelector('[name="code"]').value = type.code;
    document.querySelector('[name="name"]').value = type.name;

    document.querySelector('[name="is_paid"]').checked = type.is_paid;
    
    // opcionalno ako imaš
    if (document.querySelector('[name="counts_as_work"]')) {
        document.querySelector('[name="counts_as_work"]').checked = type.counts_as_work;
    }

    if (document.querySelector('[name="affects_vacation"]')) {
        document.querySelector('[name="affects_vacation"]').checked = type.affects_vacation;
    }

    // promeni form action na update
    let form = document.getElementById('typeForm');
    form.action = '/work-entry-types/' + type.id;

    // dodaj PUT method
    if (!document.getElementById('method_field')) {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_method';
        input.value = 'PUT';
        input.id = 'method_field';
        form.appendChild(input);
    }
}
</script>
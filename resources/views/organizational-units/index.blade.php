@extends('layouts.app')

@section('title', 'Organizacione jedinice')

@section('content')

<div class="bg-white rounded shadow p-4">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Organizacione jedinice</h2>

        <button onclick="openUnitModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded">
            + Nova jedinica
        </button>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-200 text-xs uppercase">
            <tr>
                <th class="p-2 text-left">ID</th>
                <th class="p-2 text-left">Naziv</th>
                <th class="p-2 text-right">Akcije</th>
            </tr>
        </thead>

        <tbody>
            @foreach($units as $unit)
                @include('organizational-units.partials.node', ['unit' => $unit, 'level' => 0])
            @endforeach
        </tbody>
    </table>

</div>

@include('organizational-units.partials.modals')

@endsection

<script>
function el(id){ return document.getElementById(id); }

function csrf(){
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

// OPEN
function openUnitModal(){
    window.currentUnitId = null;
    el('unit_name').value = '';
    el('parent_id').value = '';
    el('unitModal').classList.remove('hidden');
}

// CLOSE
function closeUnitModal(){
    el('unitModal').classList.add('hidden');
}

// CREATE
function saveUnit(){

    let name = el('unit_name').value;
    let parent = el('parent_id').value;

    if(!name){
        alert('Naziv je obavezan');
        return;
    }

    fetch("{{ route('organizational-units.store') }}", {
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":csrf()
        },
        body: JSON.stringify({
            name: name,
            parent_id: parent
        })
    })
    .then(res => res.json())
    .then(() => location.reload());
}

// DELETE
function deleteUnit(id){

    if(!confirm('Obrisati jedinicu?')) return;

    fetch(`/organizational-units/${id}`, {
        method:"POST",
        headers:{
            "X-CSRF-TOKEN":csrf(),
            "X-HTTP-Method-Override":"DELETE"
        }
    })
    .then(() => location.reload());
}

// EDIT (POPULATE)
function editUnit(id, name, parent){

    el('unit_name').value = name;
    el('parent_id').value = parent;

    el('unitModal').classList.remove('hidden');

    // prebacimo save u update
    window.currentUnitId = id;
}

// OVERRIDE SAVE ZA EDIT
function saveUnit(){

    let name = el('unit_name').value;
    let parent = el('parent_id').value;

    if(!name){
        alert('Naziv je obavezan');
        return;
    }

    // ako edit
    if(window.currentUnitId){

        fetch(`/organizational-units/${window.currentUnitId}`, {
            method:"POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":csrf(),
                "X-HTTP-Method-Override":"PUT"
            },
            body: JSON.stringify({
                name: name,
                parent_id: parent
            })
        })
        .then(() => location.reload());

    } else {

        fetch("{{ route('organizational-units.store') }}", {
            method:"POST",
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":csrf()
            },
            body: JSON.stringify({
                name: name,
                parent_id: parent
            })
        })
        .then(() => location.reload());
    }
}
</script>
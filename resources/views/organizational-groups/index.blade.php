@extends('layouts.app')
@section('title', 'Organizacione celine')

@section('content')

<div class="bg-white rounded shadow p-4">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Organizacione celine</h2>

        <button onclick="openGroupModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded">
            + Nova celina
        </button>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-200 text-xs uppercase">
            <tr>
                <th class="p-2 text-left">Šifra</th>
                <th class="p-2 text-left">Naziv</th>
                <th class="p-2 text-right">Akcije</th>
            </tr>
        </thead>

        <tbody>
            @foreach($groups as $g)
            <tr>
                <td class="p-2">
                    <span class="bg-gray-100 px-2 py-1 rounded font-mono">
                        {{ $g->code }}
                    </span>
                </td>

                <td class="p-2">{{ $g->name }}</td>

                <td class="p-2 text-right space-x-2">

                    <button 
                        data-id="{{ $g->id }}"
                        data-name="{{ $g->name }}"
                        data-code="{{ $g->code }}"
                        onclick="openEditGroupModal(this)"
                        class="bg-yellow-400 px-2 py-1 rounded">
                        ✏️ Izmena
                    </button>

                    <button 
                        onclick="deleteGroup({{ $g->id }})"
                        class="bg-red-500 text-white px-2 py-1 rounded">
                        🗑
                    </button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<!-- GROUP MODAL -->
<div id="groupOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>

<div id="groupModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded w-80">

        <h3 class="text-lg font-bold mb-3">Organizaciona celina</h3>

        <input id="group_name"
            class="w-full border px-3 py-2 mb-3"
            placeholder="Naziv">

        <input id="group_code"
            class="w-full border px-3 py-2 mb-3"
            placeholder="Šifra (2 cifre)">

        <div class="flex justify-end gap-2">
            <button onclick="closeGroupModal()">Zatvori</button>
            <button onclick="saveGroup()" class="bg-blue-600 text-white px-4 py-2 rounded">
                Sačuvaj
            </button>
        </div>

    </div>
</div>

<script>
    let currentGroupId = null;

function el(id){ return document.getElementById(id); }
function csrf(){ return document.querySelector('meta[name="csrf-token"]').content; }

// OPEN
function openGroupModal(){
    currentGroupId = null;

    el('group_name').value = '';
    el('group_code').value = '';

    el('groupOverlay').classList.remove('hidden');
    el('groupModal').classList.remove('hidden');
}

// EDIT
function openEditGroupModal(btn){
    currentGroupId = btn.dataset.id;

    el('group_name').value = btn.dataset.name;
    el('group_code').value = btn.dataset.code;

    el('groupOverlay').classList.remove('hidden');
    el('groupModal').classList.remove('hidden');
}

// CLOSE
function closeGroupModal(){
    el('groupModal').classList.add('hidden');
    el('groupOverlay').classList.add('hidden');
}

// SAVE
function saveGroup(){

    let name = el('group_name').value;
    let code = el('group_code').value;

    if(!name){
        alert('Naziv je obavezan');
        return;
    }

    if(!/^\d{2}$/.test(code)){
        alert('Šifra mora imati 2 cifre');
        return;
    }

    let url = currentGroupId
        ? `/organizational-groups/${currentGroupId}`
        : `/organizational-groups`;

    let method = currentGroupId ? "PUT" : "POST";

    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf()
        },
        body: JSON.stringify({ name, code })
    })
    .then(res => {
        if(!res.ok){
            return res.json().then(err => alert(err.error || 'Greška'));
        }
        location.reload();
    });
}

// DELETE
function deleteGroup(id){

    if(!confirm('Da li si siguran?')) return;

    fetch(`/organizational-groups/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrf()
        }
    })
    .then(res => {
        if(!res.ok){
            return res.json().then(err => alert(err.error));
        }
        location.reload();
    });
}

</script>
@endsection
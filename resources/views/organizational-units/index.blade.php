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
                <th class="p-2 text-left">Šifra org.jed.</th>
                <th class="p-2 text-left">Naziv org.jed.</th>
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

{{-- MODALI --}}
@include('organizational-units.partials.modals')
@include('modals.employee-modal')

{{-- EMPLOYEES LIST MODAL --}}
<div id="employeesOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>

<div id="employeesModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-3/4 max-h-[80vh] overflow-auto p-6">

        <h2 class="text-lg font-bold mb-4">Zaposleni</h2>

        <table class="w-full text-sm">
            <thead class="bg-gray-200 text-xs uppercase">
                <tr>
                    <th class="p-2">MB</th>
                    <th class="p-2">Ime</th>
                    <th class="p-2">Pozicija</th>
                    <th class="p-2">Jedinica</th>
                </tr>
            </thead>

            <tbody id="employeesTable"></tbody>
        </table>

        <div class="text-right mt-4">
            <button onclick="closeEmployeesModal()" class="px-4 py-2 border rounded">
                Zatvori
            </button>
        </div>

    </div>
</div>

<script>

let units = JSON.parse('@json($units)');

function el(id){ return document.getElementById(id); }
function csrf(){ return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

// ================= EMPLOYEES =================

function showEmployees(unitId){

    let tbody = el('employeesTable');

    el('employeesModal').classList.remove('hidden');
    el('employeesOverlay').classList.remove('hidden');

    fetch(`/employees/by-unit/${unitId}`)
    .then(res => res.json())
    .then(data => {

        tbody.innerHTML = '';

        if(data.length === 0){
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-400 italic">
                        Nema zaposlenih u ovoj jedinici
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(emp => {
            tbody.innerHTML += `
                <tr data-id="${emp.id}" class="employee-row cursor-pointer hover:bg-gray-100">
                    
                    <td>${emp.employee_number ?? '-'}</td>
                    <td>${emp.first_name ?? ''} ${emp.last_name ?? ''}</td>
                    <td>${emp.position ?? '-'}</td>
                    <td>${emp.organizational_unit_name ?? '-'}</td>
                </tr>
            `;
        });
    });
}

function closeEmployeesModal(){
    el('employeesModal').classList.add('hidden');
    el('employeesOverlay').classList.add('hidden');
}

// ================= TREE =================

function toggleNode(e, row){

    if(e.target.closest('.unit-name')) return;
    if(e.target.closest('button')) return;

    let level = parseInt(row.dataset.level);
    let next = row.nextElementSibling;

    if(!next) return;

    let icon = row.querySelector('.toggle-icon');

    if(parseInt(next.dataset.level) <= level) return;

    let hide = !next.classList.contains('hidden');

    while(next && parseInt(next.dataset.level) > level){ 

        next.classList.toggle('hidden', hide);
        next = next.nextElementSibling;
    }

    if(icon){
        icon.style.transform = hide ? 'rotate(-90deg)' : 'rotate(0deg)';
    }
}

// ================= UNIT CRUD =================

function openUnitModal(){
    window.currentUnitId = null;

    el('unit_name').value = '';
    el('unit_code').value = '';
    el('parent_id').value = '';

    el('unitOverlay').classList.remove('hidden');
    el('unitModal').classList.remove('hidden');
}

function openEditUnitModal(btn){

    window.currentUnitId = btn.dataset.id;

    el('unit_name').value = btn.dataset.name;
    el('unit_code').value = btn.dataset.code;
    el('parent_id').value = btn.dataset.parent || '';

    el('unitOverlay').classList.remove('hidden');
    el('unitModal').classList.remove('hidden');
}

function closeUnitModal(){
    el('unitModal').classList.add('hidden');
    el('unitOverlay').classList.add('hidden');
}

function saveUnit(){

    let name = el('unit_name').value;
    let code = el('unit_code').value;
    let parent = el('parent_id').value;

    if(!name){
        alert('Naziv je obavezan');
        return;
    }

    if(!/^\d{3}$/.test(code)){
        alert('Šifra mora imati 3 cifre');
        return;
    }

    let url = window.currentUnitId
        ? `/organizational-units/${window.currentUnitId}`
        : `/organizational-units`;

    let method = window.currentUnitId ? "PUT" : "POST";

    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf()
        },
        body: JSON.stringify({
            name: name,
            code: code,
            parent_id: parent
        })
    })
    .then(() => location.reload());
}

function deleteUnit(id){

    if(!confirm('Da li si siguran?')) return;

    fetch(`/organizational-units/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrf()
        }
    })
    .then(() => location.reload());
}

</script>

@endsection
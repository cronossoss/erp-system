@extends('layouts.app')

@section('title', 'Radnici')

@section('content')

<div class="bg-white rounded shadow p-4">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Lista zaposlenih</h2>

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
            <tr id="row-{{ $employee->id }}" class="border-b">
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
                        onclick="openEditModal(this)">
                        ✏️
                    </button>

                    <button class="bg-red-500 text-white px-2 py-1 rounded"
                        data-id="{{ $employee->id }}"
                        onclick="deleteEmployee(this)">
                        🗑
                    </button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('employees.partials.modals')

<script>

function el(id){ return document.getElementById(id); }
function csrf(){ return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

// MODALS
function openModal(){
    el('employee_number').value = '';
    el('first_name').value = '';
    el('last_name').value = '';
    el('position').value = '';
    el('contract_type').value = '';
    el('unit_select').value = '';

    el('overlay').classList.remove('hidden');
    el('modal').classList.remove('hidden');
}

function closeModal(){
    el('modal').classList.add('hidden');
    el('overlay').classList.add('hidden');
}

function openEditModal(btn){
    el('overlay').classList.remove('hidden');
    el('editModal').classList.remove('hidden');

    el('edit_id').value = btn.dataset.id;
    el('edit_first_name').value = btn.dataset.first;
    el('edit_last_name').value = btn.dataset.last;
    el('edit_position').value = btn.dataset.position;
    el('edit_unit_select').value = btn.dataset.unit;
    el('edit_employee_number').value = btn.dataset.employee_number;
    el('edit_contract_type').value = btn.dataset.contract_type;
}

function closeEditModal(){
    el('editModal').classList.add('hidden');
    el('overlay').classList.add('hidden');
}

// VALIDACIJA
function validateEmployee(data){
    if(!/^\d{5}$/.test(data.employee_number)){
        showToast('Matični broj mora imati 5 cifara');
        return false;
    }
    if(!data.first_name) return showToast('Ime je obavezno'), false;
    if(!data.last_name) return showToast('Prezime je obavezno'), false;
    if(!data.contract_type) return showToast('Izaberi ugovor'), false;
    return true;
}

function openUnitModal(){
    el('overlay').classList.remove('hidden');
    el('unitModal').classList.remove('hidden');
}

function closeUnitModal(){
    el('unitModal').classList.add('hidden');
    el('overlay').classList.add('hidden');
}

function saveUnit(){

    let name = el('unit_name').value;
    let parent = el('parent_id').value;

    if(!name){
        showToast('Naziv je obavezan');
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
    .then(res => {
        if (!res.ok) throw new Error('Greška pri čuvanju');
        return res.json();
    })
    .then(unit => {

        // 🔥 DODAJ U SELECT
        let option = new Option(unit.name, unit.id);

        el('unit_select').add(option);
        el('edit_unit_select').add(option.cloneNode(true));

        // selektuj odmah
        el('unit_select').value = unit.id;

        closeUnitModal();
        showToast('Jedinica dodata');

        // reset inputa
        el('unit_name').value = '';
    })
    .catch(err => console.error(err));
}

// CRUD
function saveEmployee(){

    let data = {
        employee_number: el('employee_number').value,
        first_name: el('first_name').value,
        last_name: el('last_name').value,
        position: el('position').value,
        organizational_unit_id: el('unit_select').value,
        contract_type: el('contract_type').value
    };

    if(!validateEmployee(data)) return;

    fetch("{{ route('employees.store') }}", {
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":csrf()
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(() => {
        showToast('Dodat');
        location.reload();
    });
}

function updateEmployee(){

    let id = el('edit_id').value;

    let data = {
        employee_number: el('edit_employee_number').value,
        first_name: el('edit_first_name').value,
        last_name: el('edit_last_name').value,
        position: el('edit_position').value,
        organizational_unit_id: el('edit_unit_select').value,
        contract_type: el('edit_contract_type').value
    };

    if(!validateEmployee(data)) return;

    fetch(`/employees/${id}`, {
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":csrf(),
            "X-HTTP-Method-Override":"PUT"
        },
        body: JSON.stringify(data)
    })
    .then(res=>res.json())
    .then(()=> location.reload());
}

function deleteEmployee(btn){
    let id = btn.dataset.id;

    fetch(`/employees/${id}`,{
        method:"POST",
        headers:{
            "X-CSRF-TOKEN":csrf(),
            "X-HTTP-Method-Override":"DELETE"
        }
    })
    .then(()=> location.reload());
}

// TOAST
function showToast(msg){
    let t = el('toast');
    el('toast-msg').innerText = msg;
    t.classList.remove('hidden');
    setTimeout(()=>t.classList.add('hidden'),2000);
}

</script>

@endsection
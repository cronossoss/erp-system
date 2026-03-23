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
            <tr 
                data-id="{{ $employee->id }}"
                class="employee-row border-b hover:bg-gray-50 cursor-pointer">
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

<script>

document.addEventListener('click', function(e){

    let row = e.target.closest('.employee-row');

    if(row){
        let id = row.dataset.id;
        showEmployeeDetail(id);
    }
});
const unitsEl = document.getElementById('units-data');
window.units = unitsEl ? JSON.parse(unitsEl.dataset.units) : [];

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

function filterByUnit(unitId){

    fetch(`/employees/by-unit/${unitId}`)
    .then(res => res.json())
    .then(data => {

        let tbody = document.querySelector("tbody");
        tbody.innerHTML = '';

        data.forEach(emp => {
            tbody.insertAdjacentHTML('beforeend', renderRow(emp));
        });

    });
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
    .then(async res => {

    let text = await res.text();

    let response;

    try {
        response = JSON.parse(text);
    } catch(e){
        showToast("Server greška", true);
        return;
    }

    if(!res.ok){
        showToast(response.error || "Greška", true);
        return;
    }

    let emp = response;

    let tbody = document.querySelector("tbody");
    tbody.insertAdjacentHTML('beforeend', renderRow(emp));

    showToast('Radnik dodat');
    closeModal();
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
        contract_type: el('edit_contract_type').value,
        contract_end_date: el('edit_contract_end_date').value // 👈 OVO FALI
    };

    if(!validateEmployee(data)) return;

    fetch(`/employees/${id}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf(),
            "X-HTTP-Method-Override": "PUT"
        },
        body: JSON.stringify(data)
    })
    .then(() => location.reload());
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
function showToast(msg, isError = false){

    let toast = document.getElementById('toast');
    let span = document.getElementById('toast-msg');

    span.innerText = msg;

    toast.classList.remove('hidden');

    if(isError){
        toast.classList.remove('bg-green-600');
        toast.classList.add('bg-red-600');
    } else {
        toast.classList.remove('bg-red-600');
        toast.classList.add('bg-green-600');
    }

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}

function renderRow(emp){
    return `
        <tr id="row-${emp.id}" class="border-b cursor-pointer hover:bg-gray-50"
            onclick="showEmployeeDetail(${emp.id})">
            <td class="p-3">${emp.employee_number ?? ''}</td>
            <td class="p-3">${emp.first_name} ${emp.last_name}</td>
            <td class="p-3">${emp.position ?? ''}</td>
            <td class="p-3">${emp.organizational_unit_name ?? '-'}</td>
            <td class="p-3">${emp.contract_type ?? ''}</td>

            <td class="p-3 text-right space-x-2">

                <button class="bg-yellow-400 px-2 py-1 rounded"
                    data-id="${emp.id}"
                    data-first="${emp.first_name}"
                    data-last="${emp.last_name}"
                    data-position="${emp.position ?? ''}"
                    data-unit="${emp.organizational_unit_id ?? ''}"
                    data-employee_number="${emp.employee_number ?? ''}"
                    data-contract_type="${emp.contract_type ?? ''}"
                    onclick="event.stopPropagation(); openEditModal(this)">
                    ✏️
                </button>

                <button class="bg-red-500 text-white px-2 py-1 rounded"
                    data-id="${emp.id}"
                    onclick="deleteEmployee(this)">
                    🗑
                </button>

            </td>
        </tr>
    `;
}


function showEmployeeDetail(id){

    fetch(`/employees/${id}/json`)
    .then(res => res.json())
    .then(emp => {

        let html = `

            <!-- HEADER -->
            <div class="flex items-center gap-4 mb-6">

                <!-- AVATAR -->
                <img src="https://ui-avatars.com/api/?name=${emp.first_name}+${emp.last_name}&background=0D8ABC&color=fff"
                    class="rounded-full w-20 h-20 object-cover">

                <!-- IME -->
                <div>
                    <div class="text-xl font-bold">
                        ${emp.first_name} ${emp.last_name}
                    </div>
                    <div class="text-gray-500 text-sm">
                        Matični broj: ${emp.employee_number}
                    </div>
                </div>

                <!-- JMBG DESNO -->
                <div class="ml-auto text-right">
                    <div class="text-xs text-gray-500">JMBG</div>
                    <input id="d_jmbg" value="${emp.jmbg ?? ''}" 
                        class="border px-2 py-1 rounded text-sm">
                </div>

                <!-- EDIT BTN -->
                <button onclick="enableEditMode()" 
                    class="bg-yellow-500 text-white px-4 py-2 rounded ml-4">
                    ✏️ Izmeni
                </button>
            </div>

            <!-- HIDDEN (da ne puca save) -->
            <input type="hidden" id="d_first_name" value="${emp.first_name ?? ''}">
            <input type="hidden" id="d_last_name" value="${emp.last_name ?? ''}">
            <input type="hidden" id="d_employee_number" value="${emp.employee_number ?? ''}">

            <!-- GRID -->
            <div class="grid grid-cols-3 gap-4 text-sm">

                <!-- KOLONA 1 -->
                <div class="bg-gray-50 p-3 rounded">
                    <div class="text-xs text-gray-500">Email</div>
                    <input id="d_email" value="${emp.email ?? ''}" disabled class="w-full border px-2 py-1 rounded mb-2">

                    <div class="text-xs text-gray-500">Telefon (posao)</div>
                    <input id="d_phone_work" value="${emp.phone_work ?? ''}" disabled class="w-full border px-2 py-1 rounded mb-2">

                    <div class="text-xs text-gray-500">Telefon (privatni)</div>
                    <input id="d_phone_private" value="${emp.phone_private ?? ''}" disabled class="w-full border px-2 py-1 rounded">
                </div>

                <!-- KOLONA 2 -->
                <div class="bg-gray-50 p-3 rounded">
                    <div class="text-xs text-gray-500">Pozicija</div>
                    <input id="d_position" value="${emp.position ?? ''}" disabled class="w-full border px-2 py-1 rounded mb-2">

                    <div class="text-xs text-gray-500">Jedinica</div>
                    <select id="d_unit" disabled class="w-full border px-2 py-1 rounded mb-2">
                        ${window.units.map(u => `
                            <option value="${u.id}" ${u.id == emp.organizational_unit_id ? 'selected' : ''}>
                                ${u.name}
                            </option>
                        `).join('')}
                    </select>

                    <div class="text-xs text-gray-500">Tip ugovora</div>
                    <input id="d_contract_type" value="${emp.contract_type ?? ''}" disabled class="w-full border px-2 py-1 rounded">
                </div>

                <!-- KOLONA 3 (DATUMI) -->
                <div class="bg-gray-50 p-3 rounded">

                    <div class="text-xs text-gray-500">Datum rođenja</div>
                    <input type="date" id="d_birth_date" value="${emp.birth_date ?? ''}" disabled class="w-full border px-2 py-1 rounded mb-2">

                    <div class="text-xs text-gray-500">Datum zaposlenja</div>
                    <input type="date" id="d_employment_date" value="${emp.employment_date ?? ''}" disabled class="w-full border px-2 py-1 rounded mb-2">

                    <div class="text-xs text-gray-500">Istek ugovora</div>
                    <input type="date" id="d_contract_end_date" value="${emp.contract_end_date ?? ''}" disabled class="w-full border px-2 py-1 rounded">

                </div>

            </div>

            <!-- SAVE -->
            <div class="flex justify-end gap-2 mt-6">
                <button onclick="saveFromDetail(${emp.id})"
                    id="detailSaveBtn"
                    class="bg-blue-600 text-white px-4 py-2 rounded hidden">
                    Sačuvaj
                </button>
            </div>
            `;

        document.getElementById('employeeDetailContent').innerHTML = html;

        document.getElementById('overlay').classList.remove('hidden');
        document.getElementById('employeeDetailModal').classList.remove('hidden');
    });
}

function searchEmployees(){

    let query = document.getElementById('searchInput').value;

    fetch(`/employees/search?search=${query}`)
    .then(res => res.json())
    .then(data => {

        let tbody = document.querySelector("tbody");
        tbody.innerHTML = '';

        data.forEach(emp => {
            tbody.insertAdjacentHTML('beforeend', renderRow(emp));
        });

    });
}

function closeDetailModal(){
    document.getElementById('employeeDetailModal').classList.add('hidden');
    document.getElementById('overlay').classList.add('hidden');
}


function formatDate(date){
    if(!date) return '';
    let d = new Date(date);
    return d.toLocaleDateString('sr-RS');
}

function openEditFromDetail(id){

    fetch(`/employees/${id}/json`)
    .then(res => res.json())
    .then(emp => {

        closeDetailModal();

        el('overlay').classList.remove('hidden');
        el('editModal').classList.remove('hidden');

        el('edit_id').value = emp.id;
        el('edit_first_name').value = emp.first_name;
        el('edit_last_name').value = emp.last_name;
        el('edit_position').value = emp.position;
        el('edit_employee_number').value = emp.employee_number;
        el('edit_contract_type').value = emp.contract_type;
        el('edit_unit_select').value = emp.organizational_unit_id;
        el('edit_contract_end_date').value = emp.contract_end_date;
    });
}

function enableEditMode(){
    document.querySelectorAll('#employeeDetailModal input, #employeeDetailModal select')
        .forEach(el => el.disabled = false);

    document.getElementById('detailSaveBtn').classList.remove('hidden');
}

function saveFromDetail(id){

    let data = {
        employee_number: el('d_employee_number')?.value || '',
        first_name: el('d_first_name').value,
        last_name: el('d_last_name').value,
        position: el('d_position').value,
        organizational_unit_id: el('d_unit').value,
        contract_type: el('d_contract_type').value,
        employment_date: el('d_employment_date').value,
        contract_end_date: el('d_contract_end_date').value,
        email: el('d_email').value,
        phone_work: el('d_phone_work').value || null,
        phone_private: el('d_phone_private').value || null,
        jmbg: el('d_jmbg').value || null,
        birth_date: el('d_birth_date').value
    };

    fetch(`/employees/${id}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf(),
            "X-HTTP-Method-Override": "PUT"
        },
        body: JSON.stringify(data)
    })
    .then(() => {
        showToast("Sačuvano");
        location.reload();
    });
}

function inputSection(title, rows){
    return `
        <div class="bg-gray-50 p-2 rounded shadow-sm">
            <h4 class="font-semibold mb-1 text-xs">${title}</h4>

            ${rows.map(r => {

                if(!r[1]){
                    return `
                        <div class="flex justify-between py-1">
                            <span class="text-gray-500">${r[0]}</span>
                            <span class="font-medium">${r[2] ?? '-'}</span>
                        </div>
                    `;
                }

                if(r[3] === "unit_select"){
                    return `
                        <div class="mb-1">
                            <label class="text-gray-500 text-xs">${r[0]}</label>
                            <select id="${r[1]}" disabled class="w-full border px-2 py-[3px] text-xs rounded">
                                ${window.units.map(u => `
                                    <option value="${u.id}" ${u.id == r[2] ? 'selected' : ''}>
                                        ${u.name}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                    `;
                }
                return `
                    <div class="mb-1">
                        <label class="text-gray-500 text-xs">${r[0]}</label>
                        <input type="${r[3] ?? 'text'}"
                            id="${r[1]}"
                            value="${r[2] ?? ''}"
                            disabled
                            class="w-full border px-2 py-1 rounded">
                    </div>
                `;
            }).join('')}

        </div>
    `;
}


</script>

@endsection
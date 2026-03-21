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

<div id="employeesOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>

@include('organizational-units.partials.modals')

@endsection

<!-- EMPLOYEES MODAL -->
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

            <tbody id="employeesTable">
                <tr>
                    <td colspan="4" class="p-2 text-center">Učitavanje...</td>
                </tr>
        </tbody>
        </table>

        <div class="text-right mt-4">
            <button onclick="closeEmployeesModal()" class="px-4 py-2 border rounded">
                Zatvori
            </button>
        </div>

    </div>
</div>

<!-- EMPLOYEE DETAIL MODAL -->
<div id="employeeDetailModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-[700px] p-6">

        <h2 class="text-lg font-bold mb-4">Detalji zaposlenog</h2>

        <div id="employeeDetailContent" class="grid grid-cols-2 gap-4 text-sm"></div>

        <div class="text-right mt-4 space-x-2">
            <button onclick="enableEdit()" class="px-4 py-2 bg-yellow-500 text-white rounded">
                Izmeni
            </button>

            <button onclick="closeEmployeeDetail()" class="px-4 py-2 border rounded">
                Zatvori
            </button>
        </div>

    </div>
</div>

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
    .then(() => {
        showToast('Jedinica obrisana');
        location.reload();
    });
}

// EDIT (POPULATE)
function editUnit(id, name, parent){

    let row = document.querySelector(`tr[data-id='${id}']`);
    let currentName = name;

    if(row){
        let nameCell = row.querySelector('.unit-name');
        if(nameCell){
            currentName = nameCell.innerText.trim();
        }
    }

    el('unit_name').value = currentName;
    el('parent_id').value = parent;

    el('unitModal').classList.remove('hidden');

    window.currentUnitId = id;
}

// OVERRIDE SAVE ZA EDIT
function saveUnit(){

    let name = el('unit_name').value;
    let parent = el('parent_id').value;

   let row = document.querySelector(`tr[data-id='${window.currentUnitId}']`);
    if(row){
        oldParent = row.dataset.parent;
    }

    if(!name){
        showToast('Naziv je obavezan', 'error');
        return;
    }

    // EDIT
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
        .then(() => {

    let row = document.querySelector(`tr[data-id='${window.currentUnitId}']`);

    if(row){

        let oldParent = row.dataset.parent ?? '';
        let newParent = parent ?? '';

        // DEBUG (slobodno ostavi privremeno)
        console.log("OLD:", oldParent, "NEW:", newParent);

        // ako parent NIJE isti → refresh
        if(oldParent != newParent){
            location.reload();
            return;
        }

        // update name
        let nameCell = row.querySelector('.unit-name');
        if(nameCell){
            let nameSpan = row.querySelector('.unit-name');
                if(nameSpan){
                    nameSpan.textContent = name;
                }
        }
    }

    showToast('Jedinica izmenjena'); // 👈 OVDE

    closeUnitModal();
});

    } else {

        // CREATE
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
        .then(data => {

            let parent = el('parent_id').value ?? '';

            // ako ima parent → refresh
                location.reload();
                return;
          

            // dodaj u root tabelu
            let tbody = document.querySelector('table tbody');

            let newRow = document.createElement('tr');
            newRow.setAttribute('data-id', data.id);
            newRow.setAttribute('data-parent', '');
            newRow.className = "cursor-pointer hover:bg-gray-100";

            newRow.innerHTML = `
                <td class="p-2">${data.id}</td>
                <td class="p-2">
                    <span class="unit-name">${data.name}</span>
                </td>
                <td class="p-2 text-right space-x-2">
                    <button 
                        onclick="event.stopPropagation(); editUnit(${data.id}, '${data.name}', '')"
                        class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                        Edit
                    </button>

                    <button 
                        onclick="event.stopPropagation(); deleteUnit(${data.id})"
                        class="bg-red-500 text-white px-2 py-1 rounded">
                        🗑
                    </button>
                </td>
            `;

            tbody.appendChild(newRow);

            showToast('Jedinica dodata');

            closeUnitModal();
        });
    }
}

function showEmployees(unitId){

    let tbody = document.getElementById('employeesTable');

    // LOADING
    tbody.innerHTML = `
        <tr>
            <td colspan="4" class="p-2 text-center">Učitavanje...</td>
        </tr>
    `;

    document.getElementById('employeesModal').classList.remove('hidden');
    document.getElementById('employeesOverlay').classList.remove('hidden');

    fetch(`/employees/by-unit/${unitId}`)
    .then(res => res.json())
    .then(data => {

        tbody.innerHTML = '';

        if(data.length === 0){
            tbody.innerHTML = '<tr><td colspan="4" class="p-2 text-center">Nema zaposlenih</td></tr>';
        }

        data.forEach(emp => {
            tbody.innerHTML += `
                <tr onclick="showEmployeeDetail(${emp.id})"
                    class="border-b hover:bg-gray-50 cursor-pointer">

                    <td class="p-2">${emp.employee_number ?? ''}</td>
                    <td class="p-2">${emp.first_name} ${emp.last_name}</td>
                    <td class="p-2">${emp.position ?? ''}</td>
                    <td class="p-2">${emp.organizational_unit_name ?? ''}</td>

                </tr>
            `;
        });

    })
    .catch(() => {
        tbody.innerHTML = '<tr><td colspan="4" class="p-2 text-center text-red-500">Greška pri učitavanju</td></tr>';
    });
}

function showEmployeeDetail(id){

    window.currentEmployeeId = id;

    fetch(`/employees/${id}/json`)
    .then(res => res.json())
    .then(emp => {
        window.currentEmployeeData = emp;

        document.getElementById('employeeDetailContent').innerHTML = `

            <div><b>Matični broj:</b> ${emp.employee_number ?? '-'}</div>
            <div><b>Ime:</b> ${emp.first_name ?? '-'}</div>

            <div><b>Prezime:</b> ${emp.last_name ?? '-'}</div>
            <div><b>Radno mesto:</b> ${emp.position ?? '-'}</div>

            <div><b>Organizaciona jedinica:</b> ${emp.organizational_unit ?? '-'}</div>
            <div><b>Ugovor:</b> ${emp.contract_type ?? '-'}</div>

            <div><b>Datum rođenja:</b> ${formatDate(emp.birth_date)}</div>
            <div><b>JMBG:</b> ${emp.jmbg ?? '-'}</div>

            <div><b>Zasnivanje radnog odnosa:</b> ${formatDate(emp.employment_date) ?? '-'}</div>
            <div><b>Istek ugovora:</b> ${formatDate(emp.contract_end_date) ?? '-'}</div>

            <div><b>Email:</b> ${emp.email ?? '-'}</div>
            <div><b>Službeni telefon:</b> ${emp.phone_work ?? '-'}</div>

            <div><b>Privatni telefon:</b> ${emp.phone_private ?? '-'}</div>

            <div class="col-span-2">
                <b>Slika:</b><br>
                ${emp.photo 
                    ? `<img src="/storage/${emp.photo}" class="w-24 h-24 object-cover rounded mt-2">`
                    : 'Nema slike'}
            </div>
        `;

        document.getElementById('employeeDetailModal').classList.remove('hidden');
    });
}

function closeEmployeeDetail(){
    document.getElementById('employeeDetailModal').classList.add('hidden');
}


function closeEmployeesModal(){
    document.getElementById('employeesModal').classList.add('hidden');
    document.getElementById('employeesOverlay').classList.add('hidden');
}

let overlay = document.getElementById('employeesOverlay');
if(overlay){
    overlay.addEventListener('click', closeEmployeesModal);
}

document.addEventListener('keydown', function(e){
    if(e.key === "Escape"){
        closeEmployeesModal();
    }
});


document.addEventListener('click', function(e){

    // EDIT
    let editBtn = e.target.closest('.btn-edit');
    if(editBtn){
        e.stopPropagation();

        let id = editBtn.dataset.id;
        let name = editBtn.dataset.name;
        let parent = editBtn.dataset.parent;

        editUnit(id, name, parent);
        return;
    }

    // DELETE
    let deleteBtn = e.target.closest('.btn-delete');
    if(deleteBtn){
        e.stopPropagation();

        let id = deleteBtn.dataset.id;
        deleteUnit(id);
        return;
    }

});

function showToast(message, type = 'success'){

    let toast = document.getElementById('toast');

    toast.innerText = message;
    toast.classList.remove('hidden');

    // boje
    if(type === 'success'){
        toast.className = "fixed top-5 right-5 px-4 py-2 rounded text-white shadow-lg bg-green-500 z-50";
    } else {
        toast.className = "fixed top-5 right-5 px-4 py-2 rounded text-white shadow-lg bg-red-500 z-50";
    }

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 2500);
}

function toggleNode(e, row, id){

    // ako je klik na dugme → ignoriši (edit/delete)
    if(e.target.closest('button')) return;

    // ako je klik na naziv (employees) → ignoriši
    if(e.target.closest('.unit-name')) return;

    let level = parseInt(row.dataset.level);
    let next = row.nextElementSibling;

    if(!next) return;

    let icon = row.querySelector('.toggle-icon');

    // da li ima children
    if(parseInt(next.dataset.level) <= level) return;

    // proveri da li su trenutno sakriveni ili ne
    let hide = !next.classList.contains('hidden');

    while(next && parseInt(next.dataset.level) > level){

        if(hide){
            next.classList.add('hidden');
        } else {
            next.classList.remove('hidden');
        }

        next = next.nextElementSibling;
    }

    // rotacija strelice
    if(icon){
        if(hide){
            icon.style.transform = 'rotate(-90deg)'; // ▶
        } else {
            icon.style.transform = 'rotate(0deg)'; // ▼
        }
    }
}

function enableEdit(){

    fetch(`/employees/${window.currentEmployeeId}/json`)
    .then(res => res.json())
    .then(emp => {

        document.getElementById('employeeDetailContent').innerHTML = `

            <div><b>Matični broj:</b><br>
                <input id="edit_employee_number" value="${emp.employee_number ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>Ime:</b><br>
                <input id="edit_first_name" value="${emp.first_name ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>Prezime:</b><br>
                <input id="edit_last_name" value="${emp.last_name ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>Radno mesto:</b><br>
                <input id="edit_position" value="${emp.position ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>Email:</b><br>
                <input id="edit_email" value="${emp.email ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>Telefon službeni:</b><br>
                <input id="edit_phone_work" value="${emp.phone_work ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>Telefon privatni:</b><br>
                <input id="edit_phone_private" value="${emp.phone_private ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>Datum rođenja:</b><br>
                <input type="date" id="edit_birth_date" value="${emp.birth_date ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>JMBG:</b><br>
                <input id="edit_jmbg" value="${emp.jmbg ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>Zasnivanje radnog odnosa:</b><br>
                <input type="date" id="edit_employment_date" value="${emp.employment_date ?? ''}" class="border p-1 w-full">
            </div>

            <div><b>Istek ugovora:</b><br>
                <input type="date" id="edit_contract_end_date" value="${emp.contract_end_date ?? ''}" class="border p-1 w-full">
            </div>

        `;

        // dugmad
        let footer = document.querySelector('#employeeDetailModal .text-right');
        footer.innerHTML = `
            <button id="saveEmployeeBtn" class="px-4 py-2 bg-blue-600 text-white rounded">
                Sačuvaj
            </button>
            <button onclick="closeEmployeeDetail()" class="px-4 py-2 border rounded">
                Otkaži
            </button>
        `;
    });
}

function saveEmployeeDetail(){
    console.log("SAVE CLICKED");

    let id = window.currentEmployeeId;

    
    let data = {
        employee_number: document.getElementById('edit_employee_number').value,
        first_name: document.getElementById('edit_first_name').value,
        last_name: document.getElementById('edit_last_name').value,
        position: document.getElementById('edit_position').value,
        email: document.getElementById('edit_email').value,
        phone_work: document.getElementById('edit_phone_work').value,
        phone_private: document.getElementById('edit_phone_private').value,
        birth_date: document.getElementById('edit_birth_date').value || null,
        jmbg: document.getElementById('edit_jmbg').value,
        employment_date: document.getElementById('edit_employment_date').value || null,
        contract_end_date: document.getElementById('edit_contract_end_date').value || null,

        // 🔥 OVO JE FIX
        organizational_unit_id: window.currentEmployeeData.organizational_unit_id,
        contract_type: window.currentEmployeeData.contract_type
    };

    fetch(`/employees/${id}`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf()
        },
        body: JSON.stringify(data)
    })
    .then(res => {
        if(!res.ok){
            throw new Error("Greška pri snimanju");
        }
        return res.json().catch(() => ({})); // fallback ako nema JSON
    })
    .then(() => {

    showToast('Izmenjeno');

    // 🔥 UPDATE U TABELI
    let rows = document.querySelectorAll('#employeesTable tr');

    rows.forEach(row => {
        if(row.getAttribute('onclick')?.includes(`showEmployeeDetail(${id})`)){

            row.children[0].innerText = data.employee_number ?? '';
            row.children[1].innerText = data.first_name + ' ' + data.last_name;
            row.children[2].innerText = data.position ?? '';

        }
    });

    closeEmployeeDetail();

// reset footer na default
let footer = document.querySelector('#employeeDetailModal .text-right');

footer.innerHTML = `
    <button onclick="enableEdit()" class="px-4 py-2 bg-yellow-500 text-white rounded">
        Izmeni
    </button>

    <button onclick="closeEmployeeDetail()" class="px-4 py-2 border rounded">
        Zatvori
    </button>
`;
});
}

document.addEventListener('click', function(e){

    if(e.target && e.target.id === 'saveEmployeeBtn'){
        saveEmployeeDetail();
    }

});

function formatDate(date){

    if(!date) return '-';

    let d = new Date(date);

    let day = String(d.getDate()).padStart(2, '0');
    let month = String(d.getMonth() + 1).padStart(2, '0');
    let year = d.getFullYear();

    return `${day}/${month}/${year}`;
}

</script>
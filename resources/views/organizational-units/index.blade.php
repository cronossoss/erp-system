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

function buildUnitOptions(units, selectedId, level = 0) {

    let result = '';

    units.forEach(u => {

        let prefix = '—'.repeat(level);
        let selected = u.id == selectedId ? 'selected' : '';

        result += `<option value="${u.id}" ${selected}>${prefix} [${u.code}] ${u.name}</option>`;

        // 🔥 AKO IMA CHILDREN
        if(u.children && u.children.length){
            result += buildUnitOptions(u.children, selectedId, level + 1);
        }
    });

    return result;
}

function el(id){ return document.getElementById(id); }
function csrf(){ return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

function showEmployees(unitId){
    let tbody = el('employeesTable');

    el('employeesModal').classList.remove('hidden');
    el('employeesOverlay').classList.remove('hidden');

    fetch(`/employees/by-unit/${unitId}`)
    .then(res => res.json())
    .then(data => {

        tbody.innerHTML = '';

        // AKO NEMA ZAPOSLENIH
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
                <tr onclick="showEmployeeDetail(${emp.id})" class="cursor-pointer hover:bg-gray-100">
                    <td>${emp.employee_number ?? '-'}</td>
                    <td>${emp.first_name ?? ''} ${emp.last_name ?? ''}</td>
                    <td>${emp.position ?? '-'}</td>
                    <td>${emp.organizational_unit_name ?? '-'}</td>
                </tr>
            `;
        });
    });
}

function showEmployeeDetail(id){
    closeEmployeesModal();

    window.currentEmployeeId = id;
    document.getElementById('employeeDetailContent').innerHTML = '';

    fetch(`/employees/${id}/json`)
    .then(res => res.json())
    .then(emp => {

        window.currentEmployeeData = emp;

        document.getElementById('employeeDetailContent').innerHTML = `

            <div><b>Matični broj:</b> ${emp.employee_number ?? '-'}</div>
            <div><b>Ime:</b> ${emp.first_name ?? '-'}</div>

            <div><b>Prezime:</b> ${emp.last_name ?? '-'}</div>
            <div><b>Radno mesto:</b> ${emp.position ?? '-'}</div>

            <div><b>Organizaciona jedinica:</b> ${emp.organizational_unit_name ?? '-'}</div>
            <div><b>Ugovor:</b> ${emp.contract_type ?? '-'}</div>

            <div><b>Datum rođenja:</b> ${formatDate(emp.birth_date)}</div>
            <div><b>JMBG:</b> ${emp.jmbg ?? '-'}</div>

            <div><b>Zasnivanje radnog odnosa:</b> ${formatDate(emp.employment_date)}</div>
            <div><b>Istek ugovora:</b> ${formatDate(emp.contract_end_date)}</div>

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

        document.getElementById('employeeDetailFooter').innerHTML = `
            <button onclick="enableEdit()" class="px-4 py-2 bg-yellow-500 text-white rounded">
                Izmeni
            </button>
            <button onclick="closeEmployeeDetail()" class="px-4 py-2 border rounded">
                Zatvori
            </button>
        `;

        document.getElementById('employeeDetailModal').classList.remove('hidden');
    });
}

function highlight(text, search){

    if(!search) return text;

    let lower = text.toLowerCase();
    let s = search.toLowerCase();

    if(lower.startsWith(s)){
        return `<span class="bg-yellow-200">${text.substring(0, search.length)}</span>${text.substring(search.length)}`;
    }

    return text;
}

function enableEdit(){

    let emp = window.currentEmployeeData;

    // UNIT OPTIONS
    let unitOptions = '<option value="">-- Izaberi --</option>';
    unitOptions += buildUnitOptions(units, emp.organizational_unit_id);

    

    // CONTRACT OPTIONS
    let contractOptions = `
        <option value="">-- Izaberi --</option>
        <option value="neodređeno" ${emp.contract_type == 'neodređeno' ? 'selected' : ''}>Neodređeno</option>
        <option value="određeno" ${emp.contract_type == 'određeno' ? 'selected' : ''}>Određeno</option>
        <option value="drugo" ${emp.contract_type == 'drugo' ? 'selected' : ''}>Drugo</option>
    `;

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

        <div><b>Pozicija:</b><br>
            <input id="edit_position" value="${emp.position ?? ''}" class="border p-1 w-full">
        </div>

        <div><b>Organizaciona jedinica:</b><br>
            <select id="edit_unit_id" class="border p-1 w-full">
                ${unitOptions}
            </select>
        </div>

        <div><b>Tip ugovora:</b><br>
            <select id="edit_contract_type" class="border p-1 w-full">
                ${contractOptions}
            </select>
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

    document.getElementById('employeeDetailFooter').innerHTML = `
        <button onclick="saveEmployeeDetail()" class="px-4 py-2 bg-blue-600 text-white rounded">
            Sačuvaj
        </button>

        <button onclick="closeEmployeeDetail()" class="px-4 py-2 border rounded">
            Otkaži
        </button>
    `;
}

function saveEmployeeDetail(){
    let id = window.currentEmployeeId;

    let data = {
        employee_number: el('edit_employee_number').value || null,
        first_name: el('edit_first_name').value || null,
        last_name: el('edit_last_name').value || null,
        position: el('edit_position').value || null,
        email: el('edit_email').value || null,
        phone_work: el('edit_phone_work').value || null,
        phone_private: el('edit_phone_private').value || null,

        birth_date: el('edit_birth_date').value || null,
        jmbg: el('edit_jmbg').value || null,
        employment_date: el('edit_employment_date').value || null,
        contract_end_date: el('edit_contract_end_date').value || null,

        organizational_unit_id: document.getElementById('edit_unit_id').value || null,
        contract_type: document.getElementById('edit_contract_type').value || null
    };

    fetch(`/employees/${id}`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf(),
            "Accept": "application/json"
        },
        body: JSON.stringify(data)
    })
    .then(() => {
        showEmployeeDetail(id);
    });
}

function closeEmployeeDetail(){
    el('employeeDetailModal').classList.add('hidden');
}

function closeEmployeesModal(){
    el('employeesModal').classList.add('hidden');
    el('employeesOverlay').classList.add('hidden');
}

function formatDate(date){

    if(!date) return '-';

    let d = new Date(date);

    let day = String(d.getDate()).padStart(2, '0');
    let month = String(d.getMonth() + 1).padStart(2, '0');
    let year = d.getFullYear();

    return `${day}/${month}/${year}`;
}

function toggleNode(e, row, id){

    // ignorisi klik na naziv (to već radi modal)
    if(e.target.closest('.unit-name')) return;

    // ignorisi klik na dugmad
    if(e.target.closest('button')) return;

    let level = parseInt(row.dataset.level);
    let next = row.nextElementSibling;

    if(!next) return;

    let icon = row.querySelector('.toggle-icon');

    // proveri da li ima child redova
    if(parseInt(next.dataset.level) <= level) return;

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
        icon.style.transform = hide ? 'rotate(-90deg)' : 'rotate(0deg)';
    }
}

function saveUnit(){

    console.log("EDIT ID:", window.currentUnitId);

    let name = el('unit_name').value;
    let code = el('unit_code').value;
    let parent = el('parent_id').value;

    // VALIDACIJA
    if(!name){
        alert('Naziv je obavezan');
        return;
    }

    if(!/^\d{3}$/.test(code)){
        alert('Šifra mora imati 3 cifre');
        return;
    }

    // EDIT
    if(window.currentUnitId){

        fetch(`/organizational-units/${window.currentUnitId}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf(),
                "X-HTTP-Method-Override": "PUT"
            },
            body: JSON.stringify({
                name: name,
                code: code,
                parent_id: parent
            })
        })
        .then(async res => {

            if(!res.ok){
                let error = await res.json();

                let msg = Object.values(error.errors).join('\n');
                alert(msg);
                return;
            }

            location.reload();
        });

    } else {

        // CREATE
            fetch(`/organizational-units`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf(),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    name: name,
                    code: code,
                    parent_id: parent
                })
            })
            .then(async res => {

                if(!res.ok){
                    let error = await res.json();

                    let msg = Object.values(error.errors).join('\n');
                    alert(msg);
                    return;
                }

                location.reload();
            });
    }
}

function openUnitModal(){
    window.currentUnitId = null;

    el('unit_name').value = '';
    el('unit_code').value = '';
    el('parent_id').value = '';

    el('unitOverlay').classList.remove('hidden'); // 👈 DODAJ
    el('unitModal').classList.remove('hidden');
}

function openEditUnitModal(btn){

    // uzmi ID
    window.currentUnitId = btn.dataset.id;

    // popuni formu
    document.getElementById('unit_name').value = btn.dataset.name;
    document.getElementById('unit_code').value = btn.dataset.code;
    document.getElementById('parent_id').value = btn.dataset.parent || '';

    // otvori modal
    document.getElementById('unitModal').classList.remove('hidden');
}

function closeUnitModal(){
    el('unitModal').classList.add('hidden');
    el('unitOverlay').classList.add('hidden'); // 👈 DODAJ
}

</script>
</script>


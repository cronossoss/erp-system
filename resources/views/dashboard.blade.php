@extends('layouts.app')

@section('content')
<div class="p-4">

    <h2 class="text-xl font-semibold mb-4">Dashboard</h2>

    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-4">Zaposleni po organizacionim jedinicama</h3>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">

            @foreach($units as $unit)
                <div class="border p-2 rounded shadow-sm text-sm cursor-pointer hover:bg-gray-50"
                    onclick="showEmployees({{ $unit->id }})">

                    <div class="text-gray-500 text-sm">[{{ $unit->code }}]</div>
                    <div class="font-semibold text-sm truncate">{{ $unit->name }}</div>
                    <div class="text-lg font-bold mt-1 text-blue-600">{{ $unit->employees_count }}</div>
                </div>
            @endforeach

        </div>
    </div>

</div>

<!-- MODAL LISTA -->
<div id="employeesModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-3/4 max-h-[80vh] overflow-auto p-6">

        <h2 class="text-lg font-bold mb-4">Zaposleni</h2>

        <table class="w-full text-sm">
            <tbody id="employeesTable"></tbody>
        </table>

    </div>
</div>

<!-- MODAL DETALJI -->
<div id="employeeDetailModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-[700px] p-6">

        <h2 class="text-lg font-bold mb-4">Detalji zaposlenog</h2>

        <div id="employeeDetailContent" class="grid grid-cols-2 gap-4 text-sm"></div>

        <div class="mt-4 text-right">
            <button id="addWorkBtn" class="bg-green-500 text-white px-3 py-1 rounded">
                + Unos rada
            </button>
        </div>

    </div>
</div>

<script>

// =====================================================
// PRIKAZ ZAPOSLENIH
// =====================================================
function showEmployees(unitId){

    let tbody = document.getElementById('employeesTable');

    document.getElementById('employeesModal').classList.remove('hidden');

    fetch(`/employees/by-unit/${unitId}`)
    .then(res => res.json())
    .then(data => {

        tbody.innerHTML = '';

        data.forEach(emp => {

            tbody.innerHTML += `
                <tr 
                    class="border-b hover:bg-gray-50 cursor-pointer employee-row"
                    data-id="${emp.id}"
                >
                    <td class="p-2">${emp.employee_number ?? ''}</td>
                    <td class="p-2">${emp.first_name} ${emp.last_name}</td>
                    <td class="p-2">${emp.position ?? ''}</td>
                </tr>
            `;
        });
    });
}

document.addEventListener('click', function(e){

    const row = e.target.closest('.employee-row');

    if (row) {
        const id = row.dataset.id;

        console.log('KLIK NA RED:', id);

        showEmployeeDetail(id);
    }

});

// =====================================================
// DETALJI ZAPOSLENOG
// =====================================================
function showEmployeeDetail(id){

    fetch(`/employees/${id}/json`)
    .then(res => res.json())
    .then(emp => {

        // 🔥 KLJUČ
        window.currentEmployeeId = emp.id;
        console.log('SET GLOBAL ID:', emp.id);

        document.getElementById('employeeDetailContent').innerHTML = `
            <div><b>Matični broj:</b> ${emp.employee_number ?? '-'}</div>
            <div><b>Ime:</b> ${emp.first_name ?? '-'}</div>
            <div><b>Prezime:</b> ${emp.last_name ?? '-'}</div>
            <div><b>Pozicija:</b> ${emp.position ?? '-'}</div>
            <div><b>Email:</b> ${emp.email ?? '-'}</div>
        `;

        document.getElementById('employeeDetailModal').classList.remove('hidden');
    });
}

// =====================================================
// DUGME UNOS RADA
// =====================================================
document.addEventListener('click', function(e){

    const btn = e.target.closest('#addWorkBtn');

    if (btn) {

        const employeeId = window.currentEmployeeId;

        console.log('GLOBAL ID:', employeeId);

        if (!employeeId) {
            alert('Greška: nema employee ID');
            return;
        }

        // OVDE TI SE POZIVA TVOJ MODAL
        openWorkEntryModal(employeeId);
    }
});

</script>

@endsection
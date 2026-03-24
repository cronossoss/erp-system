@extends('layouts.app')

@if($expiringContracts->count())
<div class="bg-red-100 border border-red-300 text-red-800 p-4 rounded mb-4">

    <div class="font-bold mb-2">
        Ugovori ističu uskoro (15 dana)
    </div>

    <ul class="text-sm space-y-1">
        @foreach($expiringContracts as $emp)

            @php
                $end = \Carbon\Carbon::parse($emp->contract_end_date);
                $today = \Carbon\Carbon::today();
                $days = $today->diffInDays($end, false); // može biti negativno
            @endphp

            <li class="
                @if($days < 0)
                    text-gray-500
                @elseif($days <= 3)
                    text-red-700 font-semibold
                @elseif($days <= 15)
                    text-yellow-700
                @endif
            ">
                {{ $emp->first_name }} {{ $emp->last_name }} —

                @if($days < 0)
                    istekao pre {{ abs($days) }} {{ abs($days) == 1 ? 'dan' : 'dana' }}
                @elseif($days === 0)
                    ističe danas
                @else
                    ističe za {{ $days }} {{ $days == 1 ? 'dan' : 'dana' }}
                @endif

                ({{ \Carbon\Carbon::parse($emp->contract_end_date)->format('d/m/Y') }})

                {{-- 🔥 HITNO BADGE --}}
                @if($days <= 3 && $days >= 0)
                    <span class="ml-2 px-2 py-1 bg-red-600 text-white text-xs rounded animate-pulse">
                        HITNO
                    </span>
                @endif

            </li>

        @endforeach
    </ul>

</div>
@endif

@section('content')
<div class="p-4">

    <h2 class="text-xl font-semibold mb-4">Dashboard</h2>

    <div class="bg-white p-4 rounded shadow mb-4">

    @php $user = auth()->user(); @endphp

        <h4 class="text-lg font-semibold">
            Dobrodošao,
            {{ $user->employee
                ? $user->employee->first_name . ' ' . $user->employee->last_name
                : $user->name }}
        </h4>

    <p class="text-sm text-gray-600">
        Organizacija:
        {{ auth()->user()->employee->organizationalUnit->name ?? '-' }}
    </p>
</div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="group [perspective:1000px]">

            <a href="{{ route('employees.index') }}"
                    class="relative block h-32 transition duration-500 ease-in-out [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">

                        {{-- FRONT --}}
                        <div class="absolute inset-0 bg-white rounded shadow p-4 flex flex-col justify-center
                                    [backface-visibility:hidden]">

                            <div class="text-sm text-gray-500">Zaposleni</div>

                            <div class="text-2xl font-bold">
                                {{ $employeesCount }}
                            </div>

                        </div>

                        {{-- BACK --}}
                        <div class="absolute inset-0 bg-blue-600 text-white rounded shadow p-4 flex items-center justify-center text-center
                                    [transform:rotateY(180deg)] [backface-visibility:hidden]">

                            Ovde možete videti<br>spisak radnika

                        </div>

                    </a>

            </div>

            <div class="group [perspective:1000px]">

                <a href="{{ route('organizational-units.index') }}"
                class="relative block h-32 transition duration-500 ease-in-out [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">

                    {{-- FRONT --}}
                    <div class="absolute inset-0 bg-white rounded shadow p-4 flex flex-col justify-center
                                [backface-visibility:hidden]">

                        <div class="text-sm text-gray-500">Organizacione jedinice</div>

                        <div class="text-2xl font-bold">
                            {{ $organizationalUnitsCount }}
                        </div>

                    </div>

                    {{-- BACK --}}
                    <div class="absolute inset-0 bg-blue-600 text-white rounded shadow p-4 flex items-center justify-center text-center
                                [transform:rotateY(180deg)] [backface-visibility:hidden]">

                        Ovde možete videti<br>spisak organizacionih jedinica

                    </div>

                </a>

            </div>

            <div class="group [perspective:1000px]">

                <a href="{{ route('organizational-groups.index') }}"
                class="relative block h-32 transition duration-500 ease-in-out [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">

                    {{-- FRONT --}}
                    <div class="absolute inset-0 bg-white rounded shadow p-4 flex flex-col justify-center
                                [backface-visibility:hidden]">

                        <div class="text-sm text-gray-500">Organizacione celine</div>

                        <div class="text-2xl font-bold">
                            {{ $groupsCount }}
                        </div>

                    </div>

                    {{-- BACK --}}
                    <div class="absolute inset-0 bg-blue-600 text-white rounded shadow p-4 flex items-center justify-center text-center
                                [transform:rotateY(180deg)] [backface-visibility:hidden]">

                        Ovde možete videti<br>spisak organizacionih celina

                    </div>

                </a>

            </div>

    </div>

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

  <div id="employeesOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>

<div id="employeesModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-3/4 max-h-[80vh] overflow-auto p-6">

        <h2 class="text-lg font-bold mb-4">Zaposleni</h2>

        <input type="text" id="employeeSearch"
            placeholder="Pretraga zaposlenih..."
            class="w-full border px-3 py-2 mb-4"
            onkeyup="filterEmployees()">

        <select id="contractFilter"
            class="w-full border px-3 py-2 mb-4"
            onchange="filterEmployees()">

            <option value="">-- Svi ugovori --</option>
            <option value="neodređeno">Neodređeno</option>
            <option value="određeno">Određeno</option>
            <option value="drugo">Drugo</option>
        </select>

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

<div id="employeeDetailModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-[700px] p-6">

        <h2 class="text-lg font-bold mb-4">Detalji zaposlenog</h2>

        <div id="employeeDetailContent" class="grid grid-cols-2 gap-4 text-sm"></div>

        <div id="employeeDetailFooter" class="text-right mt-4 space-x-2">
            <button onclick="closeEmployeeDetail()" class="px-4 py-2 border rounded">
                Zatvori
            </button>
        </div>

    </div>
</div>
<script>

function showEmployees(unitId){

    let tbody = document.getElementById('employeesTable');

    document.getElementById('employeesModal').classList.remove('hidden');
    document.getElementById('employeesOverlay').classList.remove('hidden');

    fetch(`/employees/by-unit/${unitId}`)
    .then(res => res.json())
    .then(data => {

        tbody.innerHTML = '';

        if(data.length === 0){
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="p-4 text-center text-gray-400 italic">
                        Nema zaposlenih
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(emp => {

            let contract = (emp.contract_type ?? '').toLowerCase();

            let searchText = (
                (emp.employee_number ?? '') + ' ' +
                (emp.first_name ?? '') + ' ' +
                (emp.last_name ?? '') + ' ' +
                (emp.position ?? '') + ' ' +
                contract
            ).toLowerCase();

            tbody.innerHTML += `
                <tr class="border-b hover:bg-gray-50 cursor-pointer employee-row"
                    data-id="${emp.id}"
                    data-search="${searchText}"
                    data-contract="${contract}">

                    <td class="p-2">${emp.employee_number ?? ''}</td>
                    <td class="p-2">${emp.first_name} ${emp.last_name}</td>
                    <td class="p-2">${emp.position ?? ''}</td>
                    <td class="p-2">${emp.organizational_unit_name ?? ''}</td>

                </tr>
            `;
        });
    });
}

function closeEmployeesModal(){
    document.getElementById('employeesModal').classList.add('hidden');
    document.getElementById('employeesOverlay').classList.add('hidden');
}

document.addEventListener('click', function(e){

    let row = e.target.closest('.employee-row');

    if(row){
        let id = row.dataset.id;
        showEmployeeDetail(id);
    }

});

function showEmployeeDetail(id){

    fetch(`/employees/${id}/json`)
    .then(res => res.json())
    .then(emp => {

        document.getElementById('employeeDetailContent').innerHTML = `
            <div><b>Matični broj:</b> ${emp.employee_number ?? '-'}</div>
            <div><b>Ime:</b> ${emp.first_name ?? '-'}</div>

            <div><b>Prezime:</b> ${emp.last_name ?? '-'}</div>
            <div><b>Pozicija:</b> ${emp.position ?? '-'}</div>

            <div><b>Email:</b> ${emp.email ?? '-'}</div>
            <div><b>Telefon:</b> ${emp.phone_work ?? '-'}</div>

            <div><b>Jedinica:</b> ${emp.organizational_unit_name ?? '-'}</div>
            <div><b>Ugovor:</b> ${emp.contract_type ?? '-'}</div>
        `;

        document.getElementById('employeeDetailModal').classList.remove('hidden');
    });
}

function closeEmployeeDetail(){
    document.getElementById('employeeDetailModal').classList.add('hidden');
}


function filterEmployees(){

    let search = document.getElementById('employeeSearch').value.toLowerCase();
    let contract = document.getElementById('contractFilter').value;

    let rows = document.querySelectorAll('#employeesTable .employee-row');

    rows.forEach(row => {

        let text = row.getAttribute('data-search') || '';
        let rowContract = row.getAttribute('data-contract') || '';

        let matchSearch = text.includes(search);
        let matchContract = !contract || rowContract === contract;

        if(matchSearch && matchContract){
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }

    });
}
</script>

  


@endsection
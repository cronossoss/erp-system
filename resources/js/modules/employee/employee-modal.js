let currentEmployee = null;

// =====================
// HELPER
// =====================

function formatDate(dateStr) {
    if (!dateStr) return '-';

    const d = new Date(dateStr);
    if (isNaN(d)) return dateStr;

    return d.toLocaleDateString('sr-RS'); // 24.03.2026
}

function formatForInput(dateStr) {
    if (!dateStr) return '';

    const d = new Date(dateStr);
    if (isNaN(d)) return '';

    return d.toISOString().split('T')[0]; // 2026-03-24
}

function initDatepickers() {
    flatpickr("#edit_birth_date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        maxDate: "today"
    });

    flatpickr("#edit_employment_date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y"
    });

    flatpickr("#edit_contract_end_date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        minDate: "today"
    });
}

// =====================
// OPEN MODAL
// =====================
export function openEmployeeModal(id) {
    
    fetch(`/employees/${id}`)
        .then(res => res.json())
        .then(data => {
            currentEmployee = data;

            renderView(data);
            fillEdit(data);

            setMode(false);

            const modal = document.getElementById('employeeModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                initDatepickers();
            }, 50);
        })
        .catch(err => {
            console.error(err);
            alert('Greška pri učitavanju');
        });
}
window.openEmployeeModal = openEmployeeModal;



// =====================
// VIEW MODE
// =====================
function renderView(data) {
    const content = `
        <div class="flex gap-6">

            <!-- LEVO -->
            <div class="w-64 border-r pr-6">

                <div class="text-center">
                    <img 
                        src="${data.photo ? '/storage/' + data.photo : '/images/default-avatar.png'}" 
                        class="w-32 h-32 rounded-full object-cover mx-auto border"
                    >

                    <h3 class="mt-4 text-lg font-semibold">
                        ${data.first_name} ${data.last_name}
                    </h3>

                    <p class="text-gray-500 text-sm">
                        ${data.position ?? '-'}
                    </p>
                </div>

                <!-- KONTAKT -->
                <div class="mt-6 border-t pt-4 text-sm">
                    <h4 class="text-gray-500 mb-2">Kontakt</h4>

                    <div class="mb-2">
                        <span class="text-gray-500">Email</span><br>
                        <strong>${data.email ?? '-'}</strong>
                    </div>

                    <div class="mb-2">
                        <span class="text-gray-500">Telefon</span><br>
                        <strong>${data.phone_work ?? '-'}</strong>
                    </div>

                    <div>
                        <span class="text-gray-500">Privatni</span><br>
                        <strong>${data.phone_private ?? '-'}</strong>
                    </div>
                </div>

            </div>

            <!-- DESNO -->
            <div class="flex-1 space-y-6">

                <!-- RADNI PODACI -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-600 mb-2 border-b pb-1">
                        Radni podaci
                    </h4>

                    <div class="grid grid-cols-2 gap-4 text-sm">

                        <div>
                            <span class="text-gray-500">Matični broj</span><br>
                            <strong>${data.employee_number ?? '-'}</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">JMBG</span><br>
                            <strong>${data.jmbg ?? '-'}</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">Organizaciona jedinica</span><br>
                            <strong>${
                                data.organizational_unit
                                    ? (data.organizational_unit.code
                                        ? data.organizational_unit.code + ' - '
                                        : '') + data.organizational_unit.name
                                    : '-'
                            }</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">Tip ugovora</span><br>
                            <strong>${data.contract_type?.name ?? '-'}</strong>

                            ${
                                data.contract_end_date
                                    ? `<div class="mt-2">
                                            <span class="text-gray-500 text-xs">Istek ugovora</span><br>
                                            <span class="font-semibold text-red-600">
                                                ${formatDate(data.contract_end_date)}
                                            </span>
                                    </div>`
                                    : ''
                            }
                        </div>

                        <div>
                            <span class="text-gray-500">Datum zaposlenja</span><br>
                            <strong>${formatDate(data.employment_date)}</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">Datum rođenja</span><br>
                            <strong>${formatDate(data.birth_date)}</strong>
                        </div>

                    </div>
                </div>
             
            </div>
        </div>
    `;

    document.getElementById('viewMode').innerHTML = content;
}


// =====================
// EDIT MODE FILL
// =====================
function fillEdit(data) {
    document.getElementById('edit_first_name').value = data.first_name || '';
    document.getElementById('edit_last_name').value = data.last_name || '';
    document.getElementById('edit_position').value = data.position || '';

    document.getElementById('edit_employee_number').value = data.employee_number || '';
    document.getElementById('edit_jmbg').value = data.jmbg || '';

    document.getElementById('edit_unit_id').value = data.organizational_unit_id || '';

    const select = document.getElementById('edit_contract_type_id');
    if (select) {
    select.value = data.contract_type_id ? String(data.contract_type_id) : '';
}

    document.getElementById('edit_employment_date').value = data.employment_date || '';
    document.getElementById('edit_contract_end_date').value = data.contract_end_date || '';
    document.getElementById('edit_birth_date').value = formatForInput(data.birth_date);

    document.getElementById('edit_email').value = data.email || '';
    document.getElementById('edit_phone_work').value = data.phone_work || '';
    document.getElementById('edit_phone_private').value = data.phone_private || '';
}


// =====================
// MODE SWITCH
// =====================
function setMode(edit) {
    document.getElementById('viewMode').classList.toggle('hidden', edit);
    document.getElementById('editMode').classList.toggle('hidden', !edit);

    document.getElementById('editBtn').classList.toggle('hidden', edit);
    document.getElementById('saveBtn').classList.toggle('hidden', !edit);
}


// =====================
// SAVE
// =====================
function saveEmployee() {
    const formData = new FormData();

    formData.append('_method', 'PUT');

    formData.append('first_name', document.getElementById('edit_first_name').value);
    formData.append('last_name', document.getElementById('edit_last_name').value);
    formData.append('position', document.getElementById('edit_position').value);

    formData.append('employee_number', document.getElementById('edit_employee_number').value);
    formData.append('jmbg', document.getElementById('edit_jmbg').value);

    formData.append('organizational_unit_id', document.getElementById('edit_unit_id').value);

    const contractVal = document.getElementById('edit_contract_type_id').value;
    console.log('CONTRACT VALUE:', contractVal);

    formData.append('contract_type_id', contractVal !== '' ? contractVal : '');

    formData.append('employment_date', document.getElementById('edit_employment_date').value);
    formData.append('contract_end_date', document.getElementById('edit_contract_end_date').value);
    formData.append('birth_date', document.getElementById('edit_birth_date').value);

    formData.append('email', document.getElementById('edit_email').value);
    formData.append('phone_work', document.getElementById('edit_phone_work').value);
    formData.append('phone_private', document.getElementById('edit_phone_private').value);

    fetch(`/employees/${currentEmployee.id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                console.error('SERVER ERROR:', text);
                throw new Error(text);
            });
        }
        return response.json();
    })
    .then(data => {
        currentEmployee = data;

        renderView(data);
        fillEdit(data);
        setMode(false);
    })
    .catch(err => {
        console.error(err);
        alert('Greška pri snimanju (vidi console)');
    });
}


// =====================
// EVENTS
// =====================
document.addEventListener('DOMContentLoaded', () => {

    document.getElementById('editBtn')?.addEventListener('click', () => {
        setMode(true);
    });

    document.getElementById('saveBtn')?.addEventListener('click', () => {
        saveEmployee();
    });

    const modal = document.getElementById('employeeModal');

    modal?.addEventListener('click', (e) => {
        if (e.target === modal) closeEmployeeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEmployeeModal();
    });
});


// =====================
// CLOSE
// =====================
export function closeEmployeeModal() {
    const modal = document.getElementById('employeeModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
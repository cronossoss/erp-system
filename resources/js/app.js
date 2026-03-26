import './bootstrap';

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import { openEmployeeModal, closeEmployeeModal } from "./modules/employee/employee-modal";


// GLOBAL
window.openEmployeeModal = openEmployeeModal;
window.closeEmployeeModal = closeEmployeeModal;
window.flatpickr = flatpickr;

console.log('APP JS LOADED');

// =====================================================
// GLOBAL CLICK HANDLER
// =====================================================
document.addEventListener('click', function(e) {

    // 👉 KLIK NA RED (employee)
    const row = e.target.closest('.employee-row');
    if (row && row.dataset.id) {
        openEmployeeModal(row.dataset.id);
        return;
    }

    // 👉 + UNOS RADA
    const btn = e.target.closest('#addWorkBtn');
    if (btn) {

        const employeeId = window.currentEmployeeId;

        console.log('GLOBAL ID:', employeeId);

        if (!employeeId) {
            alert('Greška: nema employee ID');
            return;
        }

        openWorkEntryModal(employeeId);
        return;
    }

});

window.openWorkEntryModal = function(employeeId) {

    console.log('Otvaram modal za employee:', employeeId);

    // postavi employee_id
    document.getElementById('we_employee_id').value = employeeId;

    let modal = document.getElementById('workEntryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // 🔥 OVDE UBACUJEŠ FETCH
 

}

// =====================================================
// SUBMIT WORK ENTRY FORM
// =====================================================
document.addEventListener('submit', function(e) {

    if (e.target && e.target.id === 'workEntryForm') {

        e.preventDefault();

        console.log('SUBMIT WORK ENTRY');

        let formData = new FormData(e.target);

        // 🔥 DEBUG
        console.log([...formData.entries()]);

        fetch('/work-entries', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async res => {

            const text = await res.text();
            console.log('SERVER RESPONSE:', text);

            if (!res.ok) {
                alert('Greška pri unosu');
                return;
            }

            try {
                // ✅ SUCCESS BLOK
                closeWorkEntryModal();

                document.getElementById('workEntryForm').reset();

                console.log('TOAST SE POZIVA');

                showToast('Sačuvano!');
            } catch (err) {
                console.error('POST-SUCCESS ERROR:', err);
            }

            if (window.currentEmployeeId) {
                openEmployeeModal(window.currentEmployeeId);
            }

        })
        .catch(err => {
            console.error('FETCH ERROR:', err);
            alert('Fetch greška');
        });
    }

});

window.closeWorkEntryModal = function() {

    let modal = document.getElementById('workEntryModal');

    if (!modal) {
        console.error('Modal nije pronađen!');
        return;
    }

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    const form = document.getElementById('workEntryForm');
    if (form) form.reset();
}

// =====================================================
// OTVARANJE MODALA ZA RADNO VREME
// =====================================================

document.addEventListener('click', function(e){

    const btn = e.target.closest('#openWorkHistoryBtn');

    if (btn) {

        const id = window.currentEmployeeId;

        openWorkHistoryModal(id);
    }

});

// =====================================================
// FUNKCIJA ZA RADNO VREME
// =====================================================

window.openWorkHistoryModal = function(employeeId) {

    const modal = document.getElementById('workHistoryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    const fromInput = document.getElementById('wh_from');
    const toInput = document.getElementById('wh_to');

    const savedFrom = localStorage.getItem('workHistory_from');
    const savedTo = localStorage.getItem('workHistory_to');

    if (savedFrom && savedTo) {
        fromInput.value = savedFrom;
        toInput.value = savedTo;

        loadWorkHistory(employeeId, savedFrom, savedTo);
    } else {
        loadWorkHistory(employeeId);
    }

        loadWorkHistory(employeeId);
    }

// =====================================================
// LOAD I FILTER
// =====================================================

function loadWorkHistory(employeeId, from = null, to = null) {

    let url = `/employees/${employeeId}/work-entries`;

    let totalMinutes = 0;
    let paidMinutes = 0;
    let unpaidMinutes = 0;

    if (from && to) {
        url += `?from=${from}&to=${to}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {

            const list = document.getElementById('workHistoryList');
            list.innerHTML = '';

            if (!data.length) {
                list.innerHTML = '<div class="text-gray-400">Nema unosa</div>';
            }

            data.forEach(e => {

                const fromT = e.time_from?.substring(11,16) ?? '';
                const toT = e.time_to?.substring(11,16) ?? '';

                if (e.time_from && e.time_to) {

                    const start = new Date(e.time_from);
                    const end = new Date(e.time_to);

                    const diff = (end - start) / 1000 / 60;

                    if (!isNaN(diff)) {

                        totalMinutes += diff;

                        if (e.type?.is_paid) {
                            paidMinutes += diff;
                        } else {
                            unpaidMinutes += diff;
                        }
                    }
                }

                list.innerHTML += `
                    <div class="border p-2 rounded flex justify-between">
                        <div>${e.date}</div>
                        <div>${e.type?.name}</div>
                        <div>${fromT} - ${toT}</div>
                    </div>
                `;
            });

            function format(min) {
                const h = Math.floor(min / 60);
                const m = Math.round(min % 60);
                return `${h}h ${m}min`;
            }

            document.getElementById('workHistorySummary').textContent =
                `Ukupno: ${format(totalMinutes)}`;

            document.getElementById('workHistoryPaid').textContent =
                `Plaćeni sati: ${format(paidMinutes)}`;

            document.getElementById('workHistoryUnpaid').textContent =
                `Neplaćeni sati: ${format(unpaidMinutes)}`;

        });
}

document.addEventListener('click', function(e){

    const btn = e.target.closest('#wh_filter');

    if (btn) {

        const from = document.getElementById('wh_from').value;
        const to = document.getElementById('wh_to').value;

        const id = window.currentEmployeeId;

        loadWorkHistory(id, from, to);
    }

});

// =====================================================
// AUTO FILTER (date change)
// =====================================================
function autoFilter() {

    const from = document.getElementById('wh_from')?.value;
    const to = document.getElementById('wh_to')?.value;
    
    if (from && to && window.currentEmployeeId) {

        // 🔥 SAČUVAJ
        localStorage.setItem('workHistory_from', from);
        localStorage.setItem('workHistory_to', to);

        loadWorkHistory(window.currentEmployeeId, from, to);
    }
}

// Godišnji odmor VACATION - OTVARANJE
document.getElementById('addVacationBtn').addEventListener('click', () => {
    document.getElementById('vacationModal').classList.remove('hidden');

    // postavi employee id
    document.getElementById('vac_employee_id').value = window.currentEmployeeId;
});
// Godišnji odmor VACATION - ZATVARANJE
function closeVacationModal() {
    document.getElementById('vacationModal').classList.add('hidden');
}
// Godišnji odmor VACATION - SUBMIT
document.getElementById('vacationForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/vacations', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => {
    if (!res.ok) {
        return res.json().then(err => { throw err });
    }
    return res.json();
    })
    .then(data => {
        closeVacationModal();
        alert('Sačuvano!');
    })
    .catch(err => {
        alert(err.error || 'Greška pri unosu');
    });
    });

// EVENTI
document.addEventListener('change', function(e){

    if (e.target.id === 'wh_from' || e.target.id === 'wh_to') {
        autoFilter();
    }

});

// =====================================================
// funkcija za zatvaranje modala radnog vremena
// =====================================================

window.closeWorkHistoryModal = function() {

    const modal = document.getElementById('workHistoryModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// =====================================================
// klik van modala zatvara i esc zatvara
// =====================================================

document.addEventListener('click', function(e) {

    const modal = document.getElementById('workHistoryModal');

    if (e.target === modal) {
        closeWorkHistoryModal();
    }

});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeWorkHistoryModal();
    }
});

// =====================================================
// TOAST
// =====================================================
window.showToast = function(msg, type = 'success') {

    const toast = document.getElementById('toast');

    if (!toast) {
        console.error('Toast element ne postoji!');
        return;
    }

    toast.textContent = msg;

    toast.className = `fixed top-5 right-5 px-4 py-2 rounded text-white shadow-lg z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;

    toast.classList.remove('hidden');

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}
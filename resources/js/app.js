import './bootstrap';

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import { searchEmployees } from "./modules/employee/employee-search";
import { openEmployeeModal, closeEmployeeModal } from "./modules/employee/employee-modal";

console.log("APP JS RADI");

// =====================================================
// GLOBAL (dostupno iz Blade-a)
// =====================================================
window.searchEmployees = searchEmployees;
window.openEmployeeModal = openEmployeeModal;
window.closeEmployeeModal = closeEmployeeModal;
window.flatpickr = flatpickr;

// =====================================================
// DELETE STATE
// =====================================================
let deleteId = null;

// =====================================================
// GLOBAL CLICK HANDLER (SVE NA JEDNOM MESTU)
// =====================================================
document.addEventListener('click', function(e) {

    // =========================
    // DELETE BUTTON
    // =========================
    const deleteBtn = e.target.closest('.delete-btn');
    if (deleteBtn) {
        e.stopPropagation();

        deleteId = deleteBtn.dataset.id;

        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        return;
    }

    // =========================
    // ROW CLICK → otvara zaposlenog
    // =========================
    const row = e.target.closest('.employee-row');
    if (row && row.dataset.id) {
        openEmployeeModal(row.dataset.id);
        return;
    }

    // =========================
    // + UNOS RADA BUTTON
    // =========================
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

// =====================================================
// DELETE ACTIONS
// =====================================================
window.closeDeleteModal = function() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.confirmDelete = function() {

    fetch(`/employees/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        closeDeleteModal();
        location.reload();
    })
    .catch(() => alert('Greška pri brisanju'));

}

// =====================================================
// WORK ENTRY MODAL (otvaranje/zatvaranje)
// =====================================================
window.openWorkEntryModal = function(employeeId) {

    console.log('Otvaram modal za employee:', employeeId);

    document.getElementById('we_employee_id').value = employeeId;

    let modal = document.getElementById('workEntryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

window.closeWorkEntryModal = function() {

    let modal = document.getElementById('workEntryModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// =====================================================
// SUBMIT WORK ENTRY FORM
// =====================================================
document.addEventListener('submit', function(e) {

    if (e.target && e.target.id === 'workEntryForm') {

        e.preventDefault();

        console.log('SUBMIT WORK ENTRY');

        let formData = new FormData(e.target);

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
                showToast('Greška pri unosu', 'error');
                return;
            }

            // SUCCESS
            closeWorkEntryModal();

            // reset forme
            document.getElementById('workEntryForm').reset();

            showToast('Sačuvano!');
        })
        .catch(err => {
            console.error(err);
            showToast('Fetch greška', 'error');
        });
    }

});

// =====================================================
// TOAST (lep umesto alert)
// =====================================================
function showToast(msg, type = 'success') {

    const toast = document.getElementById('toast');

    toast.textContent = msg;

    toast.className = `fixed top-5 right-5 px-4 py-2 rounded text-white shadow-lg z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;

    toast.classList.remove('hidden');

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}
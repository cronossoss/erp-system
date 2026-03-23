export function showEmployeeDetail(id){

    fetch(`/employees/${id}/json`)
    .then(res => res.json())
    .then(emp => {

        let html = `
            <div class="text-lg font-bold mb-4">
                ${emp.first_name} ${emp.last_name}
            </div>

            <div class="text-sm">
                <p><b>Pozicija:</b> ${emp.position ?? '-'}</p>
                <p><b>Email:</b> ${emp.email ?? '-'}</p>
                <p><b>Telefon:</b> ${emp.phone_work ?? '-'}</p>
            </div>
        `;

        document.getElementById('employeeDetailContent').innerHTML = html;

        document.getElementById('overlay').classList.remove('hidden');
        document.getElementById('employeeDetailModal').classList.remove('hidden');
    });
}
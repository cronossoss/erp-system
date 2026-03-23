export function openEmployeeModal(id) {
    fetch(`/employees/${id}`)
        .then(res => {
            if (!res.ok) {
                throw new Error("Server error");
            }
            return res.json();
        })
        .then(data => {

            const content = `
                <div class="flex gap-6">

                    <!-- AVATAR -->
                    <div class="w-40 text-center">
                        <img 
                            src="${data.photo ? '/storage/' + data.photo : '/images/default-avatar.png'}" 
                            class="w-32 h-32 rounded-full object-cover mx-auto border"
                        >
                        <p class="mt-2 font-semibold">${data.first_name} ${data.last_name}</p>
                        <p class="text-sm text-gray-500">${data.position ?? '-'}</p>
                    </div>

                    <!-- DETALJI -->
                    <div class="flex-1 grid grid-cols-2 gap-4 text-sm">

                        <div>
                            <span class="text-gray-500">Matični broj</span><br>
                            <strong>${data.employee_number}</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">JMBG</span><br>
                            <strong>${data.jmbg ?? '-'}</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">Organizaciona jedinica</span><br>
                            <strong>${
                                data.organizational_unit 
                                    ? data.organizational_unit.name 
                                    : '-'
                            }</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">Tip ugovora</span><br>
                            <strong>${data.contract_type ?? '-'}</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">Datum zaposlenja</span><br>
                            <strong>${data.employment_date ?? '-'}</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">Email</span><br>
                            <strong>${data.email ?? '-'}</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">Telefon (posao)</span><br>
                            <strong>${data.phone_work ?? '-'}</strong>
                        </div>

                        <div>
                            <span class="text-gray-500">Telefon (privatni)</span><br>
                            <strong>${data.phone_private ?? '-'}</strong>
                        </div>

                    </div>
                </div>
            `;

            document.getElementById('employeeModalContent').innerHTML = content;

            const modal = document.getElementById('employeeModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        })
        .catch(err => {
            console.error(err);
            alert("Greška pri učitavanju radnika");
        });
}

export function closeEmployeeModal() {
    const modal = document.getElementById('employeeModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('employeeModal');

    if (!modal) return;

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            window.closeEmployeeModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            window.closeEmployeeModal();
        }
    });
});


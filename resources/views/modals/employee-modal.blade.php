<!-- resources/views/modals/employee-modal.blade.php -->

<div id="employeeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-4xl h-[80vh] rounded-2xl shadow-lg p-8 relative overflow-y-auto">

        <button onclick="closeEmployeeModal()" class="absolute top-2 right-3 text-xl">&times;</button>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Detalji zaposlenog</h2>

            <div class="flex gap-2">
                <button id="editBtn" class="bg-yellow-400 px-3 py-1 rounded">Izmeni</button>
                <button id="saveBtn" class="bg-blue-600 text-white px-3 py-1 rounded hidden">Sačuvaj</button>
            </div>
        </div>

        <!-- VIEW MODE -->
        <div id="viewMode"></div>

        <!-- EDIT MODE -->

        <!-- EDIT MODE -->
<div id="editMode" class="hidden">

    <div class="flex gap-6">

        <!-- LEVO -->
        <div class="w-64 border-r pr-6 text-center">

            <img 
                src="/images/default-avatar.png" 
                class="w-32 h-32 rounded-full object-cover mx-auto border"
            >

            <!-- ime -->
             <label class="text-gray-500">Ime radnika</label>
            <input id="edit_first_name" class="border p-2 w-full mt-4 text-center" placeholder="Ime">
            <label class="text-gray-500">Prezime radnika</label>
            <input id="edit_last_name" class="border p-2 w-full mt-2 text-center" placeholder="Prezime">

            <!-- pozicija -->
            <label class="text-gray-500">Pozicija</label>
            <input id="edit_position" class="border p-2 w-full mt-2 text-center" placeholder="Pozicija">

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
                        <label class="text-gray-500">Matični broj</label>
                        <input id="edit_employee_number" class="border p-2 w-full">
                    </div>

                    <div>
                        <label class="text-gray-500">JMBG</label>
                        <input id="edit_jmbg" class="border p-2 w-full">
                    </div>

                    <div>
                        <label class="text-gray-500">Pol</label>
                        <select id="edit_gender" class="border p-2 w-full">
                            <option value="">-- Izaberi --</option>
                            <option value="M">Muški</option>
                            <option value="Z">Ženski</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-500">Organizaciona jedinica</label>
                        <select id="edit_unit_id" class="border p-2 w-full">
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->code ?? '' }} - {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-500">Tip ugovora</label>
                        <select id="edit_contract_type_id" class="border p-2 w-full">
                            @foreach($contractTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-500">Datum zaposlenja</label>
                        <input id="edit_employment_date" class="datepicker border p-2 w-full">
                    </div>

                    <div>
                        <label class="text-gray-500">Datum isteka ugovora</label>
                        <input id="edit_contract_end_date" class="datepicker border p-2 w-full">
                    </div>

                    <div>
                        <label class="text-gray-500">Datum rođenja</label>
                        <input id="edit_birth_date" class="datepicker border p-2 w-full">
                    </div>

                </div>
            </div>

            <!-- KONTAKT -->
            <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-2 border-b pb-1">
                    Kontakt
                </h4>

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="text-gray-500">Email</label>
                        <input id="edit_email" class="border p-2 w-full">
                    </div>

                    <div>
                        <label class="text-gray-500">Telefon (posao)</label>
                        <input id="edit_phone_work" class="border p-2 w-full">
                    </div>

                    <div>
                        <label class="text-gray-500">Telefon (privatni)</label>
                        <input id="edit_phone_private" class="border p-2 w-full">
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

    
    </div>

</div>
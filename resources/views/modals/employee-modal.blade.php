<div id="employeeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-6xl h-[80vh] rounded-2xl shadow-lg p-8 relative overflow-y-auto">

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
        <div id="editMode" class="hidden">

            <div class="flex gap-8">

                <!-- LEVO -->
                <div class="w-56 text-center border-r pr-6">

                    <img 
                        src="/images/default-avatar.png" 
                        class="w-32 h-32 rounded-full object-cover mx-auto border"
                    >

                    <input id="edit_first_name" class="border p-2 w-full mt-4" placeholder="Ime">
                    <input id="edit_last_name" class="border p-2 w-full mt-2" placeholder="Prezime">

                    <input id="edit_position" class="border p-2 w-full mt-2" placeholder="Pozicija">

                </div>

                <!-- DESNO -->
                <div class="flex-1 space-y-6">

                    <!-- RADNI PODACI -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-600 mb-2 border-b pb-1">
                            Radni podaci
                        </h4>

                        <div class="grid grid-cols-2 gap-4">

                            <input id="edit_employee_number" class="border p-2" placeholder="Matični broj">
                            <input id="edit_jmbg" class="border p-2" placeholder="JMBG">

                            <select id="edit_unit_id" class="border p-2">
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">
                                        {{ $unit->code ?? '' }} - {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="edit_contract_type_id" class="border p-2 w-full mt-1">
                                <option value="" disabled selected>-- Tip ugovora --</option>

                                @foreach($contractTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>

                            <input id="edit_employment_date" type="date" class="border p-2">
                            <div>
                                <span class="text-gray-500 text-sm">Datum isteka ugovora</span>
                                <input 
                                    id="edit_contract_end_date"
                                    type="date"
                                    class="border p-2 w-full mt-1"
                                >
                            </div>

                            <input id="edit_contract_end_date" type="date" class="border p-2 hidden">

                        </div>
                    </div>

                    <!-- KONTAKT -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-600 mb-2 border-b pb-1">
                            Kontakt
                        </h4>

                        <div class="grid grid-cols-2 gap-4">

                            <input id="edit_email" class="border p-2" placeholder="Email">
                            <input id="edit_phone_work" class="border p-2" placeholder="Telefon posao">

                            <input id="edit_phone_private" class="border p-2" placeholder="Telefon privatni">

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
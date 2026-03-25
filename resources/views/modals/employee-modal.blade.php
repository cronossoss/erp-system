<!-- resources/views/modals/employee-modal.blade.php -->

@vite(['resources/js/app.js'])

<div id="employeeModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-[60]">

    <div class="bg-white w-full max-w-4xl h-[80vh] rounded-2xl shadow-lg p-8 relative overflow-y-auto">

        <button onclick="closeEmployeeModal()" class="absolute top-2 right-3 text-xl">&times;</button>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Detalji zaposlenog</h2>

            <div class="flex gap-2">
                <button id="editBtn" class="bg-yellow-400 px-3 py-1 rounded">Izmeni</button>
                <button id="addWorkBtn" class="bg-green-500 text-white px-3 py-1 rounded">+ Unos rada</button>
                <button id="saveBtn" class="bg-blue-600 text-white px-3 py-1 rounded hidden">Sačuvaj</button>
            </div>
        </div>

        <!-- VIEW MODE -->
        <div id="viewMode"></div>

        <button id="openWorkHistoryBtn"
            class="text-blue-600 text-sm underline mt-2">
            Pregled radnog vremena
        </button>

        {{-- <!-- 🔥 LISTA UNOSA RADA -->
        <div class="mt-6">
            <h3 class="text-sm font-semibold mb-2">Unosi rada</h3>

            <div id="workEntriesList" class="text-sm space-y-2 max-h-40 overflow-y-auto">
                <!-- puni JS -->
            </div>
        </div> --}}

@php
    $types = \App\Models\WorkEntryType::all();
@endphp
<div id="workEntryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg p-6 rounded-xl">

        <h2 class="text-lg font-bold mb-4">Dodaj unos</h2>

        <form id="workEntryForm">
            <input type="hidden" name="employee_id" id="we_employee_id">

            <div class="mb-2">
                <label>Tip</label>
                <select id="we_work_entry_type_id" name="work_entry_type_id" class="w-full border p-2">
                    @foreach($types as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <label>Datum</label>
                <input type="date" name="date" class="w-full border p-2">
            </div>

            <div class="flex gap-2">
                <div class="w-1/2">
                    <label>Od</label>
                    <input type="time" name="time_from" class="w-full border p-2">
                </div>
                <div class="w-1/2">
                    <label>Do</label>
                    <input type="time" name="time_to" class="w-full border p-2">
                </div>
            </div>

            <div class="mt-2">
                <label>Napomena</label>
                <textarea name="note" class="w-full border p-2"></textarea>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="closeWorkEntryModal()">Otkaži</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Sačuvaj</button>
            </div>
        </form>

    </div>
</div>

<!-- MODAL ZA VIEW LISTE RADNOG VREMENA -->

<div id="workHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[70]">

    <div class="bg-white w-full max-w-5xl h-[85vh] rounded-xl p-6 overflow-y-auto relative">

              

        <div class="sticky top-0 bg-white z-10 pb-3 border-b mb-4">

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold">Radno vreme</h2>
                
                <!-- 🔥 ZATVARANJE -->
                <button onclick="closeWorkHistoryModal()"
                    class="text-2xl font-bold hover:text-red-500">
                    &times;
                </button>
            </div>

            <!-- FILTER -->
            <div class="flex gap-2 mt-3">
                <input type="date" id="wh_from" class="border p-2">
                <input type="date" id="wh_to" class="border p-2">
                <button id="wh_filter" class="bg-blue-500 text-white px-3 py-1 rounded">
                    Filtriraj
                </button>
            </div>

            <div id="workHistorySummary" class="text-sm font-semibold text-blue-600"></div>

            <div id="workHistoryPaid" class="text-sm text-green-600"></div>
            <div id="workHistoryUnpaid" class="text-sm text-red-600"></div>

        </div>

        <!-- LISTA -->
        <div id="workHistoryList" class="space-y-2 text-sm"></div>

    </div>
</div>

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
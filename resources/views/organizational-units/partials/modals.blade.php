<!-- resources/views/organizational-units/partials/modals.blade.php -->

<div id="unitOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>
<!-- UNIT MODAL -->
<div id="unitModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded w-80">

        <h3 class="text-lg font-bold mb-3">Nova jedinica</h3>

        <!-- NAZIV -->
        <input id="unit_name"
            class="w-full border px-3 py-2 mb-3"
            placeholder="Naziv">

        <!-- ŠIFRA -->
        <input id="unit_code"
            class="w-full border px-3 py-2 mb-3"
            placeholder="Šifra (3 cifre)">

        <!-- PARENT -->
        <select id="parent_id" class="w-full border px-3 py-2 mb-4">
            <option value="">-- Nema (root) --</option>

            @foreach($allUnits as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>

        <!-- DUGMAD -->
        <div class="flex justify-end gap-2">
            <button onclick="closeUnitModal()">Zatvori</button>
            <button onclick="console.log('klik'); saveUnit()" class="bg-blue-600 text-white px-4 py-2 rounded">
                Sačuvaj
            </button>
        </div>

    </div>
</div>
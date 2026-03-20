<!DOCTYPE html>
<html>

<head>
    <title>Edit zaposlenog</title>
</head>

<body>

    <h1>Edit zaposlenog</h1>

    <a href="{{ route('employees.index') }}">← Nazad</a>

    @if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('employees.update', $employee->id) }}">
        @csrf
        @method('PUT')

        <label>Ime:</label><br>
        <input type="text" name="first_name" value="{{ $employee->first_name }}"><br><br>

        <label>Prezime:</label><br>
        <input type="text" name="last_name" value="{{ $employee->last_name }}"><br><br>

        <label>Pozicija:</label><br>
        <input type="text" name="position" value="{{ $employee->position }}"><br><br>

        <label>Organizaciona jedinica:</label><br>
        <select name="organizational_unit_id">
            @foreach($units as $unit)
            <option value="{{ $unit->id }}"
                {{ $employee->organizational_unit_id == $unit->id ? 'selected' : '' }}>
                {{ $unit->name }}
            </option>
            @endforeach
        </select><br><br>

        <button type="submit">Sačuvaj izmene</button>
    </form>

</body>

</html>
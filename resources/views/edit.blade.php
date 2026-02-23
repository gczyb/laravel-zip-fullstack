@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Város szerkesztése</h2>
    <form action="{{ route('cities.update', $city->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Város neve</label>
            <input type="text" name="name" class="form-control" value="{{ $city->name }}" required>
        </div>
        <div class="mb-3">
            <label>Megye</label>
            <select name="county_id" class="form-control" required>
                @foreach($counties as $county)
                    <option value="{{ $county->id }}" {{ $city->county_id == $county->id ? 'selected' : '' }}>{{ $county->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Frissítés</button>
    </form>
</div>
@endsection
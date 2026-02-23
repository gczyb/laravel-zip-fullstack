@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Új város hozzáadása</h2>
    <form action="{{ route('cities.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Város neve</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Megye</label>
            <select name="county_id" class="form-control" required>
                @foreach($counties as $county)
                    <option value="{{ $county->id }}">{{ $county->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Irányítószám (4 számjegy)</label>
            <input type="text" name="postal_code" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Mentés</button>
    </form>
</div>
@endsection
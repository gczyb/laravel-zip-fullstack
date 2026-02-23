@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Város szerkesztése: {{ $city->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('cities.update', $city->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Város neve</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $city->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Megye</label>
                            <select name="county_id" class="form-control @error('county_id') is-invalid @enderror" required>
                                <option value="">-- Válassz megyét --</option>
                                @foreach($counties as $county)
                                    <option value="{{ $county->id }}" {{ (old('county_id', $city->county_id) == $county->id) ? 'selected' : '' }}>
                                        {{ $county->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('county_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('cities.index') }}" class="btn btn-secondary">Vissza</a>
                            <button type="submit" class="btn btn-warning">Frissítés</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
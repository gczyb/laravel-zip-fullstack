@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <h2>Városok listája</h2>
        @if(Auth::check() && Auth::user()->hasVerifiedEmail())
            <a href="{{ route('cities.create') }}" class="btn btn-primary">Új város</a>
        @endif
    </div>

    <form action="{{ route('cities.search') }}" method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="q" class="form-control" placeholder="Keresés..." value="{{ request('q') }}">
        </div>
        <div class="col-md-5">
            <select name="county_id" class="form-control">
                <option value="">-- Minden megye --</option>
                @foreach($counties as $county)
                    <option value="{{ $county->id }}" {{ request('county_id') == $county->id ? 'selected' : '' }}>{{ $county->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-dark w-100">Keresés</button>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('cities.export.csv') }}" class="btn btn-success">CSV Export</a>
            <a href="{{ route('cities.export.pdf') }}" class="btn btn-danger">PDF Export</a>
        </div>
        <div class="col-md-6">
            <form action="{{ route('cities.sendEmail') }}" method="POST" class="d-flex">
                @csrf
                <input type="email" name="email" class="form-control me-2" placeholder="E-mail cím" required>
                <button type="submit" class="btn btn-info text-white">Küldés emailben</button>
            </form>
        </div>
    </div>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Város</th>
                <th>Megye</th>
                <th>Irányítószám</th>
                @auth <th>Műveletek</th> @endauth
            </tr>
        </thead>
        <tbody>
            @forelse($cities as $city)
                <tr>
                    <td>{{ $city->name }}</td>
                    <td>{{ $city->county->name ?? '' }}</td>
                    <td>
                        @foreach($city->postalCodes as $postalCode)
                            {{ $postalCode->code }}
                        @endforeach
                    </td>
                    @auth
                        <td>
                            @if(Auth::user()->hasVerifiedEmail())
                                <a href="{{ route('cities.edit', $city->id) }}" class="btn btn-sm btn-warning">Szerkesztés</a>
                                <form action="{{ route('cities.destroy', $city->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Törlés</button>
                                </form>
                            @else
                                <span class="text-muted">A szerkesztéshez erősítsd meg az e-mail címed!</span>
                            @endif
                        </td>
                    @endauth
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">Nincs adat.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $cities->links() }}
</div>
@endsection
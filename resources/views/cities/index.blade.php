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

    <form action="{{ route('cities.search') }}" method="GET" class="row g-2 mb-4" id="searchForm">
        <div class="col-md-6">
            <input type="text" name="q" id="searchInput" class="form-control" placeholder="Keresés városra vagy irányítószámra..." value="{{ request('q') }}" autocomplete="off">
        </div>
        <div class="col-md-6">
            <select name="county_id" id="countySelect" class="form-control">
                <option value="">-- Minden megye --</option>
                @foreach($counties as $county)
                    <option value="{{ $county->id }}" {{ request('county_id') == $county->id ? 'selected' : '' }}>{{ $county->name }}</option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="d-none">Keresés</button>
    </form>

    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('cities.export.csv', ['q' => request('q'), 'county_id' => request('county_id')]) }}" class="btn btn-success">CSV Export</a>
            <a href="{{ route('cities.export.pdf', ['q' => request('q'), 'county_id' => request('county_id')]) }}" class="btn btn-danger">PDF Export</a>
        </div>
        <div class="col-md-6">
            <form action="{{ route('cities.sendEmail') }}" method="POST" class="d-flex">
                @csrf
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="hidden" name="county_id" value="{{ request('county_id') }}">

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
                            <span class="badge bg-secondary">{{ $postalCode->code }}</span>
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
    
    <div class="d-flex justify-content-center">
        {{ $cities->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        const countySelect = document.getElementById('countySelect');

        let typingTimer;                
        const doneTypingInterval = 500; // 500 ms = Fél másodperc várakozás gépelés után

        // 1. Amikor a felhasználó gépel a szövegmezőbe
        searchInput.addEventListener('input', function () {
            clearTimeout(typingTimer); // Töröljük az előző időzítőt, ha folyamatosan gépel
            typingTimer = setTimeout(function() {
                searchForm.submit(); // Beküldjük az űrlapot
            }, doneTypingInterval);
        });

        // 2. Amikor megváltoztatja a megyét a legördülő menüben (azonnali keresés)
        countySelect.addEventListener('change', function () {
            searchForm.submit();
        });

        // 3. Fókusz megtartása oldalfrissítés után
        // Ha van beleírva szöveg (tehát épp keresett valamit), akkor a kurzort tegyük vissza a szöveg végére
        if (searchInput.value.length > 0) {
            let val = searchInput.value;
            searchInput.focus();
            searchInput.value = ''; // Kiürítjük...
            searchInput.value = val; // Majd visszatesszük a szöveget, hogy a kurzor a legvégére kerüljön
        }
    });
</script>
@endsection
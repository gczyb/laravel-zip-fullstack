<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\County;
use App\Models\PostalCode;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\CitiesExportMail;

class CityController extends Controller
{
    private function getFilteredCities(Request $request)
    {
        $q = $request->input('q');
        $county_id = $request->input('county_id');

        $query = City::with(['county', 'postalCodes']);

        if (!empty($q)) {
            $query->where(function($subQuery) use ($q) {
                $subQuery->where('name', 'LIKE', "%{$q}%")
                         ->orWhereHas('postalCodes', function($q2) use ($q) {
                             $q2->where('code', 'LIKE', "%{$q}%");
                         });
            });
        }

        if (!empty($county_id)) {
            $query->where('county_id', $county_id);
        }

        return $query->orderBy('name');
    }

    public function index()
    {
        $cities = City::with(['county', 'postalCodes'])->orderBy('name')->simplePaginate(15);
        $counties = County::orderBy('name')->get();
        return view('cities.index', compact('cities', 'counties'));
    }

    public function search(Request $request)
    {
        $cities = $this->getFilteredCities($request)->simplePaginate(15)->appends($request->all());
        $counties = County::orderBy('name')->get();

        return view('cities.index', compact('cities', 'counties'));
    }

public function create()
    {
        $counties = County::orderBy('name')->get();
        return view('cities.create', compact('counties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'county_id' => 'required|exists:counties,id',
            'postal_code' => 'required|string|size:4',
        ]);

        $city = City::create([
            'name' => $request->name,
            'county_id' => $request->county_id,
        ]);

        PostalCode::create([
            'code' => $request->postal_code,
            'city_id' => $city->id,
        ]);

        return redirect()->route('cities.index')->with('success', 'Város sikeresen hozzáadva!');
    }

    public function edit(City $city)
    {
        $counties = County::orderBy('name')->get();
        return view('cities.edit', compact('city', 'counties'));
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'county_id' => 'required|exists:counties,id',
        ]);

        $city->update([
            'name' => $request->name,
            'county_id' => $request->county_id,
        ]);

        return redirect()->route('cities.index')->with('success', 'Város frissítve!');
    }

    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->route('cities.index')->with('success', 'Város törölve!');
    }

    public function exportCsv(Request $request)
    {
        $cities = $this->getFilteredCities($request)->get();
        $filename = 'cities_' . date('Y-m-d') . '.csv';

        $callback = function() use ($cities) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); 
            fputcsv($file, ['Város', 'Megye', 'Irányítószám'], ';'); 

            foreach ($cities as $city) {
                $postalCode = $city->postalCodes->first() ? $city->postalCodes->first()->code : 'N/A';
                $countyName = $city->county ? $city->county->name : 'N/A';
                fputcsv($file, [$city->name, $countyName, $postalCode], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        ini_set('memory_limit', '2G');
        set_time_limit(300);

        $cities = $this->getFilteredCities($request)->get();

        $pdf = Pdf::loadView('cities.pdf', [
            'cities' => $cities,
            'title' => 'Városok listája (Szűrt)',
            'date' => now()->format('Y-m-d H:i:s')
        ]);

        return $pdf->download('cities.pdf');
    }

    public function sendEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        Mail::to($request->email)->send(new CitiesExportMail($request->input('q'), $request->input('county_id')));
        
        return back()->with('success', 'Email sikeresen elküldve a PDF melléklettel!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\CountryRequest;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $countries = Country::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('settings/Countries', [
            'countries' => $countries,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(CountryRequest $request)
    {
        Country::create($request->validated());
        return back();
    }

    public function update(CountryRequest $request, Country $country)
    {
        $country->update($request->validated());
        return back();
    }

    public function destroy(Country $country)
    {
        // Num projeto real, validaríamos se o país está a ser usado antes de apagar.
        $country->delete();
        return back();
    }
}
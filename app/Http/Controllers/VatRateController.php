<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\VatRateRequest;
use App\Models\VatRate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VatRateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $rates = VatRate::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('value')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('settings/VatRates', [
            'rates' => $rates,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(VatRateRequest $request)
    {
        VatRate::create($request->validated());
        return back();
    }

    public function update(VatRateRequest $request, VatRate $vatRate)
    {
        $vatRate->update($request->validated());
        return back();
    }

    public function destroy(VatRate $vatRate)
    {
        $vatRate->delete();
        return back();
    }
}
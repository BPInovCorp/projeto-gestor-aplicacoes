<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\CalendarEventTypeRequest;
use App\Models\CalendarEventType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarEventTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $types = CalendarEventType::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('settings/CalendarEventTypes', [
            'types' => $types,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(CalendarEventTypeRequest $request)
    {
        CalendarEventType::create($request->validated());
        return back();
    }

    public function update(CalendarEventTypeRequest $request, CalendarEventType $calendarEventType)
    {
        $calendarEventType->update($request->validated());
        return back();
    }

    public function destroy(CalendarEventType $calendarEventType)
    {
        $calendarEventType->delete();
        return back();
    }
}
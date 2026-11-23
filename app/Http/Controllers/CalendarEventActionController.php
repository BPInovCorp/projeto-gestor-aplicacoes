<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\CalendarEventActionRequest;
use App\Models\CalendarEventAction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarEventActionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $actions = CalendarEventAction::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('settings/CalendarEventActions', [
            'actions' => $actions,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(CalendarEventActionRequest $request)
    {
        CalendarEventAction::create($request->validated());
        return back();
    }

    public function update(CalendarEventActionRequest $request, CalendarEventAction $calendarEventAction)
    {
        $calendarEventAction->update($request->validated());
        return back();
    }

    public function destroy(CalendarEventAction $calendarEventAction)
    {
        $calendarEventAction->delete();
        return back();
    }
}
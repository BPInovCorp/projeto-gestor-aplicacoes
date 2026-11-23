<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\ContactRoleRequest;
use App\Models\ContactRole;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactRoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $roles = ContactRole::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('settings/ContactRoles', [
            'roles' => $roles,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(ContactRoleRequest $request)
    {
        ContactRole::create($request->validated());
        return back();
    }

    public function update(ContactRoleRequest $request, ContactRole $contactRole)
    {
        $contactRole->update($request->validated());
        return back();
    }

    public function destroy(ContactRole $contactRole)
    {
        $contactRole->delete();
        return back();
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\ContactRole;
use App\Models\Entity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('contactos.ler');

        $search = $request->input('search');

        $contacts = Contact::query()
            ->with(['entity:id,name', 'role:id,name'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhereHas('entity', function ($entityQuery) use ($search) {
                            $entityQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('contacts/Index', [
            'contacts' => $contacts,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('contactos.criar');

        return Inertia::render('contacts/Form', [
            'entities' => Entity::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'roles' => ContactRole::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        Gate::authorize('contactos.criar');

        Contact::create($request->validated());

        return redirect()->route('contacts.index');
    }

    public function edit(Contact $contact): Response
    {
        Gate::authorize('contactos.atualizar');

        return Inertia::render('contacts/Form', [
            'contact' => $contact,
            'entities' => Entity::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'roles' => ContactRole::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(ContactRequest $request, Contact $contact): RedirectResponse
    {
        Gate::authorize('contactos.atualizar');

        $contact->update($request->validated());

        return redirect()->route('contacts.index');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        Gate::authorize('contactos.eliminar');

        $contact->delete();

        return back();
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntityRequest;
use App\Models\Entity;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class EntityController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('entidades.ler');

        $type = $request->query('type');
        $search = $request->input('search');

        $entities = Entity::query()
            ->with('country')
            ->when($type === 'cliente', fn ($q) => $q->clients())
            ->when($type === 'fornecedor', fn ($q) => $q->suppliers())
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('entities/Index', [
            'entities' => $entities,
            'filters' => $request->only('search', 'type'),
        ]);
    }

    public function create()
    {
        Gate::authorize('entidades.criar');

        return Inertia::render('entities/Form', [
            'countries' => Country::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function store(EntityRequest $request)
    {
        Entity::create($request->validated());
        return redirect()->route('entities.index');
    }

    public function edit(Entity $entity)
    {
        Gate::authorize('entidades.atualizar');

        return Inertia::render('entities/Form', [
            'entity' => $entity,
            'countries' => Country::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function update(EntityRequest $request, Entity $entity)
    {
        $entity->update($request->validated());
        return redirect()->route('entities.index');
    }

    public function destroy(Entity $entity)
    {
        Gate::authorize('entidades.eliminar');
        $entity->delete();
        return back();
    }
}
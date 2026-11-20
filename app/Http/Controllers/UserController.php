<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('utilizadores.ler');

        $search = $request->input('search');

        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'is_active' => (bool) $user->is_active,
                'role' => $user->roles->first()?->name,
            ]);

        return Inertia::render('users/Index', [
            'users' => $users,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        Gate::authorize('utilizadores.criar');

        return Inertia::render('users/Form', [
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make(Str::random(16));

        $user = User::create($data);
        $user->assignRole($request->input('role'));

        return redirect()->route('utilizadores.index');
    }

    public function edit(User $user)
    {
        Gate::authorize('utilizadores.atualizar');

        return Inertia::render('users/Form', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'is_active' => (bool) $user->is_active,
                'role' => $user->roles->first()?->name,
            ],
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->validated());
        $user->syncRoles([$request->input('role')]);

        return redirect()->route('utilizadores.index');
    }

    public function destroy(User $user)
    {
        Gate::authorize('utilizadores.eliminar');

        if ($user->id === request()->user()->id) {
            return back()->withErrors(['message' => 'Não pode eliminar a sua própria conta.']);
        }

        $user->delete();

        return redirect()->route('utilizadores.index');
    }
}
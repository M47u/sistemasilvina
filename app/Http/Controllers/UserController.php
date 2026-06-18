<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Profesional;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['roles', 'profesional'])
            ->orderBy('name')
            ->paginate(20);

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('usuarios.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $usuario = User::create([
            'name'     => $request->apellido . ' ' . $request->nombre,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $usuario->syncRoles($request->roles);

        if (in_array('Profesional', $request->roles)) {
            Profesional::create([
                'user_id'  => $usuario->id,
                'apellido' => $request->apellido,
                'nombre'   => $request->nombre,
                'area'     => $request->area,
                'activo'   => true,
            ]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        $roles = Role::orderBy('name')->get();
        $usuario->load(['roles', 'profesional']);
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(UserRequest $request, User $usuario)
    {
        $datos = ['name' => $request->apellido . ' ' . $request->nombre, 'email' => $request->email];

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);
        $usuario->syncRoles($request->roles);

        if (in_array('Profesional', $request->roles)) {
            Profesional::updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'apellido' => $request->apellido,
                    'nombre'   => $request->nombre,
                    'area'     => $request->area,
                    'activo'   => true,
                ]
            );
        } else {
            // Si pierde el rol Profesional, desactivar su registro
            $usuario->profesional?->update(['activo' => false]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No podés eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }
}

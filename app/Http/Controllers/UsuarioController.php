<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return view('roles.admin.personal', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:usuarios,username',
            'email'    => 'required|email|unique:usuarios,email',
            'nombre'   => 'required',
            'password' => 'required|string|min:6',
            'rol'      => 'required|in:admin,facturador,bodeguero',
        ]);

        Usuario::create([
            'username' => $request->username,
            'email'    => $request->email,
            'nombre'   => $request->nombre,
            'rol'      => $request->rol,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('personal.index')
                         ->with('success', 'Usuario creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'username' => 'required|unique:usuarios,username,'.$id.',id_usuario',
            'email'    => 'required|email|unique:usuarios,email,'.$id.',id_usuario',
            'nombre'   => 'required',
            'rol'      => 'required|in:admin,facturador,bodeguero',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $request->validate($rules);

        $usuario = Usuario::findOrFail($id);
        $usuario->username = $request->username;
        $usuario->email    = $request->email;
        $usuario->nombre   = $request->nombre;
        $usuario->rol      = $request->rol;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('personal.index')
                         ->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy($id)
    {
        Usuario::findOrFail($id)->delete();
        return redirect()->route('personal.index')
                         ->with('success', 'Usuario eliminado correctamente');
    }
}

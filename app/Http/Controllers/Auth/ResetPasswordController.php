<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Reset Password Controller
    |--------------------------------------------------------------------------
    |
    | Este controlador gestiona las solicitudes de restablecimiento de contraseñas
    | y usa un trait para incluir toda la lógica que Laravel provee por defecto.
    |
    */

    use ResetsPasswords;

    /**
     * Dónde redirigir tras un reset exitoso.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Crea una nueva instancia del controlador.
     * Solo invitados pueden acceder a este flujo.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Sobrescribe el método que Laravel invoca para aplicar el reset:
     * 1) Hashea la contraseña que el usuario envió.
     * 2) Limpia el remember_token para invalidar sesiones antiguas.
     * 3) Graba los cambios.
     * 4) Dispara el evento PasswordReset.
     * 5) Loguea al usuario.
     *
     * @param  \App\Models\Usuario  $user
     * @param  string               $password
     */
    protected function resetPassword(Usuario $user, $password)
    {
        // 1) Hash de la nueva contraseña
        $user->password = Hash::make($password);

        // 2) Limpiar cualquier remember_token previo
        $user->remember_token = null;

        // 3) Guardar cambios
        $user->save();

        // 4) Disparar evento de notificación
        event(new PasswordReset($user));

        // 5) Loguear al usuario en esta misma petición
        $this->guard()->login($user);
    }

    /**
     * (Opcional) Si quieres personalizar la vista del formulario de reset:
     *
     * public function showResetForm(Request $request, $token = null)
     * {
     *     return view('auth.passwords.reset')->with([
     *         'token' => $token,
     *         'email' => $request->email,
     *     ]);
     * }
     */
}

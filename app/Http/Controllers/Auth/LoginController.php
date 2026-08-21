<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /**
     * Z2-W0-9 — ein deaktiviertes Konto kommt gar nicht erst durch die Anmeldung.
     *
     * `AuthenticatesUsers` fragt hier, womit gesucht wird. Durch `disabled_at => null` findet der
     * Versuch den Nutzer nicht und endet als ungültige Zugangsdaten — dieselbe Antwort wie bei
     * falschem Kennwort, also **ohne** dem Anrufer zu verraten, dass es das Konto gibt.
     */
    protected function credentials(\Illuminate\Http\Request $request): array
    {
        $daten = [
            $this->username() => $request->get($this->username()),
            'password' => $request->get('password'),
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'disabled_at')) {
            $daten['disabled_at'] = null;
        }

        return $daten;
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

 
}

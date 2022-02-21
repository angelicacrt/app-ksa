<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        Redirect::setIntendedUrl(url()->previous());
        return view('auth.register');
    }

    public function createAdmin()
    {
        return view('auth.registeradmin');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {   
        // digits_between:6,7
        $request->validate([
            'name' => ['required', 'regex:/^[a-zA-Z\s-]*$/'],
            'no_induk_pegawai' => ['required', 'numeric', 'digits_between:6,7', 'unique:users'],
            // 'user_noTelp' => ['required', 'numeric','digits_between:8,12', 'unique:users'],
            'email' => ['required', 'string', 'email:rfc,dns', 'ends_with:ptksa.id', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'cabang' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'no_induk_pegawai' => $request->no_induk_pegawai,
            'user_noTelp' => $request->user_noTelp,
            'email' => $request->email,
            'cabang' => $request->cabang,
            'password' => Hash::make($request->password),
        ]);

        $user->attachRole($request->role_id);
        event(new Registered($user));

        // Preferences: auto login after register || change the code below
        Auth::login($user);
        return redirect(RouteServiceProvider::HOME);
    }
}

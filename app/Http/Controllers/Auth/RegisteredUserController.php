<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            // o form do Breeze usa "name"; mapeamos para a coluna legada "username"
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','email','max:255','unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        // valores padrão para colunas legadas NOT NULL
        $legacyDefaults = [
            'country'  => '',
            'gender'   => '',
            'birthday' => '',
            'about'    => '',
            'avatar'   => '',
        ];

        $user = User::create(array_merge($legacyDefaults, [
            'username'        => $request->input('name'),
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            // varchar no legado — mantenha um formato consistente
            'registered_date' => now()->format('Y-m-d H:i:s'),
        ]));

        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}

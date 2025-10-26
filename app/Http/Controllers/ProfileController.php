<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Mostrar formulário de edição (GET /profile).
     */
    public function edit(Request $request)
    {
        $user = Auth::user();

        // Reaproveita a view que você já tem
        return view('profile.edit', compact('user'));
    }

    /**
     * Atualizar dados do perfil (PATCH /profile).
     * - Atualiza nome e e-mail
     * - Altera senha se uma nova for informada (validando a atual)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validação base
        $data = $request->validate([
            'name'  => ['required','string','max:255'],
            'email' => [
                'required','email','max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            // Campos de senha são opcionais; só exigimos a atual se quiser trocar
            'current_password'      => ['nullable','string'],
            'password'              => ['nullable','string','min:8','confirmed'],
            'password_confirmation' => ['nullable','string'],
        ]);

        // Se quiser trocar a senha, exige a senha atual correta
        if ($request->filled('password')) {
            if (!$request->filled('current_password') || !Hash::check($request->input('current_password'), $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'A senha atual não confere.'])
                    ->withInput();
            }
            $user->password = Hash::make($request->input('password'));
        }

        // Atualiza nome e e-mail
        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->save();

        return redirect()->route('profile.show')->with('status', 'Perfil atualizado com sucesso!');
    }

    /**
     * Excluir conta (DELETE /profile).
     * Opcionalmente confirme a senha se quiser (aqui deixei simples).
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        Auth::logout();

        // Se quiser exigir senha aqui, valide antes:
        // $request->validate(['password' => 'required']);
        // if (!Hash::check($request->password, $user->password)) { ... }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Conta excluída.');
    }
}

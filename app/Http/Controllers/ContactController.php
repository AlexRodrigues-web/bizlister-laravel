<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactFormSubmitted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }

    public function submit(ContactRequest $request)
    {
        $data = $request->validated();

        Log::info('CONTACT_HTTP_START', [
            'nome'     => $data['nome'] ?? null,
            'email'    => $data['email'] ?? null,
            'assunto'  => $data['assunto'] ?? null,
            'length'   => isset($data['mensagem']) ? mb_strlen($data['mensagem']) : 0,
        ]);

        // ADMIN_EMAILS (vírgulas) -> fallback mail.from.address -> no-reply@localhost
        $adminEnv = trim((string) config('app.admin_emails', env('ADMIN_EMAILS', '')));
        $toList   = array_values(array_filter(array_map('trim', explode(',', $adminEnv))));
        if (empty($toList)) {
            $from   = trim((string) config('mail.from.address', ''));
            $toList = [$from !== '' ? $from : 'no-reply@localhost'];
        }

        try {
            Mail::to($toList)->send(
                (new ContactFormSubmitted($data))
                    ->replyTo($data['email'], $data['nome'] ?? null)
            );

            Log::info('CONTACT_HTTP_MAIL_OK', [
                'to'      => $toList,
                'assunto' => $data['assunto'] ?? null,
            ]);

            return back()->with('status', 'Mensagem enviada! Verifique o log (mailer=log).');
        } catch (\Throwable $e) {
            Log::error('CONTACT_HTTP_MAIL_ERROR', [
                'error'   => $e->getMessage(),
                'to'      => $toList,
                'assunto' => $data['assunto'] ?? null,
            ]);

            return back()
                ->withInput()
                ->withErrors(['mensagem' => 'Falha ao enviar a mensagem. Veja o laravel.log para detalhes.']);
        }
    }
}

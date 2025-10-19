<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        // Define Reply-To no próprio Mailable
        if (!empty($this->data['email'])) {
            $this->replyTo($this->data['email'], $this->data['nome'] ?? null);
        }

        return $this->subject('Contato: ' . ($this->data['assunto'] ?? ''))
            ->markdown('emails.contact.submitted')
            ->with($this->data);
    }
}

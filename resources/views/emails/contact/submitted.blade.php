@component('mail::message')
# Nova mensagem de contato

**Nome:** {{ $nome }}

**E-mail:** {{ $email }}

**Assunto:** {{ $assunto }}

**Mensagem:**

{{ $mensagem }}

@endcomponent

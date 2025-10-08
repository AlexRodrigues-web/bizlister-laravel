## Requisitos

- PHP 8.2+ com extensões: `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`.

- Composer 2.x

- Node.js 16+ e NPM 8+ (para build dos assets).

- MySQL/MariaDB local (porta 3306).




---




## Instalação & Configuração

1. **Clone**

```bash

git clone https://github.com/AlexRodrigues-web/bizlister-laravel.git

cd bizlister-laravel 

Composer



composer install

.env



copy .env.example .env php artisan key:generate

Ajuste o .env para seu banco local (ver seção Banco de Dados).

Links de storage (se necessário para uploads)



php artisan storage:link

Cache / otimizações (opcional em dev)



php artisan optimize:clear
Banco de Dados

Banco de desenvolvimento recomendado: flippy_bizlister_v2 (cópia do legado).

Charset/collation: utf8mb4 / utf8mb4_unicode_ci.

Exemplo de .env:



APP_URL=$BaseUrl APP_LOCALE=pt_BR APP_FALLBACK_LOCALE=pt_BR APP_TIMEZONE=America/Sao_Paulo DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=flippy_bizlister_v2 DB_USERNAME=root DB_PASSWORD=

Observação: Nesta fase a estrutura segue o schema legado para reduzir risco. Evoluções de modelagem (FKs, timestamps, normalização) serão feitas após estabilização do front público.

Build de Assets

Projeto utiliza Laravel Mix:



npm install npm run dev # desenvolvimento # npm run prod # build de produção
Rotas Principais

Base URL local: $BaseUrl

Públicas

GET /categorias — Listagem

GET /categoria/{id}-{slug?} — Detalhe

GET /cidades — Listagem

GET /cidade/{id}-{slug?} — Detalhe / Filtro

GET /negocio/{id}-{slug?} — Detalhe de negócio

GET /buscar — Busca (termo + categoria + cidade)

Autenticação (Breeze)

GET /login, POST /login

GET /register, POST /register

GET /forgot-password, POST /forgot-password (password.email)

GET /reset-password/{token}, POST /reset-password

Área autenticada

GET /profile — Perfil (visualizar/editar básico)

GET /negocio/novo — Formulário de criação

POST /negocio — Persistência do negócio
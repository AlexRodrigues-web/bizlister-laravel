# BizLister — Guia de Instalação / Installation Guide

> Documento preparado para entrega ao cliente — atualizado em 2025-10-26

## 📦 Pré-requisitos / Prerequisites
- **Windows + XAMPP** (Apache + MySQL/MariaDB)
- **PHP 8.2+** e **Composer 2.x**
- *(Opcional)* **Node.js/NPM** (se houver build de assets) / *(Optional)* Node.js/NPM (for asset build)

> **Nota/Note:** A pasta pública do Laravel é `public/`. Em ambientes com subpasta (ex.: `htdocs\TECINFOSP\bizlister-laravel\public`), mantenha `APP_URL` com o sufixo `/public`. Se usar VirtualHost apontando **direto** para `public/`, **não** inclua `/public` no `APP_URL`.

---

## 🖥️ Ambiente de exemplo (local) / Example local environment
- **APP_URL**: `http://localhost/TECINFOSP/bizlister-laravel/public`
- **DB_DATABASE**: `flippy_bizlister_v2`
- **DB_USERNAME**: `root`

---

## 🚀 Passo a passo / Step by step

1) **Clonar / Clone**  
   `C:\xampp\htdocs\TECINFOSP\bizlister-laravel`
   ```bash
   git clone <SEU_REPO_URL> C:\xampp\htdocs\TECINFOSP\bizlister-laravel
   ```

2) **Iniciar / Start** Apache e MySQL no XAMPP

3) **Criar o banco / Create database** (se ainda não existir / if not exists)
   ```sql
   CREATE DATABASE flippy_bizlister_v2
     CHARACTER SET utf8mb4
     COLLATE utf8mb4_unicode_ci;
   ```

4) **Criar `.env`** e configurar `APP_URL`/DB  
   ```bash
   copy .env.example .env
   # edite / edit .env:
   # APP_URL=http://localhost/TECINFOSP/bizlister-laravel/public
   # DB_DATABASE=flippy_bizlister_v2
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

5) **Instalar dependências** e **gerar a chave** / Install deps & app key
   ```bash
   composer install --no-interaction --prefer-dist
   php artisan key:generate
   ```

6) **Migrar** e **Seed** (páginas estáticas: Sobre, Termos, Privacidade)  
   ```bash
   php artisan migrate
   php artisan db:seed --class=Database\Seeders\PagesSeeder
   # ou / or
   # php artisan migrate --seed  (se o DatabaseSeeder já chama o PagesSeeder)
   ```

7) **Uploads (storage link)**  
   ```bash
   php artisan storage:link
   ```
   > **Windows:** pode exigir **modo de desenvolvedor** habilitado ou console **Administrador**.

8) **(Opcional) Build de assets / (Optional) Asset build**
   ```bash
   npm install
   # desenvolvimento / development
   npm run dev
   # produção / production
   npm run production
   ```

9) **Acessar / Access**
   - Home: `http://localhost/TECINFOSP/bizlister-laravel/public`
   - Páginas / Pages: `/sobre`, `/termos`, `/politica-de-privacidade`

### 🔁 Aliases/redirects úteis / Helpful aliases
- `/sobre-nos` → `/sobre`
- `/privacy` → `/politica-de-privacidade`
- `/about` e `/about-us` → `/sobre`
- `/terms` e `/terms-of-service` → `/termos`

---

## 🔐 Admin / Acesso
- Área / Area: `/admin` (middleware: `auth` + `is_admin`)

**Promover admin via SQL / Promote admin via SQL:**
```sql
-- Exemplo: se a tabela users tem is_admin (TINYINT)
UPDATE users SET is_admin = 1 WHERE email = 'seu@email.com';
```

**OU / OR: via `.env` (lista de emails admin)**
```env
ADMIN_EMAILS=admin@seu.dominio, outro-admin@dominio.com
```

---

## 🧪 Testes / Tests (básico)
```bash
php artisan test
```

---

## 🧰 Troubleshooting

- **404 nas páginas /sobre, /termos, /privacidade**  
  **PT:** Confirme que a tabela `pages` tem conteúdo. **EN:** Ensure `pages` table is populated.
  ```sql
  SELECT slug, LENGTH(content) FROM pages
  WHERE slug IN ('sobre','termos','politica-de-privacidade');
  ```
  Limpar caches / Clear caches:
  ```bash
  php artisan optimize:clear
  # ou / or
  php artisan view:clear && php artisan route:clear && php artisan config:clear
  ```

- **419 (CSRF) em POST**  
  **PT:** Garanta que o POST usa a mesma sessão/cookie do GET do formulário.  
  **EN:** Ensure POST reuses the same session/cookie as the form GET.

- **Uploads não aparecem / Uploads not visible**  
  Verifique o symlink e permissões / Check symlink & permissions:
  ```bash
  php artisan storage:link
  ```

---

## 🗺️ Rotas principais (resumo) / Key routes (summary)
- Público / Public: `/`, `/search` (ou / or `/buscar`), `/categorias`, `/cidades`, `/negocio/{id}-{slug?}`
- CMS Pages: `/{slug}` → `pages.show` (ex.: `sobre`, `termos`, `politica-de-privacidade`)
- Admin: `/admin/...` (middleware: `auth,is_admin`)

---

**Observação / Note:** Este guia foi escrito para a estrutura atual em `C:\xampp\htdocs\TECINFOSP\bizlister-laravel`. Se instalar em outro caminho, ajuste `APP_URL` no `.env`.  

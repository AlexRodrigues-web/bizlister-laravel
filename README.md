# BizLister – Laravel 8.4

**PT-BR — Resumo:**  
BizLister é um diretório de negócios migrado de um código legado em PHP 7 para **Laravel 8.4**. O projeto adota o padrão MVC do Laravel, com autenticação, painel admin e páginas estáticas (CMS) básicas.

**EN — Summary:**  
BizLister is a business directory migrated from a legacy PHP 7 codebase to **Laravel 8.4**. It follows Laravel’s MVC conventions and ships with authentication, an admin area, and basic static pages (CMS).

---

## ✨ Funcionalidades / Features
- **Autenticação / Authentication:** registro, login, reset de senha / registration, login, password reset
- **Painel Administrativo / Admin Panel:** `/admin` protegido por `auth` + `is_admin`
- **Listagens de Negócios / Business Listings:** CRUD completo
- **Categorias / Categories** e **Cidades / Cities**
- **Páginas estáticas / Static Pages (CMS):** `sobre`, `termos`, `politica-de-privacidade`
- **Aliases úteis / Helpful aliases:** `/sobre-nos → /sobre`, `/about(-us) → /sobre`, `/terms(-of-service) → /termos`, `/privacy(-policy) → /politica-de-privacidade`

---

## ⚙️ Requisitos / Requirements
- PHP **8.2+**, Composer 2.x
- MySQL/MariaDB
- (Opcional / Optional) Node.js & NPM para assets

> **Dica / Tip:** A pasta pública é `public/`. Se o servidor apontar direto para `public/`, `APP_URL` **não** deve incluir `/public`.

---

## 🚀 Instalação Rápida / Quickstart
```bash
composer install
cp .env.example .env   # Windows: copy .env.example .env
# edite / edit .env (APP_URL, DB_*)
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev        # ou / or npm run production
php artisan serve
```

Guia completo de instalação: consulte **README-INSTALL.md**.

---

## 🧪 Testes / Tests
```bash
php artisan test
```

---

## 🔐 Admin
- Rota: `/admin`
- Promover admin via SQL:
  ```sql
  UPDATE users SET is_admin = 1 WHERE email = 'admin@dominio.com';
  ```
- Ou via `.env`:
  ```env
  ADMIN_EMAILS=admin@dominio.com
  ```

---

## 🛠️ Scripts úteis / Useful scripts
```bash
# Desenvolvimento / Development
npm run dev

# Produção / Production
npm run production

# Limpar caches / Clear caches
php artisan optimize:clear
```

---

## 📄 Licença / License
Projeto interno do cliente. Se for abrir o código, escolha uma licença adequada (ex.: MIT).

---

## 📞 Suporte / Support
Abra uma issue ou contate a equipe responsável pelo projeto.

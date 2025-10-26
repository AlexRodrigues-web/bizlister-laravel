# BizLister — Guia de Instalação

> Documento gerado automaticamente em 2025-10-26 19:00:13

## Pré-requisitos
- Windows + XAMPP (Apache + MySQL/MariaDB)
- PHP / Composer
- (Opcional) Node.js/NPM (se houver build de assets)

### Detecção automática
- **APP_URL**: http://localhost/TECINFOSP/bizlister-laravel/public
- **DB_DATABASE**: flippy_bizlister_v2
- **DB_USERNAME**: root
- **Ferramentas**:
  - PHP 8.2.12 (cli) (built: Oct 24 2023 21:15:15) (ZTS Visual C++ 2019 x64)
  - Composer version 2.8.8 2025-04-04 16:56:46
  - Node: v22.13.0
  - npm: 10.9.2

## Passo a passo
1. **Clonar o repositório** no diretório do XAMPP (ex.: C:\xampp\htdocs\TECINFOSP\bizlister-laravel).
2. **Iniciar Apache e MySQL** pelo XAMPP.
3. **Criar o banco** (se necessário):
   `sql
   CREATE DATABASE flippy_bizlister_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   # APP_URL=http://localhost/TECINFOSP/bizlister-laravel/public
   # DB_DATABASE=flippy_bizlister_v2
   # DB_USERNAME=root
   # DB_PASSWORD=
   `"
Line 
6. **Migrar e semear**:
   `ash
   php artisan migrate
   php artisan db:seed --class=Database\\Seeders\\PagesSeeder
8. **(Opcional) Build de assets**:
   `ash
   npm install
   npm run build
`	ext
+----------------------------------------+---------------------------------------+--------------------------------+--------------+
| Method                                 | URI                                   | Name                           | Middleware   |
+----------------------------------------+---------------------------------------+--------------------------------+--------------+
| GET|HEAD                               | /                                     | welcome                        | web          |
| GET|HEAD                               | _debug/page/{slug}                    |                                | web          |
| GET|HEAD                               | _debug/pages                          |                                | web          |
| GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | about                                 |                                | web          |
| GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | about-us                              |                                | web          |
| GET|HEAD                               | admin                                 | admin.dashboard                | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/advertisements                  | admin.advertisements.edit      | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| PUT                                    | admin/advertisements                  | admin.advertisements.update    | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/business                        | admin.business.index           | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/businesses                      | admin.businesses.index         | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| POST                                   | admin/businesses                      | admin.businesses.store         | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/businesses/create               | admin.businesses.create        | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| PUT|PATCH                              | admin/businesses/{business}           | admin.businesses.update        | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| DELETE                                 | admin/businesses/{business}           | admin.businesses.destroy       | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/businesses/{business}/edit      | admin.businesses.edit          | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/categories                      | admin.categories.index         | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| POST                                   | admin/categories                      | admin.categories.store         | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/categories/create               | admin.categories.create        | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| PUT|PATCH                              | admin/categories/{category}           | admin.categories.update        | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| DELETE                                 | admin/categories/{category}           | admin.categories.destroy       | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/categories/{category}/edit      | admin.categories.edit          | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/cities                          | admin.cities.index             | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| POST                                   | admin/cities                          | admin.cities.store             | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/cities/create                   | admin.cities.create            | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| PUT|PATCH                              | admin/cities/{city}                   | admin.cities.update            | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| DELETE                                 | admin/cities/{city}                   | admin.cities.destroy           | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/cities/{city}/edit              | admin.cities.edit              | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/pages                           | admin.pages.index              | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| POST                                   | admin/pages                           | admin.pages.store              | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/pages/create                    | admin.pages.create             | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| PUT|PATCH                              | admin/pages/{page}                    | admin.pages.update             | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| DELETE                                 | admin/pages/{page}                    | admin.pages.destroy            | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/pages/{page}/edit               | admin.pages.edit               | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/ping                            | admin.ping                     | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/reviews                         | admin.reviews.index            | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| DELETE                                 | admin/reviews/{review}                | admin.reviews.destroy          | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| POST                                   | admin/reviews/{review}/approve        | admin.reviews.approve          | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| POST                                   | admin/reviews/{review}/hide           | admin.reviews.hide             | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | admin/settings                        | admin.settings.edit            | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| PUT                                    | admin/settings                        | admin.settings.update          | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | is_admin     |
| GET|HEAD                               | api/user                              |                                | api          |
|                                        |                                       |                                | auth:api     |
| GET|HEAD                               | auth/{provider}/callback              | social.callback                | web          |
| GET|HEAD                               | auth/{provider}/redirect              | social.redirect                | web          |
| GET|HEAD                               | bookmarks                             | bookmarks.index                | web          |
|                                        |                                       |                                | auth         |
| POST                                   | bookmarks                             | bookmarks.store                | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | bookmarks/{bookmark}                  | bookmarks.show                 | web          |
|                                        |                                       |                                | auth         |
| DELETE                                 | bookmarks/{bookmark}                  | bookmarks.destroy              | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | buscar                                | search.alias                   | web          |
| GET|HEAD                               | business-{id}                         |                                | web          |
| GET|HEAD                               | business-{id}-{slug?}                 |                                | web          |
| GET|HEAD                               | business-{id}-{slug?}.html            |                                | web          |
| GET|HEAD                               | categoria/{id}-{slug?}                | categories.show                | web          |
| GET|HEAD                               | categorias                            | categories.index               | web          |
| GET|HEAD                               | categories/{category}/subcategories   | categories.subcategories.index | web          |
| GET|HEAD                               | category-{id}-{slug?}                 |                                | web          |
| GET|HEAD                               | cidade/{id}-{slug?}                   | cities.show                    | web          |
| GET|HEAD                               | cidades                               | cities.index                   | web          |
| GET|HEAD                               | confirm-password                      | password.confirm               | web          |
|                                        |                                       |                                | auth         |
| POST                                   | confirm-password                      |                                | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | contato                               | contact.show                   | web          |
| POST                                   | contato                               | contact.send                   | web          |
| GET|HEAD                               | dashboard                             | dashboard                      | web          |
|                                        |                                       |                                | auth         |
| POST                                   | email/verification-notification       | verification.send              | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | throttle:6,1 |
| GET|HEAD                               | forgot-password                       | password.request               | web          |
|                                        |                                       |                                | guest        |
| POST                                   | forgot-password                       | password.email                 | web          |
|                                        |                                       |                                | guest        |
| GET|HEAD                               | hours/{hour}                          | hours.show                     | web          |
|                                        |                                       |                                | auth         |
| PUT|PATCH                              | hours/{hour}                          | hours.update                   | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | login                                 | login                          | web          |
|                                        |                                       |                                | guest        |
| POST                                   | login                                 |                                | web          |
|                                        |                                       |                                | guest        |
| POST                                   | logout                                | logout                         | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | meu-perfil                            |                                | web          |
| GET|HEAD                               | negocio                               |                                | web          |
| POST                                   | negocio                               | business.store                 | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | negocio/novo                          | business.create                | web          |
| POST                                   | negocio/{biz}/bookmark                | business.bookmark              | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | negocio/{biz}/galeria                 | business.gallery.index         | web          |
|                                        |                                       |                                | auth         |
| POST                                   | negocio/{biz}/galeria                 | business.gallery.store         | web          |
|                                        |                                       |                                | auth         |
| POST                                   | negocio/{biz}/galeria/reorder         | business.gallery.reorder       | web          |
|                                        |                                       |                                | auth         |
| DELETE                                 | negocio/{biz}/galeria/{image}         | business.gallery.destroy       | web          |
|                                        |                                       |                                | auth         |
| POST                                   | negocio/{biz}/galeria/{image}/primary | business.gallery.primary       | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | negocio/{id}-{slug?}                  | business.show                  | web          |
| POST                                   | negocio/{id}/reviews                  | reviews.store                  | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | perfil                                | profile.show                   | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | privacidade                           |                                | web          |
| GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | privacy                               |                                | web          |
| GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | privacy-policy                        |                                | web          |
| GET|HEAD                               | profile                               | profile.edit                   | web          |
|                                        |                                       |                                | auth         |
| PATCH                                  | profile                               | profile.update                 | web          |
|                                        |                                       |                                | auth         |
| DELETE                                 | profile                               | profile.destroy                | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | register                              | register                       | web          |
|                                        |                                       |                                | guest        |
| POST                                   | register                              |                                | web          |
|                                        |                                       |                                | guest        |
| POST                                   | reset-password                        | password.update                | web          |
|                                        |                                       |                                | guest        |
| GET|HEAD                               | reset-password/{token}                | password.reset                 | web          |
|                                        |                                       |                                | guest        |
| GET|HEAD                               | search                                | search.index                   | web          |
| GET|HEAD                               | sitemap.xml                           | sitemap                        | web          |
| GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | sobre-nos                             |                                | web          |
| GET|HEAD                               | subcategories/{subcategory}           | subcategories.show             | web          |
| GET|HEAD                               | subcategory-{id}-{slug?}              |                                | web          |
| GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | terms                                 |                                | web          |
| GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | terms-of-service                      |                                | web          |
| GET|HEAD                               | verify-email                          | verification.notice            | web          |
|                                        |                                       |                                | auth         |
| GET|HEAD                               | verify-email/{id}/{hash}              | verification.verify            | web          |
|                                        |                                       |                                | auth         |
|                                        |                                       |                                | signed       |
|                                        |                                       |                                | throttle:6,1 |
| GET|HEAD                               | write_a_review-{id}                   |                                | web          |
| GET|HEAD                               | {fallbackPlaceholder}                 |                                | web          |
| GET|HEAD                               | {slug}                                | pages.show                     | web          |
+----------------------------------------+---------------------------------------+--------------------------------+--------------+
`"
Line "
Line 
- **404 nas páginas**: confirme que a tabela pages tem conteúdo e limpe caches:
  `ash
  php artisan view:clear && php artisan route:clear && php artisan config:clear

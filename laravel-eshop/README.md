# E-Shop Tenisiek - Laravel MVC

## Prehľad projektu

Tento projekt je e-shop na predaj tenisiek vytvorený v **Laravel** frameworku s dodržaním **MVC architektúry** a **OOP princípov**.

## Splnené požiadavky

### ✅ MVC Architektúra
- **Models** (`app/Models/`): Product, ProductVariant, ProductImage, Inventory, Order, OrderItem, Payment
- **Views** (`resources/views/`): Blade templates organizované podľa funkcionality
- **Controllers** (`app/Http/Controllers/`): ProductController, OrderController, AdminProductController, AuthController

### ✅ OOP Princípy
- Triedy s jasne definovanými zodpovednosťami
- Relácie medzi modelmi (hasMany, belongsTo)
- Scopes pre filtrovanie (active, search, priceRange)
- Accessor atribúty (getMainImageAttribute, getAvailableSizesAttribute)

### ✅ 10+ Vlastných CSS Pravidiel
Súbor `public/css/app.css` obsahuje 18+ vlastných CSS pravidiel:
1. Produktová karta s hover efektom
2. Obrázok produktu so zoom efektom
3. Cena a zľava štýl
4. Košík drawer (slide-in panel)
5. Položka košíka
6. Vlastné tlačidlá
7. Výber variantu (farba/veľkosť)
8. Responzívny dizajn (media queries)
9. Hero sekcia
10. Filtre
11. Formuláre
12. Admin panel
13. Tabuľky v admin
14. Animácie (fadeIn, slideIn)
15. Toast notifikácie
16. Galéria produktu
17. Loading state
18. Farebné premenné (:root)

### ✅ CRUD Operácie
- **Create**: Vytváranie produktov, variantov, objednávok
- **Read**: Zobrazenie produktov, detailov, košíka
- **Update**: Úprava produktov, skladu, variantov
- **Delete**: Mazanie produktov, variantov, obrázkov

### ✅ Validácia formulárov
- **Klientská strana** (JavaScript): Validácia pred odoslaním
- **Serverová strana** (PHP/Laravel): ProductRequest, validácia v controlleroch

### ✅ Responzívny dizajn
- Bootstrap 5 grid systém
- Media queries pre rôzne zariadenia
- Mobilný navbar

### ✅ Netriviálny JavaScript
- Modul košíka s localStorage persistenciou
- Dynamické pridávanie/odoberanie položiek
- Filtrovanie a zoraďovanie produktov
- Toast notifikácie
- Výber variantov produktu

## Štruktúra projektu

```
laravel-eshop/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminProductController.php  # CRUD produktov
│   │   │   │   └── AuthController.php          # Autentifikácia
│   │   │   ├── ProductController.php           # Produkty frontend
│   │   │   └── OrderController.php             # Objednávky
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php             # Ochrana admin routes
│   │   └── Requests/
│   │       └── ProductRequest.php              # Validácia produktu
│   └── Models/
│       ├── Product.php
│       ├── ProductVariant.php
│       ├── ProductImage.php
│       ├── Inventory.php
│       ├── Order.php
│       ├── OrderItem.php
│       └── Payment.php
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php                       # Hlavný layout
│   │   └── admin.blade.php                     # Admin layout
│   ├── products/
│   │   ├── index.blade.php                     # Katalóg produktov
│   │   └── show.blade.php                      # Detail produktu
│   ├── admin/
│   │   ├── login.blade.php                     # Admin login
│   │   └── products/
│   │       ├── index.blade.php                 # Zoznam produktov
│   │       ├── create.blade.php                # Nový produkt
│   │       ├── edit.blade.php                  # Úprava produktu
│   │       └── _form.blade.php                 # Zdieľaný formulár
│   ├── checkout.blade.php                      # Pokladňa
│   ├── order-success.blade.php                 # Potvrdenie objednávky
│   └── login-choice.blade.php                  # Výber prihlásenia
├── public/
│   ├── css/app.css                             # Vlastné CSS (18+ pravidiel)
│   └── js/
│       ├── app.js                              # Hlavný JS
│       └── cart.js                             # Modul košíka
├── routes/
│   ├── web.php                                 # Web routes
│   └── api.php                                 # API routes
└── database/
    ├── migrations/                             # Databázové migrácie
    └── seeders/
        └── ProductSeeder.php                   # Testovacie dáta
```

## Spustenie projektu

### Prerekvizity
- Docker Desktop

### Spustenie

```powershell
cd "C:\Users\rchle\OneDrive\Počítač\vai experiment"
docker-compose -f docker-compose-laravel.yml up -d --build
```

Alebo použite pripravený skript:
```powershell
.\run_laravel.ps1 -OpenBrowser
```

### Prístup
- **Webová stránka**: http://localhost:8000
- **Admin panel**: http://localhost:8000/admin/login
- **phpMyAdmin**: http://localhost:8081

### Admin prihlásenie
- **Heslo**: admin123 (ľubovoľné meno)

## Zastavenie

```powershell
docker-compose -f docker-compose-laravel.yml down
```

## Technológie
- **Backend**: PHP 8.4, Laravel 12
- **Frontend**: HTML5, CSS3, JavaScript ES6, Bootstrap 5
- **Databáza**: MySQL 8.0
- **Kontainerizácia**: Docker


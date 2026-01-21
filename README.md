# E-Shop Tenisiek - Semestrálna práca

Webová aplikácia e-shopu s teniskami vyvinutá v **Laravel MVC** frameworku v rámci semestrálnej práce.

---

## 🚀 Funkcie a Implementácia

### 🔐 1. Registrácia a Autentifikácia
- **Role:** Admin a Customer (Zákazník).
- **Funkcie:** Registrácia, Login, Logout, Správa profilu (zmena údajov a hesla).
- **Implementácia:** `UserAuthController`, `RoleMiddleware`, vlastná migrácia pre rolu používateľa.
- **Bezpečnosť:** Hashovanie hesiel, ochrana routes pomocou middleware.

### ⚡ 2. AJAX Funkcionalita
- **Live Search Filtrovanie:** Produkty sa filtrujú okamžite pri písaní alebo zmene parametrov (cena, veľkosť, farba) bez reloadu stránky.
  - *Súbor:* `public/js/product-filter.js` (Debouncing, Fetch API).
- **In-place Editing:** V admin paneli je možné meniť skladové zásoby priamo v tabuľke. Zmena sa uloží automaticky.
  - *Súbor:* `public/js/admin.js`.

### 🖼️ 3. Správa Obrázkov (Drag & Drop)
- **Upload:** Moderný drag & drop interface pre nahrávanie viacerých obrázkov naraz.
- **Validácia:** Kontrola typu (JPG, PNG, WebP) a veľkosti (max 5MB) na strane klienta aj servera.
- **Manažment:** AJAX mazanie obrázkov a nastavenie hlavného obrázka produktu.
- *Backend:* `AdminProductController` s novými API endpointmi.

### 📂 4. Kategórie a Varianty
- **Kategórie:** Plný CRUD pre správu kategórií. Produkty sú radené do kategórií (Tenisky, Doplnky...).
- **Varianty:** Každý produkt má varianty (kombinácia farba + veľkosť) s vlastným sledovaním skladu.
- **Dátový model:** Vzťahy 1:N (Category -> Products) a M:N (Orders -> Products).

---

## 🛠️ Technológie

- **Backend:** Laravel 11 (PHP 8.4)
- **Frontend:** Blade, Bootstrap 5, Tailwind CSS, Vanilla JS
- **Databáza:** MySQL 8.0
- **Prostredie:** Docker (Sail)

---

## 📦 Inštalácia a Spustenie

### Požiadavky
- Docker Desktop

### Postup
1. Spustite Docker Desktop.
2. Spustite aplikáciu pomocou skriptu:
   ```powershell
   .\start.bat
   ```
3. Počkajte na naštartovanie kontajnerov.
4. **Naplnenie databázy dátami (Prvé spustenie):**
   ```bash
   docker compose exec web php artisan migrate:fresh --seed
   ```
   *(Pozor: Toto vyresetuje databázu a naplní ju testovacími dátami)*

5. Otvorte prehliadač: [http://localhost:8000](http://localhost:8000)

### Prístupové údaje
| Rola | Email | Heslo |
|------|-------|-------|
| **Admin** | admin@eshop.sk | admin123 |
| **Zákazník** | zakaznik@example.sk | password123 |
| **Databáza** (phpMyAdmin) | [http://localhost:8081](http://localhost:8081) | root / example |

---

## 📂 Štruktúra Projektu

```
laravel-eshop/
├── app/
│   ├── Http/Controllers/       # Aplikačná logika (Admin, Auth, Shop)
│   ├── Models/                 # Eloquent Modely (Product, Order, User...)
│   └── Middleware/             # Ochrana prístupu (AdminMiddleware)
├── resources/views/            # Blade šablóny
│   ├── admin/                  # Admin panel
│   ├── products/               # Frontend obchodu
│   └── layouts/                # Hlavné layouty
├── public/
│   ├── js/                     # Vlastný JavaScript (filtre, košík, admin)
│   └── css/                    # Vlastné CSS štýly
├── database/                   # Migrácie a Seedery
└── routes/                     # Definovanie ciest (web.php, api.php)
```

## ✅ Splnené Požiadavky Semestrálnej Práce

- **Git:** Projekt je verziovaný.
- **Databáza:** Min. 3 entity (Products, Variants, Categories, Users, Orders), väzby 1:N a M:N.
- **Validácia:** Server-side (Laravel Request) aj Client-side (JS).
- **Bezpečnosť:** Auth, CSRF, XSS ochrana, SQL Injection (ORM).
- **AJAX:** Filtrovanie produktov, editácia skladu, košík.
- **Frontend:** Responzívny dizajn, vlastné CSS (>20 pravidiel), vlastný JS (>50 riadkov).
- **MVC:** Striktné oddelenie logiky.

---
*Autor: Richard Chlebak*

# E-Shop Tenisiek - Vývojová dokumentácia

Tento súbor dokumentuje postup vývoja semestrálnej práce krok za krokom.

---

## 📌 Čo už je hotové

### ✅ 1. Registrácia a prihlásenie používateľov (16.1.2025)

**Čo to robí:**
- Zákazníci sa môžu zaregistrovať na stránke
- Zákazníci sa môžu prihlásiť a odhlásiť
- Existujú 2 role: **admin** a **customer** (zákazník)
- Používateľ vidí v navigácii buď "Prihlásiť sa" alebo svoje meno s dropdownom

**Ako som to spravil:**

1. **Migrácia databázy** (`database/migrations/2024_01_02_000001_add_role_to_users_table.php`)
   - Pridal som do tabuľky `users` nové stĺpce:
     - `role` - či je to admin alebo zákazník
     - `phone`, `address`, `city`, `zip` - kontaktné údaje

2. **Model User** (`app/Models/User.php`)
   - Pridal som metódy `isAdmin()` a `isCustomer()` na kontrolu role
   - Rozšíril som zoznam polí ktoré môžu byť vyplnené

3. **Controller pre autentifikáciu** (`app/Http/Controllers/UserAuthController.php`)
   - `showRegisterForm()` - zobrazí registračný formulár
   - `register()` - spracuje registráciu, uloží používateľa do DB
   - `showLoginForm()` - zobrazí prihlasovací formulár
   - `login()` - overí heslo a prihlási používateľa
   - `logout()` - odhlási používateľa
   - `profile()` - zobrazí profil
   - `updateProfile()` - uloží zmeny v profile
   - `updatePassword()` - zmení heslo

4. **Middleware** (`app/Http/Middleware/RoleMiddleware.php`)
   - Kontroluje či má používateľ správnu rolu pre prístup na stránku
   - Ak nie je prihlásený alebo nemá správnu rolu, presmeruje ho

5. **Blade šablóny** (views)
   - `resources/views/auth/register.blade.php` - registračný formulár
   - `resources/views/auth/login.blade.php` - prihlasovací formulár
   - `resources/views/auth/profile.blade.php` - stránka profilu

6. **Routes** (`routes/web.php`)
   - `/register` - registrácia (len pre neprihlásených)
   - `/login` - prihlásenie (len pre neprihlásených)
   - `/logout` - odhlásenie (len pre prihlásených)
   - `/profile` - profil (len pre prihlásených)

7. **Navigácia** (`resources/views/layouts/app.blade.php`)
   - Pridaný dropdown menu pre prihláseného používateľa
   - Zobrazuje "Môj profil", "Admin panel" (ak je admin), "Odhlásiť sa"

**Testovacie účty:**
- Admin: `admin@eshop.sk` / `admin123`
- Zákazník: `zakaznik@example.sk` / `password123`

---

### ✅ 2. AJAX funkcionalita (16.1.2025)

**Čo to robí:**
- Produkty sa filtrujú bez obnovenia stránky (live search)
- V admin paneli sa dá meniť sklad priamo v tabuľke bez klikania na tlačidlo

**Ako som to spravil:**

1. **AJAX Filtrovanie produktov** (`public/js/product-filter.js`)
   - Keď píšeš do vyhľadávania, produkty sa automaticky filtrujú
   - Funguje aj filter ceny, veľkosti a farby
   - Používa API endpoint `/api/products` ktorý vracia JSON
   - Debounce - čaká 300ms po prestání písania, aby neposlal príliš veľa requestov

2. **In-place editing skladu v admin** (`public/js/admin.js`)
   - V editácii produktu sú inputy pre sklad
   - Keď zmeníš číslo a opustíš pole, automaticky sa uloží cez AJAX
   - Zobrazí sa zelená notifikácia "Sklad aktualizovaný"
   - API endpoint: `PUT /api/admin/variants/{id}/stock`

3. **API Endpoints** (`routes/api.php`)
   - `GET /api/products` - vráti produkty vo formáte JSON
   - `PUT /api/admin/variants/{id}/stock` - aktualizuje sklad

4. **Controller** (`app/Http/Controllers/Admin/AdminProductController.php`)
   - Pridaná metóda `apiUpdateStock()` pre AJAX update skladu

---

### ✅ 3. Drag & Drop Upload obrázkov (19.1.2025)

**Čo to robí:**
- Moderný drag & drop interface pre nahrávanie obrázkov
- Validácia typu súboru (JPG, PNG, GIF, WebP) a veľkosti (max 5MB)
- Náhľad obrázkov pred uploadom
- AJAX mazanie a nastavenie hlavného obrázka

**Ako som to spravil:**

1. **JavaScript modul** (`public/js/admin.js`)
   - `ImageUploadModule` s drag & drop logikou
   - Klientská validácia súborov pred uploadom
   - Náhľady vybraných obrázkov s možnosťou odstránenia

2. **Formulár produktu** (`resources/views/admin/products/_form.blade.php`)
   - Drag & drop zóna s vizuálnym feedbackom
   - CSS štýly pre interaktívny interface

3. **Controller** (`app/Http/Controllers/Admin/AdminProductController.php`)
   - Vylepšená metóda `handleImages()` s validáciou
   - Nové API endpointy `apiDeleteImage()` a `apiSetMainImage()`

4. **API Routes** (`routes/api.php`)
   - `DELETE /api/admin/images/{id}` - AJAX mazanie
   - `POST /api/admin/images/{id}/main` - nastavenie hlavného obrázka

---

### ✅ 4. Kategórie produktov (19.1.2025)

**Čo to robí:**
- CRUD operácie pre kategórie v admin paneli
- Produkty môžu byť zaradené do kategórií
- Navigácia v admin paneli obsahuje odkaz na kategórie

**Ako som to spravil:**

1. **Migrácia** (`database/migrations/2024_01_03_000001_create_categories_table.php`)
   - Nová tabuľka `categories` (id, name, slug, description, is_active, sort_order)
   - Pridanie `category_id` do tabuľky `products`

2. **Model Category** (`app/Models/Category.php`)
   - Automatické generovanie slug z názvu
   - Scope `active()` pre aktívne kategórie
   - Relácia `products()` k produktom

3. **Model Product** (`app/Models/Product.php`)
   - Pridaná relácia `category()` (belongsTo)
   - Pridané `category_id` do fillable

4. **Controller** (`app/Http/Controllers/Admin/AdminCategoryController.php`)
   - Kompletný CRUD pre kategórie
   - Ochrana pred mazaním kategórií s produktmi

5. **Views** (`resources/views/admin/categories/`)
   - `index.blade.php` - zoznam s počtom produktov
   - `create.blade.php` a `edit.blade.php` - formuláre
   - `_form.blade.php` - zdieľaný formulár

6. **Routes** (`routes/web.php`)
   - CRUD routes: index, create, store, edit, update, destroy

---

## 📋 Čo ešte treba spraviť

### Fáza 5: Finalizácia
- [x] Kontrola bezpečnosti
- [x] README s inštalačnými krokmi
- [x] Dokumentácia fáz 3 a 4
- [ ] Manuálne testovanie

---

## 🗂️ Štruktúra dôležitých súborov

```
laravel-eshop/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── UserAuthController.php      ← prihlásenie zákazníkov
│   │   │   ├── ProductController.php       ← zobrazenie produktov
│   │   │   └── Admin/
│   │   │       ├── AdminProductController.php  ← CRUD produktov
│   │   │       └── AdminCategoryController.php ← CRUD kategórií
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php         ← ochrana admin stránok
│   │       └── RoleMiddleware.php          ← kontrola rolí
│   └── Models/
│       ├── User.php                        ← model používateľa
│       ├── Product.php                     ← model produktu
│       └── Category.php                    ← model kategórie
├── database/
│   └── migrations/                         ← zmeny v databáze
├── public/
│   └── js/
│       ├── app.js                          ← hlavný JS
│       ├── cart.js                         ← košík
│       ├── product-filter.js               ← AJAX filtrovanie
│       └── admin.js                        ← AJAX v admin paneli + drag&drop upload
├── resources/views/
│   ├── auth/                               ← prihlásenie/registrácia
│   ├── products/                           ← produkty frontend
│   ├── admin/
│   │   ├── products/                       ← správa produktov
│   │   └── categories/                     ← správa kategórií
│   └── layouts/                            ← šablóny stránok
└── routes/
    ├── web.php                             ← URL adresy
    └── api.php                             ← API endpointy
```

---

## 🔐 Bezpečnosť

- **Hash hesiel** - heslá sa ukladajú zašifrované (bcrypt)
- **CSRF ochrana** - všetky formuláre majú `@csrf` token
- **Validácia** - vstupy sa kontrolujú na serveri aj klientovi
- **SQL Injection** - Eloquent ORM automaticky escapuje
- **File Upload** - validácia typu a veľkosti súborov (max 5MB)
- **XSS ochrana** - Blade automaticky escapuje výstup

---

## 📝 Git commity

1. `registracia a login` - Fáza 1
2. `AJAX` - Fáza 2
3. `drag-drop-upload` - Fáza 3
4. `kategorie` - Fáza 4

---

*Posledná aktualizácia: 19.1.2025*

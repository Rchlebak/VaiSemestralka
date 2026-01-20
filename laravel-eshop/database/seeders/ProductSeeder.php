<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Inventory;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catTenisky = Category::where('slug', 'tenisky')->first()->category_id ?? null;
        $catDoplnky = Category::where('slug', 'doplnky')->first()->category_id ?? null;
        $catBeh = Category::where('slug', 'beh')->first()->category_id ?? null;
        $catLifestyle = Category::where('slug', 'lifestyle')->first()->category_id ?? null;

        $products = [
            // === NIKE AIR FORCE 1 (3 unique AF1 images) ===
            [
                'name' => 'Nike Air Force 1 Low White',
                'brand' => 'Nike',
                'sku_model' => 'NK-AF1-001',
                'base_price' => 129.99,
                'gender' => 'unisex',
                'description' => 'Legendárne Nike Air Force 1 v čistej bielej farbe. Prémiová koža a ikonická silueta.',
                'category_id' => $catTenisky,
                'colors' => ['white'],
                'sizes' => ['39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600',
                    'https://images.unsplash.com/photo-1600269452121-4f2416e55c28?w=600',
                    'https://images.unsplash.com/photo-1604068599427-024c0d0c3eb1?w=600',
                ]
            ],
            [
                'name' => 'Nike Air Force 1 Low Black',
                'brand' => 'Nike',
                'sku_model' => 'NK-AF1-002',
                'base_price' => 129.99,
                'gender' => 'unisex',
                'description' => 'Klasické Air Force 1 v elegantnej čiernej farbe.',
                'category_id' => $catTenisky,
                'colors' => ['black'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1588117305388-c2631a279f82?w=600',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                    'https://images.unsplash.com/photo-1579338559194-a162d19bf842?w=600',
                ]
            ],
            // === NIKE DUNK ===
            [
                'name' => 'Nike Dunk Low Panda',
                'brand' => 'Nike',
                'sku_model' => 'NK-DNK-003',
                'base_price' => 119.99,
                'gender' => 'unisex',
                'description' => 'Nike Dunk Low v obľúbenej čierno-bielej kombinácii Panda.',
                'category_id' => $catTenisky,
                'colors' => ['panda'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1612902456551-333ac5afa26e?w=600',
                    'https://images.unsplash.com/photo-1597045566677-8cf032ed6634?w=600',
                    'https://images.unsplash.com/photo-1584735175315-9d5df23860e6?w=600',
                ]
            ],
            // === AIR JORDAN 1 ===
            [
                'name' => 'Air Jordan 1 Retro High Chicago',
                'brand' => 'Jordan',
                'sku_model' => 'JD-AJ1-004',
                'base_price' => 189.99,
                'gender' => 'men',
                'description' => 'Ikonické Air Jordan 1 v legendárnej Chicago farebnosti.',
                'category_id' => $catTenisky,
                'colors' => ['chicago'],
                'sizes' => ['40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600',
                    'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600',
                    'https://images.unsplash.com/photo-1546868871-0f936769675e?w=600',
                ]
            ],
            [
                'name' => 'Air Jordan 1 Low',
                'brand' => 'Jordan',
                'sku_model' => 'JD-AJ1L-005',
                'base_price' => 139.99,
                'gender' => 'unisex',
                'description' => 'Nízka verzia legendárnych Jordan 1.',
                'category_id' => $catTenisky,
                'colors' => ['white-black'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600',
                    'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=600',
                    'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=600',
                ]
            ],
            // === ADIDAS SAMBA ===
            [
                'name' => 'Adidas Samba OG White',
                'brand' => 'Adidas',
                'sku_model' => 'AD-SMB-006',
                'base_price' => 119.99,
                'gender' => 'unisex',
                'description' => 'Klasické Adidas Samba v bielej farbe s čiernymi prúžkami.',
                'category_id' => $catTenisky,
                'colors' => ['white'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1608379743498-63e2fa966943?w=600',
                    'https://images.unsplash.com/photo-1605733160314-4fc7dac4bb16?w=600',
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600',
                ]
            ],
            [
                'name' => 'Adidas Samba OG Black',
                'brand' => 'Adidas',
                'sku_model' => 'AD-SMB-007',
                'base_price' => 119.99,
                'gender' => 'unisex',
                'description' => 'Adidas Samba v čiernej koži s bielymi prúžkami.',
                'category_id' => $catTenisky,
                'colors' => ['black'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1520256862855-398228c41684?w=600',
                    'https://images.unsplash.com/photo-1543508282-6319a3e2621f?w=600',
                    'https://images.unsplash.com/photo-1580906853149-2b92a8d680e0?w=600',
                ]
            ],
            // === ADIDAS STAN SMITH ===
            [
                'name' => 'Adidas Stan Smith',
                'brand' => 'Adidas',
                'sku_model' => 'AD-STS-008',
                'base_price' => 99.99,
                'gender' => 'unisex',
                'description' => 'Minimalistické Adidas Stan Smith so zelenými detailmi.',
                'category_id' => $catLifestyle,
                'colors' => ['white-green'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1603787081207-362bcef7c144?w=600',
                    'https://images.unsplash.com/photo-1581017316471-1f6ef7ce6fd3?w=600',
                    'https://images.unsplash.com/photo-1609585903816-a3a39a8a7a4d?w=600',
                ]
            ],
            // === ADIDAS SUPERSTAR ===
            [
                'name' => 'Adidas Superstar',
                'brand' => 'Adidas',
                'sku_model' => 'AD-SUP-009',
                'base_price' => 109.99,
                'gender' => 'unisex',
                'description' => 'Legendárne Adidas Superstar s ikonickou gumovou špičkou.',
                'category_id' => $catTenisky,
                'colors' => ['white-black'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1575537302964-96cd47c06b1b?w=600',
                    'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?w=600',
                    'https://images.unsplash.com/photo-1595341888016-a392ef81b7de?w=600',
                ]
            ],
            // === NIKE AIR FORCE 1 LOW WHITE (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'Nike Air Force 1 Low Triple White',
                'brand' => 'Nike',
                'sku_model' => 'NK-AF1W-066',
                'base_price' => 119.99,
                'gender' => 'unisex',
                'description' => 'Nike Air Force 1 Low v celobielom prevedení Triple White. Kožený zvršok, Air jednotka v päte, non-marking gumová podrážka. Od roku 1982 najpredávanejšia biela silueta na svete.',
                'category_id' => $catTenisky,
                'colors' => ['white'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&fit=crop&crop=left',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600&fit=crop&crop=right',
                ]
            ],
            // === NIKE DUNK LOW PANDA (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'Nike Dunk Low Panda',
                'brand' => 'Nike',
                'sku_model' => 'NK-DLPND-067',
                'base_price' => 129.99,
                'gender' => 'unisex',
                'description' => 'Nike Dunk Low Panda v kultovej čierno-bielej farebnosti. Kožený zvršok s perforovanou špičkou, pena v medzipodrážke, gumová podrážka. Najobľúbenejší Dunk colorway všetkých čias.',
                'category_id' => $catTenisky,
                'colors' => ['black-white'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1612902456551-333ac5afa26e?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1612902456551-333ac5afa26e?w=600&fit=crop&crop=top',
                    'https://images.unsplash.com/photo-1612902456551-333ac5afa26e?w=600&fit=crop&crop=bottom',
                ]
            ],
            // === NEW BALANCE 550 WHITE GREEN (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'New Balance 550 White Green',
                'brand' => 'New Balance',
                'sku_model' => 'NB-550WG-068',
                'base_price' => 139.99,
                'gender' => 'unisex',
                'description' => 'New Balance 550 v retro basketbalovom štýle. Kožený zvršok, perforácie na špičke, EVA medzipodrážka, gumová podrážka. Silhouette z 80. rokov znovu populárna vďaka Aimé Leon Dore.',
                'category_id' => $catTenisky,
                'colors' => ['white-green'],
                'sizes' => ['39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600&fit=crop&crop=left',
                    'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600&fit=crop&crop=right',
                ]
            ],
            // === ADIDAS SAMBA OG (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'Adidas Samba OG',
                'brand' => 'Adidas',
                'sku_model' => 'AD-SAMBA-069',
                'base_price' => 109.99,
                'gender' => 'unisex',
                'description' => 'Adidas Samba OG - legenda od roku 1950 pôvodne pre futbal na zamrznutom povrchu. Kožený zvršok, semišová špička, gumová podrážka. Najpredávanejšia Adidas silueta roku 2024.',
                'category_id' => $catTenisky,
                'colors' => ['white-black'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1608379743498-63e2fa966943?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1608379743498-63e2fa966943?w=600&fit=crop&crop=top',
                    'https://images.unsplash.com/photo-1608379743498-63e2fa966943?w=600&fit=crop&crop=bottom',
                ]
            ],
            // === CONVERSE CHUCK 70 HIGH (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'Converse Chuck 70 High',
                'brand' => 'Converse',
                'sku_model' => 'CV-C70H-070',
                'base_price' => 89.99,
                'gender' => 'unisex',
                'description' => 'Converse Chuck 70 High - prémiová verzia klasického All Star. Hrubšie plátno, vintage logo, OrthoLite vložka pre pohodlie. Vulkanizovaná gumová podrážka s ikonickou hviezdou.',
                'category_id' => $catTenisky,
                'colors' => ['black'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600&fit=crop&crop=left',
                    'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600&fit=crop&crop=right',
                ]
            ],
            // === VANS OLD SKOOL (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'Vans Old Skool Classic',
                'brand' => 'Vans',
                'sku_model' => 'VN-OSC-071',
                'base_price' => 79.99,
                'gender' => 'unisex',
                'description' => 'Vans Old Skool s ikonickým Jazz Stripe od roku 1977. Kombinácia semišu a plátna, vulkanizovaná waffle podrážka. Prvá Vans topánka s bočným prúžkom.',
                'category_id' => $catTenisky,
                'colors' => ['black-white'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600&fit=crop&crop=top',
                    'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600&fit=crop&crop=bottom',
                ]
            ],
            // === REEBOK CLASSIC LEATHER (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'Reebok Classic Leather',
                'brand' => 'Reebok',
                'sku_model' => 'RB-CL-072',
                'base_price' => 89.99,
                'gender' => 'unisex',
                'description' => 'Reebok Classic Leather z roku 1983 - čistý kožený bežecký dizajn. Mäkká kožená horná časť, EVA medzipodrážka, Hi-Abrasion gumová podrážka. Nadčasová retro klasika.',
                'category_id' => $catTenisky,
                'colors' => ['white'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&fit=crop&crop=left',
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600&fit=crop&crop=right',
                ]
            ],
            // === PUMA SUEDE CLASSIC (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'Puma Suede Classic XXI',
                'brand' => 'Puma',
                'sku_model' => 'PM-SC21-073',
                'base_price' => 79.99,
                'gender' => 'unisex',
                'description' => 'Puma Suede Classic XXI - ikona od roku 1968. Prémiový semišový zvršok, Formstrip na bokoch, gumová podrážka. Symbol hip-hop a breakdance kultúry.',
                'category_id' => $catTenisky,
                'colors' => ['black-white'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&fit=crop&crop=top',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&fit=crop&crop=bottom',
                ]
            ],
            // === ASICS GEL-1130 (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'ASICS Gel-1130',
                'brand' => 'ASICS',
                'sku_model' => 'AS-G1130-074',
                'base_price' => 129.99,
                'gender' => 'unisex',
                'description' => 'ASICS Gel-1130 - retro bežecká silueta z roku 2009 znovu v móde. GEL technológia v päte a špičke, mesh a koža, Trusstic stabilizátor. Archívny kúsok pre blokecore štýl.',
                'category_id' => $catTenisky,
                'colors' => ['white-silver'],
                'sizes' => ['39', '40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600&fit=crop&crop=left',
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600&fit=crop&crop=right',
                ]
            ],
            // === NIKE CORTEZ (TENISKY) - KONZISTENTNÉ OBRÁZKY ===
            [
                'name' => 'Nike Cortez',
                'brand' => 'Nike',
                'sku_model' => 'NK-CRTEZ-075',
                'base_price' => 99.99,
                'gender' => 'unisex',
                'description' => 'Nike Cortez - prvá bežecká topánka Nike od Billa Bowermana z roku 1972. Kožený zvršok, pena v medzipodrážke, rybia kosť podrážka. Filmová ikona vďaka Forrest Gump.',
                'category_id' => $catTenisky,
                'colors' => ['white-red'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600&fit=crop',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600&fit=crop&crop=top',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600&fit=crop&crop=bottom',
                ]
            ],
            // === NIKE SB DUNK LOW (TENISKY) ===
            [
                'name' => 'Nike SB Dunk Low Pro',
                'brand' => 'Nike',
                'sku_model' => 'NK-SBDL-056',
                'base_price' => 119.99,
                'gender' => 'unisex',
                'description' => 'Nike SB Dunk Low Pro navrhnuté pre skateboarding. Zoom Air jednotka v päte pre tlmenie pri dopadoch, prémiová semišová koža pre odolnosť. Polstrovaný golier a jazyk, gumová podrážka pre grip na doske.',
                'category_id' => $catTenisky,
                'colors' => ['black-white'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1612902456551-333ac5afa26e?w=600',
                    'https://images.unsplash.com/photo-1597045566677-8cf032ed6634?w=600',
                    'https://images.unsplash.com/photo-1584735175315-9d5df23860e6?w=600',
                ]
            ],
            [
                'name' => 'Nike SB Dunk Low Chicago',
                'brand' => 'Nike',
                'sku_model' => 'NK-SBDLC-057',
                'base_price' => 129.99,
                'gender' => 'unisex',
                'description' => 'Nike SB Dunk Low v ikonickej červeno-bielej farebnosti Chicago. Zoom Air tlmenie, prémiové kožené panely a klasická skate silueta. Obľúbené medzi skatermi aj sneakerheadmi.',
                'category_id' => $catTenisky,
                'colors' => ['chicago'],
                'sizes' => ['39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600',
                    'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600',
                    'https://images.unsplash.com/photo-1546868871-0f936769675e?w=600',
                ]
            ],
            // === NIKE AIR JORDAN 4 (TENISKY) ===
            [
                'name' => 'Air Jordan 4 Retro',
                'brand' => 'Jordan',
                'sku_model' => 'JD-AJ4-058',
                'base_price' => 219.99,
                'gender' => 'men',
                'description' => 'Air Jordan 4 Retro s originálnym dizajnom od Tinkera Hatfielda z roku 1989. Mesh panely pre priedušnosť, viditeľná Air jednotka v päte, plastové krídla pre podporu. Basketbalová legenda prepracovaná pre ulicu.',
                'category_id' => $catTenisky,
                'colors' => ['white-cement'],
                'sizes' => ['40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                    'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=600',
                    'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=600',
                ]
            ],
            // === NIKE BLAZER MID (TENISKY) ===
            [
                'name' => 'Nike Blazer Mid 77',
                'brand' => 'Nike',
                'sku_model' => 'NK-BLZ77-059',
                'base_price' => 109.99,
                'gender' => 'unisex',
                'description' => 'Nike Blazer Mid 77 s vintage basketbalovým dizajnom z roku 1977. Kožený zvršok, ikonický Swoosh a vulkanizovaná gumová podrážka. Retro štýl s moderným pohodlím.',
                'category_id' => $catTenisky,
                'colors' => ['white-black'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600',
                    'https://images.unsplash.com/photo-1463100099107-aa0980c362e6?w=600',
                    'https://images.unsplash.com/photo-1494496195158-c3becb4f2475?w=600',
                ]
            ],
            // === ADIDAS YEEZY 350 V2 (TENISKY) ===
            [
                'name' => 'Adidas Yeezy 350 V2',
                'brand' => 'Adidas',
                'sku_model' => 'AD-YZY350-060',
                'base_price' => 279.99,
                'gender' => 'unisex',
                'description' => 'Adidas Yeezy 350 V2 navrhnuté Kanyem Westom. Primeknit pletený zvršok pre ponožkový fit, celoplytvá Boost medzipodrážka pre výnimočné tlmenie a návratnosť energie. Polopriehľadný bočný prúžok SPLY-350.',
                'category_id' => $catTenisky,
                'colors' => ['zebra'],
                'sizes' => ['40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                ]
            ],
            // === ADIDAS CAMPUS 00S (TENISKY) ===
            [
                'name' => 'Adidas Campus 00s',
                'brand' => 'Adidas',
                'sku_model' => 'AD-CMP00-061',
                'base_price' => 109.99,
                'gender' => 'unisex',
                'description' => 'Adidas Campus 00s - návrat kultovej siluety z Y2K éry. Mäkký semišový zvršok, polstrovaný golier a jazyk, gumová cupsole. Tri prúžky a Trefoil logo pre autentický retro look.',
                'category_id' => $catTenisky,
                'colors' => ['black'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1520256862855-398228c41684?w=600',
                    'https://images.unsplash.com/photo-1543508282-6319a3e2621f?w=600',
                    'https://images.unsplash.com/photo-1580906853149-2b92a8d680e0?w=600',
                ]
            ],
            // === AIR JORDAN 3 RETRO (TENISKY) ===
            [
                'name' => 'Air Jordan 3 Retro',
                'brand' => 'Jordan',
                'sku_model' => 'JD-AJ3-062',
                'base_price' => 199.99,
                'gender' => 'men',
                'description' => 'Air Jordan 3 Retro z roku 1988 - prvý Jordan s viditeľnou Air jednotkou a sloním potlačou. Dizajn od Tinkera Hatfielda, kožený zvršok, Jumpman logo na jazyku. Basketbalová klasika.',
                'category_id' => $catTenisky,
                'colors' => ['white-cement'],
                'sizes' => ['40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600',
                    'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600',
                    'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600',
                ]
            ],
            // === VANS ERA (TENISKY) ===
            [
                'name' => 'Vans Era',
                'brand' => 'Vans',
                'sku_model' => 'VN-ERA-063',
                'base_price' => 69.99,
                'gender' => 'unisex',
                'description' => 'Vans Era - prvá skate topánka značky Vans z roku 1976. Plátenný zvršok, polstrovaný golier pre pohodlie, vulkanizovaná gumová podrážka waffle pre grip. Jednoduchý a nadčasový dizajn.',
                'category_id' => $catTenisky,
                'colors' => ['navy'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600',
                    'https://images.unsplash.com/photo-1609585903816-a3a39a8a7a4d?w=600',
                    'https://images.unsplash.com/photo-1603787081207-362bcef7c144?w=600',
                ]
            ],
            // === CONVERSE RUN STAR HIKE (TENISKY) ===
            [
                'name' => 'Converse Run Star Hike',
                'brand' => 'Converse',
                'sku_model' => 'CV-RSH-064',
                'base_price' => 119.99,
                'gender' => 'unisex',
                'description' => 'Converse Run Star Hike s platformovou podrážkou a zubatým dizajnom. All Star DNA s moderným twistom, plátenný zvršok, CX pena pre pohodlie. Statement kúsok pre odvážny štýl.',
                'category_id' => $catTenisky,
                'colors' => ['black'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43'],
                'images' => [
                    'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600',
                    'https://images.unsplash.com/photo-1580906853149-2b92a8d680e0?w=600',
                    'https://images.unsplash.com/photo-1605733160314-4fc7dac4bb16?w=600',
                ]
            ],
            // === PUMA CLYDE (TENISKY) ===
            [
                'name' => 'Puma Clyde All-Pro',
                'brand' => 'Puma',
                'sku_model' => 'PM-CLYDE-065',
                'base_price' => 99.99,
                'gender' => 'men',
                'description' => 'Puma Clyde pomenovaná po basketbalovej legende Walte "Clyde" Frazierovi. Kožený zvršok, formstrip na bokoch, gumová podrážka. Od roku 1973 symbol elegancie na palubovke aj ulici.',
                'category_id' => $catTenisky,
                'colors' => ['white-gold'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                    'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?w=600',
                    'https://images.unsplash.com/photo-1575537302964-96cd47c06b1b?w=600',
                ]
            ],
            // === ADIDAS ULTRABOOST (BEH) ===
            [
                'name' => 'Adidas Ultraboost 22',
                'brand' => 'Adidas',
                'sku_model' => 'AD-UB22-010',
                'base_price' => 179.99,
                'gender' => 'men',
                'description' => 'Prémiové bežecké tenisky s Boost technológiou pre maximálnu energiu.',
                'category_id' => $catBeh,
                'colors' => ['black'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                    'https://images.unsplash.com/photo-1628253747716-0c4f5c90fdda?w=600',
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                ]
            ],
            [
                'name' => 'Adidas Ultraboost Light',
                'brand' => 'Adidas',
                'sku_model' => 'AD-UBL-011',
                'base_price' => 189.99,
                'gender' => 'women',
                'description' => 'Najľahší Ultraboost v histórii pre dámske nohy.',
                'category_id' => $catBeh,
                'colors' => ['pink'],
                'sizes' => ['36', '37', '38', '39', '40', '41'],
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                ]
            ],
            // === NEW BALANCE 574 ===
            [
                'name' => 'New Balance 574 Grey',
                'brand' => 'New Balance',
                'sku_model' => 'NB-574-012',
                'base_price' => 89.99,
                'gender' => 'unisex',
                'description' => 'Klasické New Balance 574 v sivej farbe.',
                'category_id' => $catLifestyle,
                'colors' => ['grey'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600',
                    'https://images.unsplash.com/photo-1565814636199-ae8133055c1c?w=600',
                    'https://images.unsplash.com/photo-1582966772680-860e372bb558?w=600',
                ]
            ],
            [
                'name' => 'New Balance 574 Navy',
                'brand' => 'New Balance',
                'sku_model' => 'NB-574N-013',
                'base_price' => 89.99,
                'gender' => 'men',
                'description' => 'New Balance 574 v tmavomodrej farbe.',
                'category_id' => $catLifestyle,
                'colors' => ['navy'],
                'sizes' => ['41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1584735175315-9d5df23860e6?w=600',
                    'https://images.unsplash.com/photo-1582588678413-dbf45f4823e9?w=600',
                    'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?w=600',
                ]
            ],
            // === NEW BALANCE 550 ===
            [
                'name' => 'New Balance 550',
                'brand' => 'New Balance',
                'sku_model' => 'NB-550-014',
                'base_price' => 129.99,
                'gender' => 'unisex',
                'description' => 'Retro basketbalový štýl New Balance 550.',
                'category_id' => $catTenisky,
                'colors' => ['white-green'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1597045566677-8cf032ed6634?w=600',
                    'https://images.unsplash.com/photo-1600269452121-4f2416e55c28?w=600',
                    'https://images.unsplash.com/photo-1612902456551-333ac5afa26e?w=600',
                ]
            ],
            // === VANS OLD SKOOL ===
            [
                'name' => 'Vans Old Skool Black',
                'brand' => 'Vans',
                'sku_model' => 'VN-OS-015',
                'base_price' => 74.99,
                'gender' => 'unisex',
                'description' => 'Kultové Vans Old Skool v čiernej farbe s bielym prúžkom.',
                'category_id' => $catTenisky,
                'colors' => ['black'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600',
                    'https://images.unsplash.com/photo-1609585903816-a3a39a8a7a4d?w=600',
                    'https://images.unsplash.com/photo-1603787081207-362bcef7c144?w=600',
                ]
            ],
            [
                'name' => 'Vans Sk8-Hi',
                'brand' => 'Vans',
                'sku_model' => 'VN-SK8-016',
                'base_price' => 84.99,
                'gender' => 'unisex',
                'description' => 'Vysoké Vans Sk8-Hi s polstrovaným golierom.',
                'category_id' => $catTenisky,
                'colors' => ['black-white'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                    'https://images.unsplash.com/photo-1579338559194-a162d19bf842?w=600',
                    'https://images.unsplash.com/photo-1595341888016-a392ef81b7de?w=600',
                ]
            ],
            // === CONVERSE ===
            [
                'name' => 'Converse Chuck Taylor High',
                'brand' => 'Converse',
                'sku_model' => 'CV-CTH-017',
                'base_price' => 69.99,
                'gender' => 'unisex',
                'description' => 'Vysoké Converse Chuck Taylor All Star v čiernej farbe.',
                'category_id' => $catTenisky,
                'colors' => ['black'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=600',
                    'https://images.unsplash.com/photo-1463100099107-aa0980c362e6?w=600',
                    'https://images.unsplash.com/photo-1494496195158-c3becb4f2475?w=600',
                ]
            ],
            [
                'name' => 'Converse Chuck Taylor Low White',
                'brand' => 'Converse',
                'sku_model' => 'CV-CTL-018',
                'base_price' => 64.99,
                'gender' => 'unisex',
                'description' => 'Nízke Converse v bielej farbe.',
                'category_id' => $catTenisky,
                'colors' => ['white'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43'],
                'images' => [
                    'https://images.unsplash.com/photo-1580906853149-2b92a8d680e0?w=600',
                    'https://images.unsplash.com/photo-1605733160314-4fc7dac4bb16?w=600',
                    'https://images.unsplash.com/photo-1608379743498-63e2fa966943?w=600',
                ]
            ],
            // === PUMA ===
            [
                'name' => 'Puma RS-X',
                'brand' => 'Puma',
                'sku_model' => 'PM-RSX-019',
                'base_price' => 99.99,
                'gender' => 'unisex',
                'description' => 'Futuristické Puma RS-X s retro dizajnom.',
                'category_id' => $catLifestyle,
                'colors' => ['white-pink'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                ]
            ],
            [
                'name' => 'Puma Suede Classic',
                'brand' => 'Puma',
                'sku_model' => 'PM-SDE-020',
                'base_price' => 84.99,
                'gender' => 'unisex',
                'description' => 'Kultové Puma Suede so semišovým zvrškom.',
                'category_id' => $catTenisky,
                'colors' => ['black'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?w=600',
                    'https://images.unsplash.com/photo-1575537302964-96cd47c06b1b?w=600',
                    'https://images.unsplash.com/photo-1609585903816-a3a39a8a7a4d?w=600',
                ]
            ],
            // === NIKE RUNNING ===
            [
                'name' => 'Nike Pegasus 40',
                'brand' => 'Nike',
                'sku_model' => 'NK-PEG-021',
                'base_price' => 139.99,
                'gender' => 'men',
                'description' => 'Osvedčené bežecké tenisky Nike Pegasus pre každodenný tréning.',
                'category_id' => $catBeh,
                'colors' => ['black-white'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                ]
            ],
            [
                'name' => 'Nike Pegasus 40 Women',
                'brand' => 'Nike',
                'sku_model' => 'NK-PEGW-022',
                'base_price' => 139.99,
                'gender' => 'women',
                'description' => 'Dámske Nike Pegasus 40 pre pohodlný beh.',
                'category_id' => $catBeh,
                'colors' => ['pink'],
                'sizes' => ['36', '37', '38', '39', '40', '41'],
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                    'https://images.unsplash.com/photo-1628253747716-0c4f5c90fdda?w=600',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600',
                ]
            ],
            [
                'name' => 'Nike ZoomX Vaporfly',
                'brand' => 'Nike',
                'sku_model' => 'NK-VPF-023',
                'base_price' => 249.99,
                'gender' => 'unisex',
                'description' => 'Pretekárske tenisky Nike ZoomX Vaporfly pre rýchle časy.',
                'category_id' => $catBeh,
                'colors' => ['neon'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=600',
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600',
                    'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=600',
                ]
            ],
            // === NIKE INVINCIBLE 3 (BEH) ===
            [
                'name' => 'Nike Invincible 3',
                'brand' => 'Nike',
                'sku_model' => 'NK-INV3-036',
                'base_price' => 199.99,
                'gender' => 'men',
                'description' => 'Nike Invincible 3 s technológiou ZoomX peny poskytuje maximálne tlmenie a výnimočnú návratnosť energie. Širšia geometria medzipodrážky zaisťuje stabilitu pri dlhých behoch. Flyknit zvršok s priedušnými zónami pre optimálny komfort.',
                'category_id' => $catBeh,
                'colors' => ['black'],
                'sizes' => ['40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                ]
            ],
            [
                'name' => 'Nike Invincible 3 Women',
                'brand' => 'Nike',
                'sku_model' => 'NK-INV3W-037',
                'base_price' => 199.99,
                'gender' => 'women',
                'description' => 'Dámska verzia Nike Invincible 3 s prémiovou ZoomX penou pre najvyšší komfort. Vylepšená stabilita vďaka širšej základni a klip na päte. Ideálne pre dlhé tréningové behy a regeneráciu.',
                'category_id' => $catBeh,
                'colors' => ['pink'],
                'sizes' => ['36', '37', '38', '39', '40', '41'],
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600',
                ]
            ],
            // === NIKE ALPHAFLY 3 (BEH) ===
            [
                'name' => 'Nike Alphafly 3',
                'brand' => 'Nike',
                'sku_model' => 'NK-ALF3-038',
                'base_price' => 299.99,
                'gender' => 'unisex',
                'description' => 'Najľahší Alphafly v histórii - pretekárska topánka pre maratóny. Kombinuje dve Air Zoom jednotky, celoplytvú karbónovú dosku Flyplate a ZoomX penu pre maximálnu návratnosť energie a propulziu. AtomKnit zvršok pre perfektné objatie nohy.',
                'category_id' => $catBeh,
                'colors' => ['neon-green'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=600',
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                ]
            ],
            // === NIKE STRUCTURE 25 (BEH) ===
            [
                'name' => 'Nike Structure 25',
                'brand' => 'Nike',
                'sku_model' => 'NK-STR25-039',
                'base_price' => 149.99,
                'gender' => 'men',
                'description' => 'Nike Structure 25 je stabilná bežecká topánka pre bežcov s pronáciou. Dvojvrstvová medzipodrážka s React penou poskytuje mäkké tlmenie a podporu. Široká základňa a spevnená päta zaručujú stabilitu počas behu.',
                'category_id' => $catBeh,
                'colors' => ['blue'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600',
                    'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=600',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                ]
            ],
            // === ADIDAS ADIZERO BOSTON 12 (BEH) ===
            [
                'name' => 'Adidas Adizero Boston 12',
                'brand' => 'Adidas',
                'sku_model' => 'AD-BOS12-040',
                'base_price' => 169.99,
                'gender' => 'men',
                'description' => 'Adizero Boston 12 pre tempo behy a preteky. Dvojvrstvová medzipodrážka s Lightstrike Pro a Lightstrike 2.0 penou. ENERGYRODS 2.0 zo sklenených vlákien poskytujú propulzívny pocit. Continental gumová podrážka pre maximálnu priľnavosť.',
                'category_id' => $catBeh,
                'colors' => ['orange'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                ]
            ],
            // === ADIDAS SUPERNOVA RISE (BEH) ===
            [
                'name' => 'Adidas Supernova Rise',
                'brand' => 'Adidas',
                'sku_model' => 'AD-SNVR-041',
                'base_price' => 139.99,
                'gender' => 'unisex',
                'description' => 'Adidas Supernova Rise je ideálny pre každodenný tréning. Dreamstrike+ pena ponúka vyvážené tlmenie a responzivitu. Priedušný mesh zvršok a gumová podrážka Continental pre spoľahlivú trakciu za každého počasia.',
                'category_id' => $catBeh,
                'colors' => ['grey'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1628253747716-0c4f5c90fdda?w=600',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                ]
            ],
            // === ASICS GEL NIMBUS 25 (BEH) ===
            [
                'name' => 'ASICS Gel Nimbus 25',
                'brand' => 'ASICS',
                'sku_model' => 'AS-NIM25-042',
                'base_price' => 189.99,
                'gender' => 'men',
                'description' => 'ASICS Gel Nimbus 25 s FF BLAST+ ECO penou pre maximálne tlmenie a pocit behu na oblaku. Technológia PureGEL pohltí nárazy a zabezpečí plynulé prechody. Pletený zvršok objíma nohu a ponúka prémiovú pohodlnosť pri dlhých behoch.',
                'category_id' => $catBeh,
                'colors' => ['blue'],
                'sizes' => ['40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600',
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600',
                ]
            ],
            // === ASICS GEL KAYANO 30 (BEH) ===
            [
                'name' => 'ASICS Gel Kayano 30',
                'brand' => 'ASICS',
                'sku_model' => 'AS-KAY30-043',
                'base_price' => 199.99,
                'gender' => 'women',
                'description' => 'ASICS Gel Kayano 30 je prémiová stabilizačná topánka pre bežcov s overpronáciou. FF BLAST+ pena poskytuje mäkké tlmenie, 4D Guidance System zaisťuje podporu a stabilitu. Ideálny pre dlhé vzdialenosti.',
                'category_id' => $catBeh,
                'colors' => ['purple'],
                'sizes' => ['36', '37', '38', '39', '40', '41'],
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                    'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=600',
                    'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=600',
                ]
            ],
            // === HOKA CLIFTON 9 (BEH) ===
            [
                'name' => 'HOKA Clifton 9',
                'brand' => 'HOKA',
                'sku_model' => 'HK-CLF9-044',
                'base_price' => 159.99,
                'gender' => 'men',
                'description' => 'HOKA Clifton 9 je ľahká a maximálne tlmená topánka pre každodenný tréning. Prepracovaná CMEVA pena je mäkšia a ľahšia. Meta-Rocker geometria zabezpečuje plynulé prechody. Priedušný mesh zvršok a Durabrasion guma na podrážke.',
                'category_id' => $catBeh,
                'colors' => ['white'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600',
                ]
            ],
            // === HOKA MACH 5 (BEH) ===
            [
                'name' => 'HOKA Mach 5',
                'brand' => 'HOKA',
                'sku_model' => 'HK-MCH5-045',
                'base_price' => 149.99,
                'gender' => 'unisex',
                'description' => 'HOKA Mach 5 je rýchla a responzívna topánka pre tempo behy a preteky. Ľahká PROFLY medzipodrážka kombinuje mäkkosť a odraz. Nízka váha a dynamický rocker pre prirodzený pohyb. Priedušný creel jacquard mesh zvršok.',
                'category_id' => $catBeh,
                'colors' => ['yellow'],
                'sizes' => ['39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600',
                ]
            ],
            // === REEBOK ===
            [
                'name' => 'Reebok Classic Leather',
                'brand' => 'Reebok',
                'sku_model' => 'RB-CL-024',
                'base_price' => 89.99,
                'gender' => 'unisex',
                'description' => 'Ikonické Reebok Classic Leather v bielej farbe.',
                'category_id' => $catLifestyle,
                'colors' => ['white'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1595341888016-a392ef81b7de?w=600',
                    'https://images.unsplash.com/photo-1608379743498-63e2fa966943?w=600',
                    'https://images.unsplash.com/photo-1605733160314-4fc7dac4bb16?w=600',
                ]
            ],
            [
                'name' => 'Reebok Club C 85',
                'brand' => 'Reebok',
                'sku_model' => 'RB-CC-025',
                'base_price' => 79.99,
                'gender' => 'unisex',
                'description' => 'Tenisová klasika Reebok Club C 85.',
                'category_id' => $catLifestyle,
                'colors' => ['white-green'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1580256079206-46a04a632758?w=600',
                    'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=600',
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600',
                ]
            ],
            // === LIFESTYLE WOMEN ===
            [
                'name' => 'Nike Air Max 90 Women',
                'brand' => 'Nike',
                'sku_model' => 'NK-AM90W-026',
                'base_price' => 149.99,
                'gender' => 'women',
                'description' => 'Dámske Nike Air Max 90 v pastelových farbách.',
                'category_id' => $catLifestyle,
                'colors' => ['pink'],
                'sizes' => ['36', '37', '38', '39', '40', '41'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                ]
            ],
            [
                'name' => 'Adidas Gazelle Women',
                'brand' => 'Adidas',
                'sku_model' => 'AD-GAZW-027',
                'base_price' => 109.99,
                'gender' => 'women',
                'description' => 'Dámske Adidas Gazelle v ružovej farbe.',
                'category_id' => $catLifestyle,
                'colors' => ['pink'],
                'sizes' => ['36', '37', '38', '39', '40'],
                'images' => [
                    'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=600',
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600',
                    'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=600',
                ]
            ],
            // === NIKE AIR MAX 97 (LIFESTYLE) ===
            [
                'name' => 'Nike Air Max 97 Silver Bullet',
                'brand' => 'Nike',
                'sku_model' => 'NK-AM97-046',
                'base_price' => 189.99,
                'gender' => 'unisex',
                'description' => 'Ikonické Nike Air Max 97 s vlnitým dizajnom inšpirovaným vlnami vody. Celoplytvá viditeľná Air jednotka poskytuje výnimočné tlmenie. Skrytý šnurovací systém pre čisté línie a priedušný mesh zvršok.',
                'category_id' => $catLifestyle,
                'colors' => ['silver'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                    'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600',
                ]
            ],
            [
                'name' => 'Nike Air Max 97 Triple Black',
                'brand' => 'Nike',
                'sku_model' => 'NK-AM97B-047',
                'base_price' => 189.99,
                'gender' => 'men',
                'description' => 'Nike Air Max 97 v celočiernom prevedení. Full-length Air jednotka pre maximálny komfort počas celého dňa. Robustná gumová podrážka s BRS1000 uhlíkovou gumou pre trvanlivosť.',
                'category_id' => $catLifestyle,
                'colors' => ['black'],
                'sizes' => ['40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1588117305388-c2631a279f82?w=600',
                    'https://images.unsplash.com/photo-1579338559194-a162d19bf842?w=600',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                ]
            ],
            // === NIKE AIR MAX 1 (LIFESTYLE) ===
            [
                'name' => 'Nike Air Max 1',
                'brand' => 'Nike',
                'sku_model' => 'NK-AM1-048',
                'base_price' => 149.99,
                'gender' => 'unisex',
                'description' => 'Originálne Nike Air Max 1, ktoré v roku 1987 predstavili viditeľnú Air technológiu. Kombinácia kože, semišu a mesh materiálov. Nadčasový dizajn od Tinkera Hatfielda.',
                'category_id' => $catLifestyle,
                'colors' => ['white-red'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600',
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                ]
            ],
            // === ADIDAS FORUM LOW (LIFESTYLE) ===
            [
                'name' => 'Adidas Forum Low',
                'brand' => 'Adidas',
                'sku_model' => 'AD-FORUM-049',
                'base_price' => 119.99,
                'gender' => 'unisex',
                'description' => 'Adidas Forum Low z roku 1984 prepracovaný pre moderný streetwear. Ikonický remienok na členku, prémiová koža a tri prúžky. EVA medzipodrážka pre celodenný komfort.',
                'category_id' => $catLifestyle,
                'colors' => ['white'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1608379743498-63e2fa966943?w=600',
                    'https://images.unsplash.com/photo-1605733160314-4fc7dac4bb16?w=600',
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600',
                ]
            ],
            // === ADIDAS GAZELLE (LIFESTYLE) ===
            [
                'name' => 'Adidas Gazelle',
                'brand' => 'Adidas',
                'sku_model' => 'AD-GAZ-050',
                'base_price' => 99.99,
                'gender' => 'unisex',
                'description' => 'Adidas Gazelle je legenda od roku 1966. Prémový semišový zvršok, kontrastné tri prúžky a gumová podrážka. Jednoduchý a nadčasový dizajn pre každodenné nosenie.',
                'category_id' => $catLifestyle,
                'colors' => ['blue'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1520256862855-398228c41684?w=600',
                    'https://images.unsplash.com/photo-1543508282-6319a3e2621f?w=600',
                    'https://images.unsplash.com/photo-1580906853149-2b92a8d680e0?w=600',
                ]
            ],
            // === NEW BALANCE 2002R (LIFESTYLE) ===
            [
                'name' => 'New Balance 2002R',
                'brand' => 'New Balance',
                'sku_model' => 'NB-2002R-051',
                'base_price' => 159.99,
                'gender' => 'unisex',
                'description' => 'New Balance 2002R spája retro estetiku s moderným komfortom. Kombinácia prasacieho semišu a mesh materiálov. ABZORB tlmenie, ACTEVA LITE medzipodrážka a N-ergy podrážka pre maximálny komfort.',
                'category_id' => $catLifestyle,
                'colors' => ['grey'],
                'sizes' => ['40', '41', '42', '43', '44', '45', '46'],
                'images' => [
                    'https://images.unsplash.com/photo-1539185441755-769473a23570?w=600',
                    'https://images.unsplash.com/photo-1565814636199-ae8133055c1c?w=600',
                    'https://images.unsplash.com/photo-1582966772680-860e372bb558?w=600',
                ]
            ],
            // === NEW BALANCE 327 (LIFESTYLE) ===
            [
                'name' => 'New Balance 327',
                'brand' => 'New Balance',
                'sku_model' => 'NB-327-052',
                'base_price' => 119.99,
                'gender' => 'unisex',
                'description' => 'New Balance 327 s retro bežeckým dizajnom z 70. rokov. Výrazná veľká podrážka, semišové a nylonové panely. Moderná interpretácia klasiky pre streetwear štýl.',
                'category_id' => $catLifestyle,
                'colors' => ['green'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44'],
                'images' => [
                    'https://images.unsplash.com/photo-1584735175315-9d5df23860e6?w=600',
                    'https://images.unsplash.com/photo-1582588678413-dbf45f4823e9?w=600',
                    'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?w=600',
                ]
            ],
            // === PUMA PALERMO (LIFESTYLE) ===
            [
                'name' => 'Puma Palermo',
                'brand' => 'Puma',
                'sku_model' => 'PM-PAL-053',
                'base_price' => 89.99,
                'gender' => 'unisex',
                'description' => 'Puma Palermo pôvodne navrhnuté pre futbalových fanúšikov v 80. rokoch. Semišový a nylonový zvršok, gumová podrážka a T-toe dizajn. Obľúbené v terrace kultúre.',
                'category_id' => $catLifestyle,
                'colors' => ['blue'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=600',
                    'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=600',
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600',
                ]
            ],
            // === ASICS GEL-LYTE III (LIFESTYLE) ===
            [
                'name' => 'ASICS Gel-Lyte III OG',
                'brand' => 'ASICS',
                'sku_model' => 'AS-GL3-054',
                'base_price' => 139.99,
                'gender' => 'unisex',
                'description' => 'ASICS Gel-Lyte III z roku 1990 s ikonickým rozdeleným jazykom. GEL technológia pre tlmenie nárazov, semišový a mesh zvršok. Kultový sneaker uznávaný kolekcionármi po celom svete.',
                'category_id' => $catLifestyle,
                'colors' => ['white-blue'],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=600',
                    'https://images.unsplash.com/photo-1600185365926-3a2ce3cdb9eb?w=600',
                    'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?w=600',
                ]
            ],
            // === ONITSUKA TIGER MEXICO 66 (LIFESTYLE) ===
            [
                'name' => 'Onitsuka Tiger Mexico 66',
                'brand' => 'Onitsuka Tiger',
                'sku_model' => 'OT-MEX66-055',
                'base_price' => 129.99,
                'gender' => 'unisex',
                'description' => 'Onitsuka Tiger Mexico 66 vytvorené pre olympiádu v Mexiku 1968. Kožený zvršok s ikonickými prúžkami, plochá kopaná podrážka. Minimalistický a nadčasový japonský dizajn.',
                'category_id' => $catLifestyle,
                'colors' => ['white-red-blue'],
                'sizes' => ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'],
                'images' => [
                    'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=600',
                    'https://images.unsplash.com/photo-1556906781-9a412961c28c?w=600',
                    'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=600',
                ]
            ],
            // === ACCESSORIES - BACKPACKS (unikátne obrázky batohov) ===
            [
                'name' => 'Nike Heritage Backpack',
                'brand' => 'Nike',
                'sku_model' => 'NK-BP-028',
                'base_price' => 44.99,
                'gender' => 'unisex',
                'description' => 'Praktický batoh Nike Heritage na každý deň.',
                'category_id' => $catDoplnky,
                'colors' => ['black'],
                'sizes' => ['UNI'],
                'images' => [
                    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600',
                    'https://images.unsplash.com/photo-1581605405669-fcdf81165afa?w=600',
                    'https://images.unsplash.com/photo-1509762774605-f07235a08f1f?w=600',
                ]
            ],
            [
                'name' => 'Adidas Classic Backpack',
                'brand' => 'Adidas',
                'sku_model' => 'AD-BP-029',
                'base_price' => 39.99,
                'gender' => 'unisex',
                'description' => 'Batoh Adidas s 3 prúžkami.',
                'category_id' => $catDoplnky,
                'colors' => ['black'],
                'sizes' => ['UNI'],
                'images' => [
                    'https://images.unsplash.com/photo-1581605405669-fcdf81165afa?w=600',
                    'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600',
                    'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600',
                ]
            ],
            [
                'name' => 'Premium Leather Backpack',
                'brand' => 'Atellé',
                'sku_model' => 'AT-LB-030',
                'base_price' => 159.00,
                'gender' => 'unisex',
                'description' => 'Luxusný kožený batoh ručnej výroby.',
                'category_id' => $catDoplnky,
                'colors' => ['brown'],
                'sizes' => ['UNI'],
                'images' => [
                    'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600',
                    'https://images.unsplash.com/photo-1491637639811-60e2756cc1c7?w=600',
                    'https://images.unsplash.com/photo-1509762774605-f07235a08f1f?w=600',
                ]
            ],
            // === ACCESSORIES - CAPS (unikátne obrázky šiltoviek) ===
            [
                'name' => 'New Era 9FORTY Black',
                'brand' => 'New Era',
                'sku_model' => 'NE-CAP-031',
                'base_price' => 34.99,
                'gender' => 'unisex',
                'description' => 'Čierna šiltovka New Era 9FORTY.',
                'category_id' => $catDoplnky,
                'colors' => ['black'],
                'sizes' => ['UNI'],
                'images' => [
                    'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=600',
                    'https://images.unsplash.com/photo-1521369909029-2afed882baee?w=600',
                    'https://images.unsplash.com/photo-1534215754734-18e55d13e346?w=600',
                ]
            ],
            [
                'name' => 'Nike Swoosh Cap',
                'brand' => 'Nike',
                'sku_model' => 'NK-CAP-032',
                'base_price' => 24.99,
                'gender' => 'unisex',
                'description' => 'Klasická šiltovka Nike so Swoosh logom.',
                'category_id' => $catDoplnky,
                'colors' => ['black'],
                'sizes' => ['UNI'],
                'images' => [
                    'https://images.unsplash.com/photo-1556306535-0f09a537f0a3?w=600',
                    'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=600',
                    'https://images.unsplash.com/photo-1596455607563-ad6193f76b17?w=600',
                ]
            ],
            // === ACCESSORIES - BEANIES ===
            [
                'name' => 'Adidas Trefoil Beanie',
                'brand' => 'Adidas',
                'sku_model' => 'AD-BN-033',
                'base_price' => 29.99,
                'gender' => 'unisex',
                'description' => 'Teplá zimná čiapka Adidas Trefoil.',
                'category_id' => $catDoplnky,
                'colors' => ['black'],
                'sizes' => ['UNI'],
                'images' => [
                    'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?w=600',
                    'https://images.unsplash.com/photo-1510598155894-93bfc0df0a7e?w=600',
                    'https://images.unsplash.com/photo-1529391409740-26f21dcd8157?w=600',
                ]
            ],
            // === ACCESSORIES - SOCKS ===
            [
                'name' => 'Nike Crew Socks 3-Pack',
                'brand' => 'Nike',
                'sku_model' => 'NK-SOX-034',
                'base_price' => 19.99,
                'gender' => 'unisex',
                'description' => 'Balenie 3 párov ponožiek Nike.',
                'category_id' => $catDoplnky,
                'colors' => ['white'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'images' => [
                    'https://images.unsplash.com/photo-1582966772680-860e372bb558?w=600',
                    'https://images.unsplash.com/photo-1571689936114-b16146c9570a?w=600',
                    'https://images.unsplash.com/photo-1558171813-4c088753af8f?w=600',
                ]
            ],
            [
                'name' => 'Adidas Ankle Socks 3-Pack',
                'brand' => 'Adidas',
                'sku_model' => 'AD-SOX-035',
                'base_price' => 16.99,
                'gender' => 'unisex',
                'description' => 'Členkové ponožky Adidas, balenie 3 párov.',
                'category_id' => $catDoplnky,
                'colors' => ['white'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'images' => [
                    'https://images.unsplash.com/photo-1571689936114-b16146c9570a?w=600',
                    'https://images.unsplash.com/photo-1582966772680-860e372bb558?w=600',
                    'https://images.unsplash.com/photo-1558171813-4c088753af8f?w=600',
                ]
            ],
        ];

        $productId = 1;
        $variantId = 1;

        foreach ($products as $p) {
            Product::create([
                'product_id' => $productId,
                'name' => $p['name'],
                'brand' => $p['brand'],
                'sku_model' => $p['sku_model'],
                'base_price' => $p['base_price'],
                'gender' => $p['gender'],
                'description' => $p['description'],
                'is_active' => 1,
                'category_id' => $p['category_id'],
            ]);

            foreach ($p['images'] as $i => $url) {
                ProductImage::create([
                    'product_id' => $productId,
                    'image_path' => $url,
                    'is_main' => $i === 0 ? 1 : 0,
                    'sort_order' => $i + 1,
                ]);
            }

            foreach ($p['colors'] as $color) {
                foreach ($p['sizes'] as $size) {
                    ProductVariant::create([
                        'variant_id' => $variantId,
                        'product_id' => $productId,
                        'sku' => strtoupper(substr($p['brand'], 0, 2)) . "-{$productId}-" . strtoupper(substr($color, 0, 3)) . "-{$size}",
                        'color' => $color,
                        'size_eu' => $size,
                        'is_active' => 1,
                    ]);
                    Inventory::create(['variant_id' => $variantId, 'stock_qty' => rand(0, 25)]);
                    $variantId++;
                }
            }
            $productId++;
        }

        $this->command->info("Vytvorených " . count($products) . " produktov.");
    }
}

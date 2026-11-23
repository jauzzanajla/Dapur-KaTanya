<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\RawMaterialPurchase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ensure storage directories exist
        $productsDir = storage_path('app/public/products');
        $qrisDir = storage_path('app/public/qris');
        $proofDir = storage_path('app/public/payment_proofs');

        File::ensureDirectoryExists($productsDir);
        File::ensureDirectoryExists($qrisDir);
        File::ensureDirectoryExists($proofDir);

        // Helper function to write SVG image
        $createSvg = function (string $path, string $title, string $subtitle, string $bgColor1, string $bgColor2, string $accentColor, string $iconSvg) {
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$bgColor1};stop-opacity:1" />
      <stop offset="100%" style="stop-color:{$bgColor2};stop-opacity:1" />
    </linearGradient>
    <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="12" stdDeviation="16" flood-color="#000000" flood-opacity="0.18"/>
    </filter>
    <filter id="soft" x="-10%" y="-10%" width="120%" height="120%">
      <feDropShadow dx="0" dy="4" stdDeviation="8" flood-color="{$accentColor}" flood-opacity="0.3"/>
    </filter>
  </defs>

  <!-- Background -->
  <rect width="800" height="600" fill="url(#grad)" rx="24"/>

  <!-- Decorative circles -->
  <circle cx="700" cy="100" r="160" fill="{$accentColor}" opacity="0.08" />
  <circle cx="100" cy="500" r="120" fill="{$accentColor}" opacity="0.06" />

  <!-- Content Card -->
  <g transform="translate(100, 70)" filter="url(#shadow)">
    <rect width="600" height="460" rx="28" fill="#FFFFFF" />

    <!-- Top Badge -->
    <rect x="220" y="32" width="160" height="36" rx="18" fill="{$accentColor}" />
    <text x="300" y="56" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="14" font-weight="700" fill="#FFFFFF" text-anchor="middle" letter-spacing="1">DAPUR KATANYA</text>

    <!-- Center Graphic Circle -->
    <g transform="translate(300, 200)">
      <circle cx="0" cy="0" r="95" fill="{$bgColor1}" opacity="0.3" />
      <circle cx="0" cy="0" r="80" fill="{$accentColor}" opacity="0.12" />
      {$iconSvg}
    </g>

    <!-- Title & Subtitle -->
    <text x="300" y="340" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="28" font-weight="800" fill="#1E293B" text-anchor="middle">{$title}</text>
    <text x="300" y="375" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="16" font-weight="500" fill="#64748B" text-anchor="middle">{$subtitle}</text>

    <!-- Bottom aesthetic bar -->
    <g transform="translate(175, 410)">
      <rect width="250" height="8" rx="4" fill="{$accentColor}" opacity="0.8" />
    </g>
  </g>
</svg>
SVG;
            file_put_contents($path, $svg);
        };

        // Icons for SVGs
        $dimsumIcon = '<path d="M-40,20 C-40,-30 40,-30 40,20 Z" fill="#F59E0B" stroke="#D97706" stroke-width="4"/><circle cx="0" cy="-5" r="12" fill="#DC2626"/><circle cx="-18" cy="5" r="8" fill="#FBBF24"/><circle cx="18" cy="5" r="8" fill="#FBBF24"/><path d="M-50,22 Q0,32 50,22" stroke="#EA580C" stroke-width="6" fill="none" stroke-linecap="round"/>';
        $dimsumIcon2 = '<circle cx="-25" cy="-10" r="28" fill="#FDE68A" stroke="#F59E0B" stroke-width="3"/><circle cx="25" cy="-10" r="28" fill="#FDE68A" stroke="#F59E0B" stroke-width="3"/><circle cx="0" cy="15" r="28" fill="#FDE68A" stroke="#F59E0B" stroke-width="3"/><path d="M-45,35 Q0,55 45,35" stroke="#EA580C" stroke-width="8" fill="none" stroke-linecap="round"/>';

        $cirengIcon = '<ellipse cx="0" cy="0" rx="55" ry="38" fill="#FEF08A" stroke="#CA8A04" stroke-width="4"/><ellipse cx="-15" cy="-10" rx="15" ry="10" fill="#FACC15" opacity="0.6"/><ellipse cx="18" cy="8" rx="18" ry="12" fill="#FACC15" opacity="0.6"/><path d="M-30,-5 Q10,-20 30,10" stroke="#DC2626" stroke-width="5" fill="none" stroke-linecap="round"/>';
        $cirengIcon2 = '<path d="M-45,15 Q-20,-40 20,-30 Q60,-10 40,30 Q-10,50 -45,15 Z" fill="#FEF08A" stroke="#EAB308" stroke-width="4"/><path d="M-20,-10 Q0,20 25,0" stroke="#DC2626" stroke-width="6" fill="none" stroke-linecap="round"/>';

        $teaIcon = '<rect x="-26" y="-35" width="52" height="70" rx="8" fill="#F59E0B" opacity="0.85" stroke="#B45309" stroke-width="3"/><rect x="-22" y="-10" width="16" height="16" rx="3" fill="#FFFFFF" opacity="0.6"/><rect x="4" y="5" width="16" height="16" rx="3" fill="#FFFFFF" opacity="0.6"/><path d="M15,-50 L25,-25" stroke="#DC2626" stroke-width="5" stroke-linecap="round"/>';
        $teaIcon2 = '<path d="M-30,-40 L30,-40 L22,35 L-22,35 Z" fill="#D97706" opacity="0.9" stroke="#92400E" stroke-width="3"/><circle cx="-5" cy="-10" r="6" fill="#FDE68A"/><circle cx="10" cy="5" r="5" fill="#FDE68A"/>';

        $chocoIcon = '<rect x="-28" y="-38" width="56" height="76" rx="10" fill="#78350F" stroke="#451A03" stroke-width="4"/><rect x="-18" y="-20" width="36" height="12" rx="4" fill="#B45309"/><ellipse cx="0" cy="30" rx="20" ry="6" fill="#451A03"/><path d="M12,-55 L22,-20" stroke="#EA580C" stroke-width="5" stroke-linecap="round"/>';
        $chocoIcon2 = '<ellipse cx="0" cy="-30" rx="32" ry="12" fill="#92400E"/><path d="M-32,-30 L-22,35 L22,35 L32,-30 Z" fill="#78350F" stroke="#451A03" stroke-width="3"/><circle cx="-8" cy="-5" r="8" fill="#FFFFFF" opacity="0.4"/>';

        $bundlingIcon = '<circle cx="-25" cy="-15" r="28" fill="#F59E0B"/><circle cx="25" cy="-15" r="28" fill="#EA580C"/><rect x="-15" y="10" width="30" height="35" rx="6" fill="#DC2626"/><text x="0" y="32" font-family="sans-serif" font-size="16" font-weight="900" fill="#FFF" text-anchor="middle">KOMBO</text>';
        $bundlingIcon2 = '<polygon points="0,-45 42,35 -42,35" fill="#DC2626" opacity="0.9"/><circle cx="0" cy="-5" r="16" fill="#FEF08A"/><text x="0" y="24" font-family="sans-serif" font-size="14" font-weight="900" fill="#FFF" text-anchor="middle">LULUS</text>';

        // Generate SVGs for products
        $createSvg($productsDir.'/dimsum-1.svg', 'Dimsum Kuah Keju', 'Porsi Spesial Gurih & Creamy', '#FFFBEB', '#FEF3C7', '#DC2626', $dimsumIcon);
        $createSvg($productsDir.'/dimsum-2.svg', 'Dimsum Kuah Keju', 'Tampak Atas & Extra Keju Lumer', '#FEF2F2', '#FEE2E2', '#EA580C', $dimsumIcon2);

        $createSvg($productsDir.'/cireng-1.svg', 'Cireng Kuah Keju', 'Kenyal Krispi Disiram Kuah Keju', '#FFF7ED', '#FFEDD5', '#EA580C', $cirengIcon);
        $createSvg($productsDir.'/cireng-2.svg', 'Cireng Kuah Keju', 'Crispy Golden Brown & Chili Cheese', '#FFFBEB', '#FEF3C7', '#DC2626', $cirengIcon2);

        $createSvg($productsDir.'/esteh-1.svg', 'Es.T (Esteh)', 'Teh Segar Melati Manis Pas', '#ECFDF5', '#D1FAE5', '#059669', $teaIcon);
        $createSvg($productsDir.'/esteh-2.svg', 'Es.T (Esteh)', 'Segar Dingin Pelepas Dahaga', '#FEF3C7', '#FDE68A', '#EA580C', $teaIcon2);

        $createSvg($productsDir.'/skom-1.svg', 'S.Kom (Es Coklat)', 'Es Coklat Kental Mood Booster', '#FEF2F2', '#FEE2E2', '#78350F', $chocoIcon);
        $createSvg($productsDir.'/skom-2.svg', 'S.Kom (Es Coklat)', 'Racikan Coklat Premium Legit', '#FFF7ED', '#FFEDD5', '#DC2626', $chocoIcon2);

        $createSvg($productsDir.'/bundling-1.svg', 'Paket Cepat Lulus', 'Dimsum + Cireng + Minuman Pilihan', '#FEF2F2', '#FFFBEB', '#DC2626', $bundlingIcon);
        $createSvg($productsDir.'/bundling-2.svg', 'Paket Cepat Lulus', 'Paling Hemat & Puas Buat Nugas', '#FFF7ED', '#FEF2F2', '#EA580C', $bundlingIcon2);

        // Generate QRIS Official Image
        $qrisSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 700" width="100%" height="100%">
  <defs>
    <filter id="qris-shadow" x="-10%" y="-10%" width="120%" height="120%">
      <feDropShadow dx="0" dy="6" stdDeviation="12" flood-color="#000" flood-opacity="0.12"/>
    </filter>
  </defs>

  <!-- Background White Card -->
  <rect width="500" height="700" rx="20" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="2" filter="url(#qris-shadow)"/>

  <!-- QRIS Header Banner (Red) -->
  <rect x="0" y="0" width="500" height="90" rx="20" fill="#DC2626"/>
  <rect x="0" y="70" width="500" height="20" fill="#DC2626"/>

  <text x="250" y="48" font-family="Arial, Helvetica, sans-serif" font-size="28" font-weight="900" fill="#FFFFFF" text-anchor="middle" letter-spacing="3">QRIS</text>
  <text x="250" y="72" font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="600" fill="#FEF2F2" text-anchor="middle" letter-spacing="1">QR CODE STANDAR PEMBAYARAN NASIONAL</text>

  <!-- Merchant Info -->
  <text x="250" y="130" font-family="-apple-system, BlinkMacSystemFont, sans-serif" font-size="22" font-weight="800" fill="#1E293B" text-anchor="middle">DAPUR KATANYA</text>
  <text x="250" y="152" font-family="-apple-system, BlinkMacSystemFont, sans-serif" font-size="13" font-weight="500" fill="#64748B" text-anchor="middle">NMID: ID1024398291048 | A01</text>

  <!-- QR Code Frame -->
  <rect x="65" y="175" width="370" height="370" rx="16" fill="#F8FAFC" stroke="#CBD5E1" stroke-width="2"/>

  <!-- Simulated QR Pattern -->
  <!-- Top Left Marker -->
  <rect x="95" y="205" width="70" height="70" rx="8" fill="#1E293B"/>
  <rect x="107" y="217" width="46" height="46" rx="4" fill="#FFFFFF"/>
  <rect x="117" y="227" width="26" height="26" rx="2" fill="#DC2626"/>

  <!-- Top Right Marker -->
  <rect x="335" y="205" width="70" height="70" rx="8" fill="#1E293B"/>
  <rect x="347" y="217" width="46" height="46" rx="4" fill="#FFFFFF"/>
  <rect x="357" y="227" width="26" height="26" rx="2" fill="#DC2626"/>

  <!-- Bottom Left Marker -->
  <rect x="95" y="445" width="70" height="70" rx="8" fill="#1E293B"/>
  <rect x="107" y="457" width="46" height="46" rx="4" fill="#FFFFFF"/>
  <rect x="117" y="467" width="26" height="26" rx="2" fill="#DC2626"/>

  <!-- Matrix Elements -->
  <g fill="#1E293B">
    <!-- QR Data Blocks -->
    <rect x="180" y="210" width="16" height="16" rx="2"/>
    <rect x="210" y="210" width="32" height="16" rx="2"/>
    <rect x="260" y="210" width="16" height="32" rx="2"/>
    <rect x="290" y="210" width="32" height="16" rx="2"/>

    <rect x="180" y="240" width="32" height="16" rx="2"/>
    <rect x="230" y="240" width="16" height="16" rx="2"/>
    <rect x="280" y="240" width="32" height="32" rx="2"/>

    <rect x="95" y="295" width="48" height="16" rx="2"/>
    <rect x="160" y="295" width="32" height="32" rx="2"/>
    <rect x="210" y="280" width="48" height="16" rx="2"/>
    <rect x="335" y="295" width="32" height="16" rx="2"/>
    <rect x="380" y="295" width="25" height="48" rx="2"/>

    <!-- Center Logo Shield -->
    <circle cx="250" cy="360" r="42" fill="#FFFFFF" stroke="#DC2626" stroke-width="3"/>
    <circle cx="250" cy="360" r="34" fill="#DC2626"/>
    <text x="250" y="367" font-family="sans-serif" font-size="16" font-weight="900" fill="#FFF" text-anchor="middle">DK</text>

    <rect x="95" y="330" width="16" height="32" rx="2"/>
    <rect x="125" y="345" width="32" height="16" rx="2"/>
    <rect x="175" y="340" width="16" height="32" rx="2"/>

    <rect x="310" y="335" width="32" height="16" rx="2"/>
    <rect x="355" y="355" width="50" height="16" rx="2"/>

    <rect x="95" y="390" width="32" height="32" rx="2"/>
    <rect x="145" y="405" width="48" height="16" rx="2"/>
    <rect x="210" y="420" width="32" height="32" rx="2"/>
    <rect x="260" y="410" width="48" height="16" rx="2"/>
    <rect x="325" y="390" width="32" height="32" rx="2"/>
    <rect x="370" y="415" width="35" height="16" rx="2"/>

    <rect x="180" y="465" width="32" height="16" rx="2"/>
    <rect x="230" y="465" width="16" height="32" rx="2"/>
    <rect x="265" y="455" width="48" height="16" rx="2"/>
    <rect x="330" y="445" width="32" height="32" rx="2"/>
    <rect x="380" y="450" width="25" height="16" rx="2"/>
    <rect x="345" y="490" width="60" height="25" rx="2"/>
  </g>

  <!-- Supported Payments Footer -->
  <g transform="translate(40, 565)">
    <text x="210" y="20" font-family="-apple-system, BlinkMacSystemFont, sans-serif" font-size="11" font-weight="700" fill="#64748B" text-anchor="middle" letter-spacing="1">MENERIMA PEMBAYARAN DARI SEMUA APLIKASI</text>

    <!-- App Badges -->
    <rect x="10" y="35" width="56" height="24" rx="4" fill="#005BAA"/>
    <text x="38" y="51" font-family="sans-serif" font-size="10" font-weight="700" fill="#FFF" text-anchor="middle">BCA</text>

    <rect x="74" y="35" width="64" height="24" rx="4" fill="#002D62"/>
    <text x="106" y="51" font-family="sans-serif" font-size="10" font-weight="700" fill="#FFF" text-anchor="middle">Mandiri</text>

    <rect x="146" y="35" width="56" height="24" rx="4" fill="#0081C9"/>
    <text x="174" y="51" font-family="sans-serif" font-size="10" font-weight="700" fill="#FFF" text-anchor="middle">GoPay</text>

    <rect x="210" y="35" width="56" height="24" rx="4" fill="#4C2A86"/>
    <text x="238" y="51" font-family="sans-serif" font-size="10" font-weight="700" fill="#FFF" text-anchor="middle">OVO</text>

    <rect x="274" y="35" width="56" height="24" rx="4" fill="#118EEA"/>
    <text x="302" y="51" font-family="sans-serif" font-size="10" font-weight="700" fill="#FFF" text-anchor="middle">DANA</text>

    <rect x="338" y="35" width="74" height="24" rx="4" fill="#EE4D2D"/>
    <text x="375" y="51" font-family="sans-serif" font-size="9" font-weight="700" fill="#FFF" text-anchor="middle">ShopeePay</text>
  </g>

  <!-- Instructions text -->
  <text x="250" y="655" font-family="-apple-system, BlinkMacSystemFont, sans-serif" font-size="11" font-weight="600" fill="#DC2626" text-anchor="middle">Scan dengan aplikasi mobile banking / e-wallet apapun</text>
</svg>
SVG;
        file_put_contents($qrisDir.'/qris-dapur-katanya.svg', $qrisSvg);

        // Also create a sample payment proof for seeded orders
        $sampleProofSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 550" width="100%" height="100%">
  <rect width="400" height="550" fill="#F8FAFC" rx="16"/>
  <rect x="30" y="30" width="340" height="490" fill="#FFFFFF" rx="12" stroke="#E2E8F0"/>
  <circle cx="200" cy="90" r="30" fill="#10B981"/>
  <path d="M188,90 L196,98 L212,82" stroke="#FFFFFF" stroke-width="4" fill="none" stroke-linecap="round"/>
  <text x="200" y="150" font-family="sans-serif" font-size="18" font-weight="bold" fill="#0F172A" text-anchor="middle">Pembayaran Berhasil</text>
  <text x="200" y="175" font-family="sans-serif" font-size="12" fill="#64748B" text-anchor="middle">QRIS - Dapur KaTanya</text>
  <line x1="60" y1="200" x2="340" y2="200" stroke="#E2E8F0" stroke-dasharray="4"/>
  <text x="60" y="235" font-family="sans-serif" font-size="13" fill="#64748B">Nominal Transfer</text>
  <text x="340" y="235" font-family="sans-serif" font-size="16" font-weight="bold" fill="#DC2626" text-anchor="end">Rp 40.000</text>
  <text x="60" y="275" font-family="sans-serif" font-size="13" fill="#64748B">Metode</text>
  <text x="340" y="275" font-family="sans-serif" font-size="13" font-weight="600" fill="#0F172A" text-anchor="end">QRIS BCA</text>
  <text x="60" y="315" font-family="sans-serif" font-size="13" fill="#64748B">Tanggal & Waktu</text>
  <text x="340" y="315" font-family="sans-serif" font-size="12" fill="#0F172A" text-anchor="end">15 Nov 2026, 10:15 WIB</text>
  <rect x="60" y="350" width="280" height="40" rx="8" fill="#ECFDF5"/>
  <text x="200" y="375" font-family="sans-serif" font-size="13" font-weight="bold" fill="#059669" text-anchor="middle">TRANSAKSI BERHASIL</text>
</svg>
SVG;
        file_put_contents($proofDir.'/sample-proof.svg', $sampleProofSvg);

        // 2. Seed Admin User
        $adminEmail = config('services.admin.email');
        $adminPassword = config('services.admin.password');

        if (filled($adminEmail) && filled($adminPassword)) {
            User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => 'Admin Dapur KaTanya',
                    'password' => $adminPassword,
                    'role' => 'admin',
                ]
            );
        }
        // 3. Seed Initial 5 Products with multi-image
        $productsData = [
            [
                'name' => 'Dimsum Kuah Keju',
                'slug' => 'dimsum-kuah-keju',
                'category' => 'makanan',
                'badge' => 'Best Seller',
                'harga_jual' => 22000,
                'modal' => 14000,
                'description' => 'Dimsum kukus premium lembut dengan isian daging ayam melimpah, disiram kuah keju spesial yang kental, gurih, dan creamy. Disajikan hangat lengkap dengan chili oil terpisah.',
                'images' => [
                    ['path' => 'products/dimsum-1.svg', 'is_primary' => true, 'sort' => 1],
                    ['path' => 'products/dimsum-2.svg', 'is_primary' => false, 'sort' => 2],
                ],
            ],
            [
                'name' => 'Cireng Kuah Keju',
                'slug' => 'cireng-kuah-keju',
                'category' => 'makanan',
                'badge' => 'Signature',
                'harga_jual' => 18000,
                'modal' => 10000,
                'description' => 'Cireng renyah di luar kenyal lembut di dalam, berpadu sempurna dengan kuah keju lumer khas racikan Dapur KaTanya. Sensasi gurih dan sensasional di setiap gigitan!',
                'images' => [
                    ['path' => 'products/cireng-1.svg', 'is_primary' => true, 'sort' => 1],
                    ['path' => 'products/cireng-2.svg', 'is_primary' => false, 'sort' => 2],
                ],
            ],
            [
                'name' => 'Es.T (Esteh)',
                'slug' => 'es-t-esteh',
                'category' => 'minuman',
                'badge' => 'Segar Dingin',
                'harga_jual' => 5000,
                'modal' => 2000,
                'description' => 'Teh melati racikan tradisional dengan es batu melimpah dan manis pas. Teman setia penumpas dahaga saat menikmati camilan kuah keju.',
                'images' => [
                    ['path' => 'products/esteh-1.svg', 'is_primary' => true, 'sort' => 1],
                    ['path' => 'products/esteh-2.svg', 'is_primary' => false, 'sort' => 2],
                ],
            ],
            [
                'name' => 'S.Kom (Es Coklat)',
                'slug' => 's-kom-es-coklat',
                'category' => 'minuman',
                'badge' => 'Favorit Kampus',
                'harga_jual' => 12000,
                'modal' => 6500,
                'description' => 'Es coklat kental pekat dengan aroma kakao autentik. Bikin mood langsung naik dan semangat nugas skripsi sampai tuntas!',
                'images' => [
                    ['path' => 'products/skom-1.svg', 'is_primary' => true, 'sort' => 1],
                    ['path' => 'products/skom-2.svg', 'is_primary' => false, 'sort' => 2],
                ],
            ],
            [
                'name' => 'Paket Bundling: "Paket Cepat Lulus"',
                'slug' => 'paket-cepat-lulus',
                'category' => 'paket',
                'badge' => 'Hemat Bundling',
                'harga_jual' => 35000,
                'modal' => 21000,
                'description' => 'Paket bundling super hemat! Mendapatkan 1 porsi Dimsum Kuah Keju, 1 porsi Cireng Kuah Keju, plus pilihan minuman segar Es.T atau S.Kom. Solusi kenyang & bahagia di tanggal tua!',
                'images' => [
                    ['path' => 'products/bundling-1.svg', 'is_primary' => true, 'sort' => 1],
                    ['path' => 'products/bundling-2.svg', 'is_primary' => false, 'sort' => 2],
                ],
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $data) {
            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'category' => $data['category'],
                    'badge' => $data['badge'],
                    'harga_jual' => $data['harga_jual'],
                    'modal' => $data['modal'],
                    'description' => $data['description'],
                    'is_active' => true,
                ]
            );

            // Re-sync images
            $product->images()->delete();
            foreach ($data['images'] as $img) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $img['path'],
                    'is_primary' => $img['is_primary'],
                    'sort_order' => $img['sort'],
                ]);
            }

            $createdProducts[$product->slug] = $product;
        }

        // 4. Seed Initial Raw Material Purchases (Pembelian Bahan Baku)
        $materials = [
            ['purchase_date' => Carbon::today()->subDays(6)->toDateString(), 'material_name' => 'Daging Ayam Fillet Giling', 'quantity' => 10, 'unit' => 'kg', 'total_cost' => 380000, 'notes' => 'Pasar Induk'],
            ['purchase_date' => Carbon::today()->subDays(5)->toDateString(), 'material_name' => 'Keju Cheddar & Mozzarella Melt', 'quantity' => 8, 'unit' => 'box', 'total_cost' => 280000, 'notes' => 'Supplier Keju Prima'],
            ['purchase_date' => Carbon::today()->subDays(4)->toDateString(), 'material_name' => 'Tepung Tapioka & Bumbu Cireng', 'quantity' => 15, 'unit' => 'kg', 'total_cost' => 165000, 'notes' => 'Toko Bahan Kue Berkah'],
            ['purchase_date' => Carbon::today()->subDays(3)->toDateString(), 'material_name' => 'Bubuk Coklat Premium & Susu Evaporasi', 'quantity' => 6, 'unit' => 'kg', 'total_cost' => 210000, 'notes' => 'Grosir Minuman Manis'],
            ['purchase_date' => Carbon::today()->subDays(2)->toDateString(), 'material_name' => 'Teh Melati Wangi & Gula Pasir', 'quantity' => 12, 'unit' => 'kg', 'total_cost' => 180000, 'notes' => 'Supermarket Grosir'],
            ['purchase_date' => Carbon::today()->subDays(1)->toDateString(), 'material_name' => 'Cup Minuman & Paper Bowl Packaging', 'quantity' => 300, 'unit' => 'pcs', 'total_cost' => 195000, 'notes' => 'Pabrik Kemasan Eco'],
        ];

        foreach ($materials as $mat) {
            RawMaterialPurchase::create($mat);
        }

        // 5. Seed Initial Orders (representing realistic sales over the past few days)
        $sampleOrders = [
            [
                'order_number' => 'DKT-'.Carbon::today()->subDays(4)->format('Ymd').'-001',
                'customer_name' => 'Anisa Rahmawati',
                'customer_phone' => '081234567890',
                'pickup_date' => Carbon::today()->subDays(4)->toDateString(),
                'pickup_time' => '13:00',
                'notes' => 'Minta kuah kejunya dipisah ya kak',
                'payment_proof' => 'payment_proofs/sample-proof.svg',
                'status' => 'selesai',
                'created_at' => Carbon::today()->subDays(4)->setHour(11)->setMinute(20),
                'items' => [
                    ['slug' => 'dimsum-kuah-keju', 'qty' => 2],
                    ['slug' => 'es-t-esteh', 'qty' => 2],
                ],
            ],
            [
                'order_number' => 'DKT-'.Carbon::today()->subDays(3)->format('Ymd').'-002',
                'customer_name' => 'Bima Satria',
                'customer_phone' => '082198765432',
                'pickup_date' => Carbon::today()->subDays(3)->toDateString(),
                'pickup_time' => '15:30',
                'notes' => 'Pedas sedang untuk cirengnya',
                'payment_proof' => 'payment_proofs/sample-proof.svg',
                'status' => 'selesai',
                'created_at' => Carbon::today()->subDays(3)->setHour(14)->setMinute(10),
                'items' => [
                    ['slug' => 'cireng-kuah-keju', 'qty' => 2],
                    ['slug' => 's-kom-es-coklat', 'qty' => 2],
                ],
            ],
            [
                'order_number' => 'DKT-'.Carbon::today()->subDays(2)->format('Ymd').'-003',
                'customer_name' => 'Cindy Natalia',
                'customer_phone' => '085712348899',
                'pickup_date' => Carbon::today()->subDays(2)->toDateString(),
                'pickup_time' => '12:00',
                'notes' => 'Buat makan siang bareng temen sekelas',
                'payment_proof' => 'payment_proofs/sample-proof.svg',
                'status' => 'selesai',
                'created_at' => Carbon::today()->subDays(2)->setHour(10)->setMinute(45),
                'items' => [
                    ['slug' => 'paket-cepat-lulus', 'qty' => 3],
                    ['slug' => 'es-t-esteh', 'qty' => 3],
                ],
            ],
            [
                'order_number' => 'DKT-'.Carbon::today()->subDays(1)->format('Ymd').'-004',
                'customer_name' => 'Dwi Prasetyo',
                'customer_phone' => '087855443322',
                'pickup_date' => Carbon::today()->subDays(1)->toDateString(),
                'pickup_time' => '16:00',
                'notes' => 'Diambil pas pulang kuliah',
                'payment_proof' => 'payment_proofs/sample-proof.svg',
                'status' => 'selesai',
                'created_at' => Carbon::today()->subDays(1)->setHour(14)->setMinute(30),
                'items' => [
                    ['slug' => 'dimsum-kuah-keju', 'qty' => 1],
                    ['slug' => 'cireng-kuah-keju', 'qty' => 1],
                    ['slug' => 's-kom-es-coklat', 'qty' => 1],
                ],
            ],
            [
                'order_number' => 'DKT-'.Carbon::today()->format('Ymd').'-005',
                'customer_name' => 'Eka Wahyuni',
                'customer_phone' => '089912345678',
                'pickup_date' => Carbon::today()->toDateString(),
                'pickup_time' => '17:00',
                'notes' => 'Bukti bayar sudah diupload kak, mohon diproses ya',
                'payment_proof' => 'payment_proofs/sample-proof.svg',
                'status' => 'diproses',
                'created_at' => Carbon::today()->setHour(9)->setMinute(15),
                'items' => [
                    ['slug' => 'paket-cepat-lulus', 'qty' => 2],
                ],
            ],
            [
                'order_number' => 'DKT-'.Carbon::today()->format('Ymd').'-006',
                'customer_name' => 'Fajar Pratama',
                'customer_phone' => '081399887766',
                'pickup_date' => Carbon::today()->addDay()->toDateString(),
                'pickup_time' => '11:30',
                'notes' => 'Pesanan untuk besok siang',
                'payment_proof' => 'payment_proofs/sample-proof.svg',
                'status' => 'menunggu_verifikasi',
                'created_at' => Carbon::today()->setHour(10)->setMinute(5),
                'items' => [
                    ['slug' => 'dimsum-kuah-keju', 'qty' => 3],
                    ['slug' => 's-kom-es-coklat', 'qty' => 2],
                ],
            ],
        ];

        foreach ($sampleOrders as $oData) {
            $totalOmset = 0;
            $totalModal = 0;

            $order = Order::create([
                'order_number' => $oData['order_number'],
                'customer_name' => $oData['customer_name'],
                'customer_phone' => $oData['customer_phone'],
                'pickup_date' => $oData['pickup_date'],
                'pickup_time' => $oData['pickup_time'],
                'notes' => $oData['notes'],
                'payment_method' => 'QRIS',
                'payment_proof' => $oData['payment_proof'],
                'total_omset' => 0,
                'total_modal' => 0,
                'net_profit' => 0,
                'status' => $oData['status'],
                'created_at' => $oData['created_at'],
                'updated_at' => $oData['created_at'],
            ]);

            foreach ($oData['items'] as $item) {
                $p = $createdProducts[$item['slug']];
                $subtotal = $p->harga_jual * $item['qty'];
                $subtotalModal = $p->modal * $item['qty'];
                $subtotalProfit = $subtotal - $subtotalModal;

                $totalOmset += $subtotal;
                $totalModal += $subtotalModal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p->id,
                    'product_name' => $p->name,
                    'quantity' => $item['qty'],
                    'price' => $p->harga_jual,
                    'modal' => $p->modal,
                    'subtotal' => $subtotal,
                    'subtotal_modal' => $subtotalModal,
                    'subtotal_profit' => $subtotalProfit,
                    'created_at' => $oData['created_at'],
                    'updated_at' => $oData['created_at'],
                ]);
            }

            $order->update([
                'total_omset' => $totalOmset,
                'total_modal' => $totalModal,
                'net_profit' => $totalOmset - $totalModal,
            ]);
        }
    }
}

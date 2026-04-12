# Beauty Caré E-Commerce

## Cara jalanin di XAMPP/Laragon
1. Buat folder project `Beauty Care`
2. Copy semua file ke struktur yang sesuai
3. Jalankan `composer install`
4. Import `db/db.sql`
5. Pastikan document root ke folder `public`
6. Buat folder upload:
   - `public/uploads/products`
   - `public/uploads/payments`
7. Pastikan kedua folder writable
8. Akses: `http://localhost/Beauty care/public`

## Login admin
- Email: admin@glowe.test
- Password: admin123

## Fitur yang sudah ada
- login/register
- katalog multi-brand
- global search
- cart multi-item
- alamat user
- checkout + custom ongkir
- pembayaran transfer bank
- upload bukti transfer
- verifikasi pembayaran admin
- invoice HTML + PDF Dompdf
- CRUD produk
- CRUD brand, kategori, ongkir
- laporan bulanan
- stock movement
- WhatsApp order helper

## Catatan
- Ganti `BASE_URL` jika folder project berubah
- Untuk production, set cookie secure dan gunakan HTTPS
- Password admin seed bisa kamu reset manual bila perlu

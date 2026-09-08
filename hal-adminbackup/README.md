# Dashboard Admin FIKES - Fixed Path CSS & JavaScript

Versi ini memperbaiki masalah CSS/JavaScript yang tidak terbaca saat membuka halaman dari folder `modules`.

## Cara install

1. Ekstrak folder `fikes_admin_dashboard` ke:
   `C:\xampp\htdocs\`

2. Pastikan struktur awalnya:
   `C:\xampp\htdocs\fikes_admin_dashboard\`

3. Jalankan Apache dan MySQL di XAMPP.

4. Import:
   `database/fikes.sql`
   ke phpMyAdmin.

5. Buka:
   `http://localhost/fikes_admin_dashboard/`

   atau:
   `http://localhost/fikes_admin_dashboard/admin/login.php`

## Login

Username: `admin`
Password: `admin123`

## Perbaikan

Path CSS dan JavaScript sekarang dibuat berdasarkan lokasi project secara otomatis, sehingga tetap benar saat membuka:

- Dashboard
- Daftar Dosen
- Program Studi
- Visi & Misi
- Struktur Organisasi
- Akreditasi
- Logo
- Kemahasiswaan
- Berita
- Pengaturan

Menu sidebar dan tombol Logout juga menggunakan path yang benar dari semua halaman.

Jika browser masih menampilkan tampilan lama, lakukan hard refresh dengan `Ctrl + F5`.

## Teknologi

- PHP native/prosedural
- MySQL
- mysqli/PDO sesuai modul asli
- HTML
- CSS
- JavaScript dasar

Catatan: password demo menggunakan MD5 sesuai project awal. Untuk website produksi sebaiknya migrasikan ke `password_hash()` dan `password_verify()`.

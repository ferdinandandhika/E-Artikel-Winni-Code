Website E-Artikel Winnie Code adalah platform pengelolaan artikel yang dilengkapi dengan panel admin untuk mengelola konten secara terstruktur.

Dapat dilihat melalui website https://eartikelwinnicode.freesite.online/

#Panduan Instalasi E-Artikel Winnie Code#
Persyaratan Sistem
XAMPP (dengan PHP dan MySQL)
Web Browser
Text Editor

Langkah-langkah Instalasi
1. Persiapan Database
1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Buat database baru dengan nama winnicode
3. Import file database yang tersedia (biasanya dengan format .sql)

2. Instalasi Aplikasi
1. Copy folder project ke direktori:
C:\xampp\htdocs\winnicode
Pastikan struktur folder seperti berikut:
winnicode/
├── admin/
├── assets/
├── config/
├── images/
└── index.php

3. Konfigurasi
Buka file config/koneksi.php
Sesuaikan konfigurasi database jika diperlukan:
$host = "localhost";
$user = "root";
$pass = "";
$db   = "winnicode";

5. Menjalankan Aplikasi
1. Nyalakan XAMPP (Apache dan MySQL)
Buka browser
Akses aplikasi melalui:
Frontend: http://localhost/winnicode
Admin: http://localhost/winnicode/admin

5. Login Admin
Username: (ferdi)
Password: (ferdi)

Fitur Utama
Manajemen artikel (tambah, edit, hapus)
Upload gambar
Pengaturan kategori
Status publikasi artikel
Pengaturan tanggal artikel

Catatan
Pastikan folder images memiliki permission write
Ukuran maksimal upload gambar disesuaikan dengan konfigurasi PHP
Backup database secara berkala
Untuk informasi lebih lanjut atau jika mengalami kendala, silakan hubungi administrator.

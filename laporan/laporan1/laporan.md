# Laporan Modul 1: Perkenalan Laravel  
**Mata Kuliah:** Workshop Web Lanjut  
**Nama:** Muhammad Dhiyaul Atha  
**NIM:** 2024573010075  
**Kelas:** TI 2B  

---

## Abstrak  
Laporan ini membahas pengenalan framework Laravel sebagai fondasi dalam pengembangan web modern berbasis PHP. Tujuan dari laporan ini adalah memahami struktur dasar Laravel, komponen utama, serta pola kerja Model-View-Controller (MVC) yang menjadi tulang punggung Laravel.  

---

## 1. Pendahuluan  
Laravel adalah framework web PHP yang populer, bersifat open-source, dan dirancang untuk membangun aplikasi web modern yang skalabel dan aman.  
Laravel merupakan framework high-level yang bersifat opinionated (punya aturan dan konvensi tertentu). Laravel juga mengikuti arsitektur Model-View-Controller (MVC) dengan tujuan mempermudah sekaligus membuat proses pengembangan menjadi lebih efisien dan menyenangkan.  

### Apa Itu Laravel?  
Framework web adalah kumpulan pustaka (library) dan alat (tools) yang membantu pengembang membangun aplikasi lebih cepat dengan menyediakan fitur-fitur umum seperti:  
- Routing  
- Manajemen basis data  
- Autentikasi (authentication)  

Laravel dibuat oleh Taylor Otwell pada tahun 2011 dan hingga kini menjadi salah satu framework PHP paling populer di dunia. Laravel dikenal karena:  
- Sintaks yang ekspresif dan elegan  
- Fitur yang lengkap dan kuat  
- Komunitas yang aktif  

Laravel digunakan untuk membangun berbagai jenis aplikasi, mulai dari blog sederhana hingga sistem perusahaan yang kompleks.  

Biasanya, saat orang belajar PHP, ada dua jalur umum:  
- **WordPress** → Cocok untuk website berbasis konten atau blog  
- **Laravel** → Cocok untuk aplikasi yang kompleks  

Tidak ada larangan untuk mempelajari keduanya. 

### Karakteristik Utama Laravel:
- Arsitektur MVC
- Tersedia banyak command lewat Artisan CLI
- Routing yang simpel
- ORM bawaan (Eloquent)
- Blade templating engine
- Migration & seeder untuk database

### Laravel cocok digunakan untuk:
- Aplikasi web skala kecil hingga besar
- Sistem informasi internal
- Web API
- E-Commerce, dashboard admin, dsb.

---

## 2. Komponen Utama Laravel  
- **Blade (templating):** Blade adalah engine templating Laravel yang memungkinkan penulisan tampilan HTML dengan sintaks sederhana dan fleksibel, serta mendukung inheritance antar layout.  
- **Eloquent (ORM):** Eloquent adalah Object-Relational Mapping Laravel yang memudahkan interaksi dengan database melalui model PHP, tanpa perlu menulis query SQL secara langsung.  
- **Routing:** Routing berfungsi untuk menentukan respon terhadap permintaan URL tertentu, mengarahkan request ke fungsi atau controller yang sesuai.  
- **Controllers:** Controller adalah komponen yang menjembatani antara model dan view, berisi logika untuk memproses permintaan dari pengguna.  
- **Migrations & Seeders:** Migrations digunakan untuk mengelola struktur tabel database secara versioned, sedangkan seeders digunakan untuk mengisi data awal ke dalam tabel.  
- **Artisan CLI:** Artisan adalah command line interface Laravel yang menyediakan berbagai perintah untuk mempercepat pengembangan, seperti membuat controller, migration, dan menjalankan server lokal.  
- **Testing (PHPUnit):** Laravel terintegrasi dengan PHPUnit untuk melakukan testing otomatis terhadap aplikasi, baik untuk unit testing maupun feature testing.  

---

## 3. Struktur Folder & File pada Project Laravel  
- **app/** – kode aplikasi (Models, Controllers, Middleware)  
- **bootstrap/** – bootstrap & konfigurasi awal  
- **config/** – file konfigurasi aplikasi (database, mail, cache, dll.)  
- **database/** – migrasi, factories, seeders  
- **public/** – akses publik (index.php, assets)  
- **resources/** – Blade templates, bahasa, asset frontend  
- **routes/** – definisi routing (web.php, api.php)  
- **storage/** – cache, log, session, file unggahan  
- **tests/** – file pengujian aplikasi  
- **vendor/** – dependency dari Composer  
- **.env** – konfigurasi environment sensitif  

---
    
## 4. Diagram MVC dan Cara Kerjanya  

![Diagram MVC](gambar/img.png)  

**Penjelasan Diagram:**  
1. User (Browser) mengirimkan request ke aplikasi.  
2. Routing menerima request dan meneruskannya ke Controller.  
3. Controller menjalankan logika bisnis—menggunakan Model (Eloquent) untuk mengambil atau menyimpan data.  
4. Model berinteraksi dengan Database.  
5. Setelah data siap, Controller mengirimkan data ke View (Blade) untuk dirender.  
6. View menyajikan hasil akhir, lalu Response dikirimkan kembali ke User.  

---

## 6. Kelebihan & Kekurangan (Refleksi Singkat)  
**Kelebihan Laravel:**  
- Sintaks ekspresif dan elegan, fitur lengkap, serta komunitas yang aktif.  
- Opinionated framework: menyediakan banyak fitur bawaan (routing, ORM, keamanan, dll) sehingga pengembangan jadi lebih cepat dan konsisten.  

**Tantangan bagi Pemula:**  
- Banyak fitur dan kompleksitas bisa membingungkan jika baru mengenal OOP atau konsep MVC.  
- Struktur project dan konvensi Laravel yang ketat dapat terasa membatasi bagi yang terbiasa dengan kebebasan penuh dalam coding.  

---

## 7. Referensi  
- Modul 1 – *Introduction to Laravel* (HackMD) — https://hackmd.io/@mohdrzu/By0Wc1Dule  
- Dokumentasi Resmi Laravel — https://laravel.com/docs  

---

![Laravel](https://laravel.com/assets/img/components/logo-laravel.svg)

<h1 align="center">Aplikasi Silsilah Keluarga</h1>

> **Development in progress**  
> Dalam proses development, perubahan struktur tabel akan **diubah langsung pada file migration** yang sesuai.

## Tentang
Aplikasi silsilah keluarga untuk mempermudah pendataan keluarga kita.

## Pemanfaatan
1. Melihat Silsilah keluarga
2. Melihat data ahli waris

## Fitur
Aplikasi ini menggunakan Bahasa Indonesia dan Bahasa Sunda, diatur pada `config.locale`.

### Konsep
1. Satu orang memiliki satu ayah (belum sebagai tentu orang tua)
2. Satu orang memiliki satu ibu (belum sebagai tentu orang tua)
3. satu orang memiliki satu orang tua
4. Satu orang memiliki 0 s/d beberapa anak
5. Satu orang bisa memiliki pasangan (Istri/Suami)
6. Satu pasangan bisa memiliki 0 s/d beberapa anak

### Input ke sistem
1. Input Nama dan Jenis Kelamin
2. Tambah Ayah
3. Tambah Ibu
4. Tambah Pasangan
5. Tambah Anak

### Data Orang
1. Nama Panggilan
2. Jenis Kelamin
3. Nama Lengkap
4. Tanggal Lahir
5. Tanggal Meninggal (atau cukup tahun)
6. Alamat
7. Telp
8. Email

## Cara Install
1. Clone Repo, pada terminal : `git clone https://github.com/hildafdan/kopi.git`
2. `cd kopi`
3. `composer install`
4. `cp .env.example .env`
5. `php artisan key:generate`
6. Buat **database pada mysql** untuk aplikasi ini
7. **Setting database** pada file `.env`
8. `php artisan migrate`
9. `php artisan serve`
10. Selesai (Register user baru untuk mulai mengisi silsilah).

## Testing
Ingin mencoba automated testingnya? Silakan ketik perintah pada terminal: `vendor/bin/phpunit`

## Screenshots

## License
The Laravel framework is open-sourced software licensed under the [MIT license](LICENSE).

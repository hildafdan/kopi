<?php

return [

    /*
    |---------------------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi
    |---------------------------------------------------------------------------------------
    |
    | Baris bahasa berikut ini berisi standar pesan kesalahan yang digunakan oleh
    | kelas validasi. Beberapa aturan mempunyai multi versi seperti aturan 'size'.
    | Jangan ragu untuk mengoptimalkan setiap pesan yang ada di sini.
    |
    */

    "accepted"             => "Eusi :attribute kudu ditarima.",
    "active_url"           => "Eusi :attribute lain URL nu valid.",
    "after"                => "Eusi :attribute kudu tanggal saatos :date.",
    'after_or_equal'       => 'Eusi :attribute kudu tanggal saatos atawa sarua jeung :date.',
    "alpha"                => "Eusi :attribute ukur bisa ngandung hurup.",
    "alpha_dash"           => "Eusi :attribute ukur bisa ngandung hurup, angka, jeung strip.",
    "alpha_num"            => "Eusi :attributeukur bisa ngandung hurup jeung angka.",
    "array"                => "Eusi :attribute kudu mangrupa Array.",
    "before"               => "Eusi :attribute kudu tanggal saacan :date.",
    'before_or_equal'      => 'Eusi :attribute kudu tanggal saacan atawa sarua jeung :date.',
    "between"              => [
        "numeric" => "Eusi :attribute kudu antara :min jeung :max.",
        "file"    => "Eusi :attribute kudu antara :min jeung :max kilobytes.",
        "string"  => "Eusi :attribute kudu antara :min jeung :max karakter.",
        "array"   => "Eusi :attribute kudu antara :min jeung :max item.",
    ],
    "boolean"              => "Eusi :attribute kudu mangrupa True atawa False",
    "confirmed"            => "Konpirmasi :attribute teu cocok.",
    "date"                 => "Eusi :attribute lain tanggal anu valid.",
    "date_format"          => "Eusi :attributeteu cocok jeung format :format.",
    "different"            => "Eusi :attribute jeung :other kudu beda.",
    "digits"               => "Eusi :attribute kudu mangrupa angka :digits.",
    "digits_between"       => "Eusi :attribute kudu antara angka :min jeung :max.",
    'dimensions'           => 'Dimensi gambar :attribute teu valid.',
    'distinct'             => 'Eusi :attribute kaduplikat.',
    "email"                => "Eusi :attribute kudu mangrupa alamat email nu valid.",
    "exists"               => "Eusi :attribute yang dipilih tidak valid.",
    'file'                 => 'Eusi :attribute kudu mangrupa file.',
    "filled"               => "Bidang Eusi :attribute wajib dieusian.",
    "image"                => "Eusi :attribute kudu mangrupa gambar.",
    "in"                   => "Eusi :attribute anu dipilih teu valid.",
    'in_array'             => 'Eusi :attribute teu kapendak dina :other.',
    "integer"              => "Eusi :attribute kudu mangrupa bilangan buleud.",
    "ip"                   => "Eusi :attribute kudu mangrupa alamat IP nu valid.",
    'ipv4'                 => 'Eusi :attribute kudu mangrupa alamat IPv4 nu valid.',
    'ipv6'                 => 'Eusi :attribute kudu mangrupa alamat IPv6 nu valid.',
    'json'                 => 'Eusi :attribute kudu mangrupa string JSON nu valid.',
    "max"                  => [
        "numeric" => "Eusi :attribute kuduna teu leuwih ti :max.",
        "file"    => "Eusi :attribute kuduna teu leuwih ti :max kilobytes.",
        "string"  => "Eusi :attribute kuduna teu leuwih ti :max karakter.",
        "array"   => "Eusi :attribute kuduna teu leuwih ti :max item.",
    ],
    "mimes"                => "Eusi :attribute kudu dokumen anu jenisna : :values.",
    'mimetypes'            => 'Eusi :attribute kudu dokumen anu jenisna : :values.',
    "min"                  => [
        "numeric" => "Eusi :attribute minimal kudu :min.",
        "file"    => "Eusi :attribute minimal kudu :min kilobytes.",
        "string"  => "Eusi :attribute minimal kudu :min karakter.",
        "array"   => "Eusi :attribute minimal kudu :min item.",
    ],
    "not_in"               => "Eusi :attribute nu dipilih teu valid.",
    "numeric"              => "Eusi :attribute kudu mangrupa angka.",
    'present'              => 'Eusi :attribute kudu aya.',
    "regex"                => "Format eusi :attribute teu valid.",
    "required"             => "Wajib dieusian.",
    "required_if"          => "Bidang eusi :attribute wajib dieusian lamun :other mangrupakeun :value.",
    'required_unless'      => 'Bidang eusi :attribute wajib dieusian kecuali :other eusina :values.',
    "required_with"        => "Bidang eusi :attribute wajib dieusian lamun aya :values.",
    "required_with_all"    => "Bidang eusi :attribute wajib dieusian lamun aya :values.",
    "required_without"     => "Bidang eusi :attribute wajib dieusian lamun euweuh :values.",
    "required_without_all" => "Bidang eusi :attribute wajib dieusian lamun eweuh aya :values.",
    "same"                 => "Eusi :attribute jeung :other kudu akur.",
    "size"                 => [
        "numeric" => "Eusi :attribute ukurana kudu :size.",
        "file"    => "Eusi :attribute ukurana kudu :size kilobyte.",
        "string"  => "Eusi :attribute ukurana kudu :size karakter.",
        "array"   => "Eusi :attribute kudu mengandung :size item.",
    ],
    "string"               => "Eusi :attribute kudu mangrupa string.",
    "timezone"             => "Eusi :attribute kudu mangrupa zona waktu nu valid.",
    "unique"               => "Eusi :attribute geus aya saacana.",
    'uploaded'             => 'Eusi :attribute geus diupload.',
    "url"                  => "Format eusi :attribute teu valid.",

    /*
    |---------------------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi Kustom
    |---------------------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan pesan validasi kustom untuk atribut dengan menggunakan
    | konvensi "attribute.rule" dalam penamaan baris. Hal ini membuat cepat dalam
    | menentukan spesifik baris bahasa kustom untuk aturan atribut yang diberikan.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |---------------------------------------------------------------------------------------
    | Kustom Validasi Atribut
    |---------------------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan untuk menukar atribut 'place-holders'
    | dengan sesuatu yang lebih bersahabat dengan pembaca seperti Alamat Surel daripada
    | "surel" saja. Ini benar-benar membantu kita membuat pesan sedikit bersih.
    |
    */

    'attributes' => [],

];

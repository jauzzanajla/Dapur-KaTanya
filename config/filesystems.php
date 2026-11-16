<?php

// S3 hanya dipakai bila bucket DAN key/secret terisi. Tanpa key, AWS SDK mencari
// kredensial ke metadata server EC2 dan menggantung sampai timeout 30 detik.
$useS3 = env('AWS_BUCKET') && env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY');

$s3Disk = [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),
    'endpoint' => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
    // AWS SDK versi baru menambahkan checksum CRC32 + upload "aws-chunked" untuk file/stream.
    // Supabase Storage belum mendukung itu, sehingga PUT file ditolak (403). Matikan kecuali wajib.
    'request_checksum_calculation' => 'when_required',
    'response_checksum_validation' => 'when_required',
    'throw' => true,
    'report' => true,
];

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Media Disk
    |--------------------------------------------------------------------------
    |
    | Disk untuk gambar produk & QRIS. Isi MEDIA_DISK=s3 untuk Supabase Storage.
    |
    */

    'media_disk' => env('MEDIA_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        // Bila AWS_BUCKET diisi, disk "public" ikut memakai Supabase Storage (S3),
        // supaya kode lama yang memakai disk('public') tetap tersimpan ke Supabase.
        'public' => $useS3
            ? $s3Disk
            : [
                'driver' => 'local',
                'root' => storage_path('app/public'),
                'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
                'visibility' => 'public',
                'throw' => false,
                'report' => false,
            ],

        's3' => $s3Disk,

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
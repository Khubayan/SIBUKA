<?php

return [
    'school_name' => env('SIBUKA_SCHOOL_NAME', 'SMK TI Bali Global Klungkung'),

    'point_thresholds' => [
        'warning' => (int) env('SIBUKA_POINT_WARNING', -25),
        'sp1' => (int) env('SIBUKA_POINT_SP1', -50),
        'sp2' => (int) env('SIBUKA_POINT_SP2', -75),
        'sp3' => (int) env('SIBUKA_POINT_SP3', -100),
    ],

    'upload' => [
        'max_size_kb' => 2048,
        'allowed_mimes' => ['jpg', 'jpeg', 'png', 'pdf'],
    ],

    'backup' => [
        'retention_days' => (int) env('SIBUKA_BACKUP_RETENTION_DAYS', 7),
        'mysqldump_path' => env('SIBUKA_MYSQLDUMP_PATH', 'mysqldump'),
    ],
];

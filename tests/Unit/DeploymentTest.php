<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

test('backup command creates sqlite backup file', function () {
    $originalDefault = config('database.default');
    $dbPath = storage_path('framework/testing-backup.sqlite');
    File::put($dbPath, 'sqlite-backup-test');

    Config::set('database.connections.sqlite_backup_test', [
        'driver' => 'sqlite',
        'database' => $dbPath,
        'prefix' => '',
        'foreign_key_constraints' => true,
    ]);
    Config::set('database.default', 'sqlite_backup_test');

    try {
        $this->artisan('sibuka:backup-database')
            ->assertSuccessful();

        $backups = File::glob(storage_path('backups/sqlite_*.sqlite'));
        expect($backups)->not->toBeEmpty();
    } finally {
        Config::set('database.default', $originalDefault);
        File::delete($dbPath);
    }
});

test('backup command is scheduled daily', function () {
    $this->artisan('schedule:list')
        ->expectsOutputToContain('sibuka:backup-database')
        ->assertSuccessful();
});

test('deploy artifacts exist', function () {
    expect(file_exists(base_path('deploy/deploy.sh')))->toBeTrue();
    expect(file_exists(base_path('deploy/supervisor-sibuka-worker.conf')))->toBeTrue();
    expect(file_exists(base_path('.env.production.example')))->toBeTrue();
    expect(file_exists(base_path('docs/DEPLOYMENT.md')))->toBeTrue();
    expect(file_exists(base_path('docs/UAT_CHECKLIST.md')))->toBeTrue();
    expect(file_exists(base_path('docs/USER_MANUAL.md')))->toBeTrue();
});

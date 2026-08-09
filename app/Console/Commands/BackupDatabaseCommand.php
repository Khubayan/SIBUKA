<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'sibuka:backup-database
                            {--retention= : Hari retensi backup (default dari config)}';

    protected $description = 'Backup database ke folder storage/backups';

    public function handle(): int
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        $backupDir = storage_path('backups');

        if (! File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_His');

        $path = match ($driver) {
            'sqlite' => $this->backupSqlite($connection, $backupDir, $timestamp),
            'mysql' => $this->backupMysql($connection, $backupDir, $timestamp),
            default => null,
        };

        if ($path === null) {
            $this->error("Driver database [{$driver}] tidak didukung untuk backup otomatis.");

            return self::FAILURE;
        }

        $this->info("Backup berhasil: {$path}");

        $retention = (int) ($this->option('retention') ?? config('sibuka.backup.retention_days', 7));
        $deleted = $this->pruneOldBackups($backupDir, $retention);

        if ($deleted > 0) {
            $this->line("Menghapus {$deleted} backup lama (>{$retention} hari).");
        }

        return self::SUCCESS;
    }

    private function backupSqlite(string $connection, string $backupDir, string $timestamp): ?string
    {
        $source = config("database.connections.{$connection}.database");

        if ($source === ':memory:' || ! is_readable($source)) {
            $this->error('SQLite :memory: atau file tidak dapat dibaca — backup dilewati.');

            return null;
        }

        $dest = "{$backupDir}/sqlite_{$timestamp}.sqlite";

        if (! copy($source, $dest)) {
            $this->error('Gagal menyalin file SQLite.');

            return null;
        }

        return $dest;
    }

    private function backupMysql(string $connection, string $backupDir, string $timestamp): ?string
    {
        $config = config("database.connections.{$connection}");
        $dest = "{$backupDir}/mysql_{$config['database']}_{$timestamp}.sql";

        $mysqldump = config('sibuka.backup.mysqldump_path', 'mysqldump');

        $process = new Process([
            $mysqldump,
            '--host='.$config['host'],
            '--port='.$config['port'],
            '--user='.$config['username'],
            '--result-file='.$dest,
            '--single-transaction',
            '--quick',
            '--lock-tables=false',
            $config['database'],
        ]);

        if ($config['password']) {
            $process->setEnv(['MYSQL_PWD' => $config['password']]);
        }

        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('mysqldump gagal: '.$process->getErrorOutput());

            return null;
        }

        return $dest;
    }

    private function pruneOldBackups(string $backupDir, int $retentionDays): int
    {
        if ($retentionDays <= 0) {
            return 0;
        }

        $cutoff = now()->subDays($retentionDays)->getTimestamp();
        $deleted = 0;

        foreach (File::files($backupDir) as $file) {
            if ($file->getMTime() < $cutoff) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        return $deleted;
    }
}

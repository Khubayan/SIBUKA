# Panduan Deployment SIBUKA

Dokumen ini melengkapi Fase 7 di [IMPLEMENTATION_DOCUMENTATION.md](./IMPLEMENTATION_DOCUMENTATION.md).

## Prasyarat Server

| Software | Versi |
|----------|-------|
| Ubuntu / Debian | 22.04+ |
| PHP | 8.3+ (php-fpm, extensions: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo) |
| MySQL / MariaDB | 8.0 / 10.6 |
| Nginx | 1.18+ |
| Composer | 2.x |
| Supervisor | 4.x |
| Certbot (SSL) | opsional |

## Checklist Pre-Deploy

- [ ] Semua test pass: `php artisan test`
- [ ] `.env` produksi dikonfigurasi (`APP_DEBUG=false`, `APP_ENV=production`)
- [ ] `php artisan key:generate` di server
- [ ] Database `sibuka` dan user MySQL dibuat
- [ ] Asset SB Admin 2 ada di `public/assets/sb-admin-2/`
- [ ] SSL certificate terpasang
- [ ] Backup strategy aktif (cron + `sibuka:backup-database`)

## Instalasi Awal

```bash
# Clone / upload ke server
cd /var/www
git clone <repo-url> sibuka
cd sibuka

# Environment
cp .env.production.example .env
php artisan key:generate
# Edit .env — DB, MAIL, APP_URL

# Dependencies
composer install --optimize-autoloader --no-dev

# Database
php artisan migrate --force
php artisan db:seed --class=DemoDataSeeder   # opsional UAT

# Storage & cache
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permission
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Deploy Update (Rilis Berikutnya)

```bash
cd /var/www/sibuka
./deploy/deploy.sh
```

Atau manual mengikuti langkah di `deploy/deploy.sh`.

## Queue Worker (Supervisor)

```bash
sudo cp deploy/supervisor-sibuka-worker.conf /etc/supervisor/conf.d/
# Edit path di file jika perlu
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sibuka-worker:*
```

Verifikasi: `sudo supervisorctl status sibuka-worker:*`

## Scheduler & Backup Otomatis

```bash
# Tambahkan cron (lihat deploy/cron.example)
crontab -e
```

Scheduler menjalankan `sibuka:backup-database` setiap hari pukul 02:00.
Backup disimpan di `storage/backups/` dengan retensi default 7 hari.

Manual backup:

```bash
php artisan sibuka:backup-database
php artisan sibuka:backup-database --retention=14
```

## Nginx

```bash
sudo cp deploy/nginx-sibuka.conf.example /etc/nginx/sites-available/sibuka
sudo ln -s /etc/nginx/sites-available/sibuka /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

## SSL (Let's Encrypt)

```bash
sudo certbot --nginx -d sibuka.smk-tibaliglobal.sch.id
```

## Verifikasi Post-Deploy

| Cek | Perintah / URL |
|-----|----------------|
| Health check | `curl -I https://<domain>/up` |
| Login admin | Browser → `/login` |
| Queue worker | `supervisorctl status` |
| Scheduler | `php artisan schedule:list` |
| Backup | `ls -la storage/backups/` |

## Rollback

```bash
php artisan down
git checkout <tag-sebelumnya>
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

Restore database dari backup:

```bash
mysql -u sibuka_user -p sibuka < storage/backups/mysql_sibuka_YYYY-MM-DD_HHMMSS.sql
```

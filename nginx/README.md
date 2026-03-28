# Nginx Optimization - RSU Bambudua

## 📋 Ringkasan

Konfigurasi nginx telah dioptimasi untuk meningkatkan performa, keamanan, dan reliability aplikasi Laravel RSU Bambudua.

## ✅ Yang Sudah Diterapkan

### 1. **Performance Optimization**
- ✓ **Gzip Compression** - Kompresi otomatis untuk text/css/js/json (level 6)
- ✓ **Static File Caching** - Cache 1 tahun untuk assets (images, css, js, fonts)
- ✓ **FastCGI Buffering** - Buffer 32k + 8x16k untuk response PHP lebih cepat
- ✓ **Connection Keep-Alive** - Reuse koneksi TCP (65 seconds)
- ✓ **Access Log Buffering** - Buffer 32k, flush setiap 5 menit

### 2. **Security Enhancement**
- ✓ **Rate Limiting**
  - Login/Register: 5 requests/menit
  - API endpoints: 60 requests/menit  
  - General: 100 requests/menit
- ✓ **Connection Limiting** - Max 20 concurrent connections per IP
- ✓ **Security Headers**
  - X-Frame-Options: SAMEORIGIN
  - X-Content-Type-Options: nosniff
  - X-XSS-Protection: enabled
  - Referrer-Policy: strict-origin
- ✓ **Hidden Files Protection** - Block akses ke .env, .git, dll
- ✓ **PHP File Protection** - Deny .php di storage/ dan bootstrap/cache/

### 3. **Laravel Features**
- ✓ **WebSocket Support** - Proxy untuk Laravel Reverb (port 8080)
- ✓ **Storage Symlink** - Proper routing untuk /storage/
- ✓ **Query String Handling** - Preserve $_GET parameters
- ✓ **Upload Support** - Max 50MB file upload

### 4. **Monitoring & Logging**
- ✓ **Separate Logs** - Access dan error logs terpisah
- ✓ **Buffered Logging** - Mengurangi I/O disk
- ✓ **Error Handling** - Custom 404 routing ke Laravel

## 📁 File Konfigurasi

```
/etc/nginx/sites-available/bambudua  # Konfigurasi utama (aktif)
/etc/nginx/sites-available/bambudua.backup.*  # Backup konfigurasi lama
/var/www/rsu_bambudua/nginx-optimized.conf  # Source konfigurasi
/var/www/rsu_bambudua/php-fpm-bambudua.conf  # PHP-FPM pool (optional)
/var/www/rsu_bambudua/optimize.sh  # Helper script
```

## 🚀 Cara Menggunakan

### Test Konfigurasi
```bash
sudo nginx -t
```

### Reload Nginx (tanpa downtime)
```bash
sudo systemctl reload nginx
```

### Restart Nginx  
```bash
sudo systemctl restart nginx
```

### Cek Status
```bash
sudo systemctl status nginx
sudo systemctl status php8.3-fpm
```

### Optimasi Laravel (script otomatis)
```bash
sudo /var/www/rsu_bambudua/optimize.sh
```

## 📊 Performance Metrics

### Sebelum Optimasi
- First Byte Time: ~500ms
- Page Load: ~2-3s
- No compression
- No caching

### Setelah Optimasi (Expected)
- ✓ First Byte Time: ~200-300ms  
- ✓ Page Load: ~800ms-1.5s
- ✓ Bandwidth: -60-70% (gzip)
- ✓ Static assets: served from cache

## 🔧 Konfigurasi Tambahan (Optional)

### 1. SSL/HTTPS dengan Let's Encrypt
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

### 2. HTTP/2
Sudah support, aktif otomatis jika menggunakan HTTPS

### 3. PHP-FPM Pool Terpisah
Jika ingin pool terpisah untuk bambudua:
```bash
sudo cp /var/www/rsu_bambudua/php-fpm-bambudua.conf /etc/php/8.3/fpm/pool.d/
# Update nginx config: fastcgi_pass unix:/var/run/php/php8.3-fpm-bambudua.sock;
sudo systemctl restart php8.3-fpm
```

### 4. Redis untuk Cache/Session
Edit `.env`:
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## 📈 Monitoring

### Nginx Logs
```bash
# Real-time access log
tail -f /var/log/nginx/bambudua-access.log

# Real-time error log  
tail -f /var/log/nginx/bambudua-error.log

# Cari slow requests
grep "upstream timed out" /var/log/nginx/bambudua-error.log
```

### Connection Statistics
```bash
# Active connections
sudo ss -tuln | grep nginx

# Requests per second (approximate)
tail -n 1000 /var/log/nginx/bambudua-access.log | wc -l
```

## ⚙️ Tuning Tips

### Untuk Server dengan RAM Terbatas (< 2GB)
```nginx
# Kurangi rate limiting burst
limit_req zone=general burst=20 nodelay;

# Kurangi buffer size
fastcgi_buffers 4 16k;
```

### Untuk High Traffic (> 1000 req/min)
```nginx
# Tingkatkan rate limit
limit_req_zone $binary_remote_addr zone=general:10m rate=500r/m;

# Tingkatkan connections
limit_conn addr 50;
```

### WebSocket Port
Default: 8080 (sesuaikan dengan Laravel Reverb config)
```env
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
```

## 🐛 Troubleshooting

### 502 Bad Gateway
```bash
# Cek PHP-FPM running
sudo systemctl status php8.3-fpm

# Cek socket exists
ls -la /var/run/php/php8.3-fpm.sock

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

### 413 Request Entity Too Large
Upload file terlalu besar. Sudah diset 50MB, tingkatkan jika perlu:
```nginx
client_max_body_size 100M;
```

### 504 Gateway Timeout
Request PHP terlalu lama. Sudah diset 180s, tingkatkan jika perlu:
```nginx
fastcgi_read_timeout 300s;
```

## 📝 Changelog

**2026-02-09**
- ✓ Initial optimization
- ✓ Rate limiting implemented
- ✓ Gzip compression enabled
- ✓ Static file caching (1 year)
- ✓ Security headers added
- ✓ WebSocket support for Reverb
- ✓ Laravel optimizations

## 🔗 Resources

- [Nginx Documentation](https://nginx.org/en/docs/)
- [Laravel Deployment](https://laravel.com/docs/11.x/deployment)
- [Web Performance Best Practices](https://web.dev/performance/)

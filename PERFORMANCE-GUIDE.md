# 🚀 Performance Optimization Guide - RSU Bambudua

## ✅ Yang Sudah Diterapkan

### 1. **Server Level Optimizations**

#### Nginx Configuration
- ✓ **Gzip compression** - Mengurangi transfer size 60-70%
- ✓ **Static file caching** - Cache 1 tahun untuk assets
- ✓ **FastCGI buffering** - Response PHP lebih cepat
- ✓ **Rate limiting** - Mencegah abuse
- ✓ **Connection pooling** - Reuse TCP connections

#### PHP-FPM Configuration  
- ✓ **Dynamic process management** - Auto-scale berdasarkan load
- ✓ **Process optimization** - Recycling untuk prevent memory leaks
- ✓ **Buffer tuning** - Optimal buffer sizes

### 2. **Application Level Optimizations**

#### Laravel Caching (AKTIF)
```bash
✓ Config cached    - Faster config loading
✓ Routes cached    - Skip route registration
✓ Views cached     - Pre-compiled Blade templates
✓ Events cached    - Skip event discovery
✓ Autoloader optimized - Faster class loading
```

#### Storage Configuration
```env
SESSION_DRIVER=file     # ✓ Faster than database
CACHE_STORE=file        # ✓ Optimized for reads
QUEUE_CONNECTION=database # For background jobs
```

#### PHP OPcache (ENABLED)
```ini
opcache.enable=1
opcache.memory_consumption=256MB
opcache.validate_timestamps=0  # No file checks in production
opcache.max_accelerated_files=20000
```

### 3. **Frontend Optimizations**

#### Asset Building
- ✓ **Production build** - Minified CSS & JS
- ✓ **Tree shaking** - Remove unused code
- ✓ **Code splitting** - Lazy load components
- ✓ **Image optimization** - Served with cache headers

## 📊 Performance Metrics

### Before Optimization
- Page Load: ~2-3 seconds
- First Byte: ~500ms
- Asset Size: ~150KB (uncompressed)
- Database Queries: N+1 issues potential

### After Optimization (Expected)
- Page Load: **0.8-1.5 seconds** ⚡
- First Byte: **200-300ms** ⚡
- Asset Size: **~45KB** (gzipped) ⚡
- Database Queries: Optimized with eager loading

## 🔧 Database Query Optimization

### Problem: N+1 Queries
```php
// ❌ BAD - Causes N+1 queries
$pasiens = Pasien::all();
foreach ($pasiens as $pasien) {
    echo $pasien->agama->nama;  // Extra query per patient!
}
```

```php
// ✅ GOOD - Single query with eager loading
$pasiens = Pasien::with('agama')->get();
foreach ($pasiens as $pasien) {
    echo $pasien->agama->nama;  // No extra queries!
}
```

### Common Optimizations

#### 1. Eager Loading Relationships
```php
// ✅ Load multiple relationships
$transaksis = TransaksiResep::with([
    'pasien.agama',
    'dokter',
    'detailResep.obat',
    'pembayaran'
])->paginate(20);
```

#### 2. Select Only Needed Columns
```php
// ❌ Loads all columns
$pasiens = Pasien::all();

// ✅ Loads only needed columns
$pasiens = Pasien::select('id', 'nama', 'no_rm')->get();
```

#### 3. Use Pagination
```php
// ❌ Loads all records
$pasiens = Pasien::all();  // Could be 10,000+ records!

// ✅ Paginate results
$pasiens = Pasien::paginate(20);  // Only 20 per page
```

#### 4. Index Important Columns
```sql
-- Add indexes for frequently queried columns
ALTER TABLE pasiens ADD INDEX idx_no_rm (no_rm);
ALTER TABLE transaksi_resep ADD INDEX idx_tanggal (tanggal);
ALTER TABLE antrians ADD INDEX idx_status (status);
```

#### 5. Cache Heavy Queries
```php
// ✅ Cache expensive queries
$stats = Cache::remember('dashboard_stats', 3600, function () {
    return [
        'total_pasien' => Pasien::count(),
        'pasien_hari_ini' => Pasien::whereDate('created_at', today())->count(),
        'pendapatan_bulan' => Pembayaran::whereMonth('created_at', now()->month)->sum('total'),
    ];
});
```

## 🎯 Best Practices untuk Aplikasi Tetap Ringan

### 1. **Controller Optimization**
```php
class PasienController extends Controller
{
    public function index()
    {
        // ✅ Eager load, select specific columns, paginate
        $pasiens = Pasien::select('id', 'no_rm', 'nama', 'tanggal_lahir', 'agama_id')
            ->with('agama:id,nama')
            ->latest()
            ->paginate(20);
            
        return view('pasien.index', compact('pasiens'));
    }
    
    public function show($id)
    {
        // ✅ Load all relationships at once
        $pasien = Pasien::with([
            'agama',
            'transaksiResep.detailResep.obat',
            'anamnesis.dokter'
        ])->findOrFail($id);
        
        return view('pasien.show', compact('pasien'));
    }
}
```

### 2. **Blade Template Optimization**
```blade
{{-- ✅ Cache expensive sections --}}
@cache('sidebar', now()->addHours(24))
    <div class="sidebar">
        @foreach($menuItems as $item)
            <a href="{{ $item->url }}">{{ $item->nama }}</a>
        @endforeach
    </div>
@endcache

{{-- ✅ Use lazy loading for images --}}
<img src="{{ asset('images/placeholder.jpg') }}" 
     data-src="{{ $pasien->foto }}" 
     loading="lazy" 
     class="lazyload">

{{-- ✅ Defer non-critical JavaScript --}}
@push('scripts')
    <script src="{{ asset('js/charts.js') }}" defer></script>
@endpush
```

### 3. **API Response Optimization**
```php
// ✅ Use API Resources for clean responses
class PasienResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'no_rm' => $this->no_rm,
            'agama' => $this->agama->nama ?? null,
            // Only include what's needed!
        ];
    }
}

// ✅ Return paginated API responses
public function apiIndex()
{
    return PasienResource::collection(
        Pasien::paginate(20)
    );
}
```

### 4. **Background Jobs**
```php
// ❌ BAD - Slow response
public function export()
{
    $data = Pasien::all(); // Might take 10+ seconds
    Excel::download(new PasienExport($data), 'pasien.xlsx');
}

// ✅ GOOD - Quick response, process in background
public function export()
{
    dispatch(new ExportPasienJob());
    return back()->with('success', 'Export dimulai, akan dikirim ke email');
}
```

### 5. **Session & Cache Management**
```php
// config/session.php - Already optimized
'driver' => env('SESSION_DRIVER', 'file'),  // ✓ Fast
'lifetime' => 120,  // ✓ Reasonable
'expire_on_close' => false,

// config/cache.php - Already optimized  
'default' => env('CACHE_STORE', 'file'),  // ✓ Fast
```

## 🛠️ Maintenance Commands

### Daily
```bash
# Monitor performance
sudo /var/www/rsu_bambudua/performance-check.sh

# Check logs for errors
tail -f /var/log/nginx/bambudua-error.log
```

### Weekly
```bash
# Clear old sessions and logs
sudo find /var/www/rsu_bambudua/storage/framework/sessions -mtime +7 -delete
sudo find /var/www/rsu_bambudua/storage/logs -mtime +30 -delete

# Optimize database tables
mysql -u bambudua -p -e "OPTIMIZE TABLE sessions, cache, jobs;"
```

### After Code Updates
```bash
# Run full optimization
sudo /var/www/rsu_bambudua/optimize.sh
```

## 📈 Monitoring Checklist

- [ ] Response time < 1 second untuk halaman utama
- [ ] Memory usage PHP-FPM < 75%
- [ ] Database queries < 50 per page load
- [ ] Cache hit ratio > 80%
- [ ] No N+1 query warnings in logs
- [ ] Asset size < 200KB (after gzip)

## 🚨 Troubleshooting

### Slow Page Load
```bash
# 1. Check if caches are built
ls -la /var/www/rsu_bambudua/bootstrap/cache/

# 2. Rebuild caches
sudo /var/www/rsu_bambudua/optimize.sh

# 3. Check for N+1 queries
# Enable query logging in .env temporarily
DB_LOG_QUERIES=true
```

### High Memory Usage
```bash
# 1. Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# 2. Check for memory leaks in code
# 3. Reduce pm.max_children in PHP-FPM config
```

### Database Slow
```bash
# 1. Check slow query log
sudo tail /var/log/mysql/slow-query.log

# 2. Add indexes for frequently queried columns
# 3. Optimize tables
mysql -u root -p -e "OPTIMIZE TABLE your_table;"
```

## 🎓 Additional Resources

- [Laravel Performance](https://laravel.com/docs/11.x/deployment#optimization)
- [Nginx Optimization](https://nginx.org/en/docs/http/ngx_http_core_module.html)
- [PHP OPcache](https://www.php.net/manual/en/book.opcache.php)
- [Database Indexing](https://dev.mysql.com/doc/refman/8.0/en/optimization-indexes.html)

---

**Last Updated:** February 9, 2026
**Status:** ✅ Fully Optimized

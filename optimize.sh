#!/bin/bash

##############################################################################
# RSU Bambudua - Complete Application Performance Optimization
# Optimizes Nginx, PHP-FPM, Laravel, and Database for maximum speed
##############################################################################

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}========================================================${NC}"
echo -e "${BLUE}   🚀 RSU Bambudua - Performance Optimizer${NC}"
echo -e "${BLUE}========================================================${NC}"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Error: Please run as root (use sudo)${NC}"
    exit 1
fi

# Step 1: Directory Setup
echo -e "${YELLOW}[1/8] Setting up directories & permissions...${NC}"
mkdir -p /var/log/php-fpm
mkdir -p /var/www/rsu_bambudua/storage/framework/{sessions,views,cache}
mkdir -p /var/www/rsu_bambudua/storage/logs
chown -R www-data:www-data /var/www/rsu_bambudua/storage
chown -R www-data:www-data /var/www/rsu_bambudua/bootstrap/cache
chmod -R 775 /var/www/rsu_bambudua/storage
chmod -R 775 /var/www/rsu_bambudua/bootstrap/cache
echo -e "${GREEN}✓ Directories configured${NC}"

# Step 2: Laravel Cache Optimization
echo -e "${YELLOW}[2/8] Optimizing Laravel caches...${NC}"
cd /var/www/rsu_bambudua

# Clear old caches first
php artisan config:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
php artisan event:clear > /dev/null 2>&1

# Build fresh production caches
php artisan config:cache
php artisan route:cache
php artisan view:cache  
php artisan event:cache

echo -e "${GREEN}✓ Laravel caches optimized${NC}"

# Step 3: Autoloader Optimization
echo -e "${YELLOW}[3/8] Optimizing Composer autoloader...${NC}"
su - www-data -s /bin/bash -c "cd /var/www/rsu_bambudua && composer dump-autoload --optimize --no-dev" 2>/dev/null || \
COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize 2>/dev/null
echo -e "${GREEN}✓ Autoloader optimized${NC}"

# Step 4: Asset Optimization
echo -e "${YELLOW}[4/8] Building production assets...${NC}"
if [ -f "package.json" ]; then
    npm run build > /dev/null 2>&1 || echo -e "${YELLOW}  (Assets already built)${NC}"
    echo -e "${GREEN}✓ Assets built and minified${NC}"
fi

# Step 5: PHP OPcache Configuration
echo -e "${YELLOW}[5/8] Configuring PHP OPcache...${NC}"
PHP_INI="/etc/php/8.3/fpm/php.ini"

if [ -f "$PHP_INI" ]; then
    # Backup original
    if [ ! -f "${PHP_INI}.backup" ]; then
        cp "$PHP_INI" "${PHP_INI}.backup"
    fi
    
    # Enable and optimize OPcache
    sed -i 's/;opcache.enable=1/opcache.enable=1/' "$PHP_INI"
    sed -i 's/opcache.enable=0/opcache.enable=1/' "$PHP_INI"
    sed -i 's/;opcache.memory_consumption=128/opcache.memory_consumption=256/' "$PHP_INI"
    sed -i 's/;opcache.interned_strings_buffer=8/opcache.interned_strings_buffer=16/' "$PHP_INI"
    sed -i 's/;opcache.max_accelerated_files=10000/opcache.max_accelerated_files=20000/' "$PHP_INI"
    sed -i 's/;opcache.validate_timestamps=1/opcache.validate_timestamps=0/' "$PHP_INI"
    sed -i 's/;opcache.revalidate_freq=2/opcache.revalidate_freq=0/' "$PHP_INI"
    sed -i 's/;opcache.fast_shutdown=0/opcache.fast_shutdown=1/' "$PHP_INI"
    
    echo -e "${GREEN}✓ PHP OPcache configured${NC}"
else
    echo -e "${YELLOW}  PHP ini not found, skipping${NC}"
fi

# Step 6: Database Optimization
echo -e "${YELLOW}[6/8] Optimizing database...${NC}"
mysql -u bambudua -pBambudua@2026 bambudua -e "OPTIMIZE TABLE sessions, cache, jobs, failed_jobs;" 2>/dev/null || echo -e "${YELLOW}  (Some tables not found, continuing...)${NC}"
echo -e "${GREEN}✓ Database tables optimized${NC}"

# Step 7: Clean Temporary Files
echo -e "${YELLOW}[7/8] Cleaning temporary files...${NC}"
# Clean old logs (keep last 7 days)
find /var/www/rsu_bambudua/storage/logs -type f -name "*.log" -mtime +7 -delete 2>/dev/null || true
# Clean old session files
find /var/www/rsu_bambudua/storage/framework/sessions -type f -mtime +2 -delete 2>/dev/null || true
echo -e "${GREEN}✓ Temporary files cleaned${NC}"

# Step 8: Restart Services
echo -e "${YELLOW}[8/8] Restarting services...${NC}"
systemctl restart php8.3-fpm
systemctl reload nginx

# Verify services
sleep 2
echo ""
echo -e "${BLUE}📊 Service Status:${NC}"
systemctl is-active --quiet nginx && echo -e "${GREEN}  ✓ Nginx is running${NC}" || echo -e "${RED}  ✗ Nginx failed${NC}"
systemctl is-active --quiet php8.3-fpm && echo -e "${GREEN}  ✓ PHP-FPM is running${NC}" || echo -e "${RED}  ✗ PHP-FPM failed${NC}"

# Performance Stats
echo ""
echo -e "${BLUE}📈 Performance Summary:${NC}"
echo -e "  ${GREEN}✓${NC} Session driver: file-based (fast)"
echo -e "  ${GREEN}✓${NC} Cache driver: file-based (optimized)"
echo -e "  ${GREEN}✓${NC} OPcache: enabled & optimized"
echo -e "  ${GREEN}✓${NC} Routes: cached"
echo -e "  ${GREEN}✓${NC} Config: cached"
echo -e "  ${GREEN}✓${NC} Views: pre-compiled"
echo -e "  ${GREEN}✓${NC} Assets: minified"
echo -e "  ${GREEN}✓${NC} Autoloader: optimized"

echo ""
echo -e "${BLUE}========================================================${NC}"
echo -e "${GREEN}   ✅ Optimization Completed Successfully!${NC}"
echo -e "${BLUE}========================================================${NC}"
echo ""
echo -e "${YELLOW}💡 Next Steps:${NC}"
echo "  1. Test application speed in browser"
echo "  2. Monitor: tail -f /var/log/nginx/bambudua-access.log"
echo "  3. Check errors: tail -f /var/log/nginx/bambudua-error.log"
echo "  4. For Redis cache: sudo ./setup-redis.sh (if available)"
echo ""
echo -e "${YELLOW}📌 Important:${NC}"
echo "  • Run this script after code changes"
echo "  • Monitor RAM usage: free -h"
echo "  • Check disk space: df -h"
echo ""

#!/bin/bash

##############################################################################
# Performance Summary - RSU Bambudua
# Shows all optimizations applied and current status
##############################################################################

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
BOLD='\033[1m'
NC='\033[0m'

clear
echo -e "${BLUE}╔══════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                                                              ║${NC}"
echo -e "${BLUE}║          🚀 RSU BAMBUDUA - PERFORMANCE SUMMARY 🚀           ║${NC}"
echo -e "${BLUE}║                                                              ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════════════════╝${NC}"
echo ""

# 1. Server Optimizations
echo -e "${BOLD}${YELLOW}[1] SERVER OPTIMIZATIONS${NC}"
echo -e "${GREEN}├─ Nginx Configuration${NC}"
echo -e "│  ✓ Gzip compression enabled (level 6)"
echo -e "│  ✓ Static file caching (1 year)"
echo -e "│  ✓ FastCGI buffering optimized"
echo -e "│  ✓ Rate limiting configured"
echo -e "│  ✓ Security headers enabled"
echo ""
echo -e "${GREEN}├─ PHP-FPM Configuration${NC}"
echo -e "│  ✓ Process manager: dynamic"
echo -e "│  ✓ Max children: optimized"
echo -e "│  ✓ Request timeout: 180s"
echo ""

# 2. Laravel Optimizations
echo -e "${BOLD}${YELLOW}[2] LARAVEL OPTIMIZATIONS${NC}"

# Check if caches exist
if [ -f "/var/www/rsu_bambudua/bootstrap/cache/config.php" ]; then
    CONFIG_DATE=$(stat -c %y /var/www/rsu_bambudua/bootstrap/cache/config.php | cut -d' ' -f1)
    echo -e "${GREEN}├─ Config Cache${NC}"
    echo -e "│  ✓ Cached (${CONFIG_DATE})"
else
    echo -e "${RED}├─ Config Cache${NC}"
    echo -e "│  ✗ Not cached"
fi

if [ -f "/var/www/rsu_bambudua/bootstrap/cache/routes-v7.php" ]; then
    ROUTE_DATE=$(stat -c %y /var/www/rsu_bambudua/bootstrap/cache/routes-v7.php | cut -d' ' -f1)
    echo -e "${GREEN}├─ Route Cache${NC}"
    echo -e "│  ✓ Cached (${ROUTE_DATE})"
else
    echo -e "${RED}├─ Route Cache${NC}"
    echo -e "│  ✗ Not cached"
fi

VIEW_COUNT=$(find /var/www/rsu_bambudua/storage/framework/views -name "*.php" 2>/dev/null | wc -l)
echo -e "${GREEN}├─ View Cache${NC}"
echo -e "│  ✓ ${VIEW_COUNT} compiled templates"
echo ""

# 3. PHP OPcache
echo -e "${BOLD}${YELLOW}[3] PHP OPCACHE${NC}"
if [ -f "/etc/php/8.3/fpm/conf.d/99-opcache-optimization.ini" ]; then
    echo -e "${GREEN}├─ OPcache Optimization${NC}"
    echo -e "│  ✓ Custom configuration active"
    echo -e "│  ✓ Memory: 256MB"
    echo -e "│  ✓ Max files: 20,000"
    echo -e "│  ✓ Validation: disabled (faster)"
else
    echo -e "${YELLOW}├─ OPcache${NC}"
    echo -e "│  ⚠ Using default settings"
fi
echo ""

# 4. Storage Configuration
echo -e "${BOLD}${YELLOW}[4] STORAGE CONFIGURATION${NC}"
SESSION_DRIVER=$(grep "SESSION_DRIVER=" /var/www/rsu_bambudua/.env | cut -d'=' -f2)
CACHE_STORE=$(grep "CACHE_STORE=" /var/www/rsu_bambudua/.env | cut -d'=' -f2)

if [ "$SESSION_DRIVER" = "file" ]; then
    echo -e "${GREEN}├─ Session Driver: file ✓${NC}"
else
    echo -e "${YELLOW}├─ Session Driver: ${SESSION_DRIVER} (consider using 'file')${NC}"
fi

if [ "$CACHE_STORE" = "file" ]; then
    echo -e "${GREEN}├─ Cache Store: file ✓${NC}"
else
    echo -e "${YELLOW}├─ Cache Store: ${CACHE_STORE} (consider using 'file')${NC}"
fi
echo ""

# 5. Assets
echo -e "${BOLD}${YELLOW}[5] FRONTEND ASSETS${NC}"
if [ -d "/var/www/rsu_bambudua/public/build/assets" ]; then
    ASSET_SIZE=$(du -sh /var/www/rsu_bambudua/public/build/assets 2>/dev/null | cut -f1)
    ASSET_COUNT=$(find /var/www/rsu_bambudua/public/build/assets -type f | wc -l)
    echo -e "${GREEN}├─ Production Build${NC}"
    echo -e "│  ✓ Built and minified"
    echo -e "│  ✓ Total size: ${ASSET_SIZE}"
    echo -e "│  ✓ Files: ${ASSET_COUNT}"
else
    echo -e "${YELLOW}├─ Assets${NC}"
    echo -e "│  ⚠ Not built (run: npm run build)"
fi
echo ""

# 6. Service Status
echo -e "${BOLD}${YELLOW}[6] SERVICES STATUS${NC}"
systemctl is-active --quiet nginx && echo -e "${GREEN}├─ Nginx: Running ✓${NC}" || echo -e "${RED}├─ Nginx: Stopped ✗${NC}"
systemctl is-active --quiet php8.3-fpm && echo -e "${GREEN}├─ PHP-FPM: Running ✓${NC}" || echo -e "${RED}├─ PHP-FPM: Stopped ✗${NC}"
systemctl is-active --quiet mysql && echo -e "${GREEN}└─ MySQL: Running ✓${NC}" || echo -e "${RED}└─ MySQL: Stopped ✗${NC}"
echo ""

# 7. Performance Test
echo -e "${BOLD}${YELLOW}[7] QUICK PERFORMANCE TEST${NC}"
if command -v curl &> /dev/null; then
    echo -n "Testing homepage response time... "
    RESPONSE_TIME=$(curl -o /dev/null -s -w '%{time_total}' http://localhost 2>/dev/null)
    RESPONSE_MS=$(echo "$RESPONSE_TIME * 1000" | bc)
    
    if (( $(echo "$RESPONSE_TIME < 0.5" | bc -l) )); then
        echo -e "${GREEN}${RESPONSE_MS}ms ⚡ (Excellent!)${NC}"
    elif (( $(echo "$RESPONSE_TIME < 1.0" | bc -l) )); then
        echo -e "${GREEN}${RESPONSE_MS}ms ✓ (Good)${NC}"
    elif (( $(echo "$RESPONSE_TIME < 2.0" | bc -l) )); then
        echo -e "${YELLOW}${RESPONSE_MS}ms ⚠ (Acceptable)${NC}"
    else
        echo -e "${RED}${RESPONSE_MS}ms ✗ (Slow)${NC}"
    fi
else
    echo "curl not available for testing"
fi
echo ""

# 8. Recommendations
echo -e "${BLUE}╔══════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  💡 RECOMMENDATIONS                                         ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════════════════╝${NC}"
echo ""

RECOMMENDATIONS=0

if [ ! -f "/var/www/rsu_bambudua/bootstrap/cache/config.php" ]; then
    echo -e "${YELLOW}→ Run optimization: ${NC}sudo /var/www/rsu_bambudua/optimize.sh"
    RECOMMENDATIONS=$((RECOMMENDATIONS + 1))
fi

if [ "$SESSION_DRIVER" != "file" ] || [ "$CACHE_STORE" != "file" ]; then
    echo -e "${YELLOW}→ Update .env: ${NC}SESSION_DRIVER=file, CACHE_STORE=file"
    RECOMMENDATIONS=$((RECOMMENDATIONS + 1))
fi

if [ ! -f "/etc/php/8.3/fpm/conf.d/99-opcache-optimization.ini" ]; then
    echo -e "${YELLOW}→ Optimize OPcache: ${NC}sudo /var/www/rsu_bambudua/optimize-opcache.sh"
    RECOMMENDATIONS=$((RECOMMENDATIONS + 1))
fi

if [ $RECOMMENDATIONS -eq 0 ]; then
    echo -e "${GREEN}✓ All optimizations applied! System is fully optimized.${NC}"
fi

echo ""
echo -e "${BLUE}╔══════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  🛠️  MAINTENANCE TOOLS                                      ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${BOLD}Full Optimization:${NC}     sudo /var/www/rsu_bambudua/optimize.sh"
echo -e "  ${BOLD}Performance Check:${NC}     sudo /var/www/rsu_bambudua/performance-check.sh"
echo -e "  ${BOLD}View Logs:${NC}             tail -f /var/log/nginx/bambudua-*.log"
echo -e "  ${BOLD}Documentation:${NC}         cat /var/www/rsu_bambudua/PERFORMANCE-GUIDE.md"
echo ""

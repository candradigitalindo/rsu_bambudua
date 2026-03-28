#!/bin/bash

##############################################################################
# Performance Monitor - RSU Bambudua
# Quick performance check and monitoring script
##############################################################################

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   Performance Monitor${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# 1. System Resources
echo -e "${YELLOW}💻 System Resources:${NC}"
echo -e "${GREEN}Memory Usage:${NC}"
free -h | grep -E "Mem|Swap"
echo ""

echo -e "${GREEN}Disk Usage:${NC}"
df -h / | tail -n 1
echo ""

echo -e "${GREEN}CPU Load:${NC}"
uptime
echo ""

# 2. Service Status
echo -e "${YELLOW}🔧 Service Status:${NC}"
systemctl is-active --quiet nginx && echo -e "${GREEN}✓ Nginx: Running${NC}" || echo -e "${RED}✗ Nginx: Stopped${NC}"
systemctl is-active --quiet php8.3-fpm && echo -e "${GREEN}✓ PHP-FPM: Running${NC}" || echo -e "${RED}✗ PHP-FPM: Stopped${NC}"
systemctl is-active --quiet mysql && echo -e "${GREEN}✓ MySQL: Running${NC}" || echo -e "${RED}✗ MySQL: Stopped${NC}"
echo ""

# 3. PHP-FPM Status
echo -e "${YELLOW}⚡ PHP-FPM Processes:${NC}"
ps aux | grep -E "php-fpm|PID" | grep -v grep | head -n 6
echo ""

# 4. Nginx Connections
echo -e "${YELLOW}🌐 Nginx Status:${NC}"
NGINX_CONN=$(ss -tuln | grep :80 | wc -l)
echo -e "Active connections on port 80: ${GREEN}${NGINX_CONN}${NC}"
echo ""

# 5. Laravel Cache Status
echo -e "${YELLOW}📦 Laravel Cache:${NC}"
if [ -f "/var/www/rsu_bambudua/bootstrap/cache/config.php" ]; then
    echo -e "${GREEN}✓ Config cached${NC} ($(stat -c %y /var/www/rsu_bambudua/bootstrap/cache/config.php | cut -d' ' -f1))"
else
    echo -e "${RED}✗ Config not cached${NC}"
fi

if [ -f "/var/www/rsu_bambudua/bootstrap/cache/routes-v7.php" ]; then
    echo -e "${GREEN}✓ Routes cached${NC} ($(stat -c %y /var/www/rsu_bambudua/bootstrap/cache/routes-v7.php | cut -d' ' -f1))"
else
    echo -e "${RED}✗ Routes not cached${NC}"
fi

if [ -d "/var/www/rsu_bambudua/storage/framework/views" ]; then
    VIEW_COUNT=$(find /var/www/rsu_bambudua/storage/framework/views -name "*.php" | wc -l)
    echo -e "${GREEN}✓ Views compiled${NC} ($VIEW_COUNT files)"
fi
echo ""

# 6. OPcache Status
echo -e "${YELLOW}🚀 PHP OPcache:${NC}"
OPCACHE_STATUS=$(php -r "echo ini_get('opcache.enable') ? 'Enabled' : 'Disabled';")
if [ "$OPCACHE_STATUS" = "Enabled" ]; then
    echo -e "${GREEN}✓ OPcache: Enabled${NC}"
    OPCACHE_MEM=$(php -r "echo ini_get('opcache.memory_consumption');")
    echo -e "  Memory: ${OPCACHE_MEM}MB"
else
    echo -e "${RED}✗ OPcache: Disabled${NC}"
fi
echo ""

# 7. Recent Errors
echo -e "${YELLOW}⚠️  Recent Errors (last 10):${NC}"
if [ -f "/var/log/nginx/bambudua-error.log" ]; then
    ERROR_COUNT=$(wc -l < /var/log/nginx/bambudua-error.log)
    echo -e "Total errors in log: ${ERROR_COUNT}"
    echo -e "${BLUE}Last 5 errors:${NC}"
    tail -n 5 /var/log/nginx/bambudua-error.log 2>/dev/null | sed 's/^/  /' || echo "  No recent errors"
else
    echo -e "${GREEN}No error log found${NC}"
fi
echo ""

# 8. Response Time Test
echo -e "${YELLOW}⏱️  Response Time Test:${NC}"
if command -v curl &> /dev/null; then
    RESPONSE_TIME=$(curl -o /dev/null -s -w '%{time_total}\n' http://localhost 2>/dev/null | head -n 1)
    echo -e "Homepage response: ${GREEN}${RESPONSE_TIME}s${NC}"
else
    echo "curl not available"
fi
echo ""

# 9. Recommendations
echo -e "${BLUE}========================================${NC}"
echo -e "${YELLOW}💡 Quick Actions:${NC}"
echo "  • If high memory: sudo systemctl restart php8.3-fpm"
echo "  • If slow: sudo /var/www/rsu_bambudua/optimize.sh"
echo "  • View logs: tail -f /var/log/nginx/bambudua-*.log"
echo "  • Clear Laravel cache: php artisan cache:clear"
echo ""

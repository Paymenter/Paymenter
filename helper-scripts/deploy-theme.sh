#!/bin/bash

# Paymenter Theme Deployment Script
# Builds theme assets and cleans caches

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Detect project root by looking for docker-compose file
detect_project_root() {
    local current_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    
    # Check if docker-compose or compose file exists in same directory as script (any variation)
    if ls "$current_dir"/{docker-,}compose*.{yml,yaml} 2>/dev/null | grep -q .; then
        echo "$current_dir"
        return 0
    fi
    
    # Check parent directory
    local parent_dir="$(dirname "$current_dir")"
    if ls "$parent_dir"/{docker-,}compose*.{yml,yaml} 2>/dev/null | grep -q .; then
        echo "$parent_dir"
        return 0
    fi
    
    # Not found
    echo -e "${RED}Error: docker-compose or compose file not found in script directory or parent directory${NC}" >&2
    exit 1
}

PROJECT_ROOT="$(detect_project_root)"
cd "$PROJECT_ROOT"

# Function: Clear Caches
clear_caches() {
    echo -e "${YELLOW}Clearing Paymenter cache...${NC}"
    docker compose exec paymenter php artisan cache:clear
    docker compose exec paymenter php artisan view:clear
    docker compose exec paymenter php artisan config:clear
    docker compose exec paymenter php artisan route:clear
    echo -e "${GREEN}Cache cleared successfully!${NC}"
}


echo -e "${CYAN}=== Paymenter Theme Deployment ===${NC}"
echo ""

# Get available themes
THEMES_PATH="./themes"
if [ ! -d "$THEMES_PATH" ]; then
    echo -e "${RED}Themes directory not found: $THEMES_PATH${NC}"
    exit 1
fi

# List available themes
echo -e "${YELLOW}Available themes:${NC}"

# Create indexed list of themes
i=1
for theme_dir in "$THEMES_PATH"/*/; do
    if [ -d "$theme_dir" ]; then
        theme=$(basename "$theme_dir")
        echo "  $i. $theme"
        eval "THEME_$i='$theme'"
        i=$((i + 1))
    fi
done

THEME_COUNT=$((i - 1))

if [ "$THEME_COUNT" -eq 0 ]; then
    echo -e "${RED}No themes found in $THEMES_PATH${NC}"
    exit 1
fi

# Prompt user for theme selection
echo ""
printf "Select theme number [1-$THEME_COUNT]: "
read THEME_NUM

# Validate input
if ! echo "$THEME_NUM" | grep -qE '^[0-9]+$' || [ "$THEME_NUM" -lt 1 ] || [ "$THEME_NUM" -gt "$THEME_COUNT" ]; then
    echo -e "${RED}Invalid selection${NC}"
    exit 1
fi

# Get selected theme
eval "THEME_NAME=\$THEME_$THEME_NUM"

echo ""
echo -e "${CYAN}Building and Deploying Theme: $THEME_NAME${NC}"
echo ""

# 1. Build Assets
echo -e "${YELLOW}1. Building assets...${NC}"
docker compose run --rm asset-builder npm install
docker compose run --rm asset-builder npm run build -- "$THEME_NAME"

# 2. Verify Build Output
echo -e "${YELLOW}2. Verifying build output...${NC}"
if docker compose exec paymenter test -f /app/public/$THEME_NAME/manifest.json; then
    echo -e "${GREEN} manifest.json found${NC}"
else
    echo -e "${RED} manifest.json not found! Build may have failed.${NC}"
    exit 1
fi

# 3. Ensure Storage Link
echo -e "${YELLOW}3. Ensuring storage link...${NC}"
docker compose exec paymenter php artisan storage:link 2>/dev/null || echo -e "${CYAN}Storage link already exists${NC}"

# 4. Clear Caches
echo -e "${YELLOW}4. Clearing caches...${NC}"
clear_caches

echo ""
echo -e "${GREEN}═══════════════════════════════════${NC}"
echo -e "${GREEN}Theme deployment complete!${NC}"
echo -e "${CYAN}Theme: $THEME_NAME${NC}"
echo -e "${CYAN}Manifest: /app/public/$THEME_NAME/manifest.json${NC}"
echo -e "${GREEN}═══════════════════════════════════${NC}"
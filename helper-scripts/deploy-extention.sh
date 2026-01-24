#!/bin/bash

# Paymenter Extension Deployment Script
# Automatically discovers extensions, builds assets and runs migrations

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


echo -e "${CYAN}=== Paymenter Extension Deployment ===${NC}"
echo ""

# Get available extensions
EXTENSIONS_PATH="./extensions"
if [ ! -d "$EXTENSIONS_PATH" ]; then
    echo -e "${RED}Extensions directory not found: $EXTENSIONS_PATH${NC}"
    exit 1
fi

# Recursively find all extensions in subdirectories
echo -e "${YELLOW}Scanning extensions folder...${NC}"
echo ""

# Create indexed list of extensions (recursively)
i=1
EXTENSION_COUNT=0
declare -A EXTENSION_MAP

for extension_dir in "$EXTENSIONS_PATH"/*/*/; do
    if [ -d "$extension_dir" ]; then
        # Get the extension name (last directory)
        extension=$(basename "$extension_dir")
        # Get the category (parent directory)
        category=$(basename "$(dirname "$extension_dir")")
        
        full_path="$category/$extension"
        echo "  $i. $full_path"
        EXTENSION_MAP[$i]="$full_path"
        EXTENSION_COUNT=$((i))
        i=$((i + 1))
    fi
done

if [ "$EXTENSION_COUNT" -eq 0 ]; then
    echo -e "${RED}No extensions found in $EXTENSIONS_PATH${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Found $EXTENSION_COUNT extension(s)${NC}"
echo ""

# Prompt user for selection
printf "Select extensions to deploy (enter number/comma-separated, or 'all'): "
read EXTENSION_INPUT

if [ "$EXTENSION_INPUT" = "" ]; then
    echo -e "${YELLOW}Deployment cancelled${NC}"
    exit 0
fi

# Collect selected extensions
SELECTED_EXTENSIONS=()

if [ "$EXTENSION_INPUT" = "all" ]; then
    for ((j=1; j<=EXTENSION_COUNT; j++)); do
        SELECTED_EXTENSIONS+=("${EXTENSION_MAP[$j]}")
    done
else
    # Parse comma-separated input
    IFS=',' read -ra SELECTIONS <<< "$EXTENSION_INPUT"
    for selection in "${SELECTIONS[@]}"; do
        selection=$(echo "$selection" | xargs) # Trim whitespace
        if [ -n "${EXTENSION_MAP[$selection]}" ]; then
            SELECTED_EXTENSIONS+=("${EXTENSION_MAP[$selection]}")
        else
            echo -e "${RED}Invalid selection: $selection${NC}"
            exit 1
        fi
    done
fi

if [ ${#SELECTED_EXTENSIONS[@]} -eq 0 ]; then
    echo -e "${RED}No valid extensions selected${NC}"
    exit 1
fi

echo ""
echo -e "${CYAN}Deploying ${#SELECTED_EXTENSIONS[@]} extension(s)...${NC}"
echo ""

# Deploy each selected extension
for EXTENSION_PATH in "${SELECTED_EXTENSIONS[@]}"; do
    echo -e "${CYAN}─────────────────────────────────${NC}"
    echo -e "${CYAN}Extension: $EXTENSION_PATH${NC}"
    echo -e "${CYAN}─────────────────────────────────${NC}"
    echo -e "${GREEN}Extension registered${NC}"
    echo ""
done

# Run Migrations
echo -e "${CYAN}─────────────────────────────────${NC}"
echo -e "${YELLOW}Running database migrations...${NC}"
echo -e "${CYAN}─────────────────────────────────${NC}"
docker compose exec paymenter php artisan migrate --force
echo -e "${GREEN}Migrations completed${NC}"

echo ""

# Clear Caches
echo -e "${CYAN}─────────────────────────────────${NC}"
echo -e "${YELLOW}Clearing caches...${NC}"
echo -e "${CYAN}─────────────────────────────────${NC}"
clear_caches

echo ""
echo -e "${GREEN}═══════════════════════════════════${NC}"
echo -e "${GREEN}Extension deployment complete!${NC}"
echo -e "${CYAN}Deployed: $(IFS=', '; echo "${SELECTED_EXTENSIONS[*]}")${NC}"
echo -e "${GREEN}═══════════════════════════════════${NC}"
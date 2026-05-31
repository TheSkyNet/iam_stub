#!/usr/bin/env bash

set -e

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}Running CI checks...${NC}"

echo "1. Running PHPUnit tests..."
./vendor/bin/phpunit

echo "2. Running Rector (dry-run)..."
./vendor/bin/rector process --dry-run

echo "3. Running PHPCS..."
./vendor/bin/phpcs --standard=phpcs.xml --runtime-set ignore_warnings_on_exit 1

echo -e "${GREEN}✓ CI checks passed!${NC}"

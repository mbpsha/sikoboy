#!/bin/bash

# SIKOBOY - Production Build Script
# This script prepares your Laravel + Vue app for cPanel deployment

echo "🚀 SIKOBOY Production Build Script"
echo "==================================="
echo ""

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: composer.json not found. Run this script from the project root."
    exit 1
fi

# Step 1: Check dependencies
echo "📦 Step 1: Checking dependencies..."
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer first."
    exit 1
fi

if ! command -v npm &> /dev/null; then
    echo "❌ NPM is not installed. Please install Node.js and NPM first."
    exit 1
fi
echo "✅ Dependencies found"
echo ""

# Step 2: Install PHP dependencies
echo "📦 Step 2: Installing PHP dependencies (production)..."
composer install --no-dev --optimize-autoloader --no-interaction
echo "✅ PHP dependencies installed"
echo ""

# Step 3: Install Node dependencies
echo "📦 Step 3: Installing Node dependencies..."
npm install
echo "✅ Node dependencies installed"
echo ""

# Step 4: Build frontend assets
echo "🔨 Step 4: Building frontend assets for production..."
npm run build
echo "✅ Frontend assets built"
echo ""

# Step 5: Clear caches
echo "🧹 Step 5: Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Caches cleared"
echo ""

# Step 6: Generate production .env if not exists
if [ ! -f ".env.production" ]; then
    echo "📝 Step 6: Creating .env.production from example..."
    cp .env.production.example .env.production
    echo "⚠️  Please edit .env.production with your production settings!"
    echo ""
else
    echo "✅ .env.production already exists"
fi

# Step 7: Create deployment package info
echo "📋 Step 7: Creating deployment checklist..."
cat > DEPLOY_INSTRUCTIONS.txt << 'EOF'
=====================================
SIKOBOY DEPLOYMENT INSTRUCTIONS
=====================================

YOUR PROJECT IS NOW READY FOR DEPLOYMENT!

NEXT STEPS:
1. Review and update .env.production with your production settings
2. Generate APP_KEY: php artisan key:generate --show
3. Copy the APP_KEY to .env.production
4. Create a ZIP of the project (excluding node_modules, .git, tests)
5. Upload to cPanel
6. Follow DEPLOYMENT_CHECKLIST.md for complete setup

FILES TO UPLOAD TO CPANEL:
- app/
- bootstrap/
- config/
- database/
- public/
- resources/
- routes/
- storage/
- vendor/
- .env.production (rename to .env on server)
- .htaccess
- artisan
- composer.json
- composer.lock
- package.json

DO NOT UPLOAD:
- node_modules/
- .git/
- tests/
- .env (local)
- README.md

For detailed instructions, see: DEPLOYMENT_CHECKLIST.md
=====================================
EOF

echo "✅ Deployment instructions created"
echo ""

# Step 8: Summary
echo "====================================="
echo "✅ BUILD COMPLETE!"
echo "====================================="
echo ""
echo "📁 Your project is ready for cPanel deployment"
echo "📝 Review .env.production and update with production settings"
echo "📖 Read DEPLOYMENT_CHECKLIST.md for deployment steps"
echo ""
echo "Next command suggestions:"
echo "  - Edit .env.production with your server details"
echo "  - Generate APP_KEY: php artisan key:generate --show"
echo "  - Create ZIP: zip -r sikoboy-deploy.zip . -x 'node_modules/*' '.git/*' 'tests/*' '.env'"
echo ""

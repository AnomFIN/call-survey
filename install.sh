#!/bin/bash
# Installation script for Call-Survey ULTRALIGHT
# Super easy guided installation!

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║       📞 Call-Survey ULTRALIGHT - Installation Wizard       ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "This script will help you install Call-Survey step by step."
echo "Just follow the prompts!"
echo ""

# Check PHP
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 1: Checking Requirements"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if ! command -v php &> /dev/null; then
    echo "❌ PHP not found!"
    echo ""
    echo "Please install PHP 7.4 or higher:"
    echo "  Ubuntu/Debian: sudo apt-get install php php-mysql php-mbstring"
    echo "  macOS: brew install php"
    echo "  Windows: Download from https://windows.php.net/download/"
    echo ""
    exit 1
fi

PHP_VERSION=$(php -r 'echo PHP_VERSION;')
echo "✅ PHP version: $PHP_VERSION"

# Check Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer not found!"
    echo ""
    echo "Please install Composer:"
    echo "  curl -sS https://getcomposer.org/installer | php"
    echo "  sudo mv composer.phar /usr/local/bin/composer"
    echo ""
    echo "Or visit: https://getcomposer.com/download/"
    echo ""
    exit 1
fi

echo "✅ Composer found"

# Check MySQL (optional - just warn)
if ! command -v mysql &> /dev/null; then
    echo "⚠️  MySQL command not found in PATH"
    echo "   (You can still install manually or use Docker)"
else
    echo "✅ MySQL found"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 2: Installing PHP Dependencies"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Running: composer install"
echo ""
composer install

if [ $? -ne 0 ]; then
    echo ""
    echo "❌ Failed to install dependencies"
    exit 1
fi

echo ""
echo "✅ Dependencies installed successfully!"

# Check for config.php
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 3: Configuration Setup"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ ! -f "config.php" ]; then
    echo "Creating config.php from template..."
    cp config.example.php config.php
    echo "✅ config.php created"
    echo ""
    echo "⚠️  IMPORTANT: You need to edit config.php!"
    echo ""
    echo "You need to add your Twilio credentials:"
    echo "  1. Get Account SID from https://console.twilio.com"
    echo "  2. Get Auth Token from https://console.twilio.com"
    echo "  3. Buy a phone number (or use trial number)"
    echo ""
    echo "Then edit config.php and fill in:"
    echo "  - account_sid"
    echo "  - auth_token"
    echo "  - phone_number"
    echo ""
    read -p "Press Enter to edit config.php now, or Ctrl+C to do it later..."
    
    # Try to find a suitable editor
    if command -v nano &> /dev/null; then
        nano config.php
    elif command -v vim &> /dev/null; then
        vim config.php
    elif command -v vi &> /dev/null; then
        vi config.php
    else
        echo "No text editor found. Please edit config.php manually."
    fi
else
    echo "✅ config.php already exists"
    echo "   (If you need to reconfigure, edit: config.php)"
fi

# Database setup
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 4: Database Setup"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Do you want to set up the database now? (y/n)"
echo "(You need MySQL installed and running)"
read -r response

if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    echo ""
    echo "Enter your MySQL username (default: root):"
    read -r DB_USER
    DB_USER=${DB_USER:-root}
    
    echo "Enter your MySQL password:"
    read -s DB_PASS
    echo ""
    
    echo "Enter database name (default: call_survey):"
    read -r DB_NAME
    DB_NAME=${DB_NAME:-call_survey}
    
    echo ""
    echo "Creating database '$DB_NAME'..."
    mysql -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        echo "✅ Database created"
        
        echo "Importing database schema..."
        mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database.sql 2>/dev/null
        
        if [ $? -eq 0 ]; then
            echo "✅ Database schema imported successfully!"
            echo ""
            echo "⚠️  Update config.php with these database settings:"
            echo "   'database' => '$DB_NAME',"
            echo "   'username' => '$DB_USER',"
            echo "   'password' => '***',"
        else
            echo "❌ Failed to import database schema"
            echo "   You can do it manually: mysql -u $DB_USER -p $DB_NAME < database.sql"
        fi
    else
        echo "❌ Failed to create database"
        echo "   Make sure MySQL is running and credentials are correct"
        echo "   You can create it manually:"
        echo "   mysql -u $DB_USER -p"
        echo "   CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4;"
    fi
else
    echo "⏭️  Skipping database setup"
    echo "   You can set it up later with:"
    echo "   mysql -u root -p < database.sql"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🎉 Installation Complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ What was installed:"
echo "   • PHP dependencies (Twilio SDK)"
echo "   • Configuration file (config.php)"
echo "   • Database setup (if selected)"
echo ""
echo "📝 Next Steps:"
echo ""
echo "1. ⚙️  Configure Twilio credentials in config.php"
echo "      • Account SID"
echo "      • Auth Token"
echo "      • Phone Number"
echo ""
echo "2. 🧪 Test your installation:"
echo "      php test.php"
echo ""
echo "3. 🚀 Start the development server:"
echo "      ./start-dev.sh"
echo "   OR:"
echo "      php -S localhost:8000 -t public"
echo ""
echo "4. 🌐 Open your browser:"
echo "      http://localhost:8000"
echo ""
echo "5. 📞 Create your first survey:"
echo "      php create-sample-survey.php"
echo "   OR use the web interface!"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📚 Documentation:"
echo "   • SETUP.md        - Detailed setup guide for beginners"
echo "   • QUICKSTART.md   - Quick reference"
echo "   • README.md       - Full documentation"
echo "   • API.md          - API reference"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Need help? Check SETUP.md for troubleshooting!"
echo ""


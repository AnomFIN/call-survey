#!/bin/bash
# Development server startup script for Call-Survey ULTRALIGHT

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║    🚀 Call-Survey ULTRALIGHT - Development Server            ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Check if config exists
if [ ! -f "config.php" ]; then
    echo "⚠️  Warning: config.php not found!"
    echo ""
    echo "You need to create and configure config.php first."
    echo ""
    read -p "Do you want to copy it from the template now? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        cp config.example.php config.php
        echo "✅ config.php created"
        echo ""
        echo "📝 IMPORTANT: Edit config.php and add your Twilio credentials:"
        echo "   • Account SID (from https://console.twilio.com)"
        echo "   • Auth Token"
        echo "   • Phone Number"
        echo ""
        echo "Run this script again after editing config.php"
        exit 0
    else
        echo ""
        echo "❌ Cannot start without config.php"
        echo "   Create it manually: cp config.example.php config.php"
        exit 1
    fi
fi

# Check if vendor exists
if [ ! -d "vendor" ]; then
    echo "⚠️  Warning: Dependencies not installed!"
    echo ""
    echo "Installing Composer dependencies..."
    echo "(This may take a minute...)"
    echo ""
    composer install
    if [ $? -ne 0 ]; then
        echo ""
        echo "❌ Failed to install dependencies"
        echo "   Try running: composer install"
        exit 1
    fi
    echo ""
    echo "✅ Dependencies installed successfully!"
fi

# Determine port
PORT="${1:-8000}"

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Starting PHP Development Server"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📍 Server URL:"
echo "   👉 http://localhost:$PORT"
echo ""
echo "💡 Tips:"
echo "   • Dashboard: http://localhost:$PORT/"
echo "   • API docs: See API.md"
echo "   • Test setup: php test.php"
echo ""
echo "🛑 To stop the server: Press Ctrl+C"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Start PHP built-in server
php -S localhost:$PORT -t public


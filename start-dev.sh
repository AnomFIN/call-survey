#!/bin/bash
# Development server startup script

echo "🚀 Starting Call-Survey ULTRALIGHT development server..."
echo ""

# Check if config exists
if [ ! -f "config.php" ]; then
    echo "⚠️  Warning: config.php not found!"
    echo "Copy config.example.php to config.php and configure it first."
    echo ""
    read -p "Do you want to copy it now? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        cp config.example.php config.php
        echo "✅ config.php created. Please edit it with your Twilio credentials."
        echo "   nano config.php"
        exit 0
    else
        exit 1
    fi
fi

# Check if vendor exists
if [ ! -d "vendor" ]; then
    echo "⚠️  Warning: Dependencies not installed!"
    echo "Installing Composer dependencies..."
    composer install
    if [ $? -ne 0 ]; then
        echo "❌ Failed to install dependencies"
        exit 1
    fi
fi

# Determine port
PORT="${1:-8000}"

echo "📍 Server will be available at:"
echo "   http://localhost:$PORT"
echo ""
echo "🛑 Press Ctrl+C to stop the server"
echo ""

# Start PHP built-in server
php -S localhost:$PORT -t public

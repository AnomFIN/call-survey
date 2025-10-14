#!/bin/bash
# Installation script for Call-Survey ULTRALIGHT

echo "📞 Call-Survey ULTRALIGHT - Asennusohjelma"
echo "=========================================="
echo ""

# Check PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP ei ole asennettu. Asenna PHP 7.4 tai uudempi."
    exit 1
fi

PHP_VERSION=$(php -r 'echo PHP_VERSION;')
echo "✅ PHP versio: $PHP_VERSION"

# Check Composer
if ! command -v composer &> /dev/null; then
    echo "❌ Composer ei ole asennettu. Asenna Composer."
    exit 1
fi

echo "✅ Composer löydetty"

# Install dependencies
echo ""
echo "📦 Asennetaan riippuvuudet..."
composer install

if [ $? -ne 0 ]; then
    echo "❌ Riippuvuuksien asennus epäonnistui"
    exit 1
fi

echo "✅ Riippuvuudet asennettu"

# Check for config.php
if [ ! -f "config.php" ]; then
    echo ""
    echo "⚙️  Luodaan konfiguraatiotiedosto..."
    cp config.example.php config.php
    echo "✅ config.php luotu. Muokkaa tiedostoa ja täytä Twilio-tunnuksesi."
fi

# Database setup
echo ""
echo "💾 Tietokanta-asennus"
echo "Haluatko asentaa tietokannan nyt? (y/n)"
read -r response

if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    echo "Anna MySQL-käyttäjätunnus:"
    read -r DB_USER
    echo "Anna MySQL-salasana:"
    read -s DB_PASS
    echo ""
    echo "Anna tietokannan nimi (default: call_survey):"
    read -r DB_NAME
    DB_NAME=${DB_NAME:-call_survey}
    
    echo "Luodaan tietokanta..."
    mysql -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    
    echo "Ajetaan tietokantaskriptit..."
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database.sql
    
    if [ $? -eq 0 ]; then
        echo "✅ Tietokanta asennettu onnistuneesti"
    else
        echo "❌ Tietokannan asennus epäonnistui"
        exit 1
    fi
fi

echo ""
echo "✅ Asennus valmis!"
echo ""
echo "🚀 Käynnistä kehityspalvelin:"
echo "   php -S localhost:8000 -t public"
echo ""
echo "🌐 Avaa selaimessa:"
echo "   http://localhost:8000"
echo ""
echo "📝 Muista:"
echo "   1. Muokkaa config.php ja täytä Twilio-tunnuksesi"
echo "   2. Aseta tietokannan yhteystiedot config.php:ssä"
echo ""

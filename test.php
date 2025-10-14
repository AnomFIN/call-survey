#!/usr/bin/env php
<?php
/**
 * Test script for Call-Survey ULTRALIGHT
 * Run this to verify that the basic functionality works
 */

echo "📞 Call-Survey ULTRALIGHT - Test Script\n";
echo "========================================\n\n";

// Test 1: Check PHP version
echo "Test 1: PHP Version\n";
$phpVersion = PHP_VERSION;
$requiredVersion = '7.4.0';
if (version_compare($phpVersion, $requiredVersion, '>=')) {
    echo "✅ PHP version $phpVersion (required: >= $requiredVersion)\n\n";
} else {
    echo "❌ PHP version $phpVersion is too old (required: >= $requiredVersion)\n\n";
    exit(1);
}

// Test 2: Check if config.php exists
echo "Test 2: Configuration File\n";
if (file_exists(__DIR__ . '/config.php')) {
    echo "✅ config.php exists\n\n";
} else {
    echo "⚠️  config.php not found. Copy config.example.php to config.php\n\n";
}

// Test 3: Check if composer dependencies are installed
echo "Test 3: Composer Dependencies\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✅ Composer dependencies installed\n\n";
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    echo "❌ Composer dependencies not installed. Run: composer install\n\n";
    exit(1);
}

// Test 4: Check if classes can be loaded
echo "Test 4: Class Loading\n";
try {
    $reflection = new ReflectionClass('CallSurvey\\Database');
    echo "✅ Database class loaded\n";
    
    $reflection = new ReflectionClass('CallSurvey\\Survey');
    echo "✅ Survey class loaded\n";
    
    $reflection = new ReflectionClass('CallSurvey\\TwilioService');
    echo "✅ TwilioService class loaded\n\n";
} catch (Exception $e) {
    echo "❌ Failed to load classes: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 5: Check database connection (if config exists)
if (file_exists(__DIR__ . '/config.php')) {
    echo "Test 5: Database Connection\n";
    try {
        $config = require __DIR__ . '/config.php';
        $db = \CallSurvey\Database::getInstance($config['database']);
        echo "✅ Database connection successful\n\n";
        
        // Test 6: Check if tables exist
        echo "Test 6: Database Tables\n";
        $tables = ['surveys', 'questions', 'responses', 'answers'];
        foreach ($tables as $table) {
            try {
                $db->query("SELECT 1 FROM $table LIMIT 1");
                echo "✅ Table '$table' exists\n";
            } catch (Exception $e) {
                echo "❌ Table '$table' missing. Run database.sql\n";
            }
        }
        echo "\n";
        
    } catch (Exception $e) {
        echo "⚠️  Database connection failed: " . $e->getMessage() . "\n";
        echo "   Check your database configuration in config.php\n\n";
    }
}

// Test 7: Check Twilio SDK
echo "Test 7: Twilio SDK\n";
if (class_exists('Twilio\\Rest\\Client')) {
    echo "✅ Twilio SDK loaded\n\n";
} else {
    echo "❌ Twilio SDK not found\n\n";
}

// Test 8: Check directory structure
echo "Test 8: Directory Structure\n";
$directories = ['src', 'public', 'public/survey'];
foreach ($directories as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "✅ Directory '$dir' exists\n";
    } else {
        echo "❌ Directory '$dir' missing\n";
    }
}
echo "\n";

// Test 9: Check key files
echo "Test 9: Key Files\n";
$files = [
    'public/index.php',
    'public/dashboard.php',
    'public/api.php',
    'public/webhook.php',
    'public/survey/create.php',
    'public/survey/view.php',
    'public/survey/results.php',
];
foreach ($files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ File '$file' exists\n";
    } else {
        echo "❌ File '$file' missing\n";
    }
}
echo "\n";

echo "========================================\n";
echo "🎉 Basic tests completed!\n\n";
echo "Next steps:\n";
echo "1. Make sure config.php has your Twilio credentials\n";
echo "2. Run database.sql to create tables\n";
echo "3. Start the dev server: php -S localhost:8000 -t public\n";
echo "4. Visit http://localhost:8000\n\n";

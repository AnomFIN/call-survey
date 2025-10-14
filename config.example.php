<?php
/**
 * Call-Survey ULTRALIGHT Configuration Example
 * 
 * Copy this file to config.php and fill in your credentials
 */

return [
    // Twilio Configuration
    'twilio' => [
        'account_sid' => 'YOUR_TWILIO_ACCOUNT_SID',
        'auth_token' => 'YOUR_TWILIO_AUTH_TOKEN',
        'phone_number' => 'YOUR_TWILIO_PHONE_NUMBER', // E.164 format: +1234567890
        'whatsapp_number' => 'whatsapp:+1234567890', // Optional WhatsApp number
    ],
    
    // Database Configuration
    'database' => [
        'host' => 'localhost',
        'database' => 'call_survey',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    
    // Application Settings
    'app' => [
        'base_url' => 'http://localhost',
        'timezone' => 'Europe/Helsinki',
    ],
];

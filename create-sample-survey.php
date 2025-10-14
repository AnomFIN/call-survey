<?php
/**
 * Example script to create a sample survey
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load configuration
if (!file_exists(__DIR__ . '/config.php')) {
    die("Error: config.php not found. Copy config.example.php to config.php first.\n");
}

$config = require __DIR__ . '/config.php';

// Initialize database
try {
    $db = \CallSurvey\Database::getInstance($config['database']);
    echo "✅ Database connection successful\n\n";
} catch (Exception $e) {
    die("❌ Database connection failed: " . $e->getMessage() . "\n");
}

// Create survey service
$surveyService = new \CallSurvey\Survey($db);

// Create a sample customer satisfaction survey
echo "Creating sample survey...\n";

$surveyId = $surveyService->create(
    'Asiakastyytyväisyyskysely',
    'Kerro meille kokemuksestasi palvelustamme'
);

echo "✅ Survey created with ID: $surveyId\n\n";

// Add questions
echo "Adding questions...\n";

$questions = [
    [
        'text' => 'Miten tyytyväinen olit palveluumme? Asteikolla 1-5.',
        'type' => 'rating',
        'order' => 1
    ],
    [
        'text' => 'Suosittelisitko palveluamme ystävillesi? Paina 1 kyllä tai 2 ei.',
        'type' => 'yesno',
        'order' => 2
    ],
    [
        'text' => 'Miten hyvin asiakaspalvelija auttoi sinua? Asteikolla 1-5.',
        'type' => 'rating',
        'order' => 3
    ]
];

foreach ($questions as $q) {
    $questionId = $surveyService->addQuestion(
        $surveyId,
        $q['text'],
        $q['type'],
        $q['order']
    );
    echo "  ✅ Question {$q['order']} added (ID: $questionId)\n";
}

echo "\n";
echo "🎉 Sample survey created successfully!\n\n";
echo "Survey ID: $surveyId\n";
echo "Survey Title: Asiakastyytyväisyyskysely\n";
echo "Questions: 3\n\n";

echo "To send this survey via API:\n";
echo "curl -X POST http://localhost:8000/api/send-survey \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{\"survey_id\": $surveyId, \"phone_number\": \"+358401234567\", \"channel\": \"call\"}'\n\n";

echo "Or view it in the dashboard:\n";
echo "http://localhost:8000/\n";

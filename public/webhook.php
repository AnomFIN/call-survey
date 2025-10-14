<?php
/**
 * Twilio Webhook Handler for Voice Calls
 */

header('Content-Type: text/xml');

// Get survey ID from request
$surveyId = $_GET['survey_id'] ?? null;
$responseId = $_SESSION['response_id'] ?? null;

if (!$surveyId) {
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<Response><Say language="fi-FI">Virhe: Kyselyä ei löytynyt.</Say></Response>';
    exit;
}

$surveyService = new \CallSurvey\Survey($db);
$survey = $surveyService->get($surveyId);
$questions = $surveyService->getQuestions($surveyId);

// Start new response if not exists
if (!$responseId) {
    $phoneNumber = $_POST['From'] ?? 'unknown';
    $responseId = $surveyService->startResponse($surveyId, $phoneNumber, 'call');
    $_SESSION['response_id'] = $responseId;
    $_SESSION['current_question'] = 0;
}

$currentQuestion = $_SESSION['current_question'] ?? 0;

// Handle response to previous question
if (isset($_POST['Digits']) && $currentQuestion > 0) {
    $questionId = $questions[$currentQuestion - 1]['id'];
    $answer = $_POST['Digits'];
    $surveyService->saveAnswer($responseId, $questionId, $answer);
}

// Check if survey is complete
if ($currentQuestion >= count($questions)) {
    $surveyService->completeResponse($responseId);
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<Response>';
    echo '<Say language="fi-FI">Kiitos vastauksistasi! Hyvää päivänjatkoa.</Say>';
    echo '<Hangup/>';
    echo '</Response>';
    session_destroy();
    exit;
}

// Ask next question
$question = $questions[$currentQuestion];
$_SESSION['current_question']++;

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<Response>';
echo '<Gather numDigits="1" action="/webhook/voice.php?survey_id=' . $surveyId . '" method="POST">';
echo '<Say language="fi-FI">' . htmlspecialchars($question['question_text']) . '</Say>';

if ($question['question_type'] === 'rating') {
    echo '<Say language="fi-FI">Paina numero yhdestä viiteen.</Say>';
} elseif ($question['question_type'] === 'yesno') {
    echo '<Say language="fi-FI">Paina yksi kyllä, tai kaksi ei.</Say>';
}

echo '</Gather>';
echo '<Say language="fi-FI">Emme saaneet vastaustasi. Hyvästä.</Say>';
echo '</Response>';

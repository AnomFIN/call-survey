<?php
/**
 * API Endpoints for Call-Survey
 */

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Parse action from path
$action = str_replace('/api/', '', strtok($path, '?'));

$surveyService = new \CallSurvey\Survey($db);
$twilioService = new \CallSurvey\TwilioService($config['twilio']);

// Handle different API endpoints
switch ($action) {
    case 'send-survey':
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $surveyId = $data['survey_id'] ?? null;
        $phoneNumber = $data['phone_number'] ?? null;
        $channel = $data['channel'] ?? 'call'; // call, sms, whatsapp
        
        if (!$surveyId || !$phoneNumber) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required parameters']);
            exit;
        }
        
        $survey = $surveyService->get($surveyId);
        if (!$survey) {
            http_response_code(404);
            echo json_encode(['error' => 'Survey not found']);
            exit;
        }
        
        $result = null;
        $webhookUrl = $config['app']['base_url'] . '/webhook/voice.php?survey_id=' . $surveyId;
        
        switch ($channel) {
            case 'call':
                $result = $twilioService->makeCall($phoneNumber, $webhookUrl);
                break;
            case 'sms':
                $message = $survey['title'] . "\n\nVastaa kyselyyn: " . 
                          $config['app']['base_url'] . '/survey/respond.php?id=' . $surveyId;
                $result = $twilioService->sendSMS($phoneNumber, $message);
                break;
            case 'whatsapp':
                $message = $survey['title'] . "\n\nVastaa kyselyyn: " . 
                          $config['app']['base_url'] . '/survey/respond.php?id=' . $surveyId;
                $result = $twilioService->sendWhatsApp($phoneNumber, $message);
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid channel']);
                exit;
        }
        
        echo json_encode($result);
        break;
        
    case 'survey-stats':
        $surveyId = $_GET['id'] ?? null;
        if (!$surveyId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing survey ID']);
            exit;
        }
        
        $results = $surveyService->getResults($surveyId);
        $questions = $surveyService->getQuestions($surveyId);
        
        echo json_encode([
            'survey_id' => $surveyId,
            'total_responses' => count($results),
            'completed_responses' => count(array_filter($results, fn($r) => $r['status'] === 'completed')),
            'questions' => count($questions),
        ]);
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
}

<?php
/**
 * Survey Results View
 */

require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../../config.php';
$db = \CallSurvey\Database::getInstance($config['database']);
$surveyService = new \CallSurvey\Survey($db);

$surveyId = $_GET['id'] ?? null;
if (!$surveyId) {
    header('Location: /');
    exit;
}

$survey = $surveyService->get($surveyId);
if (!$survey) {
    die('Kyselyä ei löytynyt');
}

$questions = $surveyService->getQuestions($surveyId);
$responses = $surveyService->getResults($surveyId);

// Calculate statistics
$totalResponses = count($responses);
$completedResponses = count(array_filter($responses, fn($r) => $r['status'] === 'completed'));
$completionRate = $totalResponses > 0 ? round(($completedResponses / $totalResponses) * 100) : 0;

// Get answers for each question
$questionStats = [];
foreach ($questions as $question) {
    $sql = "SELECT a.answer_value, COUNT(*) as count 
            FROM answers a 
            INNER JOIN responses r ON a.response_id = r.id
            WHERE a.question_id = ? AND r.survey_id = ?
            GROUP BY a.answer_value
            ORDER BY a.answer_value";
    $answers = $db->fetchAll($sql, [$question['id'], $surveyId]);
    
    $questionStats[$question['id']] = [
        'question' => $question,
        'answers' => $answers,
        'total' => array_sum(array_column($answers, 'count'))
    ];
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulokset - <?= htmlspecialchars($survey['title']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-bottom: 10px; }
        h2 { color: #333; margin-bottom: 15px; font-size: 18px; }
        h3 { color: #333; margin-bottom: 10px; font-size: 16px; }
        .meta {
            color: #999;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-box .value {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 5px;
        }
        .stat-box .label {
            color: #666;
            font-size: 14px;
        }
        .question-result {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .question-result h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .answer-bar {
            margin-bottom: 10px;
        }
        .answer-bar .label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .answer-bar .bar {
            height: 30px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }
        .answer-bar .fill {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #45a049);
            display: flex;
            align-items: center;
            padding: 0 10px;
            color: white;
            font-size: 13px;
            font-weight: 500;
        }
        .response-list {
            margin-top: 20px;
        }
        .response-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 4px solid #2196F3;
        }
        .response-item .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .response-item .phone {
            font-weight: 500;
            color: #333;
        }
        .response-item .time {
            color: #999;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 500;
        }
        .badge-success { background: #4CAF50; color: white; }
        .badge-warning { background: #FF9800; color: white; }
        .badge-info { background: #2196F3; color: white; }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2196F3;
            text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/survey/view.php?id=<?= $surveyId ?>" class="back-link">← Takaisin kyselyn hallintaan</a>
        
        <div class="card">
            <h1>Tulokset: <?= htmlspecialchars($survey['title']) ?></h1>
            <div class="meta">
                <?= htmlspecialchars($survey['description']) ?>
            </div>
            
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="value"><?= $totalResponses ?></div>
                    <div class="label">Vastausta yhteensä</div>
                </div>
                <div class="stat-box">
                    <div class="value"><?= $completedResponses ?></div>
                    <div class="label">Valmistunutta</div>
                </div>
                <div class="stat-box">
                    <div class="value"><?= $completionRate ?>%</div>
                    <div class="label">Valmistumisaste</div>
                </div>
                <div class="stat-box">
                    <div class="value"><?= count($questions) ?></div>
                    <div class="label">Kysymystä</div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($questionStats)): ?>
            <div class="card">
                <h2>Vastaukset kysymyksittäin</h2>
                
                <?php foreach ($questionStats as $qId => $stat): ?>
                    <div class="question-result">
                        <h3>
                            <?= $stat['question']['question_order'] ?>. 
                            <?= htmlspecialchars($stat['question']['question_text']) ?>
                            <span class="badge badge-info"><?= $stat['question']['question_type'] ?></span>
                        </h3>
                        
                        <?php if (empty($stat['answers'])): ?>
                            <p style="color: #999;">Ei vastauksia</p>
                        <?php else: ?>
                            <?php 
                            $maxCount = max(array_column($stat['answers'], 'count'));
                            foreach ($stat['answers'] as $answer): 
                                $percentage = $stat['total'] > 0 ? round(($answer['count'] / $stat['total']) * 100) : 0;
                                $barWidth = $maxCount > 0 ? ($answer['count'] / $maxCount) * 100 : 0;
                            ?>
                                <div class="answer-bar">
                                    <div class="label">
                                        <span>Vastaus: <?= htmlspecialchars($answer['answer_value']) ?></span>
                                        <span><?= $answer['count'] ?> vastausta (<?= $percentage ?>%)</span>
                                    </div>
                                    <div class="bar">
                                        <div class="fill" style="width: <?= $barWidth ?>%">
                                            <?php if ($barWidth > 15): ?>
                                                <?= $answer['count'] ?> kpl
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Vastaukset (<?= count($responses) ?>)</h2>
            
            <?php if (empty($responses)): ?>
                <div class="empty-state">
                    <p>Ei vastauksia vielä.</p>
                </div>
            <?php else: ?>
                <div class="response-list">
                    <?php foreach ($responses as $response): ?>
                        <div class="response-item">
                            <div class="header">
                                <span class="phone"><?= htmlspecialchars($response['phone_number']) ?></span>
                                <span class="time"><?= date('d.m.Y H:i', strtotime($response['started_at'])) ?></span>
                            </div>
                            <div>
                                <span class="badge badge-<?= $response['status'] === 'completed' ? 'success' : 'warning' ?>">
                                    <?= htmlspecialchars($response['status']) ?>
                                </span>
                                <span class="badge badge-info"><?= htmlspecialchars($response['channel']) ?></span>
                                <span style="margin-left: 10px; font-size: 13px; color: #666;">
                                    Vastauksia: <?= $response['answer_count'] ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

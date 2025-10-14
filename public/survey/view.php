<?php
/**
 * View and manage survey
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

// Handle adding question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $questionText = trim($_POST['question_text'] ?? '');
    $questionType = $_POST['question_type'] ?? 'rating';
    $order = count($questions) + 1;
    
    if (!empty($questionText)) {
        $surveyService->addQuestion($surveyId, $questionText, $questionType, $order);
        header('Location: /survey/view.php?id=' . $surveyId);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($survey['title']) ?> - Call-Survey</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-bottom: 10px; }
        h2 { color: #333; margin-bottom: 15px; font-size: 18px; }
        .meta {
            color: #999;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .question-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 4px solid #2196F3;
        }
        .question-item .number {
            display: inline-block;
            background: #2196F3;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            font-size: 12px;
            margin-right: 10px;
        }
        .question-item .type {
            display: inline-block;
            background: #e0e0e0;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
            margin-left: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        input[type="text"], select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover { background: #45a049; }
        .btn-secondary { background: #2196F3; }
        .btn-secondary:hover { background: #0b7dda; }
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2196F3;
            text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
        }
        .webhook-url {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 13px;
            word-break: break-all;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Takaisin hallintapaneeliin</a>
        
        <div class="card">
            <h1><?= htmlspecialchars($survey['title']) ?></h1>
            <div class="meta">
                <?= htmlspecialchars($survey['description']) ?><br>
                Luotu: <?= date('d.m.Y H:i', strtotime($survey['created_at'])) ?>
            </div>
            
            <h2>Webhook URL (Twilio Voice)</h2>
            <div class="webhook-url">
                <?= htmlspecialchars($config['app']['base_url']) ?>/webhook/voice.php?survey_id=<?= $surveyId ?>
            </div>
        </div>
        
        <div class="card">
            <h2>Kysymykset (<?= count($questions) ?>)</h2>
            
            <?php if (empty($questions)): ?>
                <div class="empty-state">
                    <p>Ei kysymyksiä. Lisää ensimmäinen kysymys alla.</p>
                </div>
            <?php else: ?>
                <?php foreach ($questions as $question): ?>
                    <div class="question-item">
                        <span class="number"><?= $question['question_order'] ?></span>
                        <?= htmlspecialchars($question['question_text']) ?>
                        <span class="type"><?= htmlspecialchars($question['question_type']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Lisää kysymys</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="question_text">Kysymysteksti</label>
                    <textarea id="question_text" name="question_text" required
                              placeholder="Esim. Miten tyytyväinen olit palveluumme?"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="question_type">Kysymystyyppi</label>
                    <select id="question_type" name="question_type">
                        <option value="rating">Arviointi (1-5)</option>
                        <option value="yesno">Kyllä/Ei</option>
                        <option value="text">Tekstivastaus</option>
                    </select>
                </div>
                
                <button type="submit" name="add_question" class="btn">Lisää kysymys</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Lähetä kysely</h2>
            <p style="margin-bottom: 15px; color: #666;">Käytä API:a lähettääksesi tämän kyselyn asiakkaille.</p>
            
            <div style="display: flex; gap: 10px;">
                <a href="/survey/results.php?id=<?= $surveyId ?>" class="btn btn-secondary">
                    Näytä tulokset
                </a>
            </div>
        </div>
    </div>
</body>
</html>

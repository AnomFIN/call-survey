<?php
/**
 * Create New Survey
 */

require_once __DIR__ . '/../../vendor/autoload.php';
$config = require __DIR__ . '/../../config.php';
$db = \CallSurvey\Database::getInstance($config['database']);
$surveyService = new \CallSurvey\Survey($db);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if (empty($title)) {
        $error = 'Otsikko on pakollinen';
    } else {
        $surveyId = $surveyService->create($title, $description);
        $success = "Kysely luotu onnistuneesti! ID: $surveyId";
    }
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luo uusi kysely - Call-Survey</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container { max-width: 800px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-bottom: 20px; }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover { background: #45a049; }
        .btn-secondary {
            background: #999;
            margin-left: 10px;
        }
        .btn-secondary:hover { background: #777; }
        .error {
            background: #f44336;
            color: white;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success {
            background: #4CAF50;
            color: white;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2196F3;
            text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Takaisin hallintapaneeliin</a>
        
        <div class="card">
            <h1>Luo uusi kysely</h1>
            
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success">
                    <?= htmlspecialchars($success) ?>
                    <br><br>
                    <a href="/survey/view.php?id=<?= $surveyId ?>" style="color: white; text-decoration: underline;">
                        Siirry kyselyn hallintaan →
                    </a>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="title">Kyselyn otsikko *</label>
                    <input type="text" id="title" name="title" required 
                           placeholder="Esim. Asiakastyytyväisyyskysely">
                </div>
                
                <div class="form-group">
                    <label for="description">Kuvaus</label>
                    <textarea id="description" name="description" 
                              placeholder="Kerro lyhyesti kyselyn tarkoituksesta"></textarea>
                </div>
                
                <button type="submit" class="btn">Luo kysely</button>
                <a href="/" class="btn btn-secondary">Peruuta</a>
            </form>
        </div>
    </div>
</body>
</html>

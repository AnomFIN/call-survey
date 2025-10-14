<?php
/**
 * Dashboard - Survey Management Interface
 */

$surveyService = new \CallSurvey\Survey($db);
$surveys = $surveyService->getActive();

?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Call-Survey ULTRALIGHT - Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 20px; }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .survey-list { display: grid; gap: 15px; }
        .survey-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #4CAF50;
        }
        .survey-item h3 { color: #333; margin-bottom: 8px; }
        .survey-item p { color: #666; font-size: 14px; }
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
        .actions { margin-top: 10px; display: flex; gap: 10px; }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📞 Call-Survey ULTRALIGHT</h1>
        
        <div class="card">
            <h2 style="margin-bottom: 15px;">Kyselyt</h2>
            <button class="btn" onclick="location.href='/survey/create.php'">+ Luo uusi kysely</button>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 15px;">Aktiiviset kyselyt</h3>
            <?php if (empty($surveys)): ?>
                <div class="empty-state">
                    <p>Ei aktiivisia kyselyitä. Luo ensimmäinen kyselysi!</p>
                </div>
            <?php else: ?>
                <div class="survey-list">
                    <?php foreach ($surveys as $survey): ?>
                        <div class="survey-item">
                            <h3><?= htmlspecialchars($survey['title']) ?></h3>
                            <p><?= htmlspecialchars($survey['description']) ?></p>
                            <p style="font-size: 12px; color: #999; margin-top: 5px;">
                                Luotu: <?= date('d.m.Y H:i', strtotime($survey['created_at'])) ?>
                            </p>
                            <div class="actions">
                                <a href="/survey/view.php?id=<?= $survey['id'] ?>" class="btn">Näytä</a>
                                <a href="/survey/results.php?id=<?= $survey['id'] ?>" class="btn btn-secondary">Tulokset</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

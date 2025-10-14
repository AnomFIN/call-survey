<?php
declare(strict_types=1);

$step = isset($_GET['step']) ? (int) $_GET['step'] : 1;
$steps = [
    1 => 'Yhteysasetukset',
    2 => 'Twilio-konfiguraatio',
    3 => 'Varmistus',
];
?><!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Asennus · AnomFIN Call-Survey ULTRALIGHT</title>
    <link rel="stylesheet" href="../public/css/style.css" />
</head>
<body>
    <main class="app-main" style="grid-template-columns: 1fr;">
        <section class="app-showcase" style="box-shadow:none;">
            <h1 class="app-hero__title" style="font-size:2.5rem;">Asennuswizard</h1>
            <p class="app-hero__body">Täytä vaiheittain tietokanta-, Twilio- ja turva-asetukset. Wizard kirjoittaa `.env`-tiedoston valittuun polkuun.</p>
            <ol>
                <?php foreach ($steps as $number => $label): ?>
                    <li style="margin-bottom:0.75rem;">
                        <strong>Vaihe <?php echo $number; ?>:</strong> <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        <?php if ($number === $step): ?>
                            <span class="app-cta app-cta--ghost" style="margin-left:0.5rem;">Aktiivinen</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
            <p class="app-hero__body">Kun wizard on valmis, poista `install/`-hakemisto tai aseta palvelimelle <code>ANOMFIN_INSTALL_ACTIVE=0</code>.</p>
        </section>
    </main>
</body>
</html>

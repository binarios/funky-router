<?php
// Eine einfache View-Datei
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Keine Überschrift') ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($title ?? 'Willkommen') ?></h1>
    <p>Dies ist die Home-Ansicht.</p>
</body>
</html>
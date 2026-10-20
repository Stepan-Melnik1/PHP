<?php
declare(strict_types=1);

$dir = "image/";
$files = scandir($dir);

if ($files === false) {
    $files = [];
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галерея</title>
    <style>
        body { font-family: sans-serif; margin: 0; }
        header, footer { background: #333; color: #fff; padding: 16px; text-align: center; }
        nav ul { list-style: none; display: flex; gap: 16px; justify-content: center; padding: 12px; margin: 0; background: #eee; }
        main { padding: 16px; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
        .gallery img { width: 100%; height: 200px; object-fit: cover; }
    </style>
</head>
<body>
    <header><h1>Моя галерея</h1></header>
    <nav>
        <ul>
            <li><a href="#">Главная</a></li>
            <li><a href="#">Галерея</a></li>
            <li><a href="#">Контакты</a></li>
        </ul>
    </nav>
    <main>
        <div class="gallery">
        <?php foreach ($files as $file): ?>
            <?php if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === "jpg"): ?>
                <img src="<?= htmlspecialchars($dir . $file) ?>" alt="">
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
    </main>
    <footer>© 2026</footer>
</body>
</html>

<?php
// db.php
$driver  = 'sqlite'; // 'sqlite' (défaut) ou 'mysql'
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    if ($driver === 'sqlite') {
        $sqlitePath = '/data/app.sqlite';
        $dsn = "sqlite:" . $sqlitePath;
        $pdo = new PDO($dsn, null, null, $options);
    } elseif ($driver === 'mysql') {
        $host = '127.0.0.1';
        $port = '3306';
        $name = 'demo_app';
        $user = 'demo_user';
        $pass = 'change_me';
        $dsn  = "mysql:host=$host;port=$port;dbname=$name;charset=$charset";
        $pdo  = new PDO($dsn, $user, $pass, $options);
    } else {
        throw new RuntimeException("DB_DRIVER inconnu: $driver");
    }
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Erreur de connexion BD. Vérifie les variables d'environnement.\n";
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
    }
    exit;
}

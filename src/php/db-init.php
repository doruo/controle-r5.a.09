<?php
// docker-init.php
function p($msg) {
    if (PHP_SAPI === 'cli') {
        fwrite(STDOUT, "[init] $msg\n");
    } else {
        error_log("[init] $msg");
        echo "[init] $msg<br>";
    }
}

$driver  = 'mysql';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $host = 'bd-dev';
    $port = '3306';
    $db   = 'demo_app';
    $user = 'demo_user';
    $pass = 'change_me';

    $pdo = null;
    $deadline = time() + 30;
    $dsnNoDb = "mysql:host=$host;port=$port;charset=$charset";
    while (time() < $deadline) {
        try {
            $pdo = new PDO($dsnNoDb, $user, $pass, $options);
            break;
        } catch (Throwable $e) {
            p("MySQL indisponible, nouvel essai...");
            sleep(1);
        }
    }
    if (!$pdo) { throw new RuntimeException("Impossible de se connecter à MySQL ($host:$port)."); }

    p("Connecté à MySQL. Création base si besoin…");
    $collate = ($charset === 'utf8mb4') ? 'utf8mb4_unicode_ci' : "{$charset}_general_ci";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET $charset COLLATE $collate");

    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, $options);

    $sqlFile = 'init.sql';
    if (!is_file($sqlFile)) throw new RuntimeException("init.sql introuvable: $sqlFile");
    $sql = str_replace('__DB_NAME__', $db, file_get_contents($sqlFile));

    foreach (array_filter(array_map('trim', explode(';', $sql))) as $st) {
        if ($st !== '') $pdo->exec($st);
    }
    p("Initialisation MySQL OK.");
} catch (Throwable $e) {
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, "[init][ERREUR] " . $e->getMessage() . PHP_EOL);
    } else {
        error_log("[init][ERREUR] " . $e->getMessage());
        echo "[init][ERREUR] " . $e->getMessage();
    }
    exit(1);
}

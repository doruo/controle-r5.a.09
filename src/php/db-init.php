<?php
// docker-init.php
function p($msg) { fwrite(STDOUT, "[init] $msg\n"); }

$driver  = 'sqlite';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    if ($driver === 'mysql') {
        $host = '127.0.0.1';
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

        $sqlFile = '/usr/local/bin/init.sql';
        if (!is_file($sqlFile)) throw new RuntimeException("init.sql introuvable: $sqlFile");
        $sql = str_replace('__DB_NAME__', $db, file_get_contents($sqlFile));

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $st) {
            if ($st !== '') $pdo->exec($st);
        }
        p("Initialisation MySQL OK.");
    } else {
        $sqlitePath = '/data/app.sqlite';
        @mkdir(dirname($sqlitePath), 0775, true);
        $dsn = "sqlite:" . $sqlitePath;
        $pdo = new PDO($dsn, null, null, $options);

        p("SQLite utilisé: $sqlitePath");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS items (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              label TEXT NOT NULL,
              created_at TEXT DEFAULT (datetime('now'))
            )
        ");

        $count = (int)$pdo->query("SELECT COUNT(*) AS c FROM items")->fetch()['c'];
        if ($count === 0) {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO items (label) VALUES (:label)");
            foreach (['Premier tuple','Deuxième tuple','Troisième tuple'] as $lab) {
                $stmt->execute([':label' => $lab]);
            }
            $pdo->commit();
            p("Données seed insérées (SQLite).");
        } else {
            p("Données déjà présentes (SQLite), rien à faire.");
        }
    }
} catch (Throwable $e) {
    fwrite(STDERR, "[init][ERREUR] " . $e->getMessage() . PHP_EOL);
    exit(1);
}

<?php
// insert.php
require __DIR__ . '/db.php';

// Supporte application/x-www-form-urlencoded, multipart/form-data, et JSON
header('Content-Type: application/json; charset=utf-8');

// Récupération des données POST
$input = null;
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (stripos($contentType, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);
    if (!is_array($input)) { $input = []; }
    $label = trim((string)($input['label'] ?? ''));
} else {
    $label = trim((string)($_POST['label'] ?? ''));
}

if ($label === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => "Champ 'label' manquant ou vide."]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO items (label) VALUES (:label)");
    $stmt->execute([':label' => $label]);
    $id = (int)$pdo->lastInsertId();

    echo json_encode([
        'ok' => true,
        'id' => $id,
        'label' => $label,
        'created_at' => (new DateTime())->format('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erreur serveur.']);
    // En CLI, afficher l'erreur technique
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
    }
    exit;
}

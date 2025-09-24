<?php
// list.php
require __DIR__ . '/db.php';

// Option simple: sortie HTML
$stmt = $pdo->query("SELECT id, label, created_at FROM items ORDER BY id ASC");
$rows = $stmt->fetchAll();

// Si on demande JSON (en ajoutant ?format=json ou en envoyant Accept: application/json)
$wantJson = (isset($_GET['format']) && $_GET['format'] === 'json')
    || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

if ($wantJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Liste des tuples</title>
  <style>
    body { font-family: system-ui, Arial, sans-serif; margin: 2rem; }
    table { border-collapse: collapse; width: 600px; max-width: 100%; }
    th, td { border: 1px solid #ddd; padding: .5rem .75rem; }
    th { background: #f6f6f6; text-align: left; }
    caption { text-align: left; margin-bottom: .5rem; font-weight: 600; }
  </style>
</head>
<body>
  <h1>Liste des tuples</h1>
  <p>form.phpAjouter un tuple</a></p>
  <table>
    <caption>Table <code>items</code></caption>
    <thead>
      <tr>
        <th>ID</th>
        <th>Label</th>
        <th>Créé le</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="3"><em>Aucune donnée</em></td></tr>
      <?php else: ?>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($r['created_at'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>

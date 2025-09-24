<?php /* form.php */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Ajouter un tuple</title>
  <style>
    body { font-family: system-ui, Arial, sans-serif; margin: 2rem; }
    form { display: grid; gap: .75rem; max-width: 420px; }
    label { font-weight: 600; }
    input[type="text"] { padding: .5rem; border: 1px solid #ccc; border-radius: 4px; }
    button { padding: .5rem .75rem; border: 1px solid #0b5; background: #0c6; color: #fff; border-radius: 4px; cursor: pointer; }
    button:hover { background: #0b5; }
    .note { color: #666; font-size: .9rem; }
    pre { background: #f6f6f6; padding: .75rem; border-radius: 6px; overflow:auto; }
  </style>
</head>
<body>
  <h1>Ajouter un tuple</h1>
  <form action="insert.php" method="post">
    <div>
      <label for="label">Label</label><br>
      <input type="text" name="label" id="label" required placeholder="Ex: Nouveau tuple">
    </div>
    <button type="submit">Insérer</button>
  </form>

  <p class="note">Après insertion, consulte <a href="listste</a>.</p>

  <hr>
  <h2>Appeler via curl</h2>
  <pre><code>curl -X POST -d "label=Depuis le formulaire (doc)" http://localhost/mini-app/insert.php</code></pre>
</body>
</html>

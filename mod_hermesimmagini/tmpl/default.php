<?php defined('_JEXEC') or die;
$db = JFactory::getDbo();

// GESTIONE UPLOAD MULTIPLO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event_id']) && isset($_FILES['new_images'])) {
    $addEventId = (int) $_POST['add_event_id'];
    $files = $_FILES['new_images'];
    $uploadDir = 'images/eventi/';
    $uploadPath = JPATH_ROOT . '/' . $uploadDir;
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $filename = basename($files['name'][$i]);
            $tmpFile = $files['tmp_name'][$i];
            $targetFile = $uploadPath . $filename;
            if (move_uploaded_file($tmpFile, $targetFile)) {
                $query = $db->getQuery(true)
                    ->insert($db->qn('hermes_immagini'))
                    ->columns([$db->qn('id_evento'), $db->qn('nome_file'), $db->qn('percorso_file')])
                    ->values($db->quote($addEventId) . ', ' . $db->quote($filename) . ', ' . $db->quote($uploadDir . $filename));
                $db->setQuery($query)->execute();
                echo '<div class="alert alert-success mt-2">✅ Immagine "' . $filename . '" aggiunta a evento ' . $addEventId . '.</div>';
            }
        }
    }
}

// ELIMINAZIONE
if (isset($_POST['delete_image_id'])) {
    $deleteId = (int) $_POST['delete_image_id'];
    $query = $db->getQuery(true)
        ->select('percorso_file')
        ->from($db->qn('hermes_immagini'))
        ->where('id = ' . $deleteId);
    $db->setQuery($query);
    $path = $db->loadResult();
    if ($path && file_exists(JPATH_ROOT . '/' . $path)) {
        unlink(JPATH_ROOT . '/' . $path);
    }
    $query = $db->getQuery(true)
        ->delete($db->qn('hermes_immagini'))
        ->where('id = ' . $deleteId);
    $db->setQuery($query)->execute();
    echo '<div class="alert alert-success mt-2">✅ Immagine eliminata.</div>';
}

// SOSTITUZIONE
if (isset($_POST['replace_image_id']) && isset($_FILES['replace_file']) && $_FILES['replace_file']['error'] === UPLOAD_ERR_OK) {
    $replaceId = (int) $_POST['replace_image_id'];
    $query = $db->getQuery(true)
        ->select('percorso_file')
        ->from($db->qn('hermes_immagini'))
        ->where('id = ' . $replaceId);
    $db->setQuery($query);
    $oldPath = $db->loadResult();
    if ($oldPath && file_exists(JPATH_ROOT . '/' . $oldPath)) {
        unlink(JPATH_ROOT . '/' . $oldPath);
    }
    $newFile = $_FILES['replace_file'];
    $uploadDir = 'images/eventi/';
    $uploadPath = JPATH_ROOT . '/' . $uploadDir;
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    $newName = basename($newFile['name']);
    $newPath = $uploadDir . $newName;
    if (move_uploaded_file($newFile['tmp_name'], $uploadPath . $newName)) {
        $query = $db->getQuery(true)
            ->update($db->qn('hermes_immagini'))
            ->set([
                $db->qn('nome_file') . ' = ' . $db->quote($newName),
                $db->qn('percorso_file') . ' = ' . $db->quote($newPath)
            ])
            ->where('id = ' . $replaceId);
        $db->setQuery($query)->execute();
        echo '<div class="alert alert-success mt-2">✅ Immagine sostituita.</div>';
    }
}

$query = $db->getQuery(true)
    ->select('*')
    ->from($db->qn('hermes_immagini'));
$db->setQuery($query);
$images = $db->loadObjectList();
?>

<div class="hermesimporter mt-4">
    <form method="post" enctype="multipart/form-data" class="mb-4">
        <fieldset class="border p-3">
            <legend class="mb-3">Aggiungi immagini a un evento</legend>
            <div class="mb-3">
                <label for="add_event_id" class="form-label">ID Evento</label>
                <input type="number" name="add_event_id" id="add_event_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="new_images" class="form-label">Seleziona immagini</label>
                <input type="file" name="new_images[]" id="new_images" class="form-control" multiple required>
            </div>
            <button type="submit" class="btn btn-primary">Carica Immagini</button>
        </fieldset>
    </form>

    <?php if ($images): ?>
        <fieldset class="border p-3">
            <legend class="mb-3">Gestione immagini caricate</legend>
            <div class="row">
                <?php foreach ($images as $img): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="/<?= htmlspecialchars($img->percorso_file) ?>" class="card-img-top" style="object-fit:cover; height:200px;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($img->nome_file) ?></h5>
                                <p class="card-text"><strong>ID Evento:</strong> <?= (int)$img->id_evento ?></p>
                                <form method="post" enctype="multipart/form-data" class="mb-2">
                                    <input type="hidden" name="replace_image_id" value="<?= (int)$img->id ?>">
                                    <input type="file" name="replace_file" class="form-control mb-2" required>
                                    <button type="submit" class="btn btn-warning w-100">Sostituisci</button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="delete_image_id" value="<?= (int)$img->id ?>">
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Sicuro di voler eliminare questa immagine?');">Elimina</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </fieldset>
    <?php else: ?>
        <div class="alert alert-info">Nessuna immagine trovata nel database.</div>
    <?php endif; ?>
</div>

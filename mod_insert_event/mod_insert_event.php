<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

require_once __DIR__ . '/helper.php';

$input = Factory::getApplication()->input;
$moduleId = $module->id;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input->get('mod_id') == $moduleId) {
    $data = [
        'data'       => $input->getString('data'),
        'ora'        => $input->getString('ora'),
        'tipo'       => $input->getString('tipo'),
        'area'       => $input->getString('area'),
        'stazione'   => $input->getString('stazione'),
        'componente' => $input->getString('componente'),
        'note'       => $input->getString('note'),
        'Md'         => $input->getString('Md'),
        'profondita' => $input->getString('profondita'),
        'lat'        => $input->getString('lat'),
        'lon'        => $input->getString('lon')
    ];

    $idEvento = ModInsertEventHelper::salvaEvento($data);

    if ($idEvento) {
        $db = Factory::getDbo();

        $images = $input->files->get('immagine', [], 'array');
        if (!empty($images['name'][0])) {
            $uploadDir  = 'images/eventi/';
            $uploadPath = JPATH_ROOT . '/' . $uploadDir;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            for ($i = 0, $n = count($images['name']); $i < $n; $i++) {
                if ($images['error'][$i] === UPLOAD_ERR_OK) {
                    $filename   = basename($images['name'][$i]);
                    $tmpFile    = $images['tmp_name'][$i];
                    $targetFile = $uploadPath . $filename;
                    if (move_uploaded_file($tmpFile, $targetFile)) {
                        $query = $db->getQuery(true)
                            ->insert($db->qn('hermes_immagini'))
                            ->columns([$db->qn('id_evento'), $db->qn('nome_file'), $db->qn('percorso_file')])
                            ->values((int) $idEvento . ', ' . $db->quote($filename) . ', ' . $db->quote($uploadDir . $filename));
                        $db->setQuery($query)->execute();
                    }
                }
            }
        }

        $files = $input->files->get('file_evento', [], 'array');
        if (!empty($files['name'][0])) {
            $uploadDir  = 'files/eventi/';
            $uploadPath = JPATH_ROOT . '/' . $uploadDir;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            for ($i = 0, $n = count($files['name']); $i < $n; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $filename   = basename($files['name'][$i]);
                    $tmpFile    = $files['tmp_name'][$i];
                    $targetFile = $uploadPath . $filename;
                    if (move_uploaded_file($tmpFile, $targetFile)) {
                        $query = $db->getQuery(true)
                            ->insert($db->qn('hermes_immagini'))
                            ->columns([$db->qn('id_evento'), $db->qn('nome_file'), $db->qn('percorso_file')])
                            ->values((int) $idEvento . ', ' . $db->quote($filename) . ', ' . $db->quote($uploadDir . $filename));
                        $db->setQuery($query)->execute();
                    }
                }
            }
        }

        $esito = true;
    } else {
        $esito = false;
    }
}

require ModuleHelper::getLayoutPath('mod_insert_event', 'default');

?>

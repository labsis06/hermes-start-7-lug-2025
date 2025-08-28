<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

require_once __DIR__ . '/helper.php';

$input = Factory::getApplication()->input;
$moduleId = $module->id;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $input->get('mod_id') == $moduleId) {
        $data = [
                'data'      => $input->getString('data'),
                'ora'       => $input->getString('ora'),
                'tipo'      => $input->getString('tipo'),
                'area'      => $input->getString('area'),
                'stazione'  => $input->getString('stazione'),
                'componente'=> $input->getString('componente'),
            'note'=> $input->getString('note'),
        'Md'=> $input->getString('Md'),
        'profondita'=> $input->getString('profondita'),
        'lat'=> $input->getString('lat'),
        'lon'=> $input->getString('lon')
           ];

        $imageFile = $_FILES['immagine'] ?? null;
        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
                $imgDir = JPATH_ROOT . '/images/eventi/';
                if (!is_dir($imgDir)) {
                        mkdir($imgDir, 0755, true);
                }
                $imgName = basename($imageFile['name']);
                $imgPath = $imgDir . $imgName;
                if (move_uploaded_file($imageFile['tmp_name'], $imgPath)) {
                        $data['immagine_nome'] = $imgName;
                        $data['immagine_percorso'] = 'images/eventi/' . $imgName;
                }
        }

        $file = $_FILES['file_evento'] ?? null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $fileDir = JPATH_ROOT . '/files/eventi/';
                if (!is_dir($fileDir)) {
                        mkdir($fileDir, 0755, true);
                }
                $fileName = basename($file['name']);
                $filePath = $fileDir . $fileName;
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                        $data['file_nome'] = $fileName;
                        $data['file_percorso'] = $filePath;
                }
        }

echo '<pre>Dati ricevuti in PHP:</pre>';
print_r($data);
  
	$esito = ModInsertEventHelper::salvaEvento($data);
echo '<pre>Funzione eseguita. Esito: ' . var_export($esito, true) . '</pre>';
}

echo '<pre>';
print_r($_POST);
echo '</pre>';

require ModuleHelper::getLayoutPath('mod_insert_event', 'default');

?>

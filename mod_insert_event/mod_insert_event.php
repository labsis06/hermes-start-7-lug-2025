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

        $data['immagini'] = [];
        if (!empty($_FILES['immagine']) && is_array($_FILES['immagine']['name'])) {
                $uploadDir = 'images/eventi/';
                $uploadPath = JPATH_ROOT . '/' . $uploadDir;
                if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                }
                foreach ($_FILES['immagine']['name'] as $k => $nome) {
                        if ($_FILES['immagine']['error'][$k] === UPLOAD_ERR_OK) {
                                $tmpName = $_FILES['immagine']['tmp_name'][$k];
                                $filename = basename($nome);
                                $target = $uploadPath . $filename;
                                if (move_uploaded_file($tmpName, $target)) {
                                        $data['immagini'][] = [
                                                'nome' => $filename,
                                                'percorso' => $uploadDir . $filename
                                        ];
                                }
                        }
                }
        }

        $data['files'] = [];
        if (!empty($_FILES['file_evento']) && is_array($_FILES['file_evento']['name'])) {
                $uploadDir = 'files/eventi/';
                $uploadPath = JPATH_ROOT . '/' . $uploadDir;
                if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                }
                foreach ($_FILES['file_evento']['name'] as $k => $nome) {
                        if ($_FILES['file_evento']['error'][$k] === UPLOAD_ERR_OK) {
                                $tmpName = $_FILES['file_evento']['tmp_name'][$k];
                                $filename = basename($nome);
                                $target = $uploadPath . $filename;
                                if (move_uploaded_file($tmpName, $target)) {
                                        $data['files'][] = [
                                                'nome' => $filename,
                                                'percorso' => $uploadDir . $filename
                                        ];
                                }
                        }
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

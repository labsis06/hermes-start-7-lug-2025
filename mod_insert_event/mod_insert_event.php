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

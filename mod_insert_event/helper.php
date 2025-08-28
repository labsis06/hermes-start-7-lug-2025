<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class ModInsertEventHelper
{
    public static function salvaEvento($data)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // Inserimento in hermes_eventi
        $columns = ['data', 'ora', 'tipo', 'area', 'stazione_first', 'comp1', 'note', 'Md', 'prof', 'lat', 'lon'];
        $values = [
            $db->quote($data['data']),
            $db->quote($data['ora']),
            $db->quote($data['tipo']),
            $db->quote($data['area']),
            $db->quote($data['stazione']),
            $db->quote($data['componente']),
            $db->quote($data['note']),
            $db->quote($data['Md']),
            $db->quote($data['profondita']),
            $db->quote($data['lat']),
            $db->quote($data['lon'])
        ];

        $query
            ->insert($db->quoteName('hermes_eventi'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        try {
            $db->setQuery($query);
            $db->execute();

            return (int) $db->insertid();
        } catch (Exception $e) {
            echo '<pre><strong>ERRORE:</strong> ' . $e->getMessage() . '</pre>';
            return false;
        }
    }
}

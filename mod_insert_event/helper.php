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

            // ID dell'evento appena inserito
            $idEvento = $db->insertid();

            // Se è stata caricata un'immagine, inserisci anche in hermes_immagini
            if (!empty($data['immagine_nome']) && !empty($data['immagine_percorso'])) {
                $query = $db->getQuery(true);

                $columns = ['id_evento', 'nome_file', 'percorso_file'];
                $values = [
                    (int) $idEvento,
                    $db->quote($data['immagine_nome']),
                    $db->quote($data['immagine_percorso'])
                ];

                $query
                    ->insert($db->quoteName('hermes_immagini'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));

                $db->setQuery($query);
                $db->execute();
            }

            // Se è stato caricato un file, inserisci anche in hermes_files
            if (!empty($data['file_nome']) && !empty($data['file_percorso'])) {
                $query = $db->getQuery(true);

                $columns = ['id_evento', 'nome_file', 'percorso_file'];
                $values = [
                    (int) $idEvento,
                    $db->quote($data['file_nome']),
                    $db->quote($data['file_percorso'])
                ];

                $query
                    ->insert($db->quoteName('hermes_files'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));

                $db->setQuery($query);
                $db->execute();
            }

            return true;
        } catch (Exception $e) {
            echo '<pre><strong>ERRORE:</strong> ' . $e->getMessage() . '</pre>';
            return false;
        }
    }
}

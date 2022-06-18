<?php

require_once __DIR__ . "/database-manager.php";


/**
 * Stampa tutte le <pre><option></pre> che formano il menù a tendina con tutte le
 * professioni presenti sul database.
 * Mostra come placeholder {@link $selected} oppure "Professione" se {@link $selected} è vuoto
 *
 * @param string $selected Professione da mostrare come default (se esiste)
 */
function buildMenu($selected = '') {
    global $database;

    // Mostra il placeholder "Professione" (grazie all'attributo selected)
    if($selected === '')
        echo '<option value="" disabled selected hidden>Professione</option>';


    // Elenco di professioni nel tag <option>
    $result = $database->query("SELECT Nome FROM professione");
    while ($row = $database->fetch($result)) {
        $professione = $row['Nome'];

        echo '<option value="' . $professione . '"';
        if($selected !== '' && $professione === $selected) // Mostra come placeholder (attributo selected) la professione specificata
            echo ' selected';
        echo '>' . $professione . '</option>';
    }

    $database->closeConnection();
}
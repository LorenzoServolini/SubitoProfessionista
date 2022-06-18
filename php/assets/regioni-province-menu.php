<?php

require_once __DIR__ . "/database-manager.php";


/** Costruisce un menù contenente tutte le province italiane (organizzate per regioni)
 *
 * @param int|null $tabIndex Numero da inserire come attributo "tabindex", null se non deve esserci il tabindex
 * @param string $label Etichetta (label) del campo di input
 * @param string|null $title Informazioni da inserire nell'attributo "title", null se non deve esserci il title
 * @param bool $required True se per sottomettere la form è necessario selezionare una voce nel menù, false altrimenti
 * @param int $size Numero di province visibili nel menù
 * @param array|null Elenco delle province da mostrare come già selezionate
 */
function buildProvincesMenu($tabIndex, $label, $title, $required = false, $size = 6, $selected = null) {
    global $database;

    if($required)
        $label = '<mark>' . $label . '</mark>'; // Sottolinea il campo per segnalare l'obbligatorietà

    if($label === '')
        echo '<label for="province"></label>';
    else
        echo '<label for="province"' . ($title === null ? '' : ' title="' . $title . '"') . '><b>' .  $label . '</b></label>';
    echo '<select class="input-field" id="province" name="province[]" title="' . $title . '"' . ($tabIndex === null ? '' : ' tabindex="' . $tabIndex . '"') . ($required ? ' required' : '') . ' size="' . $size . '" multiple>';


    $regioni = $database->query("SELECT Nome FROM regione"); // Elenco delle regioni
    while ($row = $database->fetch($regioni)) {
        $regione = $row['Nome'];

        echo '<optgroup label="' . $regione . '">';

        /*
         * str_replace sostituisce ' (presente ad esempio in "Valle d'Aosta") con \'
         * in modo che non ci siano problemi con gli apici della query su MySQL
         */
        $province = $database->query("SELECT Nome FROM provincia WHERE Regione = '" . str_replace("'", "\\'", $regione) . "';");

        // Elenco delle province appartenenti alla regione attuale
        while ($row1 = $database->fetch($province)) {
            $provincia = $row1['Nome'];

            echo '<option value="' . $provincia . '"';
            if($selected !== null) // Se ci sono province da mostrare come già selezionate
            {
                foreach ($selected as $daSelezionare) {

                    // Controllo se la provincia è da selezionare
                    if($provincia === $daSelezionare) {
                        echo ' selected';
                        break;
                    }
                }
            }
            echo '>' . $provincia . '</option>';
        }
        echo '</optgroup>';
    }

    echo '</select>';

    $database->closeConnection();
}

<?php

    require_once __DIR__ . "/assets/sessione-manager.php";

    // Se la professione da ricercare non è stata inserita
    if(empty($_GET['professione'])) {
        setAlert("Campo 'professione' mancante", "red");
        header('Location: ../index.php');
        exit();
    }

    $professione = $_GET['professione'];
    $province = null;
    $nome = null;
    $cognome = null;

    require_once __DIR__ . "/assets/database-manager.php";
    require_once __DIR__ . "/assets/paginator.php";
    require_once __DIR__ . "/assets/validation.php";
    require_once __DIR__ . "/assets/regioni-province-menu.php";
    global $database;

    // Controlla che la professione inserita esista
    if (!$database->existsProfession($professione)) {
        $database->closeConnection();

        setAlert('La professione \'' . Validation::purify($professione) . '\' non esiste', 'red');
        header('Location: ../index.php');
        exit();
    }


    $validation = new Validation();

    // Se sono state inserite delle province in cui ricercare i professionisti
    if(!empty($_GET['province'])) {
        $province = $_GET['province'];


        // Controllo che sia un array
        $validation->name('province')->value($province)->vectorial();
        if (!$validation->isSuccess()) {
            setAlert($validation->getError(), 'red');
            header('Location: ../index.php');
            exit();
        }


        // Controlla che tutte le province inserite esistano
        foreach ($province as $provincia) {
            $sql = "SELECT EXISTS(SELECT 1 FROM provincia WHERE Nome = ?) AS found;";
            $stmt = $database->prepared_query($sql, array('s' => $provincia));
            $result = $database->getResult($stmt);

            // Provincia inesistente
            if(!$database->fetch($result, 'found')) {
                $database->closeConnection();

                setAlert('La provincia \''  . Validation::purify($provincia) . '\' non esiste', 'red');
                header('Location: ../index.php');
                exit();
            }
        }
    }


    // Se è stato inserito un nome da cercare nell'elenco dei professionisti
    if(!empty($_GET['name'])) {
        $nome = $_GET['name'];

        // Controlla se è stato rispettato il formato del campo
        $validation->name('nome')->value($nome)->customRegex('/^[A-Z][A-Z ]{0,19}$/i');
        if (!$validation->isSuccess()) {
            setAlert($validation->getError(), 'red');
            header('Location: ../index.php');
            exit();
        }
    }

    // Se è stato inserito un cognome di un professionista da ricercare
    if(!empty($_GET['surname'])) {
        $cognome = $_GET['surname'];

        // Controlla se è stato rispettato il formato del campo
        $validation->name('cognome')->value($cognome)->customRegex('/^[A-Z][A-Z ]{0,14}$/i');
        if (!$validation->isSuccess()) {
            setAlert($validation->getError(), 'red');
            header('Location: ../index.php');
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lorenzo Servolini">
    <meta name="keywords" content="ricerca SubitoProfessionista, ricerca professionista">
    <meta name="description" content="Ricerca tra una categoria di professionisti e scegli il tuo preferito">

    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">

    <link rel="stylesheet" type="text/css" href="../css/layout.css">

    <script type="text/javascript" src="../js/contatori-manager.js"></script>
    <script type="text/javascript" src="../js/ricerca.js"></script>

    <title>Su di noi - SubitoProfessionista</title>
</head>
<body onload="addFilterHandlers();">
    <nav>
        <ul>
            <li><a href="../index.php">Home</a></li>
            <?php
            /*
             * Se l'utente ha già effettuato il login:
             * 1) non mostrare il pulsante per registrarsi o accedere
             * 2) mostrare il pulsante per la propria scheda personale e per il logout
             */
            if(isLogged()) {
                echo '<li><a href="scheda-personale.php?email=' . getLoginEmail() . '">Scheda personale</a></li>';
                echo '<li class="right"><a href="logout.php">Logout</a></li>';
            }
            else {
                echo '<li><a href="registra-professionista/registrazione.php">Registrazione - professionista</a></li>';
                echo '<li><a href="registra-utente/registrazione.php">Registrazione - utente</a></li>';
                echo '<li><a href="login/login.php">Login</a></li>';
            }
            ?>
            <li class="right"><a href="manuale.php">About</a></li>
        </ul>
    </nav>

    <div class="page-container">
        <?php
            $select = "p.Email, p.Nome, p.Cognome, p.Prezzi, p.Copertura, AVG(r.Stelle) AS media";
            $from = "professionista p LEFT OUTER JOIN recensione r ON p.Email = r.Target";
            $where = "p.Professione = ?";
            $group = "p.Email";
            $order = "media DESC, p.Cognome ASC, p.Nome ASC";
            $param = array($professione);

            // Se è stato impostato un nome e/o un cognome da ricercare nell'elenco dei professionisti
            if($nome !== null) {
                $where .= " AND p.Nome = ?";
                $param[] = $nome;
            }
            if($cognome !== null) {
                $where .= " AND p.Cognome = ?";
                $param[] = $cognome;
            }

            // Se sono state impostate delle province in cui ricercare i professionisti
            if($province !== null) {

                /*
                 * Mostra solo i professionisti che servono le province inserite così da rendere la ricerca
                 * più utile (verranno comunque mostrati anche i professionisti di altre zone)
                 */
                $length = count($province);

                $where .= " AND (";
                for($i = 0; $i < $length ; $i++) {
                    $where .= "p.Copertura LIKE '%" . str_replace("'", "\\'", $province[$i]) . "%'";

                    // Se è presente un'altra provincia
                    if(isset($province[$i + 1]))
                        $where .= ' OR ';
                }
                $where .= ')';
            }
            else if(isLogged()) // Ordinare in base alle zone (province) di chi sta visualizzando la pagina
            {

                /*
                 * Query per leggere le province (eventualmente) inserite al momento della
                 * registrazione di chi sta visualizzando (utente o professionista).
                 * Una volta ottenute le province sarà possibile mostrare all'inizio i
                 * professionisti che servono quelle stesse zone così da rendere la ricerca
                 * più utile (verranno comunque mostrati anche i professionisti di altre zone)
                 */
                $sql = "SELECT
                                (SELECT Province FROM utente WHERE Email = ?),
                                (SELECT Copertura FROM professionista WHERE Email = ?);";
                $stmt = $database->prepared_query($sql, array('s' => array(getLoginEmail(), getLoginEmail())));
                $result = $database->getResult($stmt);
                $row = $database->fetch($result, '', 'row');

                // Province eventualmente impostate da chi sta visualizzando (utente o professionista)
                $province_utente = $row[0];
                $province_professionista = $row[1];

                // Se chi sta visualizzando ha impostato almeno una provincia
                if($province_utente !== null || $province_professionista !== null) {

                    // Province (eventualmente) inserite da chi sta visualizzando
                    $provinces = ($province_utente !== null ? explode(',', $province_utente) : explode(',', $province_professionista));
                    $length = count($provinces);

                    $mainOrdering = "(";
                    for($i = 0; $i < $length ; $i++) {
                        /*
                         * str_replace sostituisce ' (presente ad esempio in "Valle d'Aosta") con \'
                         * in modo che non ci siano problemi con gli apici della query su MySQL
                         */
                        $mainOrdering .= "Copertura LIKE '%" . str_replace("'", "\\'", $provinces[$i]) . "%'";

                        // Se è presente un'altra provincia
                        if(isset($provinces[$i + 1]))
                            $mainOrdering .= ' OR ';
                    }
                    $mainOrdering .= ') DESC, ';


                    // Aggiungo l'ordinamento primario alla clausola ORDER BY della query
                    $order = $mainOrdering . $order;
                }
            }

            $paging = new MultiPage($select, $from, $where, $group, $order, $param, 8);

            /*
             * Se non ci sono professionisti registrati che svolgono la
             * professione cercata (e che rispettano gli eventuali filtri applicati)
             */
            if($paging->isEmpty()) {
                $database->closeConnection();

                if($province !== null || $nome !== null || $cognome !== null) // Se è stato applicato almeno un campo per il filtraggio
                    setAlert('Nessun ' . $professione . ' trovato che rispetti i criteri impostati', 'green');
                else
                    setAlert('Nessun ' . $professione . ' trovato', 'green');
                header('Location: ../index.php');
                exit();
            }

            echo '<h1>Professione: ' . strtolower($professione) . '</h1>';

            echo '<form action="ricerca.php" method="get" id="filtra" class="pannello">';

            // Campo invisibile utile a mantenere la professione attualmente ricercata ($_GET['professione']) quando verrà inviata la form
            echo '<input type="hidden" name="professione" value="'. $professione .'">';

            echo '<fieldset>';
            echo '<legend><b>Filtra in base alla provincia</b></legend>';

            /*
             * Mostra il menù contenente tutte le province italiane.
             * Se è stato applicato un filtro mostra (nel menù) come già selezionate le province scelte
             */
            buildProvincesMenu(null, '', null, false, 7, $province);
            echo '</fieldset>';


            echo '<fieldset>';
            echo '<legend><b>Filtra in base al nominativo</b></legend>';

            echo '<label for="name"></label>';
            echo '<input class="input-field long" id="name" type="text" placeholder="Nome" name="name" title="Nome di al massimo 20 caratteri" maxlength="20" pattern="[A-Za-z][A-Za-z ]{0,19}" autocomplete="on"';
            if($nome !== null) // Se presente, imposta come valore di default del campo di input il nome applicato come filtro
                echo ' value="' . Validation::purify($nome) . '"';
            echo '>';
            echo '<div class="contatore"></div>';

            echo '<label for="surname"></label>';
            echo '<input class="input-field long" id="surname" type="text" placeholder="Cognome" name="surname" title="Cognome di al massimo 15 caratteri" maxlength="15" pattern="[A-Za-z][A-Za-z ]{0,14}" autocomplete="on"';
            if($cognome !== null) // Se presente, imposta come valore di default del campo di input la data di fine applicata come filtro
                echo ' value="' . Validation::purify($cognome) . '"';
            echo '>';
            echo '<div class="contatore"></div>';

            echo '</fieldset>';

            echo '<div id="button-container">';
            echo '<button type="submit" class="submit" disabled>Filtra</button>';
            echo '<button type="reset" class="cancel">Reset</button>';
            echo '</div></form>';


            echo '<div id="search-result" class="pannello">';

            // Stampa una voce per ogni professionista trovato
            $result = $paging->getCurrentPageItems();
            while ($row = $database->fetch($result)) {
                $email = $row['Email'];
                $nominativo = $row['Nome'] . ' ' . $row['Cognome'];
                $prezzi = ($row['Prezzi'] === null ? '//' : $row['Prezzi']);
                $copertura = str_replace(',', ', ', $row['Copertura']);
                $media = ($row['media'] === null ? '-' : round($row['media'], 1)); // Valutazione media

                echo "<a href=\"scheda-personale.php?email={$email}\">";

                echo '<p><b>' . $nominativo . '</b></p>';
                echo '<hr>';

                echo '<p><b>Prezzi</b></p>';
                echo '<pre>' . $prezzi . '</pre>';
                echo '<p><b>Copertura</b></p>';
                echo '<pre>' . $copertura . '</pre>';
                echo '<p class="valutazione"><b>Valutazione media:</b> ' . $media . '</p>';
                echo '</a>';
            }
            echo '</div>';

            // Mostra la barra di navigazione contenete i pulsanti per spostarsi tra le pagine
            echo ($paging->toHtml("ricerca.php?professione={$professione}"));

            $database->closeConnection();
        ?>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/../html/layouts/footer.html"; ?>
</body>
</html>
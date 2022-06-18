<?php

    require_once __DIR__ . "/assets/sessione-manager.php";

    if (empty($_GET['email'])) {
        setAlert('Email da ricercare mancante', 'red');
        header('Location: ../index.php');
        exit();
    }

    require_once __DIR__ . "/assets/database-manager.php";
    require_once __DIR__ . "/assets/paginator.php";
    require_once __DIR__ . "/assets/validation.php";
    require_once __DIR__ . "/assets/menu-professioni.php";
    global $database;

    $email = $_GET['email'];
    $found_user = $database->isUser($email);
    $found_professionista = $database->isProfessional($email);

    // Controlla che l'email sia registrata (o a un professionista o ad un utente)
    if (!$found_user && !$found_professionista) {
        $database->closeConnection();

        setAlert('L\'email  ' . Validation::purify($email) . ' non corrisponde a nessuno', 'red');
        header('Location: ../index.php');
        exit();
    }

    /*
     * Se si sta visualizzando la scheda personale di un utente oppure gli
     * interventi di un professionista (= parametro GET 'interventi' presente)
     * dobbiamo verificare che sia un professionista a leggere la scheda e che
     * l'utente/professionista abbia scelto di condividere il proprio storico
     */
    if($found_user || isset($_GET['interventi'])) {

        // Solo i professionisti (loggati) possono visualizzare le schede personali degli utenti
        if (!isLogged()) {
            $database->closeConnection();

            setAlert('Solo i professionisti possono accedere all\'elenco degli interventi', 'red');
            header('Location: ../index.php');
            exit();
        }

        // Se non si sta visualizzando la propria scheda personale
        if (getLoginEmail() !== $email) {

            // Se la persona che sta visualizzando la pagina non è un professionista
            if (!$database->isProfessional(getLoginEmail())) {
                $database->closeConnection();

                setAlert('Solo i professionisti possono accedere all\'elenco degli interventi', 'red');
                header('Location: ../index.php');
                exit();
            }

            // Se l'utente/professionista ha scelto di non condividere il proprio storico
            if(!$database->hasSharedInfo($email, 'utente')) {
                $database->closeConnection();

                setAlert($email . ' ha scelto di non condividere il proprio storico degli interventi', 'red');
                header('Location: ../index.php');
                exit();
            }
        }


        /* Validazione dei parametri GET, utilizzati per il filtraggio dei risultati */
        $validation = new Validation();

        // Se presenti, valida il formato delle date inserite
        if (!empty($_GET['from'])) {

            /*
             * Controlla che la data iniziale in input:
             * 1) Sia in formato sia YYYY-mm-dd
             * 2) Non sia precedente alla data minima consentita: 1 gennaio 2010
             * 3) Non superi la data odierna (data massima permessa)
             */
            $validation->name('data iniziale')->value($_GET['from'])->pattern('date')->range(2010, time());

            if(!$validation->isSuccess()) {
                $database->closeConnection();

                setAlert($validation->getError(), 'red');
                header('Location: ../index.php');
                exit();
            }
        }
        if (!empty($_GET['to'])) {

            /*
             * Controlla che la data finale in input:
             * 1) Sia in formato sia YYYY-mm-dd
             * 2) Non sia precedente alla data minima consentita: 1 gennaio 2010
             * 3) Non superi la data odierna (data massima permessa)
             */
            $validation->name('data finale')->value($_GET['to'])->pattern('date')->range(2010, time());

            if(!$validation->isSuccess()) {
                $database->closeConnection();

                setAlert($validation->getError(), 'red');
                header('Location: ../index.php');
                exit();
            }
        }

        // Controlla che la data iniziale non superi quella finale
        if (!empty($_GET['from']) && !empty($_GET['to']) && strtotime($_GET['from']) > strtotime($_GET['to'])) {
            $database->closeConnection();

            setAlert('La data iniziale non può superare la data finale!', 'red');
            header('Location: ../index.php');
            exit();
        }


        // Verifica che la professione inserita (se presente) esista
        if (!empty($_GET['professione']) && !$database->existsProfession($_GET['professione'])) {
            $database->closeConnection();

            setAlert('La professione ' . Validation::purify($_GET['professione']) . ' non esiste', 'red');
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
    <meta name="keywords" content="interventi utente, scheda utente, scheda professionista, valutazioni professionista">
    <meta name="description" content="Scheda personale di un utente o di un professionista">

    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">

    <!-- Libreria "Font Awesome" per le icone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/layout.css">

    <?php
        // JavaScript è necessario solo quando si visualizza l'elenco degli interventi
        if($found_user || isset($_GET['interventi'])) {
            echo '<script type="text/javascript" src="../js/date-manager.js"></script>';
            echo '<script type="text/javascript" src="../js/scheda-personale.js"></script>';
        }
    ?>

    <title>Scheda personale - SubitoProfessionista</title>
</head>
<body<?php if($found_user || isset($_GET['interventi'])) echo ' onload="addFilterHandlers();"'; ?>>
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
                    // Se sta visualizzando la propria scheda personale, evidenzia "Scheda personale" nella navbar (classe active)
                    echo '<li><a' . (getLoginEmail() === $email ? ' class="active"' : '');
                    echo ' href="scheda-personale.php?email=' . getLoginEmail() . '">Scheda personale</a></li>';
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
            /*
             * Se l'email inserita appartiene ad un utente oppure se si vogliono visualizzare
             * gli interventi svolti ad un professionista ($_GET['interventi'] presente) mostro
             * il pannello degli interventi, altrimenti la scheda personale di un professionista
             */
            if($found_user || isset($_GET['interventi']))
                buildInterventionsPanel();
            else
                buildProfessionalCard();

            $database->closeConnection();
        ?>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/../html/layouts/footer.html"; ?>
</body>
</html>

<?php

/**
 * Costruisce la pagina che elenca gli interventi eseguiti su
 * un utente o un professionista
 */
function buildInterventionsPanel() {
    global $database, $email;

    /*
     * Costruisce la query in base ai parametri GET passati,
     * utili per filtrare i risultati o per cambiare pagina
     *
     * $param => contiene un array di parametri da passare a bind_param()
     * $where => contiene le condizioni aggiuntive della clausola where necessarie per il filtraggio
     * $query => contiene la query da eseguire sul database
     */
    $param = array($email, $email);
    $where = '';
    if(!empty($_GET['professione'])) {
        $where.= " AND p.Professione = ?";
        $param[] = $_GET['professione'];
    }
    if(!empty($_GET['from'])) {
        $where .= " AND i.Data >= ?";
        $param[] = $_GET['from'];
    }
    if(!empty($_GET['to'])) {
        $where .= " AND i.Data <= ?";
        $param[] = $_GET['to'];
    }

    $select = "p.Nome, p.Cognome, p.Professione, i.Data, i.Descrizione";
    $from = "intervento i INNER JOIN professionista p ON i.Professionista = p.Email";
    $where = "(i.TargetUtente = ? OR i.TargetProfessionista = ?) {$where}";
    $order = "i.Data DESC";

    $paging = new MultiPage($select, $from, $where, null, $order, $param, 5);

    // Se non ci sono interventi che rispettano i filtri (se applicati)
    if($paging->isEmpty()) {
        $msg = ''; // Messaggio da mostrare all'utente

        // Se è stato eseguito un filtraggio
        if(!empty($_GET['professione']) || !empty($_GET['from']) || !empty($_GET['to']))
        {
            if(isLogged() && getLoginEmail() === $email)
                $msg = 'Non ci sono interventi a tuo carico che rispettano i filtri applicati';
            else
                $msg = 'Non ci sono interventi a carico di ' . $email . ' che rispettano i filtri applicati';
        }
        else // Se non è stato applicato nessun filtro ai risultati
        {
            if(isLogged() && getLoginEmail() === $email)
                $msg = 'Nessun intervento registrato a tuo carico';
            else
                $msg = 'Nessun intervento registrato a carico di ' . $email;
        }

        $database->closeConnection();

        setAlert($msg, 'red');
        header('Location: ../index.php');
        exit();
    }


    echo '<h1>Scheda di ' . $email . '</h1>';
    echo '<form action="scheda-personale.php" method="get" id="filtra" class="pannello">';

    // Campo invisibile utile a mantenere l'email attuale $_GET['email'] quando verrà inviata la form
    echo '<input type="hidden" name="email" value="'. $email .'">';

    if(isset($_GET['interventi'])) {
        /*
         * Campo invisibile utile, nel caso della scheda di un professionista,
         * a distinguere se mostrare la scheda personale o l'elenco degli interventi.
         * L'elenco degli interventi a lui eseguiti vanno mostrati solo se il
         * parametro GET 'interventi' è presente
         */
        echo '<input type="hidden" name="interventi" value="">';
    }

    echo '<fieldset>';
    echo '<legend><b>Filtra in base alla professione</b></legend>';

    echo '<label for="professione"><b>Tipo: </b></label>';
    echo '<select class="input-field" id="professione" name="professione">';

    /*
     * Nel menù a tendina mostro come placeholder:
     * - "Professione" se non c'è nessun filtro sulla professione
     * - la professione che si sta filtrando se è applicato un filtro sulla professione
     */
    if(!empty($_GET['professione']))
        buildMenu($_GET['professione']);
    else
        buildMenu();

    echo '</select></fieldset>';

    echo '<fieldset>';
    echo '<legend><b>Filtra in base alla data</b></legend>';

    // La data massima inseribile (attributo max) è la data odierna e viene inserita con JS (scheda-personale.js)
    echo '<label for="from">Da: </label>';
    echo '<input class="input-field" id="from" type="date" name="from" title="Data minima: 01/01/2010&#10;Data massima: " min="2010-01-01"';
    if(!empty($_GET['from'])) // Se presente, imposta come valore di default del campo di input la data di inizio applicata come filtro
        echo ' value="' . date('Y-m-d', strtotime($_GET['from'])) . '"';
    echo '>';

    echo '<label for="to"> fino a: </label>';
    echo '<input class="input-field" id="to" type="date" name="to" title="Data minima: 01/01/2010&#10;Data massima: " min="2010-01-01"';
    if(!empty($_GET['to'])) // Se presente, imposta come valore di default del campo di input la data di fine applicata come filtro
        echo ' value="' . date('Y-m-d', strtotime($_GET['to'])) . '"';
    echo '>';
    echo '<div id="message"></div>';

    echo '</fieldset>';

    echo '<div id="button-container">';
    echo '<button type="submit" class="submit" disabled>Filtra</button>';
    echo '<button type="reset" class="cancel">Reset</button>';
    echo '</div></form>';


    echo '<div id="scheda-utente" class="pannello">';

    // Stampa una voce per ogni intervento trovato (appartenente alla pagina corrente)
    $result = $paging->getCurrentPageItems();
    while ($row = $database->fetch($result)) {
        echo '<div><div class="riga">';

        echo '<p class="colonna"><b>' . $row['Professione'] . ': </b>' . $row['Nome'] . ' ' . $row['Cognome'] . '</p>';
        echo '<p class="colonna"><b>Intervento del: </b>' . $row['Data'] . '</p>';
        echo '</div><hr><hr>';

        echo '<p><b>Descrizione</b></p>';
        echo '<pre>' . $row['Descrizione'] . '</pre>';
        echo '</div>';
    }
    echo '</div>';


    $href = 'scheda-personale.php?email=' . $email;
    if(isset($_GET['interventi']))
        $href .= '&interventi';
    if(!empty($_GET['professione']))
        $href .= '&professione=' . $_GET['professione'];
    if(!empty($_GET['from']))
        $href .= '&from=' . $_GET['from'];
    if(!empty($_GET['to']))
        $href .= '&to=' . $_GET['to'];

    // Mostra la barra di navigazione contenete i pulsanti per spostarsi tra le pagine
    echo ($paging->toHtml($href));
}

/**
 * Costruisce la scheda personale di un professionista
 */
function buildProfessionalCard() {
    global $database, $email;

    $info = 'Genere, Nome, Cognome, Professione, Prezzi, Copertura, Descrizione, Contatti, Orari';
    $row = $database->getProfessionalInfo($email, $info);

    $nominativo = $row['Nome'] . ' ' . $row['Cognome'];
    $genere = $row['Genere'] === 'M' ? 'Maschio' : ($row['Genere'] === 'F' ? 'Femmina' : 'Non specificato');
    $professione = $row['Professione'];
    $prezzi = ($row['Prezzi'] === null ? '//' : $row['Prezzi']);
    $copertura = str_replace(',', ', ', $row['Copertura']);
    $descrizione = ($row['Descrizione'] === null ? '//' : $row['Descrizione']);
    $contatti = ($row['Contatti'] === null ? '//' : $row['Contatti']);
    $orari = ($row['Orari'] === null ? '//' : $row['Orari']);


    echo '<h1>Scheda di ' . $nominativo . '</h1>';

    echo '<div id="scheda-professionista" class="pannello">';

    echo '<div>';
    echo '<div class="riga">';
    echo '<p class="colonna"><b>Genere: </b>' . $genere . '</p>';
    echo '<p class="colonna"><b>Professione: </b>' . $professione . '</p>';
    echo '</div>';

    echo '<hr>';

    echo '<p><b>Prezzi</b></p>';
    echo '<pre>' . $prezzi . '</pre>';

    echo '<hr>';

    echo '<p><b>Copertura</b></p>';
    echo '<pre>' . $copertura . '</pre>';

    echo '<hr>';

    echo '<p><b>Descrizione</b></p>';
    echo '<pre>' . $descrizione . '</pre>';

    echo '<hr>';

    echo '<p><b>Contatti</b></p>';
    echo '<pre>' . $contatti . '</pre>';

    echo '<hr>';

    echo '<p><b>Orari</b></p>';
    echo '<pre>' . $orari . '</pre>';

    echo '<hr>';

    echo '<p><b>Recensioni</b></p>';
    echo '<div id="recensioni">';
    echo '<div id="votazione"><p>Valutazione</p>';

    $stmt = $database->prepared_query("SELECT AVG(Stelle) AS media FROM recensione WHERE Target = ?", array('s' => $email));
    $result = $database->getResult($stmt);
    $media = round($database->fetch($result, 'media'), 1); // Valutazione media

    $stmt = $database->prepared_query("SELECT COUNT(*) AS totale FROM recensione WHERE Target = ?", array('s' => $email));
    $result = $database->getResult($stmt);
    $numRecensioni = $database->fetch($result, 'totale'); // Totale recensioni

    // Stampa le stelle
    $floor_media = floor($media);
    for ($i = 1; $i <= 5; $i++) {
        if($i <= $floor_media) // Colora le stelle in base alla media arrotondata per difetto
            echo '<span class="fa fa-star checked"></span>';
        else
            echo '<span class="fa fa-star"></span>';
    }

    if($numRecensioni === 0) // Se non ci sono ancora recensioni a carico di questo professionista
        echo "<p>Nessuna recensione presente</p></div>";
    else {
        if($numRecensioni === 1) // Singolare "recensione"
            echo "<p>Voto medio basato su {$numRecensioni} recensione: {$media}</p></div>";
        else // Plurale "recensioni"
            echo "<p>Voto medio basato su {$numRecensioni} recensioni: {$media}</p></div>";
    }

    $existReviews = $numRecensioni !== 0; // Se ci sono recensioni registrate al professionista
    $numInterventi = 0;

    if(isLogged()) {

        /*
         * Conta il numero di interventi registrati a carico dell'utente da parte
         * del professionista che si vuole recensione successivi all'ultima
         * recensione lasciata dall'utente (se presente)
         */
        $sql = "SELECT COUNT(*) AS totale
                FROM intervento i
                WHERE (i.TargetUtente = ? OR i.TargetProfessionista = ?)
                  AND i.Professionista = ?
                  AND i.Data > (
                
                    # Trova (se presente) la data dell'ultima recensione lasciata da chi sta visualizzando la scheda personale (utente o professionista)
                    SELECT IFNULL(MAX(r.Data), 0) AS last_date
                    FROM recensione r
                    WHERE r.Target = ? AND (r.EmailProfessionista = ? OR r.EmailUtente = ?)
                )";
        $stmt = $database->prepared_query($sql, array('s' => array(getLoginEmail(), getLoginEmail(), $email, $email, getLoginEmail(), getLoginEmail())));
        $result = $database->getResult($stmt);

        $numInterventi = $database->fetch($result, 'totale');
    }

    /*
     * Un utente/professionista che sta visualizzando la scheda personale può lasciare una recensione se:
     * 1) ha eseguito il login
     * 2) se non è un professionista che sta navigando sulla propria scheda personale
     * 3) ha ricevuto un intervento dal professionista dopo l'ultima recensione che ha lasciato (se presente)
     */
    $canReview = isLogged() && getLoginEmail() !== $email && $numInterventi > 0;
    $existButtons = $existReviews || $canReview;

    if($existButtons)
        echo '<div>'; // Se c'è almeno un bottone da mostrare inserisco un contenitore

    if($existReviews) // Se c'è almeno una recensione inserita per il professionista
    {
        // Pulsante per vedere tutte le recensioni nel dettaglio
        echo '<div id="lista-recensioni" title="Lista di tutte le recensioni a nome del professionista">';
        echo '<a href="lista-recensioni.php?email=' . $email . '">Mostra tutte le recensioni</a>';
        echo '</div>';
    }

    // Se può lasciare una recensione
    if($canReview) {
        // Mostro il pulsante per lasciare una recensione
        echo '<div id="nuova-recensione" title="Registra una nuova recensione">';
        echo '<a href="registra-recensione/registrazione.php?email=' . $email . '">Inserisci nuova recensione</a>';
        echo '</div>';
    }

    if($existButtons)
        echo '</div>'; // Se c'è almeno un bottone da mostrare chiudo il contenitore

    echo '</div></div></div>';


    // Se un professionista sta visualizzando la propria pagina personale
    if(isLogged() && getLoginEmail() === $email) {
        echo '<div id="contenitore-bottoni">';

        echo '<form action="scheda-personale.php" method="get" id="cerca-utente" class="pannello">';
        echo '<label for="email"><b>Cerca gli interventi di un utente: </b></label>';
        echo '<input class="input-field" id="email" type="email" placeholder="Email utente" name="email" title="example@abc.com" maxlength="50" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}" autocomplete="on" tabindex="1" required>';
        echo '<input type="hidden" name="interventi" value="">';
        echo '<button type="submit" class="submit" tabindex="2">Cerca</button>';
        echo '</form>';

        echo '<div id="nuovo-intervento" title="Registra un nuovo intervento ad un utente">';
        echo '<a href="registra-intervento/registrazione.php" tabindex="3">Registra nuovo intervento</a>';
        echo '</div>';

        echo '</div>';
    }
}
?>
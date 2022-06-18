<?php
    require_once __DIR__ . "/../assets/sessione-manager.php";

    // Se l'utente non ha eseguito l'accesso riportarlo alla home
    if(!isLogged()) {
        setAlert('Solo i professionisti possono registrare dei nuovi interventi', 'red');
        header('Location: ../../index.php');
        exit();
    }

    require_once __DIR__ . "/../assets/database-manager.php";
    global $database;

    // Se l'utente che sta visualizzando la pagina non è un professionista
    if (!$database->isProfessional(getLoginEmail())) {
        $database->closeConnection();

        setAlert('Solo i professionisti possono registrare dei nuovi interventi', 'red');
        header('Location: ../../index.php');
        exit();
    }

    $database->closeConnection();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lorenzo Servolini">
    <meta name="keywords" content="interventi, nuovo intervento SubitoProfessionista, registrazione manutenzione">
    <meta name="description" content="Pagina per la registrazione di nuovi interventi">

    <link rel="shortcut icon" type="image/x-icon" href="../../img/favicon.ico">

    <link rel="stylesheet" type="text/css" href="../../css/layout.css">

    <script type="text/javascript" src="../../js/contatori-manager.js"></script>
    <script type="text/javascript" src="../../js/date-manager.js"></script>
    <script type="text/javascript" src="../../js/registra-intervento.js"></script>

    <title>Registra intervento - SubitoProfessionista</title>
</head>
<body onload="addInputHandlers();">
    <nav>
        <ul>
            <li><a href="../../index.php">Home</a></li>
            <?php
                echo '<li><a href="../scheda-personale.php?email=' . getLoginEmail() . '">Scheda personale</a></li>';
            ?>
            <li class="right"><a href="../logout.php">Logout</a></li>
            <li class="right"><a href="../manuale.php">About</a></li>
        </ul>
    </nav>

    <div class="page-container">
        <form action="registrazione-handler.php" method="post" id="registra-intervento" class="pannello">
            <h1>Registrazione nuovo intervento</h1>
            <hr>

            <div class="main-panel">
                <?php showAlert(); ?>

                <div class="riga">
                    <div class="colonna">
                        <label for="email"><b>Email dell'utente</b></label>
                        <input class="input-field" id="email" type="email" placeholder="Email utente" name="email" title="example@abc.com" maxlength="50" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}" autocomplete="on" tabindex="1" required>
                    </div>

                    <div class="colonna">
                        <label for="data"><b>Data di intervento</b></label>

                        <!--
                            La data massima inseribile (attributo max), il valore di default (attributo value)
                            e la parte finale dell'attributo title corrispondono alla data odierna che
                            viene inserita usando JS (registra-intervento.js)
                        -->
                        <input class="input-field" id="data" type="date" name="data" title="Data in cui è stato eseguito l'intervento&#10;Data minima: 01/01/2010&#10;Data massima: " min="2010-01-01" tabindex="2" required>
                    </div>
                </div>

                <label for="descrizione"><b>Descrizione dell'intervento</b></label>
                <textarea class="input-field" id="descrizione" placeholder="Descrizione" name="descrizione" title="Descrizione dell'intervento&#10;&#10;Attenzione! I caratteri 'speciali' occupano più spazio:&#10;tieni sotto controllo il contatore!" maxlength="400" cols="50" rows="10" autocomplete="off" tabindex="3" required></textarea>
                <div class="contatore"></div>

                <button type="submit" class="submit" tabindex="4">Registra</button>
                <button type="reset" class="cancel" tabindex="5">Reset</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/../../html/layouts/footer.html"; ?>
</body>
</html>
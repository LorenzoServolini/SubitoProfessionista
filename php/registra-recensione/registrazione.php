<?php

    require_once __DIR__ . "/../assets/sessione-manager.php";
    require_once __DIR__ . "/../assets/validation.php";

    // Se l'utente non ha eseguito l'accesso riportarlo alla home
    if(!isLogged()) {
        setAlert('Solo gli utenti registrati possono inserire nuove recensioni', 'red');
        header('Location: ../../index.php');
        exit();
    }

    if(empty($_GET['email'])) {
        setAlert("Campo 'email' mancante", 'red');
        header('Location: ../../index.php');
        exit();
    }


    require_once __DIR__ . "/../assets/database-manager.php";
    global $database;

    $email = $_GET['email'];
    $row = $database->getProfessionalInfo($email, 'Nome, Cognome');

    // Controlla se l'email inserita esiste ed è di un professionista (solo loro possono essere recensiti)
    if ($row === null) {
        $database->closeConnection();

        setAlert('L\'email ' . Validation::purify($email) . ' non corrisponde a nessun professionista', 'red');
        header('Location: ../../index.php');
        exit();
    }

    $database->closeConnection();


    // Controlla che non si stia inserendo una recensione per se stessi (non permesso)
    if (getLoginEmail() === $email) {
        setAlert('Non è permesso inserire una recensione per se stessi', 'red');
        header('Location: ../../index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lorenzo Servolini">
    <meta name="keywords" content="recensioni, nuova recensione SubitoProfessionista, registrazione valutazione">
    <meta name="description" content="Pagina per l'inserimento di recensioni relative ai professionisti">

    <link rel="shortcut icon" type="image/x-icon" href="../../img/favicon.ico">

    <link rel="stylesheet" type="text/css" href="../../css/layout.css">

    <script type="text/javascript" src="../../js/contatori-manager.js"></script>
    <script type="text/javascript" src="../../js/registra-recensione.js"></script>

    <title>Nuova recensione - SubitoProfessionista</title>
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
        <form action="registrazione-handler.php" method="post" id="registra-recensione" class="pannello">
            <h1>Recensione per <?php echo "{$row['Nome']} {$row['Cognome']}"; ?></h1>
            <hr>

            <div class="main-panel">
                <?php showAlert(); ?>

                <?php
                    // Campo invisibile utile a mantenere l'email del professionista (in $_GET['email']) quando verrà inviata la form
                    echo '<input type="hidden" name="email" value="'. $email .'">';
                ?>

                <label for="voto"><b>Valutazione</b></label>
                <select class="input-field" id="voto" name="voto" tabindex="1" required>
                    <option value="" disabled selected hidden>Votazione da 1 a 5</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>

                <label for="feedback"><b>Commento</b></label>
                <textarea class="input-field" id="feedback" placeholder="Scrivi il tuo feedback!" name="feedback" title="Descrivi maggiormente la tua recensione" maxlength="300" cols="50" rows="10" autocomplete="off" tabindex="2" required></textarea>
                <div class="contatore"></div>

                <button type="submit" class="submit" tabindex="3">Registra</button>
                <button type="reset" class="cancel" tabindex="4">Reset</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/../../html/layouts/footer.html"; ?>
</body>
</html>
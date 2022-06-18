<?php

    require_once __DIR__ . "/assets/sessione-manager.php";
    require_once __DIR__ . "/assets/validation.php";

    // Se l'email del professionista di cui leggere le recensioni non è stata inserita
    if(empty($_GET['email'])) {
        setAlert("Campo 'email' mancante", "red");
        header('Location: ../index.php');
        exit();
    }

    require_once __DIR__ . "/assets/database-manager.php";
    require_once __DIR__ . "/assets/paginator.php";
    global $database;

    $email = $_GET['email'];
    $row = $database->getProfessionalInfo($email, 'Nome, Cognome');

    // Si assicura che l'email inserita sia di un professionista
    if ($row === null) {
        $database->closeConnection();

        setAlert('L\'email ' . Validation::purify($email) . ' non corrisponde a nessun professionista', 'red');
        header('Location: ../index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lorenzo Servolini">
    <meta name="keywords" content="lista recensioni SubitoProfessionista, recensioni professionista">
    <meta name="description" content="Leggi in dettaglio tutte le recensioni di un professionista">

    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">

    <link rel="stylesheet" type="text/css" href="../css/layout.css">

    <title>Recensioni - SubitoProfessionista</title>
</head>
<body>
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
        $select = "r.EmailUtente, r.EmailProfessionista, r.Target, r.Stelle, r.Commento, r.Data, p.Nome, p.Cognome";
        $from = "recensione r INNER JOIN professionista p ON p.Email = r.Target";
        $where = "r.Target = ?";
        $order = "r.Data DESC, r.Stelle DESC"; // Per le recensioni inserite nella stessa data, ordina per voto decrescente
        $param = array($email);

        $paging = new MultiPage($select, $from, $where, null, $order, $param, 8);

        // Se non ci sono recensioni registrate a nome del professionista specificato
        if($paging->isEmpty()) {
            $database->closeConnection();

            setAlert("Nessuna recensione trovata a nome di {$row['Nome']} {$row['Cognome']}", 'red');
            header('Location: ../index.php');
            exit();
        }

        echo "<h1>Recensioni di {$row['Nome']} {$row['Cognome']}</h1>";
        echo '<div id="elenco-recensioni" class="pannello">';

        // Stampa una voce per ogni recensione trovata
        $result = $paging->getCurrentPageItems(); // Elementi appartenenti alla pagina corrente
        while ($row = $database->fetch($result)) {
            echo '<div class="recensione">';

            echo '<div class="sommario">';
            echo "<p><b>Autore:</b> " . ($row['EmailUtente'] !== NULL ? $row['EmailUtente'] : $row['EmailProfessionista']) . "</p>";

            echo "<p><b>Data:</b> {$row['Data']}</p>";
            echo "<p><b>Voto:</b> {$row['Stelle']}</p>";
            echo '</div>';

            echo '<hr>';

            echo '<p><b>Commento</b></p>';
            echo '<pre>' . $row['Commento'] . '</pre>';

            echo '</div>';
        }
        echo '</div>';

        // Mostra la barra di navigazione contenete i pulsanti per spostarsi tra le pagine
        echo ($paging->toHtml("lista-recensioni.php?email={$email}"));

        $database->closeConnection();
    ?>
</div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/../html/layouts/footer.html"; ?>
</body>
</html>
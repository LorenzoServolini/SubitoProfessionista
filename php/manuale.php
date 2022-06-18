<?php require_once __DIR__ . "/assets/sessione-manager.php"; ?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lorenzo Servolini">
    <meta name="keywords" content="manuale utente, info SubitoProfessionista, about SubitoProfessionista, funzionamento">
    <meta name="description" content="Manuale utente di SubitoProfessionista">

    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">

    <link rel="stylesheet" type="text/css" href="../css/layout.css">

    <title>Manuale utente - SubitoProfessionista</title>
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
            <li class="right"><a class="active" href="manuale.php">About</a></li>
        </ul>
    </nav>

    <div class="page-container">
         <h1>Manuale utente</h1>

        <p class="manuale">

            <!-- Testo del manuale -->
            <?php require_once __DIR__ . '/../manuale.html'; ?>
        </p>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/../html/layouts/footer.html"; ?>
</body>
</html>
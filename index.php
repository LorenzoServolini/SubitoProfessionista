<?php
    require_once __DIR__ . "/php/assets/sessione-manager.php";
    require_once __DIR__ . "/php/assets/menu-professioni.php";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lorenzo Servolini">
    <meta name="keywords" content="professionisti, ricerca professionista, professionista per te, professionisti online">
    <meta name="description" content="Motore di ricerca per professionisti: trova avvocati, geometri, psicologi, commercialisti e tanti altre figure professionali nella tua città!">

    <link rel="shortcut icon" type="image/x-icon" href="./img/favicon.ico">

    <link rel="stylesheet" type="text/css" href="./css/layout.css">

    <title>SubitoProfessionista</title>
</head>
<body>
    <nav>
        <ul>
            <li><a class="active" href="index.php">Home</a></li>
            <?php
                /*
                 * Se l'utente ha già effettuato il login:
                 * 1) non mostrare il pulsante per registrarsi o accedere
                 * 2) mostrare il pulsante per la propria scheda personale e per il logout
                 */
                if(isLogged()) {
                    echo '<li><a href="php/scheda-personale.php?email=' . getLoginEmail() . '">Scheda personale</a></li>';
                    echo '<li class="right"><a href="php/logout.php">Logout</a></li>';
                }
                else {
                    echo '<li><a href="php/registra-professionista/registrazione.php">Registrazione - professionista</a></li>';
                    echo '<li><a href="php/registra-utente/registrazione.php">Registrazione - utente</a></li>';
                    echo '<li><a href="php/login/login.php">Login</a></li>';
                }
            ?>
            <li class="right"><a href="php/manuale.php">About</a></li>
        </ul>
    </nav>

    <div class="page-container">
        <div id="main-page">
            <?php showAlert(); ?>

            <h1>Trova il professionista adatto a te!</h1>

            <form action="php/ricerca.php" method="get" id="ricerca">
                <label for="professione">
                    <select name="professione" id="professione" tabindex="1" required>
                        <?php buildMenu(); ?>
                    </select>
                </label>
                <button type="submit" tabindex="2">Cerca<span></span></button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/html/layouts/footer.html"; ?>
</body>
</html>
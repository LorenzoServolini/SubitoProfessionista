<?php
    require_once __DIR__ . "/../assets/sessione-manager.php";
    require_once __DIR__ . "/../assets/regioni-province-menu.php";

    // Se l'utente ha già eseguito l'accesso riportarlo alla home
    if(isLogged()) {
        header('Location: ../../index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lorenzo Servolini">
    <meta name="keywords" content="registrazione utenti SubitoProfessionista">
    <meta name="description" content="Pagina di registrazione per utenti su SubitoProfessionista">

    <link rel="shortcut icon" type="image/x-icon" href="../../img/favicon.ico">

    <!-- Libreria "Font Awesome" per le icone -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../../css/layout.css">

    <script type="text/javascript" src="../../js/contatori-manager.js"></script>
    <script type="text/javascript" src="../../js/iscrizione.js"></script>

    <title>Registrazione - SubitoProfessionista</title>
</head>

<body onload="addSignupHandlers();">
    <nav>
        <ul>
            <li><a href="../../index.php">Home</a></li>
            <li><a href="../registra-professionista/registrazione.php">Registrazione - professionista</a></li>
            <li><a class="active" href="registrazione.php">Registrazione - utente</a></li>
            <li><a href="../login/login.php">Login</a></li>
            <li class="right"><a href="../manuale.php">About</a></li>
        </ul>
    </nav>

    <div class="page-container">
        <form action="registrazione-handler.php" method="post" id="registra" class="pannello">
            <h1>Registrazione utente</h1>
            <hr>

            <div class="main-panel">
                <?php showAlert(); ?>

                <p class="info">I campi <mark>sottolineati</mark> sono obbligatori</p>

                <div class="riga">
                    <div class="colonna">
                        <label for="email"><b><mark>Email</mark></b></label>
                        <input class="input-field" id="email" type="email" placeholder="Email" name="email" title="example@abc.com" maxlength="50" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" autocomplete="on" tabindex="1" required>
                        <div class="contatore"></div>
                    </div>

                    <div class="colonna">
                        <!-- Menù contenente tutte le province italiane -->
                        <?php buildProvincesMenu(2, 'Abitazione', 'Seleziona le province in cui ti trovi/abiti in modo che la ricerca dei professionisti avvenga in base alla tua zona'); ?>
                    </div>
                </div>

                <div class="riga">
                    <div class="colonna">
                        <label for="psw"><b><mark>Password</mark></b></label>
                        <input class="input-field" id="psw" type="password" placeholder="Password" name="psw" title="Password di almeno 5 caratteri e massimo 72&#10;&#10;Attenzione! I caratteri 'speciali' occupano più spazio:&#10;tieni sotto controllo il contatore!" minlength="5" maxlength="72" autocomplete="off" tabindex="3" required>
                        <div class="contatore"></div>
                    </div>

                    <div class="colonna">
                        <label for="psw-repeat"><b><mark>Conferma password</mark></b></label>
                        <input class="input-field" id="psw-repeat" type="password" placeholder="Conferma password" name="psw-repeat" title="Ripeti la password inserita" maxlength="72" autocomplete="off" tabindex="4" required>
                        <div id="message"></div> <!-- Spazio per il testo (gestito da JS) che conferma o meno il match delle password -->
                    </div>
                </div>

                <label class="checkbox" title="Selezionando la casella acconsenti a condividere lo storico degli interventi registrati dai professionisti">
                    <input type="checkbox" checked="checked" name="share" title="Selezionando la casella acconsenti a condividere lo storico degli interventi registrati dai professionisti" tabindex="5"> Accetto di condividere lo storico degli interventi

                    <!-- Simbolo di informazioni: al passaggio del mouse (hover) viene visualizzato il title con le informazioni extra -->
                    <span class="fa fa-info-circle" title="I professionisti possono registrare gli interventi eseguiti ad un utente.&#10;Se selezioni la casella, acconsenti a condividere con tutti e soli i professionisti le informazioni presenti negli interventi registrati.&#10;In altre parole, i professionisti avranno modo di consultare tutto l'elenco degli interventi a te registrati.&#10;&#10;Maggiori informazioni nella pagina 'About'."></span>
                </label>

                <button type="submit" class="submit" tabindex="6">Iscriviti</button>
                <button type="reset" class="cancel" tabindex="7">Reset</button>
            </div>

            <div class="sub-panel">
                <a class="underlined" href="../login/login.php" tabindex="8">Hai già un account? Esegui il login</a>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/../../html/layouts/footer.html"; ?>
</body>
</html>
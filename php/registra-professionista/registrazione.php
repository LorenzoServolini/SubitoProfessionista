<?php
    require_once __DIR__ . "/../assets/sessione-manager.php";
    require_once __DIR__ . "/../assets/regioni-province-menu.php";

    // Se l'utente ha già eseguito l'accesso riportarlo alla home
    if(isLogged()) {
        header('Location: ../../index.php');
        exit();
    }

    require_once __DIR__ . "/../assets/menu-professioni.php";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Lorenzo Servolini">
    <meta name="keywords" content="registrazione professionista SubitoProfessionista">
    <meta name="description" content="Pagina di registrazione per professionisti su SubitoProfessionista">

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
            <li><a class="active" href="registrazione.php">Registrazione - professionista</a></li>
            <li><a href="../registra-utente/registrazione.php">Registrazione - utente</a></li>
            <li><a href="../login/login.php">Login</a></li>
            <li class="right"><a href="../manuale.php">About</a></li>
        </ul>
    </nav>

    <div class="page-container">
        <form action="registrazione-handler.php" method="post" id="registra" class="pannello">
            <h1>Registrazione professionista</h1>
            <hr>

            <div class="main-panel">
                <?php showAlert(); ?>

                <p class="info">I campi <mark>sottolineati</mark> sono obbligatori</p>

                <div class="riga">
                    <div class="colonna">
                        <label for="nome" class="required"><b><mark>Nome</mark></b></label>
                        <input class="input-field" id="nome" type="text" placeholder="Nome" name="nome" title="Nome di al massimo 20 caratteri" maxlength="20" pattern="[A-Za-z][A-Za-z ]{0,19}" autocomplete="off" tabindex="1" required>
                        <div class="contatore"></div>
                    </div>

                    <div class="colonna">
                        <label for="cognome" class="required"><b><mark>Cognome</mark></b></label>
                        <input class="input-field" id="cognome" type="text" placeholder="Cognome" name="cognome" title="Cognome di al massimo 15 caratteri" maxlength="15" pattern="[A-Za-z][A-Za-z ]{0,14}" autocomplete="off" tabindex="2" required>
                        <div class="contatore"></div>
                    </div>
                </div>

                <div class="riga">
                    <div class="colonna">
                        <label for="professione"><b><mark>Professione</mark></b></label>
                        <select class="input-field" id="professione" name="professione" tabindex="3" required>
                            <?php buildMenu(); ?>
                        </select>
                    </div>

                    <div class="colonna">
                        <label for="email"><b><mark>Email</mark></b></label>
                        <input class="input-field" id="email" type="email" placeholder="Email" name="email" title="example@abc.com" maxlength="50" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}" autocomplete="on" tabindex="4" required>
                        <div class="contatore"></div>
                    </div>
                </div>


                <div class="riga">
                    <div class="colonna">
                        <label for="prezzi"><b>Prezzi</b></label>
                        <textarea class="input-field" id="prezzi" placeholder="Fasce di prezzo" name="prezzi" title="Esempio:&#10;€50-100 revisione caldaia&#10;€200 nuovo impianto&#10;&#10;Attenzione! I caratteri 'speciali' occupano più spazio:&#10;tieni sotto controllo il contatore!" maxlength="130" autocomplete="off"  tabindex="5"></textarea>
                        <div class="contatore"></div>
                    </div>

                    <div class="colonna">
                        <label for="descrizione"><b>Descrizione</b></label>
                        <textarea class="input-field" id="descrizione" placeholder="Descrizione" name="descrizione" title="Presentati, descriviti, parla delle tue esperienze...&#10;&#10;Attenzione! I caratteri 'speciali' occupano più spazio:&#10;tieni sotto controllo il contatore!" maxlength="200" autocomplete="off" tabindex="6"></textarea>
                        <div class="contatore"></div>
                    </div>
                </div>

                <div class="riga">
                    <div class="colonna">
                        <label for="contatti"><b>Contatti</b></label>
                        <textarea class="input-field" id="contatti" placeholder="Contatti" name="contatti" title="Come possono contattarti? Via telefono, via social...&#10;&#10;Attenzione! I caratteri 'speciali' occupano più spazio:&#10;tieni sotto controllo il contatore!" maxlength="100" autocomplete="off" tabindex="7"></textarea>
                        <div class="contatore"></div>
                    </div>

                    <div class="colonna">
                        <label for="orari"><b>Orari lavorativi</b></label>
                        <textarea class="input-field" id="orari" placeholder="Orari di lavoro" name="orari" title="Esempio:&#10;Lunedì 08.00-13.00 15.00-18.00 &#10;Martedì 09.00-14.00 16.00-19.00&#10;&#10;Attenzione! I caratteri 'speciali' occupano più spazio:&#10;tieni sotto controllo il contatore!" maxlength="300" autocomplete="off" tabindex="8"></textarea>
                        <div class="contatore"></div>
                    </div>
                </div>

                <div class="riga">
                    <div class="colonna">
                        <!-- Menù contenente tutte le province italiane -->
                        <?php buildProvincesMenu(9, 'Zone servite', 'Province servite&#10;&#10;Esempi:&#10;Provincia di Pisa&#10;Livorno, Pisa e Firenze', true); ?>
                    </div>

                    <div class="colonna">
                        <b>Genere</b>
                        <fieldset>
                            <input id="maschio" type="radio" name="genere" title="Sesso maschile" value="M" tabindex="10">
                            <label for="maschio">Maschio</label><br>

                            <input id="femmina" type="radio" name="genere" title="Sesso femminile" value="F" tabindex="10">
                            <label for="femmina">Femmina</label><br>

                            <input id="sconosciuto" type="radio" name="genere" title="Altro" value="U" tabindex="10">
                            <label for="sconosciuto">Non specificato</label><br>
                        </fieldset>
                    </div>
                </div>

                <div class="riga">
                    <div class="colonna">
                        <label for="psw"><b><mark>Password</mark></b></label>
                        <input class="input-field" id="psw" type="password" placeholder="Password" name="psw" title="Password di almeno 5 caratteri e massimo 72&#10;&#10;Attenzione! I caratteri 'speciali' occupano più spazio:&#10;tieni sotto controllo il contatore!" minlength="5" maxlength="72" autocomplete="off" tabindex="11" required>
                        <div class="contatore"></div>
                    </div>

                    <div class="colonna">
                        <label for="psw-repeat"><b><mark>Conferma password</mark></b></label>
                        <input class="input-field" id="psw-repeat" type="password" placeholder="Conferma password" name="psw-repeat" title="Ripeti la password inserita" autocomplete="off" tabindex="12" required>
                        <span id="message"></span>
                    </div>
                </div>

                <label class="checkbox" title="Selezionando la casella acconsenti a condividere lo storico degli interventi registrati da altri professionisti">
                    <input type="checkbox" checked="checked" name="share" title="Selezionando la casella acconsenti a condividere lo storico degli interventi registrati da altri professionisti" tabindex="13"> Accetto di condividere lo storico degli interventi

                    <!-- Simbolo di informazioni: al passaggio del mouse (hover) viene visualizzato il title con le informazioni extra -->
                    <span class="fa fa-info-circle" title="I professionisti possono registrare gli interventi eseguiti ad un utente o ad un altro professionista.&#10;Se selezioni la casella, acconsenti a condividere con tutti e soli i professionisti le informazioni presenti negli interventi registrati.&#10;In altre parole, i professionisti avranno modo di consultare tutto l'elenco degli interventi a te registrati.&#10;&#10;Maggiori informazioni nella pagina 'About'."></span>
                </label>

                <button type="submit" class="submit" tabindex="14">Iscriviti</button>
                <button type="reset" class="cancel" tabindex="15">Reset</button>
            </div>

            <div class="sub-panel">
                <a class="underlined" href="../login/login.php" tabindex="16">Hai già un account? Esegui il login</a>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . "/../../html/layouts/footer.html"; ?>
</body>
</html>
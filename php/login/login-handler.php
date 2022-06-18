<?php

require_once __DIR__ . "/../assets/sessione-manager.php";

// Se l'utente ha già eseguito l'accesso riportarlo alla home
if(isLogged()) {
    header('Location: ../../index.php');
    exit();
}

// Se non sono stati inseriti tutti i campi
if(empty($_POST['email']) || empty($_POST['psw'])) {
    setAlert('Non tutti i campi sono stati inseriti', 'red');
    header('Location: login.php');
    exit();
}

$email = $_POST['email'];
$password = $_POST['psw'];

require_once __DIR__ . "/../assets/database-manager.php";
global $database;

$psw_utente = $database->getUserPassword($email);
$psw_professionista = $database->getProfessionalPassword($email);

// Verifica se la email inserita è registrata (per un utente o un professionista)
if($psw_utente !== NULL || $psw_professionista !== NULL) {

    // Controlla la correttezza della password
    if (password_verify($password, $psw_utente !== NULL ? $psw_utente : $psw_professionista)) {
        session_regenerate_id();

        // Ricorda l'utente in modo che al prossimo accesso al sito non debba effettuare il login di nuovo
        setSession($email);

        setAlert('Login eseguito con successo', 'green');

        header('Location: ../../index.php');

        $database->closeConnection();
        exit();
    }
}

/* *** Email inesistente o password errata *** */
setAlert('Errore: email o password non corretti!', 'red');
header('Location: login.php');
$database->closeConnection();
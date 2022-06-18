<?php

require_once __DIR__ . "/../assets/sessione-manager.php";
require_once __DIR__ . "/../assets/validation.php";

// Se l'utente non ha eseguito l'accesso riportarlo alla home
if(!isLogged()) {
    setAlert('Solo gli utenti registrati possono inserire nuove recensioni', 'red');
    header('Location: ../../index.php');
    exit();
}

if(empty($_POST['email']) || empty($_POST['voto']) || empty($_POST['feedback'])) {
    setAlert('Non tutti i campi sono stati inseriti', 'red');
    header('Location: registrazione.php');
    exit();
}

$email = $_POST['email'];
$voto = $_POST['voto'];
$commento = htmlentities($_POST['feedback']);

$validation = new Validation();
$validation->name('email')->value($email)->pattern('email')->range(0, 50);
$validation->name('voto')->value($voto)->numeric()->range(1, 5);
$validation->name('commento')->value($commento)->range(0, 300);

if (!$validation->isSuccess()) // Se non sono stati rispettati i formati di tutti i campi
{
    setAlert($validation->getError(), 'red');
    header('Location: registrazione.php');
    exit();
}


require_once __DIR__ . "/../assets/database-manager.php";
require_once __DIR__ . "/../assets/validation.php";
global $database, $validation;

// L'email passata come parametro deve esistere ed essere di un professionista (solo loro possono essere recensiti)
if (!$database->isProfessional($email)) {
    $database->closeConnection();

    setAlert('L\'email ' . Validation::purify($email) . ' non corrisponde a nessun professionista', "red");
    header('Location: ../../index.php');
    exit();
}

// Controlla che non si stia inserendo una recensione per se stessi
if (getLoginEmail() === $email) {
    $database->closeConnection();

    setAlert('Non è permesso inserire una recensione per se stessi', 'red');
    header('Location: ../../index.php');
    exit();
}


/* *** Registrazione sul database *** */

// Controllo se chi sta inserendo la recensione è un utente o un professionista
if (!$database->isProfessional(getLoginEmail())) // Se è un utente
    $params = array(getLoginEmail(), null, $email, $voto, $commento, date('Y-m-d'));
else // Se è un professionista
    $params = array(null, getLoginEmail(), $email, $voto, $commento, date('Y-m-d'));

$stmt = $database->prepared_query("INSERT INTO recensione(EmailUtente, EmailProfessionista, Target, Stelle, Commento, Data) VALUES (?, ?, ?, ?, ?, ?)", array('s' => $params));
$database->closeStatement($stmt);
$database->closeConnection();

// Visualizza messaggio di successo colorato
setAlert('Recensione registrata con successo!', 'green');
header('Location: ../../index.php');

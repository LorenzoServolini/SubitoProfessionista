<?php

require_once __DIR__ . "/../assets/sessione-manager.php";

// Se l'utente non ha eseguito l'accesso riportarlo alla home
if(!isLogged()) {
    setAlert('Solo i professionisti possono registrare dei nuovi interventi', 'red');
    header('Location: ../../index.php');
    exit();
}

if(empty($_POST['email']) || empty($_POST['data']) || empty($_POST['descrizione'])) {
    setAlert('Non tutti i campi sono stati inseriti', 'red');
    header('Location: registrazione.php');
    exit();
}

require_once __DIR__ . "/../assets/database-manager.php";
require_once __DIR__ . "/../assets/validation.php";
global $database;

// Se l'utente che sta interagendo con la pagina non è un professionista
if (!$database->isProfessional(getLoginEmail())) {
    $database->closeConnection();

    setAlert('Solo i professionisti possono registrare dei nuovi interventi', 'red');
    header('Location: ../../index.php');
    exit();
}

$email = $_POST['email'];
$data = $_POST['data'];
$descrizione = htmlentities($_POST['descrizione']);

$validation = new Validation();
$validation->name('email')->value($email)->pattern('email')->range(0, 50);
$validation->name('data di intervento')->value($data)->pattern('date')->range(2010, time());
$validation->name('descrizione')->value($descrizione)->range(0, 400);

if (!$validation->isSuccess()) // Se non sono stati rispettati i formati di tutti i campi
{
    $database->closeConnection();

    setAlert($validation->getError(), 'red');
    header('Location: registrazione.php');
    exit();
}


$found_user = $database->isUser($email);
$found_professionista = $database->isProfessional($email);

// Controlla che l'email inserita sia registrata (o a un professionista o ad un utente)
if (!$found_user && !$found_professionista) {
    $database->closeConnection();

    setAlert('L\'email ' . Validation::purify($email) . ' non corrisponde a nessuno', 'red');
    header('Location: registrazione.php');
    exit();
}


/* *** Registrazione sul database con input verificati *** */
if ($found_user) // Se è un utente
    $params = array(getLoginEmail(), $email, null, $data, $descrizione);
else // Se è un professionista
    $params = array(getLoginEmail(), null, $email, $data, $descrizione);

$stmt = $database->prepared_query("INSERT INTO intervento(Professionista, TargetUtente, TargetProfessionista, Data, Descrizione) VALUES (?, ?, ?, ?, ?)", array('s' => $params));
$database->closeStatement($stmt);
$database->closeConnection();

// Visualizza messaggio di successo colorato
setAlert('Registrazione dell\'intervento avvenuta con successo!', 'green');
header('Location: ../../index.php');

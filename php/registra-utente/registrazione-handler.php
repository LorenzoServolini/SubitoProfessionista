<?php

require_once __DIR__ . "/../assets/sessione-manager.php";
require_once __DIR__ . "/../assets/validation.php";

// Se l'utente ha già eseguito l'accesso riportarlo alla home
if(isLogged()) {
    header('Location: ../../index.php');
    exit();
}

if(empty($_POST['email']) || empty($_POST['psw']) || empty($_POST['psw-repeat'])) {
    setAlert('Non tutti i campi sono stati inseriti', 'red');
    header('Location: registrazione.php');
    exit();
}

$email = $_POST['email'];
$province = ((isset($_POST['province']) && count($_POST['province']) > 0) ? $_POST['province'] : null);
$password = $_POST['psw'];
$password_repeat = $_POST['psw-repeat'];
$share = ((isset($_POST['share']) && $_POST['share'] === 'on') ? '1' : '0');

$validation = new Validation();
if($province !== null)
    $validation->name('abitazione')->value($province)->vectorial();
$validation->name('email')->value($email)->pattern('email')->range(0, 50);
$validation->name('password')->value($password)->range(5, 72);
$validation->name('conferma password')->value($password_repeat)->equal($password);

if (!$validation->isSuccess()) // Se non sono stati rispettati i formati di tutti i campi
{
    setAlert($validation->getError(), 'red');
    header('Location: registrazione.php');
    exit();
}


require_once __DIR__ . "/../assets/database-manager.php";
global $database;

// Controlla che l'email inserita non sia già registrata (per un utente o un professionista)
if (!$database->availableEmail($email)) {
    $database->closeConnection();

    setAlert("L'email {$email} risulta già registrata", "red");
    header('Location: registrazione.php');
    exit();
}

// Controlla che le province inserite esistano
if($province !== null) {

    // Scorre tutte le province inserite
    foreach ($province as $provincia) {
        $sql = "SELECT EXISTS(SELECT 1 FROM provincia WHERE Nome = ?) AS found;";
        $stmt = $database->prepared_query($sql, array('s' => $provincia));
        $result = $database->getResult($stmt);

        // Provincia inesistente
        if(!$database->fetch($result, 'found')) {
            $database->closeConnection();

            setAlert('La provincia '  . Validation::purify($provincia) . ' non esiste', 'red');
            header('Location: registrazione.php');
            exit();
        }
    }
}



/* *** Registrazione sul database *** */
$hashed_psw = password_hash($password, PASSWORD_BCRYPT);
if($province !== null)
    $province = implode(",", $province); // Memorizza l'array di province sotto forma di stringa

$stmt = $database->prepared_query("INSERT INTO utente VALUES (?, ?, ?, ?)", array('s' => array($email, $province, $hashed_psw, $share)));
$database->closeStatement($stmt);
$database->closeConnection();


// Ricorda l'utente in modo che al prossimo accesso al sito non debba effettuare il login di nuovo
setSession($email);

// Visualizza messaggio di successo colorato
setAlert('Registrazione avvenuta con successo', 'green');
header('Location: ../../index.php');